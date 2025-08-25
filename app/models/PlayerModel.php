<?php
class PlayerModel
{
    private $conn;
    private $table_name = "jugadores";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createPlayer($data)
    {
        $query = "INSERT INTO " . $this->table_name . " 
                 (id_equipo, foto, nombre_completo, curp, numero_sired, estatura, domicilio, ciudad, telefono, numero_playera) 
                 VALUES (:id_equipo, :foto, :nombre_completo, :curp, :numero_sired, :estatura, :domicilio, :ciudad, :telefono, :numero_playera)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_equipo", $data['id_equipo']);
        $stmt->bindParam(":foto", $data['foto']);
        $stmt->bindParam(":nombre_completo", $data['nombre_completo']);
        $stmt->bindParam(":curp", $data['curp']);
        $stmt->bindParam(":numero_sired", $data['numero_sired']);
        $stmt->bindParam(":estatura", $data['estatura']);
        $stmt->bindParam(":domicilio", $data['domicilio']);
        $stmt->bindParam(":ciudad", $data['ciudad']);
        $stmt->bindParam(":telefono", $data['telefono']);
        $stmt->bindParam(":numero_playera", $data['numero_playera']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getPlayerByCurp($curp)
    {
        $query = "SELECT j.*, e.nombre_equipo, e.rama 
                  FROM " . $this->table_name . " j 
                  LEFT JOIN equipos e ON j.id_equipo = e.id 
                  WHERE j.curp = :curp AND e.validado = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":curp", $curp);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        return false;
    }

    public function getTeamPlayers($team_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id_equipo = :team_id 
                  ORDER BY numero_playera, nombre_completo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":team_id", $team_id);
        $stmt->execute();

        $players = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $players[] = $row;
        }
        return $players;
    }

    public function countTeamPlayers($team_id)
    {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table_name . " 
                  WHERE id_equipo = :team_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":team_id", $team_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function deletePlayer($player_id, $team_id)
    {
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE id = :player_id AND id_equipo = :team_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":player_id", $player_id);
        $stmt->bindParam(":team_id", $team_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Agrega estas funciones a tu clase PlayerModel

    public function countAllPlayers()
    {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function countPlayersWithSired()
    {
        $query = "SELECT COUNT(*) as total 
              FROM " . $this->table_name . " 
              WHERE numero_sired IS NOT NULL AND numero_sired != ''";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function searchPlayers($search_term)
    {
        $query = "SELECT j.*, e.nombre_equipo, e.rama, l.nombre as liga 
              FROM " . $this->table_name . " j 
              JOIN equipos e ON j.id_equipo = e.id 
              JOIN ligas l ON e.id_liga = l.id 
              WHERE j.nombre_completo LIKE :search 
                 OR j.curp LIKE :search 
                 OR j.numero_sired LIKE :search 
                 OR e.nombre_equipo LIKE :search 
              ORDER BY j.nombre_completo";

        $stmt = $this->conn->prepare($query);
        $search_term = "%" . $search_term . "%";
        $stmt->bindParam(":search", $search_term);
        $stmt->execute();

        $players = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $players[] = $row;
        }
        return $players;
    }

    public function getPlayersByTeam($team_id)
    {
        $query = "SELECT j.*, e.nombre_equipo, e.rama 
              FROM " . $this->table_name . " j 
              JOIN equipos e ON j.id_equipo = e.id 
              WHERE j.id_equipo = :team_id 
              ORDER BY j.numero_playera, j.nombre_completo";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":team_id", $team_id);
        $stmt->execute();

        $players = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $players[] = $row;
        }
        return $players;
    }
}
