<?php
require_once __DIR__ . '/../database/app.php';
require_role(['admin']);
$database = app_database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $action = $_POST['action'] ?? '';

  if ($action === 'save_rule') {
    $ruleTypeValue = $_POST['rule_type'] ?? '';
    $ruleType = is_string($ruleTypeValue) ? $ruleTypeValue : '';
    $categoryValue = $_POST['category_id'] ?? '';
    $categoryId = $ruleType === 'sale' && is_scalar($categoryValue) ? filter_var($categoryValue, FILTER_VALIDATE_INT) : null;
    $rateValue = $_POST['rate_percent'] ?? '';
    $fixedValue = $_POST['fixed_amount'] ?? '';
    $rate = is_scalar($rateValue) ? filter_var($rateValue, FILTER_VALIDATE_FLOAT) : false;
    $fixedAmount = is_scalar($fixedValue) ? filter_var($fixedValue, FILTER_VALIDATE_FLOAT) : false;
    $effectiveText = $_POST['effective_from'] ?? '';
    $endText = $_POST['effective_until'] ?? '';
    $effectiveDate = is_string($effectiveText) ? DateTime::createFromFormat('Y-m-d\\TH:i', $effectiveText) : false;
    $effectiveErrors = DateTime::getLastErrors();
    $endDate = is_string($endText) && $endText !== '' ? DateTime::createFromFormat('Y-m-d\\TH:i', $endText) : false;
    $endErrors = DateTime::getLastErrors();
    $effectiveAt = $effectiveDate instanceof DateTime ? $effectiveDate->format('Y-m-d H:i:s') : '';
    $endAt = $endDate instanceof DateTime ? $endDate->format('Y-m-d H:i:s') : null;
    $databaseNow = $database->getDatabaseNow();
    $validDate = $effectiveDate instanceof DateTime
      && ($effectiveErrors === false || ($effectiveErrors['warning_count'] === 0 && $effectiveErrors['error_count'] === 0))
      && $effectiveDate->format('Y-m-d\\TH:i') === $effectiveText
      && $effectiveAt >= $databaseNow
      && ($endText === '' || ($endDate instanceof DateTime
        && ($endErrors === false || ($endErrors['warning_count'] === 0 && $endErrors['error_count'] === 0))
        && $endDate->format('Y-m-d\\TH:i') === $endText
        && $endAt > $effectiveAt));

    if (!in_array($ruleType, ['sale', 'delivery'], true)
        || ($ruleType === 'sale' && (!$categoryId || $categoryId < 1))
        || $rate === false || $rate < 0 || $rate > 100
        || $fixedAmount === false || $fixedAmount < 0
        || !$validDate) {
      set_flash('error', 'Enter a valid commission type, rate, amount, and date range.');
    } else {
      try {
        $database->saveCommissionRule($ruleType, $categoryId, (float) $rate, (float) $fixedAmount, $effectiveAt, $endAt);
        set_flash('success', 'Commission rule scheduled.');
      } catch (Throwable $exception) {
        set_flash('error', 'The commission rule could not be saved. Please try again.');
      }
    }
  } elseif ($action === 'disable_rule') {
    $ruleId = filter_var($_POST['rule_id'] ?? null, FILTER_VALIDATE_INT);
    if ($ruleId) {
      $disabled = $database->disableCommissionRule((int) $ruleId) === 1;
      set_flash($disabled ? 'success' : 'error', $disabled ? 'Rule deactivated.' : 'That active rule could not be deactivated.');
    } else {
      set_flash('error', 'The selected rule is invalid.');
    }
  } elseif ($action === 'reactivate_rule') {
    $ruleId = filter_var($_POST['rule_id'] ?? null, FILTER_VALIDATE_INT);
    if ($ruleId) {
      if ($database->reactivateCommissionRule((int) $ruleId)) {
        set_flash('success', 'Rule reactivated from now.');
      } else {
        set_flash('error', 'Only an inactive or ended rule can be reactivated.');
      }
    } else {
      set_flash('error', 'The selected rule is invalid.');
    }
  } elseif ($action === 'cancel_rule') {
    $ruleId = filter_var($_POST['rule_id'] ?? null, FILTER_VALIDATE_INT);
    if ($ruleId) {
      $cancelled = $database->cancelCommissionRule((int) $ruleId) === 1;
      set_flash($cancelled ? 'success' : 'error', $cancelled ? 'Rule permanently cancelled.' : 'The selected rule could not be found.');
    } else {
      set_flash('error', 'The selected rule is invalid.');
    }
  } else {
    set_flash('error', 'Unknown commission action.');
  }

  $queryString = http_build_query($_GET);
  redirect_to('commission-manager.php' . ($queryString === '' ? '' : '?' . $queryString));
}

