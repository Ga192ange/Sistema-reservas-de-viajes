<?php
// procesar_reserva.php
session_start();
include("conexion.php");

// Solo clientes pueden reservar
if (!isset($_SESSION['user_id']) || !isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: reservar.php");
    exit();
}

$viaje_id = isset($_POST['id_viaje']) ? (int)$_POST['id_viaje'] : 0;
$usuario_id = (int)$_SESSION['user_id'];
$plazas = 1; // por ahora una plaza por clic (puedes cambiarlo luego)
$estado = 'booked';

if ($viaje_id <= 0) {
    header("Location: reservar.php?error=viaje_invalid");
    exit();
}

// 1) Verificar existencia del viaje y capacidad
$stmt = $conexion2->prepare("SELECT capacidad FROM viajes WHERE id = ?");
if (!$stmt) {
    die("Error DB: " . $conexion2->error);
}
$stmt->bind_param("i", $viaje_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    $stmt->close();
    header("Location: reservar.php?error=viaje_no_encontrado");
    exit();
}
$viaje = $res->fetch_assoc();
$stmt->close();
$capacidad = (int)$viaje['capacidad'];

// 2) Calcular plazas ocupadas actualmente (solo booked)
$stmt2 = $conexion2->prepare("SELECT COALESCE(SUM(plazas),0) AS ocupadas FROM reservas WHERE viaje_id = ? AND estado = 'booked'");
$stmt2->bind_param("i", $viaje_id);
$stmt2->execute();
$res2 = $stmt2->get_result()->fetch_assoc();
$ocupadas = (int)$res2['ocupadas'];
$stmt2->close();

$disponibles = $capacidad - $ocupadas;
if ($disponibles < $plazas) {
    header("Location: reservar.php?error=no_hay_cupos&disponibles=" . $disponibles);
    exit();
}

// 3) Insertar la reserva
$stmt3 = $conexion2->prepare("INSERT INTO reservas (usuario_id, viaje_id, plazas, estado) VALUES (?, ?, ?, ?)");
if (!$stmt3) {
    die("Error DB insert prepare: " . $conexion2->error);
}
$stmt3->bind_param("iiis", $usuario_id, $viaje_id, $plazas, $estado);
if ($stmt3->execute()) {
    $stmt3->close();
    header("Location: mis_reservas.php?success=1");
    exit();
} else {
    $stmt3->close();
    header("Location: reservar.php?error=insert_fail");
    exit();
}
