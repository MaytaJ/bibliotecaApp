<?php
require __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
$libroId = (int)($_GET['libro'] ?? 0);

$stmt = $mysqli->prepare("DELETE FROM resena WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header('Location: ../libros/ver.php?id=' . $libroId);
exit;