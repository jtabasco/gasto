# Dashboard Administrativo - Sistema de Gestión de Gastos

## 📊 Descripción General

El Dashboard Administrativo es una herramienta completa para administradores que permite monitorear el estado financiero de todas las familias y usuarios del sistema de gestión de gastos. Proporciona métricas en tiempo real, alertas críticas y análisis detallados de deudas pendientes.

## 🚀 Características Principales

### 📈 Métricas en Tiempo Real
- **Total de Usuarios Activos**: Número de usuarios activos en el sistema
- **Total de Familias**: Cantidad de familias registradas
- **Deudas Pendientes**: Suma total de deudas no pagadas
- **Pagado este Mes**: Total de pagos realizados en el mes actual

### 🏠 Análisis por Familia
- Resumen financiero por cada familia
- Usuarios activos por familia
- Estado de deudas (Al día, Moderada, Alta)
- Comparación de pagos vs deudas pendientes

### 👥 Top 10 Usuarios con Más Deudas
- Lista de usuarios con mayores deudas pendientes
- Días de mora por usuario
- Estado de riesgo (Normal, Mora Alta, Crítico)
- Promedio de días de mora

### ⚠️ Alertas Críticas
- Usuarios con deudas superiores a $1,000
- Deudas con más de 30 días de mora
- Notificaciones automáticas de riesgo

### 📊 Gráficos Interactivos
- **Gráfico de Mora**: Distribución de deudas por días de mora
- **Gráfico de Familias**: Comparación de deudas vs pagos por familia

## 🛠️ Archivos del Sistema

### Archivos Principales
- `system/dashboard_admin.php` - Dashboard principal
- `system/get_dashboard_data.php` - API para datos del dashboard
- `system/generar_reporte_dashboard.php` - Generador de reportes
- `include/nav.php` - Navegación (actualizado con enlace al dashboard)

### Estructura de Datos
El dashboard utiliza las siguientes tablas de la base de datos:
- `usuarios` - Información de usuarios
- `familia` - Información de familias
- `detalle_gasto` - Detalles de gastos y deudas
- `Gastos` - Gastos principales

## 📱 Funcionalidades

### 1. Vista General del Mes
- Resumen completo del mes actual
- Comparación con meses anteriores
- Tendencias de pagos y deudas

### 2. Deudas Pendientes por Familia
- Análisis detallado por familia
- Usuarios activos por familia
- Estado financiero de cada familia

### 3. Días de Mora por Deuda
- Clasificación por rangos de días:
  - 1-7 días (Verde)
  - 8-15 días (Amarillo)
  - 16-30 días (Naranja)
  - Más de 30 días (Rojo)

### 4. Alertas y Notificaciones
- Alertas automáticas para deudas críticas
- Notificaciones de mora alta
- Recomendaciones de acción

## 🎨 Interfaz de Usuario

### Diseño Responsivo
- Compatible con dispositivos móviles
- Diseño moderno con gradientes
- Iconos intuitivos de Bootstrap

### Colores y Estados
- **Verde**: Pagos realizados, usuarios al día
- **Amarillo**: Deudas moderadas, atención requerida
- **Rojo**: Deudas críticas, acción inmediata necesaria
- **Azul**: Información general, métricas

### Navegación
- Menú de navegación actualizado
- Enlaces directos a funcionalidades
- Botones de acción claros

## 📋 Reportes

### Generación de Reportes
- Reporte completo en HTML
- Opción de impresión
- Exportación de datos
- Formato profesional para presentaciones

### Contenido del Reporte
1. **Resumen General**
   - Métricas principales
   - Comparativas del mes

2. **Análisis por Familia**
   - Tabla detallada por familia
   - Estados financieros

3. **Top Usuarios con Deudas**
   - Lista de usuarios críticos
   - Días de mora

4. **Recomendaciones**
   - Acciones sugeridas
   - Estrategias de cobro

## 🔧 Configuración

### Requisitos
- PHP 7.4 o superior
- MySQL/MariaDB
- Bootstrap 5.3.3
- Chart.js para gráficos

### Instalación
1. Asegurar que los archivos estén en la carpeta `system/`
2. Verificar permisos de escritura
3. Configurar conexión a base de datos
4. Acceder como administrador (rol = 1)

### Personalización
- Umbrales de alerta configurables
- Colores personalizables
- Métricas adicionales
- Gráficos personalizados

## 🔒 Seguridad

### Control de Acceso
- Solo usuarios con rol de administrador
- Verificación de sesión activa
- Validación de permisos

### Protección de Datos
- Sanitización de datos de entrada
- Escape de caracteres especiales
- Validación de consultas SQL

## 📈 Métricas y KPIs

### Indicadores Clave
1. **Tasa de Cobranza**: Pagos realizados vs deudas totales
2. **Días Promedio de Mora**: Tiempo promedio de deudas pendientes
3. **Concentración de Deudas**: Distribución de deudas por usuario
4. **Eficiencia por Familia**: Rendimiento financiero por familia

### Alertas Automáticas
- Deudas superiores a $1,000
- Mora superior a 30 días
- Familias con múltiples deudores
- Usuarios inactivos con deudas

## 🚀 Mejoras Futuras

### Funcionalidades Planificadas
- [ ] Notificaciones por email
- [ ] Dashboard móvil nativo
- [ ] Exportación a Excel
- [ ] Gráficos avanzados
- [ ] Predicciones de cobranza
- [ ] Integración con calendario

### Optimizaciones
- [ ] Caché de consultas
- [ ] Lazy loading de datos
- [ ] Compresión de respuestas
- [ ] Optimización de consultas SQL

## 📞 Soporte

### Documentación
- Este archivo README
- Comentarios en el código
- Guías de usuario

### Contacto
Para soporte técnico o consultas sobre el dashboard administrativo, contactar al equipo de desarrollo.

---

**Versión**: 1.0  
**Fecha**: <?php echo date('d/m/Y'); ?>  
**Autor**: Sistema de Gestión de Gastos 