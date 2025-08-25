<?php
class TeamModel
{
    private $conn;
    private $table_name = "equipos";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createTeam($data)
    {

        // En el método para insertar equipos, asegúrate de incluir la rama

        $query = "INSERT INTO " . $this->table_name . " (id_liga, id_usuario, nombre_equipo, logo, comprobante_inscripcion, referencia_pago, categoria, rama) VALUES (:id_liga, :id_usuario, :nombre_equipo, :logo, :comprobante_inscripcion, :referencia_pago, :categoria, :rama)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_liga', $data['id_liga']);
        $stmt->bindParam(':id_usuario', $data['id_usuario']);
        $stmt->bindParam(':nombre_equipo', $data['nombre_equipo']);
        $stmt->bindParam(':logo', $data['logo']);
        $stmt->bindParam(':comprobante_inscripcion', $data['comprobante_inscripcion']);
        $stmt->bindParam(':referencia_pago', $data['referencia_pago']);
        $stmt->bindParam(':categoria', $data['categoria']);
        $stmt->bindParam(':rama', $data['rama']);

        return $stmt->execute();

        /*$query = "INSERT INTO " . $this->table_name . " 
                 (id_liga, id_usuario, nombre_equipo, logo, rama, categoria, comprobante_inscripcion) 
                 VALUES (:id_liga, :id_usuario, :nombre_equipo, :logo, :rama, :categoria, :comprobante_inscripcion)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_liga", $data['id_liga']);
        $stmt->bindParam(":id_usuario", $data['id_usuario']);
        $stmt->bindParam(":nombre_equipo", $data['nombre_equipo']);
        $stmt->bindParam(":logo", $data['logo']);
        $stmt->bindParam(":rama", $data['rama']);
        $stmt->bindParam(":categoria", $data['categoria']);
        $stmt->bindParam(":comprobante_inscripcion", $data['comprobante_inscripcion']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;*/
    }

    public function getTeam($id)
    {
        $query = "SELECT e.*, l.nombre as nombre_liga, u.nombre_completo as delegado 
                  FROM " . $this->table_name . " e 
                  LEFT JOIN ligas l ON e.id_liga = l.id 
                  LEFT JOIN usuarios u ON e.id_usuario = u.id 
                  WHERE e.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        return false;
    }

    public function getUserTeams($user_id)
    {
        $query = "SELECT e.*, l.nombre as nombre_liga 
                  FROM " . $this->table_name . " e 
                  LEFT JOIN ligas l ON e.id_liga = l.id 
                  WHERE e.id_usuario = :user_id 
                  ORDER BY e.fecha_registro DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        $teams = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $teams[] = $row;
        }
        return $teams;
    }

    public function validateTeam($team_id)
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET validado = 1 
                  WHERE id = :team_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":team_id", $team_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getActiveLeagues()
    {
        $query = "SELECT id, nombre FROM ligas WHERE activa = 1 ORDER BY nombre";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $leagues = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $leagues[] = $row;
        }
        return $leagues;
    }

    public function countTeamsByRama($rama)
    {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table_name . " 
                  WHERE rama = :rama AND validado = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":rama", $rama);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Agrega estas funciones a tu clase TeamModel

    public function countTeams()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE validado = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function countPendingTeams()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE validado = 0";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getAllTeams($filters = [])
    {
        $query = "SELECT e.*, u.nombre_completo as delegado, u.email, u.telefono, l.nombre as liga 
              FROM " . $this->table_name . " e 
              JOIN usuarios u ON e.id_usuario = u.id 
              JOIN ligas l ON e.id_liga = l.id 
              WHERE 1=1";

        // Aplicar filtros
        if (!empty($filters['estado'])) {
            $query .= " AND e.validado = :estado";
        }
        if (!empty($filters['rama'])) {
            $query .= " AND e.rama = :rama";
        }
        if (!empty($filters['liga'])) {
            $query .= " AND e.id_liga = :liga";
        }
        if (!empty($filters['busqueda'])) {
            $query .= " AND (e.nombre_equipo LIKE :busqueda OR u.nombre_completo LIKE :busqueda)";
        }

        $query .= " ORDER BY e.fecha_registro DESC";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        if (!empty($filters['estado'])) {
            $stmt->bindParam(':estado', $filters['estado']);
        }
        if (!empty($filters['rama'])) {
            $stmt->bindParam(':rama', $filters['rama']);
        }
        if (!empty($filters['liga'])) {
            $stmt->bindParam(':liga', $filters['liga']);
        }
        if (!empty($filters['busqueda'])) {
            $search_term = "%" . $filters['busqueda'] . "%";
            $stmt->bindParam(':busqueda', $search_term);
        }

        $stmt->execute();

        $teams = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $teams[] = $row;
        }
        return $teams;
    }

    public function updateTeam($team_id, $data)
    {
        $query = "UPDATE " . $this->table_name . " 
              SET nombre_equipo = :nombre_equipo, 
                  rama = :rama, 
                  categoria = :categoria, 
                  logo = :logo 
              WHERE id = :team_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nombre_equipo", $data['nombre_equipo']);
        $stmt->bindParam(":rama", $data['rama']);
        $stmt->bindParam(":categoria", $data['categoria']);
        $stmt->bindParam(":logo", $data['logo']);
        $stmt->bindParam(":team_id", $team_id);

        return $stmt->execute();
    }

    public function getTeamsByLeague($league_id)
    {
        $query = "SELECT e.*, u.nombre_completo as delegado 
              FROM " . $this->table_name . " e 
              JOIN usuarios u ON e.id_usuario = u.id 
              WHERE e.id_liga = :league_id AND e.validado = 1 
              ORDER BY e.nombre_equipo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":league_id", $league_id);
        $stmt->execute();

        $teams = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $teams[] = $row;
        }
        return $teams;
    }
}
