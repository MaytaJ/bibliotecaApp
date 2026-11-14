<?php
require __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $mysqli->prepare("SELECT * FROM prestamo WHERE id = ? AND estado = 'PRESTADO'");
$stmt->bind_param("i", $id);
$stmt->execute();
$prestamo = $stmt->get_result()->fetch_assoc();

if ($prestamo) {
    $hoy = date('Y-m-d');
    $upd = $mysqli->prepare("UPDATE prestamo SET estado='DEVUELTO', fecha_devolucion=? WHERE id=?");
    $upd->bind_param("si", $hoy, $id);
    $upd->execute();

    $updLibro = $mysqli->prepare("UPDATE libro SET ejemplares_disponibles = ejemplares_disponibles + 1 WHERE id = ?");
    $updLibro->bind_param("i", $prestamo['id_libro']);
    $updLibro->execute();
}

header('Location: index.php?msg=ok');
exit;