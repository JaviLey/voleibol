<?php
// app/controllers/TeamController.php
class TeamController {
    private $db;
    private $teamModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->teamModel = new TeamModel($db);
    }
    
    public function registerTeam() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'delegado') {
            header('Location: ../login.php');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar logo si se subió
            $logoPath = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
                $logoPath = $this->uploadFile($_FILES['logo'], 'logos');
            }
            
            // Procesar comprobante de pago
            $comprobantePath = null;
            if (isset($_FILES['comprobante_inscripcion']) && $_FILES['comprobante_inscripcion']['error'] == UPLOAD_ERR_OK) {
                $comprobantePath = $this->uploadFile($_FILES['comprobante_inscripcion'], 'comprobantes');
            }
            
            $data = [
                'id_usuario' => $_SESSION['user_id'],
                'id_liga' => $_POST['id_liga'],
                'nombre_equipo' => $_POST['nombre_equipo'],
                'logo' => $logoPath,
                'rama' => $_POST['rama'],
                'categoria' => $_POST['categoria'],
                'comprobante_inscripcion' => $comprobantePath
            ];
            
            if ($this->teamModel->createTeam($data)) {
                // Mostrar mensaje de éxito con SweetAlert
                $_SESSION['swal'] = [
                    'icon' => 'success',
                    'title' => 'Éxito',
                    'text' => 'Equipo registrado correctamente. Sus datos serán validados por la mesa directiva.'
                ];
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Error al registrar el equipo";
            }
        }
        
        $ligas = $this->teamModel->getActiveLeagues();
        include 'views/delegado/registro_equipo.php';
    }
    
    private function uploadFile($file, $folder) {
        $uploadDir = "../assets/uploads/$folder/";
        $fileName = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $fileName;
        }
        
        return null;
    }
}
?>