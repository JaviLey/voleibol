<?php
// Determinar la página actual para resaltar el enlace activo
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="col-md-3 col-lg-2 sidebar py-3">
    <div class="text-center mb-4">
        <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 80px; height: 80px;">
            <i class="fas fa-user-tie fa-2x text-white"></i>
        </div>
        <h6 class="mt-2"><?php echo $_SESSION['user_name']; ?></h6>
        <p class="text-muted small">Delegado/Entrenador</p>
    </div>
    
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'equipos.php' ? 'active' : ''; ?>" href="equipos.php">
                <i class="fas fa-users me-2"></i>Mis Equipos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'jugadores.php' ? 'active' : ''; ?>" href="jugadores.php">
                <i class="fas fa-user-friends me-2"></i>Jugadores
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'cedula.php' ? 'active' : ''; ?>" href="cedula.php">
                <i class="fas fa-file-pdf me-2"></i>Cédula
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'credenciales.php' ? 'active' : ''; ?>" href="credenciales.php">
                <i class="fas fa-id-card me-2"></i>Credenciales
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page == 'perfil.php' ? 'active' : ''; ?>" href="perfil.php">
                <i class="fas fa-user-edit me-2"></i>Mi Perfil
            </a>
        </li>
    </ul>
    
    <div class="mt-4 p-3 bg-light rounded">
        <h6 class="text-center">Mis Estadísticas</h6>
        <div class="small">
            <div class="d-flex justify-content-between">
                <span>Equipos:</span>
                <span class="fw-bold"><?php echo $stats['total_equipos']; ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Validados:</span>
                <span class="fw-bold text-success"><?php echo $stats['equipos_validados']; ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Jugadores:</span>
                <span class="fw-bold text-info"><?php echo $stats['total_jugadores']; ?></span>
            </div>
        </div>
    </div>
</div>