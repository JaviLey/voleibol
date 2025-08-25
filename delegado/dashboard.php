<?php
require_once '../app/config/config.php';

// Verificar autenticación y permisos
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'delegado') {
    header('Location: ../login.php');
    exit();
}

// Incluir modelos
require_once '../app/models/TeamModel.php';
require_once '../app/models/PlayerModel.php';

// Instanciar modelos
$teamModel = new TeamModel($db);
$playerModel = new PlayerModel($db);

// Obtener equipos del usuario
$equipos_usuario = $teamModel->getUserTeams($_SESSION['user_id']);

// Obtener estadísticas del usuario
$stats = [
    'total_equipos' => count($equipos_usuario),
    'equipos_validados' => 0,
    'equipos_pendientes' => 0,
    'total_jugadores' => 0,
    'jugadores_registrados' => 0,
    'equipos_varonil' => 0,
    'equipos_femenil' => 0
];

foreach ($equipos_usuario as $equipo) {
    if ($equipo['validado']) {
        $stats['equipos_validados']++;
    } else {
        $stats['equipos_pendientes']++;
    }

    // Contar por categoría
    if ($equipo['categoria'] == 'varonil') {
        $stats['equipos_varonil']++;
    } else {
        $stats['equipos_femenil']++;
    }

    // Contar jugadores por equipo
    $jugadores_equipo = $playerModel->countTeamPlayers($equipo['id']);
    $stats['total_jugadores'] += $jugadores_equipo;

    if ($jugadores_equipo > 0) {
        $stats['jugadores_registrados']++;
    }
}

// Verificar si puede registrar más equipos
$puede_registrar_equipo = ($stats['total_equipos'] < 2);
$puede_registrar_varonil = ($stats['equipos_varonil'] < 2);
$puede_registrar_femenil = ($stats['equipos_femenil'] < 2);

// Procesar solicitud de credenciales
if (isset($_POST['solicitar_credenciales'])) {
    $equipo_id = $_POST['equipo_id'];
    $cantidad = $_POST['cantidad'];
    $metodo_pago = $_POST['metodo_pago'];

    // Procesar comprobante de pago
    $comprobante_path = null;
    if (isset($_FILES['comprobante_pago']) && $_FILES['comprobante_pago']['error'] == UPLOAD_ERR_OK) {
        $comprobante_info = pathinfo($_FILES['comprobante_pago']['name']);
        $comprobante_ext = strtolower($comprobante_info['extension']);

        // Validar tipo de archivo
        if (in_array($comprobante_ext, array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES))) {
            $comprobante_name = uniqid() . '.' . $comprobante_ext;
            $comprobante_path = 'comprobantes_credenciales/' . $comprobante_name;
            $comprobante_target = UPLOAD_DIR . $comprobante_path;

            if (move_uploaded_file($_FILES['comprobante_pago']['tmp_name'], $comprobante_target)) {
                // Insertar solicitud de credenciales
                $query = "INSERT INTO pagos_credenciales (id_equipo, cantidad, metodo_pago, comprobante) 
                         VALUES (:id_equipo, :cantidad, :metodo_pago, :comprobante)";

                $stmt = $db->prepare($query);
                $stmt->bindParam(':id_equipo', $equipo_id);
                $stmt->bindParam(':cantidad', $cantidad);
                $stmt->bindParam(':metodo_pago', $metodo_pago);
                $stmt->bindParam(':comprobante', $comprobante_path);

                if ($stmt->execute()) {
                    setSweetAlert('success', 'Solicitud enviada', 'Tu solicitud de credenciales ha sido enviada correctamente. Será validada por la administración.');
                } else {
                    setSweetAlert('error', 'Error', 'No se pudo procesar tu solicitud. Intenta nuevamente.');
                }
            } else {
                setSweetAlert('error', 'Error', 'No se pudo subir el comprobante. Intenta nuevamente.');
            }
        } else {
            setSweetAlert('error', 'Error', 'Formato de archivo no válido. Formatos permitidos: ' . implode(', ', array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES)));
        }
    } else {
        setSweetAlert('error', 'Error', 'Debes subir un comprobante de pago.');
    }

    header('Location: dashboard.php');
    exit();
}

