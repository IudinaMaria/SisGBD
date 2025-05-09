<?php

/**
 * Страница просмотра журнала действий пользователей (только для администратора).
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../templates/header.php';

if (!isAdmin()) {
    http_response_code(403);
    echo "<div class='alert alert-danger text-center mt-4'>🚫 У вас нет прав на просмотр журнала действий.</div>";
    require_once __DIR__ . '/../templates/footer.php';
    exit;
}

require_once __DIR__ . '/../db/db.php';

$pdo = getPDO();
$logs = $pdo->query("SELECT * FROM actions_log ORDER BY created_at DESC")->fetchAll();
?>

<h1>Журнал действий</h1>

<table class="table table-bordered bg-white">
    <thead>
        <tr>
            <th>Дата</th>
            <th>Событие</th>
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

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
