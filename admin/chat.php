<?php
require_once __DIR__ . '/../database/app.php';
$admin = require_role(['admin']);
$database = app_database();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_token();
  $conversationId = (int) ($_POST['conversation_id'] ?? 0);
  $body = trim($_POST['body'] ?? '');

  if ($conversationId < 1 || $body === '' || strlen($body) > 5000) {
    $error = 'Choose a conversation and enter a message of up to 5,000 characters.';
  } else {
    try {
      $database->sendAdminConversationMessage($conversationId, (int) $admin['id'], $body);
      redirect_to('chat.php?conversation=' . $conversationId);
    } catch (Throwable $exception) {
      $error = 'The message could not be sent. Please try again.';
    }
  }
}

$searchValue = $_GET['q'] ?? '';
$dateFromValue = $_GET['date_from'] ?? '';
$dateToValue = $_GET['date_to'] ?? '';
$filters = [
  'q' => is_string($searchValue) ? trim(substr($searchValue, 0, 120)) : '',
  'date_from' => is_string($dateFromValue) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFromValue) ? $dateFromValue : '',
  'date_to' => is_string($dateToValue) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateToValue) ? $dateToValue : '',
];
$allConversations = $database->getAdminConversations();
$conversations = array_values(array_filter($allConversations, static function ($conversation) use ($filters) {
  $activityDate = substr($conversation['last_activity'], 0, 10);
  if ($filters['date_from'] !== '' && $activityDate < $filters['date_from']) {
    return false;
  }
  if ($filters['date_to'] !== '' && $activityDate > $filters['date_to']) {
    return false;
  }
  if ($filters['q'] === '') {
    return true;
  }

  foreach ($conversation['participants'] as $participant) {
    if (stripos($participant['name'], $filters['q']) !== false || stripos($participant['role'], $filters['q']) !== false) {
      return true;
    }
  }

  return stripos($conversation['order_number'] ?? '', $filters['q']) !== false;
}));

$requestedConversationId = filter_var($_GET['conversation'] ?? null, FILTER_VALIDATE_INT);
$selected = null;
foreach ($conversations as $conversation) {
  if ((int) $conversation['id'] === (int) $requestedConversationId) {
    $selected = $conversation;
    break;
  }
}
if ($selected === null && $conversations) {
  $selected = $conversations[0];
}

$conversationId = $selected ? (int) $selected['id'] : 0;
$avatarName = $selected['participants'][0]['name'] ?? 'Admin';
$avatarText = strtoupper(substr($avatarName, 0, 2));
$messages = $selected ? $database->getConversationMessages($conversationId) : [];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Messages — Admin</title>
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

  <main class="chat-shell">
    <aside class="chat-sidebar" aria-label="Message filters and conversations">
      <div class="sidebar-card filter-card">
        <p class="filter-heading">Filter chats</p>
        <form action="chat.php" method="get">
          <label class="date-filter-group">
            <span>From</span>
            <input type="date" name="date_from" value="<?= escape_html($filters['date_from']) ?>">
          </label>
          <label class="date-filter-group">
            <span>To</span>
            <input type="date" name="date_to" value="<?= escape_html($filters['date_to']) ?>">
          </label>
          <label class="search-filter-group">
            <span>Search user</span>
            <input type="search" name="q" value="<?= escape_html($filters['q']) ?>" placeholder="Type a name">
          </label>
          <button class="btn btn-primary" type="submit">Apply</button>
          <a class="button button-secondary" href="chat.php">Clear</a>
        </form>
      </div>

      <div class="sidebar-card thread-card">
        <div class="thread-list">
          <?php foreach ($conversations as $conversation): ?>
            <?php $firstParticipant = $conversation['participants'][0] ?? ['name' => 'Conversation']; ?>
            <div class="thread-item <?= (int) $conversation['id'] === $conversationId ? 'active' : '' ?>">
              <span class="thread-avatar avatar-rice"><?= escape_html(strtoupper(substr($firstParticipant['name'], 0, 2))) ?></span>
              <span class="thread-copy">
                <?php foreach ($conversation['participants'] as $participant): ?>
                  <a class="profile-link" href="user-profile.php?id=<?= (int) $participant['id'] ?>"><strong><?= escape_html($participant['name']) ?></strong></a>
                <?php endforeach; ?>
                <a href="chat.php?conversation=<?= (int) $conversation['id'] ?>"><small><?= escape_html($conversation['order_number'] ?: 'Conversation #' . $conversation['id']) ?> · <?= escape_html(date('Y-m-d H:i', strtotime($conversation['last_activity']))) ?></small></a>
              </span>
            </div>
          <?php endforeach; ?>
          <?php if (!$conversations): ?><p class="empty-state">No conversations match these filters.</p><?php endif; ?>
        </div>
      </div>
    </aside>

    <section class="chat-panel" aria-label="Chat conversation">
      <header class="chat-header">
        <div class="chat-user">
          <span class="chat-avatar avatar-rice"><?= escape_html($avatarText) ?></span>
          <div>
            <h1><?= $selected ? escape_html($selected['order_number'] ?: 'Conversation #' . $selected['id']) : 'No conversation selected' ?></h1>
            <p>
              <?php if ($selected): ?>
                <?php foreach ($selected['participants'] as $index => $participant): ?>
                  <?= $index > 0 ? ' · ' : '' ?><a class="profile-link" href="user-profile.php?id=<?= (int) $participant['id'] ?>"><?= escape_html($participant['name']) ?></a>
                <?php endforeach; ?>
              <?php else: ?>Choose a conversation to view its messages.<?php endif; ?>
            </p>
          </div>
        </div>
        <span class="chat-status">Admin support</span>
      </header>

      <div class="chat-thread">
        <?php foreach ($messages as $message): ?>
          <div class="message-row <?= (int) $message['sender_id'] === (int) $admin['id'] ? 'outgoing' : 'incoming' ?>">
            <div class="message-bubble">
              <strong><?= escape_html($message['fullname'] ?: $message['username']) ?> · <?= escape_html(ucfirst($message['role'])) ?></strong><br>
              <?= nl2br(escape_html($message['body'])) ?>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if ($error !== ''): ?><p role="alert"><?= escape_html($error) ?></p><?php endif; ?>
        <?php if ($selected && !$messages): ?><p>No messages yet. Start the conversation.</p><?php endif; ?>
        <?php if (!$selected): ?><p>No conversations are available for these filters.</p><?php endif; ?>
      </div>

      <form class="message-compose" action="chat.php?conversation=<?= $conversationId ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="conversation_id" value="<?= $conversationId ?>">
        <label class="message-input-wrap" aria-label="Type a message">
          <input type="text" name="body" maxlength="5000" placeholder="Type your message..." <?= $selected ? 'required' : 'disabled' ?>>
        </label>
        <button class="btn btn-primary" type="submit" <?= $selected ? '' : 'disabled' ?>>Send</button>
      </form>
    </section>
  </main>
</body>
</html>
