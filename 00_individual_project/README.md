# Furniture Store — PHP + MySQL + MongoDB + Docker Web App

Веб-приложение для управления мебелью, заказами, отзывами и пользователями.

**Цель проекта** — реализовать систему с полным CRUD-функционалом, логированием и ролевым управлением доступом, использующую две базы данных:
- **MySQL** — для хранения основной информации (пользователи, мебель, заказы)
- **MongoDB** — для хранения отзывов пользователей.<br>
<br>
Приложение полностью развернуто в Docker-среде и включает:
- Apache + PHP
- MySQL + phpMyAdmin
- MongoDB + Mongo Express

---

## 🚀 Инструкции по запуску проекта

1. Убедитесь, что установлен Docker + Docker Compose
2. Клонируйте/скачайте проект в любую папку (например `C:/SisGBD/...`)
3. Создайте структуру директорий, если не создана:
```bash
mkdir -p docker/apache
```
4. Создайте/проверьте файлы:
- `docker-compose.yml`
- `docker/apache/Dockerfile`
- `docker/apache/apache-config.conf`
5. Запустите проект:
```bash
docker-compose up -d --build
```
6. Импортируйте MySQL-базу данных (если нужно):
```bash
Get-Content furniture_store.sql | docker exec -i furniture_mysql mysql -uroot -proot furniture_store
```
7. Перейдите в браузере на:
- Приложение: `http://localhost`
- phpMyAdmin: `http://localhost:8080`
- Mongo Express: `http://localhost:8081`<br>
 **Логин:** admin  <br>
 **Пароль:** admin

---

## 📁 Структура проекта и основные страницы
- `index.php` — каталог мебели (MySQL)
- `pages/buyers.php` — управление покупателями (MySQL)
- `pages/orders.php` — оформление заказов (MySQL)
- `pages/logs.php` — журнал действий (MySQL)
- `pages/comments.php` — отзывы (MongoDB)
- `login.php / register.php` — авторизация (MySQL)
- `includes/, db/, templates/` — логика, подключение и оформление

---

## 🧩 Функциональность

| Возможность                                 | Admin   | User |
| ------------------------------------------- | :---:   | :--: |
| Просмотр каталога                           |   ✅   |   ✅ |
| Добавление, редактирование, удаление мебели |   ✅   |   ❌ | |
| ПУправление покупателями                    |   ✅   |   ❌ | |
| Оформление заказов                          |   ✅   |   ✅ | |
| Просмотр логов                              |   ✅   |   ❌ | |
| Отзывы: добавление, удаление своих          |   ✅   |   ✅ | |
| Отзывы: удаление любых                      |   ✅   |   ❌ |

---

## 💬 Работа с отзывами (MongoDB)

MongoDB используется для хранения отзывов. Каждый пользователь может:
- оставить комментарий
- редактировать и удалять свои отзывы
- администратор может удалять любые

Используется библиотека `mongodb/mongodb` и расширение `mongodb.so`.

### Структура MongoDB (коллекция furniture_app.comments):
```javascript
{
  _id: ObjectId,
  user: string,       
  content: string,    
  created_at: datetime
}
```

---

## 🗄️ Работа с основной базой данных (MySQL)

### Хранит всю бизнес-логику приложения:
- таблицы пользователей, товаров, заказов, покупателей
- все операции проходят через PDO с защитой от SQL-инъекций
- таблица `actions_log` хранит системные события

### Таблицы:
- `users(id, login, password, role, token)`
- `furniture(id, name, description, price, image)`
- `buyers(id, name, email)`
- `orders(buyer_id, furniture_id)`
- `actions_log(id, action, created_at)`

---

## 🔐 Сценарии взаимодействия

### 👨‍💼 Администратор:
- Управляет товарами и заказами
- Видит покупателей и действия
- Может редактировать/удалять отзывы

### 👤 Пользователь:
- Оформляет заказы
- Пишет и редактирует отзывы
- Просматривает каталог

---

## 💬 Примеры использования (ключевые фрагменты кода)

### Подключение MySQL (PDO)

```php
$pdo = new PDO("mysql:host=mysql;dbname=furniture_store;charset=utf8", "root", "root");
```

### Подключение MongoDB (через библиотеку `mongodb/mongodb`)

```php
require_once __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client;
$mongo = new Client("mongodb://furniture_mongo:27017");
$collection = $mongo->furniture_app->comments;
```

### Аутентификация (auth.php)

```php
    require_once __DIR__ . '/../db/db.php';

    $pdo = getPDO();
    $token = $_COOKIE['auth_token'] ?? null;

    if (!$token) {
        header('Location: /login.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
       setcookie('auth_token', '', time() - 3600, '/');
     header('Location: /login.php');
      exit;
    }

    function isAdmin(): bool {
       global $user;
      return $user['role'] === 'admin';
    }
```
### Добавление мебели (actions/add_furniture.php)

