// Carousel Logic
let currentIndex = 0; 
const images = document.querySelectorAll('.carousel-images img');
const totalImages = images.length;

function showNextImage() {
    currentIndex = (currentIndex + 1) % totalImages;
    const offset = -currentIndex * 100;
    document.querySelector('.carousel-images').style.transform = `translateX(${offset}%)`;
}

function startCarousel() {
    setInterval(showNextImage, 5000); // Cambia de imagen cada 5 segundos
}

document.addEventListener('DOMContentLoaded', function () {
    startCarousel(); // Iniciar carrusel después de cargar

    const botonesAgregar = document.querySelectorAll('.agregar-carrito');
    botonesAgregar.forEach(boton => {
        boton.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const precio = this.getAttribute('data-precio');
            const imagen = this.getAttribute('data-imagen');

            fetch('php/agregarCarrito.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&nombre=${nombre}&precio=${precio}&imagen=${imagen}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    actualizarCarrito();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});


//carrito
document.addEventListener('DOMContentLoaded', function () {
    const botonesAgregar = document.querySelectorAll('.agregar-carrito');

    botonesAgregar.forEach(boton => {
        boton.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const precio = this.getAttribute('data-precio');
            const imagen = this.getAttribute('data-imagen');

            // Enviar datos al servidor con fetch
            fetch('php/agregarCarrito.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}&nombre=${nombre}&precio=${precio}&imagen=${imagen}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    actualizarCarrito();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});




function actualizarCarrito() {
    fetch('php/verCarrito.php') // Crear un archivo PHP que devuelva los productos en formato JSON
        .then(response => response.json())
        .then(data => {
            const carritoContainer = document.querySelector('#lista-carrito tbody');
            carritoContainer.innerHTML = ''; // Vacía el carrito actual

            if (data.length > 0) {
                data.forEach(item => {
                    const row = `
                        <tr>
                            <td><img src="${item.imagen}" width="50"></td>
                            <td>${item.nombre}</td>
                            <td>$${item.precio}</td>
                        </tr>
                    `;
                    carritoContainer.innerHTML += row;
                });
            } else {
                carritoContainer.innerHTML = '<tr><td colspan="3">El carrito está vacío</td></tr>';
            }
        })
        .catch(error => console.error('Error al actualizar el carrito:', error));
}






//carrito inicio menu

