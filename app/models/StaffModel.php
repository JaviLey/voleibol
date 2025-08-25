<?php
class StaffModel {
    private $conn;
    private $table_name = "personal_tecnico";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function createStaff($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (id_equipo, tipo, foto, nombre_completo, curp, telefono) 
                 VALUES (:id_equipo, :tipo, :foto, :nombre_completo, :curp, :telefono)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id_equipo", $data['id_equipo']);
        $stmt->bindParam(":tipo", $data['tipo']);
        $stmt->bindParam(":foto", $data['foto']);
        $stmt->bindParam(":nombre_completo", $data['nombre_completo']);
        $stmt->bindParam(":curp", $data['curp']);
        $stmt->bindParam(":telefono", $data['telefono']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    public function getTeamStaff($team_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id_equipo = :team_id 
                  ORDER BY FIELD(tipo, 'entrenador', 'asistente_entrenador', 'delegado', 'representante', 'medico', 'auxiliar_medico')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":team_id", $team_id);
        $stmt->execute();
        
        $staff = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $staff[] = $row;
        }
        return $staff;
    }
    
    public function getStaffByType($team_id, $type) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id_equipo = :team_id AND tipo = :type";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":team_id", $team_id);
        $stmt->bindParam(":type", $type);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        return false;
    }
}
?>