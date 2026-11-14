<?php
require __DIR__ . '/../config/conexion.php';

$resultado = $mysqli->query("SELECT a.*, COUNT(l.id) AS total_libros FROM autor a LEFT JOIN libro l ON l.id_autor = a.id GROUP BY a.id ORDER BY a.nombre");

$page = 'autores';
$titulo = 'Autores';
include __DIR__ . '/../includes/header.php';
?>

<?php if (($_GET['msg'] ?? '') === 'ok') { ?>
    <div class="alert-ok"><i class="bi bi-check-circle-fill"></i> Operación realizada</div>
<?php } ?>

<div class="page-header">
    <h1><i class="bi bi-pen-fill"></i> Autores</h1>
</div>

<div class="card-custom">
    <div style="margin-bottom:20px;">
        <a href="form.php" class="btn-custom btn-primary-custom"><i class="bi bi-plus-circle"></i> Nuevo Autor</a>
    </div>

    <div style="overflow-x:auto;">
        <table class="table-custom">
            <thead><tr><th>Nombre</th><th>Nacionalidad</th><th>Libros</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php while ($a = $resultado->fetch_assoc()) { ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($a['nombre']); ?></strong></td>
                        <td><?php echo htmlspecialchars($a['nacionalidad'] ?? '—'); ?></td>
                        <td><span class="badge-custom badge-info"><?php echo $a['total_libros']; ?></span></td>
                        <td>
                            <a href="form.php?id=<?php echo $a['id']; ?>" class="btn-custom btn-icon btn-edit"><i class="bi bi-pencil"></i></a>
                            <a href="eliminar.php?id=<?php echo $a['id']; ?>" class="btn-custom btn-icon btn-delete" onclick="return confirm('¿Eliminar autor?');"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>