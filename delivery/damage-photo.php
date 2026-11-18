<?php
require_once __DIR__ . '/../database/app.php';
$user = require_role(['delivery']);
$photoId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$photoId) {
  http_response_code(404);
  exit;
}

$photo = app_database()->getDeliveryDamagePhoto($photoId, (int) $user['id']);
if (!$photo || !in_array($photo['mime_type'], ['image/jpeg', 'image/png', 'image/webp'], true)) {
  http_response_code(404);
  exit;
}

header('Content-Type: ' . $photo['mime_type']);
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, max-age=300');
echo $photo['image_data'];