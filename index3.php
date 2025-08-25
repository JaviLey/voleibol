<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Voleibol - Tuxtla Gutiérrez</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --accent: #e74c3c;
            --light: #ecf0f1;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            background-color: var(--secondary);
        }
        
        .header-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1592656094267-764a451b5dbd?ixlib=rb-4.0.3') center/cover;
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .league-card {
            transition: transform 0.3s;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .league-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--primary);
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            background-color: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
        }
        
        .process-step {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        footer {
            background-color: var(--secondary);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fa-solid fa-volleyball"></i> Voleibol Tuxtla
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Ligas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reglamento</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="#">Iniciar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="header-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Sistema de Gestión de Voleibol</h1>
            <p class="lead">Plataforma oficial para la administración de ligas de voleibol en Tuxtla Gutiérrez</p>
            <a href="#" class="btn btn-primary btn-lg mt-3">Registrar mi equipo</a>
        </div>
    </div>

    <!-- Leagues Section -->
    <section class="container mb-5">
        <h2 class="section-title">Ligas Afiliadas</h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card league-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-basketball-ball feature-icon"></i>
                        <h5 class="card-title">Liga OMA</h5>
                        <p class="card-text">Liga de Voleibol OMA, una de las más tradicionales de la ciudad.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card league-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-basketball-ball feature-icon"></i>
                        <h5 class="card-title">México 2000</h5>
                        <p class="card-text">Liga de Voleibol México 2000, con amplia trayectoria en la región.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card league-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-basketball-ball feature-icon"></i>
                        <h5 class="card-title">Las Canchitas</h5>
                        <p class="card-text">Liga de Voleibol Independiente "las canchitas".</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card league-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-basketball-ball feature-icon"></i>
                        <h5 class="card-title">LIVOTUX</h5>
                        <p class="card-text">Liga Independiente de Voleibol Tuxtla LIVOTUX.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="container mb-5">
        <h2 class="section-title">Proceso de Inscripción</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <div>
                        <h5>Registro del Equipo</h5>
                        <p>Completa el formulario con los datos de tu equipo y sube el comprobante de pago.</p>
                    </div>
                </div>
                <div class="process-step">
                    <div class="step-number">2</div>
                    <div>
                        <h5>Validación por la Liga</h5>
                        <p>Espera la validación de tus documentos por parte de los administradores de la liga.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="process-step">
                    <div class="step-number">3</div>
                    <div>
                        <h5>Registro de Jugadores</h5>
                        <p>Agrega a todos tus jugadores con sus datos completos y fotografías.</p>
                    </div>
                </div>
                <div class="process-step">
                    <div class="step-number">4</div>
                    <div>
                        <h5>Impresión de Credenciales</h5>
                        <p>Una vez validados los pagos, imprime las credenciales en formato PDF.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="bg-light py-5">
        <div class="container">
            <h2 class="section-title">Funcionalidades Principales</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-users feature-icon"></i>
                            <h5 class="card-title">Gestión de Equipos</h5>
                            <p class="card-text">Registra y administra la información de tu equipo de manera sencilla.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-id-card feature-icon"></i>
                            <h5 class="card-title">Cédulas Digitales</h5>
                            <p class="card-text">Genera cédulas oficiales en PDF listas para imprimir y usar.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-shield-alt feature-icon"></i>
                            <h5 class="card-title">Control de Jugadores</h5>
                            <p class="card-text">Sistema que evita que un jugador esté registrado en múltiples equipos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="container my-5 text-center">
        <h2>¿Listo para registrar a tu equipo?</h2>
        <p class="lead mb-4">Comienza el proceso de inscripción para participar en las ligas de voleibol de Tuxtla</p>
        <a href="#" class="btn btn-primary btn-lg">Comenzar Registro</a>
    </section>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5>Voleibol Tuxtla</h5>
                    <p>Plataforma oficial de gestión de ligas de voleibol de Tuxtla Gutiérrez, Chiapas.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Contacto</h5>
                    <p><i class="fas fa-envelope me-2"></i> info@voleiboluxtla.com</p>
                    <p><i class="fas fa-phone me-2"></i> (961) 123 4567</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Síguenos</h5>
                    <div class="d-flex justify-content-center">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <p>&copy; 2023 Voleibol Tuxtla. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>