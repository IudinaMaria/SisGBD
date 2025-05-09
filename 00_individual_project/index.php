<?php require_once __DIR__ . "/includes/auth.php"; ?>
<?php require_once __DIR__ . "/templates/header.php"; ?>
<?php require_once __DIR__ . "/db/db.php";

$pdo = getPDO();
$products = $pdo->query("SELECT * FROM furniture ORDER BY id DESC")->fetchAll();
?>

<h1 class="mb-4">Каталог мебели</h1>

<div class="row row-cols-1 row-cols-md-2 g-4">
    <?php foreach ($products as $index => $product): ?>
        <div class="col">
            <div class="card h-100 shadow-sm fade-in" style="animation-delay: <?= $index * 0.1 ?>s;">
                <div class="card-body">
                    <p class='card-text mb-2'><strong>ID:</strong> <?= $product['id'] ?></p>
                    <p class='card-text mb-2'><strong>Название:</strong> <?= htmlspecialchars($product['name']) ?></p>
                    <p class='card-text mb-2'><strong>Описание:</strong> <?= htmlspecialchars($product['description']) ?></p>
                    <p class='card-text mb-2'><strong>Цена:</strong> $<?= number_format($product['price'], 2) ?></p>
                    <p class='card-text'>
    <a href="pages/furniture_buyers.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-info">👥 Покупатели</a>

    <?php if (isAdmin()): ?>
        <a href="pages/edit_furniture.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-secondary ms-2">✏️ Редактировать</a>
        <a href="actions/delete_furniture.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger ms-2" onclick="return confirm('Удалить этот товар?')">🗑️ Удалить</a>
    <?php endif; ?>
</p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (isAdmin()): ?>
  <h2 class="mt-5">Добавить мебель</h2>
  <form method="post" action="actions/add_furniture.php">
    <div class="mb-3">
      <label class="form-label">Название</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Описание</label>
      <textarea name="description" class="form-control"></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Цена</label>
      <input type="number" name="price" step="0.01" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Ссылка на изображение</label>
      <input type="text" name="image" class="form-control">
    </div>
    <button class="btn btn-primary">Добавить</button>
  </form>
<?php endif; ?>


<?php require_once __DIR__ . "/templates/footer.php"; ?>
