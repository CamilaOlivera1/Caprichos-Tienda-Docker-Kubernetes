<?php 
// Inicia la sesión del usuario para poder manejar login, carrito y perfil
session_start(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Caprichos</title>
  <link rel="icon" href="imagenes/favicon.jpg" type="image/png" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <!-- Cabecera principal -->
  <header class="header sticky-header">
    <div class="menu container">
      <!-- Logo de la tienda -->
      <img class="logo-1" src="imagenes/logo caprichos.jpg" alt="Logo" />

      <!-- Menú de navegación -->
      <nav class="nav-menu-container">
        <ul class="nav-menu">
          <li><a href="index.php">Inicio</a></li>
          <li>
            <a href="php/productos.php">Productos <span class="arrow">&#9662;</span></a>
            <!-- Submenú de categorías -->
            <ul class="submenu">
              <li><a href="php/productos.php?categoria=Caprichos%20Women">Caprichos Women</a></li>
              <li><a href="php/productos.php?categoria=Caprichos%20Men">Caprichos Men</a></li>
              <li>
                <a href="caprichos-children.html">Caprichos Children</a>
                <ul class="sub-submenu">
                  <li><a href="php/productos.php?categoria=Caprichos%20Children">Niños</a></li>
                  <li><a href="php/productos.php?categoria=Caprichos%20Children">Niñas</a></li>
                </ul>
              </li>
            </ul>
          </li>
          <li><a href="#footer">Contacto</a></li>
        </ul>
      </nav>

      <!-- Menú de usuario -->
      <div class="user-menu">
        <img src="imagenes/usuario.png" alt="Usuario" class="user-icon" />
        <div class="dropdown-menu">
          <?php if (isset($_SESSION['user'])): ?>
            <!-- Si el usuario está logueado, muestra su perfil y opción de cerrar sesión -->
            <a href="php/perfil.php">Mi Perfil</a>
            <a href="php/cerrarSesion.php">Cerrar Sesión</a>
          <?php else: ?>
            <!-- Si no está logueado, muestra opciones para iniciar sesión o registrarse -->
            <a href="php/inicioSesion.php">Inicio de Sesión</a>
            <a href="php/registrarUsuario.php">Registrarme</a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Icono del carrito de compras -->
      <div id="carrito">
        <a href="php/carrito.php">
          <img src="imagenes/carrito compras.png" alt="Carrito" class="carrito-icon" />
        </a>
      </div>
    </div>
  </header>

  <!-- Carrusel de imágenes principal -->
  <section class="carousel">
    <div class="carousel-images">
        <img src="imagenes/Logo un poco mas grande.jpg" alt="Imagen 1">
        <img src="imagenes/women.jpg" alt="Imagen 2">
        <img src="imagenes/Imagen blanco carusel.jpg" alt="Imagen 3">
        <img src="imagenes/image.jpg" alt="Imagen 4">
    </div>
  </section>

  <!-- Sección de información destacada -->
  <section class="informacion-container">
    <div class="informacion-content">
        <div class="informacion-1">
            <img src="imagenes/envio.png" alt="Envio">
            <h3>Envios</h3>
            <p>Hacemos envíos con mucho cariño a los departamentos de Junín, Rivadavia y San Martín para que tus compras lleguen perfectas hasta tu puerta</p>
        </div>

        <div class="informacion-1">
            <img src="imagenes/devoluciones.png" alt="No Devoluciones">
            <h3>No hay devoluciones</h3>
            <p>No aceptamos devoluciones, pero nuestro equipo estará disponible para ayudarte si tienes algún problema</p>
        </div>
    </div>
  </section>

  <!-- Sección de categorías -->
  <section class="categorias">
    <div class="categorias-container">
      <a href="php/productos.php?categoria=Caprichos%20Women" class="card">
        <img src="imagenes/Caprichos Women.jpg" alt="Caprichos Women">
        <div class="card-text">Caprichos Woman</div>
      </a>

      <a href="php/productos.php?categoria=Caprichos%20Men" class="card">
        <img src="imagenes/Caprichos Men.png" alt="Caprichos Men">
        <div class="card-text">Caprichos Men</div>
      </a>

      <a href="php/productos.php?categoria=Caprichos%20Children" class="card">
        <img src="imagenes/Caprichos Children.jpg" alt="Caprichos Children">
        <div class="card-text">Caprichos Children</div>
      </a>
    </div>
  </section>

  <!-- Pie de página -->
  <footer id="footer" class="footer">
    <div class="footer-section">
        <h2>Sobre Nosotros</h2>
        <p>Somos "Caprichos" nos especializamos en la venta de indumentaria de alta calidad para mujeres, hombres y niños</p>
        <p>fusionando moda y funcionalidad para satisfacer las exigencias de nuestros clientes.</p>
    </div>
    <div class="footer-section">
        <h2>Medios de pago</h2>
        <p>Efectivo, Tarjeta de Crédito y Tarjeta de Débito</p>
    </div>
    <div class="footer-section">
        <h2>Redes Sociales</h2>
        <p><a href="https://www.facebook.com/groups/3033189253417410/?ref=share&mibextid=NSMWBT">Facebook</a> </p> 
        <p>
          <a href="https://www.instagram.com/caprichos_women?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==">Instagram Woman</a> 
          | <a href="https://www.instagram.com/caprichos_men?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==">Instagram Men</a>
          | <a href="https://www.instagram.com/caprichos_children?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==">Instagram Children</a>
        </p>
    </div>
    <p>&copy; 2025 Tu Tienda Online. Todos los derechos reservados.</p>
  </footer>

  <!-- Script principal -->
  <script src="js/script.js"></script>
</body>
</html>
