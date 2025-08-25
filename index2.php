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
            --success: #2ecc71;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 56px;
        }
        
        .navbar {
            background-color: var(--secondary);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1592656094267-764a451b5dbd?ixlib=rb-4.0.3') center/cover;
            color: white;
            padding: 4rem 0;
            margin-bottom: 2rem;
        }
        
        .league-card {
            transition: transform 0.3s;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            height: 100%;
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
            padding: 10px 20px;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--success);
            border: none;
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
        
        .form-section {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .dashboard-card {
            transition: all 0.3s;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            height: 100%;
            color: white;
            text-align: center;
            padding: 20px;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        
        .card-team {
            background: linear-gradient(to right, #3498db, #2c3e50);
        }
        
        .card-player {
            background: linear-gradient(to right, #2ecc71, #27ae60);
        }
        
        .card-pending {
            background: linear-gradient(to right, #e74c3c, #c0392b);
        }
        
        .card-print {
            background: linear-gradient(to right, #9b59b6, #8e44ad);
        }
        
        .player-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .player-item {
            border-left: 4px solid var(--primary);
            margin-bottom: 10px;
        }
        
        .preview-card {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-top: 20px;
            background-color: #f9f9f9;
        }
        
        .preview-image {
            max-width: 150px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        /* Modal personalizado */
        .modal-content {
            border-radius: 10px;
            border: none;
        }
        
        .modal-header {
            background-color: var(--primary);
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
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
                        <a class="nav-link active" href="#" data-section="home">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-section="leagues">Ligas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-section="login">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-section="register">Registrarse</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2 d-none d-lg-block" href="#" data-section="dashboard">Mi Dashboard</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sección de Inicio -->
    <section id="home-section" class="content-section">
        <!-- Header Section -->
        <div class="header-section">
            <div class="container text-center">
                <h1 class="display-4 fw-bold">Sistema de Gestión de Voleibol</h1>
                <p class="lead">Plataforma oficial para la administración de ligas de voleibol en Tuxtla Gutiérrez</p>
                <a href="#" class="btn btn-primary btn-lg mt-3" data-section="register">Registrar mi equipo</a>
            </div>
        </div>

        <!-- Leagues Section -->
        <section class="container mb-5">
            <h2 class="section-title">Ligas Afiliadas</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card league-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-volleyball-ball feature-icon"></i>
                            <h5 class="card-title">Liga OMA</h5>
                            <p class="card-text">Liga de Voleibol OMA, una de las más tradicionales de la ciudad.</p>
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#leagueInfoModal" data-liga="OMA">Más información</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card league-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-volleyball-ball feature-icon"></i>
                            <h5 class="card-title">México 2000</h5>
                            <p class="card-text">Liga de Voleibol México 2000, con amplia trayectoria en la región.</p>
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#leagueInfoModal" data-liga="México 2000">Más información</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card league-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-volleyball-ball feature-icon"></i>
                            <h5 class="card-title">Las Canchitas</h5>
                            <p class="card-text">Liga de Voleibol Independiente "las canchitas".</p>
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#leagueInfoModal" data-liga="Las Canchitas">Más información</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card league-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-volleyball-ball feature-icon"></i>
                            <h5 class="card-title">LIVOTUX</h5>
                            <p class="card-text">Liga Independiente de Voleibol Tuxtla LIVOTUX.</p>
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#leagueInfoModal" data-liga="LIVOTUX">Más información</button>
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
            <a href="#" class="btn btn-primary btn-lg" data-section="register">Comenzar Registro</a>
        </section>
    </section>

    <!-- Sección de Login -->
    <section id="login-section" class="content-section d-none">
        <div class="container">
            <div class="login-container">
                <h2 class="text-center mb-4">Iniciar Sesión</h2>
                <form>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Recordarme</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                    <div class="text-center mt-3">
                        <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                    </div>
                    <hr>
                    <div class="text-center">
                        <p>¿No tienes una cuenta? <a href="#" data-section="register">Regístrate aquí</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Sección de Registro -->
    <section id="register-section" class="content-section d-none">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="form-section">
                        <h2 class="section-title">Registro de Equipo</h2>
                        
                        <div class="mb-4">
                            <h5>Paso 1: Información del Equipo</h5>
                            <form>
                                <div class="mb-3">
                                    <label for="teamName" class="form-label">Nombre del Equipo</label>
                                    <input type="text" class="form-control" id="teamName" required>
                                </div>
                                <div class="mb-3">
                                    <label for="leagueSelect" class="form-label">Liga</label>
                                    <select class="form-select" id="leagueSelect" required>
                                        <option value="" selected disabled>Selecciona una liga</option>
                                        <option value="OMA">Liga de Voleibol OMA</option>
                                        <option value="MEX2000">Liga de Voleibol México 2000</option>
                                        <option value="CANCHITAS">Liga de Voleibol Independiente "las canchitas"</option>
                                        <option value="LIVOTUX">Liga Independiente de Voleibol Tuxtla LIVOTUX</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="teamPhoto" class="form-label">Logo del Equipo (Opcional)</label>
                                    <input class="form-control" type="file" id="teamPhoto" accept="image/*">
                                </div>
                            </form>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Paso 2: Documentación</h5>
                            <form>
                                <div class="mb-3">
                                    <label for="paymentReceipt" class="form-label">Comprobante de Pago de Inscripción</label>
                                    <input class="form-control" type="file" id="paymentReceipt" required accept="image/*,.pdf">
                                    <div class="form-text">Sube una imagen o PDF del comprobante de pago</div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Paso 3: Información del Delegado/Entrenador</h5>
                            <form>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="coachName" class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" id="coachName" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="coachPhone" class="form-label">Teléfono de Contacto</label>
                                        <input type="tel" class="form-control" id="coachPhone" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="coachEmail" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="coachEmail" required>
                                </div>
                                <div class="mb-3">
                                    <label for="coachAddress" class="form-label">Domicilio</label>
                                    <textarea class="form-control" id="coachAddress" rows="2" required></textarea>
                                </div>
                            </form>
                        </div>
                        
                        <div class="text-center">
                            <button type="button" class="btn btn-primary btn-lg">Enviar Solicitud de Registro</button>
                            <div class="form-text mt-2">Después de enviar, espera la validación por parte de la liga para poder registrar jugadores</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Dashboard -->
    <section id="dashboard-section" class="content-section d-none">
        <div class="container">
            <h2 class="section-title">Mi Dashboard</h2>
            
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="dashboard-card card-team">
                        <h3>2</h3>
                        <p>Equipos Registrados</p>
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="dashboard-card card-player">
                        <h3>14</h3>
                        <p>Jugadores Registrados</p>
                        <i class="fas fa-user fa-2x"></i>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="dashboard-card card-pending">
                        <h3>3</h3>
                        <p>Pendientes de Validación</p>
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="dashboard-card card-print">
                        <h3>10</h3>
                        <p>Credenciales Listas</p>
                        <i class="fas fa-print fa-2x"></i>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="form-section">
                        <h4>Registrar Nuevo Jugador</h4>
                        <form>
                            <div class="mb-3">
                                <label for="playerPhoto" class="form-label">Foto del Jugador</label>
                                <input type="file" class="form-control" id="playerPhoto" accept="image/*" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="playerName" class="form-label">Nombre Completo</label>
                                    <input type="text" class="form-control" id="playerName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="playerNumber" class="form-label">Número de Playera</label>
                                    <input type="number" class="form-control" id="playerNumber" min="1" max="99" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="playerBirthdate" class="form-label">Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" id="playerBirthdate" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="playerCurp" class="form-label">CURP</label>
                                    <input type="text" class="form-control" id="playerCurp" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="playerAddress" class="form-label">Domicilio</label>
                                <textarea class="form-control" id="playerAddress" rows="2" required></textarea>
                            </div>
                            <button type="button" class="btn btn-success">Agregar Jugador</button>
                        </form>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="form-section">
                        <h4>Jugadores Registrados</h4>
                        <div class="player-list">
                            <div class="card player-item">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Juan Pérez Hernández</h5>
                                            <p class="card-text">#10 | CURP: PEHJ020405HCHRRNA5</p>
                                        </div>
                                        <div>
                                            <span class="badge bg-success">Validado</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card player-item">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">María García López</h5>
                                            <p class="card-text">#5 | CURP: GALM020718MCHRPRB5</p>
                                        </div>
                                        <div>
                                            <span class="badge bg-warning">Pendiente</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card player-item">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="card-title">Carlos Rodríguez Martínez</h5>
                                            <p class="card-text">#7 | CURP: ROMC021231HCHDRRA2</p>
                                        </div>
                                        <div>
                                            <span class="badge bg-success">Validado</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-primary">Generar Cédula en PDF</button>
                            <button class="btn btn-outline-primary">Solicitar Validación de Credenciales</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

    <!-- Modal Información de Liga -->
    <div class="modal fade" id="leagueInfoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Información de la Liga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="leagueOMA" class="league-info">
                        <h4>Liga de Voleibol OMA</h4>
                        <p><strong>Contacto:</strong> Juan Martínez - (961) 123 4567</p>
                        <p><strong>Email:</strong> oma.voleibol@example.com</p>
                        <p><strong>Temporada:</strong> Enero - Junio 2024</p>
                        <p><strong>Costo de inscripción:</strong> $1,500 por equipo</p>
                        <p><strong>Costo de credenciales:</strong> $100 por jugador</p>
                    </div>
                    <div id="leagueMexico2000" class="league-info d-none">
                        <h4>Liga de Voleibol México 2000</h4>
                        <p><strong>Contacto:</strong> María López - (961) 234 5678</p>
                        <p><strong>Email:</strong> mexico2000.voleibol@example.com</p>
                        <p><strong>Temporada:</strong> Febrero - Julio 2024</p>
                        <p><strong>Costo de inscripción:</strong> $1,200 por equipo</p>
                        <p><strong>Costo de credenciales:</strong> $80 por jugador</p>
                    </div>
                    <div id="leagueCanchitas" class="league-info d-none">
                        <h4>Liga de Voleibol Independiente "las canchitas"</h4>
                        <p><strong>Contacto:</strong> Roberto Sánchez - (961) 345 6789</p>
                        <p><strong>Email:</strong> lascanchitas@example.com</p>
                        <p><strong>Temporada:</strong> Marzo - Agosto 2024</p>
                        <p><strong>Costo de inscripción:</strong> $1,000 por equipo</p>
                        <p><strong>Costo de credenciales:</strong> $70 por jugador</p>
                    </div>
                    <div id="leagueLivotux" class="league-info d-none">
                        <h4>Liga Independiente de Voleibol Tuxtla LIVOTUX</h4>
                        <p><strong>Contacto:</strong> Laura Méndez - (961) 456 7890</p>
                        <p><strong>Email:</strong> livotux@example.com</p>
                        <p><strong>Temporada:</strong> Abril - Septiembre 2024</p>
                        <p><strong>Costo de inscripción:</strong> $1,800 por equipo</p>
                        <p><strong>Costo de credenciales:</strong> $120 por jugador</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" data-section="register">Registrar Equipo</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navegación entre secciones
            const navLinks = document.querySelectorAll('a[data-section]');
            const contentSections = document.querySelectorAll('.content-section');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetSection = this.getAttribute('data-section');
                    
                    // Ocultar todas las secciones
                    contentSections.forEach(section => {
                        section.classList.add('d-none');
                    });
                    
                    // Mostrar la sección objetivo
                    document.getElementById(`${targetSection}-section`).classList.remove('d-none');
                    
                    // Scroll to top
                    window.scrollTo(0, 0);
                });
            });
            
            // Manejo del modal de información de ligas
            const leagueModal = document.getElementById('leagueInfoModal');
            leagueModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const league = button.getAttribute('data-liga');
                const leagueInfos = document.querySelectorAll('.league-info');
                
                // Ocultar toda la información de ligas
                leagueInfos.forEach(info => {
                    info.classList.add('d-none');
                });
                
                // Mostrar la información de la liga seleccionada
                let leagueId = '';
                switch(league) {
                    case 'OMA':
                        leagueId = 'leagueOMA';
                        break;
                    case 'México 2000':
                        leagueId = 'leagueMexico2000';
                        break;
                    case 'Las Canchitas':
                        leagueId = 'leagueCanchitas';
                        break;
                    case 'LIVOTUX':
                        leagueId = 'leagueLivotux';
                        break;
                }
                
                document.getElementById(leagueId).classList.remove('d-none');
            });
            
            // Simulación de subida de imagen y vista previa
            const photoInput = document.getElementById('playerPhoto');
            if (photoInput) {
                photoInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            // Crear vista previa si no existe
                            let preview = document.getElementById('photoPreview');
                            if (!preview) {
                                preview = document.createElement('div');
                                preview.id = 'photoPreview';
                                preview.className = 'preview-card';
                                
                                const previewImg = document.createElement('img');
                                previewImg.className = 'preview-image';
                                previewImg.alt = 'Vista previa';
                                
                                const previewText = document.createElement('p');
                                previewText.textContent = 'Vista previa de la foto';
                                
                                preview.appendChild(previewImg);
                                preview.appendChild(previewText);
                                photoInput.parentNode.appendChild(preview);
                            }
                            
                            // Actualizar la vista previa
                            const img = preview.querySelector('img');
                            img.src = e.target.result;
                        }
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
        });
    </script>
</body>
</html>