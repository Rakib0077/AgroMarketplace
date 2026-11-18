<?php
require_once __DIR__ . '/../database/app.php';
redirect_authenticated_user();

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $role = $_POST['accountType'] ?? '';
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $data = [
    'email' => $email,
    'fullname' => trim($_POST['fullname'] ?? ''),
    'phone' => trim($_POST['phone'] ?? ''),
    'address' => trim($_POST['address'] ?? ''),
    'preferred_buying_category' => trim($_POST['preferred_buying_category'] ?? ''),
    'typical_order_volume' => trim($_POST['typical_order_volume'] ?? ''),
    'business_name' => trim($_POST['business_name'] ?? ''),
    'land_area' => trim($_POST['land_area'] ?? ''),
    'primary_crops' => trim($_POST['primary_crops'] ?? ''),
    'verification_documents' => trim($_POST['verification_documents'] ?? ''),
    'vehicle_capacity' => trim($_POST['vehicle_capacity'] ?? ''),
    'vehicle_license_plate' => trim($_POST['vehicle_license_plate'] ?? ''),
  ];

  if (!in_array($role, ['buyer', 'farmer', 'delivery'], true)) {
    $error = 'Choose a valid account type.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Enter a valid email address.';
  } elseif (strlen($password) < 8) {
    $error = 'Use a password with at least 8 characters.';
  } elseif ($password !== ($_POST['password_confirmation'] ?? '')) {
    $error = 'The passwords do not match.';
  } else {
    try {
      if (Database::getInstance()->register($role, $email, $password, $data, 'pending')) {
        $success = 'Registration successful. Your account is waiting for admin approval.';
      } else {
        $error = 'We could not create your account. Please try again.';
      }
    } catch (Throwable $exception) {
      $error = $exception instanceof PDOException && $exception->getCode() === '23000'
        ? 'An account with this email already exists.'
        : 'We could not create your account. Please try again.';
    }
  }
}

$notice = take_flash();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register — AgroMarketplace</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="register.css">
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
        <a class="btn btn-ghost" href="login.php">Login</a>
      </div>
    </div>
  </header>

  <main class="auth-main">
    <section class="auth-card" aria-labelledby="signup-title">
      <h1 id="signup-title">Create Your Account</h1>
      <p class="muted">Create a buyer, farmer, or delivery account for AgroMarketplace.</p>

      <?php if ($error !== ''): ?><p class="form-feedback form-feedback-error" role="alert"><?= escape_html($error) ?></p><?php endif; ?>
      <?php if ($success !== ''): ?><p class="form-feedback form-feedback-success" role="status"><?= escape_html($success) ?></p><?php endif; ?>
      <?php if ($notice): ?><p class="form-feedback form-feedback-success" role="status"><?= escape_html($notice['message']) ?></p><?php endif; ?>
      <?php if ($success === ''): ?>
      <form class="auth-form" action="register.php" method="post">
        <?= csrf_field() ?>
        <div class="role-picker" aria-label="Account type selection">
          <input type="radio" name="accountType" id="role-buyer" value="buyer" checked>
          <label class="role-option" for="role-buyer">
            <span class="role-name">Buyer</span>
            <small>Purchase produce</small>
          </label>

          <input type="radio" name="accountType" id="role-farmer" value="farmer">
          <label class="role-option" for="role-farmer">
            <span class="role-name">Farmer</span>
            <small>Sell crops</small>
          </label>

          <input type="radio" name="accountType" id="role-delivery" value="delivery">
          <label class="role-option" for="role-delivery">
            <span class="role-name">Delivery</span>
            <small>Deliver orders</small>
          </label>
        </div>

        <label class="field">
          <span>Full Name</span>
          <input type="text" name="fullname" placeholder="Tanvir Ahmed" required>
        </label>

        <label class="field">
          <span>Email Address</span>
          <input type="email" name="email" placeholder="you@example.com" required>
        </label>

        <label class="field">
          <span>Phone Number</span>
          <input type="tel" name="phone" placeholder="+8801700000000" required>
        </label>

        <label class="field">
          <span>District / Location</span>
          <input type="text" name="address" placeholder="Dhaka, Bogra, Khulna..." required>
        </label>

        <label class="field">
          <span>Password</span>
          <input type="password" name="password" autocomplete="new-password" required>
        </label>

        <label class="field">
          <span>Confirm Password</span>
          <input type="password" name="password_confirmation" autocomplete="new-password" required>
        </label>

        <!-- BUYER EXTRA FIELDS -->
        <div class="role-panel buyer-panel">
          <label class="field">
            <span>Preferred Buying Category</span>
            <select name="preferred_buying_category">
              <option>Rice</option>
              <option>Wheat</option>
              <option>Vegetables</option>
              <option>Pulse &amp; Seeds</option>
              <option>Other</option>
            </select>
          </label>

          <label class="field">
            <span>Typical Order Volume</span>
            <input type="text" name="typical_order_volume" placeholder="Example: 5 MT per month">
          </label>
        </div>

        <!-- FARMER EXTRA FIELDS -->
        <div class="role-panel farmer-panel">
          <label class="field">
            <span>Farm / Business Name</span>
            <input type="text" name="business_name" placeholder="Green Valley Agro">
          </label>

          <label class="field">
            <span>Land Area</span>
            <input type="text" name="land_area" placeholder="Example: 12 acres">
          </label>

          <label class="field">
            <span>Primary Crops</span>
            <input type="text" name="primary_crops" placeholder="Rice, wheat, mustard">
          </label>

          <label class="field">
            <span>Verification Documents</span>
            <input type="text" name="verification_documents" placeholder="NID, land papers, certificate">
          </label>
        </div>

        <div class="role-panel delivery-panel">
          <label class="field">
            <span>Vehicle Capacity</span>
            <input type="text" name="vehicle_capacity" placeholder="Example: 5 MT">
          </label>
          <label class="field">
            <span>Vehicle License Plate</span>
            <input type="text" name="vehicle_license_plate" placeholder="Example: DHA-1234">
          </label>
        </div>

        <div class="form-actions">
          <button class="btn btn-primary" type="submit">Create Account</button>
        </div>
      </form>
      <?php endif; ?>
    </section>
  </main>

  <script>
    const roleInputs = document.querySelectorAll('input[name="accountType"]');
    const rolePanels = document.querySelectorAll('.role-panel');

    function updateRolePanels() {
      const activeRole = document.querySelector('input[name="accountType"]:checked');
      const roleName = activeRole ? activeRole.id.replace('role-', '') : 'buyer';

      rolePanels.forEach((panel) => {
        const isVisible = panel.classList.contains(`${roleName}-panel`);
        panel.classList.toggle('is-visible', isVisible);
        panel.hidden = !isVisible;
      });
    }

    roleInputs.forEach((input) => input.addEventListener('change', updateRolePanels));
    updateRolePanels();
  </script>
</body>
</html>