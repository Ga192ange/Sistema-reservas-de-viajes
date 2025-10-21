<?php
session_start();
include("conexion.php");

// Si el usuario no ha iniciado sesión, redirige
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener todos los viajes disponibles
$resultado = $conexion2->query("SELECT * FROM viajes ORDER BY fecha_salida ASC");
$viajes = $resultado->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reservar Viajes</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 min-h-screen">

  <div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-center text-blue-800">Viajes Disponibles</h1>

    <div class="grid md:grid-cols-3 sm:grid-cols-2 gap-6">
      <?php foreach ($viajes as $v): ?>
        <div class="card-animada bg-white rounded-xl shadow-md overflow-hidden border hover:shadow-lg transition duration-300">
          
          <!-- Imagen del viaje -->
          <?php if (!empty($v['imagen']) && file_exists($v['imagen'])): ?>
            <img src="<?= htmlspecialchars($v['imagen']) ?>" alt="Imagen del viaje" class="w-full h-48 object-cover">
          <?php else: ?>
            <img src="img/default.jpg" alt="Sin imagen" class="w-full h-48 object-cover opacity-70">
          <?php endif; ?>

          <!-- Info del viaje -->
          <div class="p-4">
            <h2 class="text-xl font-semibold text-gray-800 mb-1"><?= htmlspecialchars($v['titulo']) ?></h2>
            <p class="text-sm text-gray-500 mb-2">📅 <?= htmlspecialchars($v['fecha_salida']) ?></p>
            <p class="text-gray-700 text-sm mb-3"><?= htmlspecialchars($v['descripcion']) ?></p>
            <p class="text-green-700 font-semibold mb-2">💰 $<?= number_format($v['precio'], 0, ',', '.') ?> COP</p>
            <p class="text-sm text-gray-600 mb-4">🧍‍♂️ Capacidad: <?= htmlspecialchars($v['capacidad']) ?></p>

            <!-- Botón de reserva -->
            <form method="POST" action="procesar_reserva.php">
              <input type="hidden" name="id_viaje" value="<?= $v['id'] ?>">
              <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">
                Reservar
              </button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="mt-6 text-center">
      <a href="panel.php" class="text-gray-600 hover:underline">⬅ Volver al Panel</a>
    </div>
  </div>

  <!-- 🔹 Animación añadida -->
  <style>
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(25px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .card-animada {
      opacity: 0;
      transform: translateY(25px);
      transition: all 0.6s ease;
    }
    .card-visible {
      animation: fadeUp 0.8s ease forwards;
    }
  </style>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const cards = document.querySelectorAll(".card-animada");
      cards.forEach((card, i) => {
        setTimeout(() => {
          card.classList.add("card-visible");
        }, i * 150);
      });
    });
  </script>

</body>
</html>
