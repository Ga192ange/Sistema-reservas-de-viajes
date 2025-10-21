# 🧳 Proyecto: Sistema de Reservas de Viajes

## 📘 Descripción general
El sistema **Reserva_Viajes** permite a los usuarios registrarse, iniciar sesión y gestionar reservas de viajes.
Incluye roles de usuario y administrador, con funciones para administrar destinos, usuarios y reservas.
Está desarrollado con **HTML, CSS, PHP y MySQL**.

---

## 🗂️ Estructura del proyecto (Indexado)
| Archivo / Carpeta | Descripción |
|--------------------|-------------|
| **form.html** | Página principal de presentación del sitio |
| **registro.php** | Formulario para registrar nuevos usuarios |
| **sesion.php** | Página de inicio de sesión |
| **validar.php** | Valida las credenciales ingresadas por el usuario |
| **dashboard.php** | Panel principal del usuario una vez iniciada la sesión |
| **crearreserva.php** | Formulario para crear nuevas reservas |
| **mis_reservas.php** | Muestra las reservas del usuario |
| **gestionar_reservas.php** | CRUD para modificar o eliminar reservas |
| **gestionar_viajes.php** | CRUD para administrar viajes |
| **gestionar_usuarios.php** | CRUD para administrar usuarios |
| **admin.php** | Panel principal del administrador |
| **conexion.php** | Archivo de conexión a la base de datos |
| **cerrar_sesion.php** | Cierra la sesión actual |
| **/img/** | Carpeta con imágenes e íconos |
| **/css/** | Hojas de estilo (colores, fuentes y diseño) |

---

## 🧾 Documentación de código
Ejemplo de encabezado que puedes colocar en cada archivo:

```php
/*

<?php
// conexion.php
$servidor = 'localhost';
$basedatos = 'agencia_viajes';
$usuario = 'root';
$contrasena = '';

$conexion2 = new mysqli($servidor, $usuario, $contrasena, $basedatos);
if ($conexion2->connect_errno) {
    echo "Error de conexión, verifique sus datos";
    exit();
}
// No echo en producción; por ahora dejamos silencioso.
?>

```

Y dentro del código:

```php
// Conexión a la base de datos
$pdo = new PDO(...);

// Consulta para verificar el usuario
$sql = "SELECT * FROM usuarios WHERE correo = ?";
```

---

## 🧭 Mapa de navegación del sistema

```
Página principal (form.html)
 ├── Registro de usuario (registro.php)
 ├── Iniciar sesión (sesion.php)
 │      ↓
 │   Dashboard / Panel principal (dashboard.php)
 │      ├── Crear reserva (crearreserva.php)
 │      ├── Mis reservas (mis_reservas.php)
 │      ├── Gestionar reservas (gestionar_reservas.php)
 │      ├── Gestionar viajes (gestionar_viajes.php)
 │      ├── Gestionar usuarios (gestionar_usuarios.php)
 │      └── Cerrar sesión (cerrar_sesion.php)
 └── Carpeta img/ → recursos e imágenes del sitio
```

---

## 🛠️ Lenguajes y herramientas utilizadas
- **HTML / CSS:** estructura y diseño de la interfaz.
- **PHP:** lógica de servidor y conexión con la base de datos.
- **MySQL:** almacenamiento de información.
- **XAMPP:** entorno de desarrollo local.

---

