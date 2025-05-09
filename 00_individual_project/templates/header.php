<?php
$current = basename($_SERVER['SCRIPT_NAME']);
$base = str_contains($_SERVER['SCRIPT_NAME'], '/pages/') ? '../' : '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Мебельный Менеджер</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <script src="https://kit.fontawesome.com/a2d9d5fcde.js" crossorigin="anonymous"></script>
  
  <link rel="stylesheet" href="<?= $base ?>style.css">
</head>
<body class="fade-in-page">


<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= $base ?>index.php">
      <i class="fas fa-couch"></i> МЕБЕЛЬ-МЕНЕДЖЕР
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse show" id="mainNavbar">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $current === 'index.php' ? 'active fw-bold text-warning' : '' ?>" href="<?= $base ?>index.php">
            <i class="fas fa-th-large"></i> Каталог
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current === 'buyers.php' ? 'active fw-bold text-warning' : '' ?>" href="<?= $base ?>pages/buyers.php">
            <i class="fas fa-users"></i> Покупатели
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current === 'orders.php' ? 'active fw-bold text-warning' : '' ?>" href="<?= $base ?>pages/orders.php">
            <i class="fas fa-cart-arrow-down"></i> Заказы
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current === 'logs.php' ? 'active fw-bold text-warning' : '' ?>" href="<?= $base ?>pages/logs.php">
            <i class="fas fa-clipboard-list"></i> Логи
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger" href="<?= $base ?>logout.php">
            <i class="fas fa-sign-out-alt"></i> Выход
          </a>
        </li>
      </ul>

      <form action="<?= $base ?>pages/buyers.php" method="get" class="d-flex ms-3" role="search">
        <input class="form-control me-2" type="search" name="q" placeholder="Поиск покупателей..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" aria-label="Поиск">
        <button class="btn btn-outline-light" type="submit">Найти</button>
      </form>
    </div>
  </div>
</nav>

<div class="container py-4">
