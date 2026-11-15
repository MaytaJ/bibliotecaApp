<?php
require __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
$modoEdicion = $id > 0;

$bibliotecario = [
    'nombre' => '',
    'telefono' => ''
];

if ($modoEdicion) {
    $stmt = $mysqli->prepare("SELECT * FROM bibliotecario WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $bibliotecario = $stmt->get_result()->fetch_assoc();

    if (!$bibliotecario) {
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');

    if ($nombre !== '') {
        if ($modoEdicion) {
            $stmt = $mysqli->prepare("UPDATE bibliotecario SET nombre = ?, telefono = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nombre, $telefono, $id);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO bibliotecario (nombre, telefono) VALUES (?, ?)");
            $stmt->bind_param("ss", $nombre, $telefono);
        }
        $stmt->execute();
        header('Location: index.php?msg=ok');
        exit;
    }
}

$page = 'bibliotecarios';
$titulo = $modoEdicion ? 'Editar Bibliotecario' : 'Nuevo Bibliotecario';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>
        <i class="bi bi-person-badge-fill"></i>
        <?php echo $modoEdicion ? 'Editar Bibliotecario' : 'Nuevo Bibliotecario'; ?>
    </h1>
</div>

<div class="card-custom" style="max-width:700px;">
    <form method="POST">
        <div class="form-row">
            <label class="form-label">Nombre *</label>
            <input type="text" name="nombre" class="form-control-custom" required value="<?php echo htmlspecialchars($bibliotecario['nombre']); ?>">
        </div>

        <div class="form-row">
            <label class="form-label">Teléfono</label>
            <input type="tel" name="telefono" class="form-control-custom" value="<?php echo htmlspecialchars($bibliotecario['telefono'] ?? ''); ?>">
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="index.php" class="btn-custom btn-back">Cancelar</a>
            <button type="submit" class="btn-custom btn-primary-custom">
                <i class="bi bi-save"></i> Guardar
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>