<?php

/**
 * Страница управления покупателями (только для администратора).
 *
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db/db.php';

if (!isAdmin()) {
  header('Location: ../index.php');
  exit;
}

$pdo = getPDO();

$query = $_GET['q'] ?? '';
$buyers = [];

if ($query !== '') {
    $stmt = $pdo->prepare("SELECT * FROM buyers WHERE name LIKE ?");
    $stmt->execute(["%$query%"]);
    $buyers = $stmt->fetchAll();
} else {
    $stmt = $pdo->query("SELECT * FROM buyers");
    $buyers = $stmt->fetchAll();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || $email === '') {
        $error = 'Пожалуйста, заполните все поля.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO buyers (name, email) VALUES (?, ?)");
        $stmt->execute([$name, $email]);
        header('Location: buyers.php');
        exit;
    }
}
?>

<?php include __DIR__ . '/../templates/header.php'; ?>

<div class="container mt-4">
  <h2 class="mb-3">Список покупателей</h2>

  <!-- Поиск -->
  <form method="get" class="mb-3 d-flex" role="search">
    <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" class="form-control me-2" placeholder="Поиск по имени...">
    <button class="btn btn-outline-primary">Найти</button>
    <?php if ($query): ?>
      <a href="buyers.php" class="btn btn-outline-secondary ms-2">Сброс</a>
    <?php endif; ?>
  </form>

  <!-- Таблица -->
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Имя</th>
        <th>Email</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($buyers as $buyer): ?>
        <tr>
          <td><?= $buyer['id'] ?></td>
          <td><?= htmlspecialchars($buyer['name']) ?></td>
          <td><?= htmlspecialchars($buyer['email']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php if (isAdmin()): ?>
  <h4 class="mt-5">Добавить покупателя</h4>
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>
  <form method="post" class="mt-3">
    <div class="mb-3">
      <label class="form-label">Имя</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <button class="btn btn-success">Добавить</button>
  </form>
<?php endif; ?>


<?php include __DIR__ . '/../templates/footer.php'; ?>
