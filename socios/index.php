<?php
require __DIR__ . '/../config/conexion.php';

$resultado = $mysqli->query("SELECT s.*, COUNT(p.id) AS total_prestamos FROM socio s LEFT JOIN prestamo p ON p.id_socio = s.id AND p.estado='PRESTADO' GROUP BY s.id ORDER BY s.nombre");

$page = 'socios';
$titulo = 'Socios';
include __DIR__ . '/../includes/header.php';
?>

<?php if (($_GET['msg'] ?? '') === 'ok') { ?>
    <div class="alert-ok"><i class="bi bi-check-circle-fill"></i> Operación realizada</div>
<?php } ?>

<div class="page-header">
    <h1><i class="bi bi-people-fill"></i> Socios</h1>
</div>

<div class="card-custom">
    <div style="margin-bottom:20px;">
        <a href="form.php" class="btn-custom btn-primary-custom"><i class="bi bi-plus-circle"></i> Nuevo Socio</a>
    </div>

    <div style="overflow-x:auto;">
        <table class="table-custom">
            <thead><tr><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Dirección</th><th>Préstamos activos</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php while ($s = $resultado->fetch_assoc()) { ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($s['nombre']); ?></strong></td>
                        <td><?php echo htmlspecialchars($s['email']); ?></td>
                        <td><?php echo htmlspecialchars($s['telefono'] ?? '—'); ?></td>
                        <td><?php echo htmlspecialchars($s['direccion'] ?? '—'); ?></td>
                        <td><span class="badge-custom badge-info"><?php echo $s['total_prestamos']; ?></span></td>
                        <td>
                            <a href="form.php?id=<?php echo $s['id']; ?>" class="btn-custom btn-icon btn-edit"><i class="bi bi-pencil"></i></a>
                            <a href="eliminar.php?id=<?php echo $s['id']; ?>" class="btn-custom btn-icon btn-delete" onclick="return confirm('¿Eliminar socio?');"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>