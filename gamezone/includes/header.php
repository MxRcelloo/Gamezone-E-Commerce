<?php
$pageTitle = $pageTitle ?? 'GameZone';
$rootPath = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/admin/') ? '../' : '';
$flash = get_flash();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | GameZone</title>
    <meta name="description" content="GameZone: equipamentos, acessórios e tecnologia gamer.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= $rootPath ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
<header class="site-header">
    <nav class="navbar navbar-expand-lg container py-3">
        <a class="navbar-brand brand" href="<?= $rootPath ?>index.php"><span class="brand-mark">G</span> GAME<span>ZONE</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Abrir menu"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <form class="search-form mx-lg-auto my-3 my-lg-0" action="<?= $rootPath ?>index.php" method="get">
                <input type="search" name="q" placeholder="Buscar produtos..." value="<?= e($_GET['q'] ?? '') ?>">
                <button aria-label="Buscar"><i class="bi bi-search"></i> Buscar</button>
            </form>
            <div class="nav-actions">
                <a href="<?= $rootPath ?>carrinho.php" class="nav-link-custom">Carrinho <span class="cart-badge"><?= cart_count() ?></span></a>
                <?php if (current_user()): ?>
                    <a href="<?= $rootPath ?>pedidos.php" class="nav-link-custom">Meus pedidos</a>
                    <?php if (current_user()['role'] === 'admin'): ?><a href="<?= $rootPath ?>admin/index.php" class="nav-link-custom">Admin</a><?php endif; ?>
                    <a href="<?= $rootPath ?>logout.php" class="btn btn-outline-light btn-sm">Sair</a>
                <?php else: ?>
                    <a href="<?= $rootPath ?>login.php" class="nav-link-custom">Entrar</a>
                    <a href="<?= $rootPath ?>cadastro.php" class="btn btn-primary btn-sm">Criar conta</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>
<main class="container page-main">
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show mt-3" role="alert">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endif; ?>
