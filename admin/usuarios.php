<?php
require_once '../app/config/config.php';

// Verificar autenticación y permisos
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Incluir modelos
require_once '../app/models/UserModel.php';
$userModel = new UserModel($db);

// Obtener todos los usuarios
$query = "SELECT u.*, e.nombre_equipo 
          FROM usuarios u 
          LEFT JOIN equipos e ON u.id = e.id_usuario 
          ORDER BY u.fecha_registro DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar activación de usuario
if (isset($_GET['activar']) && is_numeric($_GET['activar'])) {
    $usuario_id = $_GET['activar'];
    
    if ($userModel->activateUser($usuario_id)) {
        setSweetAlert('success', 'Usuario activado', 'El usuario ha sido activado correctamente.');
    } else {
        setSweetAlert('error', 'Error', 'No se pudo activar el usuario.');
    }
    
    header('Location: usuarios.php');
    exit();
}

// Procesar desactivación de usuario
if (isset($_GET['desactivar']) && is_numeric($_GET['desactivar'])) {
    $usuario_id = $_GET['desactivar'];
    
    $query = "UPDATE usuarios SET activo = 0 WHERE id = :usuario_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':usuario_id', $usuario_id);
    
    if ($stmt->execute()) {
        setSweetAlert('success', 'Usuario desactivado', 'El usuario ha sido desactivado correctamente.');
    } else {
        setSweetAlert('error', 'Error', 'No se pudo desactivar el usuario.');
    }
    
    header('Location: usuarios.php');
    exit();
}

$page_title = "Gestión de Usuarios";
include 'partials/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include 'partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 py-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Gestión de Usuarios</h2>
                <a href="reportes.php?tipo=usuarios" class="btn btn-outline-primary">
                    <i class="fas fa-download me-2"></i>Exportar
                </a>
            </div>

            <!-- Tabla de usuarios -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Lista de Usuarios</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Equipo</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                    <td><?php echo htmlspecialchars($usuario['telefono']); ?></td>
                                    <td><?php echo !empty($usuario['nombre_equipo']) ? htmlspecialchars($usuario['nombre_equipo']) : 'N/A'; ?></td>
                                    <td>
                                        <?php if ($usuario['tipo'] == 'admin'): ?>
                                            <span class="badge bg-danger">Administrador</span>
                                        <?php else: ?>
                                            <span class="badge bg-info">Delegado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($usuario['activo']): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <?php if (!$usuario['activo'] && $usuario['tipo'] == 'delegado'): ?>
                                            <a href="usuarios.php?activar=<?php echo $usuario['id']; ?>" class="btn btn-success" title="Activar usuario">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <?php elseif ($usuario['activo'] && $usuario['tipo'] == 'delegado'): ?>
                                            <a href="usuarios.php?desactivar=<?php echo $usuario['id']; ?>" class="btn btn-warning" title="Desactivar usuario">
                                                <i class="fas fa-times"></i>
                                            </a>
                                            <?php endif; ?>
                                            <?php if ($usuario['tipo'] == 'delegado'): ?>
                                            <a href="equipos.php?usuario=<?php echo $usuario['id']; ?>" class="btn btn-info" title="Ver equipos">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>