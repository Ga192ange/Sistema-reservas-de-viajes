<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'empleado') {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM viajes ORDER BY fecha_salida ASC";
$resultado = $conexion2->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Disponibilidad de viajes</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 min-h-screen">
  <div class="max-w-6xl mx-auto">
    <h2 class="text-3xl font-bold text-center mb-6">Disponibilidad de viajes</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php while ($v = $resultado->fetch_assoc()): ?>
        <?php
        // Imagen por defecto
        $imagen = "img/default.jpg";

        // Si existe una imagen guardada en la BD y el archivo está en el servidor
        if (!empty($v['imagen']) && file_exists($v['imagen'])) {
            $imagen = $v['imagen'];
        } else {
            // Asignar imágenes según el título
            $titulo = strtolower($v['titulo']);
            if (strpos($titulo, 'cartagena') !== false) {
                $imagen = "img/cartagena.jpg";
            } elseif (strpos($titulo, 'monterrey') !== false) {
                $imagen = "img/monterrey.jpg";
            } elseif (strpos($titulo, 'medellin') !== false || strpos($titulo, 'medellín') !== false) {
                $imagen = "img/medellin.jpg";
            }
        }
        ?>
        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
          <img src="<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($v['titulo']) ?>" class="w-full h-48 object-cover">
          <div class="p-4 text-center">
            <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($v['titulo']) ?></h3>
            <p class="text-gray-600 mb-2"><?= htmlspecialchars($v['descripcion']) ?></p>
            <p class="text-sm text-gray-500 mb-2">📅 Salida: <?= htmlspecialchars($v['fecha_salida']) ?></p>
            <p class="text-sm font-semibold text-blue-600 mb-2">🧍 Capacidad: <?= htmlspecialchars($v['capacidad']) ?> personas</p>
            <p class="text-green-700 font-bold text-lg">💰 $<?= number_format($v['precio'], 0, ',', '.') ?> COP</p>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

    <div class="text-center mt-6">
      <a href="panel.php" class="text-gray-600 hover:underline">⬅ Volver al panel</a>
    </div>
  </div>
</body>
</html>
