<?php
/**
 * Retry failed approval notification emails (status=failed).
 * CLI: php migrations/retry_failed_approval_mails.php
 * Or browser with ?key=maha-approval-mail
 */
date_default_timezone_set('Asia/Kolkata');

if (php_sapi_name() !== 'cli') {
    if (($_GET['key'] ?? '') !== 'maha-approval-mail') {
        http_response_code(403);
        die('Forbidden');
    }
}

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/approval_mail_service.php';

approval_mail_ensure_log_table($conn);
$limit = 50;
$rows = getList("SELECT * FROM tbl_approval_mail_log WHERE status='failed' ORDER BY id ASC LIMIT $limit");
if (!$rows) {
    echo "No failed mails.\n";
    exit(0);
}

$okN = 0;
$failN = 0;
foreach ($rows as $row) {
    $module = $row['module'];
    $requestId = (int) $row['request_id'];
    $stage = $row['stage'];
    $decision = (stripos($row['decision'], 'Reject') !== false) ? '2' : '1';
    $actor = (int) $row['actor_user_id'];
    // Clear dedupe so retry can insert/send again — update same row instead
    $conn->query("UPDATE tbl_approval_mail_log SET dedupe_key=CONCAT(dedupe_key,'-retry-',id), status='pending' WHERE id='" . (int) $row['id'] . "'");
    $sent = approval_mail_notify($conn, $module, $requestId, $stage, $decision, $actor, 'Retry send');
    if ($sent) {
        $okN++;
        echo "OK #{$row['id']} $module/$requestId\n";
    } else {
        $failN++;
        echo "FAIL #{$row['id']} $module/$requestId\n";
    }
}
echo "Done. ok=$okN fail=$failN\n";
