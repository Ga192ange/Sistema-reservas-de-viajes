<?php
// mis_reservas.php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'cliente') {
    header("Location: login.php");
    exit();
}
include("conexion.php");
$user_id = $_SESSION['user_id'];

$stmt = $conexion2->prepare("SELECT r.*, v.titulo, v.fecha_salida FROM reservas r JOIN viajes v ON r.viaje_id = v.id WHERE r.usuario_id = ? ORDER BY r.created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$mis = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mis Reservas - Agencia</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 min-h-screen">
  <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Mis reservaciones</h2>

    <?php if (!empty($_GET['success'])) echo "<div class='bg-green-100 text-green-700 p-2 mb-3 rounded'>Reserva creada correctamente.</div>"; ?>

    <?php if (empty($mis)): ?>
      <p>No tienes reservas.</p>
    <?php else: ?>
      <table class="w-full table-auto">
        <thead><tr class="bg-gray-100"><th class="p-2">ID</th><th class="p-2">Viaje</th><th class="p-2">Fecha salida</th><th class="p-2">Plazas</th><th class="p-2">Estado</th></tr></thead>
        <tbody>
        <?php foreach ($mis as $m): ?>
          <tr>
            <td class="p-2"><?= $m['id'] ?></td>
            <td class="p-2"><?= htmlspecialchars($m['titulo']) ?></td>
            <td class="p-2"><?= $m['fecha_salida'] ?></td>
            <td class="p-2"><?= $m['plazas'] ?></td>
            <td class="p-2"><?= $m['estado'] ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <a href="panel.php" class="inline-block mt-4 text-sm text-gray-600">Volver al panel</a>
  </div>
</body>
</html>
