<?php
require_once __DIR__ . '/config/bootstrap.php';

$q = trim($_GET['q'] ?? '');
$categoryId = filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT) ?: null;

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();

$sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1";
$params = [];
if ($q !== '') {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%{$q}%";
    $params[] = "%{$q}%";
}
if ($categoryId) {
    $sql .= " AND p.category_id = ?";
    $params[] = $categoryId;
}
$sql .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$pageTitle = 'Loja gamer';
require __DIR__ . '/includes/header.php';
?>
<section class="hero-section">
    <div class="hero-copy">
        <span class="eyebrow"><span class="pulse-dot"></span> EQUIPAMENTOS PARA JOGAR MELHOR</span>
        <h1>SEU JOGO.<br><span>SUA ZONA.</span></h1>
        <p>Monte seu setup, encontre seus acessórios e prepare-se para o próximo nível.</p>
        <a href="#produtos" class="btn btn-primary btn-lg">Explorar produtos <i class="bi bi-arrow-right"></i></a>
        <div class="hero-stats"><div><strong>100%</strong><small>Experiência gamer</small></div><div><strong>PIX</strong><small>Pagamento simulado</small></div><div><strong>24/7</strong><small>Na sua partida</small></div></div>
    </div>
    <div class="hero-art"><div class="hero-glow"></div><img src="https://images.unsplash.com/photo-1593305841991-05c297ba4575?auto=format&fit=crop&w=1000&q=85" alt="Setup gamer iluminado"></div>
</section>

<section class="category-strip">
    <div class="section-heading"><div><span class="eyebrow">ESCOLHA SUA CATEGORIA</span><h2>Explore a GameZone</h2></div></div>
    <div class="category-pills">
        <a href="index.php" class="<?= !$categoryId ? 'active' : '' ?>">Todos</a>
        <?php foreach ($categories as $category): ?>
            <a href="index.php?categoria=<?= (int)$category['id'] ?>" class="<?= $categoryId === (int)$category['id'] ? 'active' : '' ?>"><?= e($category['name']) ?></a>
        <?php endforeach; ?>
    </div>
</section>

<section id="produtos" class="products-section">
    <div class="section-heading"><div><span class="eyebrow">SELEÇÃO GAMEZONE</span><h2><?= $q ? 'Resultados da busca' : 'Produtos em destaque' ?></h2></div><span class="muted"><?= count($products) ?> produto(s)</span></div>
    <?php if (!$products): ?>
        <div class="empty-state"><i class="bi bi-controller"></i><h3>Nenhum produto encontrado</h3><p>Tente outra busca ou categoria.</p><a href="index.php" class="btn btn-outline-light">Ver todos</a></div>
    <?php else: ?>
        <div class="row g-4">
        <?php foreach ($products as $product): ?>
            <div class="col-6 col-md-4 col-xl-3">
                <article class="product-card">
                    <a href="produto.php?id=<?= (int)$product['id'] ?>" class="product-image">
                        <img src="<?= e($product['image'] ?: 'https://placehold.co/600x450/171522/ffffff?text=GameZone') ?>" alt="<?= e($product['name']) ?>" loading="lazy">
                        <span class="image-tag"><?= e($product['category_name'] ?? 'Gamer') ?></span>
                    </a>
                    <div class="product-info">
                        <h3><a href="produto.php?id=<?= (int)$product['id'] ?>"><?= e($product['name']) ?></a></h3>
                        <div class="product-bottom"><strong><?= money($product['price']) ?></strong><a href="produto.php?id=<?= (int)$product['id'] ?>" aria-label="Ver produto"><i class="bi bi-arrow-up-right"></i></a></div>
                        <small class="<?= (int)$product['stock'] > 0 ? 'stock-ok' : 'stock-out' ?>"><?= (int)$product['stock'] > 0 ? 'Em estoque' : 'Indisponível' ?></small>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
