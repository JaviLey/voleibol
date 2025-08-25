<div class="col-md-3 col-lg-2 sidebar py-3">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'equipos.php' ? 'active' : ''; ?>" href="equipos.php">
                <i class="fas fa-users me-2"></i>Gestión de Equipos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'jugadores.php' ? 'active' : ''; ?>" href="jugadores.php">
                <i class="fas fa-user-friends me-2"></i>Jugadores
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'ligas.php' ? 'active' : ''; ?>" href="ligas.php">
                <i class="fas fa-trophy me-2"></i>Ligas/Torneos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pagos.php' ? 'active' : ''; ?>" href="pagos.php">
                <i class="fas fa-credit-card me-2"></i>Control de Pagos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'credenciales.php' ? 'active' : ''; ?>" href="credenciales.php">
                <i class="fas fa-id-card me-2"></i>Credencialización
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reportes.php' ? 'active' : ''; ?>" href="reportes.php">
                <i class="fas fa-chart-bar me-2"></i>Reportes y Estadísticas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'comunicaciones.php' ? 'active' : ''; ?>" href="comunicaciones.php">
                <i class="fas fa-envelope me-2"></i>Comunicaciones
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'configuracion.php' ? 'active' : ''; ?>" href="configuracion.php">
                <i class="fas fa-cog me-2"></i>Configuración
            </a>
        </li>
    </ul>
</div>