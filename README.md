 # ✈️ Reserva_Viajes

 *Una mini plataforma de reservas de viajes escrita en PHP, con interfaz estilizada usando Tailwind (CDN).* 

## 📘 Resumen

Proyecto con páginas PHP para: registro, inicio de sesión, creación y gestión de reservas, y un panel administrativo. El código combina consultas con PDO (`$pdo`) y algunos scripts con MySQLi (`$conn`).

## 📁 Archivos principales

- `form.html` — *Página principal* con navegación, hero, secciones "Qué ofrecemos" y "Destinos populares"; utiliza Tailwind.
- `sesion.php` — *Login* (campos `email`, `password`), maneja sesión PHP.
- `registro.php` — *Registro de usuarios*; inserta en `usuarios` con rol por defecto `cliente`.
- `crearreserva.php` — *Crear reserva* (usuarios con rol `Cliente`).
- `gestionarreserva.php` — *Gestión de reservas* para empleados/administradores (listar, modificar, eliminar).
- `admin.php` — *Panel de administración*: CRUD de viajes y gestión de usuarios (cambiar rol, eliminar). Usa PDO para las operaciones principales.
- `conexion.php` — Conexión a la base de datos (actualmente con PDO `$pdo`).
- `dashboard.php` — Punto de entrada tras login (referenciado por `sesion.php`).
- `cerrar_sesion.php` — Cierre de sesión (si existe).
- `img/` — Carpeta para imágenes/recursos (según proyecto).

## 🗂️ Esquema de base de datos (mínimo esperado)

*Usuarios* (`usuarios`)
- `id` INT PK
- `nombre` VARCHAR
- `email` VARCHAR UNIQUE
- `password` VARCHAR
- `rol` VARCHAR (`cliente` | `empleado` | `administrador`)
- `creado_en` DATETIME

*Viajes* (`viajes`)
- `id` INT PK
- `destino` VARCHAR
- `fecha` DATE (o `fecha_salida` / `fecha_regreso` según uso)
- `precio` DECIMAL
- `disponible` BOOL/INT
- `creado_en` DATETIME

*Reservas* (`reservas`)
- `id` INT PK
- `usuario_id` FK → `usuarios.id`
- `viaje_id` FK → `viajes.id`
- `cantidad` INT
- `estado` VARCHAR
- `creado_en` DATETIME

## ⚙️ Notas de implementación

- `conexion.php` expone `$pdo` para consultas PDO; sin embargo hay archivos que aún usan `$conn` (MySQLi).
- Las rutas y nombres de campos en formularios se han mantenido para compatibilidad con el código original.
- `admin.php` incluye protecciones básicas para evitar que el admin actual se quite permisos o se elimine a sí mismo desde la interfaz.
- Tailwind se carga por CDN para una apariencia moderna sin dependencias locales.

## ⚠️ Recomendaciones

- Mantener respaldos antes de ejecutar operaciones destructivas en la base de datos.
- Unificar la capa de acceso a datos (recomendado: migrar todo a PDO `$pdo`).
- Usar `password_hash` y `password_verify` para contraseñas (migrar si hay otro esquema de hashing).
- No publicar credenciales de la base de datos en repositorios públicos.

## ✨ Sugerencias de mejora

- Migrar todos los scripts a PDO para consistencia.
- Añadir validación y mensajes de éxito/error visibles (flash messages).
- Implementar auditoría para registrar acciones administrativas.

---

## 🧾 Ejemplos de código 

1) SQL mínimo para crear las tablas principales:

```sql
CREATE DATABASE IF NOT EXISTS agencia_viajes CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE agencia_viajes;

CREATE TABLE usuarios (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(255) NOT NULL,
	email VARCHAR(255) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL,
	rol VARCHAR(50) NOT NULL DEFAULT 'cliente',
	creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE viajes (
	id INT AUTO_INCREMENT PRIMARY KEY,
	destino VARCHAR(255) NOT NULL,
	fecha DATE,
	precio DECIMAL(10,2) DEFAULT 0,
	disponible TINYINT(1) DEFAULT 1,
	creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reservas (
	id INT AUTO_INCREMENT PRIMARY KEY,
	usuario_id INT NOT NULL,
	viaje_id INT NOT NULL,
	cantidad INT DEFAULT 1,
	estado VARCHAR(50) DEFAULT 'pendiente',
	creado_en DATETIME DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
	FOREIGN KEY (viaje_id) REFERENCES viajes(id) ON DELETE CASCADE
);
```

