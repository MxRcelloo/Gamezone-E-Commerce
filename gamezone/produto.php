<?php
require_once __DIR__ . '/config/bootstrap.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE p.id=? AND p.is_active=1");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { http_response_code(404); set_flash('warning', 'Produto não encontrado.'); redirect('index.php'); }
$pageTitle = $product['name'];
require __DIR__ . '/includes/header.php';
?>
<div class="breadcrumb-line"><a href="index.php">Início</a> / <?= e($product['category_name'] ?? 'Produto') ?> / <?= e($product['name']) ?></div>
<section class="product-detail">
    <div class="detail-image"><img src="<?= e($product['image'] ?: 'https://placehold.co/800x650/171522/ffffff?text=GameZone') ?>" alt="<?= e($product['name']) ?>"></div>
    <div class="detail-copy">
        <span class="eyebrow"><?= e($product['category_name'] ?? 'GAMEZONE') ?></span>
        <h1><?= e($product['name']) ?></h1>
        <div class="detail-price"><?= money($product['price']) ?></div>
        <p><?= nl2br(e($product['description'] ?? 'Produto gamer selecionado pela GameZone.')) ?></p>
        <p class="<?= (int)$product['stock'] > 0 ? 'stock-ok' : 'stock-out' ?>"><i class="bi bi-box-seam"></i> <?= (int)$product['stock'] > 0 ? 'Disponível · ' . (int)$product['stock'] . ' unidade(s)' : 'Produto sem estoque' ?></p>
        <?php if ((int)$product['stock'] > 0): ?>
        <form action="carrinho.php" method="post" class="add-cart-form">
            <?= csrf_field() ?><input type="hidden" name="action" value="add"><input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
            <label for="quantity">Quantidade</label><input id="quantity" class="form-control quantity-input" type="number" name="quantity" min="1" max="<?= (int)$product['stock'] ?>" value="1">
            <button class="btn btn-primary btn-lg">Adicionar ao carrinho <i class="bi bi-bag-plus"></i></button>
        </form>
        <?php endif; ?>
        <div class="detail-perks"><span><i class="bi bi-shield-check"></i> Compra segura (simulada)</span><span><i class="bi bi-lightning-charge"></i> Confirmação imediata</span></div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
