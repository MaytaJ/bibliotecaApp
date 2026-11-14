<?php
require __DIR__ . '/../config/conexion.php';
require __DIR__ . '/../includes/funciones.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $mysqli->prepare("SELECT l.*, a.nombre AS autor_nombre, a.nacionalidad, e.nombre AS editorial_nombre,
    COALESCE((SELECT AVG(estrellas) FROM resena WHERE id_libro = l.id), 0) AS promedio,
    (SELECT COUNT(*) FROM resena WHERE id_libro = l.id) AS total_resenas
    FROM libro l
    LEFT JOIN autor a ON a.id = l.id_autor
    LEFT JOIN editorial e ON e.id = l.id_editorial
    WHERE l.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$libro = $stmt->get_result()->fetch_assoc();

if (!$libro) { header('Location: ../index.php'); exit; }

$stmt = $mysqli->prepare("SELECT * FROM resena WHERE id_libro = ? ORDER BY fecha DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$resenas = $stmt->get_result();

$page = 'inicio';
$titulo = $libro['titulo'];
include __DIR__ . '/../includes/header.php';
?>

<div class="card-custom">
    <div class="book-detail">
        <div class="book-detail-cover <?php echo coverClass($libro['categoria']); ?>">
            <?php echo $libro['portada']; ?>
        </div>
        <div>
            <span class="badge-custom badge-info"><?php echo htmlspecialchars($libro['categoria']); ?></span>
            <h1 class="book-detail-title" style="margin-top:10px;"><?php echo htmlspecialchars($libro['titulo']); ?></h1>
            <div class="book-detail-author">
                por <strong><?php echo htmlspecialchars($libro['autor_nombre'] ?? 'Desconocido'); ?></strong>
                <?php if ($libro['nacionalidad']) echo '(' . htmlspecialchars($libro['nacionalidad']) . ')'; ?>
            </div>

            <div class="stars-large">
                <?php
                $p = round($libro['promedio']);
                for ($i = 1; $i <= 5; $i++) echo $i <= $p ? '★' : '<span class="stars-empty">★</span>';
                ?>
                <span style="font-size:0.9rem; color:#64748b; margin-left:10px;">
                    <?php echo number_format($libro['promedio'], 1); ?> (<?php echo $libro['total_resenas']; ?> reseñas)
                </span>
            </div>

            <div class="book-detail-meta">
                <span><i class="bi bi-building"></i> <?php echo htmlspecialchars($libro['editorial_nombre'] ?? '—'); ?></span>
                <span><i class="bi bi-calendar"></i> <?php echo $libro['anio']; ?></span>
                <span><i class="bi bi-upc"></i> <?php echo htmlspecialchars($libro['isbn'] ?? '—'); ?></span>
                <span><i class="bi bi-collection"></i> <?php echo $libro['ejemplares_disponibles']; ?> de <?php echo $libro['ejemplares_total']; ?> disponibles</span>
            </div>

            <p class="book-desc"><?php echo nl2br(htmlspecialchars($libro['descripcion'])); ?></p>

            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:20px;">
                <?php if ($libro['ejemplares_disponibles'] > 0) { ?>
                    <a href="../prestamos/form.php?id_libro=<?php echo $libro['id']; ?>" class="btn-custom btn-primary-custom">
                        <i class="bi bi-journal-arrow-up"></i> Prestar este libro
                    </a>
                <?php } else { ?>
                    <span class="badge-custom badge-danger" style="padding:12px 20px; font-size:0.9rem;">Sin ejemplares disponibles</span>
                <?php } ?>
                <a href="../index.php" class="btn-custom btn-back"><i class="bi bi-arrow-left"></i> Volver al catálogo</a>
                <a href="form.php?id=<?php echo $libro['id']; ?>" class="btn-custom btn-edit"><i class="bi bi-pencil"></i> Editar</a>
            </div>
        </div>
    </div>
</div>

<div class="card-custom">
    <h2 style="font-size:1.3rem; margin-bottom:15px;"><i class="bi bi-chat-square-text"></i> Reseñas</h2>

    <form method="POST" action="../resenas/guardar.php" style="background:#f8fafc; padding:20px; border-radius:12px; margin-bottom:20px;">
        <input type="hidden" name="id_libro" value="<?php echo $libro['id']; ?>">
        <div class="form-row">
            <label class="form-label">Tu nombre</label>
            <input type="text" name="nombre_lector" class="form-control-custom" required maxlength="100">
        </div>
        <div class="form-row">
            <label class="form-label">Tu puntuación</label>
            <div class="star-picker">
                <?php for ($i = 5; $i >= 1; $i--) { ?>
                    <input type="radio" name="estrellas" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" required>
                    <label for="star<?php echo $i; ?>">★</label>
                <?php } ?>
            </div>
        </div>
        <div class="form-row">
            <label class="form-label">Comentario</label>
            <textarea name="comentario" class="form-control-custom" rows="3" maxlength="500" placeholder="¿Qué te pareció el libro?"></textarea>
        </div>
        <button type="submit" class="btn-custom btn-primary-custom"><i class="bi bi-send"></i> Publicar reseña</button>
    </form>

    <?php if ($resenas->num_rows > 0) { ?>
        <div class="review-list">
            <?php while ($r = $resenas->fetch_assoc()) { ?>
                <div class="review-card">
                    <div class="review-header">
                        <span class="review-author"><?php echo htmlspecialchars($r['nombre_lector']); ?></span>
                        <span class="stars"><?php for ($i = 1; $i <= 5; $i++) echo $i <= $r['estrellas'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star stars-empty"></i>'; ?></span>
                    </div>
                    <div class="review-comment"><?php echo nl2br(htmlspecialchars($r['comentario'])); ?></div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                        <small style="color:#94a3b8;"><?php echo date('d/m/Y', strtotime($r['fecha'])); ?></small>
                        <a href="../resenas/eliminar.php?id=<?php echo $r['id']; ?>&libro=<?php echo $libro['id']; ?>"
                           onclick="return confirm('¿Eliminar esta reseña?');"
                           style="color:#ef4444; text-decoration:none; font-size:0.8rem;">
                           <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="empty-state" style="padding:30px;">
            <i class="bi bi-chat"></i>
            <p>Sé el primero en dejar una reseña</p>
        </div>
    <?php } ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>