<?php
require_once dirname(__DIR__) . '/config/bootstrap.php';
require_admin();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$product = ['name'=>'','description'=>'','price'=>'','stock'=>'0','image'=>'','category_id'=>''];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) { set_flash('warning', 'Produto não encontrado.'); redirect('produtos.php'); }
}
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = filter_var($_POST['price'] ?? null, FILTER_VALIDATE_FLOAT);
    $stock = filter_var($_POST['stock'] ?? null, FILTER_VALIDATE_INT);
    $category = filter_var($_POST['category_id'] ?? null, FILTER_VALIDATE_INT);
    $image = trim($_POST['image'] ?? '');
    if ($name === '') $errors[] = 'Informe o nome.';
    if ($price === false || $price < 0) $errors[] = 'Informe um preço válido.';
    if ($stock === false || $stock < 0) $errors[] = 'Informe um estoque válido.';
    if ($image !== '' && !filter_var($image, FILTER_VALIDATE_URL)) $errors[] = 'A imagem deve ser uma URL válida.';
    if (!$errors) {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE products SET category_id=?,name=?,description=?,price=?,image=?,stock=? WHERE id=?");
            $stmt->execute([$category ?: null,$name,$description,$price,$image,$stock,$id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (category_id,name,description,price,image,stock) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$category ?: null,$name,$description,$price,$image,$stock]);
        }
        set_flash('success', 'Produto salvo com sucesso.');
        redirect('produtos.php');
    }
    $product = array_merge($product, ['name'=>$name,'description'=>$description,'price'=>$price,'stock'=>$stock,'image'=>$image,'category_id'=>$category]);
}
$pageTitle = $id ? 'Editar produto' : 'Novo produto';
require dirname(__DIR__) . '/includes/header.php';
?>
<div class="page-title"><span class="eyebrow">CATÁLOGO</span><h1><?= $id ? 'Editar produto' : 'Novo produto' ?></h1></div>
<div class="panel-card form-panel"><?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
<form method="post" class="form-stack"><?= csrf_field() ?>
<label>Nome do produto<input class="form-control" name="name" required value="<?= e($product['name']) ?>"></label>
<label>Descrição<textarea class="form-control" name="description" rows="4"><?= e($product['description']) ?></textarea></label>
<div class="row g-3"><div class="col-md-6"><label>Preço (R$)<input class="form-control" type="number" name="price" step="0.01" min="0" required value="<?= e($product['price']) ?>"></label></div><div class="col-md-6"><label>Estoque<input class="form-control" type="number" name="stock" min="0" required value="<?= e($product['stock']) ?>"></label></div></div>
<label>Categoria<select class="form-select" name="category_id"><option value="">Sem categoria</option><?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (int)$product['category_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></label>
<label>URL da imagem<input class="form-control" type="url" name="image" placeholder="https://..." value="<?= e($product['image']) ?>"></label>
<div class="d-flex gap-2"><button class="btn btn-primary">Salvar produto</button><a href="produtos.php" class="btn btn-outline-light">Cancelar</a></div>
</form></div>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
