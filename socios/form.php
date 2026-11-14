<?php
require __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
$modoEdicion = $id > 0;
$socio = ['nombre' => '', 'email' => '', 'telefono' => '', 'direccion' => ''];

if ($modoEdicion) {
    $stmt = $mysqli->prepare("SELECT * FROM socio WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $socio = $stmt->get_result()->fetch_assoc();
    if (!$socio) { header('Location: index.php'); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    if ($nombre !== '' && $email !== '') {
        if ($modoEdicion) {
            $stmt = $mysqli->prepare("UPDATE socio SET nombre=?, email=?, telefono=?, direccion=? WHERE id=?");
            $stmt->bind_param("ssssi", $nombre, $email, $telefono, $direccion, $id);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO socio (nombre, email, telefono, direccion) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $email, $telefono, $direccion);
        }
        $stmt->execute();
        header('Location: index.php?msg=ok');
        exit;
    }
}

$page = 'socios';
$titulo = $modoEdicion ? 'Editar Socio' : 'Nuevo Socio';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-people-fill"></i> <?php echo $modoEdicion ? 'Editar' : 'Nuevo'; ?> Socio</h1>
</div>

<div class="card-custom" style="max-width:700px;">
    <form method="POST">
        <div class="form-grid">
            <div class="form-row">
                <label class="form-label">Nombre *</label>
                <input type="text" name="nombre" class="form-control-custom" required value="<?php echo htmlspecialchars($socio['nombre']); ?>">
            </div>
            <div class="form-row">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-control-custom" required value="<?php echo htmlspecialchars($socio['email']); ?>">
            </div>
        </div>
        <div class="form-grid">
            <div class="form-row">
                <label class="form-label">Teléfono</label>
                <input type="tel" name="telefono" class="form-control-custom" value="<?php echo htmlspecialchars($socio['telefono'] ?? ''); ?>">
            </div>
            <div class="form-row">
                <label class="form-label">Dirección</label>
                <input type="text" name="direccion" class="form-control-custom" value="<?php echo htmlspecialchars($socio['direccion'] ?? ''); ?>">
            </div>
        </div>
        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="index.php" class="btn-custom btn-back">Cancelar</a>
            <button type="submit" class="btn-custom btn-primary-custom"><i class="bi bi-save"></i> Guardar</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>