<?php
require_once __DIR__ . '/database/app.php';
redirect_authenticated_user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>AgroMarketplace — Contact</title>
  <link rel="stylesheet" href="colors.css">
  <link rel="stylesheet" href="header.css">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="contact.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
</head>
<body>
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

  <main class="contact-shell">
    <section class="contact-card" aria-label="Contact panel">
      <div class="contact-panel contact-panel--info">
        <span class="contact-kicker">Contact us</span>
        <h1 class="contact-title">Ask us anything about sourcing, pricing, or onboarding.</h1>
        <p class="contact-copy">Whether you are a buyer, seller, or logistics partner, our team is here to help you move faster and trade with confidence.</p>

        <div class="info-list">
          <div class="info-item">
            <div class="info-icon">✦</div>
            <div>
              <h3>Call us</h3>
              <p>+880 1700-000000</p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-icon">✉</div>
            <div>
              <h3>Email</h3>
              <a href="mailto:help@agromarketplace.com">help@agromarketplace.com</a>
            </div>
          </div>

          <div class="info-item">
            <div class="info-icon">⌂</div>
            <div>
              <h3>Office</h3>
              <p>Dhaka Trade Hub, Bangladesh</p>
            </div>
          </div>
        </div>
      </div>

      <div class="contact-panel">
        <form class="contact-form" action="#" method="post">
          <div class="form-row">
            <div class="field">
              <label for="full-name">Full name</label>
              <input id="full-name" name="full-name" type="text" placeholder="Your name" required>
            </div>
            <div class="field">
              <label for="company">Company / role</label>
              <input id="company" name="company" type="text" placeholder="Farmer, buyer, or partner">
            </div>
          </div>

          <div class="form-row">
            <div class="field">
              <label for="email">Email address</label>
              <input id="email" name="email" type="email" placeholder="you@example.com" required>
            </div>
            <div class="field">
              <label for="topic">Topic</label>
              <select id="topic" name="topic">
                <option>General inquiry</option>
                <option>Marketplace onboarding</option>
                <option>Pricing and product listing</option>
                <option>Delivery and logistics</option>
                <option>Technical support</option>
              </select>
            </div>
          </div>

          <div class="field">
            <label for="message">How can we help?</label>
            <textarea id="message" name="message" placeholder="Tell us about your question or request..." required></textarea>
          </div>

          <label class="checkbox">
            <input type="checkbox" name="consent">
            I agree to be contacted by the AgroMarketplace team.
          </label>

          <button class="btn btn-primary submit-button" type="submit">Send inquiry</button>
        </form>
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