$dateFromValue = $_GET['date_from'] ?? '';
$dateToValue = $_GET['date_to'] ?? '';
$categoryValue = $_GET['category_id'] ?? '';
$typeValue = $_GET['type'] ?? '';
$filters = [
  'date_from' => is_string($dateFromValue) && preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $dateFromValue) ? $dateFromValue : '',
  'date_to' => is_string($dateToValue) && preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $dateToValue) ? $dateToValue : '',
  'category_id' => filter_var($categoryValue, FILTER_VALIDATE_INT) ?: '',
  'type' => is_string($typeValue) && in_array($typeValue, ['sale', 'delivery'], true) ? $typeValue : '',
];
$report = $database->getCommissionReport($filters);
$categories = $report['categories'];
$salesSummary = $report['sales_summary'];
$deliverySummary = $report['delivery_summary'];
$commissionCollected = $report['commission_collected'];
$ledger = $report['ledger'];
$rules = $report['rules'];
$notice = take_flash();
$databaseNow = $report['database_now'];
$defaultDate = DateTime::createFromFormat('Y-m-d H:i:s', $databaseNow);
$effectiveDefault = $defaultDate->modify('+1 minute')->format('Y-m-d\\TH:i');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Commission Manager — Admin</title>
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
            <h1 class="page-title">Commission manager</h1>
          </div>
          <p class="meta-text">Sales and delivery commissions are recorded separately for each order.</p>
        </div>
        <?php if ($notice): ?>
          <p class="verification-notice <?= ($notice['type'] ?? '') === 'error' ? 'is-error' : '' ?>" role="status"><?= escape_html($notice['message']) ?></p>
        <?php endif; ?>
        <form class="commission-filters" method="get" action="commission-manager.php">
          <label class="field-group"><span>From</span><input type="date" name="date_from" value="<?= escape_html($filters['date_from']) ?>"></label>
          <label class="field-group"><span>To</span><input type="date" name="date_to" value="<?= escape_html($filters['date_to']) ?>"></label>
          <label class="field-group"><span>Category</span>
            <select name="category_id"><option value="">All categories</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id'] ?>" <?= (string) $filters['category_id'] === (string) $category['id'] ? 'selected' : '' ?>><?= escape_html($category['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label class="field-group"><span>Ledger</span>
            <select name="type">
              <option value="">All commission activity</option>
              <option value="sale" <?= $filters['type'] === 'sale' ? 'selected' : '' ?>>Sales commission</option>
              <option value="delivery" <?= $filters['type'] === 'delivery' ? 'selected' : '' ?>>Delivery commission</option>
            </select>
          </label>
          <button class="button button-primary" type="submit">Apply filters</button>
          <a class="button button-secondary" href="commission-manager.php">Clear</a>
        </form>
      </section>

      <section class="stats-grid" aria-label="Commission overview">
        <article class="stat-card"><h3>Sales volume</h3><div class="stat-value">BDT <?= number_format((float) $salesSummary['sales_volume'], 2) ?></div><p><?= (int) $salesSummary['order_count'] ?> orders</p></article>
        <article class="stat-card"><h3>Sales commission</h3><div class="stat-value">BDT <?= number_format((float) $salesSummary['sales_commission'], 2) ?></div></article>
        <article class="stat-card"><h3>Delivery commission</h3><div class="stat-value">BDT <?= number_format((float) $deliverySummary['delivery_commission'], 2) ?></div><p><?= (int) $deliverySummary['delivery_count'] ?> jobs · <?= (int) $deliverySummary['completed_deliveries'] ?> delivered</p></article>
        <article class="stat-card"><h3>Commission collected</h3><div class="stat-value">BDT <?= number_format($commissionCollected, 2) ?></div></article>
      </section>

      <section class="panel">
        <div class="section-header"><h2>Commission activity</h2><span class="pill pill-muted"><?= count($ledger) ?> orders</span></div>
        <div class="table-wrap">
          <table class="data-table commission-ledger">
            <thead><tr><th>Order</th><th>Buyer / farmer</th><th>Crop</th><th>Sales</th><th>Sale fee</th><th>Delivery fee</th><th>Collected</th><th>Delivery</th></tr></thead>
            <tbody>
              <?php foreach ($ledger as $row): ?>
                <tr>
                  <td><strong><?= escape_html($row['order_number']) ?></strong><small><?= escape_html(date('Y-m-d', strtotime($row['created_at']))) ?></small></td>
                  <td><?= escape_html($row['buyer_name'] ?: 'Buyer') ?><small>Farmer: <?= escape_html($row['farmer_name'] ?: 'Farmer') ?></small></td>
                  <td><?= escape_html($row['title'] ?: 'Listing unavailable') ?><small><?= escape_html($row['category'] ?: 'Uncategorized') ?></small></td>
                  <td><?= escape_html($row['currency']) ?> <?= number_format((float) $row['subtotal'], 2) ?></td>
                  <td><?= escape_html($row['currency']) ?> <?= number_format((float) $row['sales_commission'], 2) ?></td>
                  <td><?= escape_html($row['currency']) ?> <?= number_format((float) $row['delivery_commission'], 2) ?></td>
                  <td><?= escape_html($row['currency']) ?> <?= number_format((float) $row['collected_amount'], 2) ?></td>
                  <td><?= escape_html($row['delivery_status'] ?: 'No job') ?><small><?= escape_html($row['driver_names'] ?: 'Unassigned') ?></small></td>
                </tr>
              <?php endforeach; ?>
              <?php if ($ledger === []): ?><tr><td colspan="8" class="empty-state">No commission activity matches these filters.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <section class="panel">
        <div class="section-header"><h2>Add commission rule</h2></div>
        <form class="form-grid commission-rule-form" method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="save_rule">
          <label class="field-group"><span>Commission type</span>
            <select name="rule_type" required><option value="sale">Sale by category</option><option value="delivery">Delivery job</option></select>
          </label>
          <label class="field-group"><span>Category (sales only)</span>
            <select name="category_id"><option value="">Choose category</option>
              <?php foreach ($categories as $category): ?><option value="<?= (int) $category['id'] ?>"><?= escape_html($category['name']) ?></option><?php endforeach; ?>
            </select>
          </label>
          <label class="field-group"><span>Rate (% of order subtotal)</span><input type="number" name="rate_percent" min="0" max="100" step="0.01" value="0" required></label>
          <label class="field-group"><span>Fixed amount (BDT per order/job)</span><input type="number" name="fixed_amount" min="0" step="0.01" value="0" required></label>
          <label class="field-group"><span>Effective from</span><input type="datetime-local" name="effective_from" value="<?= escape_html($effectiveDefault) ?>" required></label>
          <label class="field-group"><span>End date and time (optional)</span><input type="datetime-local" name="effective_until"></label>
          <div class="commission-form-actions"><button class="button button-primary" type="submit">Schedule rule</button></div>
        </form>
      </section>

      <section class="panel">
        <div class="section-header"><h2>Rule schedule</h2><span class="pill pill-muted"><?= count($rules) ?> rules</span></div>
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr><th>Type</th><th>Category</th><th>Rate</th><th>Fixed BDT</th><th>Starts</th><th>Ends</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach ($rules as $rule): ?>
                <?php
                  $isScheduled = $rule['is_active'] && $rule['effective_from'] > $databaseNow;
                  $hasEnded = $rule['effective_until'] && $rule['effective_until'] <= $databaseNow;
                  $isScheduledToEnd = !$rule['is_active'] && $rule['effective_until'] && $rule['effective_until'] > $databaseNow;
                  $ruleStatus = $isScheduled ? 'Scheduled' : ($isScheduledToEnd ? 'Scheduled to end' : ($hasEnded ? 'Ended' : ($rule['is_active'] ? 'Active' : 'Inactive')));
                  $ruleStatusClass = ($isScheduled || $isScheduledToEnd) ? 'status-pending' : ($hasEnded ? 'status-rejected' : ($rule['is_active'] ? 'status-approved' : 'status-rejected'));
                  $canReactivate = !$rule['is_active'] || $hasEnded;
                ?>
                <tr>
                  <td><?= $rule['rule_type'] === 'delivery' ? 'Delivery' : 'Sale' ?></td>
                  <td><?= escape_html($rule['rule_type'] === 'delivery' ? 'All delivery jobs' : ($rule['category'] ?: 'All categories')) ?></td>
                  <td><?= number_format((float) $rule['rate_percent'], 2) ?>%</td>
                  <td><?= escape_html($rule['currency']) ?> <?= number_format((float) $rule['fixed_amount'], 2) ?></td>
                  <td><?= escape_html(date('Y-m-d H:i', strtotime($rule['effective_from']))) ?></td>
                  <td><?= $rule['effective_until'] ? escape_html(date('Y-m-d H:i', strtotime($rule['effective_until']))) : 'No end' ?></td>
                  <td><span class="status-badge <?= $ruleStatusClass ?>"><?= $ruleStatus ?></span></td>
                  <td>
                    <div class="commission-rule-actions">
                      <?php if ($rule['is_active'] && !$hasEnded): ?>
                        <form method="post" class="inline-action-form">
                          <?= csrf_field() ?><input type="hidden" name="action" value="disable_rule"><input type="hidden" name="rule_id" value="<?= (int) $rule['id'] ?>">
                          <button class="button button-secondary" type="submit">Deactivate</button>
                        </form>
                      <?php endif; ?>
                      <?php if ($canReactivate): ?>
                        <form method="post" class="inline-action-form">
                          <?= csrf_field() ?><input type="hidden" name="action" value="reactivate_rule"><input type="hidden" name="rule_id" value="<?= (int) $rule['id'] ?>">
                          <button class="button button-primary" type="submit">Reactivate</button>
                        </form>
                      <?php endif; ?>
                      <form method="post" class="inline-action-form" onsubmit="return confirm('Permanently cancel this commission rule? This cannot be undone.');">
                        <?= csrf_field() ?><input type="hidden" name="action" value="cancel_rule"><input type="hidden" name="rule_id" value="<?= (int) $rule['id'] ?>">
                        <button class="button button-danger" type="submit">Cancel</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if ($rules === []): ?><tr><td colspan="8" class="empty-state">No commission rules configured.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </section>
  </main>
</body>
</html>
