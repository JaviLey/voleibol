<?php
require_once 'app/config/config.php';

// Verificar si el usuario está logueado
$isLoggedIn = isset($_SESSION['user_id']);
$userType = $isLoggedIn ? $_SESSION['user_type'] : '';

// Redirigir según el tipo de usuario
if ($isLoggedIn) {
    if ($userType == 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: delegado/dashboard.php');
    }
    exit();
}

// Si no está logueado, mostrar la página de inicio
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-volleyball-ball me-2"></i>
                <?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i> Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php"><i class="fas fa-user-plus me-1"></i> Registrarse</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-primary">Sistema de Gestión para Ligas de Voleibol</h1>
                    <p class="lead">Facilitamos la administración de documentación para torneos y ligas de voleibol en el estado de Chiapas.</p>
                    <div class="d-grid gap-2 d-md-flex">
                        <a href="register.php" class="btn btn-primary btn-lg me-md-2">
                            <i class="fas fa-user-plus me-2"></i>Registrarse
                        </a>
                        <a href="login.php" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="assets/images/volleyball-hero.png" alt="Voleibol Chiapas" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">¿Qué ofrece nuestro sistema?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-primary mb-3">
                                <i class="fas fa-users fa-3x"></i>
                            </div>
                            <h5 class="card-title">Gestión de Equipos</h5>
                            <p class="card-text">Registra y administra tus equipos de manera sencilla, tanto varonil como femenil.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-primary mb-3">
                                <i class="fas fa-id-card fa-3x"></i>
                            </div>
                            <h5 class="card-title">Cédula Digital</h5>
                            <p class="card-text">Llena la cédula de inscripción en línea sin problemas de formato.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-primary mb-3">
                                <i class="fas fa-file-pdf fa-3x"></i>
                            </div>
                            <h5 class="card-title">Generación de PDF</h5>
                            <p class="card-text">Descarga la cédula en formato PDF lista para imprimir.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-primary mb-3">
                                <i class="fas fa-credit-card fa-3x"></i>
                            </div>
                            <h5 class="card-title">Pagos en Línea</h5>
                            <p class="card-text">Gestiona los pagos de inscripción y credenciales de forma digital.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-primary mb-3">
                                <i class="fas fa-search fa-3x"></i>
                            </div>
                            <h5 class="card-title">Control de Jugadores</h5>
                            <p class="card-text">Evita que un jugador se registre en múltiples equipos.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="text-primary mb-3">
                                <i class="fas fa-chart-bar fa-3x"></i>
                            </div>
                            <h5 class="card-title">Estadísticas</h5>
                            <p class="card-text">Genera reportes y estadísticas para presentar a la federación.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo SITE_NAME; ?></h5>
                    <p>Sistema desarrollado para facilitar la gestión de ligas y torneos de voleibol en Chiapas.</p>
                </div>
                <div class="col-md-3">
                    <h5>Enlaces</h5>
                    <ul class="list-unstyled">
                        <li><a href="login.php" class="text-white">Iniciar Sesión</a></li>
                        <li><a href="register.php" class="text-white">Registrarse</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contacto</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i> info@voleibolchiapas.com</li>
                        <li><i class="fas fa-phone me-2"></i> (961) 123 4567</li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>