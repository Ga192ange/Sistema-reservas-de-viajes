<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
    header("Location: login.php");
    exit();
}

//  Agrega o actualiza viaje
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $conexion2->real_escape_string($_POST['titulo']);
    $fecha = $conexion2->real_escape_string($_POST['fecha']);
    $capacidad = (int)$_POST['capacidad'];
    $precio = (int)$_POST['precio'];
    $descripcion = $conexion2->real_escape_string($_POST['descripcion']);
    $id_edit = isset($_POST['id_edit']) ? (int)$_POST['id_edit'] : 0;

    // Manejo de imagen
    $imagen = "";
    if (!empty($_FILES['imagen']['name'])) {
        $nombreArchivo = time() . "_" . basename($_FILES['imagen']['name']);
        $rutaDestino = "uploads/" . $nombreArchivo;

        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            $imagen = $rutaDestino;
        }
    }

    if ($id_edit > 0) {
        // Editar viajes
        if (!empty($imagen)) {
            $conexion2->query("UPDATE viajes SET 
                titulo='$titulo', 
                fecha_salida='$fecha', 
                capacidad=$capacidad, 
                precio=$precio, 
                descripcion='$descripcion', 
                imagen='$imagen' 
                WHERE id=$id_edit");
        } else {
            $conexion2->query("UPDATE viajes SET 
                titulo='$titulo', 
                fecha_salida='$fecha', 
                capacidad=$capacidad, 
                precio=$precio, 
                descripcion='$descripcion' 
                WHERE id=$id_edit");
        }
    } else {
        // Agrega nuevo viaje
        $conexion2->query("INSERT INTO viajes (titulo, fecha_salida, capacidad, precio, descripcion, imagen) 
        VALUES ('$titulo', '$fecha', $capacidad, $precio, '$descripcion', '$imagen')");
    }

    header("Location: gestionar_viajes.php");
    exit();
}

// Elimina viaje
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $res = $conexion2->query("SELECT imagen FROM viajes WHERE id = $id");
    if ($res && $fila = $res->fetch_assoc()) {
        if (!empty($fila['imagen']) && file_exists($fila['imagen'])) {
            unlink($fila['imagen']);
        }
    }
    $conexion2->query("DELETE FROM viajes WHERE id = $id");
    header("Location: gestionar_viajes.php");
    exit();
}

// Edita(mostrar datos en formulario)
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $conexion2->query("SELECT * FROM viajes WHERE id = $id");
    if ($res && $res->num_rows > 0) {
        $editData = $res->fetch_assoc();
    }
}

// Ver todos los viajes
$viajes = $conexion2->query("SELECT * FROM viajes ORDER BY fecha_salida ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestionar Viajes - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 min-h-screen">
  <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4 text-center">
      <?= $editData ? 'Editar Viaje' : 'Agregar Nuevo Viaje' ?>
    </h2>

    <!-- FORMULARIO -->
    <form method="POST" enctype="multipart/form-data" class="mb-6 grid gap-3">
      <?php if ($editData): ?>
        <input type="hidden" name="id_edit" value="<?= $editData['id'] ?>">
      <?php endif; ?>
      
      <input type="text" name="titulo" placeholder="Título del viaje" required class="border p-2 rounded"
             value="<?= $editData['titulo'] ?? '' ?>">
      <input type="date" name="fecha" required class="border p-2 rounded"
             value="<?= $editData['fecha_salida'] ?? '' ?>">
      <input type="number" name="capacidad" placeholder="Capacidad" required class="border p-2 rounded"
             value="<?= $editData['capacidad'] ?? '' ?>">
      <input type="number" name="precio" placeholder="Precio (COP)" required class="border p-2 rounded"
             value="<?= $editData['precio'] ?? '' ?>">
      <textarea name="descripcion" placeholder="Descripción del viaje" class="border p-2 rounded"><?= $editData['descripcion'] ?? '' ?></textarea>
      <input type="file" name="imagen" accept="image/*" class="border p-2 rounded">

      <?php if ($editData && !empty($editData['imagen'])): ?>
        <div class="text-center">
          <img src="<?= htmlspecialchars($editData['imagen']) ?>" alt="Imagen actual" class="w-32 h-20 object-cover mx-auto rounded mb-2">
          <p class="text-sm text-gray-500">Puedes subir una nueva imagen para reemplazarla.</p>
        </div>
      <?php endif; ?>

      <div class="flex justify-between">
        <button class="bg-green-600 text-white py-2 px-4 rounded">
          <?= $editData ? 'Actualizar Viaje' : 'Agregar Viaje' ?>
        </button>

        <?php if ($editData): ?>
          <a href="gestionar_viajes.php" class="bg-gray-400 text-white py-2 px-4 rounded">Cancelar</a>
        <?php endif; ?>
      </div>
    </form>

    <!-- TABLA DE VIAJES -->
    <table class="w-full table-auto border">
      <thead>
        <tr class="bg-gray-200 text-left">
          <th class="p-2">Imagen</th>
          <th class="p-2">Título</th>
          <th class="p-2">Fecha</th>
          <th class="p-2">Capacidad</th>
          <th class="p-2">Precio</th>
          <th class="p-2">Descripción</th>
          <th class="p-2 text-center">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($viajes as $v): ?>
          <tr class="border-t">
            <td class="p-2 text-center">
              <?php if (!empty($v['imagen']) && file_exists($v['imagen'])): ?>
                <img src="<?= htmlspecialchars($v['imagen']) ?>" alt="Imagen del viaje" class="w-24 h-16 object-cover mx-auto rounded">
              <?php else: ?>
                <span class="text-gray-400">Sin imagen</span>
              <?php endif; ?>
            </td>
            <td class="p-2"><?= htmlspecialchars($v['titulo']) ?></td>
            <td class="p-2"><?= htmlspecialchars($v['fecha_salida']) ?></td>
            <td class="p-2"><?= htmlspecialchars($v['capacidad']) ?></td>
            <td class="p-2 font-semibold text-green-700">$<?= number_format($v['precio'], 0, ',', '.') ?></td>
            <td class="p-2 text-sm"><?= htmlspecialchars($v['descripcion']) ?></td>
            <td class="p-2 text-center">
              <a href="?edit=<?= $v['id'] ?>" class="bg-yellow-500 text-white px-2 py-1 rounded mr-1">Editar</a>
              <a href="?delete=<?= $v['id'] ?>" class="bg-red-500 text-white px-2 py-1 rounded" onclick="return confirm('¿Eliminar este viaje?')">Eliminar</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <a href="panel.php" class="block mt-4 text-sm text-gray-600 text-center">⬅ Volver al panel</a>
  </div>
</body>
</html>
