<?php
// Configuración general del sistema
session_start();

// Configuración para mostrar errores (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Constantes del sistema
define('SITE_NAME', 'Sistema de Gestión de Ligas de Voleibol Chiapas');
define('SITE_URL', 'http://localhost/Voleibol/');
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/Voleibol/assets/uploads/');

// Configuración de subida de archivos
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_DOC_TYPES', ['pdf', 'doc', 'docx']);

// Incluir la clase Database que está en la misma carpeta
spl_autoload_register(function ($class_name) {
    $class_file = __DIR__ . '/../models/' . $class_name . '.php';
    if (file_exists($class_file)) {
        include_once $class_file;
    }
});
//require_once 'database.php';

// Inicializar conexión a base de datos
$database = new Database();
$db = $database->getConnection();

// Función para redireccionar
function redirect($url) {
    header("Location: $url");
    exit();
}

// Función para mostrar mensajes con SweetAlert
function setSweetAlert($icon, $title, $text) {
    $_SESSION['sweetalert'] = [
        'icon' => $icon,
        'title' => $title,
        'text' => $text
    ];
}

// Mostrar SweetAlert si existe en sesión
function showSweetAlert() {
    if (isset($_SESSION['sweetalert'])) {
        $alert = $_SESSION['sweetalert'];
        echo "<script>
            Swal.fire({
                icon: '{$alert['icon']}',
                title: '{$alert['title']}',
                text: '{$alert['text']}',
                confirmButtonText: 'Aceptar'
            });
        </script>";
        unset($_SESSION['sweetalert']);
    }
}
?>