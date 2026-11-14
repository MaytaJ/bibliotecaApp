<?php
require __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
$modoEdicion = $id > 0;
$errores = [];

$libro = [
    'titulo' => '', 'id_autor' => '', 'id_editorial' => '', 'isbn' => '',
    'anio' => date('Y'), 'categoria' => 'Novela', 'descripcion' => '',
    'portada' => '📖', 'ejemplares_total' => 1, 'ejemplares_disponibles' => 1
];

if ($modoEdicion) {
    $stmt = $mysqli->prepare("SELECT * FROM libro WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $libro = $stmt->get_result()->fetch_assoc();
    if (!$libro) { header('Location: index.php'); exit; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $libro['titulo'] = trim($_POST['titulo'] ?? '');
    $libro['id_autor'] = (int)($_POST['id_autor'] ?? 0) ?: null;
    $libro['id_editorial'] = (int)($_POST['id_editorial'] ?? 0) ?: null;
    $libro['isbn'] = trim($_POST['isbn'] ?? '');
    $libro['anio'] = (int)($_POST['anio'] ?? 0) ?: null;
    $libro['categoria'] = trim($_POST['categoria'] ?? 'Novela');
    $libro['descripcion'] = trim($_POST['descripcion'] ?? '');
    $libro['portada'] = trim($_POST['portada'] ?? '📖') ?: '📖';
    $libro['ejemplares_total'] = max(1, (int)($_POST['ejemplares_total'] ?? 1));
    $libro['ejemplares_disponibles'] = max(0, (int)($_POST['ejemplares_disponibles'] ?? 1));

    if ($libro['titulo'] === '') $errores[] = 'El título es obligatorio';

    if (empty($errores)) {
        if ($modoEdicion) {
            $stmt = $mysqli->prepare("UPDATE libro SET titulo=?, id_autor=?, id_editorial=?, isbn=?, anio=?, categoria=?, descripcion=?, portada=?, ejemplares_total=?, ejemplares_disponibles=? WHERE id=?");
            $stmt->bind_param("siisssssiii", $libro['titulo'], $libro['id_autor'], $libro['id_editorial'], $libro['isbn'], $libro['anio'], $libro['categoria'], $libro['descripcion'], $libro['portada'], $libro['ejemplares_total'], $libro['ejemplares_disponibles'], $id);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO libro (titulo, id_autor, id_editorial, isbn, anio, categoria, descripcion, portada, ejemplares_total, ejemplares_disponibles) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("siisssssii", $libro['titulo'], $libro['id_autor'], $libro['id_editorial'], $libro['isbn'], $libro['anio'], $libro['categoria'], $libro['descripcion'], $libro['portada'], $libro['ejemplares_total'], $libro['ejemplares_disponibles']);
        }
        $stmt->execute();
        header('Location: index.php?msg=ok');
        exit;
    }
}

$autores = $mysqli->query("SELECT id, nombre FROM autor ORDER BY nombre");
$editoriales = $mysqli->query("SELECT id, nombre FROM editorial ORDER BY nombre");
$categorias = ['Novela', 'Fantasía', 'Ciencia Ficción', 'Historia', 'Ciencia', 'Poesía', 'Terror', 'Aventura'];

$page = 'libros';
$titulo = $modoEdicion ? 'Editar Libro' : 'Nuevo Libro';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-<?php echo $modoEdicion ? 'pencil-square' : 'plus-circle'; ?>"></i> <?php echo $modoEdicion ? 'Editar' : 'Nuevo'; ?> Libro</h1>
</div>

<div class="card-custom">
    <?php if ($errores) { ?>
        <div class="alert-err"><i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars(implode('. ', $errores)); ?></div>
    <?php } ?>

    <form method="POST">
        <div class="form-grid">
            <div class="form-row">
                <label class="form-label">Título *</label>
                <input type="text" name="titulo" class="form-control-custom" required value="<?php echo htmlspecialchars($libro['titulo']); ?>">
            </div>
            <div class="form-row">
                <label class="form-label">Portada (emoji)</label>
                <input type="text" name="portada" class="form-control-custom" maxlength="4" value="<?php echo htmlspecialchars($libro['portada']); ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-row">
                <label class="form-label">Autor</label>
                <select name="id_autor" class="form-control-custom">
                    <option value="">— Sin autor —</option>
                    <?php while ($a = $autores->fetch_assoc()) { ?>
                        <option value="<?php echo $a['id']; ?>" <?php echo $libro['id_autor'] == $a['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($a['nombre']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-row">
                <label class="form-label">Editorial</label>
                <select name="id_editorial" class="form-control-custom">
                    <option value="">— Sin editorial —</option>
                    <?php while ($e = $editoriales->fetch_assoc()) { ?>
                        <option value="<?php echo $e['id']; ?>" <?php echo $libro['id_editorial'] == $e['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($e['nombre']); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-row">
                <label class="form-label">ISBN</label>
                <input type="text" name="isbn" class="form-control-custom" value="<?php echo htmlspecialchars($libro['isbn']); ?>">
            </div>
            <div class="form-row">
                <label class="form-label">Año</label>
                <input type="number" name="anio" class="form-control-custom" value="<?php echo htmlspecialchars($libro['anio']); ?>">
            </div>
        </div>

        <div class="form-row">
            <label class="form-label">Categoría</label>
            <select name="categoria" class="form-control-custom">
                <?php foreach ($categorias as $c) { ?>
                    <option value="<?php echo $c; ?>" <?php echo $libro['categoria'] == $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-row">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control-custom" rows="4"><?php echo htmlspecialchars($libro['descripcion']); ?></textarea>
        </div>

        <div class="form-grid">
            <div class="form-row">
                <label class="form-label">Ejemplares totales</label>
                <input type="number" name="ejemplares_total" class="form-control-custom" min="1" value="<?php echo $libro['ejemplares_total']; ?>">
            </div>
            <div class="form-row">
                <label class="form-label">Ejemplares disponibles</label>
                <input type="number" name="ejemplares_disponibles" class="form-control-custom" min="0" value="<?php echo $libro['ejemplares_disponibles']; ?>">
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
            <a href="index.php" class="btn-custom btn-back"><i class="bi bi-x-lg"></i> Cancelar</a>
            <button type="submit" class="btn-custom btn-primary-custom"><i class="bi bi-save"></i> Guardar</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>