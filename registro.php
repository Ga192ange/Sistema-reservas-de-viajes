<?php
// registro.php
session_start();
include("conexion.php");

// Activar reporte de errores (solo para desarrollo)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
    $clave  = isset($_POST['clave']) ? $_POST['clave'] : '';
    $rol    = isset($_POST['rol']) ? trim($_POST['rol']) : 'cliente';

    if ($nombre === '' || $correo === '' || $clave === '') {
        $msg = "Completa todos los campos.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $msg = "Ingresa un correo válido.";
    } elseif (!in_array($rol, ['administrador','empleado','cliente'])) {
        $msg = "Rol inválido.";
    } else {
        $clave_cifrada = password_hash($clave, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, correo, clave, rol) VALUES (?, ?, ?, ?)";
        $stmt = $conexion2->prepare($sql);

        if ($stmt === false) {
            $msg = "Error al preparar la consulta: " . htmlspecialchars($conexion2->error);
        } else {
            $stmt->bind_param("ssss", $nombre, $correo, $clave_cifrada, $rol);
            if ($stmt->execute()) {
                header("Location: login.php?registered=1");
                exit();
            } else {
                if ($stmt->errno == 1062) {
                    $msg = "El correo ya está registrado.";
                } else {
                    $msg = "Error al registrar: " . htmlspecialchars($stmt->error);
                }
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - Agencia de Viajes</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background-color: #f3f4f6; /* Fondo claro */
    }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen">

  <form method="POST" class="bg-white p-8 rounded-xl shadow-lg w-96 opacity-0 transform scale-95">
    <h2 class="text-2xl font-bold mb-4 text-center text-gray-800">Registro</h2>

    <?php if (!empty($msg)) : ?>
      <div class="bg-red-100 text-red-700 p-2 mb-3 rounded"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <input type="text" name="nombre" placeholder="Nombre" value="<?= isset($nombre) ? htmlspecialchars($nombre) : '' ?>" required class="w-full p-2 border mb-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
    <input type="email" name="correo" placeholder="Correo" value="<?= isset($correo) ? htmlspecialchars($correo) : '' ?>" required class="w-full p-2 border mb-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
    <input type="password" name="clave" placeholder="Contraseña" required class="w-full p-2 border mb-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">

    <select name="rol" class="w-full p-2 border mb-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
      <option value="cliente" <?= (isset($rol) && $rol=='cliente')?'selected':'' ?>>Cliente</option>
      <option value="empleado" <?= (isset($rol) && $rol=='empleado')?'selected':'' ?>>Empleado</option>
      <option value="administrador" <?= (isset($rol) && $rol=='administrador')?'selected':'' ?>>Administrador</option>
    </select>

    <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 hover:scale-105 transition transform">Registrar</button>

    <p class="text-center mt-3 text-sm text-gray-700">
      ¿Ya tienes cuenta? 
      <a href="login.php" class="text-blue-600 hover:underline">Inicia sesión</a>
    </p>
  </form>

  <!-- 🔹 Animación de aparición -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const form = document.querySelector("form");
      setTimeout(() => {
        form.style.transition = "all 0.8s ease";
        form.style.opacity = 1;
        form.style.transform = "scale(1)";
      }, 200);
    });
  </script>

</body>
</html>
