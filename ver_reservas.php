<?php
// ver_reservas.php
session_start();
include("conexion.php");
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'empleado') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['change_status'])) {
    $rid = (int)$_POST['rid'];
    $estado = $conexion2->real_escape_string($_POST['estado']);
    $conexion2->query("UPDATE reservas SET estado = '$estado', updated_at = NOW() WHERE id = $rid");
    header("Location: ver_reservas.php");
    exit();
}

$reservas = $conexion2->query("
    SELECT r.*, u.nombre as user_name, v.titulo as viaje_titulo
    FROM reservas r
    JOIN usuarios u ON r.usuario_id = u.id
    JOIN viajes v ON r.viaje_id = v.id
    ORDER BY r.created_at DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reservas - Empleado</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 min-h-screen">
  <div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Reservas (Empleado)</h2>

    <?php
    if (empty($reservas)) {
        echo "<p>No hay reservas.</p>";
    } else {
        // mostramos la tabla
        ?>
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
            <?php foreach ($reservas as $r) { 
                $estadoActual = $r['estado'] ?? 'booked'; // evita warning si no existe columna
            ?>
            <tr>
              <td class="p-2"><?= htmlspecialchars($r['id']) ?></td>
              <td class="p-2"><?= htmlspecialchars($r['user_name']) ?></td>
              <td class="p-2"><?= htmlspecialchars($r['viaje_titulo']) ?></td>
              <td class="p-2"><?= htmlspecialchars($r['plazas']) ?></td>
              <td class="p-2"><?= htmlspecialchars($estadoActual) ?></td>
              <td class="p-2">
                <form method="POST" style="display:inline">
                  <input type="hidden" name="rid" value="<?= htmlspecialchars($r['id']) ?>">
                  <select name="estado" class="p-1 border rounded">
                    <option value="booked" <?= $estadoActual=='booked' ? 'selected' : '' ?>>booked</option>
                    <option value="cancelled" <?= $estadoActual=='cancelled' ? 'selected' : '' ?>>cancelled</option>
                    <option value="completed" <?= $estadoActual=='completed' ? 'selected' : '' ?>>completed</option>
                  </select>
                  <button name="change_status" class="px-2 py-1 bg-yellow-400 rounded">Cambiar</button>
                </form>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
    <?php
    } // end else
    ?>

    <a href="panel.php" class="block mt-4 text-sm text-gray-600">Volver al panel</a>
  </div>
</body>
</html>
