<?php
// gestionar_reservas.php
session_start();
include("conexion.php");
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
    header("Location: login.php");
    exit();
}

// Cambiar estado (form)
if (isset($_POST['change_status'])) {
    $rid = (int)$_POST['rid'];
    $estado = $conexion2->real_escape_string($_POST['estado']);
    $conexion2->query("UPDATE reservas SET estado = '$estado', updated_at = NOW() WHERE id = $rid");
    header("Location: gestionar_reservas.php");
    exit();
}

// Eliminar reserva
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conexion2->query("DELETE FROM reservas WHERE id = $id");
    header("Location: gestionar_reservas.php");
    exit();
}

// Obtener reservas
$reservas = $conexion2->query("
    SELECT r.*, u.nombre AS user_name, v.titulo AS viaje_titulo
    FROM reservas r
    JOIN usuarios u ON r.usuario_id = u.id
    JOIN viajes v ON r.viaje_id = v.id
    ORDER BY r.id DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestionar Reservas - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 min-h-screen">
  <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Reservas</h2>

    <?php if (empty($reservas)) { ?>
      <p>No hay reservas.</p>
    <?php } else { ?>
      <table class="w-full table-auto">
        <thead>
          <tr class="bg-gray-100">
            <th class="p-2">ID</th>
            <th class="p-2">Usuario</th>
            <th class="p-2">Viaje</th>
            <th class="p-2">Plazas</th>
            <th class="p-2">Estado</th>
            <th class="p-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reservas as $r) { ?>
          <tr>
            <td class="p-2"><?= $r['id'] ?></td>
            <td class="p-2"><?= htmlspecialchars($r['user_name']) ?></td>
            <td class="p-2"><?= htmlspecialchars($r['viaje_titulo']) ?></td>
            <td class="p-2"><?= $r['plazas'] ?></td>
            <td class="p-2"><?= $r['estado'] ?></td>
            <td class="p-2">
              <form method="POST" style="display:inline">
                <input type="hidden" name="rid" value="<?= $r['id'] ?>">
                <select name="estado" class="p-1 border rounded">
                  <option value="booked" <?= $r['estado']=='booked'?'selected':'' ?>>booked</option>
                  <option value="cancelled" <?= $r['estado']=='cancelled'?'selected':'' ?>>cancelled</option>
                  <option value="completed" <?= $r['estado']=='completed'?'selected':'' ?>>completed</option>
                </select>
                <button name="change_status" class="px-2 py-1 bg-yellow-400 rounded">Cambiar</button>
              </form>
              <a href="gestionar_reservas.php?delete=<?= $r['id'] ?>" class="px-2 py-1 bg-red-500 text-white rounded" onclick="return confirm('¿Eliminar reserva?')">Eliminar</a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } ?>

    <a href="panel.php" class="block mt-4 text-sm text-gray-600">Volver al panel</a>
  </div>
</body>
</html>
