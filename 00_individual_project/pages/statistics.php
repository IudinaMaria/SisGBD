<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../templates/header.php';

$pdo = getPDO();

$topProducts = $pdo->query(<<<SQL
    SELECT f.name, COUNT(*) AS count
    FROM orders o
    JOIN furniture f ON o.furniture_id = f.id
    GROUP BY o.furniture_id
    ORDER BY count DESC
    LIMIT 5
SQL)->fetchAll();

$topBuyers = $pdo->query(<<<SQL
    SELECT b.name, COUNT(*) AS count
    FROM orders o
    JOIN buyers b ON o.buyer_id = b.id
    GROUP BY o.buyer_id
    ORDER BY count DESC
    LIMIT 5
SQL)->fetchAll();
?>

<div class="container mt-4">
  <div class="card shadow-sm p-4">
    <h2 class="mb-4 text-center">📊 Статистика заказов</h2>

    <div class="mb-4">
      <h5 class="text-primary">🔥 Самые популярные товары</h5>
      <ul class="list-group list-group-flush">
        <?php foreach ($topProducts as $row): ?>
          <li class="list-group-item d-flex justify-content-between">
            <span><?= htmlspecialchars($row['name']) ?></span>
            <span class="badge bg-success rounded-pill"><?= $row['count'] ?> заказ(ов)</span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div>
      <h5 class="text-primary">🏆 Топ покупателей</h5>
      <ul class="list-group list-group-flush">
        <?php foreach ($topBuyers as $row): ?>
          <li class="list-group-item d-flex justify-content-between">
            <span><?= htmlspecialchars($row['name']) ?></span>
            <span class="badge bg-info rounded-pill"><?= $row['count'] ?> заказ(ов)</span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>