2) Conexión PDO (`conexion.php`):

```php
<?php
$host = 'localhost';
$db   = 'agencia_viajes';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
		$pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
		die('Error de conexión: ' . $e->getMessage());
}
```

3) Ejemplo de registro seguro (PHP):

```php
<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$nombre = $_POST['nombre'];
	$email = $_POST['email'];
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

	$stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'cliente')");
	$stmt->execute([$nombre, $email, $password]);
}
```

4) Ejemplo de inicio de sesión (PHP) usando `password_verify`:

```php
<?php
require 'conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email = $_POST['email'];
	$pass = $_POST['password'];

	$stmt = $pdo->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = ?");
	$stmt->execute([$email]);
	$user = $stmt->fetch();

	if ($user && password_verify($pass, $user['password'])) {
		$_SESSION['usuario'] = [
			'id' => $user['id'],
			'nombre' => $user['nombre'],
			'rol' => $user['rol']
		];
		header('Location: dashboard.php');
		exit;
	} else {
		$error = 'Credenciales no válidas';
	}
}
```

5) Snippet de formulario con Tailwind (html):

```html
<form method="POST" class="max-w-md mx-auto p-4 bg-white rounded shadow">
	<label class="block text-sm mb-1">Email</label>
	<input type="email" name="email" required class="border p-2 w-full mb-2 rounded" />

	<label class="block text-sm mb-1">Contraseña</label>
	<input type="password" name="password" required class="border p-2 w-full mb-4 rounded" />

	<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Entrar</button>
</form>
```

---

## 🧩 Ejemplos extraídos del código del proyecto y por qué los usamos

Abajo se muestran fragmentos tomados directamente de los archivos presentes en este repositorio y una breve explicación de por qué se implementaron así en el proyecto.

### 1) `conexion.php` (ejemplo actual en el repo)
```php
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

Por qué se usó: la conexión con `mysqli` es sencilla y directa para entornos locales (XAMPP). En este proyecto hay mezcla de estilos; `mysqli` facilita consultas rápidas en scripts antiguos. Recomendación: unificar a PDO para seguridad y consistencia.

### 2) `admin.php` (operaciones con PDO)
```php
<?php
require 'auth.php';
require 'conexion.php';

if ($_SESSION['usuario']['rol'] !== 'administrador') {
	die('Acceso denegado');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_viaje'])) {
	$destino = $_POST['destino'];
	$fecha = $_POST['fecha'];
	$precio = $_POST['precio'];

	$stmt = $pdo->prepare("INSERT INTO viajes (destino, fecha, precio) VALUES (?, ?, ?)");
	$stmt->execute([$destino, $fecha, $precio]);
	header("Location: admin.php");
	exit;
}

$viajes = $pdo->query("SELECT * FROM viajes")->fetchAll();
?>
```

Por qué se usó: las operaciones administrativas (crear/editar viajes) usan PDO y sentencias preparadas para mayor seguridad y evitar inyección SQL. En el panel admin se requiere proteger las operaciones y gestionar retornos/redirects después de cada acción.

### 3) `sesion.php` (login con password_verify)
```php
<?php
session_start();
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$email = $_POST['email'];
	$password = $_POST['password'];

	$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
	$stmt->execute([$email]);
	$user = $stmt->fetch();

	if ($user && password_verify($password, $user['password'])) {
		$_SESSION['usuario'] = [
			'id' => $user['id'],
			'nombre' => $user['nombre'],
			'rol' => $user['rol']
		];
		header("Location: dashboard.php");
		exit;
	} else {
		$error = "Credenciales no válidas";
	}
}
?>
```

Por qué se usó: el uso de `password_verify` con hashes generados por `password_hash` es la forma recomendada en PHP para almacenar y validar contraseñas. El enfoque con `prepare` evita inyección y el manejo de sesión permite establecer el contexto del usuario tras el login.

---
