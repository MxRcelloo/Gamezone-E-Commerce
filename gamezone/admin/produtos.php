<?php
require_once dirname(__DIR__) . '/config/bootstrap.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    $action = $_POST['action'] ?? '';
    if ($action === 'toggle' && $id) {
        $stmt = $pdo->prepare("UPDATE products SET is_active = NOT is_active WHERE id=?");
        $stmt->execute([$id]);
        set_flash('success', 'Status do produto atualizado.');
    } elseif ($action === 'delete' && $id) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
        try { $stmt->execute([$id]); set_flash('success', 'Produto excluído.'); }
        catch (PDOException $e) { set_flash('danger', 'Este produto já aparece em um pedido. Desative-o em vez de excluir.'); }
    }
    redirect('produtos.php');
}
$products = $pdo->query("SELECT p.*,c.name AS category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.created_at DESC")->fetchAll();
$pageTitle = 'Produtos';
require dirname(__DIR__) . '/includes/header.php';
?>
<div class="admin-heading"><div><span class="eyebrow">CATÁLOGO</span><h1>Produtos</h1><p>Cadastre, edite, desative ou exclua produtos.</p></div><a href="produto-form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Novo produto</a></div>
<div class="table-responsive table-dark-custom"><table class="table align-middle"><thead><tr><th>Produto</th><th>Categoria</th><th>Preço</th><th>Estoque</th><th>Status</th><th>Ações</th></tr></thead><tbody>
<?php foreach ($products as $p): ?><tr><td><div class="admin-product"><img src="<?= e($p['image']) ?>" alt=""><b><?= e($p['name']) ?></b></div></td><td><?= e($p['category_name'] ?? '—') ?></td><td><?= money($p['price']) ?></td><td><?= (int)$p['stock'] ?></td><td><span class="status-pill"><?= $p['is_active'] ? 'Ativo' : 'Inativo' ?></span></td><td><div class="action-group"><a class="btn btn-sm btn-outline-light" href="produto-form.php?id=<?= (int)$p['id'] ?>">Editar</a><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="action" value="toggle"><button class="btn btn-sm btn-outline-light"><?= $p['is_active'] ? 'Desativar' : 'Ativar' ?></button></form><form method="post" onsubmit="return confirm('Excluir este produto?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="action" value="delete"><button class="btn btn-sm btn-outline-danger">Excluir</button></form></div></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
