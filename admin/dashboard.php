<?php
require_once '../app/config/config.php';

// Verificar autenticación y permisos
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Incluir modelos
require_once '../app/models/TeamModel.php';
require_once '../app/models/PlayerModel.php';
require_once '../app/models/UserModel.php';

// Instanciar modelos
$teamModel = new TeamModel($db);
$playerModel = new PlayerModel($db);
$userModel = new UserModel($db);

// Obtener estadísticas
$stats = [
    'total_equipos' => $teamModel->countTeams(),
    'equipos_varonil' => $teamModel->countTeamsByRama('varonil'),
    'equipos_femenil' => $teamModel->countTeamsByRama('femenil'),
    'total_jugadores' => $playerModel->countAllPlayers(),
    'equipos_pendientes' => $teamModel->countPendingTeams(),
    'jugadores_con_sired' => $playerModel->countPlayersWithSired(),
    'usuarios_pendientes' => $userModel->countPendingUsers()
];

// Obtener equipos pendientes de validación
$query_pendientes = "SELECT e.*, u.nombre_completo as delegado, u.email, u.activo as usuario_activo 
                     FROM equipos e 
                     JOIN usuarios u ON e.id_usuario = u.id 
                     WHERE e.validado = 0 
                     ORDER BY e.fecha_registro DESC";
$stmt_pendientes = $db->prepare($query_pendientes);
$stmt_pendientes->execute();
$equipos_pendientes = $stmt_pendientes->fetchAll(PDO::FETCH_ASSOC);

// Obtener últimos equipos registrados
$query_ultimos = "SELECT e.*, u.nombre_completo as delegado, u.activo, l.nombre as liga 
                  FROM equipos e 
                  JOIN usuarios u ON e.id_usuario = u.id 
                  JOIN ligas l ON e.id_liga = l.id 
                  ORDER BY e.fecha_registro DESC 
                  LIMIT 5";
$stmt_ultimos = $db->prepare($query_ultimos);
$stmt_ultimos->execute();
$ultimos_equipos = $stmt_ultimos->fetchAll(PDO::FETCH_ASSOC);

// Obtener usuarios pendientes de activación
$query_usuarios_pendientes = "SELECT u.*, e.nombre_equipo 
                              FROM usuarios u 
                              LEFT JOIN equipos e ON u.id = e.id_usuario 
                              WHERE u.activo = 0 AND u.tipo = 'delegado' 
                              ORDER BY u.fecha_registro DESC";
$stmt_usuarios = $db->prepare($query_usuarios_pendientes);
$stmt_usuarios->execute();
$usuarios_pendientes = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);

// Procesar validación de equipo
if (isset($_GET['validar_equipo']) && is_numeric($_GET['validar_equipo'])) {
    $equipo_id = $_GET['validar_equipo'];
    
    $query_validar = "UPDATE equipos SET validado = 1 WHERE id = :equipo_id";
    $stmt_validar = $db->prepare($query_validar);
    $stmt_validar->bindParam(':equipo_id', $equipo_id);
    
    if ($stmt_validar->execute()) {
        // Obtener información del equipo y usuario para notificación
        $query_info = "SELECT e.nombre_equipo, u.email, u.nombre_completo 
                       FROM equipos e 
                       JOIN usuarios u ON e.id_usuario = u.id 
                       WHERE e.id = :equipo_id";
        $stmt_info = $db->prepare($query_info);
        $stmt_info->bindParam(':equipo_id', $equipo_id);
        $stmt_info->execute();
        $info_equipo = $stmt_info->fetch(PDO::FETCH_ASSOC);
        
        setSweetAlert('success', 'Equipo validado', 'El equipo ' . $info_equipo['nombre_equipo'] . ' ha sido validado correctamente. Se notificará al delegado.');
    } else {
        setSweetAlert('error', 'Error', 'No se pudo validar el equipo.');
    }
    
    header('Location: dashboard.php');
    exit();
}

// Procesar activación de usuario
if (isset($_GET['activar_usuario']) && is_numeric($_GET['activar_usuario'])) {
    $usuario_id = $_GET['activar_usuario'];
    
    $query_activar = "UPDATE usuarios SET activo = 1 WHERE id = :usuario_id";
    $stmt_activar = $db->prepare($query_activar);
    $stmt_activar->bindParam(':usuario_id', $usuario_id);
    
    if ($stmt_activar->execute()) {
        // Obtener información del usuario
        $query_info = "SELECT nombre_completo, email FROM usuarios WHERE id = :usuario_id";
        $stmt_info = $db->prepare($query_info);
        $stmt_info->bindParam(':usuario_id', $usuario_id);
        $stmt_info->execute();
        $info_usuario = $stmt_info->fetch(PDO::FETCH_ASSOC);
        
        setSweetAlert('success', 'Usuario activado', 'El usuario ' . $info_usuario['nombre_completo'] . ' ha sido activado correctamente. Ahora puede iniciar sesión.');
    } else {
        setSweetAlert('error', 'Error', 'No se pudo activar el usuario.');
    }
    
    header('Location: dashboard.php');
    exit();
}

