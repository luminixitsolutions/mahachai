<?php
require_once __DIR__ . '/config.php';
maha_ensure_session_started();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/includes/approval_mail_service.php';

date_default_timezone_set('Asia/Kolkata');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$userId = (int) ($_SESSION['Admin']['id'] ?? 0);
if ($userId < 1) {
    http_response_code(401);
    echo json_encode(array('success' => false, 'message' => 'Unauthorized'));
    exit;
}

if (!approval_desktop_ensure_table($conn)) {
    echo json_encode(array('success' => false, 'message' => 'Notification storage unavailable'));
    exit;
}

$action = strtolower(trim((string) ($_POST['action'] ?? $_GET['action'] ?? 'list')));

if ($action === 'ack') {
    $ids = $_POST['ids'] ?? array();
    if (!is_array($ids)) {
        $ids = explode(',', (string) $ids);
    }
    $ids = array_values(array_filter(array_map('intval', $ids), function ($id) {
        return $id > 0;
    }));
    if ($ids) {
        $idList = implode(',', $ids);
        $conn->query("UPDATE tbl_approval_desktop_notifications
            SET delivered_at='" . date('Y-m-d H:i:s') . "'
            WHERE user_id='$userId' AND id IN ($idList) AND delivered_at IS NULL");
    }
    echo json_encode(array('success' => true));
    exit;
}

$rows = getList("SELECT id, title, message, view_url, created_at
    FROM tbl_approval_desktop_notifications
    WHERE user_id='$userId' AND delivered_at IS NULL
    ORDER BY id ASC
    LIMIT 10");

$notifications = array();
foreach (($rows ?: array()) as $row) {
    $notifications[] = array(
        'id' => (int) $row['id'],
        'title' => (string) $row['title'],
        'message' => (string) $row['message'],
        'view_url' => (string) $row['view_url'],
        'created_at' => (string) $row['created_at'],
    );
}

echo json_encode(array(
    'success' => true,
    'notifications' => $notifications,
), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
