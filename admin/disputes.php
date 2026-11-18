<?php
require_once __DIR__ . '/../database/app.php';
require_role(['admin']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Disputes — Admin</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="admin.css">
</head>
<body>
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
    <div class="admin-layout">
      <aside class="admin-sidebar" aria-label="Dispute filters">
        <div class="sidebar-block">
          <p class="filter-heading">Filter disputes</p>
          <div class="filter-list">
            <button class="filter-chip active" type="button"><span class="filter-graphic graphic-all">A</span>All</button>
            <button class="filter-chip" type="button"><span class="filter-graphic graphic-urgent">!</span>Escalated</button>
            <button class="filter-chip" type="button"><span class="filter-graphic graphic-buyers">D</span>Delivery</button>
            <button class="filter-chip" type="button"><span class="filter-graphic graphic-sellers">P</span>Payment</button>
            <button class="filter-chip" type="button"><span class="filter-graphic graphic-all">Q</span>Quality</button>
          </div>
        </div>
        <div class="sidebar-block">
          <p class="filter-heading">Date range</p>
          <label class="date-filter-group">
            <span>From</span>
            <input type="date" value="2026-08-01">
          </label>
          <label class="date-filter-group">
            <span>To</span>
            <input type="date" value="2026-09-30">
          </label>
          <button class="button button-primary" type="button">Apply filter</button>
        </div>
      </aside>

      <section class="content-area">
        <section class="panel">
          <div class="page-head">
            <div>
              <p class="eyebrow">Admin workspace</p>
              <h1 class="page-title">Dispute center</h1>
            </div>
            <button class="button button-primary" type="button">New review</button>
          </div>
        </section>

        <section class="stats-grid" aria-label="Dispute overview">
          <article class="stat-card">
            <h3>Open cases</h3>
            <div class="stat-value">14</div>
          </article>
          <article class="stat-card">
            <h3>Escalated</h3>
            <div class="stat-value">4</div>
          </article>
          <article class="stat-card">
            <h3>Resolved this week</h3>
            <div class="stat-value">21</div>
          </article>
          <article class="stat-card">
            <h3>Average resolution</h3>
            <div class="stat-value">2.4 days</div>
          </article>
        </section>

        <section class="panel">
          <div class="section-header">
            <h2>Active disputes</h2>
          </div>

          <div class="request-list">
            <article class="request-card">
              <div class="request-header">
                <div>
                  <h3>Late delivery compensation</h3>
                  <p>Buyer: Selim Ahmed • Farmer: Jannat Begum</p>
                </div>
                <span class="status-badge status-flagged">High priority</span>
              </div>
              <div class="request-meta">
                <span>Claim: Tk 12,000</span>
                <span>Order #AG-1048</span>
                <span>Opened: 2026-09-04 08:10</span>
                <span>Updated 2h ago</span>
              </div>
              <p class="request-copy">Delivery was delayed by 18 hours and the buyer claims spoilage on a fresh vegetable shipment. The case is awaiting documentation from the delivery agent.</p>
              <div class="request-actions">
                <button class="button button-primary" type="button">Review</button>
                <button class="button button-secondary" type="button">Message parties</button>
              </div>
            </article>

            <article class="request-card">
              <div class="request-header">
                <div>
                  <h3>Product quality mismatch</h3>
                  <p>Buyer: Tanvir Hossain • Farmer: Abdul Karim</p>
                </div>
                <span class="status-badge status-pending">In review</span>
              </div>
              <div class="request-meta">
                <span>Claim: Tk 8,500</span>
                <span>Order #AG-1014</span>
                <span>Opened: 2026-09-03 14:40</span>
                <span>Updated yesterday</span>
              </div>
              <p class="request-copy">The buyer reported a moisture mismatch and damaged packaging after receiving a rice shipment. Warehouse photo evidence is being evaluated.</p>
              <div class="request-actions">
                <button class="button button-primary" type="button">Review</button>
                <button class="button button-secondary" type="button">Request evidence</button>
              </div>
            </article>

            <article class="request-card">
              <div class="request-header">
                <div>
                  <h3>Payment release dispute</h3>
                  <p>Seller: Rafiq Mia • Buyer: Green Valley Foods</p>
                </div>
                <span class="status-badge status-approved">Resolved</span>
              </div>
              <div class="request-meta">
                <span>Claim: Tk 22,000</span>
                <span>Order #AG-990</span>
                <span>Opened: 2026-08-29 11:20</span>
                <span>Closed 3 days ago</span>
              </div>
              <p class="request-copy">The case was settled after both parties agreed to a partial refund tied to delayed verification of weight at delivery and a revised final invoice.</p>
              <div class="request-actions">
                <button class="button button-secondary" type="button">View summary</button>
              </div>
            </article>
          </div>
        </section>
      </section>
    </div>
  </main>
</body>
</html>
