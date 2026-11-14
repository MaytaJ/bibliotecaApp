<?php
require __DIR__ . '/../config/conexion.php';

$filtro = $_GET['estado'] ?? 'TODOS';
$whereSql = $filtro === 'PRESTADO' ? "WHERE p.estado = 'PRESTADO'" : ($filtro === 'DEVUELTO' ? "WHERE p.estado = 'DEVUELTO'" : "");

$resultado = $mysqli->query("
    SELECT
        p.*,
        l.portada AS portada,
        l.titulo AS titulo,
        s.nombre AS socio_nombre,
        b.nombre AS bibliotecario
    FROM prestamo p
    INNER JOIN libro l ON p.id_libro = l.id
    INNER JOIN socio s ON p.id_socio = s.id
    LEFT JOIN bibliotecario b ON p.id_bibliotecario = b.id
    ORDER BY p.fecha_prestamo DESC
");

$page = 'prestamos';
$titulo = 'Préstamos';
include __DIR__ . '/../includes/header.php';
?>

<?php if (($_GET['msg'] ?? '') === 'ok') { ?>
    <div class="alert-ok"><i class="bi bi-check-circle-fill"></i> Operación realizada</div>
<?php } ?>

<div class="page-header">
    <h1><i class="bi bi-arrow-left-right"></i> Préstamos</h1>
</div>

<div class="card-custom">
    <div
        style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <a href="form.php" class="btn-custom btn-primary-custom"><i class="bi bi-plus-circle"></i> Nuevo Préstamo</a>
        <div class="filter-bar" style="margin:0;">
            <a href="?estado=TODOS" class="filter-chip <?php echo $filtro === 'TODOS' ? 'active' : ''; ?>">Todos</a>
            <a href="?estado=PRESTADO" class="filter-chip <?php echo $filtro === 'PRESTADO' ? 'active' : ''; ?>">Prestados</a>
            <a href="?estado=DEVUELTO" class="filter-chip <?php echo $filtro === 'DEVUELTO' ? 'active' : ''; ?>">Devueltos</a>
        </div>
    </div>

    <?php if ($resultado->num_rows > 0) { ?>
        <div style="overflow-x:auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Libro</th>
                        <th>Socio</th>
                        <th>Fecha préstamo</th>
                        <th>Vence</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                        <th>Bibliotecario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($p = $resultado->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $p['portada']; ?> <strong><?php echo htmlspecialchars($p['titulo']); ?></strong></td>
                            <td><?php echo htmlspecialchars($p['socio_nombre']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($p['fecha_prestamo'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($p['fecha_vencimiento'])); ?></td>
                            <td>
                                <?php if ($p['estado'] === 'PRESTADO') { ?>
                                    <span class="badge-custom badge-info">Prestado</span>
                                <?php } else { ?>
                                    <span class="badge-custom badge-success">Devuelto</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($p['estado'] === 'PRESTADO') { ?>
                                    <a href="devolver.php?id=<?php echo $p['id']; ?>" class="btn-custom btn-icon btn-edit"
                                        onclick="return confirm('¿Registrar devolución?');" title="Devolver"><i
                                            class="bi bi-arrow-return-left"></i></a>
                                <?php } else { ?>
                                    <small
                                        style="color:#94a3b8;"><?php echo date('d/m/Y', strtotime($p['fecha_devolucion'])); ?></small>
                                <?php } ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($p['bibliotecario'] ?? 'Sin asignar'); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div class="empty-state"><i class="bi bi-inbox"></i>
            <h3>Sin préstamos</h3>
        </div>
    <?php } ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>