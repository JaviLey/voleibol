<?php
// app/controllers/CedulaController.php
require_once '../libs/tcpdf/tcpdf.php';

class CedulaController {
    private $db;
    private $cedulaModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->cedulaModel = new CedulaModel($db);
    }
    
    public function generatePDF($teamId) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ../login.php');
            exit();
        }
        
        $team = $this->cedulaModel->getTeam($teamId);
        $players = $this->cedulaModel->getTeamPlayers($teamId);
        $staff = $this->cedulaModel->getTeamStaff($teamId);
        
        // Verificar permisos
        if (($_SESSION['user_type'] == 'delegado' && $team['id_usuario'] != $_SESSION['user_id']) || 
            !$team['validado']) {
            $_SESSION['swal'] = [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'No tiene permisos para generar la cédula de este equipo.'
            ];
            header('Location: ../delegado/dashboard.php');
            exit();
        }
        
        // Crear PDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        
        // Configurar documento
        $pdf->SetCreator('Sistema Ligas Voleibol Chiapas');
        $pdf->SetAuthor($team['nombre_equipo']);
        $pdf->SetTitle('Cédula ' . $team['nombre_equipo']);
        $pdf->SetMargins(15, 25, 15);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(TRUE, 25);
        
        // Agregar página
        $pdf->AddPage();
        
        // Obtener configuraciones de la liga
        $liga = $this->cedulaModel->getLeague($team['id_liga']);
        
        // Generar contenido
        $html = $this->generateHTMLContent($team, $players, $staff, $liga);
        
        // Escribir HTML
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Output
        $pdf->Output('cedula_' . $team['nombre_equipo'] . '.pdf', 'D');
    }
    
    private function generateHTMLContent($team, $players, $staff, $liga) {
        ob_start();
        include 'views/templates/cedula_template.php';
        return ob_get_clean();
    }
}
?>