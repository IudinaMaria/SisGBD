<?php

/**
 * Страница просмотра покупателей, оформивших заказ на конкретный товар.
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../db/db.php';
require_once __DIR__ . '/../templates/header.php';

$pdo = getPDO();

$id = $_GET['id'] ?? null;
if (!$id) die("ID товара не передан");

$productStmt = $pdo->prepare("SELECT * FROM furniture WHERE id = ?");
$productStmt->execute([$id]);
$product = $productStmt->fetch();

if (!$product) die("Товар не найден");

$stmt = $pdo->prepare("
    SELECT b.* FROM buyers b
    JOIN orders o ON b.id = o.buyer_id
    WHERE o.furniture_id = ?
");
$stmt->execute([$id]);
$buyers = $stmt->fetchAll();
?>

<div class="container mt-5">
  <div class="card shadow-sm p-4 mx-auto" style="max-width: 800px;">
    <h2 class="mb-4">Покупатели товара: <span class="text-primary"><?= htmlspecialchars($product['name']) ?></span></h2>

    <?php if (count($buyers) > 0): ?>
      <table class="table table-striped">
        <thead class="table-dark">
          <tr>
            <th>Имя</th>
            <th>Email</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($buyers as $b): ?>
            <tr>
              <td><?= htmlspecialchars($b['name']) ?></td>
              <td><?= htmlspecialchars($b['email']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-info text-center">На этот товар ещё никто не оформлял заказ.</div>
    <?php endif; ?>

    <div class="text-end mt-3">
      <a href="../index.php" class="btn btn-outline-secondary">← Назад к каталогу</a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>