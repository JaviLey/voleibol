<?php
class UserModel {
    private $conn;
    private $table_name = "usuarios";
    
    public $id;
    public $email;
    public $password;
    public $nombre_completo;
    public $telefono;
    public $direccion;
    public $tipo;
    public $activo;
    public $fecha_registro;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // FUNCIÓN QUE YA EXISTÍA (NO LA REPITAS)
    public function getUserById($id) {
        $query = "SELECT id, email, nombre_completo, telefono, direccion, tipo, activo, fecha_registro 
                  FROM " . $this->table_name . " 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        return false;
    }
    
    // FUNCIÓN QUE YA EXISTÍA (NO LA REPITAS)
    public function getUserByEmail($email) {
        $query = "SELECT id, email, password, nombre_completo, telefono, direccion, tipo, activo, fecha_registro 
                  FROM " . $this->table_name . " 
                  WHERE email = :email";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        return false;
    }
    
    // FUNCIONES NUEVAS QUE DEBES AGREGAR:
    
    public function countPendingUsers() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE activo = 0 AND tipo = 'delegado'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
    
    public function getPendingUsers() {
        $query = "SELECT u.*, e.nombre_equipo 
                  FROM " . $this->table_name . " u 
                  LEFT JOIN equipos e ON u.id = e.id_usuario 
                  WHERE u.activo = 0 AND u.tipo = 'delegado' 
                  ORDER BY u.fecha_registro DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $row;
        }
        return $users;
    }
    
    public function activateUser($user_id) {
        $query = "UPDATE " . $this->table_name . " SET activo = 1 WHERE id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }
    
    public function deactivateUser($user_id) {
        $query = "UPDATE " . $this->table_name . " SET activo = 0 WHERE id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }
    
    public function createUser($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (email, password, nombre_completo, telefono, direccion, tipo) 
                 VALUES (:email, :password, :nombre_completo, :telefono, :direccion, :tipo)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitizar datos
        $email = htmlspecialchars(strip_tags($data['email']));
        $password = htmlspecialchars(strip_tags($data['password']));
        $nombre_completo = htmlspecialchars(strip_tags($data['nombre_completo']));
        $telefono = htmlspecialchars(strip_tags($data['telefono']));
        $direccion = htmlspecialchars(strip_tags($data['direccion']));
        $tipo = htmlspecialchars(strip_tags($data['tipo']));
        
        // Hash de la contraseña
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        
        // Bind parameters
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password_hashed);
        $stmt->bindParam(":nombre_completo", $nombre_completo);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":direccion", $direccion);
        $stmt->bindParam(":tipo", $tipo);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    public function updateUser($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre_completo = :nombre_completo, 
                      telefono = :telefono, 
                      direccion = :direccion 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $nombre_completo = htmlspecialchars(strip_tags($data['nombre_completo']));
        $telefono = htmlspecialchars(strip_tags($data['telefono']));
        $direccion = htmlspecialchars(strip_tags($data['direccion']));
        
        $stmt->bindParam(":nombre_completo", $nombre_completo);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":direccion", $direccion);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }
    
    public function getAllUsers() {
        $query = "SELECT u.*, e.nombre_equipo 
                  FROM " . $this->table_name . " u 
                  LEFT JOIN equipos e ON u.id = e.id_usuario 
                  ORDER BY u.fecha_registro DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $row;
        }
        return $users;
    }
}
?>