<?php
require_once __DIR__ . '/config/bootstrap.php';
require_login();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
$stmt->execute([$id, (int)current_user()['id']]);
$order = $stmt->fetch();
if (!$order) { set_flash('warning', 'Pedido não encontrado.'); redirect('pedidos.php'); }
$pageTitle = 'Pedido confirmado';
require __DIR__ . '/includes/header.php';
?>
<div class="success-panel"><div class="success-icon"><i class="bi bi-check2"></i></div><span class="eyebrow">COMPRA CONCLUÍDA</span><h1>Pedido confirmado!</h1><p>Seu pedido <strong>#<?= (int)$order['id'] ?></strong> foi registrado com sucesso.</p><div class="confirmed-total"><?= money($order['total']) ?></div><p>Pagamento simulado via <?= e($order['payment_method']) ?>.</p><a href="pedidos.php" class="btn btn-primary">Ver meus pedidos</a> <a href="index.php" class="btn btn-outline-light">Voltar à loja</a></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
