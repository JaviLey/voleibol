// assets/js/main.js
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar alertas de SweetAlert si existen en sesión
    <?php if (isset($_SESSION['swal'])): ?>
    Swal.fire({
        icon: '<?php echo $_SESSION['swal']['icon']; ?>',
        title: '<?php echo $_SESSION['swal']['title']; ?>',
        text: '<?php echo $_SESSION['swal']['text']; ?>',
        confirmButtonText: 'Aceptar'
    });
    <?php unset($_SESSION['swal']); ?>
    <?php endif; ?>
    
    // Validación de formularios
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
    
    // Previsualización de imágenes
    const imageInputs = document.querySelectorAll('input[type="file"][accept="image/*"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            const previewId = this.getAttribute('data-preview');
            if (previewId && this.files && this.files[0]) {
                const reader = new FileReader();
                const preview = document.getElementById(previewId);
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
});