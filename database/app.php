<?php

require_once __DIR__ . '/connection.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function app_db(): PDO
{
    $connection = Database::getInstance()->getConnection();
    if ($connection === null) {
        throw new RuntimeException('The database is unavailable.');
    }

    return $connection;
}

function app_database(): Database
{
    app_db();
    return Database::getInstance();
}

function escape_html(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function listing_image_url(array $listing): string
{
    $imagePath = $listing['image_path'] ?? '';
    $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
    if (str_starts_with($imagePath, 'assets/') && !str_contains($imagePath, '..') && in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
        return '../' . $imagePath;
    }

    $title = strtolower($listing['title'] ?? '');
    if (str_contains($title, 'rice')) {
        return '../assets/nazirshail rice.jpeg';
    }
    if (str_contains($title, 'wheat')) {
        return '../assets/winter wheat.jpeg';
    }
    if (str_contains($title, 'lentil')) {
        return '../assets/red lentils.jpeg';
    }
    if (str_contains($title, 'chili')) {
        return '../assets/chili.jpeg';
    }
    if (str_contains($title, 'potato')) {
        return '../assets/organic potatoes.jpeg';
    }
    if (str_contains($title, 'onion')) {
        return '../assets/onions.webp';
    }
    if (str_contains($title, 'corn')) {
        return '../assets/yellow corn.jpeg';
    }
    if (str_contains($title, 'mango')) {
        return '../assets/banana.webp';
    }
    if (str_contains($title, 'ginger') || str_contains($title, 'carrot')) {
        return '../assets/organic potatoes.jpeg';
    }
    if (str_contains($title, 'mustard')) {
        return '../assets/sunflower seeds.jpg';
    }

    return '../assets/wheat.webp';
}

function csrf_field(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return '<input type="hidden" name="csrf_token" value="' . escape_html($_SESSION['csrf_token']) . '">';
}

function verify_csrf_token(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('The form expired. Please go back and try again.');
    }
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function take_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function redirect_to(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function app_base_path(): string
{
    $scriptPath = parse_url($_SERVER['SCRIPT_NAME'] ?? '', PHP_URL_PATH);
    if (!is_string($scriptPath) || $scriptPath === '') {
        return '';
    }

    $segments = array_values(array_filter(explode('/', trim($scriptPath, '/')), 'strlen'));
    $appDirectories = ['admin', 'auth', 'buyer', 'catalogue', 'delivery', 'farmer'];
    $appDirectoryIndex = null;
    foreach ($segments as $index => $segment) {
        if (in_array($segment, $appDirectories, true)) {
            $appDirectoryIndex = $index;
        }
    }

    if ($appDirectoryIndex !== null) {
        $baseSegments = array_slice($segments, 0, $appDirectoryIndex);
        return $baseSegments === [] ? '' : '/' . implode('/', $baseSegments);
    }

    if (in_array(basename($scriptPath), ['index.php', 'contact.php', 'how-it-works.php'], true)) {
        $basePath = rtrim(dirname($scriptPath), '/.');
        return $basePath === '' ? '' : $basePath;
    }

    return '';
}

function app_url(string $path): string
{
    return app_base_path() . '/' . ltrim($path, '/');
}

function calculate_commission(PDO $connection, string $ruleType, ?int $categoryId, float $basis, ?string $effectiveAt = null): float
{
    return app_database()->calculateCommission($ruleType, $categoryId, $basis, $effectiveAt);
}

function authenticate_user(string $identifier, string $password): ?array
{
    $user = app_database()->authenticateUser($identifier, $password);
    if ($user === null) {
        return null;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['fullname'] ?: $user['username'];

    return $user;
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $user = app_database()->getUserInfo((int) $_SESSION['user_id']);

    if (!$user) {
        unset($_SESSION['user_id'], $_SESSION['user_role'], $_SESSION['user_name']);
        return null;
    }

    return $user;
}

function redirect_authenticated_user(): void
{
    $user = current_user();
    if ($user === null) {
        return;
    }

    $dashboards = [
        'buyer' => 'buyer/index.php',
        'farmer' => 'farmer/index.php',
        'delivery' => 'delivery/index.php',
        'admin' => 'admin/index.php',
    ];
    redirect_to(app_url($dashboards[$user['role']] ?? ''));
}

function require_role(array $roles): array
{
    $user = current_user();
    if ($user === null) {
        set_flash('error', 'Please sign in to continue.');
        redirect_to(app_url('auth/login.php'));
    }

    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('You do not have permission to view this page.');
    }

    return $user;
}