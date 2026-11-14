<?php
require __DIR__ . '/../config/conexion.php';

$idLibro = (int) ($_GET['id_libro'] ?? 0);
$idBibliotecario = (int) ($_POST['id_bibliotecario'] ?? 0);
$errores = [];

$libro = null;
if ($idLibro > 0) {
    $stmt = $mysqli->prepare("SELECT l.*, a.nombre AS autor_nombre FROM libro l LEFT JOIN autor a ON a.id = l.id_autor WHERE l.id = ?");
    $stmt->bind_param("i", $idLibro);
    $stmt->execute();
    $libro = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idLibroPost = (int) ($_POST['id_libro'] ?? 0);
    $idSocio = (int) ($_POST['id_socio'] ?? 0);

    if ($idLibroPost <= 0)
        $errores[] = 'Debe seleccionar un libro';
    if ($idSocio <= 0)
        $errores[] = 'Debe seleccionar un socio';

    if (empty($errores)) {
        $check = $mysqli->prepare("SELECT ejemplares_disponibles FROM libro WHERE id = ?");
        $check->bind_param("i", $idLibroPost);
        $check->execute();
        $stock = $check->get_result()->fetch_assoc();

        if (!$stock || $stock['ejemplares_disponibles'] <= 0) {
            $errores[] = 'No hay ejemplares disponibles de este libro';
        } else {
            $hoy = date('Y-m-d');
            $vence = date('Y-m-d', strtotime('+14 days'));

            $stmt = $mysqli->prepare("INSERT INTO prestamo(id_libro, id_socio, id_bibliotecario, fecha_prestamo, fecha_vencimiento, estado)VALUES (?, ?, ?, ?, ?, 'PRESTADO')");
            $stmt->bind_param("iiiss", $idLibroPost, $idSocio, $idBibliotecario, $hoy, $vence);
            $stmt->execute();

            $upd = $mysqli->prepare("UPDATE libro SET ejemplares_disponibles = ejemplares_disponibles - 1 WHERE id = ?");
            $upd->bind_param("i", $idLibroPost);
            $upd->execute();

            header('Location: index.php?msg=ok');
            exit;
        }
    }
}
if ($idBibliotecario <= 0)
    $errores[] = 'Debe seleccionar un bibliotecario';

$libros = $mysqli->query("SELECT id, titulo, ejemplares_disponibles FROM libro WHERE ejemplares_disponibles > 0 ORDER BY titulo");
$socios = $mysqli->query("SELECT id, nombre FROM socio ORDER BY nombre");
$bibliotecarios = $mysqli->query("SELECT id, nombre FROM bibliotecario ORDER BY nombre");
$page = 'prestamos';
$titulo = 'Nuevo Préstamo';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-journal-arrow-up"></i> Nuevo Préstamo</h1>
</div>

<div class="card-custom" style="max-width:700px;">
    <?php if ($errores) { ?>
        <div class="alert-err"><i class="bi bi-exclamation-triangle-fill"></i>
            <?php echo htmlspecialchars(implode('. ', $errores)); ?></div>
    <?php } ?>

    <?php if ($libro) { ?>
        <div
            style="background:#f8fafc; padding:15px; border-radius:12px; margin-bottom:20px; display:flex; align-items:center; gap:15px;">
            <div style="font-size:2.5rem;"><?php echo $libro['portada']; ?></div>
            <div>
                <strong><?php echo htmlspecialchars($libro['titulo']); ?></strong><br>
                <small style="color:#64748b;"><?php echo htmlspecialchars($libro['autor_nombre'] ?? ''); ?> ·
                    <?php echo $libro['ejemplares_disponibles']; ?> disponibles</small>
            </div>
        </div>
    <?php } ?>

    <form method="POST">
        <?php if ($libro) { ?>
            <input type="hidden" name="id_libro" value="<?php echo $libro['id']; ?>">
        <?php } else { ?>
            <div class="form-row">
                <label class="form-label">Libro *</label>
                <select name="id_libro" class="form-control-custom" required>
                    <option value="">— Elegí un libro —</option>
                    <?php while ($l = $libros->fetch_assoc()) { ?>
                        <option value="<?php echo $l['id']; ?>"><?php echo htmlspecialchars($l['titulo']); ?>
                            (<?php echo $l['ejemplares_disponibles']; ?> disp.)</option>
                    <?php } ?>
                </select>
            </div>
        <?php } ?>

        <div class="form-row">
            <label class="form-label">Socio *</label>
            <select name="id_socio" class="form-control-custom" required>
                <option value="">— Elegí un socio —</option>
                <?php while ($s = $socios->fetch_assoc()) { ?>
                    <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-row">
            <label class="form-label">Bibliotecario *</label>

            <select name="id_bibliotecario" class="form-control-custom" required>
                <option value="">— Elegí un bibliotecario —</option>
                <?php while ($b = $bibliotecarios->fetch_assoc()) { ?>
                    <option value="<?php echo $b['id']; ?>">
                        <?php echo htmlspecialchars($b['nombre']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <p style="color:#64748b; font-size:0.85rem; margin-bottom:20px;">
            <i class="bi bi-info-circle"></i> El préstamo tendrá una duración de 14 días.
        </p>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="index.php" class="btn-custom btn-back">Cancelar</a>
            <button type="submit" class="btn-custom btn-primary-custom"><i class="bi bi-check-lg"></i> Confirmar
                Préstamo</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>