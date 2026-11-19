<?php
declare(strict_types=1);

function e(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

function money(float|string $value): string {
    return 'R$ ' . number_format((float)$value, 2, ',', '.');
}

function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Solicitação inválida. Atualize a página e tente novamente.');
    }
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_login(): void {
    if (!current_user()) {
        set_flash('warning', 'Entre na sua conta para continuar.');
        redirect('login.php');
    }
}

function require_admin(): void {
    if (!current_user() || current_user()['role'] !== 'admin') {
        http_response_code(403);
        set_flash('danger', 'Acesso restrito ao administrador.');
        redirect('../index.php');
    }
}

function cart_count(): int {
    return array_sum(array_map('intval', $_SESSION['cart'] ?? []));
}

function cart_items(PDO $pdo): array {
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];

    $ids = array_values(array_filter(array_map('intval', array_keys($cart))));
    if (!$ids) return [];

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT id, name, price, image, stock, is_active FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    $items = [];
    foreach ($products as $product) {
        $id = (int)$product['id'];
        if (!(bool)$product['is_active']) continue;
        $qty = max(0, (int)($cart[$id] ?? 0));
        if ($qty < 1) continue;
        $product['quantity'] = $qty;
        $product['subtotal'] = (float)$product['price'] * $qty;
        $items[] = $product;
    }
    return $items;
}

function cart_total(array $items): float {
    return array_sum(array_map(fn($item) => (float)$item['subtotal'], $items));
}
