<?php
require_once dirname(__DIR__) . '/config/bootstrap.php';
require_admin();
$productCount = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orderCount = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$userCount = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$revenue = (float)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders")->fetchColumn();
$orders = $pdo->query("SELECT o.*,u.name AS customer FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.created_at DESC LIMIT 8")->fetchAll();
$pageTitle = 'Painel administrativo';
require dirname(__DIR__) . '/includes/header.php';
?>
<div class="admin-heading"><div><span class="eyebrow">ÁREA RESTRITA</span><h1>Painel administrativo</h1><p>Visão geral da operação GameZone.</p></div><a href="produtos.php" class="btn btn-primary">Gerenciar produtos</a></div>
<div class="stats-grid"><div class="stat-card"><span>Produtos</span><strong><?= $productCount ?></strong><i class="bi bi-box-seam"></i></div><div class="stat-card"><span>Pedidos</span><strong><?= $orderCount ?></strong><i class="bi bi-receipt"></i></div><div class="stat-card"><span>Clientes</span><strong><?= $userCount ?></strong><i class="bi bi-people"></i></div><div class="stat-card"><span>Vendas simuladas</span><strong><?= money($revenue) ?></strong><i class="bi bi-currency-dollar"></i></div></div>
<div class="section-heading mt-5"><h2>Pedidos recentes</h2><a href="pedidos.php">Ver todos</a></div>
<div class="table-responsive table-dark-custom"><table class="table align-middle"><thead><tr><th>#</th><th>Cliente</th><th>Data</th><th>Pagamento</th><th>Total</th></tr></thead><tbody><?php foreach ($orders as $o): ?><tr><td>#<?= (int)$o['id'] ?></td><td><?= e($o['customer']) ?></td><td><?= e(date('d/m/Y', strtotime($o['created_at']))) ?></td><td><?= e($o['payment_method']) ?></td><td><?= money($o['total']) ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
