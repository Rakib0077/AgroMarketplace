<?php
require_once __DIR__ . '/../database/app.php';
require_role(['admin']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Priority Action Detail — Admin</title>
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
            <h1 class="page-title">Priority action detail</h1>
          </div>
          <div class="header-actions">
            <a class="button button-secondary" href="index.php">Back to dashboard</a>
          </div>
        </div>
        <p class="meta-text">Handle with GET method.</p>
      </section>

      <section class="detail-layout single-column-detail">
        <article class="detail-card">
          <div class="section-header">
            <h2>Farmer verification appeals</h2>
            <span class="context-figure danger">High</span>
          </div>

          <div class="detail-meta">
            <div>
              <span>Queue ID</span>
              <strong>FA-2041</strong>
            </div>
            <div>
              <span>Submitted</span>
              <strong>2026-09-04 14:30</strong>
            </div>
            <div>
              <span>Assigned To</span>
              <strong>Policy review team</strong>
            </div>
            <div>
              <span>Priority</span>
              <strong>Urgent</strong>
            </div>
          </div>

          <p class="detail-copy">
            A batch of farmer verification appeals has been flagged for manual policy review. The cases include identity mismatch reports,
            address inconsistencies, and duplicate KYC submissions from the same mobile device. Review is required before final approval to
            prevent compliance risks and maintain buyer trust.
          </p>

          <div class="detail-actions">
            <button class="button button-primary" type="button">Approve</button>
            <button class="button button-secondary" type="button">Cancel Registration</button>
          </div>
        </article>
      </section>
    </section>
  </main>
</body>
</html>