```php
    require_once __DIR__ . '/../includes/auth.php';
    if (!isAdmin()) {
       die("Доступ запрещён");
    }

    $pdo = getPDO();
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $image = trim($_POST['image']);

    $stmt = $pdo->prepare("INSERT INTO furniture (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $description, $price, $image]);

    header("Location: ../index.php");
    exit;
```

### Ограничение прав доступа в buyers.php

```php
    require_once __DIR__ . '/../includes/auth.php';

    if (!isAdmin()) {
     header('Location: ../index.php');
      exit;
    }
```
### Добавление записи в журнал (includes/log_action.php)

```php
    require_once __DIR__ . '/../db/db.php';

    function logAction(string $message): void {
        $pdo = getPDO();
     $stmt = $pdo->prepare("INSERT INTO actions_log (action)    VALUES (?)");
      $stmt->execute([$message]);
    }
```

### Генерация токена при входе (login.php)

```php
    if ($user && password_verify($password, $user['password'])) {
     $token = bin2hex(random_bytes(32));
     setcookie('auth_token', $token, time() + 86400 * 7, '/');

      $stmt = $pdo->prepare("UPDATE users SET token = ? WHERE id = ?");
      $stmt->execute([$token, $user['id']]);

      header('Location: index.php');
     exit;
    }
```

---

## 🔐 Сценарии взаимодействия
**👨‍💼 Администратор:**
- Входит в систему
- Управляет товарами
- Управляет покупателями
- Просматривает логи
- Может редактировать/удалять отзывы

**👤 Обычный пользователь:**
- Входит в систему
- Просматривает каталог
- Добавляет заказ
- Пишет и редактирует отзывы

---

##  🔐 Доступ в систему (тестовые данные)

**👨‍💼 Администратор:**
- **Логин:** admin123  
- **Пароль:** 123

**👤 Обычный пользователь:**
- **Логин:** user123  
- **Пароль:** 1234

---

##  🔐 Структура баз данных

### MySQL `users`
| Поле     | Тип          |
| -------- | ------------ |
| id       | INT, PK, AI  |
| login    | VARCHAR(255) |
| password | VARCHAR(255) |
| role     | VARCHAR(50)  |
| token    | VARCHAR(255) |

### MySQL `furniture`
| Поле        | Тип           |
| ----------- | ------------- |
| id          | INT, PK, AI   |
| name        | VARCHAR(255)  |
| description | TEXT          |
| price       | DECIMAL(10,2) |
| image       | VARCHAR(255)  |

### MySQL `buyers`
| Поле        | Тип           |
| ----------- | ------------- |
| id          | INT, PK, AI   |
| name        | VARCHAR(255)  |
| description | TEXT          |
| price       | DECIMAL(10,2) |
| image       | VARCHAR(255)  |

### MySQL `actions_log`
| Поле        | Тип         |
| ----------- | ----------- |
| id          | INT, PK, AI |
| action      | TEXT        |
| created\_at | DATETIME    |

### Коллекция MongoDB `comments`
| Поле        | Тип         |
| ----------- | ----------- |
| _id         | ObjectId    |
| user        | string      |
| content     | string      |
| created_at  | UTCDateTime |

---

## 🧭 Примеры использования
- Каталог с возможностью просмотра пользователей, редактирования и удаления (админ):
![Скриншот интерфейса](img_for_report/Screenshot_17.png)<br>
- Каталог с возможностью просмотра пользователей, редактирования и удаления (пользователь):
![Скриншот интерфейса](img_for_report/Screenshot_10.png)<br>
- Форма добавления товара с валидацией полей (админ):
![Скриншот интерфейса](img_for_report/Screenshot_1.png)<br>
![Скриншот интерфейса](img_for_report/Screenshot_2.png)<br>
- Просмотр, поиск и добавление покупателей так же с валидацией полей (админ):
![Скриншот интерфейса](img_for_report/Screenshot_3.png)<br>
![Скриншот интерфейса](img_for_report/Screenshot_4.png)<br>
- Добавление заказа, удалением и поиском (админ):
![Скриншот интерфейса](img_for_report/Screenshot_5.png)<br>
- Просмотр логов (админ):
![Скриншот интерфейса](img_for_report/Screenshot_6.png)<br>
- Поиск по приложению:
![Скриншот интерфейса](img_for_report/Screenshot_7.png)<br>
- Вход в систему:
![Скриншот интерфейса](img_for_report/Screenshot_8.png)<br>
- Регистрация:
![Скриншот интерфейса](img_for_report/Screenshot_9.png)<br>
- Ограничение прав для пользователя:
![Скриншот интерфейса](img_for_report/Screenshot_11.png)<br>

---

## 🧭 Используемые технологии
- PHP 8.2 + Apache
- MySQL 5.7
- MongoDB + mongo-express
- phpMyAdmin
- Bootstrap 5
- Docker + Docker Compose

---

## 🔗 Использованные источники
- [PHP Manual](https://www.php.net/manual/ru/)
- [Bootstrap](https://getbootstrap.com/)
- [w3schools](https://www.w3schools.com/php/)
- [MongoDB PHP](https://www.mongodb.com/docs/php-library/current/)
- [ChatGPT](https://chatgpt.com/))