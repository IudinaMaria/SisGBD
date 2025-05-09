<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../db/db.php';

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\ObjectId;

$mongo = new Client("mongodb://furniture_mongo:27017");
$collection = $mongo->furniture_app->comments;

$isAdmin = isAdmin();

$login = 'anon';
if (isset($_COOKIE['auth_token'])) {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT login FROM users WHERE token = ?");
    $stmt->execute([$_COOKIE['auth_token']]);
    $user = $stmt->fetch();
    if ($user) {
        $login = $user['login'];
    }
}

// Удаление комментария
if (isset($_GET['delete'])) {
    $id = new ObjectId($_GET['delete']);
    $comment = $collection->findOne(['_id' => $id]);

    if ($isAdmin || $comment['user'] === $login) {
        $collection->deleteOne(['_id' => $id]);
        header("Location: comments.php");
        exit;
    }
}

// Редактирование комментария
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'], $_POST['new_content'])) {
    $id = new ObjectId($_POST['edit_id']);
    $comment = $collection->findOne(['_id' => $id]);

    if ($comment && $comment['user'] === $login) {
        $collection->updateOne(
            ['_id' => $id],
            ['$set' => ['content' => trim($_POST['new_content'])]]
        );
        header("Location: comments.php");
        exit;
    }
}

// Добавление комментария
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $content = trim($_POST['content']);
    if ($content !== '') {
        $collection->insertOne([
            'user' => $login,
            'content' => $content,
            'created_at' => new UTCDateTime()
        ]);
        header("Location: comments.php");
        exit;
    }
}

require_once __DIR__ . '/../templates/header.php';
$comments = $collection->find([], ['sort' => ['created_at' => -1]]);
?>

<div class="container mt-5">
  <h1 class="mb-4">Отзывы</h1>

  <form method="post" class="mb-4">
    <div class="mb-3">
      <label class="form-label">Ваш комментарий</label>
      <textarea name="content" class="form-control" required></textarea>
    </div>
    <button class="btn btn-primary">Оставить отзыв</button>
  </form>

  <ul class="list-group">
    <?php foreach ($comments as $comment): ?>
      <li class="list-group-item">
        <strong><?= htmlspecialchars($comment['user']) ?>:</strong>
        <?php if (isset($_GET['edit']) && $_GET['edit'] === (string)$comment['_id'] && $comment['user'] === $login): ?>
          <form method="post" class="d-inline">
            <input type="hidden" name="edit_id" value="<?= $comment['_id'] ?>">
            <input type="text" name="new_content" value="<?= htmlspecialchars($comment['content']) ?>" class="form-control d-inline w-50">
            <button class="btn btn-sm btn-success">Сохранить</button>
          </form>
        <?php else: ?>
          <?= htmlspecialchars($comment['content']) ?>
        <?php endif; ?>
        <?php if ($isAdmin || $comment['user'] === $login): ?>
          <div class="float-end">
            <a href="comments.php?delete=<?= $comment['_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить комментарий?')">Удалить</a>
            <?php if ($comment['user'] === $login): ?>
              <a href="comments.php?edit=<?= $comment['_id'] ?>" class="btn btn-sm btn-warning">Редактировать</a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
