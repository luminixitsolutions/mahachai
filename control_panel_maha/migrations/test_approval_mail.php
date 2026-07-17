<?php
/**
 * Dry-run / preview approval mail templates (does not send unless ?send=1).
 * Open: /control_panel_maha/migrations/test_approval_mail.php?key=maha-approval-mail&module=leave&id=1
 * Optional: &send=1 to actually send via SMTP.
 */
date_default_timezone_set('Asia/Kolkata');
$TEST_KEY = 'maha-approval-mail';
if (($_GET['key'] ?? '') !== $TEST_KEY) {
    http_response_code(403);
    die('Forbidden. Use ?key=' . $TEST_KEY);
}

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/approval_mail_service.php';

$module = trim((string) ($_GET['module'] ?? 'leave'));
$id = (int) ($_GET['id'] ?? 0);
$stage = trim((string) ($_GET['stage'] ?? 'manager'));
$decision = (string) ($_GET['decision'] ?? '1');
$send = isset($_GET['send']) && $_GET['send'] === '1';
$actor = (int) ($_SESSION['Admin']['id'] ?? ($_GET['actor'] ?? 1));

header('Content-Type: text/html; charset=utf-8');
echo '<h2>Approval Mail Test</h2>';
echo '<p>Timezone: Asia/Kolkata · ' . date('Y-m-d H:i:s') . '</p>';

approval_mail_ensure_log_table($conn);
echo '<p>Log table: ready</p>';

if ($id < 1) {
    echo '<p>Provide &id=REQUEST_ID</p>';
    echo '<p>Modules: employee_expense, petty_cash, petty_limit, vendor_expense, nso_vendor_expense, resign, advance_salary, advance_request, leave, attendance, cash_book, vendor_invoice, resignation_clearance</p>';
    exit;
}

$ctx = approval_mail_load_context($conn, $module, $id, $stage, $decision);
if (!$ctx) {
    echo '<p style="color:red">Could not load context for ' . htmlspecialchars($module) . ' #' . $id . '</p>';
    exit;
}

$actorRow = approval_mail_user($conn, $actor);
$actorName = approval_mail_user_name($actorRow) ?: 'Test Actor';
$html = approval_mail_build_html($ctx, $actorName, approval_mail_role_label($stage), 'Test remarks from preview');
$alt = approval_mail_build_alt($ctx, $actorName, approval_mail_role_label($stage), 'Test remarks from preview');

echo '<h3>Context</h3><pre>' . htmlspecialchars(print_r(array(
    'to' => $ctx['requester_email'],
    'name' => $ctx['requester_name'],
    'request_no' => $ctx['request_no'],
    'status' => $ctx['current_status'],
    'next' => $ctx['next_level'],
    'final' => $ctx['is_final'],
    'cc' => approval_mail_fixed_cc(),
), true)) . '</pre>';

echo '<h3>Preview</h3><div style="border:1px solid #ccc;padding:12px;background:#eee">' . $html . '</div>';
echo '<h3>Alt text</h3><pre>' . htmlspecialchars($alt) . '</pre>';

if ($send) {
    $ok = approval_mail_notify($conn, $module, $id, $stage, $decision, $actor, 'Test remarks from preview');
    echo $ok ? '<p style="color:green">Send attempted (check tbl_approval_mail_log).</p>' : '<p style="color:orange">Send returned false (missing email / duplicate / SMTP error — check log).</p>';
} else {
    echo '<p><a href="?key=' . urlencode($TEST_KEY) . '&module=' . urlencode($module) . '&id=' . $id . '&stage=' . urlencode($stage) . '&decision=' . urlencode($decision) . '&send=1">Send for real</a></p>';
}
