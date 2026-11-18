<?php
require_once __DIR__ . '/database/app.php';
redirect_authenticated_user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>AgroMarketplace — How It Works</title>
  <link rel="stylesheet" href="colors.css">
  <link rel="stylesheet" href="header.css">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="how-it-works.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body class="theme-buyer">
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="index.php">AgroMarketplace</a>
      <nav class="nav">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="catalogue/catalogue.php">Catalogue</a>
        <a class="nav-link" href="how-it-works.php">How It Works</a>
        <a class="nav-link" href="contact.php">Contact</a>
      </nav>
      <div class="site-header-actions">
        <a class="account-link" href="auth/login.php" aria-label="Guest account">
          <img class="avatar" alt="guest avatar" src="assets/guest-avatar.png">
          <span class="account-type">Guest</span>
        </a>
        <a class="btn btn-ghost" href="auth/login.php">Login</a>
      </div>
    </div>
  </header>

  <main class="how-shell">
    <section class="how-hero">
      <span class="section-kicker">How it works</span>
      <h1>From discovery to delivery in four simple steps.</h1>
      <p>AgroMarketplace makes it easy for farmers, buyers, and delivery partners to work together transparently and efficiently.</p>
    </section>

    <section class="steps-grid" aria-label="How it works steps">
      <article class="step-card">
        <div class="step-number">01</div>
        <h2>Explore listings</h2>
        <p>Browse verified agricultural products, compare prices, and review quality details before placing an order.</p>
      </article>

      <article class="step-card">
        <div class="step-number">02</div>
        <h2>Negotiate securely</h2>
        <p>Buyers can confirm quantity, timing, delivery expectations, and transaction terms with trusted sellers.</p>
      </article>

      <article class="step-card">
        <div class="step-number">03</div>
        <h2>Track fulfillment</h2>
        <p>Delivery partners and platform admins keep each shipment visible, coordinated, and easy to monitor.</p>
      </article>

      <article class="step-card">
        <div class="step-number">04</div>
        <h2>Receive with confidence</h2>
        <p>Once the goods are delivered, both sides can verify the completion and keep transparent records.</p>
      </article>
    </section>

    <section class="benefits-panel" aria-label="Platform benefits">
      <div class="benefits-header">
        <span class="section-kicker">Why it matters</span>
        <h2>Built for trust, speed, and better farm-to-market coordination.</h2>
      </div>

      <div class="benefit-grid">
        <div class="benefit-item">
          <h3>Verified sellers</h3>
          <p>Every listing is linked to a reliable profile and transparent operational history.</p>
        </div>
        <div class="benefit-item">
          <h3>Live pricing</h3>
          <p>Buyers can understand market conditions before they commit to a trade.</p>
        </div>
        <div class="benefit-item">
          <h3>Clear workflow</h3>
          <p>Roles are separated between farmers, buyers, admin, and delivery partners to reduce confusion.</p>
        </div>
      </div>
    </section>
  </main>

  <footer class="site-footer">
    <div class="site-footer-inner">
      <p>© AgroMarketplace</p>
    </div>
  </footer>
</body>
</html>
