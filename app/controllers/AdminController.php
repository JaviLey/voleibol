<?php
// app/controllers/AdminController.php
class AdminController {
    private $db;
    private $adminModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->adminModel = new AdminModel($db);
    }
    
    public function dashboard() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
            header('Location: ../login.php');
            exit();
        }
        
        $stats = [
            'total_equipos' => $this->adminModel->countTeams(),
            'equipos_varonil' => $this->adminModel->countTeamsByRama('varonil'),
            'equipos_femenil' => $this->adminModel->countTeamsByRama('femenil'),
            'total_jugadores' => $this->adminModel->countPlayers(),
            'equipos_pendientes' => $this->adminModel->countPendingTeams()
        ];
        
        include 'views/admin/dashboard.php';
    }
    
    public function manageTeams() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
            header('Location: ../login.php');
            exit();
        }
        
        $equipos = $this->adminModel->getAllTeams();
        include 'views/admin/gestion_equipos.php';
    }
    
    public function validateTeam($teamId) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
            header('Location: ../login.php');
            exit();
        }
        
        if ($this->adminModel->validateTeam($teamId)) {
            // Enviar notificación al delegado
            $team = $this->adminModel->getTeam($teamId);
            $this->sendValidationEmail($team['email'], $team['nombre_equipo']);
            
            $_SESSION['swal'] = [
                'icon' => 'success',
                'title' => 'Éxito',
                'text' => 'Equipo validado correctamente. Se ha notificado al delegado.'
            ];
        } else {
            $_SESSION['swal'] = [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo validar el equipo.'
            ];
        }
        
        header('Location: gestion_equipos.php');
        exit();
    }
}
?>