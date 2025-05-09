<?php

/**
 * Страница редактирования информации о покупателе (только для администратора).
 *
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../log_action.php';

$pdo = getPDO();

$id = $_GET['id'] ?? null;
if (!$id) die('Не передан ID покупателя');

$stmt = $pdo->prepare("SELECT * FROM buyers WHERE id = ?");
$stmt->execute([$id]);
$buyer = $stmt->fetch();

if (!$buyer) die('Покупатель не найден');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $upd = $pdo->prepare("UPDATE buyers SET name = ?, email = ? WHERE id = ?");
    $upd->execute([$name, $email, $id]);

    logAction("Обновлён покупатель ID $id: $name");
    header("Location: ../pages/buyers.php");
    exit;
}
?>

<?php require_once __DIR__ . '/../templates/header.php'; ?>
<h1>Редактировать покупателя</h1>
<form method="post" class="card p-4">
  <div class="mb-3">
    <label class="form-label">Имя</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($buyer['name']) ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($buyer['email']) ?>" required>
  </div>
  <button class="btn btn-primary">Сохранить</button>
  <a href="../pages/buyers.php" class="btn btn-secondary">Отмена</a>
</form>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
