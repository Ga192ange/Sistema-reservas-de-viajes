<?php
// gestionar_usuarios.php
session_start();
include("conexion.php");
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
    header("Location: login.php");
    exit();
}

// Cambiar rol
if (isset($_POST['change_role'])) {
    $uid = (int)$_POST['uid'];
    $newrole = $conexion2->real_escape_string($_POST['newrole']);
    $conexion2->query("UPDATE usuarios SET rol = '$newrole' WHERE id = $uid");
    header("Location: gestionar_usuarios.php");
    exit();
}

// Eliminar usuario
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conexion2->query("DELETE FROM usuarios WHERE id = $id");
    header("Location: gestionar_usuarios.php");
    exit();
}

// Lista usuarios (sin columna created_at)
$users = $conexion2->query("SELECT id, nombre, correo, rol FROM usuarios ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestionar Usuarios - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 min-h-screen">
  <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Usuarios</h2>

    <?php if (empty($users)): ?>
      <p>No hay usuarios registrados.</p>
    <?php else: ?>
      <table class="w-full table-auto">
        <thead>
          <tr class="bg-gray-100">
            <th class="p-2">ID</th>
            <th class="p-2">Nombre</th>
            <th class="p-2">Correo</th>
            <th class="p-2">Rol</th>
            <th class="p-2">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td class="p-2"><?= $u['id'] ?></td>
            <td class="p-2"><?= htmlspecialchars($u['nombre']) ?></td>
            <td class="p-2"><?= htmlspecialchars($u['correo']) ?></td>
            <td class="p-2"><?= htmlspecialchars($u['rol']) ?></td>
            <td class="p-2">
              <form method="POST" style="display:inline">
                <input type="hidden" name="uid" value="<?= $u['id'] ?>">
                <select name="newrole" class="p-1 border rounded">
                  <option value="cliente" <?= $u['rol']=='cliente'?'selected':'' ?>>Cliente</option>
                  <option value="empleado" <?= $u['rol']=='empleado'?'selected':'' ?>>Empleado</option>
                  <option value="administrador" <?= $u['rol']=='administrador'?'selected':'' ?>>Administrador</option>
                </select>
                <button name="change_role" class="px-2 py-1 bg-blue-600 text-white rounded">Cambiar</button>
              </form>
              <a href="gestionar_usuarios.php?delete=<?= $u['id'] ?>" class="px-2 py-1 bg-red-500 text-white rounded" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>

    <a href="panel.php" class="block mt-4 text-sm text-gray-600">Volver al panel</a>
  </div>
</body>
</html>
