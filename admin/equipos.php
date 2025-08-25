<?php
require_once '../app/config/config.php';

// Verificar autenticación y permisos
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    redirect('../login.php');
}

// Incluir modelos
require_once '../app/models/TeamModel.php';
$teamModel = new TeamModel($db);

// Obtener todos los equipos
$query = "SELECT e.*, u.nombre_completo as delegado, u.email, u.telefono, l.nombre as liga 
          FROM equipos e 
          JOIN usuarios u ON e.id_usuario = u.id 
          JOIN ligas l ON e.id_liga = l.id 
          ORDER BY e.fecha_registro DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar validación de equipo
if (isset($_GET['validar']) && is_numeric($_GET['validar'])) {
    $equipo_id = $_GET['validar'];
    
    if ($teamModel->validateTeam($equipo_id)) {
        setSweetAlert('success', 'Equipo validado', 'El equipo ha sido validado correctamente.');
    } else {
        setSweetAlert('error', 'Error', 'No se pudo validar el equipo.');
    }
    
    redirect('equipos.php');
}

// Procesar eliminación de equipo
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $equipo_id = $_GET['eliminar'];
    
    // Primero eliminar jugadores y personal técnico asociado
    $query_delete_jugadores = "DELETE FROM jugadores WHERE id_equipo = :equipo_id";
    $stmt_jugadores = $db->prepare($query_delete_jugadores);
    $stmt_jugadores->bindParam(':equipo_id', $equipo_id);
    $stmt_jugadores->execute();
    
    $query_delete_staff = "DELETE FROM personal_tecnico WHERE id_equipo = :equipo_id";
    $stmt_staff = $db->prepare($query_delete_staff);
    $stmt_staff->bindParam(':equipo_id', $equipo_id);
    $stmt_staff->execute();
    
    // Luego eliminar el equipo
    $query_delete_equipo = "DELETE FROM equipos WHERE id = :equipo_id";
    $stmt_equipo = $db->prepare($query_delete_equipo);
    $stmt_equipo->bindParam(':equipo_id', $equipo_id);
    
    if ($stmt_equipo->execute()) {
        setSweetAlert('success', 'Equipo eliminado', 'El equipo y todos sus datos asociados han sido eliminados.');
    } else {
        setSweetAlert('error', 'Error', 'No se pudo eliminar el equipo.');
    }
    
    redirect('equipos.php');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Equipos - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Custom CSS -->
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include 'partials/navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'partials/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 py-3">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Gestión de Equipos</h2>
                    <a href="reportes.php?tipo=equipos" class="btn btn-outline-primary">
                        <i class="fas fa-download me-2"></i>Exportar
                    </a>
                </div>

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="filtro_estado" class="form-label">Estado</label>
                                <select class="form-select" id="filtro_estado" name="estado">
                                    <option value="">Todos</option>
                                    <option value="1">Validados</option>
                                    <option value="0">Pendientes</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtro_rama" class="form-label">Rama</label>
                                <select class="form-select" id="filtro_rama" name="rama">
                                    <option value="">Todas</option>
                                    <option value="varonil">Varonil</option>
                                    <option value="femenil">Femenil</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtro_liga" class="form-label">Liga/Torneo</label>
                                <select class="form-select" id="filtro_liga" name="liga">
                                    <option value="">Todas</option>
                                    <?php
                                    $query_ligas = "SELECT id, nombre FROM ligas WHERE activa = 1 ORDER BY nombre";
                                    $stmt_ligas = $db->prepare($query_ligas);
                                    $stmt_ligas->execute();
                                    $ligas = $stmt_ligas->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($ligas as $liga) {
                                        echo "<option value=\"{$liga['id']}\">{$liga['nombre']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtro_busqueda" class="form-label">Buscar</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="filtro_busqueda" name="busqueda" placeholder="Nombre o delegado">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabla de equipos -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lista de Equipos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tabla-equipos">
                                <thead>
                                    <tr>
                                        <th>Equipo</th>
                                        <th>Liga</th>
                                        <th>Delegado</th>
                                        <th>Contacto</th>
                                        <th>Rama</th>
                                        <th>Estado</th>
                                        <th>Registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($equipos as $equipo): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($equipo['logo'])): ?>
                                                <img src="../assets/uploads/<?php echo $equipo['logo']; ?>" alt="Logo" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                                <?php else: ?>
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-users text-white"></i>
                                                </div>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($equipo['nombre_equipo']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($equipo['categoria']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($equipo['liga']); ?></td>
                                        <td><?php echo htmlspecialchars($equipo['delegado']); ?></td>
                                        <td>
                                            <div>
                                                <small><i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($equipo['email']); ?></small>
                                                <br>
                                                <small><i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($equipo['telefono']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($equipo['rama'] == 'varonil'): ?>
                                                <span class="badge bg-info">Varonil</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Femenil</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($equipo['validado']): ?>
                                                <span class="badge bg-success">Validado</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pendiente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($equipo['fecha_registro'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="equipos.php?ver=<?php echo $equipo['id']; ?>" class="btn btn-info" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if (!$equipo['validado']): ?>
                                                <a href="equipos.php?validar=<?php echo $equipo['id']; ?>" class="btn btn-success" title="Validar equipo">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <?php endif; ?>
                                                <a href="equipos.php?editar=<?php echo $equipo['id']; ?>" class="btn btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="equipos.php?eliminar=<?php echo $equipo['id']; ?>" class="btn btn-danger" title="Eliminar" onclick="return confirm('¿Está seguro de eliminar este equipo y todos sus datos?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <!-- Mostrar alertas -->
    <?php showSweetAlert(); ?>
    
    <script>
    $(document).ready(function() {
        // Inicializar DataTables
        $('#tabla-equipos').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json'
            },
            responsive: true,
            pageLength: 10,
            order: [[6, 'desc']]
        });
        
        // Aplicar filtros desde URL
        const urlParams = new URLSearchParams(window.location.search);
        $('#filtro_estado').val(urlParams.get('estado') || '');
        $('#filtro_rama').val(urlParams.get('rama') || '');
        $('#filtro_liga').val(urlParams.get('liga') || '');
        $('#filtro_busqueda').val(urlParams.get('busqueda') || '');
    });
    </script>
</body>
</html>