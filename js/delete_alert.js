document.addEventListener('DOMContentLoaded', () => {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.getAttribute('data-id'); // Obtener el ID del post desde el botón
            Swal.fire({
                title: "¿Estás seguro?",
                text: "¡No podrás revertir esto!",
                icon: "error",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, elimínalo"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `includes/delete_post.php?id=${postId}`;
                }
            });
        });
    });

    // Manejar la eliminación de respuestas
    const deleteResponseButtons = document.querySelectorAll('.delete-btn-response');
    deleteResponseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const responseId = this.getAttribute('data-id');
            Swal.fire({
                title: "¿Estás seguro?",
                text: "¡No podrás revertir esto!",
                icon: "error",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, elimínalo"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `includes/delete_response.php?id=${responseId}`;
                }
            });
        });
    });

    // Manejar la eliminación de respuestas anidadas
    const deleteNestedResponseButtons = document.querySelectorAll('.delete-btn-nested');
    deleteNestedResponseButtons.forEach(button => {
        button.addEventListener('click', function() {
            const nestedResponseId = this.getAttribute('data-id');
            Swal.fire({
                title: "¿Estás seguro?",
                text: "¡No podrás revertir esto!",
                icon: "error",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sí, elimínalo"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `includes/delete_nested_response.php?id=${nestedResponseId}`;
                }
            });
        });
    });
});
