<!-- views/delegado/registro_equipo.php -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Registro de Equipo</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre_equipo" class="form-label">Nombre del Equipo *</label>
                                <input type="text" class="form-control" id="nombre_equipo" name="nombre_equipo" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="logo" class="form-label">Logo (opcional)</label>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="rama" class="form-label">Rama *</label>
                                <select class="form-select" id="rama" name="rama" required>
                                    <option value="">Seleccionar</option>
                                    <option value="varonil">Varonil</option>
                                    <option value="femenil">Femenil</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="categoria" class="form-label">Categoría *</label>
                                <select class="form-select" id="categoria" name="categoria" required>
                                    <option value="">Seleccionar</option>
                                    <option value="Libre">Libre</option>
                                    <option value="1ra. Division">1ra. División</option>
                                    <option value="2da. Division">2da. División</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="id_liga" class="form-label">Liga/Torneo *</label>
                            <select class="form-select" id="id_liga" name="id_liga" required>
                                <option value="">Seleccionar liga/torneo</option>
                                <?php foreach ($ligas as $liga): ?>
                                <option value="<?php echo $liga['id']; ?>"><?php echo $liga['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="comprobante_inscripcion" class="form-label">Comprobante de Pago *</label>
                            <input type="file" class="form-control" id="comprobante_inscripcion" name="comprobante_inscripcion" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="form-text">Formatos aceptados: PDF, JPG, PNG. Tamaño máximo: 2MB</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Registrar Equipo</button>
                            <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>