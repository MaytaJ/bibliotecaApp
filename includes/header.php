<?php
require_once __DIR__ . '/../config/config.php';
if (!isset($page))
    $page = '';
if (!isset($titulo))
    $titulo = 'Biblioteca';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($titulo); ?> - Biblioteca</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/estilos.css">
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="<?php echo BASE_URL; ?>index.php" class="nav-brand"><i class="bi bi-book-half"></i> Biblioteca</a>
            <div class="nav-links">
                <a href="<?php echo BASE_URL; ?>index.php" class="<?php echo $page === 'inicio' ? 'active' : ''; ?>"><i class="bi bi-house-fill"></i> Catálogo</a>
                <a href="<?php echo BASE_URL; ?>libros/" class="<?php echo $page === 'libros' ? 'active' : ''; ?>"><i class="bi bi-book"></i> Libros</a>
                <a href="<?php echo BASE_URL; ?>autores/" class="<?php echo $page === 'autores' ? 'active' : ''; ?>"><i class="bi bi-pen-fill"></i> Autores</a>
                <a href="<?php echo BASE_URL; ?>socios/" class="<?php echo $page === 'socios' ? 'active' : ''; ?>"><i class="bi bi-people-fill"></i> Socios</a>
                <a href="<?php echo BASE_URL; ?>bibliotecarios/" class="<?php echo $page === 'bibliotecarios' ? 'active' : ''; ?>"><i class="bi bi-person-badge-fill"></i> Bibliotecarios</a>
                <a href="<?php echo BASE_URL; ?>prestamos/" class="<?php echo $page === 'prestamos' ? 'active' : ''; ?>"><i class="bi bi-arrow-left-right"></i> Préstamos</a>
            </div>
        </div>
    </nav>
    <main class="app-container">