# BENGA - Sistema de Inventario

**BENGA** es un sistema web para la gestión de inventarios, usuarios, categorías y movimientos, con interfaz moderna y responsive.

## ✨ Características Principales

- **🎨 Sistema de Temas Avanzado**: Tema claro, oscuro y automático (según preferencia del dispositivo)
- **📱 Diseño Responsive**: Optimizado para todos los dispositivos
- **🔒 Gestión de Usuarios**: Sistema de autenticación seguro
- **📦 Control de Inventario**: Gestión completa de productos y stock
- **🏷️ Categorización**: Organización eficiente por categorías
- **📊 Historial de Movimientos**: Seguimiento completo de entradas y salidas
- **🎯 Interfaz Intuitiva**: Diseño moderno con Bootstrap 5

## 🛠️ Tecnologías Utilizadas

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework CSS**: Bootstrap 5.3.2
- **Iconos**: Bootstrap Icons 1.11.1
- **Backend**: PHP 7.4+
- **Base de Datos**: MySQL/MariaDB
- **Librerías**: jQuery 3.7.1

## 🎨 Sistema de Temas

### Tema Claro
- Fondos claros con texto oscuro para máxima legibilidad
- Colores suaves y profesionales
- Contraste optimizado para entornos con mucha luz

### Tema Oscuro
- Fondos oscuros con texto claro
- Colores modernos y elegantes
- Perfecto para uso nocturno o en entornos con poca luz

### Tema Automático
- Se adapta automáticamente a la preferencia del sistema
- Cambia entre claro y oscuro según la configuración del dispositivo
- Experiencia personalizada sin intervención del usuario

## 🚀 Instalación

### Requisitos Previos
- Servidor web (Apache/Nginx)
- PHP 7.4 o superior
- MySQL 5.7 o MariaDB 10.2+
- Extensión MySQLi habilitada

### Pasos de Instalación

1. **Clona el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/simple-stock.git
   cd simple-stock
   ```

2. **Configura la base de datos**
   - Crea una nueva base de datos MySQL
   - Importa el archivo `inventario/simple_stock.sql`

3. **Configura la conexión**
   - Edita `inventario/config/db.php`
   - Actualiza las credenciales de la base de datos

4. **Configura el servidor web**
   - Apunta el document root a la carpeta `inventario`
   - Asegúrate de que PHP tenga permisos de escritura

5. **Accede a la aplicación**
   - Navega a `http://localhost/inventario1-main/inventario/login.php`
   - Usuario por defecto: `admin` / Contraseña: `admin`

## 📁 Estructura del Proyecto

```
inventario/
├── ajax/                 # Operaciones AJAX (CRUD y búsquedas)
├── classes/              # Clases PHP (Login, Registro, Validación)
├── config/               # Configuración de base de datos y conexión
├── css/                  # Estilos CSS (Bootstrap, personalizados, login)
├── img/                  # Imágenes y logos
├── js/                   # JavaScript (funciones, validaciones, modales)
├── libraries/            # Librerías externas
├── modal/                # Modales para formularios y edición
├── uploads/              # Imágenes de perfil de usuario
├── stock.php             # Inventario y movimientos
├── categorias.php        # Gestión de categorías
├── usuarios.php          # Gestión de usuarios
├── producto.php          # Detalle y edición de productos
├── perfil.php            # Perfil de usuario y empresa
├── movimientos.php       # Historial de movimientos
├── registro.php          # Registro de usuarios
├── login.php             # Login de usuarios
├── logout.php            # Cierre de sesión
├── navbar.php            # Navegación principal
├── head.php, footer.php  # Encabezado y pie de página
```

## 🎯 Funcionalidades

### 📦 Gestión de Inventario
- Agregar, editar y eliminar productos
- Consultar stock actual
- Registrar entradas y salidas
- Historial de movimientos
- Búsqueda y filtrado por nombre, categoría y código

### 🏷️ Gestión de Categorías
- Crear, editar y eliminar categorías
- Organizar productos por tipo

### 👥 Gestión de Usuarios
- Crear, editar y eliminar usuarios
- Modificar perfiles y foto
- Cambiar contraseñas
- Control de acceso y permisos

## 🎨 Personalización

### Colores y Temas
El sistema utiliza variables CSS y Bootstrap para facilitar la personalización:

```css
:root {
    --primary-color: #0d6efd;
    --success-color: #198754;
    --danger-color: #dc3545;
    --bg-primary: #ffffff;
    --text-primary: #000000;
}
```

### Estilos Responsive
- Breakpoints optimizados para móviles, tablets y desktop
- Navegación adaptativa
- Formularios responsive

## 🔒 Seguridad

- **Autenticación**: Sistema de login seguro
- **Sesiones**: Manejo seguro de sesiones de usuario
- **SQL Injection**: Protección mediante prepared statements
- **XSS**: Sanitización de entrada de datos
- **CSRF**: Protección contra ataques cross-site

## 📱 Responsive Design

- **Mobile First**: Diseño optimizado para dispositivos móviles
- **Breakpoints**: Adaptación automática a diferentes tamaños de pantalla
- **Touch Friendly**: Interfaz optimizada para pantallas táctiles
- **Performance**: Carga rápida en todos los dispositivos

## 🚀 Performance

- **CSS Optimizado**: Variables CSS para cambios de tema instantáneos
- **JavaScript Modular**: Código organizado y eficiente
- **Base de Datos**: Consultas optimizadas con índices apropiados
- **Caché**: Sistema de temas persistente en localStorage

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

Si tienes alguna pregunta o necesitas ayuda:

- 📧 Email: soporte@simplestock.com
- 🐛 Issues: [GitHub Issues](https://github.com/tu-usuario/simple-stock/issues)
- 📖 Documentación: [Wiki del Proyecto](https://github.com/tu-usuario/simple-stock/wiki)

## 🙏 Agradecimientos

- **Bootstrap Team** por el increíble framework CSS
- **Bootstrap Icons** por la librería de iconos
- **Comunidad PHP** por el soporte continuo
- **Contribuidores** que han ayudado a mejorar el proyecto

---

**BENGA** - Haciendo la gestión de inventario simple y elegante. ✨