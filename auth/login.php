<?php
require_once __DIR__ . '/../database/app.php';
redirect_authenticated_user();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  try {
    $user = authenticate_user(trim($_POST['email'] ?? ''), $_POST['password'] ?? '');
    if ($user === null) {
      $error = 'Email or password is incorrect.';
    } else {
      $destinations = [
        'buyer' => '../buyer/index.php',
        'farmer' => '../farmer/index.php',
        'delivery' => '../delivery/index.php',
        'admin' => '../admin/index.php',
      ];
      redirect_to($destinations[$user['role']] ?? '../index.php');
    }
  } catch (Throwable $exception) {
    $error = 'Sign in is temporarily unavailable. Please try again.';
  }
}

$notice = take_flash();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login — AgroMarketplace</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="login.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
  <header class="auth-header site-header">
    <div class="site-header-inner">
      <a class="brand" href="../index.php">AgroMarketplace</a>
      <nav class="nav" aria-label="Main navigation">
        <a class="nav-link" href="../index.php">Home</a>
        <a class="nav-link" href="../catalogue/catalogue.php">Catalogue</a>
        <a class="nav-link" href="../how-it-works.php">How It Works</a>
        <a class="nav-link" href="../contact.php">Contact</a>
      </nav>
      <div class="site-header-actions">
        <a class="account-link" href="login.php" aria-label="Guest account">
          <img class="avatar" alt="guest avatar" src="../assets/guest-avatar.png">
          <span class="account-type">Guest</span>
        </a>
      </div>
    </div>
  </header>
  <main class="auth-main">
    <section class="auth-card" aria-labelledby="signin-title">
      <h1 id="signin-title">Sign In to AgroMarketplace</h1>
      <p class="muted">Access your buyer, farmer or delivery dashboard account.</p>
      <?php if ($error !== ''): ?><p class="muted" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
      <?php if ($notice): ?><p class="muted" role="status"><?= escape_html($notice['message']) ?></p><?php endif; ?>
      <form class="auth-form" action="login.php" method="post">
        <?= csrf_field() ?>
        <label class="field">
          <span>Email</span>
          <input type="email" name="email" placeholder="buyer@email.com" autocomplete="username" required>
        </label>
        <label class="field">
          <span>Password</span>
          <input type="password" name="password" autocomplete="current-password" required>
        </label>
        <div class="form-actions">
          <button class="btn btn-primary" type="submit">Log In</button>
        </div>
      </form>
      <br>
      <p class="muted" style="text-align:center;">Not registered yet? <a href="register.php">Sign Up</a></p>
    </section>
  </main>
</body>
</html>
