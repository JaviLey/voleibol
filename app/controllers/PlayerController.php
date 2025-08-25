<?php
// app/controllers/PlayerController.php
class PlayerController {
    private $db;
    private $playerModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->playerModel = new PlayerModel($db);
    }
    
    public function addPlayer() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'delegado') {
            header('Location: ../login.php');
            exit();
        }
        
        // Verificar que el equipo esté validado
        $teamId = $_GET['team_id'];
        $team = $this->playerModel->getTeam($teamId);
        
        if (!$team || $team['validado'] != 1 || $team['id_usuario'] != $_SESSION['user_id']) {
            $_SESSION['swal'] = [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No tiene permisos para agregar jugadores a este equipo o el equipo no está validado.'
            ];
            header('Location: dashboard.php');
            exit();
        }
        
        // Verificar límite de jugadores (máximo 14)
        $currentPlayers = $this->playerModel->countTeamPlayers($teamId);
        if ($currentPlayers >= 14) {
            $_SESSION['swal'] = [
                'icon' => 'error',
                'title' => 'Límite alcanzado',
                'text' => 'Ya ha alcanzado el máximo de 14 jugadores permitidos.'
            ];
            header('Location: gestion_jugadores.php?team_id=' . $teamId);
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Procesar foto
            $fotoPath = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
                $fotoPath = $this->uploadAndResizePhoto($_FILES['foto']);
            }
            
            // Verificar si el jugador ya existe en otro equipo (por CURP)
            if (!empty($_POST['curp'])) {
                $existingPlayer = $this->playerModel->getPlayerByCurp($_POST['curp']);
                if ($existingPlayer && $existingPlayer['id_equipo'] != $teamId) {
                    $error = "Este jugador ya está registrado en otro equipo.";
                    include 'views/delegado/agregar_jugador.php';
                    return;
                }
            }
            
            $data = [
                'id_equipo' => $teamId,
                'foto' => $fotoPath,
                'nombre_completo' => $_POST['nombre_completo'],
                'curp' => $_POST['curp'],
                'numero_sired' => $_POST['numero_sired'],
                'estatura' => $_POST['estatura'],
                'domicilio' => $_POST['domicilio'],
                'ciudad' => $_POST['ciudad'],
                'telefono' => $_POST['telefono'],
                'numero_playera' => $_POST['numero_playera']
            ];
            
            if ($this->playerModel->createPlayer($data)) {
                $_SESSION['swal'] = [
                    'icon' => 'success',
                    'title' => 'Éxito',
                    'text' => 'Jugador agregado correctamente.'
                ];
                header('Location: gestion_jugadores.php?team_id=' . $teamId);
                exit();
            } else {
                $error = "Error al agregar el jugador";
            }
        }
        
        include 'views/delegado/agregar_jugador.php';
    }
    
    private function uploadAndResizePhoto($file) {
        $uploadDir = "../assets/uploads/jugadores/";
        $fileName = uniqid() . '.jpg';
        $targetPath = $uploadDir . $fileName;
        
        // Crear miniatura con dimensiones 3cm x 2.5cm (aprox 113x94 pixels a 96dpi)
        list($width, $height) = getimagesize($file['tmp_name']);
        $thumb = imagecreatetruecolor(113, 94);
        
        // según el tipo de imagen
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension == 'jpg' || $extension == 'jpeg') {
            $source = imagecreatefromjpeg($file['tmp_name']);
        } elseif ($extension == 'png') {
            $source = imagecreatefrompng($file['tmp_name']);
        } elseif ($extension == 'gif') {
            $source = imagecreatefromgif($file['tmp_name']);
        } else {
            return null;
        }
        
        // Redimensionar
        imagecopyresized($thumb, $source, 0, 0, 0, 0, 113, 94, $width, $height);
        
        // Guardar
        imagejpeg($thumb, $targetPath, 90);
        imagedestroy($thumb);
        imagedestroy($source);
        
        return $fileName;
    }
}
?>