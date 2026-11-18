<?php
require_once __DIR__ . '/../database/app.php';
require_role(['admin']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Dashboard — AgroMarketplace</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="admin.css">
</head>
<body class="theme-admin">
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="../index.php">AgroMarketplace</a>
      <nav class="nav" aria-label="Admin navigation">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="marketplace.php">Marketplace</a>
        <a class="nav-link" href="user-verification.php">User Verification</a>
        <a class="nav-link" href="commission-manager.php">Commission Manager</a>
        <a class="nav-link" href="disputes.php">Disputes</a>
        <a class="nav-link" href="chat.php">Chat</a>
      </nav>
      <div class="site-header-actions">
        <a class="btn btn-ghost" href="../auth/logout.php">Logout</a>
      </div>
    </div>
  </header>

  <main class="page-shell">
    <section class="content-area">
      <section class="panel">
        <div class="page-head">
          <div>
            <p class="eyebrow">Admin workspace</p>
            <h1 class="page-title">Platform operations</h1>
          </div>
          <div class="header-actions">
            <button class="button button-primary" type="button">System health</button>
            <button class="button button-secondary" type="button">Approvals</button>
          </div>
        </div>
        <p class="meta-text">Monitor platform growth, user trust, and settlement health from a single system overview.</p>
      </section>
      <section class="stats-grid">
        <div class="stat-card"><h3>Platform GMV</h3><p class="stat-value">Tk 42,850,000</p></div>
        <div class="stat-card"><h3>Commission Rev</h3><p class="stat-value">Tk 1,285,500</p></div>
        <div class="stat-card"><h3>Total Traded</h3><p class="stat-value">1,120 MT</p></div>
      </section>

      <section class="content-grid">
        <article class="context-card">
          <div class="section-header">
            <h2>Priority actions</h2>
            <span class="pill pill-success">Live</span>
          </div>
          <ul class="context-list">
            <li class="priority-item">
              <a class="priority-link" href="priority-action-detail.php?id=1">
                <div>
                  <strong>12 farmer verification appeals</strong>
                  <small>Awaiting policy review</small>
                </div>
                <span class="context-figure danger">High</span>
              </a>
            </li>
            <li class="priority-item">
              <a class="priority-link" href="priority-action-detail.php?id=2">
                <div>
                  <strong>4 commission disputes</strong>
                  <small>Escalated to payout team</small>
                </div>
                <span class="context-figure warn">Medium</span>
              </a>
            </li>
            <li class="priority-item">
              <a class="priority-link" href="priority-action-detail.php?id=3">
                <div>
                  <strong>9 suspicious listings</strong>
                  <small>Pending moderator check</small>
                </div>
                <span class="context-figure">Review</span>
              </a>
            </li>
          </ul>
        </article>

        <article class="context-card">
          <div class="section-header">
            <h2>Platform health</h2>
            <span class="pill pill-muted">Updated 8m ago</span>
          </div>
          <div class="mini-metrics">
            <div><span>Uptime</span><strong>99.7%</strong></div>
            <div><span>On-time delivery</span><strong>94.4%</strong></div>
            <div><span>New buyers</span><strong>138</strong></div>
            <div><span>Buyer satisfaction</span><strong>4.8/5</strong></div>
          </div>
        </article>
      </section>
    </section>
  </main>
</body>
</html>