// Procesar rechazo de usuario
if (isset($_GET['rechazar_usuario']) && is_numeric($_GET['rechazar_usuario'])) {
    $usuario_id = $_GET['rechazar_usuario'];
    
    // Obtener información del usuario antes de eliminarlo
    $query_info = "SELECT nombre_completo, email FROM usuarios WHERE id = :usuario_id";
    $stmt_info = $db->prepare($query_info);
    $stmt_info->bindParam(':usuario_id', $usuario_id);
    $stmt_info->execute();
    $info_usuario = $stmt_info->fetch(PDO::FETCH_ASSOC);
    
    // Eliminar usuario (esto también eliminará en cascada los equipos asociados si hay claves foráneas configuradas)
    $query_eliminar = "DELETE FROM usuarios WHERE id = :usuario_id";
    $stmt_eliminar = $db->prepare($query_eliminar);
    $stmt_eliminar->bindParam(':usuario_id', $usuario_id);
    
    if ($stmt_eliminar->execute()) {
        setSweetAlert('info', 'Usuario rechazado', 'El usuario ' . $info_usuario['nombre_completo'] . ' ha sido rechazado y eliminado del sistema.');
    } else {
        setSweetAlert('error', 'Error', 'No se pudo rechazar el usuario.');
    }
    
    header('Location: dashboard.php');
    exit();
}

$page_title = "Panel de Administración";
include 'partials/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include 'partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 py-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Panel de Administración</h2>
                <span class="badge bg-primary">Administrador</span>
            </div>

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-2 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <i class="fas fa-users fa-3x text-primary mb-2"></i>
                            <h3><?php echo $stats['total_equipos']; ?></h3>
                            <p class="card-text">Equipos Registrados</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <i class="fas fa-male fa-3x text-info mb-2"></i>
                            <h3><?php echo $stats['equipos_varonil']; ?></h3>
                            <p class="card-text">Varoniles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <i class="fas fa-female fa-3x text-danger mb-2"></i>
                            <h3><?php echo $stats['equipos_femenil']; ?></h3>
                            <p class="card-text">Femeniles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <i class="fas fa-user-friends fa-3x text-success mb-2"></i>
                            <h3><?php echo $stats['total_jugadores']; ?></h3>
                            <p class="card-text">Jugadores</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-3x text-warning mb-2"></i>
                            <h3><?php echo $stats['equipos_pendientes']; ?></h3>
                            <p class="card-text">Pendientes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <i class="fas fa-user-clock fa-3x text-secondary mb-2"></i>
                            <h3><?php echo $stats['usuarios_pendientes']; ?></h3>
                            <p class="card-text">Usuarios por activar</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Usuarios Pendientes de Activación -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-user-clock me-2"></i>Usuarios Pendientes de Activación</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($usuarios_pendientes) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Email</th>
                                                <th>Equipo</th>
                                                <th>Registro</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($usuarios_pendientes as $usuario): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
                                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                                <td><?php echo !empty($usuario['nombre_equipo']) ? htmlspecialchars($usuario['nombre_equipo']) : 'Sin equipo'; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                                                <td>
                                                    <a href="dashboard.php?activar_usuario=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-success" title="Activar usuario">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="dashboard.php?rechazar_usuario=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-danger" title="Rechazar usuario" onclick="return confirm('¿Está seguro de rechazar este usuario? Se eliminarán todos sus datos.')">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <p>No hay usuarios pendientes de activación</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Equipos Pendientes de Validación -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Equipos Pendientes de Validación</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($equipos_pendientes) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Equipo</th>
                                                <th>Delegado</th>
                                                <th>Estado Usuario</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($equipos_pendientes as $equipo): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($equipo['nombre_equipo']); ?></td>
                                                <td><?php echo htmlspecialchars($equipo['delegado']); ?></td>
                                                <td>
                                                    <?php if ($equipo['usuario_activo']): ?>
                                                        <span class="badge bg-success">Activo</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="dashboard.php?validar_equipo=<?php echo $equipo['id']; ?>" class="btn btn-sm btn-success" title="Validar equipo">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="equipos.php?ver=<?php echo $equipo['id']; ?>" class="btn btn-sm btn-info" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if (!$equipo['usuario_activo']): ?>
                                                    <a href="dashboard.php?activar_usuario=<?php echo $equipo['id_usuario']; ?>" class="btn btn-sm btn-primary" title="Activar usuario">
                                                        <i class="fas fa-user-check"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <p>No hay equipos pendientes de validación</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Últimos Equipos Registrados -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Últimos Equipos Registrados</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($ultimos_equipos) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Equipo</th>
                                                <th>Delegado</th>
                                                <th>Estado</th>
                                                <th>Usuario</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ultimos_equipos as $equipo): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($equipo['nombre_equipo']); ?></td>
                                                <td><?php echo htmlspecialchars($equipo['delegado']); ?></td>
                                                <td>
                                                    <?php if ($equipo['validado']): ?>
                                                        <span class="badge bg-success">Validado</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Pendiente</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($equipo['activo']): ?>
                                                        <span class="badge bg-success">Activo</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p>No hay equipos registrados</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Acciones Rápidas -->
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="equipos.php" class="btn btn-outline-primary text-start">
                                    <i class="fas fa-users me-2"></i>Gestionar Equipos
                                </a>
                                <a href="jugadores.php" class="btn btn-outline-secondary text-start">
                                    <i class="fas fa-user-friends me-2"></i>Buscar Jugadores
                                </a>
                                <a href="usuarios.php" class="btn btn-outline-success text-start">
                                    <i class="fas fa-users-cog me-2"></i>Gestionar Usuarios
                                </a>
                                <a href="pagos.php" class="btn btn-outline-warning text-start">
                                    <i class="fas fa-money-bill-wave me-2"></i>Control de Pagos
                                </a>
                                <a href="credenciales.php" class="btn btn-outline-info text-start">
                                    <i class="fas fa-id-card me-2"></i>Credencialización
                                </a>
                                <a href="reportes.php" class="btn btn-outline-dark text-start">
                                    <i class="fas fa-chart-bar me-2"></i>Generar Reportes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>