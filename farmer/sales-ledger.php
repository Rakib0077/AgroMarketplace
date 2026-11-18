<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['farmer']);
$database = app_database();
$orders = $database->getFarmerOrders((int) $user['id']);
$summary = $database->getFarmerSalesSummary((int) $user['id']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sales Ledger — Farmer</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="farmer.css">
</head>
<body>
  <header class="site-header">
    <div class="site-header-inner">
      <a class="brand" href="../index.php">AgroMarketplace</a>
      <nav class="nav" aria-label="Farmer navigation">
        <a class="nav-link" href="index.php">Home</a>
        <a class="nav-link" href="profile/profile-self.php">Profile</a>
        <a class="nav-link" href="marketplace.php">Marketplace</a>
        <a class="nav-link" href="create-listing.php">Listing</a>
        <a class="nav-link" href="bids.php">Bids</a>
        <a class="nav-link" href="inventory.php">Inventory</a>
        <a class="nav-link" href="sales-ledger.php">Sales Ledger</a>
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
            <p class="eyebrow">Farmer workspace</p>
            <h1 class="page-title">30-day sales history</h1>
          </div>
          <div class="header-actions">
            <button class="button button-primary" type="button">Generate report</button>
          </div>
        </div>
        <p class="meta-text">Review completed settlements, pending payouts, and daily trade movement to track revenue performance.</p>
      </section>

      <section class="stats-grid">
        <div class="stat-card"><h3>Gross sales</h3><p class="stat-value"><?= number_format((float) $summary['gross_sales'], 2) ?> BDT</p></div>
        <div class="stat-card"><h3>Settled</h3><p class="stat-value"><?= number_format((float) $summary['settled'], 2) ?> BDT</p></div>
        <div class="stat-card"><h3>Pending</h3><p class="stat-value"><?= number_format((float) $summary['pending'], 2) ?> BDT</p></div>
      </section>

      <section class="content-grid">
        <article class="context-card">
          <div class="section-header">
            <h2>Recent transactions</h2>
            <span class="pill pill-success">Updated</span>
          </div>
          <ul class="context-list">
            <?php foreach (array_slice($orders, 0, 8) as $order): ?>
              <li class="transaction-item">
                <a class="transaction-link" href="transaction-details.php?id=<?= (int) $order['id'] ?>">
                  <div>
                    <strong><?= escape_html($order['title'] ?? 'Produce order') ?> · <?= escape_html($order['order_number']) ?></strong>
                    <small><?= number_format((float) $order['quantity'], 3) ?> <?= escape_html($order['quantity_unit']) ?> · <?= escape_html($order['payment_status']) ?> · Buyer <?= escape_html($order['buyer_name']) ?></small>
                  </div>
                  <span class="context-figure"><?= number_format((float) $order['total_amount'], 2) ?> <?= escape_html($order['currency']) ?></span>
                </a>
              </li>
            <?php endforeach; ?>
            <?php if (!$orders): ?><li>No sales have been recorded.</li><?php endif; ?>
          </ul>
        </article>

        <article class="context-card">
          <div class="section-header">
            <h2>Revenue summary</h2>
            <span class="pill pill-muted">This month</span>
          </div>
          <div class="mini-metrics">
            <div><span>Orders</span><strong><?= count($orders) ?></strong></div>
            <div><span>Average order</span><strong><?= number_format($orders ? (float) $summary['gross_sales'] / count($orders) : 0, 2) ?> BDT</strong></div>
            <div><span>Unpaid / escrow</span><strong><?= number_format((float) $summary['pending'], 2) ?> BDT</strong></div>
            <div><span>Released</span><strong><?= number_format((float) $summary['settled'], 2) ?> BDT</strong></div>
          </div>
        </article>
      </section>
    </section>
  </main>
</body>
</html>
