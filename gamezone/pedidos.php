<?php
require_once __DIR__ . '/config/bootstrap.php';
require_login();
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([(int)current_user()['id']]);
$orders = $stmt->fetchAll();
$pageTitle = 'Meus pedidos';
require __DIR__ . '/includes/header.php';
?>
<div class="page-title"><span class="eyebrow">HISTÓRICO</span><h1>Meus pedidos</h1><p>Acompanhe suas compras realizadas na GameZone.</p></div>
<?php if (!$orders): ?><div class="empty-state"><i class="bi bi-receipt"></i><h3>Você ainda não tem pedidos</h3><a href="index.php" class="btn btn-primary">Explorar loja</a></div>
<?php else: ?>
<div class="table-responsive table-dark-custom"><table class="table align-middle"><thead><tr><th>Pedido</th><th>Data</th><th>Pagamento</th><th>Status</th><th>Total</th></tr></thead><tbody>
<?php foreach ($orders as $order): ?><tr><td>#<?= (int)$order['id'] ?></td><td><?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?></td><td><?= e($order['payment_method']) ?></td><td><span class="status-pill"><?= e($order['status']) ?></span></td><td><?= money($order['total']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
