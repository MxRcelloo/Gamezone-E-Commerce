<?php
require_once __DIR__ . '/config/bootstrap.php';
if (current_user()) redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare("SELECT id,name,email,password,role FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        unset($user['password']);
        $_SESSION['user'] = $user;
        set_flash('success', 'Login realizado. Bem-vindo(a)!');
        redirect('index.php');
    }
    $error = 'E-mail ou senha incorretos.';
}
$pageTitle = 'Entrar';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap"><div class="auth-card"><span class="eyebrow">BEM-VINDO DE VOLTA</span><h1>Entrar</h1><p>Acesse sua conta GameZone.</p>
<?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="form-stack"><?= csrf_field() ?>
<label>E-mail<input class="form-control" type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
<label>Senha<input class="form-control" type="password" name="password" required></label>
<button class="btn btn-primary btn-lg w-100">Entrar na minha conta</button></form><p class="auth-bottom">Ainda não tem conta? <a href="cadastro.php">Cadastre-se</a></p></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
