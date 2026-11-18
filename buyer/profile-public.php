<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Farmer Profile — Public</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="profile.css">
</head>
<body class="theme-farmer">
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="../index.php">AgroMarketplace</a>
      <nav class="nav" aria-label="Buyer navigation">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="profile-self.php">Profile</a>
        <a class="nav-link" href="marketplace.php">Marketplace</a>
        <a class="nav-link" href="bids.php">My Bids</a>
        <a class="nav-link" href="orders.php">Orders</a>
        <a class="nav-link" href="invoices.php">Invoices</a>
        <a class="nav-link" href="chat.php">Chat</a>
      </nav>
      <div class="site-header-actions">
        <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
      </div>
    </div>
  </header>

  <main class="profile-shell">
    <section class="profile-card">
      <div class="profile-hero">
        <div class="profile-identity">
          <img class="profile-avatar-img" src="../../assets/guest-avatar.png" alt="Ayesha Rahman Profile Picture">
          <div>
            <h1 class="profile-name">Ayesha Rahman</h1>
            <p class="profile-role">Organic Rice Farmer</p>
          </div>
        </div>
        <div class="hero-status">
          <span class="status-badge status-active">Verified seller</span>
        </div>
      </div>

      <div class="profile-body">
        <div class="profile-section">
          <h2 class="section-title">Public information</h2>
          <div class="profile-grid">
            <div class="profile-detail"><span>Business</span><strong>Rahman Agro Farms</strong></div>
            <div class="profile-detail"><span>Role</span><strong>Farmer / Buyer</strong></div>
            <div class="profile-detail"><span>Location</span><strong>Faridpur, Bangladesh</strong></div>
            <div class="profile-detail"><span>Joined</span><strong>2021</strong></div>
            <div class="profile-detail"><span>Specialty</span><strong>Rice, wheat, lentils</strong></div>
            <div class="profile-detail"><span>Response time</span><strong>Within 2 hours</strong></div>
          </div>

          <p class="profile-about">
            Produces high-quality organic rice and grain lots for local and regional buyers. Also participates in procurement and market trading to secure fair pricing and faster supply cycles across Bangladesh.
          </p>
        </div>

        <div class="profile-stack">
          <div class="profile-section">
            <h2 class="section-title">Highlights</h2>
            <ul class="profile-list">
              <li class="profile-item"><div><strong>Crop quality</strong><br><small>Grade A harvests</small></div><span class="status-badge status-active">Excellent</span></li>
              <li class="profile-item"><div><strong>On-time delivery</strong><br><small>98% handoff rate</small></div><span class="status-badge status-active">Reliable</span></li>
              <li class="profile-item"><div><strong>Buyer rating</strong><br><small>4.9 from recent trades</small></div><span class="status-badge status-active">Top rated</span></li>
            </ul>
          </div>

          <div class="profile-section">
            <h2 class="section-title">Contact</h2>
            <div class="metric-box"><strong>ayesha@rahmanagro.com</strong><span>Email for crop orders and direct buying inquiries</span></div>
            <div class="metric-box" style="margin-top:12px;"><strong>+880 1712-456789</strong><span>Phone for urgent market coordination</span></div>
          </div>
        </div>
      </div>
    </section>
  </main>
</body>
</html>