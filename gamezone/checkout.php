<?php
require_once __DIR__ . '/config/bootstrap.php';
require_login();
$items = cart_items($pdo);
if (!$items) { set_flash('warning', 'Seu carrinho está vazio.'); redirect('carrinho.php'); }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $method = $_POST['payment_method'] ?? '';
    if (!in_array($method, ['PIX', 'Boleto', 'Cartão'], true)) $errors[] = 'Selecione um método de pagamento válido.';
    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $lockedItems = [];
            $total = 0.0;
            foreach ($_SESSION['cart'] as $productId => $quantity) {
                $stmt = $pdo->prepare("SELECT id,name,price,stock,is_active FROM products WHERE id=? FOR UPDATE");
                $stmt->execute([(int)$productId]);
                $product = $stmt->fetch();
                $quantity = (int)$quantity;
                if (!$product || !(bool)$product['is_active'] || $quantity < 1 || (int)$product['stock'] < $quantity) {
                    throw new RuntimeException('Estoque insuficiente ou produto indisponível: ' . ($product['name'] ?? 'item'));
                }
                $lockedItems[] = ['product' => $product, 'quantity' => $quantity];
                $total += (float)$product['price'] * $quantity;
            }
            $stmt = $pdo->prepare("INSERT INTO orders (user_id,total,payment_method,status) VALUES (?,?,?,'confirmed')");
            $stmt->execute([(int)current_user()['id'], $total, $method]);
            $orderId = (int)$pdo->lastInsertId();
            foreach ($lockedItems as $entry) {
                $product = $entry['product']; $quantity = $entry['quantity'];
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id,product_id,quantity,unit_price) VALUES (?,?,?,?)");
                $stmt->execute([$orderId, (int)$product['id'], $quantity, (float)$product['price']]);
                $stmt = $pdo->prepare("UPDATE products SET stock=stock-? WHERE id=? AND stock>=?");
                $stmt->execute([$quantity, (int)$product['id'], $quantity]);
                if ($stmt->rowCount() !== 1) throw new RuntimeException('Não foi possível atualizar o estoque.');
            }
            $pdo->commit();
            $_SESSION['cart'] = [];
            redirect('pedido-confirmado.php?id=' . $orderId);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log($e->getMessage());
            $errors[] = $e instanceof RuntimeException ? $e->getMessage() : 'Não foi possível concluir o pedido. Tente novamente.';
        }
    }
}
$items = cart_items($pdo); $total = cart_total($items);
$pageTitle = 'Checkout';
require __DIR__ . '/includes/header.php';
?>
<div class="page-title"><span class="eyebrow">QUASE LÁ</span><h1>Finalizar compra</h1><p>Pagamento demonstrativo: nenhuma cobrança real será feita.</p></div>
<?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
<div class="cart-layout"><section class="panel-card"><h2>Forma de pagamento</h2><form method="post" class="form-stack"><?= csrf_field() ?>
<label class="payment-option"><input type="radio" name="payment_method" value="PIX" checked><span><i class="bi bi-qr-code"></i><b> PIX</b><small>Confirmação simulada imediata</small></span></label>
<label class="payment-option"><input type="radio" name="payment_method" value="Boleto"><span><i class="bi bi-upc"></i><b> Boleto</b><small>Emissão demonstrativa</small></span></label>
<label class="payment-option"><input type="radio" name="payment_method" value="Cartão"><span><i class="bi bi-credit-card"></i><b> Cartão</b><small>Sem inserir dados de cartão</small></span></label>
<button class="btn btn-primary btn-lg w-100">Confirmar pedido · <?= money($total) ?></button></form></section>
<aside class="summary-card"><h2>Seu pedido</h2><?php foreach ($items as $item): ?><div class="summary-line"><span><?= e($item['name']) ?> × <?= (int)$item['quantity'] ?></span><strong><?= money($item['subtotal']) ?></strong></div><?php endforeach; ?><hr><div class="summary-line total-line"><span>Total</span><strong><?= money($total) ?></strong></div></aside></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
