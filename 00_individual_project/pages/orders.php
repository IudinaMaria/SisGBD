<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../includes/log_action.php';

$isAdmin = isAdmin();
$pdo = getPDO();

if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $buyer_id = (int)($_POST['buyer_id'] ?? 0);
    $furniture_id = (int)($_POST['furniture_id'] ?? 0);

    if ($buyer_id && $furniture_id) {
        $stmt = $pdo->prepare("DELETE FROM orders WHERE buyer_id = ? AND furniture_id = ?");
        $stmt->execute([$buyer_id, $furniture_id]);
        logAction("Удалён заказ: Покупатель #$buyer_id → Товар #$furniture_id");
        header("Location: orders.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
    $buyer_id = (int)($_POST['buyer_id'] ?? 0);
    $furniture_id = (int)($_POST['furniture_id'] ?? 0);

    if ($buyer_id && $furniture_id) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO orders (buyer_id, furniture_id) VALUES (?, ?)");
        $stmt->execute([$buyer_id, $furniture_id]);
        logAction("Добавлен заказ: Покупатель #$buyer_id → Товар #$furniture_id");
        header("Location: orders.php");
        exit;
    }
}

require_once __DIR__ . '/../templates/header.php';

$buyers = $pdo->query("SELECT * FROM buyers")->fetchAll();
$products = $pdo->query("SELECT * FROM furniture")->fetchAll();
?>

<div class="container mt-4">
  <div class="card shadow-sm p-4">
    <h2 class="mb-4">📝 Оформить заказ</h2>

    <form method="post" class="row g-3 mb-4">
      <div class="col-md-5">
        <label class="form-label">Покупатель</label>
        <select name="buyer_id" class="form-select" required>
          <option value="">Выберите покупателя</option>
          <?php foreach ($buyers as $b): ?>
            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-5">
        <label class="form-label">Товар</label>
        <select name="furniture_id" class="form-select" required>
          <option value="">Выберите товар</option>
          <?php foreach ($products as $p): ?>
            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" name="order" class="btn btn-success w-100">Оформить</button>
      </div>
    </form>

    <h4 class="mb-3">📦 Список заказов</h4>

    <?php if (!$isAdmin): ?>
      <div class="alert alert-danger text-center">
        🔒 У вас нет прав для просмотра заказов.
      </div>
    <?php else: ?>
      <?php
        $search = trim($_GET['search'] ?? '');
        $query = "
          SELECT o.buyer_id, o.furniture_id, b.name AS buyer, f.name AS product
          FROM orders o
          JOIN buyers b ON o.buyer_id = b.id
          JOIN furniture f ON o.furniture_id = f.id
        ";

        if ($search !== '') {
            $stmt = $pdo->prepare($query . " WHERE b.name LIKE ?");
            $stmt->execute(['%' . $search . '%']);
            $orders = $stmt->fetchAll();
        } else {
            $orders = $pdo->query($query)->fetchAll();
        }
      ?>

      <form method="get" class="mb-3 d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Поиск по имени покупателя" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-outline-primary">Найти</button>
        <a href="orders.php" class="btn btn-outline-secondary">Сброс</a>
      </form>

      <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>👤 Покупатель</th>
            <th>🪑 Товар</th>
            <th style="width: 140px;">Действие</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td><?= htmlspecialchars($o['buyer']) ?></td>
              <td><?= htmlspecialchars($o['product']) ?></td>
              <td>
                <form method="post" class="d-inline" onsubmit="return confirm('Удалить этот заказ?');">
                  <input type="hidden" name="buyer_id" value="<?= $o['buyer_id'] ?>">
                  <input type="hidden" name="furniture_id" value="<?= $o['furniture_id'] ?>">
                  <button type="submit" name="delete" class="btn btn-sm btn-danger">Удалить</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
