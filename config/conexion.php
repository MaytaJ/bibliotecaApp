<?php
require_once __DIR__ . '/config.php';

$mysqli = new mysqli('localhost', 'root', '', 'biblioteca');

if ($mysqli->connect_error) {
    die('Error en la conexion: ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');