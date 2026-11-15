<?php
require __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $mysqli->prepare("DELETE FROM bibliotecario WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header('Location: index.php?msg=ok');
exit;