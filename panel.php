<?php
// panel.php
session_start();

if (!isset($_SESSION['rol'])) {
    header("Location: login.php");
    exit();
}
$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel - Agencia</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 min-h-screen">
  <div class="max-w-xl mx-auto bg-white p-6 rounded shadow text-center">
    <h2 class="text-2xl font-bold mb-2">Hola, <?= htmlspecialchars($nombre) ?></h2>
    <p class="mb-4">Rol: <strong class="text-blue-600"><?= htmlspecialchars($rol) ?></strong></p>

    <?php if ($rol == 'administrador'): ?>
      <a href="gestionar_usuarios.php" class="block bg-green-600 text-white py-2 rounded mb-2">Gestionar usuarios</a>
      <a href="gestionar_viajes.php" class="block bg-green-600 text-white py-2 rounded mb-2">Gestionar viajes</a>
      <a href="gestionar_reservas.php" class="block bg-green-600 text-white py-2 rounded mb-2">Gestionar reservas</a>
    <?php elseif ($rol == 'empleado'): ?>
      <a href="ver_reservas.php" class="block bg-yellow-500 text-white py-2 rounded mb-2">Ver / Gestionar reservas</a>
      <a href="disponibilidad.php" class="block bg-yellow-500 text-white py-2 rounded mb-2">Ver disponibilidad</a>
    <?php else: /* cliente */ ?>
      <a href="reservar.php" class="block bg-blue-600 text-white py-2 rounded mb-2">Hacer una reservación</a>
      <a href="mis_reservas.php" class="block bg-blue-600 text-white py-2 rounded mb-2">Mis reservaciones</a>
    <?php endif; ?>

    <a href="cerrar_sesion.php" class="block mt-4 bg-red-500 text-white py-2 rounded">Cerrar sesión</a>
  </div>
</body>
</html>
