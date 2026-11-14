<?php
require __DIR__ . '/../config/conexion.php';

$buscar = trim($_GET['buscar'] ?? '');

if ($buscar !== '') {
    $stmt = $mysqli->prepare("SELECT l.*, a.nombre AS autor_nombre FROM libro l LEFT JOIN autor a ON a.id = l.id_autor WHERE l.titulo LIKE ? OR a.nombre LIKE ? ORDER BY l.id DESC");
    $like = "%$buscar%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = $mysqli->query("SELECT l.*, a.nombre AS autor_nombre FROM libro l LEFT JOIN autor a ON a.id = l.id_autor ORDER BY l.id DESC");
}

$page = 'libros';
$titulo = 'Libros';
include __DIR__ . '/../includes/header.php';
?>

<?php if (($_GET['msg'] ?? '') === 'ok') { ?>
    <div class="alert-ok"><i class="bi bi-check-circle-fill"></i> Operación realizada correctamente</div>
<?php } ?>

<div class="page-header">
    <h1><i class="bi bi-book"></i> Gestión de Libros</h1>
    <p>Administrá el catálogo completo</p>
</div>

<div class="card-custom">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:20px; flex-wrap:wrap;">
        <a href="form.php" class="btn-custom btn-primary-custom"><i class="bi bi-plus-circle"></i> Nuevo Libro</a>
        <form method="GET" style="display:flex; gap:10px; flex:1; max-width:450px;">
            <input type="text" name="buscar" class="search-input" placeholder="Buscar..." value="<?php echo htmlspecialchars($buscar); ?>">
            <button type="submit" class="btn-custom btn-search"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <?php if ($resultado->num_rows > 0) { ?>
        <div style="overflow-x:auto;">
            <table class="table-custom">
                <thead>
                    <tr><th>Portada</th><th>Título</th><th>Autor</th><th>Categoría</th><th>Año</th><th>Stock</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php while ($l = $resultado->fetch_assoc()) { ?>
                        <tr>
                            <td style="font-size:1.8rem;"><?php echo $l['portada']; ?></td>
                            <td><strong><?php echo htmlspecialchars($l['titulo']); ?></strong></td>
                            <td><?php echo htmlspecialchars($l['autor_nombre'] ?? '—'); ?></td>
                            <td><span class="badge-custom badge-info"><?php echo htmlspecialchars($l['categoria']); ?></span></td>
                            <td><?php echo $l['anio']; ?></td>
                            <td>
                                <?php if ($l['ejemplares_disponibles'] > 0) { ?>
                                    <span class="badge-custom badge-success"><?php echo $l['ejemplares_disponibles']; ?>/<?php echo $l['ejemplares_total']; ?></span>
                                <?php } else { ?>
                                    <span class="badge-custom badge-danger">0/<?php echo $l['ejemplares_total']; ?></span>
                                <?php } ?>
                            </td>
                            <td>
                                <a href="ver.php?id=<?php echo $l['id']; ?>" class="btn-custom btn-icon btn-back" title="Ver"><i class="bi bi-eye"></i></a>
                                <a href="form.php?id=<?php echo $l['id']; ?>" class="btn-custom btn-icon btn-edit" title="Editar"><i class="bi bi-pencil"></i></a>
                                <a href="eliminar.php?id=<?php echo $l['id']; ?>" class="btn-custom btn-icon btn-delete" onclick="return confirm('¿Eliminar este libro?');" title="Eliminar"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div class="empty-state"><i class="bi bi-inbox"></i><h3>No hay libros</h3></div>
    <?php } ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>