$page_title = "Panel de Delegado";
include 'partials/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include 'partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 py-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Panel de Delegado/Entrenador</h2>
                <span class="badge bg-info">Delegado</span>
            </div>

            <!-- Bienvenida -->
            <div class="alert alert-primary">
                <h4 class="alert-heading"><i class="fas fa-volleyball-ball me-2"></i>Bienvenido, <?php echo $_SESSION['user_name']; ?></h4>
                <p class="mb-0">Desde aquí puedes gestionar tus equipos, jugadores y solicitar credenciales.</p>
            </div>

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <i class="fas fa-users fa-3x text-primary mb-2"></i>
                            <h3><?php echo $stats['total_equipos']; ?></h3>
                            <p class="card-text">Mis Equipos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                            <h3><?php echo $stats['equipos_validados']; ?></h3>
                            <p class="card-text">Equipos Validados</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <i class="fas fa-clock fa-3x text-warning mb-2"></i>
                            <h3><?php echo $stats['equipos_pendientes']; ?></h3>
                            <p class="card-text">Pendientes</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card text-center">
                        <div class="card-body">
                            <i class="fas fa-user-friends fa-3x text-info mb-2"></i>
                            <h3><?php echo $stats['total_jugadores']; ?></h3>
                            <p class="card-text">Jugadores Registrados</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mis Equipos -->
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Mis Equipos</h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($equipos_usuario) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Equipo</th>
                                                <th>Liga/Torneo</th>
                                                <th>Rama</th>
                                                <th>Jugadores</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($equipos_usuario as $equipo): ?>
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
                                                            <strong><?php echo htmlspecialchars($equipo['nombre_equipo']); ?></strong>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($equipo['nombre_liga']); ?></td>
                                                    <td>
                                                        <?php if ($equipo['rama'] == 'varonil'): ?>
                                                            <span class="badge bg-info">Varonil</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Femenil</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $num_jugadores = $playerModel->countTeamPlayers($equipo['id']);
                                                        echo $num_jugadores . ' / 14';
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($equipo['validado']): ?>
                                                            <span class="badge bg-success">Validado</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning">En revisión</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="equipo.php?id=<?php echo $equipo['id']; ?>" class="btn btn-info" title="Ver equipo">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <?php if ($equipo['validado']): ?>
                                                                <a href="jugadores.php?equipo=<?php echo $equipo['id']; ?>" class="btn btn-primary" title="Gestionar jugadores">
                                                                    <i class="fas fa-user-plus"></i>
                                                                </a>
                                                                <button class="btn btn-success" title="Solicitar credenciales" data-bs-toggle="modal" data-bs-target="#credencialesModal" data-equipo-id="<?php echo $equipo['id']; ?>" data-equipo-nombre="<?php echo htmlspecialchars($equipo['nombre_equipo']); ?>">
                                                                    <i class="fas fa-id-card"></i>
                                                                </button>
                                                            <?php else: ?>
                                                                <span class="btn btn-secondary disabled" title="Esperando validación">
                                                                    <i class="fas fa-clock"></i>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                    <h5>No tienes equipos registrados</h5>
                                    <p class="text-muted">Registra tu primer equipo para comenzar</p>
                                    <a href="../register.php?paso=equipo" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Registrar Equipo
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <?php if ($puede_registrar_equipo): ?>
                                    <button class="btn btn-outline-primary text-start" data-bs-toggle="modal" data-bs-target="#registroEquipoModal">
                                        <i class="fas fa-plus me-2"></i>Registrar Nuevo Equipo
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary text-start" disabled>
                                        <i class="fas fa-ban me-2"></i>Límite de equipos alcanzado
                                    </button>
                                <?php endif; ?>
                                <?php if ($stats['equipos_validados'] > 0): ?>
                                    <a href="jugadores.php" class="btn btn-outline-success text-start">
                                        <i class="fas fa-user-plus me-2"></i>Gestionar Jugadores
                                    </a>
                                    <a href="cedula.php" class="btn btn-outline-warning text-start">
                                        <i class="fas fa-file-pdf me-2"></i>Generar Cédula
                                    </a>
                                <?php endif; ?>
                                <a href="perfil.php" class="btn btn-outline-secondary text-start">
                                    <i class="fas fa-user-edit me-2"></i>Editar Perfil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notificaciones -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Notificaciones</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($stats['equipos_pendientes'] > 0): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Tienes <?php echo $stats['equipos_pendientes']; ?> equipo(s) en revisión</strong>
                                    <p class="mb-0 small">La administración validará tu equipo pronto.</p>
                                </div>
                            <?php endif; ?>

                            <?php if ($stats['equipos_validados'] > 0 && $stats['total_jugadores'] == 0): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>¡Felicidades! Tu equipo ha sido validado</strong>
                                    <p class="mb-0 small">Ahora puedes registrar a tus jugadores.</p>
                                </div>
                            <?php endif; ?>

                            <?php if ($stats['total_jugadores'] > 0 && $stats['total_jugadores'] < 6): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Mínimo de jugadores</strong>
                                    <p class="mb-0 small">Recuerda que necesitas al menos 6 jugadores para completar tu equipo.</p>
                                </div>
                            <?php endif; ?>

                            <?php if ($stats['equipos_pendientes'] == 0 && $stats['equipos_validados'] == 0): ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No hay notificaciones</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para solicitar credenciales -->
