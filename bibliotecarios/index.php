<?php
require __DIR__ . '/../config/conexion.php';

$resultado = $mysqli->query("
    SELECT b.*, COUNT(p.id) AS total_prestamos
    FROM bibliotecario b
    LEFT JOIN prestamo p ON p.id_bibliotecario = b.id
    GROUP BY b.id
    ORDER BY b.nombre
");

$page = 'bibliotecarios';
$titulo = 'Bibliotecarios';
include __DIR__ . '/../includes/header.php';
?>

<?php if (($_GET['msg'] ?? '') === 'ok') { ?>
    <div class="alert-ok">
        <i class="bi bi-check-circle-fill"></i> Operación realizada
    </div>
<?php } ?>

<div class="page-header">
    <h1><i class="bi bi-person-badge-fill"></i> Bibliotecarios</h1>
</div>

<div class="card-custom">
    <div style="margin-bottom:20px;">
        <a href="form.php" class="btn-custom btn-primary-custom">
            <i class="bi bi-plus-circle"></i> Nuevo Bibliotecario
        </a>
    </div>

    <div style="overflow-x:auto;">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Préstamos registrados</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($b = $resultado->fetch_assoc()) { ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($b['nombre']); ?></strong></td>
                        <td><?php echo htmlspecialchars($b['telefono'] ?? '—'); ?></td>
                        <td><span class="badge-custom badge-info"><?php echo $b['total_prestamos']; ?></span></td>
                        <td>
                            <a href="form.php?id=<?php echo $b['id']; ?>" class="btn-custom btn-icon btn-edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="eliminar.php?id=<?php echo $b['id']; ?>" class="btn-custom btn-icon btn-delete" onclick="return confirm('¿Eliminar bibliotecario?');">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>