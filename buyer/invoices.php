<?php
require_once __DIR__ . '/../database/app.php';

$user = require_role(['buyer']);
$database = app_database();
$invoices = $database->getBuyerInvoices((int) $user['id']);
$invoiceSummary = $database->getBuyerInvoiceSummary((int) $user['id']);
$totals = $invoiceSummary;
$refundTotal = $invoiceSummary['refund_total'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Invoices — Buyer</title>
  <link rel="stylesheet" href="../colors.css">
  <link rel="stylesheet" href="../header.css">
  <link rel="stylesheet" href="buyer.css">
</head>
<body class="theme-buyer">
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

  <main class="page-shell">
    <section class="content-area">
      <section class="panel">
        <div class="page-head">
          <div>
            <p class="eyebrow">Buyer workspace</p>
            <h1 class="page-title">Purchase receipts</h1>
          </div>
          <div class="header-actions">
            <button class="button button-primary" type="button">Generate report</button>
          </div>
        </div>
        <p class="meta-text">Review payment records, export reports, and keep track of your recent procurement activity.</p>
      </section>

      <section class="stats-grid">
        <div class="stat-card">
          <h3>Total paid</h3>
          <p class="stat-value"><?= number_format((float) $totals['total_paid'], 2) ?> BDT</p>
        </div>
        <div class="stat-card">
          <h3>Open invoices</h3>
          <p class="stat-value"><?= (int) $totals['open_count'] ?></p>
        </div>
        <div class="stat-card">
          <h3>Refunds</h3>
          <p class="stat-value"><?= number_format($refundTotal, 2) ?> BDT</p>
        </div>
        <div class="stat-card">
          <h3>Last payment</h3>
          <p class="stat-value"><?= $totals['last_invoice'] ? escape_html(substr($totals['last_invoice'], 0, 10)) : 'None' ?></p>
        </div>
      </section>

      <section class="panel">
        <div class="section-header">
          <h2>Invoice history</h2>
          <span class="pill pill-success">Up to date</span>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th>Invoice</th>
              <th>Order</th>
              <th>Commodity</th>
              <th>Amount</th>
              <th>Date</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($invoices as $invoice): ?>
              <tr>
                <td><?= escape_html($invoice['invoice_number']) ?></td>
                <td><?= escape_html($invoice['order_number']) ?></td>
                <td><?= escape_html($invoice['title'] ?? 'Produce') ?></td>
                <td><?= number_format((float) $invoice['total_amount'], 2) ?> <?= escape_html($invoice['currency']) ?></td>
                <td><?= escape_html(substr($invoice['issued_at'], 0, 10)) ?></td>
                <td><span class="pill pill-muted"><?= escape_html(ucfirst($invoice['status'])) ?></span></td>
                <td><a class="button button-secondary" href="transaction.php?id=<?= (int) $invoice['order_id'] ?>">View order</a></td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$invoices): ?><tr><td colspan="7">No invoices have been issued.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </section>
    </section>
  </main>
</body>
</html>
