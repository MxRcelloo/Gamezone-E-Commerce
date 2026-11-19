<?php
require_once dirname(__DIR__) . '/config/bootstrap.php';
require_admin();
$orders = $pdo->query("SELECT o.*,u.name AS customer,u.email FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.created_at DESC")->fetchAll();
$pageTitle = 'Todos os pedidos';
require dirname(__DIR__) . '/includes/header.php';
?>
<div class="page-title"><span class="eyebrow">VENDAS</span><h1>Pedidos realizados</h1><p>Visualize os pedidos e os itens de cada compra.</p></div>
<?php if (!$orders): ?><div class="empty-state"><i class="bi bi-receipt"></i><h3>Nenhum pedido realizado ainda.</h3></div><?php else: ?>
<div class="orders-admin"><?php foreach ($orders as $o): ?>
<details class="order-detail"><summary><span>#<?= (int)$o['id'] ?> · <?= e($o['customer']) ?></span><strong><?= money($o['total']) ?></strong><small><?= e(date('d/m/Y H:i', strtotime($o['created_at']))) ?> · <?= e($o['payment_method']) ?></small></summary>
<div class="order-items"><?php
$stmt = $pdo->prepare("SELECT oi.*,p.name FROM order_items oi LEFT JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?");
$stmt->execute([(int)$o['id']]);
foreach ($stmt->fetchAll() as $item): ?>
<div class="summary-line"><span><?= e($item['name'] ?? 'Produto removido') ?> × <?= (int)$item['quantity'] ?></span><strong><?= money((float)$item['unit_price'] * (int)$item['quantity']) ?></strong></div>
<?php endforeach; ?></div></details><?php endforeach; ?></div>
<?php endif; ?>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
