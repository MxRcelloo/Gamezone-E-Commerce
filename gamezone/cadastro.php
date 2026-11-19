<?php
require_once __DIR__ . '/config/bootstrap.php';
if (current_user()) redirect('index.php');
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    if (mb_strlen($name) < 2) $errors[] = 'Informe seu nome.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Informe um e-mail válido.';
    if (strlen($password) < 8) $errors[] = 'A senha deve ter pelo menos 8 caracteres.';
    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[] = 'Este e-mail já está cadastrado.';
    }
    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$pdo->lastInsertId(), 'name' => $name, 'email' => $email, 'role' => 'customer'];
        set_flash('success', 'Conta criada com sucesso. Boas-vindas à GameZone!');
        redirect('index.php');
    }
}
$pageTitle = 'Criar conta';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap"><div class="auth-card"><span class="eyebrow">ENTRE PARA O TIME</span><h1>Criar conta</h1><p>Cadastre-se para acompanhar seus pedidos e finalizar compras.</p>
<?php foreach ($errors as $error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endforeach; ?>
<form method="post" class="form-stack"><?= csrf_field() ?>
<label>Nome completo<input class="form-control" name="name" required value="<?= e($_POST['name'] ?? '') ?>"></label>
<label>E-mail<input class="form-control" type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></label>
<label>Senha<input class="form-control" type="password" name="password" minlength="8" required><small class="muted">Mínimo de 8 caracteres.</small></label>
<button class="btn btn-primary btn-lg w-100">Criar minha conta</button></form><p class="auth-bottom">Já tem cadastro? <a href="login.php">Entrar</a></p></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
