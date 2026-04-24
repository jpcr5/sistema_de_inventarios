// ============================================================
// CLASE: Producto
// Representa un producto individual del inventario
// ============================================================
class Producto {
  constructor(id, nombre, stock) {
    this.id     = id;
    this.nombre = nombre;
    this.stock  = stock;
  }
}


// ============================================================
// CLASE: Inventario
// Maneja toda la lógica de productos y stock
// ============================================================
class Inventario {
  constructor() {
    this.productos        = []; // lista de objetos Producto
    this.nextId           = 1;  // contador de IDs
    this.totalMovimientos = 0;  // contador de movimientos
  }

  // Agrega un nuevo producto a la lista
  agregarProducto(nombre, cantidad) {
    const existe = this.productos.find(
      p => p.nombre.toLowerCase() === nombre.toLowerCase()
    );
    if (existe) return { ok: false, msg: 'Ya existe un producto con ese nombre.' };

    const nuevo = new Producto(this.nextId++, nombre, cantidad);
    this.productos.push(nuevo);
    return { ok: true, msg: `Producto "${nombre}" agregado con stock ${cantidad}.` };
  }

  // Suma o resta stock a un producto existente
  actualizarStock(nombre, cantidad, operacion) {
    const prod = this.productos.find(
      p => p.nombre.toLowerCase() === nombre.toLowerCase()
    );
    if (!prod) return { ok: false, msg: `No se encontró el producto "${nombre}".` };

    if (operacion === 'sumar') {
      prod.stock += cantidad;
      this.totalMovimientos++;
      return { ok: true, msg: `+${cantidad} unidades sumadas a "${prod.nombre}".` };
    } else {
      if (prod.stock - cantidad < 0) return { ok: false, msg: 'Stock insuficiente.' };
      prod.stock -= cantidad;
      this.totalMovimientos++;
      return { ok: true, msg: `-${cantidad} unidades restadas de "${prod.nombre}".` };
    }
  }

  // Devuelve los productos con stock igual o menor a 2
  obtenerStockBajo() {
    return this.productos.filter(p => p.stock <= 2);
  }

  // Devuelve el total de unidades en stock
  obtenerTotalStock() {
    return this.productos.reduce((suma, p) => suma + p.stock, 0);
  }
}


// ============================================================
// CLASE: UI
// Maneja todo lo visual: tabla, alertas, campana, navegación
// ============================================================
class UI {
  constructor(inventario) {
    this.inventario = inventario; // recibe la instancia de Inventario
  }

  // Muestra una vista y oculta las demás
  mostrarVista(nombre) {
    const vistas = ['inicio', 'inventario', 'perfil'];
    vistas.forEach(v => {
      const vistaEl = document.getElementById('vista-' + v);
      const navEl = document.getElementById('nav-' + v);
      if (vistaEl) vistaEl.classList.remove('activa');
      if (navEl) navEl.classList.remove('active');
    });
    const vistaTarget = document.getElementById('vista-' + nombre);
    if (vistaTarget) vistaTarget.classList.add('activa');
    const navActivo = document.getElementById('nav-' + nombre);
    if (navActivo) navActivo.classList.add('active');
  }

