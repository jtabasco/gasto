# 💰 Sistema de Gestión de Gastos

![PHP](https://img.shields.io/badge/PHP-7.4+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-green.svg)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-purple.svg)
![jQuery](https://img.shields.io/badge/jQuery-3.6+-blue.svg)

## 📝 Descripción
Sistema web para la gestión de gastos compartidos entre usuarios. Permite registrar gastos, asignar deudas, realizar pagos y enviar notificaciones por correo electrónico.

## ✨ Características
- 👥 Registro y gestión de usuarios con diferentes roles
- 💵 Registro de gastos con detalles (concepto, monto, fecha)
- 📊 Asignación de deudas a usuarios
- 📈 Seguimiento de pagos y deudas pendientes
- 📧 Notificaciones por correo electrónico
- 📱 Interfaz responsiva y moderna
- 🔍 Tablas de datos con búsqueda y ordenamiento

## 🛠️ Tecnologías utilizadas
- **Backend**
  - PHP 7.4+
  - MySQL 5.7+
  - PHPMailer

- **Frontend**
  - HTML5
  - CSS3
  - JavaScript
  - jQuery
  - Bootstrap 5
  - DataTables
  - iziToast

## ⚙️ Requisitos
- Servidor web Apache
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Extensión PHP para MySQL
- Extensión PHP para envío de correos

## 🚀 Instalación
1. 📥 Clonar el repositorio en la carpeta del servidor web
2. 💾 Importar la base de datos desde el archivo SQL proporcionado
3. ⚙️ Configurar la conexión a la base de datos en `conexion.php`
4. 📧 Configurar el servidor de correo en `config/mail_config.php`
5. 🌐 Acceder al sistema desde el navegador

## 📁 Estructura del proyecto
```
gasto/
├── ajax/          # Peticiones AJAX
├── config/        # Archivos de configuración
├── include/       # Componentes reutilizables
├── js/           # Archivos JavaScript
├── system/       # Páginas principales
└── utils/        # Utilidades
```

## 🔑 Funcionalidades principales

### 👥 Gestión de usuarios
- Crear nuevos usuarios
- Editar información de usuarios
- Desactivar/activar usuarios
- Asignar roles

### 💵 Registro de gastos
- Añadir gastos con detalles completos
- Categorización de gastos
- Asignación de montos
- Registro de fechas

### 📊 Gestión de deudas
- Asignar deudas a usuarios
- Realizar pagos
- Seguimiento de deudas pendientes
- Historial de transacciones

### 📧 Notificaciones
- Envío automático de correos
- Notificaciones de pagos
- Alertas de deudas pendientes
- Confirmaciones de actualizaciones

### 📈 Reportes
- Resumen de gastos
- Estado de deudas
- Historial de pagos
- Estadísticas de uso

## 📝 Cambios Recientes

### 2025-06-16
- Añadido icono de correo para enviar notificacion de pago Neto

### 2025-05-30
- Cambios en la interfas, mas moderno y mas optimisado.
- Solo muestra las deudas cuando las tienes.
- Los detalles aparecen al dar click en el ojo.
- El pago de las deudas se puede selecionar en las tablas de por cobrar el usuario que ha pagado se muestran los detalles para selecionar cual pagar.

### 2025-05-14
- Agregado botón de Donar 🎈✅

### 2025-05-09
- Agregado botón y modal para mostrar cambios recientes
- Al dar clic en el total del deudor en cuentas por cobrar, se abre una ventana que permite conciliar todas las deudas pendientes del deudor, evitando conciliar cada una por separado. 🎈✅




## 📄 Licencia
Este proyecto es privado y su uso está restringido a los usuarios autorizados.

---
<div align="center">
  <sub>Built with ❤️ by <a href="https://github.com/jtabasco">Joel Tabasco</a></sub>
</div> 