// Validación del formulario
document.querySelector("form").addEventListener("submit", function(e) {
    const origen = document.querySelector('input[name="origen"]').value;
    const destino = document.querySelector('input[name="destino"]').value;

    if (!origen || !destino) {
        alert("Por favor, complete todos los campos.");
        e.preventDefault(); // Evita el envío del formulario si hay campos vacíos
    }
});

// Confirmación y eliminación de paquete con recarga de la página
document.querySelectorAll(".boton.rojo").forEach(function(button) {
    button.addEventListener("click", function(e) {
        if (!confirm("¿Estás seguro de que deseas eliminar este paquete?")) {
            e.preventDefault(); // Previene la eliminación si el usuario cancela
        }
    });
});

// Eliminación de paquete sin recargar la página (usando AJAX)
document.querySelectorAll(".boton.rojo").forEach(function(button) {
    button.addEventListener("click", function(e) {
        e.preventDefault();
        const id = this.getAttribute('href').split('=')[1]; // Extrae el id del paquete

        if (confirm("¿Estás seguro de que deseas eliminar este paquete?")) {
            // Hacer una solicitud fetch para eliminar el paquete
            fetch('abm_paquetes.php?accion=eliminar&id=' + id)
                .then(response => response.text()) // Leer la respuesta
                .then(data => {
                    alert("Paquete eliminado");
                    location.reload(); // Recargar la página para reflejar los cambios
                })
                .catch(error => console.error('Error al eliminar el paquete:', error));
        }
    });
});