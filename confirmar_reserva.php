<?php
// confirmar_reserva.php
session_start();
include("conexion.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $viaje_id = intval($_POST['viaje_id']);
    $usuario_id = $_SESSION['user_id'];
    $plazas = 1; // cantidad fija por ahora, puedes cambiarlo más adelante
    $estado = 'booked';

    // Verificar si el viaje existe
    $check_viaje = $conexion2->prepare("SELECT capacidad FROM viajes WHERE id = ?");
    $check_viaje->bind_param("i", $viaje_id);
    $check_viaje->execute();
    $viaje = $check_viaje->get_result()->fetch_assoc();

    if (!$viaje) {
        echo "<script>alert('El viaje no existe.'); window.location.href='reservar.php';</script>";
        exit();
    }

    // Verificar cuántas reservas ya hay
    $check_reservas = $conexion2->prepare("SELECT COALESCE(SUM(plazas), 0) as ocupadas FROM reservas WHERE viaje_id = ? AND estado = 'booked'");
    $check_reservas->bind_param("i", $viaje_id);
    $check_reservas->execute();
    $ocupadas = $check_reservas->get_result()->fetch_assoc()['ocupadas'];

    $disponibles = $viaje['capacidad'] - $ocupadas;

    if ($disponibles <= 0) {
        echo "<script>alert('No hay cupos disponibles para este viaje.'); window.location.href='reservar.php';</script>";
        exit();
    }

    // Insertar la reserva
    $stmt = $conexion2->prepare("INSERT INTO reservas (usuario_id, viaje_id, plazas, estado) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $usuario_id, $viaje_id, $plazas, $estado);

    if ($stmt->execute()) {
        echo "<script>alert('Reserva realizada con éxito ✅'); window.location.href='mis_reservas.php';</script>";
    } else {
        echo "<script>alert('Error al realizar la reserva.'); window.location.href='reservar.php';</script>";
    }

    $stmt->close();
    $conexion2->close();
} else {
    header("Location: reservar.php");
    exit();
}
?>
