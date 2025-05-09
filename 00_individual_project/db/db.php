<?php

/**
 * Получает экземпляр PDO для подключения к базе данных furniture_store.
 *
 * @return PDO Подключение к базе данных с установленными атрибутами
 */
function getPDO(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        // Используем имя сервиса из docker-compose: "mysql"
        $pdo = new PDO("mysql:host=mysql;dbname=furniture_store;charset=utf8", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return $pdo;
}
