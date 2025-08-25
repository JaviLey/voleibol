<?php
// app/controllers/AuthController.php
class AuthController {
    private $db;
    private $userModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new UserModel($db);
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            
            $user = $this->userModel->getUserByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                if ($user['activo'] == 1) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_type'] = $user['tipo'];
                    $_SESSION['user_name'] = $user['nombre_completo'];
                    
                    if ($user['tipo'] == 'admin') {
                        header('Location: admin/dashboard.php');
                    } else {
                        header('Location: delegado/dashboard.php');
                    }
                    exit();
                } else {
                    $error = "Su cuenta está pendiente de validación por la administración";
                }
            } else {
                $error = "Credenciales incorrectas";
            }
        }
        
        include 'views/auth/login.php';
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar y procesar registro
            $data = [
                'nombre_completo' => $_POST['nombre_completo'],
                'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                'telefono' => $_POST['telefono'],
                'direccion' => $_POST['direccion'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'tipo' => 'delegado'
            ];
            
            if ($this->userModel->createUser($data)) {
                // Enviar correo de confirmación
                $this->sendConfirmationEmail($data['email'], $data['nombre_completo']);
                
                $_SESSION['success'] = "Registro exitoso. Su cuenta será validada por la administración.";
                header('Location: login.php');
                exit();
            } else {
                $error = "Error al registrar usuario. Intente nuevamente.";
            }
        }
        
        include 'views/auth/register.php';
    }
}
?>