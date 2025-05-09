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
    <h2 class="mb-4">Статистика заказов</h2>

    <h4 class="mt-4">🔥 Самые популярные товары</h4>
    <ul>
        <?php foreach ($topProducts as $row): ?>
            <li><?= htmlspecialchars($row['name']) ?> — <?= $row['count'] ?> заказ(ов)</li>
        <?php endforeach; ?>
    </ul>

    <h4 class="mt-4">🏆 Топ покупателей</h4>
    <ul>
        <?php foreach ($topBuyers as $row): ?>
            <li><?= htmlspecialchars($row['name']) ?> — <?= $row['count'] ?> заказ(ов)</li>
        <?php endforeach; ?>
    </ul>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>