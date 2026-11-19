<?php
require_once __DIR__ . '/config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
    if ($action === 'remove' && $id) {
        unset($_SESSION['cart'][$id]);
        set_flash('success', 'Produto removido do carrinho.');
    } elseif ($action === 'update' && is_array($_POST['quantities'] ?? null)) {
        foreach ($_POST['quantities'] as $productId => $quantity) {
            $productId = filter_var($productId, FILTER_VALIDATE_INT);
            $quantity = filter_var($quantity, FILTER_VALIDATE_INT);
            if (!$productId) continue;
            $stmt = $pdo->prepare("SELECT stock, is_active FROM products WHERE id=?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
            if (!$product || !(bool)$product['is_active'] || $quantity < 1) {
                unset($_SESSION['cart'][$productId]);
            } else {
                $_SESSION['cart'][$productId] = min($quantity, (int)$product['stock']);
                if ($_SESSION['cart'][$productId] < 1) unset($_SESSION['cart'][$productId]);
            }
        }
        set_flash('success', 'Carrinho atualizado.');
    } elseif ($action === 'add' && $id) {
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $stmt = $pdo->prepare("SELECT stock, is_active, name FROM products WHERE id=?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if (!$product || !(bool)$product['is_active'] || (int)$product['stock'] < 1) {
            set_flash('danger', 'Produto indisponível.');
        } else {
            $current = (int)($_SESSION['cart'][$id] ?? 0);
            $_SESSION['cart'][$id] = min($current + $quantity, (int)$product['stock']);
            set_flash('success', 'Produto adicionado ao carrinho!');
        }
    }
    redirect('carrinho.php');
}

$items = cart_items($pdo);
$total = cart_total($items);
$pageTitle = 'Meu carrinho';
require __DIR__ . '/includes/header.php';
?>
<div class="page-title"><span class="eyebrow">SUA SELEÇÃO</span><h1>Meu carrinho</h1><p>Confira seus produtos antes de continuar.</p></div>
<?php if (!$items): ?>
    <div class="empty-state"><i class="bi bi-bag"></i><h3>Seu carrinho está vazio</h3><p>Que tal encontrar algo para seu setup?</p><a href="index.php" class="btn btn-primary">Explorar produtos</a></div>
<?php else: ?>
<form method="post">
    <?= csrf_field() ?><input type="hidden" name="action" value="update">
    <div class="cart-layout"><div class="cart-list">
        <?php foreach ($items as $item): ?>
        <article class="cart-item">
            <img src="<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>">
            <div class="cart-item-info"><h3><?= e($item['name']) ?></h3><strong><?= money($item['price']) ?></strong><small>Estoque: <?= (int)$item['stock'] ?></small></div>
            <input class="form-control cart-qty" type="number" name="quantities[<?= (int)$item['id'] ?>]" min="1" max="<?= (int)$item['stock'] ?>" value="<?= (int)$item['quantity'] ?>" aria-label="Quantidade">
            <div class="cart-subtotal"><?= money($item['subtotal']) ?></div>
            <button class="icon-button danger" type="submit" form="remove-<?= (int)$item['id'] ?>" aria-label="Remover"><i class="bi bi-trash"></i></button>
        </article>
        <?php endforeach; ?>
        <button class="btn btn-outline-light mt-3">Atualizar carrinho</button>
    </div>
    <aside class="summary-card"><h2>Resumo do pedido</h2><div class="summary-line"><span>Subtotal</span><strong><?= money($total) ?></strong></div><div class="summary-line"><span>Frete</span><strong>Grátis</strong></div><hr><div class="summary-line total-line"><span>Total</span><strong><?= money($total) ?></strong></div><a href="checkout.php" class="btn btn-primary w-100 mt-3">Continuar para pagamento</a><a href="index.php" class="continue-link">Continuar comprando</a></aside></div>
</form>
<?php foreach ($items as $item): ?><form id="remove-<?= (int)$item['id'] ?>" method="post" class="d-none"><?= csrf_field() ?><input type="hidden" name="action" value="remove"><input type="hidden" name="product_id" value="<?= (int)$item['id'] ?>"></form><?php endforeach; ?>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
