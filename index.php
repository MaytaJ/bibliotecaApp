<?php
require __DIR__ . '/config/conexion.php';
require __DIR__ . '/includes/funciones.php';

$buscar = trim($_GET['buscar'] ?? '');
$categoria = trim($_GET['categoria'] ?? '');
$disponible = $_GET['disponible'] ?? '';
$orden = $_GET['orden'] ?? 'recientes';

$where = [];
$params = [];
$types = '';

if ($buscar !== '') {
    $where[] = "(l.titulo LIKE ? OR a.nombre LIKE ?)";
    $like = "%$buscar%";
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}
if ($categoria !== '') {
    $where[] = "l.categoria = ?";
    $params[] = $categoria;
    $types .= 's';
}
if ($disponible === 'si')
    $where[] = "l.ejemplares_disponibles > 0";
if ($disponible === 'no')
    $where[] = "l.ejemplares_disponibles = 0";

$orderSql = 'l.id DESC';
if ($orden === 'titulo')
    $orderSql = 'l.titulo ASC';
if ($orden === 'anio')
    $orderSql = 'l.anio DESC';
if ($orden === 'puntuacion')
    $orderSql = 'promedio DESC';

$sql = "SELECT l.*, a.nombre AS autor_nombre,
        COALESCE(AVG(r.estrellas), 0) AS promedio,
        COUNT(DISTINCT r.id) AS total_resenas
        FROM libro l
        LEFT JOIN autor a ON a.id = l.id_autor
        LEFT JOIN resena r ON r.id_libro = l.id
        WHERE " . ($where ? implode(' AND ', $where) : '1=1') . "
        GROUP BY l.id
        ORDER BY $orderSql";

$stmt = $mysqli->prepare($sql);
if ($types)
    $stmt->bind_param($types, ...$params);
$stmt->execute();
$libros = $stmt->get_result();

$categorias = $mysqli->query("SELECT DISTINCT categoria FROM libro ORDER BY categoria");

$total = $mysqli->query("SELECT COUNT(*) t FROM libro")->fetch_assoc()['t'];
$disponibles = $mysqli->query("SELECT SUM(ejemplares_disponibles) t FROM libro")->fetch_assoc()['t'];
$prestados = $mysqli->query("SELECT COUNT(*) t FROM prestamo WHERE estado='PRESTADO'")->fetch_assoc()['t'];
$resenas = $mysqli->query("SELECT COUNT(*) t FROM resena")->fetch_assoc()['t'];

$page = 'inicio';
$titulo = 'Catálogo';
include __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1><i class="bi bi-book-half"></i> Catálogo de Libros</h1>
    <p>Explorá nuestra colección, filtrá por categoría y reservá tu próximo libro</p>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Libros</div>
        <div class="stat-value"><?php echo $total; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Disponibles</div>
        <div class="stat-value"><?php echo $disponibles; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">En Préstamo</div>
        <div class="stat-value"><?php echo $prestados; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Reseñas</div>
        <div class="stat-value"><?php echo $resenas; ?></div>
    </div>
</div>

<div class="card-custom">
    <form method="GET" class="filter-bar">
        <input type="text" name="buscar" class="search-input" placeholder="Buscar por título o autor..."
            value="<?php echo htmlspecialchars($buscar); ?>">
        <select name="orden" class="form-control-custom" style="max-width:180px;" onchange="this.form.submit()">
            <option value="recientes" <?php echo $orden == 'recientes' ? 'selected' : ''; ?>>Más recientes</option>
            <option value="titulo" <?php echo $orden == 'titulo' ? 'selected' : ''; ?>>Título A-Z</option>
            <option value="anio" <?php echo $orden == 'anio' ? 'selected' : ''; ?>>Año (nuevo)</option>
            <option value="puntuacion" <?php echo $orden == 'puntuacion' ? 'selected' : ''; ?>>Mejor puntuados</option>
        </select>
        <button type="submit" class="btn-custom btn-search"><i class="bi bi-search"></i> Buscar</button>
        <?php if ($buscar !== '' || $categoria !== '' || $disponible !== '') { ?>
            <a href="index.php" class="btn-custom btn-back"><i class="bi bi-x-lg"></i> Limpiar</a>
        <?php } ?>
    </form>

    <div class="filter-bar">
        <a href="index.php?buscar=<?php echo urlencode($buscar); ?>"
            class="filter-chip <?php echo $categoria === '' ? 'active' : ''; ?>">Todas</a>
        <?php while ($cat = $categorias->fetch_assoc()) { ?>
            <a href="index.php?buscar=<?php echo urlencode($buscar); ?>&categoria=<?php echo urlencode($cat['categoria']); ?>"
                class="filter-chip <?php echo $categoria === $cat['categoria'] ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($cat['categoria']); ?>
            </a>
        <?php } ?>
        <span style="margin-left:auto; font-size:0.85rem; color:#64748b;">Disponibilidad:</span>
        <a href="?buscar=<?php echo urlencode($buscar); ?>&categoria=<?php echo urlencode($categoria); ?>&disponible=si"
            class="filter-chip <?php echo $disponible === 'si' ? 'active' : ''; ?>">Con stock</a>
        <a href="?buscar=<?php echo urlencode($buscar); ?>&categoria=<?php echo urlencode($categoria); ?>&disponible=no"
            class="filter-chip <?php echo $disponible === 'no' ? 'active' : ''; ?>">Sin stock</a>
    </div>

    <?php if ($libros->num_rows > 0) { ?>
        <div class="books-grid">
            <?php while ($l = $libros->fetch_assoc()) { ?>
                <a href="libros/ver.php?id=<?php echo $l['id']; ?>" class="book-card">
                    <div class="book-cover <?php echo coverClass($l['categoria']); ?>">
                        <?php echo $l['portada']; ?>
                    </div>
                    <div class="book-info">
                        <div class="book-category"><?php echo htmlspecialchars($l['categoria']); ?></div>
                        <div class="book-title"><?php echo htmlspecialchars($l['titulo']); ?></div>
                        <div class="book-author"><?php echo htmlspecialchars($l['autor_nombre'] ?? 'Autor desconocido'); ?>
                        </div>
                        <div class="book-footer">
                            <div class="stars"><?php echo starsHtml($l['promedio']); ?></div>
                            <?php if ($l['ejemplares_disponibles'] > 0) { ?>
                                <span class="badge-custom badge-success">Disponible</span>
                            <?php } else { ?>
                                <span class="badge-custom badge-danger">Sin stock</span>
                            <?php } ?>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h3>No hay libros que coincidan</h3>
            <p>Probá con otros filtros o limpiá la búsqueda</p>
        </div>
    <?php } ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>