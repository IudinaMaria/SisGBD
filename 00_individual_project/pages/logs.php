<?php

/**
 * Страница просмотра журнала действий пользователей (только для администратора).
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../templates/header.php';

if (!isAdmin()) {
  http_response_code(403);
  echo "<div class='alert alert-danger text-center mt-5'>🚫 У вас нет прав на просмотр журнала действий.</div>";
  require_once __DIR__ . '/../templates/footer.php';
  exit;
}

require_once __DIR__ . '/../db/db.php';

$pdo = getPDO();
$logs = $pdo->query("SELECT * FROM actions_log ORDER BY created_at DESC")->fetchAll();
?>

<div class="container mt-5">
  <div class="card shadow-sm p-4">
    <h2 class="mb-4"><i class="fas fa-clipboard-list"></i> Журнал действий</h2>

    <?php if (count($logs) > 0): ?>
      <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
          <tr>
            <th style="width: 200px;">🕒 Дата</th>
            <th>📋 Событие</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($logs as $log): ?>
            <tr>
              <td><?= $log['created_at'] ?></td>
              <td><?= htmlspecialchars($log['action']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-info text-center">Журнал действий пуст.</div>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>