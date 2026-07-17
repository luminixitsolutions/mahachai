<?php
/**
 * Create tbl_approval_mail_log for approval notification emails.
 * Run once: php migrations/run_approval_mail_migration.php
 * Or open in browser while logged into the panel (optional).
 */
date_default_timezone_set('Asia/Kolkata');

require_once dirname(__DIR__) . '/config.php';

$sqlFile = __DIR__ . '/approval_mail_notifications.sql';
if (!is_file($sqlFile)) {
    fwrite(STDERR, "SQL file missing: $sqlFile\n");
    exit(1);
}

$sql = file_get_contents($sqlFile);
if ($sql === false || trim($sql) === '') {
    fwrite(STDERR, "Empty SQL file\n");
    exit(1);
}

$ok = $conn->multi_query($sql);
if ($ok) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());

    if (!$conn->errno) {
        echo "OK: approval mail and desktop notification tables ready.\n";
        exit(0);
    }
}

fwrite(STDERR, 'Migration failed: ' . $conn->error . "\n");
exit(1);
