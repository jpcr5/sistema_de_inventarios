<?php
session_start();

// Verificar que el usuario esté logueado
if(!isset($_SESSION['usuario'])){
    // Si no hay sesión, vuelve al login
    header("Location: ../inicio_registro.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <!-- Hace que la página se vea bien en celulares y tablets -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de Inventarios</title>
  <!-- Vincula el archivo de estilos externo (deben estar en la misma carpeta) -->
  <link rel="stylesheet" href="../estilos/estilos_inventario.css">
  <link rel="icon" href="../imagenes/caja.png">
</head>
<body>

<!--  comentarios para los panas que lean el código:
  PD: perdon la demora xdddd
     header — Barra de arriba
     Tiene: logo, botón, campana, avatar :v-->
<header class="header">

  <!-- Logo: clic para navegar a la vista de Inicio -->
  <div class="header-logo" onclick="mostrarVista('inicio')">
    <!-- Ícono SVG de una caja (representa inventario) -->
    <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
    </svg>
    <h3>Sistema de inventarios</h3>
  </div>

  <!-- Botón: llama a toggleSidebar() para abrir/cerrar el menú lateral -->
  <button class="hamburger">
    <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
  </button>

  <!-- Seccion del lado derecho del header -->
  <div class="header-right">

    <!-- Campana con dropdown de stock bajo -->
<div style="position:relative;">
  <button class="btn-icon" id="btn-campana">
    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
    </svg>
  </button>

  <!-- Panel emergente -->
  <div id="panel-notif" style="
    display:none;
    position:absolute;
    top:calc(100% + 10px);
    right:0;
    width:290px;
    background:#0f3d22;
    border:1px solid #27c95c;
    border-radius:12px;
    box-shadow:0 8px 24px rgba(0,0,0,0.4);
    z-index:200;
    overflow:hidden;
  ">
    <div style="padding:12px 16px; border-bottom:1px solid rgba(255,255,255,0.1); font-weight:700; color:#e8f5ee; font-size:0.95rem;">
      ! Stock bajo
    </div>
    <!-- Las notificaciones se generan aquí dinámicamente -->
    <div id="lista-notif"></div>
  </div>
</div>

    <!-- Avatar del usuario: clic navega a Mi Perfil -->
    <div class="avatar-btn">
      <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
      </svg>
    </div>

  </div>
</header>

<!-- Contenedor principal (contenido) -->
<div class="layout">

  <!-- SIDEBAR — Menú lateral de navegación
       id="sidebar" hace que JavaScript lo colapse -->
  <aside class="sidebar" id="sidebar">
<!--botones de la izquierda -->
    <!-- Botón Inicio: clase "active" -->
    <button class="nav-link active" id="nav-inicio">
      <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.75L12 3l9 6.75V21a1 1 0 01-1 1H4a1 1 0 01-1-1V9.75z"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 22V12h6v10"/>
      </svg>
      Inicio
    </button>

    <!-- Botón Inventario -->
    <button class="nav-link" id="nav-inventario">
      <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
      </svg>
      Inventario
    </button>

    <!-- Botón Mi perfil -->
    <button class="nav-link" id="nav-perfil">
      <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
      </svg>
      Mi perfil
    </button>

    <!-- Botón Cerrar sesión -->
    <button class="nav-link nav-logout" onclick="window.location='../php/cerrar_sesion.php'">
      <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
      </svg>
      Cerrar sesión
    </button>

  </aside>

  <!-- contenido donde se muestran las vistas -->
  <main class="main">

    <!-- INICIO
         Se muestra por defecto (clase "activa").
         Contiene el banner hero y las estadísticas. -->
    <div class="vista activa" id="vista-inicio">

      <h1 class="page-title">Bienvenido de nuevo Juan</h1>

      <!-- Banner con degradado de colores -->
      <div class="hero-banner">
        <div class="hero-text">
          <h2>Sistema de inventarios</h2>
          <p>Administra tus productos, movimientos y reportes desde un solo lugar.</p>
          <!-- Botón que navega directamente a la vista de Inventario -->
          <button class="btn btn-primary" onclick="mostrarVista('inventario')">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"> 
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
            </svg>
            Ver mi inventario
          </button>
        </div>
        <div class="hero-img">📦📋</div>
      </div>

      <!-- Fila de tarjetas de estadísticas -->
      <div class="stats-row">

        <!-- Tarjeta: total de productos en inventario
             id="stat-inicio-productos" es actualizado por JavaScript
             cuando se agregan/eliminan productos -->
        <div class="stat-card">
          <div class="stat-icon">
            <svg width="42" height="42" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
            </svg>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-info">
            <div class="stat-label">Inventario general</div>
            <div class="stat-number" id="stat-inicio-productos">0</div>
            <div class="stat-sub">productos registrados</div>
          </div>
        </div>

        <!-- Tarjeta: total de movimientos del mes -->
        <div class="stat-card">
          <div class="stat-icon">
            <svg width="42" height="42" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/>
            </svg>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-info">
<div class="stat-label">Movimientos</div>
<div class="stat-number stat-movimientos">0</div>
<div class="stat-sub">Hoy</div>
          </div>
        </div>

      </div>
    </div>
    <!-- FIN -->


    <!-- INVENTARIO-->
    <div class="vista" id="vista-inventario">

      <h1 class="page-title">Inventario</h1>

      <div id="alerta-inv" class="alert"></div>

      <!-- dos columnas: Añadir | Actualizar -->
      <div class="inv-grid">

        <!--Añadir producto -->
        <div class="card">
          <div class="card-title">Añadir producto</div>

          <!-- nombre del nuevo producto -->
          <div class="form-group">
            <label class="form-label">Nombre del producto</label>
            <input type="text" class="form-input" id="inp-nombre" placeholder="Ej: Veneno de rata">
          </div>

          <!--cantidad inicial de stock -->
          <div class="form-group">
            <label class="form-label">Cantidad inicial</label>
            <input type="number" class="form-input" id="inp-cantidad" placeholder="0" min="0">
          </div>

          <!-- Botón que llama a la función agregarProducto() en el JavaScript -->
          <button class="btn btn-primary" onclick="agregarProducto()">Agregar producto</button>
        </div>

        <!--Actualizar stock  -->
        <div class="card">
          <div class="card-title">Actualizar Stock</div>

          <!-- seccion: nombre del producto a modificar -->
          <div class="form-group">
            <label class="form-label">Nombre del producto</label>
            <input type="text" class="form-input" id="upd-nombre" placeholder="Nombre del producto">
          </div>

          <!-- seccion: cantidad a sumar o restar -->
          <div class="form-group">
            <label class="form-label">Cantidad</label>
            <input type="number" class="form-input" id="upd-cantidad" placeholder="0" min="1">
          </div>

          <!-- Dos botones: Ambos llaman a actualizarStock() con distinto parámetro :v-->
          <div style="display:flex; gap:10px;">
            <button class="btn btn-primary" onclick="actualizarStock('sumar')">Sumar stock</button>
            <button class="btn btn-danger" onclick="actualizarStock('restar')">Restar stock</button>
          </div>
        </div>

      </div>
      <!-- FIN -->

      <!-- Inventario actual -->
      <div class="card">
        <div class="card-title">Inventario actual</div>

        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th style="width:60px;">ID</th>
                <th>Producto</th>
                <th>Stock disponible</th>
              </tr>
            </thead>
            <!-- tbody es llenado dinámicamente por la función renderTabla() en JavaScript (abajo) -->
            <tbody id="tabla-inventario">
              <tr>
                <td colspan="3" style="text-align:center; color:#888; padding:24px;">
                  No hay productos aún. ¡Agrega uno arriba!
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Totales actualizados por JavaScript al agregar/modificar productos -->
        <div class="table-footer"><h2>Total productos = <span id="total-productos">0</span></h2></div>
        <div class="table-footer"><h2>Total stock = <span id="total-stock">0</span></h2></div>
      </div>

    </div>
    <!-- FIN -->


    <!--MI PERFIL-->
    <div class="vista" id="vista-perfil">

      <!-- Encabezado de la sección con ícono grande -->
      <div class="profile-header">
        <svg width="52" height="52" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="color:var(--text-dark)">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        <h1 style="font-size:2rem; color:var(--text-dark); font-weight:800;">Mi perfil</h1>
      </div>

      <!-- información personal | seguridad -->
      <div class="profile-grid">

        <!-- Información personal  -->
        <div class="card">
          <div class="card-title" style="text-align:center;">Información personal</div>

          <div style="display:flex; gap:18px; align-items:flex-start;">

            <!-- usuario con botón de cámara -->
            <div class="avatar-wrap">
              <div class="avatar-circle">
                <svg width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="#4a9fd4" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </div>
              <!-- Botón de cámara para cambiar la foto -->
              <div class="avatar-camera">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                  <circle cx="12" cy="13" r="3"/>
                </svg>
              </div>
            </div>

            <!-- Datos del usuario -->
            <div style="flex:1;">
              <div class="info-field">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span><strong>Nombre completo:</strong> Juan Pérez</span>
              </div>
              <div class="info-field">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span><strong>Usuario:</strong> juanp</span>
              </div>
              <div class="info-field">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span><strong>Correo:</strong> juan@ejemplo.com</span><!-- (en el back conecta con el servidor) -->
              </div>
              <div class="info-field">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span><strong>Teléfono:</strong> +57 300 000 0000</span><!-- (en el back conecta con el servidor) -->
              </div>
            </div>
          </div>

          <!-- Botón editar información -->
          <div style="display:flex; align-items:center; gap:10px; justify-content:flex-end; margin-top:16px;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:var(--text-dark)">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 013.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            <button class="btn btn-primary" onclick="alert('Editar información — conectar con backend')">
              editar información
            </button>
          </div>
        </div>
        <!-- FIN -->

        <!-- Seguridad  -->
        <div class="card">
          <div class="card-title" style="display:flex; align-items:center; gap:10px;"><!-- Ícono de escudo para la sección de seguridad -->
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><!--icono de seguridad -->
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/><!--icono de seguridad -->
            </svg>
            Seguridad
          </div>

          <!--contraseña -->
          <div class="security-field">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>contraseña: ..........</span>//recordar que esto es mientras la base de datos
          </div>

          <!-- fecha y hora del último inicio de sesión -->
          <div class="security-field">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/>
            </svg>
            <span>Último inicio: 00/00/0000 00:00</span>//recordar que esto es mientras la base de datos
          </div>

          <!--este botón cambiaria contraseña -->
          <div style="margin-top:20px;">
            <button class="btn btn-primary"
              onclick="alert('Cambiar contraseña — conectar con backend')"
              style="display:flex; align-items:center; gap:8px;">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              Cambiar contraseña
            </button>
          </div>
        </div>
        <!-- FIN  -->

      </div>
      <!-- FIN profile -->

      <!-- Estadísticas del usuario en la parte inferior del perfil
           id="stat-perfil-productos" se actualiza por el JavaScript -->
      <div class="stats-row">
        <div class="stat-card">
          
          <div class="stat-icon">
            <svg width="42" height="42" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
            </svg>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-info">
            <div class="stat-label">Productos</div>
            <div class="stat-number" id="stat-perfil-productos">0</div>
            <div class="stat-sub">Registrados</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">
            <svg width="42" height="42" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/>
            </svg>
          </div>
          <div class="stat-divider"></div>
          <div class="stat-info">
            <div class="stat-label">Movimientos</div>
<div class="stat-number stat-movimientos">0</div>
            <div class="stat-sub">Hoy</div>
          </div>
        </div>
      </div>

    </div>
    <!-- FIN DE LA VISTA DEL PERFIL -->

  </main>
</div>
</body>
<script src="../js/inventario.js"></script>
</html>
