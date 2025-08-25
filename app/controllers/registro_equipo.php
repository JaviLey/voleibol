<?php
require_once '../config/config.php';

// Verificar autenticación
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'delegado') {
    header('Location: ../login.php');
    exit();
}

// Procesar formulario de registro de equipo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar_equipo'])) {
    $user_id = $_POST['user_id'];
    $nombre_equipo = trim($_POST['nombre_equipo']);
    $categoria = trim($_POST['categoria']);
    $rama = trim($_POST['rama']);
    $referencia_pago = trim($_POST['referencia_pago']);
    
    // Validaciones
    $errors = [];
    
    // Validar nombre del equipo (mínimo 3 caracteres)
    if (strlen($nombre_equipo) < 3) {
        $errors[] = "El nombre del equipo debe tener al menos 3 caracteres.";
    }
    
    // Validar categoría
    if (empty($categoria) || !in_array($categoria, ['varonil', 'femenil'])) {
        $errors[] = "Debe seleccionar una categoría válida.";
    }
    
    // Validar rama
    if (empty($rama) || !in_array($rama, ['libre', 'primera', 'segunda'])) {
        $errors[] = "Debe seleccionar una rama válida.";
    }
    
    // Validar referencia de pago (mínimo 5 caracteres)
    if (strlen($referencia_pago) < 5) {
        $errors[] = "La referencia de pago debe tener al menos 5 caracteres.";
    }
    
    // Verificar límite de equipos por usuario
    require_once '../models/TeamModel.php';
    $teamModel = new TeamModel($db);
    $equipos_usuario = $teamModel->getUserTeams($user_id);
    
    $stats = ['equipos_varonil' => 0, 'equipos_femenil' => 0];
    foreach ($equipos_usuario as $equipo) {
        if ($equipo['categoria'] == 'varonil') {
            $stats['equipos_varonil']++;
        } else {
            $stats['equipos_femenil']++;
        }
    }
    
    // Verificar límites
    if ($categoria == 'varonil' && $stats['equipos_varonil'] >= 2) {
        $errors[] = "Ya tienes el máximo de equipos varoniles permitidos (2).";
    }
    
    if ($categoria == 'femenil' && $stats['equipos_femenil'] >= 2) {
        $errors[] = "Ya tienes el máximo de equipos femeniles permitidos (2).";
    }
    
    // Procesar logo (opcional)
    $logo_path = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
        $logo_info = pathinfo($_FILES['logo']['name']);
        $logo_ext = strtolower($logo_info['extension']);
        
        // Validar tipo de archivo
        if (!in_array($logo_ext, ALLOWED_IMAGE_TYPES)) {
            $errors[] = "El formato del logo no es válido. Formatos permitidos: " . implode(', ', ALLOWED_IMAGE_TYPES);
        }
        
        // Validar tamaño
        if ($_FILES['logo']['size'] > MAX_FILE_SIZE) {
            $errors[] = "El logo es demasiado grande. Tamaño máximo: " . (MAX_FILE_SIZE / 1024 / 1024) . "MB.";
        }
        
        if (empty($errors)) {
            $logo_name = uniqid() . '.' . $logo_ext;
            $logo_path = 'logos/' . $logo_name;
            $logo_target = UPLOAD_DIR . $logo_path;
            
            if (!move_uploaded_file($_FILES['logo']['tmp_name'], $logo_target)) {
                $errors[] = "Error al subir el logo. Intente nuevamente.";
            }
        }
    }
    
    // Procesar comprobante de pago (obligatorio)
    $comprobante_path = null;
    if (isset($_FILES['comprobante_pago']) && $_FILES['comprobante_pago']['error'] == UPLOAD_ERR_OK) {
        $comprobante_info = pathinfo($_FILES['comprobante_pago']['name']);
        $comprobante_ext = strtolower($comprobante_info['extension']);
        
        // Validar tipo de archivo
        if (!in_array($comprobante_ext, array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES))) {
            $errors[] = "El formato del comprobante no es válido. Formatos permitidos: " . implode(', ', array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES));
        }
        
        // Validar tamaño
        if ($_FILES['comprobante_pago']['size'] > MAX_FILE_SIZE) {
            $errors[] = "El comprobante es demasiado grande. Tamaño máximo: " . (MAX_FILE_SIZE / 1024 / 1024) . "MB.";
        }
        
        if (empty($errors)) {
            $comprobante_name = uniqid() . '.' . $comprobante_ext;
            $comprobante_path = 'comprobantes/' . $comprobante_name;
            $comprobante_target = UPLOAD_DIR . $comprobante_path;
            
            if (!move_uploaded_file($_FILES['comprobante_pago']['tmp_name'], $comprobante_target)) {
                $errors[] = "Error al subir el comprobante. Intente nuevamente.";
            }
        }
    } else {
        $errors[] = "Debe subir un comprobante de pago.";
    }
    
    // Si no hay errores, proceder con el registro
    if (empty($errors)) {
        try {
            // Insertar equipo (usando la primera liga activa por defecto)
            $query_liga = "SELECT id FROM ligas WHERE activa = 1 LIMIT 1";
            $stmt_liga = $db->prepare($query_liga);
            $stmt_liga->execute();
            $liga = $stmt_liga->fetch(PDO::FETCH_ASSOC);
            $liga_id = $liga ? $liga['id'] : 1;
            
            $query_equipo = "INSERT INTO equipos (id_liga, id_usuario, nombre_equipo, logo, comprobante_inscripcion, referencia_pago, categoria, rama) 
                            VALUES (:id_liga, :id_usuario, :nombre_equipo, :logo, :comprobante_inscripcion, :referencia_pago, :categoria, :rama)";
            
            $stmt_equipo = $db->prepare($query_equipo);
            $stmt_equipo->bindParam(':id_liga', $liga_id);
            $stmt_equipo->bindParam(':id_usuario', $user_id);
            $stmt_equipo->bindParam(':nombre_equipo', $nombre_equipo);
            $stmt_equipo->bindParam(':logo', $logo_path);
            $stmt_equipo->bindParam(':comprobante_inscripcion', $comprobante_path);
            $stmt_equipo->bindParam(':referencia_pago', $referencia_pago);
            $stmt_equipo->bindParam(':categoria', $categoria);
            $stmt_equipo->bindParam(':rama', $rama);
            
            if ($stmt_equipo->execute()) {
                setSweetAlert('success', 'Equipo registrado', 'Tu equipo ha sido registrado correctamente. Debe ser validado por la administración.');
            } else {
                setSweetAlert('error', 'Error', 'Hubo un problema al registrar el equipo. Intente nuevamente.');
            }
        } catch (Exception $e) {
            setSweetAlert('error', 'Error', 'Hubo un problema en el proceso de registro: ' . $e->getMessage());
        }
    } else {
        // Mostrar todos los errores
        $error_message = implode('<br>', $errors);
        setSweetAlert('error', 'Error de validación', $error_message);
    }
    
    header('Location: ./delegado/dashboard.php');
    exit();
} else {
    header('Location: ./delegado/dashboard.php');
    exit();
}
?>