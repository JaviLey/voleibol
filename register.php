<?php
require_once 'app/config/config.php';

// Si ya está logueado, redirigir al dashboard correspondiente
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] == 'admin') {
        redirect('admin/dashboard.php');
    } else {
        redirect('delegado/dashboard.php');
    }
}

// Procesar formulario de registro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_equipo = trim($_POST['nombre_equipo']);
    $nombre_completo = trim($_POST['nombre_completo']);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telefono = trim($_POST['telefono']);
    $confirm_telefono = trim($_POST['confirm_telefono']);
    $direccion = trim($_POST['direccion']);
    $referencia_pago = trim($_POST['referencia_pago']);
    $categoria = trim($_POST['categoria']);
    
    // Validaciones
    $errors = [];
    
    // Validar nombre del equipo (mínimo 3 caracteres)
    if (strlen($nombre_equipo) < 3) {
        $errors[] = "El nombre del equipo debe tener al menos 3 caracteres.";
    }
    
    // Validar nombre completo (mínimo 5 caracteres)
    if (strlen($nombre_completo) < 5) {
        $errors[] = "El nombre completo debe tener al menos 5 caracteres.";
    }
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El formato del correo electrónico no es válido.";
    }
    
    // Validar teléfono (exactamente 10 dígitos)
    if (!preg_match('/^[0-9]{10}$/', $telefono)) {
        $errors[] = "El teléfono debe tener exactamente 10 dígitos.";
    }
    
    // Validar que los teléfonos coincidan
    if ($telefono !== $confirm_telefono) {
        $errors[] = "Los números de teléfono no coinciden.";
    }
    
    // Validar dirección (mínimo 10 caracteres)
    if (strlen($direccion) < 10) {
        $errors[] = "La dirección debe tener al menos 10 caracteres.";
    }
    
    // Validar referencia de pago (mínimo 5 caracteres)
    if (strlen($referencia_pago) < 5) {
        $errors[] = "La referencia de pago debe tener al menos 5 caracteres.";
    }
    
    // Validar categoría
    if (empty($categoria) || !in_array($categoria, ['varonil', 'femenil'])) {
        $errors[] = "Debe seleccionar una categoría válida (Varonil o Femenil).";
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
        // Verificar si el email ya existe
        $query = "SELECT id FROM usuarios WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            setSweetAlert('error', 'Error', 'El correo electrónico ya está registrado.');
        } else {
            // Iniciar transacción para asegurar que ambos inserts se completen
            try {
                $db->beginTransaction();
                
                // 1. Insertar nuevo usuario (usando el teléfono como contraseña)
                $hashed_password = password_hash($telefono, PASSWORD_DEFAULT);
                $query = "INSERT INTO usuarios (nombre_completo, email, telefono, direccion, password, tipo) 
                         VALUES (:nombre_completo, :email, :telefono, :direccion, :password, 'delegado')";
                
                $stmt = $db->prepare($query);
                $stmt->bindParam(':nombre_completo', $nombre_completo);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':direccion', $direccion);
                $stmt->bindParam(':password', $hashed_password);
                
                if ($stmt->execute()) {
                    $user_id = $db->lastInsertId();
                    
                    // 2. Insertar equipo (usando la primera liga activa por defecto)
                    $query_liga = "SELECT id FROM ligas WHERE activa = 1 LIMIT 1";
                    $stmt_liga = $db->prepare($query_liga);
                    $stmt_liga->execute();
                    $liga = $stmt_liga->fetch(PDO::FETCH_ASSOC);
                    $liga_id = $liga ? $liga['id'] : 1;
                    
                    $query_equipo = "INSERT INTO equipos (id_liga, id_usuario, nombre_equipo, logo, comprobante_inscripcion, referencia_pago, categoria) 
                                    VALUES (:id_liga, :id_usuario, :nombre_equipo, :logo, :comprobante_inscripcion, :referencia_pago, :categoria)";
                    
                    $stmt_equipo = $db->prepare($query_equipo);
                    $stmt_equipo->bindParam(':id_liga', $liga_id);
                    $stmt_equipo->bindParam(':id_usuario', $user_id);
                    $stmt_equipo->bindParam(':nombre_equipo', $nombre_equipo);
                    $stmt_equipo->bindParam(':logo', $logo_path);
                    $stmt_equipo->bindParam(':comprobante_inscripcion', $comprobante_path);
                    $stmt_equipo->bindParam(':referencia_pago', $referencia_pago);
                    $stmt_equipo->bindParam(':categoria', $categoria);
                    
                    if ($stmt_equipo->execute()) {
                        $db->commit();
                        setSweetAlert('success', 'Registro exitoso', 'Su equipo y cuenta han sido creados. Deben ser validados por la administración. Su contraseña es su número de teléfono.');
                        redirect('login.php');
                    } else {
                        $db->rollBack();
                        setSweetAlert('error', 'Error', 'Hubo un problema al registrar el equipo. Intente nuevamente.');
                    }
                } else {
                    $db->rollBack();
                    setSweetAlert('error', 'Error', 'Hubo un problema al crear su cuenta. Intente nuevamente.');
                }
            } catch (Exception $e) {
                $db->rollBack();
                setSweetAlert('error', 'Error', 'Hubo un problema en el proceso de registro: ' . $e->getMessage());
            }
        }
    } else {
        // Mostrar todos los errores
        $error_message = implode('<br>', $errors);
        setSweetAlert('error', 'Error de validación', $error_message);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-volleyball-ball me-2"></i>
                <?php echo SITE_NAME; ?>
            </a>
        </div>
    </nav>

    <!-- Register Form -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Registro de Equipo y Delegado</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <h5 class="mb-3 text-primary">Datos del Equipo</h5>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="nombre_equipo" class="form-label">Nombre del Equipo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre_equipo" name="nombre_equipo" required 
                                           minlength="3" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ0-9\s]+" 
                                           title="Solo letras, números y espacios permitidos">
                                    <div class="form-text">Mínimo 3 caracteres. Solo letras, números y espacios.</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                                    <select class="form-select" id="categoria" name="categoria" required>
                                        <option value="">Seleccione una categoría</option>
                                        <option value="varonil">Varonil</option>
                                        <option value="femenil">Femenil</option>
                                    </select>
                                    <div class="form-text">Seleccione la categoría en la que participará su equipo.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="logo" class="form-label">Logo del Equipo (Opcional)</label>
                                    <input type="file" class="form-control" id="logo" name="logo" accept=".jpg,.jpeg,.png,.gif">
                                    <div class="form-text">Formatos: JPG, PNG, GIF. Máx: 2MB</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="comprobante_pago" class="form-label">Comprobante de Pago <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="comprobante_pago" name="comprobante_pago" required 
                                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                    <div class="form-text">Formatos: JPG, PNG, PDF, DOC. Máx: 2MB</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="referencia_pago" class="form-label">Número de Referencia de Pago <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="referencia_pago" name="referencia_pago" required 
                                           minlength="5" pattern="[A-Za-z0-9\-]+" 
                                           title="Solo letras, números y guiones permitidos">
                                    <div class="form-text">Ingrese el número de referencia, transacción o folio de su pago.</div>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            <h5 class="mb-3 text-primary">Datos del Delegado/Entrenador</h5>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="nombre_completo" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" required 
                                           minlength="5" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+" 
                                           title="Solo letras y espacios permitidos">
                                    <div class="form-text">Mínimo 5 caracteres. Solo letras y espacios.</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="form-text">Será su nombre de usuario para iniciar sesión.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="direccion" class="form-label">Dirección <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="direccion" name="direccion" required 
                                           minlength="10">
                                    <div class="form-text">Mínimo 10 caracteres.</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telefono" class="form-label">Teléfono/Celular <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" required 
                                           pattern="[0-9]{10}" title="Debe tener exactamente 10 dígitos" 
                                           placeholder="9611234567">
                                    <div class="form-text">10 dígitos. Será su contraseña para iniciar sesión.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_telefono" class="form-label">Confirmar Teléfono <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="confirm_telefono" name="confirm_telefono" required 
                                           pattern="[0-9]{10}" title="Debe tener exactamente 10 dígitos" 
                                           placeholder="9611234567">
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Importante:</strong> Su contraseña será su número de teléfono. 
                                Asegúrese de recordarlo para iniciar sesión.
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Registrar Equipo y Usuario</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- Mostrar alertas -->
    <?php showSweetAlert(); ?>
    
    <!-- Validación en tiempo real -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validar que los teléfonos coincidan
        const telefono = document.getElementById('telefono');
        const confirmTelefono = document.getElementById('confirm_telefono');
        
        function validatePhoneMatch() {
            if (telefono.value !== confirmTelefono.value) {
                confirmTelefono.setCustomValidity('Los números de teléfono no coinciden');
            } else {
                confirmTelefono.setCustomValidity('');
            }
        }
        
        telefono.addEventListener('input', validatePhoneMatch);
        confirmTelefono.addEventListener('input', validatePhoneMatch);
        
        // Mostrar vista previa del logo
        const logoInput = document.getElementById('logo');
        if (logoInput) {
            logoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Crear o actualizar vista previa
                        let preview = document.getElementById('logo-preview');
                        if (!preview) {
                            preview = document.createElement('div');
                            preview.id = 'logo-preview';
                            preview.className = 'mt-2 text-center';
                            logoInput.parentNode.appendChild(preview);
                        }
                        preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px;" alt="Vista previa del logo">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
    </script>
</body>
</html>