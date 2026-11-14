<?php
require __DIR__ . '/../config/conexion.php';

$idLibro = (int)($_POST['id_libro'] ?? 0);
$nombre = trim($_POST['nombre_lector'] ?? '');
$estrellas = (int)($_POST['estrellas'] ?? 0);
$comentario = trim($_POST['comentario'] ?? '');

if ($idLibro > 0 && $nombre !== '' && $estrellas >= 1 && $estrellas <= 5) {
    $stmt = $mysqli->prepare("INSERT INTO resena (id_libro, nombre_lector, estrellas, comentario) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $idLibro, $nombre, $estrellas, $comentario);
    $stmt->execute();
}

header('Location: ../libros/ver.php?id=' . $idLibro);
exit;