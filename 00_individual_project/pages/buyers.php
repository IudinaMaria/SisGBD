<?php

/**
 * Страница управления покупателями.
 * Видна только администратору. Остальным — сообщение "нет доступа".
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db/db.php';

$isAdmin = isAdmin();
$pdo = getPDO();

$query = $_GET['q'] ?? '';
$buyers = [];

if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $deleteId = (int)$_POST['delete_id'];
  $stmt = $pdo->prepare("DELETE FROM buyers WHERE id = ?");
  $stmt->execute([$deleteId]);
  header('Location: buyers.php');
  exit;
}

$error = '';
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['email'])) {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);

  if ($name === '' || $email === '') {
    $error = 'Пожалуйста, заполните все поля.';
  } else {
    $stmt = $pdo->prepare("INSERT INTO buyers (name, email) VALUES (?, ?)");
    $stmt->execute([$name, $email]);
    header('Location: buyers.php');
    exit;
  }
}

if ($isAdmin) {
  if ($query !== '') {
    $stmt = $pdo->prepare("SELECT * FROM buyers WHERE name LIKE ?");
    $stmt->execute(["%$query%"]);
    $buyers = $stmt->fetchAll();
  } else {
    $buyers = $pdo->query("SELECT * FROM buyers")->fetchAll();
  }
}
?>

<?php include __DIR__ . '/../templates/header.php'; ?>

<div class="container mt-4">
  <h2 class="mb-4 text-center text-secondary">Покупатели</h2>

  <?php if (!$isAdmin): ?>
    <div class="alert alert-danger text-center shadow-sm rounded">
      🔒 У вас нет прав для просмотра этого раздела.
    </div>
  <?php else: ?>

    <form method="get" class="mb-4 d-flex justify-content-center" role="search">
      <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" class="form-control w-50 me-2" placeholder="🔍 Поиск по имени...">
      <button class="btn btn-outline-primary shadow-sm">Найти</button>
      <?php if ($query): ?>
        <a href="buyers.php" class="btn btn-outline-secondary ms-2 shadow-sm">Сброс</a>
      <?php endif; ?>
    </form>

    <div class="table-responsive bg-light p-3 rounded shadow-sm">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Действие</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($buyers as $buyer): ?>
            <tr>
              <td><?= $buyer['id'] ?></td>
              <td><?= htmlspecialchars($buyer['name']) ?></td>
              <td><?= htmlspecialchars($buyer['email']) ?></td>
              <td>
                <form method="post" onsubmit="return confirm('Удалить этого покупателя?');" style="display:inline;">
                  <input type="hidden" name="delete_id" value="<?= $buyer['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-outline-danger rounded">Удалить</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <h4 class="mt-5 text-secondary">➕ Добавить покупателя</h4>
    <?php if ($error): ?>
      <div class="alert alert-danger shadow-sm"><?= $error ?></div>
    <?php endif; ?>
    <form method="post" class="mt-3 p-3 bg-light rounded shadow-sm">
      <div class="mb-3">
        <label class="form-label">Имя</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <button class="btn btn-success shadow-sm">Сохранить</button>
    </form>

  <?php endif; ?>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>