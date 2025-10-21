<?php
// conexion.php
$servidor = 'localhost';
$basedatos = 'agencia_viajes';
$usuario = 'root';
$contrasena = '';

$conexion2 = new mysqli($servidor, $usuario, $contrasena, $basedatos);
if ($conexion2->connect_errno) {
    echo "Error de conexión, verifique sus datos";
    exit();
}
?>

