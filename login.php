<?php
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];

    $sql = "SELECT id, nombre, correo, clave, rol FROM usuarios WHERE correo='$correo'";
    $resultado = $conexion2->query($sql);

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($clave, $usuario['clave'])) {
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];
            header("Location: panel.php");
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta');</script>";
        }
    } else {
        echo "<script>alert('Correo no encontrado');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background-color: #f3f4f6; /* fondo gris claro sin imagen */
    }
  </style>
</head>
<body class="flex items-center justify-center h-screen relative">

  <!-- Formulario -->
  <form method="POST" class="relative bg-white p-8 rounded-xl shadow-lg w-80 z-10 opacity-0 transform -translate-y-5">
    <h2 class="text-2xl font-bold mb-4 text-center text-gray-800">Iniciar sesión</h2>

    <input type="email" name="correo" placeholder="Correo" required class="w-full p-2 border mb-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
    <input type="password" name="clave" placeholder="Contraseña" required class="w-full p-2 border mb-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">

    <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Entrar</button>

    <p class="text-center mt-3 text-sm text-gray-700">
      ¿No tienes cuenta?
      <a href="registro.php" class="text-blue-600 hover:underline">Regístrate</a>
    </p>
  </form>

  <!--  Animación JavaScript -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const form = document.querySelector("form");
      setTimeout(() => {
        form.style.transition = "all 0.8s ease";
        form.style.opacity = 1;
        form.style.transform = "translateY(0)";
      }, 200);
    });
  </script>

</body>
</html>
