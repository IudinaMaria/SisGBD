<?php

/**
 * Страница редактирования мебели (доступ только для администратора).
 *
 */

require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/log_action.php';

$pdo = getPDO();

if (!isAdmin()) {
  die("Доступ запрещён: только администратор может редактировать каталог.");
}

$id = $_GET['id'] ?? null;
if (!$id) die('Не передан ID товара');

$stmt = $pdo->prepare("SELECT * FROM furniture WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) die('Товар не найден');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image = trim($_POST['image']);

    $upd = $pdo->prepare("UPDATE furniture SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
    $upd->execute([$name, $description, $price, $image, $id]);

    logAction("Обновлён товар ID $id: $name");
    header("Location: ../index.php");
    exit;
}
?>

<?php require_once __DIR__ . '/../templates/header.php'; ?>
<h1>Редактировать мебель</h1>

<form method="post" class="card p-4">
  <div class="mb-3">
    <label class="form-label">Название</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Описание</label>
    <textarea name="description" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label">Цена</label>
    <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($product['price']) ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Ссылка на изображение</label>
    <input type="text" name="image" class="form-control" value="<?= htmlspecialchars($product['image']) ?>">
  </div>
  <button class="btn btn-primary">Сохранить</button>
  <a href="../index.php" class="btn btn-secondary">Отмена</a>
</form>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