  // Abre o cierra el sidebar
  toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('collapsed');
  }

  // Abre o cierra el panel de notificaciones
  toggleNotificaciones() {
    const panel = document.getElementById('panel-notif');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
  }

  // Muestra mensaje de éxito o error por 3 segundos
  mostrarAlerta(msg, tipo) {
    const el = document.getElementById('alerta-inv');
    el.textContent      = msg;
    el.style.background = tipo === 'error' ? '#fde0e0' : '#d0f0dc';
    el.style.color      = tipo === 'error' ? '#8b1a1a' : '#1a5c38';
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), 3000);
  }

  // Dibuja la tabla con los productos actuales
  renderTabla() {
    const tbody = document.getElementById('tabla-inventario');
    const { productos } = this.inventario;

    if (productos.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="3" style="text-align:center;color:#888;padding:24px;">
            No hay productos aún. ¡Agrega uno arriba!
          </td>
        </tr>`;
    } else {
      tbody.innerHTML = productos.map(p => `
        <tr onclick="app.seleccionarProducto('${p.nombre}')"
            style="cursor:pointer;" title="Clic para seleccionar">
          <td>${p.id}</td>
          <td>${p.nombre}</td>
          <td style="
            color:${p.stock <= 2 ? '#e03030' : 'inherit'};
            font-weight:${p.stock <= 2 ? '700' : 'normal'}">
            ${p.stock}
          </td>
        </tr>
      `).join('');
    }

    this.actualizarContadores();
    this.actualizarCampana();
  }

  // Actualiza los números de productos, stock y movimientos en las tarjetas
  actualizarContadores() {
    const { inventario } = this;
    document.getElementById('total-productos').textContent       = inventario.productos.length;
    document.getElementById('total-stock').textContent           = inventario.obtenerTotalStock();
    document.getElementById('stat-inicio-productos').textContent = inventario.productos.length;
    document.getElementById('stat-perfil-productos').textContent = inventario.productos.length;
    document.querySelectorAll('.stat-movimientos').forEach(el => {
      el.textContent = inventario.totalMovimientos;
    });
  }

  // Actualiza la campana según el stock bajo
  actualizarCampana() {
    const stockBajo = this.inventario.obtenerStockBajo();
    const campana   = document.getElementById('btn-campana');
    const lista     = document.getElementById('lista-notif');

    if (stockBajo.length > 0) {
      campana.style.color = '#e03030';
      lista.innerHTML = stockBajo.map(p => `
        <div style="padding:12px 16px; border-bottom:1px solid rgba(255,255,255,0.07); font-size:0.88rem;">
          <span style="color:#f87171;">⚠️ Stock bajo:</span>
          <strong style="color:#e8f5ee;"> ${p.nombre}</strong>
          <div style="color:#b8ddc8; margin-top:3px;">
            Solo quedan <strong style="color:#f87171;">${p.stock}</strong>
            unidad${p.stock === 1 ? '' : 'es'}
          </div>
        </div>
      `).join('');
    } else {
      campana.style.color = '';
      lista.innerHTML = `
        <div style="padding:16px; color:#b8ddc8; font-size:0.88rem; text-align:center;">
          ✅ Todo el stock está en buen nivel
        </div>
      `;
    }
  }

  // Al hacer clic en una fila autocompleta el campo de "Actualizar Stock"
  seleccionarProducto(nombre) {
    const campo = document.getElementById('upd-nombre');
    campo.value = nombre;
    campo.scrollIntoView({ behavior: 'smooth', block: 'center' });
    campo.style.boxShadow = '0 0 0 3px rgba(39,201,92,0.6)';
    setTimeout(() => campo.style.boxShadow = '', 1500);
  }

  // Maneja el formulario de agregar producto
  handleAgregarProducto() {
    const nombre   = document.getElementById('inp-nombre').value.trim();
    const cantidad = parseInt(document.getElementById('inp-cantidad').value) || 0;

    if (!nombre)      { this.mostrarAlerta('Ingresa el nombre del producto.', 'error'); return; }
    if (cantidad < 0) { this.mostrarAlerta('La cantidad no puede ser negativa.', 'error'); return; }

    const resultado = this.inventario.agregarProducto(nombre, cantidad);
    this.mostrarAlerta(resultado.msg, resultado.ok ? 'success' : 'error');

    if (resultado.ok) {
      this.renderTabla();
      document.getElementById('inp-nombre').value   = '';
      document.getElementById('inp-cantidad').value = '';
    }
  }

  // Maneja el formulario de actualizar stock
  handleActualizarStock(operacion) {
    const nombre   = document.getElementById('upd-nombre').value.trim();
    const cantidad = parseInt(document.getElementById('upd-cantidad').value) || 0;

    if (!nombre)       { this.mostrarAlerta('Ingresa el nombre del producto.', 'error'); return; }
    if (cantidad <= 0) { this.mostrarAlerta('La cantidad debe ser mayor a 0.', 'error'); return; }

    const resultado = this.inventario.actualizarStock(nombre, cantidad, operacion);
    this.mostrarAlerta(resultado.msg, resultado.ok ? 'success' : 'error');

    if (resultado.ok) {
      this.renderTabla();
      document.getElementById('upd-nombre').value   = '';
      document.getElementById('upd-cantidad').value = '';
    }
  }
}


// ============================================================
// INICIO DE LA APLICACIÓN
// app es el objeto global que conecta el HTML con el JS
// ============================================================
const inventario = new Inventario();
const app        = new UI(inventario);

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
  const btnInicio = document.getElementById('nav-inicio');
  const btnInventario = document.getElementById('nav-inventario');
  const btnPerfil = document.getElementById('nav-perfil');
  const btnHamburger = document.querySelector('.hamburger');
  const btnCampana = document.getElementById('btn-campana');
  const avatarBtn = document.querySelector('.avatar-btn');
  
  if (btnInicio) {
    btnInicio.addEventListener('click', function(e) {
      e.preventDefault();
      app.mostrarVista('inicio');
    });
  }
  
  if (btnInventario) {
    btnInventario.addEventListener('click', function(e) {
      e.preventDefault();
      app.mostrarVista('inventario');
    });
  }
  
  if (btnPerfil) {
    btnPerfil.addEventListener('click', function(e) {
      e.preventDefault();
      app.mostrarVista('perfil');
    });
  }
  
  if (btnHamburger) {
    btnHamburger.addEventListener('click', function(e) {
      e.preventDefault();
      app.toggleSidebar();
    });
  }
  
  if (btnCampana) {
    btnCampana.addEventListener('click', function(e) {
      e.preventDefault();
      app.toggleNotificaciones();
    });
  }
  
  if (avatarBtn) {
    avatarBtn.addEventListener('click', function(e) {
      e.preventDefault();
      app.mostrarVista('perfil');
    });
  }
});

// Cierra el panel de notificaciones al hacer clic fuera
document.addEventListener('click', function(e) {
  const panel   = document.getElementById('panel-notif');
  const campana = document.getElementById('btn-campana');
  if (panel && campana && !campana.contains(e.target) && !panel.contains(e.target)) {
    panel.style.display = 'none';
  }
});


// Estas son llamadas desde los atributos onclick del HTML

function mostrarVista(nombre) {
  app.mostrarVista(nombre);
}

function toggleSidebar() {
  app.toggleSidebar();
}

function toggleNotificaciones() {
  app.toggleNotificaciones();
}

function agregarProducto() {
  app.handleAgregarProducto();
}

function actualizarStock(operacion) {
  app.handleActualizarStock(operacion);
}
