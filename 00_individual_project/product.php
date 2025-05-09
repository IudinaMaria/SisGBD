<?php

define('PROJECT_NAME', 'Furniture Store');

require_once './data/furniture_products.php';

/**
 * Получение ID товара из GET-параметров и преобразование в целое число
 * @var int $id Идентификатор товара
 */
$id = intval($_GET['id']) - 1;

/**
 * Получение информации о товаре по ID
 * @var array|null $product Найденный товар или null, если не найден
 */
$product = $products[$id] ?? null;

if (!$product) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

require_once './components/header.php';
?>

<article class="container mx-auto flex flex-col gap-8 mt-8">
    <div>
        <?php foreach ($product['categories'] as $category) : ?>
            <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2">
                <?php echo htmlspecialchars($category); ?>
            </span>
        <?php endforeach; ?>
    </div>
    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-100 h-100 object-cover rounded shadow-md">
    <h1 class="font-bold text-4xl font-mono"> <?php echo htmlspecialchars($product['name']); ?> </h1>
    <p class="text-lg"> <?php echo htmlspecialchars($product['description']); ?> </p>
    <p class="text-lg text-gray-800 font-bold"> <?php echo '$' . number_format($product['price'], 2); ?> </p>
    <a class="text-blue-700 transition duration-300 ease-in-out transform hover:translate-x-1 hover:text-blue-900" href="/">← Back to products</a>
</article>

<?php
require_once './components/footer.php';
?>