<div class="modal fade" id="credencialesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Solicitar Credenciales</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="equipo_id" id="equipo_id">
                    <p id="equipo_nombre_text" class="fw-bold"></p>

                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Número de credenciales necesarias:</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" max="25" required>
                        <div class="form-text">Incluye jugadores, cuerpo técnico y delegados.</div>
                    </div>

                    <div class="mb-3">
                        <label for="metodo_pago" class="form-label">Método de pago:</label>
                        <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                            <option value="">Seleccionar método</option>
                            <option value="transferencia">Transferencia bancaria</option>
                            <option value="deposito">Depósito</option>
                            <option value="oxxo">OXXO</option>
                            <option value="efectivo">Efectivo</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="comprobante_pago" class="form-label">Comprobante de pago:</label>
                        <input type="file" class="form-control" id="comprobante_pago" name="comprobante_pago" accept=".jpg,.jpeg,.png,.pdf" required>
                        <div class="form-text">Formatos: JPG, PNG, PDF. Máx: 2MB</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="solicitar_credenciales" class="btn btn-primary">Solicitar Credenciales</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para registrar nuevo equipo -->
<div class="modal fade" id="registroEquipoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Nuevo Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="../app/controllers/registro_equipo.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

                    <h5 class="mb-3 text-primary">Datos del Equipo</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre_equipo" class="form-label">Nombre del Equipo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre_equipo" name="nombre_equipo" required
                                minlength="3" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ0-9\s]+"
                                title="Solo letras, números y espacios permitidos">
                            <div class="form-text">Mínimo 3 caracteres. Solo letras, números y espacios.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="">Seleccione una categoría</option>
                                <option value="varonil" <?php echo (!$puede_registrar_varonil) ? 'disabled' : ''; ?>>Varonil</option>
                                <option value="femenil" <?php echo (!$puede_registrar_femenil) ? 'disabled' : ''; ?>>Femenil</option>
                            </select>
                            <div class="form-text">
                                <?php
                                if (!$puede_registrar_varonil && !$puede_registrar_femenil) {
                                    echo "Ya tienes el máximo de equipos permitidos (2).";
                                } elseif (!$puede_registrar_varonil) {
                                    echo "Ya tienes 2 equipos varoniles. Solo puedes registrar femeniles.";
                                } elseif (!$puede_registrar_femenil) {
                                    echo "Ya tienes 2 equipos femeniles. Solo puedes registrar varoniles.";
                                } else {
                                    echo "Seleccione la categoría en la que participará su equipo.";
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rama" class="form-label">Rama <span class="text-danger">*</span></label>
                            <select class="form-select" id="rama" name="rama" required>
                                <option value="">Seleccione la rama</option>
                                <option value="libre">Libre</option>
                                <option value="primera">1a. División</option>
                                <option value="segunda">2a. División</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="logo" class="form-label">Logo del Equipo (Opcional)</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept=".jpg,.jpeg,.png,.gif">
                            <div class="form-text">Formatos: JPG, PNG, GIF. Máx: 2MB</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="comprobante_pago" class="form-label">Comprobante de Pago <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="comprobante_pago" name="comprobante_pago" required
                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <div class="form-text">Formatos: JPG, PNG, PDF, DOC. Máx: 2MB</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="referencia_pago" class="form-label">Número de Referencia de Pago <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="referencia_pago" name="referencia_pago" required
                                minlength="5" pattern="[A-Za-z0-9\-]+"
                                title="Solo letras, números y guiones permitidos">
                            <div class="form-text">Ingrese el número de referencia, transacción o folio de su pago.</div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Importante:</strong> Recuerda que solo puedes tener un máximo de 2 equipos
                        (pueden ser dos varoniles, dos femeniles o uno de cada uno).
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="registrar_equipo" class="btn btn-primary"
                        <?php echo (!$puede_registrar_equipo) ? 'disabled' : ''; ?>>
                        Registrar Equipo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php include 'partials/footer.php'; ?>

<script>
    $(document).ready(function() {
        // Configurar modal de credenciales
        $('#credencialesModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var equipoId = button.data('equipo-id');
            var equipoNombre = button.data('equipo-nombre');

            var modal = $(this);
            modal.find('#equipo_id').val(equipoId);
            modal.find('#equipo_nombre_text').text('Solicitar credenciales para: ' + equipoNombre);

            // Establecer cantidad por defecto (14 jugadores + 6 staff = 20)
            modal.find('#cantidad').val(20);
        });
    });
</script>