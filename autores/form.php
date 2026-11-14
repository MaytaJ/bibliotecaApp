<?php
require __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
$modoEdicion = $id > 0;
$autor = ['nombre' => '', 'nacionalidad' => ''];

if ($modoEdicion) {
    $stmt = $mysqli->prepare("SELECT * FROM autor WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $autor = $stmt->get_result()->fetch_assoc();
    if (!$autor) { header('Location: index.php'); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $nacionalidad = trim($_POST['nacionalidad'] ?? '');

    if ($nombre !== '') {
        if ($modoEdicion) {
            $stmt = $mysqli->prepare("UPDATE autor SET nombre=?, nacionalidad=? WHERE id=?");
            $stmt->bind_param("ssi", $nombre, $nacionalidad, $id);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO autor (nombre, nacionalidad) VALUES (?, ?)");
            $stmt->bind_param("ss", $nombre, $nacionalidad);
        }
        $stmt->execute();
        header('Location: index.php?msg=ok');
        exit;
    }
}

$page = 'autores';
$titulo = $modoEdicion ? 'Editar Autor' : 'Nuevo Autor';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-pen-fill"></i> <?php echo $modoEdicion ? 'Editar' : 'Nuevo'; ?> Autor</h1>
</div>

<div class="card-custom" style="max-width:600px;">
    <form method="POST">
        <div class="form-row">
            <label class="form-label">Nombre *</label>
            <input type="text" name="nombre" class="form-control-custom" required value="<?php echo htmlspecialchars($autor['nombre']); ?>">
        </div>
        <div class="form-row">
            <label class="form-label">Nacionalidad</label>
            <input type="text" name="nacionalidad" class="form-control-custom" value="<?php echo htmlspecialchars($autor['nacionalidad'] ?? ''); ?>">
        </div>
        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="index.php" class="btn-custom btn-back">Cancelar</a>
            <button type="submit" class="btn-custom btn-primary-custom"><i class="bi bi-save"></i> Guardar</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>