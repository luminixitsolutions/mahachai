<?php
/**
 * Ticket Management System — shared helpers (control panel + employee app).
 */

require_once __DIR__ . '/ticket_image_pdf.php';

if (!function_exists('tkt_esc')) {
    function tkt_esc($conn, $val)
    {
        return mysqli_real_escape_string($conn, (string) $val);
    }
}

if (!function_exists('tkt_h')) {
    function tkt_h($s)
    {
        return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('tkt_require_tables')) {
    function tkt_require_tables($conn)
    {
        $r = @$conn->query("SHOW TABLES LIKE 'tbl_ticket_status'");
        if (!$r || $r->num_rows < 1) {
            return 'Ticket tables not found. Run migrations/ticket_management_system.sql first.';
        }
        return '';
    }
}

if (!function_exists('tkt_get_setting')) {
    function tkt_get_setting($conn, $key, $default = '')
    {
        $key = tkt_esc($conn, $key);
        $row = getRecord("SELECT setting_value FROM tbl_ticket_settings WHERE setting_key='$key' LIMIT 1");
        return ($row && isset($row['setting_value'])) ? $row['setting_value'] : $default;
    }
}

if (!function_exists('tkt_set_setting')) {
    function tkt_set_setting($conn, $key, $value)
    {
        $key = tkt_esc($conn, $key);
        $val = tkt_esc($conn, $value);
        $now = date('Y-m-d H:i:s');
        $exists = getRow("SELECT id FROM tbl_ticket_settings WHERE setting_key='$key'");
        if ($exists) {
            $conn->query("UPDATE tbl_ticket_settings SET setting_value='$val', updated_at='$now' WHERE setting_key='$key'");
        } else {
            $conn->query("INSERT INTO tbl_ticket_settings SET setting_key='$key', setting_value='$val', updated_at='$now'");
        }
    }
}

if (!function_exists('tkt_upload_dir')) {
    function tkt_upload_dir($ticketNo)
    {
        $base = dirname(__DIR__) . '/../ticketfiles/' . $ticketNo . '/';
        if (!is_dir($base)) {
            @mkdir($base, 0755, true);
        }
        return $base;
    }
}

if (!function_exists('tkt_upload_url')) {
    function tkt_upload_url($ticketNo, $file = '')
    {
        global $SiteUrl;
        $path = '../ticketfiles/' . rawurlencode($ticketNo) . '/';
        return $file !== '' ? $path . rawurlencode($file) : $path;
    }
}

if (!function_exists('tkt_allowed_extensions')) {
    function tkt_allowed_extensions($conn)
    {
        $raw = tkt_get_setting($conn, 'allowed_extensions', 'jpg,jpeg,png,gif,webp,pdf,xls,xlsx,doc,docx');
        $parts = array_filter(array_map('trim', explode(',', strtolower($raw))));
        return $parts ?: array('jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'xls', 'xlsx', 'doc', 'docx');
    }
}

if (!function_exists('tkt_max_upload_bytes')) {
    function tkt_max_upload_bytes($conn)
    {
        $mb = (float) tkt_get_setting($conn, 'max_attachment_mb', '5');
        if ($mb < 1) {
            $mb = 5;
        }
        return (int) ($mb * 1024 * 1024);
    }
}

if (!function_exists('tkt_validate_upload')) {
    function tkt_validate_upload($conn, $fileInfo)
    {
        $allowed = tkt_allowed_extensions($conn);
        $maxBytes = tkt_max_upload_bytes($conn);
        $name = $fileInfo['name'] ?? '';
        $size = (int) ($fileInfo['size'] ?? 0);
        $tmp = $fileInfo['tmp_name'] ?? '';
        $err = (int) ($fileInfo['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($err !== UPLOAD_ERR_OK) {
            return array('ok' => false, 'message' => 'Upload failed.');
        }
        if ($size > $maxBytes) {
            return array('ok' => false, 'message' => 'File exceeds max size.');
        }
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            return array('ok' => false, 'message' => 'File type not allowed.');
        }
        if (!is_uploaded_file($tmp)) {
            return array('ok' => false, 'message' => 'Invalid upload.');
        }
        return array('ok' => true, 'ext' => $ext);
    }
}

if (!function_exists('tkt_save_uploads')) {
    function tkt_save_uploads($conn, $ticketNo, $filesKey, $userId, $ticketId = 0, $commentId = null)
    {
        $saved = array();
        $savedImagePaths = array();
        if (empty($_FILES[$filesKey]['name']) || !is_array($_FILES[$filesKey]['name'])) {
            return $saved;
        }
        $dir = tkt_upload_dir($ticketNo);
        $now = date('Y-m-d H:i:s');
        foreach ($_FILES[$filesKey]['name'] as $i => $name) {
            if ($name === '') {
                continue;
            }
            $fi = array(
                'name' => $name,
                'type' => $_FILES[$filesKey]['type'][$i] ?? '',
                'tmp_name' => $_FILES[$filesKey]['tmp_name'][$i] ?? '',
                'error' => $_FILES[$filesKey]['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                'size' => $_FILES[$filesKey]['size'][$i] ?? 0,
            );
            $check = tkt_validate_upload($conn, $fi);
            if (!$check['ok']) {
                continue;
            }
            $fnm = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($name, PATHINFO_FILENAME));
            $filename = rand(1000, 9999) . '_' . $fnm . '.' . $check['ext'];
            $target = $dir . $filename;
            if (!move_uploaded_file($fi['tmp_name'], $target)) {
                continue;
            }
            $saved[] = $filename;
            if (tkt_is_image_extension($check['ext'])) {
                $savedImagePaths[] = $target;
            }
            if ($ticketId > 0) {
                $mime = tkt_esc($conn, $fi['type']);
                $orig = tkt_esc($conn, $name);
                $uid = (int) $userId;
                $cid = $commentId !== null ? (int) $commentId : 'NULL';
                $cidSql = $commentId !== null ? "'$cid'" : 'NULL';
                $conn->query("INSERT INTO tbl_ticket_attachments SET ticket_id='$ticketId', comment_id=$cidSql, file_name='" . tkt_esc($conn, $filename) . "', original_name='$orig', file_size='" . (int) $fi['size'] . "', mime_type='$mime', uploaded_by='$uid', created_at='$now'");
            }
        }

        if (count($savedImagePaths) >= 2) {
            $pdfFilename = tkt_create_images_pdf($dir, $savedImagePaths, $ticketNo);
            if ($pdfFilename) {
                $saved[] = $pdfFilename;
                if ($ticketId > 0) {
                    $pdfPath = $dir . $pdfFilename;
                    $pdfSize = is_file($pdfPath) ? (int) filesize($pdfPath) : 0;
                    $uid = (int) $userId;
                    $cidSql = $commentId !== null ? "'" . (int) $commentId . "'" : 'NULL';
                    $conn->query("INSERT INTO tbl_ticket_attachments SET ticket_id='$ticketId', comment_id=$cidSql, file_name='" . tkt_esc($conn, $pdfFilename) . "', original_name='Combined Images PDF', file_size='$pdfSize', mime_type='application/pdf', uploaded_by='$uid', created_at='$now'");
                }
            }
        }

        return $saved;
    }
}

if (!function_exists('tkt_generate_ticket_no')) {
    function tkt_generate_ticket_no($conn)
    {
        $year = date('Y');
        $prefix = 'TKT-' . $year . '-';
        $row = getRecord("SELECT ticket_no FROM tbl_tickets WHERE ticket_no LIKE '" . tkt_esc($conn, $prefix) . "%' ORDER BY id DESC LIMIT 1");
        $next = 1;
        if ($row && !empty($row['ticket_no'])) {
            $parts = explode('-', $row['ticket_no']);
            $last = (int) end($parts);
            $next = $last + 1;
        }
        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('tkt_get_status_by_slug')) {
    function tkt_get_status_by_slug($conn, $slug)
    {
        $slug = tkt_esc($conn, $slug);
        return getRecord("SELECT * FROM tbl_ticket_status WHERE slug='$slug' AND status=1 LIMIT 1");
    }
}

if (!function_exists('tkt_status_column_type')) {
    function tkt_status_column_type($conn)
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        $r = @$conn->query("SHOW COLUMNS FROM tbl_tickets LIKE 'status'");
        $row = $r ? $r->fetch_assoc() : null;
        $cached = strtolower((string) ($row['Type'] ?? 'varchar'));
        return $cached;
    }
}

if (!function_exists('tkt_status_enum_values')) {
    function tkt_status_enum_values($conn)
    {
        $type = tkt_status_column_type($conn);
        if (strpos($type, 'enum') === false) {
            return array();
        }
        if (!preg_match_all("/'([^']+)'/", $type, $matches)) {
            return array();
        }
        return $matches[1];
    }
}

if (!function_exists('tkt_status_for_storage')) {
    /** Map logical status slug to a value tbl_tickets.status column accepts (legacy ENUM safe). */
    function tkt_status_for_storage($conn, $slug)
    {
        $slug = strtolower(trim((string) $slug));
        if ($slug === '') {
            return 'open';
        }
        $allowed = tkt_status_enum_values($conn);
        if (empty($allowed) || in_array($slug, $allowed, true)) {
            return $slug;
        }
        $map = array(
            'assigned' => 'open',
            'pending' => 'open',
            'waiting_for_user' => 'open',
            'waiting_for_approval' => 'open',
            'waiting_for_vendor' => 'open',
            'on_hold' => 'open',
            'reopened' => 'open',
            'cancelled' => 'closed',
            'rejected' => 'closed',
        );
        $stored = $map[$slug] ?? 'open';
        if (!empty($allowed) && !in_array($stored, $allowed, true)) {
            return in_array('open', $allowed, true) ? 'open' : $allowed[0];
        }
        return $stored;
    }
}

if (!function_exists('tkt_table_has_column')) {
    function tkt_table_has_column($conn, $table, $column)
    {
        static $cache = array();
        $key = $table . '.' . $column;
        if (!isset($cache[$key])) {
            $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
            $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
            $r = @$conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
            $cache[$key] = ($r && $r->num_rows > 0);
        }
        return $cache[$key];
    }
}

if (!function_exists('tkt_status_matches_filter')) {
    /** Build SQL OR-conditions matching logical status slugs against legacy status column. */
    function tkt_status_matches_filter($conn, $statuses, $includeBlank = false)
    {
        $parts = array();
        foreach ((array) $statuses as $slug) {
            $slug = strtolower(trim((string) $slug));
            if ($slug === '') {
                continue;
            }
            $stored = tkt_status_for_storage($conn, $slug);
            $parts[] = "LOWER(TRIM(t.status))='" . mysqli_real_escape_string($conn, $stored) . "'";

            if (tkt_table_has_column($conn, 'tbl_tickets', 'status_id')) {
                $st = tkt_get_status_by_slug($conn, $slug);
                if ($st && !empty($st['id'])) {
                    $parts[] = "t.status_id='" . (int) $st['id'] . "'";
                }
            }

            if ($slug === 'assigned' && tkt_table_has_column($conn, 'tbl_tickets', 'assigned_to')) {
                $parts[] = "(LOWER(TRIM(IFNULL(t.status,''))) IN ('open','') AND t.assigned_to IS NOT NULL AND t.assigned_to > 0)";
            }
        }
        if ($includeBlank) {
            $parts[] = "IFNULL(t.status,'')=''";
        }
        $parts = array_unique($parts);
        return empty($parts) ? '1=0' : '(' . implode(' OR ', $parts) . ')';
    }
}

if (!function_exists('tkt_repair_blank_ticket_statuses')) {
    /** One-time per request: fix rows where invalid ENUM left status blank. */
    function tkt_repair_blank_ticket_statuses($conn)
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;
        @$conn->query("UPDATE tbl_tickets SET status='open' WHERE IFNULL(status,'')=''");
    }
}

if (!function_exists('tkt_ticket_display_status')) {
    function tkt_ticket_display_status($conn, $ticket)
    {
        $status = strtolower(trim((string) ($ticket['status'] ?? '')));
        if ($status !== '' && $status !== 'open') {
            return $status;
        }
        if (!empty($ticket['status_id']) && function_exists('getRecord')) {
            $row = getRecord("SELECT slug FROM tbl_ticket_status WHERE id='" . (int) $ticket['status_id'] . "' LIMIT 1");
            if ($row && !empty($row['slug'])) {
                return $row['slug'];
            }
        }
        if (!empty($ticket['assigned_to'])) {
            return 'assigned';
        }
        return $status !== '' ? $status : 'open';
    }
}

if (!function_exists('tkt_get_priority_by_slug')) {
    function tkt_get_priority_by_slug($conn, $slug)
    {
        $slug = tkt_esc($conn, $slug);
        return getRecord("SELECT * FROM tbl_ticket_priorities WHERE slug='$slug' AND status=1 LIMIT 1");
    }
}

if (!function_exists('tkt_get_departments')) {
    /** Active HR departments used across ticket system (tbl_departments). */
    function tkt_get_departments($conn)
    {
        return getList("SELECT id, Name AS name FROM tbl_departments WHERE Status=1 ORDER BY Name") ?: array();
    }
}

if (!function_exists('tkt_resolve_department_id')) {
    /** Normalize to tbl_departments.id */
    function tkt_resolve_department_id($conn, $deptId)
    {
        $deptId = (int) $deptId;
        if ($deptId < 1) {
            return 0;
        }
        if (getRecord("SELECT id FROM tbl_departments WHERE id='$deptId' AND Status=1 LIMIT 1")) {
            return $deptId;
        }
        $row = getRecord("SELECT linked_dept_id FROM tbl_ticket_departments WHERE id='$deptId' LIMIT 1");
        if ($row && (int) ($row['linked_dept_id'] ?? 0) > 0) {
            return (int) $row['linked_dept_id'];
        }
        return 0;
    }
}

if (!function_exists('tkt_user_department_id')) {
    function tkt_user_department_id($conn, $userId)
    {
        $userId = (int) $userId;
        $row = getRecord("SELECT Designation FROM tbl_users WHERE id='$userId' LIMIT 1");
        return $row ? (int) ($row['Designation'] ?? 0) : 0;
    }
}

if (!function_exists('tkt_ticket_dept_for_user')) {
    function tkt_ticket_dept_for_user($conn, $userId)
    {
        return tkt_user_department_id($conn, $userId);
    }
}

if (!function_exists('tkt_normalize_options')) {
    function tkt_normalize_options($row77)
    {
        if (function_exists('maha_normalize_user_options')) {
            return maha_normalize_user_options($row77);
        }
        $raw = isset($row77['Options2']) ? $row77['Options2'] : ($row77['Options'] ?? '');
        $parts = array_filter(array_map('trim', explode(',', (string) $raw)));
        return array_values(array_unique($parts));
    }
}

if (!function_exists('tkt_is_super_admin')) {
    function tkt_is_super_admin($userId, $roll)
    {
        return ((int) $userId === 2650) || ((int) $roll === 1);
    }
}

if (!function_exists('tkt_user_role_type')) {
    function tkt_user_role_type($conn, $userId, $row77 = null)
    {
        if (!$row77) {
            $row77 = getRecord("SELECT * FROM tbl_users WHERE id='" . (int) $userId . "' LIMIT 1");
        }
        if (!$row77) {
            return 'employee';
        }
        $opts = tkt_normalize_options($row77);
        $roll = (int) ($row77['Roll'] ?? 0);
        if (tkt_is_super_admin($userId, $roll) || in_array('101', $opts, true) && in_array('175', $opts, true)) {
            return 'admin';
        }
        if (in_array('175', $opts, true) || in_array('176', $opts, true)) {
            return 'admin';
        }
        $deptHead = getRow("SELECT id FROM tbl_ticket_departments WHERE head_user_id='" . (int) $userId . "' AND status=1 LIMIT 1");
        if ($deptHead) {
            return 'dept_head';
        }
        if (!empty($row77['ReportingMgr']) && (int) $row77['ReportingMgr'] === 1) {
            return 'manager';
        }
        if (in_array('101', $opts, true)) {
            return 'support';
        }
        return 'employee';
    }
}

if (!function_exists('tkt_can_access_module')) {
    function tkt_can_access_module($conn, $userId, $row77 = null)
    {
        if (!$row77) {
            $row77 = getRecord("SELECT * FROM tbl_users WHERE id='" . (int) $userId . "' LIMIT 1");
        }
        if (!$row77) {
            return false;
        }
        $opts = tkt_normalize_options($row77);
        $roll = (int) ($row77['Roll'] ?? 0);
        if (tkt_is_super_admin($userId, $roll)) {
            return true;
        }
        return in_array('101', $opts, true);
    }
}

if (!function_exists('tkt_can_manage_settings')) {
    function tkt_can_manage_settings($conn, $userId, $row77 = null)
    {
        $role = tkt_user_role_type($conn, $userId, $row77);
        return in_array($role, array('admin', 'dept_head'), true);
    }
}

if (!function_exists('tkt_audit_log')) {
    function tkt_audit_log($conn, $ticketId, $userId, $actionType, $oldValue = '', $newValue = '', $remarks = '')
    {
        $now = date('Y-m-d H:i:s');
        $ip = '';
        if (function_exists('maha_get_client_ip')) {
            $ip = maha_get_client_ip();
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = substr((string) $_SERVER['REMOTE_ADDR'], 0, 45);
        }
        $sql = "INSERT INTO tbl_ticket_audit_log SET
            ticket_id='" . (int) $ticketId . "',
            user_id='" . (int) $userId . "',
            action_type='" . tkt_esc($conn, $actionType) . "',
            old_value='" . tkt_esc($conn, is_array($oldValue) ? json_encode($oldValue) : $oldValue) . "',
            new_value='" . tkt_esc($conn, is_array($newValue) ? json_encode($newValue) : $newValue) . "',
            remarks='" . tkt_esc($conn, $remarks) . "',
            ip_address='" . tkt_esc($conn, $ip) . "',
            created_at='$now'";
        @$conn->query($sql);
        if (function_exists('maha_activity_log')) {
            @maha_activity_log($conn, 'TICKET_' . strtoupper($actionType), 'Ticket #' . $ticketId . ': ' . $actionType, array('ticket_id' => $ticketId));
        }
    }
}

if (!function_exists('tkt_notify')) {
    function tkt_notify($conn, $userId, $type, $title, $message, $ticketId = 0)
    {
        $userId = (int) $userId;
        if ($userId < 1) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $conn->query("INSERT INTO tbl_ticket_notifications SET
            ticket_id='" . (int) $ticketId . "',
            user_id='$userId',
            type='" . tkt_esc($conn, $type) . "',
            title='" . tkt_esc($conn, $title) . "',
            message='" . tkt_esc($conn, $message) . "',
            is_read=0,
            created_at='$now'");

        $row = getRecord("SELECT Tokens FROM tbl_users WHERE id='$userId' AND Tokens!='' LIMIT 1");
        if ($row && !empty($row['Tokens'])) {
            @include_once dirname(__DIR__) . '/../incnotification.php';
        }
    }
}

if (!function_exists('tkt_mail_dir')) {
    function tkt_mail_dir()
    {
        $paths = array(
            dirname(__DIR__, 2) . '/mail',
            __DIR__ . '/../../mail',
            dirname(__DIR__, 2) . '/exeapp_dev0607/../mail',
        );
        if (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $paths[] = rtrim((string) $_SERVER['DOCUMENT_ROOT'], '/\\') . '/mail';
        }
        foreach ($paths as $path) {
            if (is_dir($path)) {
                return rtrim($path, '/\\');
            }
        }
        return dirname(__DIR__, 2) . '/mail';
    }
}

if (!function_exists('tkt_ticket_mail_fallback')) {
    /** When assignee has no EmailId, send TO this address instead. */
    function tkt_ticket_mail_fallback()
    {
        return array(
            'email' => 'rajatdh07@gmail.com',
            'name' => 'Rajat Dhanwalkar',
        );
    }
}

if (!function_exists('tkt_ticket_mail_cc')) {
    function tkt_ticket_mail_cc()
    {
        return array(
            'pradeep.kulkarni@mahachai.in' => 'Pradeep Kulkarni',
            'coo@mahachai.in' => 'COO',
        );
    }
}

if (!function_exists('tkt_phpmailer_send')) {
    function tkt_phpmailer_send($toEmail, $toName, $subject, $message, $altMessage, $ccList = array())
    {
        $toEmail = trim((string) $toEmail);
        if ($toEmail === '' || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            $GLOBALS['ticketMailError'] = 'Invalid recipient email';
            return false;
        }

        $mailDir = tkt_mail_dir();
        $exceptionFile = $mailDir . '/PHPMailer/src/Exception.php';
        $phpmailerFile = $mailDir . '/PHPMailer/src/PHPMailer.php';
        $smtpFile = $mailDir . '/PHPMailer/src/SMTP.php';
        if (!is_file($phpmailerFile)) {
            $GLOBALS['ticketMailError'] = 'PHPMailer not found at ' . $phpmailerFile;
            @error_log('Ticket mail: ' . $GLOBALS['ticketMailError']);
            return false;
        }

        require_once $exceptionFile;
        require_once $phpmailerFile;
        require_once $smtpFile;

        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'mail.kwickfoods.in';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'info@kwickfoods.in';
            $mail->Password   = 'p60pWpKx9z7iBAfR';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom('info@kwickfoods.in', 'Mahachai');
            $mail->addAddress($toEmail, (string) $toName);

            foreach ($ccList as $ccEmail => $ccName) {
                $ccEmail = trim((string) $ccEmail);
                if ($ccEmail !== '' && strcasecmp($ccEmail, $toEmail) !== 0 && filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                    $mail->addCC($ccEmail, (string) $ccName);
                }
            }

            $mail->isHTML(true);
            $mail->Subject = (string) $subject;
            $mail->Body    = (string) $message;
            $mail->AltBody = (string) $altMessage;
            $mail->send();
            $GLOBALS['ticketMailError'] = '';
            return true;
        } catch (\Throwable $e) {
            $GLOBALS['ticketMailError'] = $e->getMessage();
            @error_log('Ticket mail failed to ' . $toEmail . ': ' . $GLOBALS['ticketMailError']);
            return false;
        }
    }
}

if (!function_exists('tkt_fetch_ticket_mail_context')) {
    /** Load ticket + related labels without relying on a single complex JOIN. */
    function tkt_fetch_ticket_mail_context($conn, $ticketId)
    {
        $ticketId = (int) $ticketId;
        if ($ticketId < 1) {
            return null;
        }

        $ticket = getRecord("SELECT * FROM tbl_tickets WHERE id='$ticketId' LIMIT 1");
        if (!$ticket) {
            return null;
        }

        $deptId = (int) ($ticket['department_id'] ?? 0);
        if ($deptId > 0) {
            $dept = getRecord("SELECT Name AS dept_name FROM tbl_departments WHERE id='$deptId' LIMIT 1");
            $ticket['dept_name'] = $dept['dept_name'] ?? '-';
        } else {
            $ticket['dept_name'] = '-';
        }

        $catId = (int) ($ticket['category_id'] ?? 0);
        if ($catId > 0) {
            $cat = @getRecord("SELECT name AS cat_name FROM tbl_ticket_categories WHERE id='$catId' LIMIT 1");
            $ticket['cat_name'] = $cat['cat_name'] ?? '-';
        } else {
            $ticket['cat_name'] = '-';
        }

        $priorityId = (int) ($ticket['priority_id'] ?? 0);
        if ($priorityId > 0) {
            $pr = @getRecord("SELECT name AS priority_name FROM tbl_ticket_priorities WHERE id='$priorityId' LIMIT 1");
            $ticket['priority_name'] = $pr['priority_name'] ?? ucfirst($ticket['priority'] ?? 'Medium');
        } else {
            $ticket['priority_name'] = ucfirst($ticket['priority'] ?? 'Medium');
        }

        $createdBy = (int) ($ticket['created_by'] ?? $ticket['reported_by'] ?? 0);
        if ($createdBy > 0) {
            $creator = getRecord("SELECT CONCAT(Fname,' ',IFNULL(Lname,'')) AS created_name FROM tbl_users WHERE id='$createdBy' LIMIT 1");
            $ticket['created_name'] = trim($creator['created_name'] ?? '');
        } else {
            $ticket['created_name'] = '';
        }

        $assignedTo = (int) ($ticket['assigned_to'] ?? 0);
        if ($assignedTo > 0) {
            $assignee = getRecord("SELECT CONCAT(Fname,' ',IFNULL(Lname,'')) AS assigned_name FROM tbl_users WHERE id='$assignedTo' LIMIT 1");
            $ticket['assigned_name'] = trim($assignee['assigned_name'] ?? '');
        } else {
            $ticket['assigned_name'] = '';
        }

        return $ticket;
    }
}

if (!function_exists('tkt_get_user_email')) {
    function tkt_get_user_email($conn, $userId)
    {
        $userId = (int) $userId;
        if ($userId < 1) {
            return '';
        }
        $row = getRecord("SELECT EmailId FROM tbl_users WHERE id='$userId' LIMIT 1");
        $email = trim($row['EmailId'] ?? '');
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
    }
}

if (!function_exists('tkt_send_ticket_created_mail')) {
    /** Notify assignee (or fallback email) + fixed CC when a ticket is created/assigned. */
    function tkt_send_ticket_created_mail($conn, $ticketId, $context = 'created')
    {
        $ticketId = (int) $ticketId;
        if ($ticketId < 1) {
            return false;
        }

        $ticket = tkt_fetch_ticket_mail_context($conn, $ticketId);
        if (!$ticket) {
            @error_log('Ticket mail: ticket not found or could not load context for id ' . $ticketId);
            return false;
        }

        $assignedTo = (int) ($ticket['assigned_to'] ?? 0);
        $assigneeEmail = $assignedTo > 0 ? tkt_get_user_email($conn, $assignedTo) : '';
        $assigneeName = trim($ticket['assigned_name'] ?? '');
        $usedFallback = false;

        $toEmail = $assigneeEmail;
        $toName = $assigneeName !== '' ? $assigneeName : 'Ticket Assignee';
        if ($toEmail === '') {
            $fallback = tkt_ticket_mail_fallback();
            $toEmail = $fallback['email'];
            $toName = $fallback['name'];
            $usedFallback = true;
        }

        $ticketNo = $ticket['ticket_no'];
        $ticketTitle = $ticket['subject'];
        $deptName = $ticket['dept_name'] ?? '-';
        $catName = $ticket['cat_name'] ?? '-';
        $priorityName = $ticket['priority_name'] ?? ucfirst($ticket['priority'] ?? 'Medium');
        $createdName = trim($ticket['created_name'] ?? '');
        $createdAt = !empty($ticket['created_at']) ? date('d M Y, h:i A', strtotime($ticket['created_at'])) : date('d M Y, h:i A');
        $descText = trim(strip_tags((string) ($ticket['description'] ?? '')));
        $descHtml = nl2br(tkt_h($descText));
        $viewUrl = 'https://kwickfoods.in/control_panel_maha/ticket_management/view-ticket.php?id=' . $ticketId;

        $assigneeLine = $assignedTo > 0
            ? ($assigneeName !== '' ? $assigneeName : 'Employee #' . $assignedTo)
            : 'Unassigned';
        if ($assignedTo > 0 && $assigneeEmail === '') {
            $fallback = tkt_ticket_mail_fallback();
            $assigneeLine .= ' (no email in profile — notified at ' . $fallback['email'] . ')';
        } elseif ($assignedTo < 1 && $usedFallback) {
            $assigneeLine .= ' (unassigned — notified at ' . tkt_ticket_mail_fallback()['email'] . ')';
        }

        $subjectPrefix = ($context === 'assigned') ? 'Ticket Assigned' : 'New Ticket Created';
        $subject = $subjectPrefix . ' - ' . $ticketNo . ' - ' . $ticketTitle;

        $message = '
<div style="font-family: Arial, sans-serif; background-color:#f4f6f8; padding:30px;">
  <div style="max-width:650px; margin:auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.08);">
    <div style="background:#0F5A4A; padding:20px; text-align:center;">
      <img src="https://mahachai.in/logo.png" alt="Maha Chai Logo" style="max-height:60px;">
    </div>
    <div style="padding:30px; color:#333;">
      <h2 style="margin-top:0; color:#0F5A4A;">' . tkt_h($subjectPrefix) . '</h2>
      <p style="font-size:15px;">Dear <strong>' . tkt_h($toName) . '</strong>,</p>
      <p style="font-size:15px; line-height:1.6;">A ticket has been ' . ($context === 'assigned' ? 'assigned' : 'raised') . ' in the ticket system. Please review and take action.</p>
      <table style="width:100%; border-collapse:collapse; margin:20px 0; font-size:14px;">
        <tr><td style="padding:10px; border:1px solid #ddd; background:#f7f7f7; font-weight:bold; width:35%;">Ticket No</td><td style="padding:10px; border:1px solid #ddd;">' . tkt_h($ticketNo) . '</td></tr>
        <tr><td style="padding:10px; border:1px solid #ddd; background:#f7f7f7; font-weight:bold;">Title</td><td style="padding:10px; border:1px solid #ddd;">' . tkt_h($ticketTitle) . '</td></tr>
        <tr><td style="padding:10px; border:1px solid #ddd; background:#f7f7f7; font-weight:bold;">Assigned To</td><td style="padding:10px; border:1px solid #ddd;">' . tkt_h($assigneeLine) . '</td></tr>
        <tr><td style="padding:10px; border:1px solid #ddd; background:#f7f7f7; font-weight:bold;">Department</td><td style="padding:10px; border:1px solid #ddd;">' . tkt_h($deptName) . '</td></tr>
        <tr><td style="padding:10px; border:1px solid #ddd; background:#f7f7f7; font-weight:bold;">Category</td><td style="padding:10px; border:1px solid #ddd;">' . tkt_h($catName) . '</td></tr>
        <tr><td style="padding:10px; border:1px solid #ddd; background:#f7f7f7; font-weight:bold;">Priority</td><td style="padding:10px; border:1px solid #ddd;">' . tkt_h($priorityName) . '</td></tr>
        <tr><td style="padding:10px; border:1px solid #ddd; background:#f7f7f7; font-weight:bold;">Raised By</td><td style="padding:10px; border:1px solid #ddd;">' . tkt_h($createdName) . '</td></tr>
        <tr><td style="padding:10px; border:1px solid #ddd; background:#f7f7f7; font-weight:bold;">Created On</td><td style="padding:10px; border:1px solid #ddd;">' . tkt_h($createdAt) . '</td></tr>
      </table>
      <p style="font-size:14px; font-weight:bold; margin-bottom:6px;">Description</p>
      <div style="padding:12px; border:1px solid #ddd; background:#fafafa; font-size:14px; line-height:1.5;">' . $descHtml . '</div>
      <p style="margin-top:25px;"><a href="' . tkt_h($viewUrl) . '" style="background:#0F5A4A; color:#fff; padding:10px 18px; text-decoration:none; border-radius:4px;">View Ticket</a></p>
      <p style="margin-top:25px;">Regards,<br><strong>Maha Chai Ticket System</strong></p>
    </div>
    <div style="background:#f1f1f1; padding:15px; text-align:center; font-size:12px; color:#777;">
      Automated ticket notification from Maha Chai.
    </div>
  </div>
</div>';

        $altMessage = "Dear {$toName},\n\n"
            . "A ticket has been " . ($context === 'assigned' ? 'assigned' : 'created') . ".\n\n"
            . "Ticket No: {$ticketNo}\n"
            . "Title: {$ticketTitle}\n"
            . "Assigned To: {$assigneeLine}\n"
            . "Department: {$deptName}\n"
            . "Category: {$catName}\n"
            . "Priority: {$priorityName}\n"
            . "Raised By: {$createdName}\n"
            . "Created On: {$createdAt}\n\n"
            . "Description:\n{$descText}\n\n"
            . "View: {$viewUrl}\n\n"
            . "Regards,\nMaha Chai Ticket System";

        return tkt_phpmailer_send($toEmail, $toName, $subject, $message, $altMessage, tkt_ticket_mail_cc());
    }
}

if (!function_exists('tkt_send_ticket_assignment_mail')) {
    function tkt_send_ticket_assignment_mail($conn, $ticketId, $assignedToUserId)
    {
        return tkt_send_ticket_created_mail($conn, $ticketId, 'assigned');
    }
}

if (!function_exists('tkt_sla_for_priority')) {
    function tkt_sla_for_priority($conn, $priorityId)
    {
        $priorityId = (int) $priorityId;
        $row = getRecord("SELECT * FROM tbl_ticket_sla_rules WHERE priority_id='$priorityId' AND status=1 LIMIT 1");
        if (!$row) {
            return array('response_minutes' => 1440, 'resolution_minutes' => 4320);
        }
        return array(
            'response_minutes' => (int) $row['response_minutes'],
            'resolution_minutes' => (int) $row['resolution_minutes'],
        );
    }
}

if (!function_exists('tkt_compute_sla_dates')) {
    function tkt_compute_sla_dates($conn, $priorityId, $createdAt = null)
    {
        $sla = tkt_sla_for_priority($conn, $priorityId);
        $base = $createdAt ? strtotime($createdAt) : time();
        return array(
            'sla_response_due' => date('Y-m-d H:i:s', $base + ($sla['response_minutes'] * 60)),
            'sla_resolution_due' => date('Y-m-d H:i:s', $base + ($sla['resolution_minutes'] * 60)),
        );
    }
}

if (!function_exists('tkt_auto_assign')) {
    function tkt_auto_assign($conn, $departmentId, $categoryId, $priorityId)
    {
        $departmentId = (int) $departmentId;
        $categoryId = (int) $categoryId;
        $priorityId = (int) $priorityId;
        $sql = "SELECT assigned_to FROM tbl_ticket_auto_assignment WHERE status=1
            AND (department_id=0 OR department_id='$departmentId')
            AND (category_id=0 OR category_id='$categoryId')
            AND (priority_id=0 OR priority_id='$priorityId')
            ORDER BY department_id DESC, category_id DESC, priority_id DESC LIMIT 1";
        $row = getRecord($sql);
        return $row ? (int) $row['assigned_to'] : 0;
    }
}

if (!function_exists('tkt_create_ticket')) {
    function tkt_create_ticket($conn, $data, $userId, $source = 'admin')
    {
        $now = date('Y-m-d H:i:s');
        $ticketNo = tkt_generate_ticket_no($conn);
        $title = tkt_esc($conn, $data['title'] ?? $data['subject'] ?? '');
        $deptId = tkt_resolve_department_id($conn, (int) ($data['department_id'] ?? 0));
        $catId = (int) ($data['category_id'] ?? 0);
        $priorityId = (int) ($data['priority_id'] ?? 0);
        $prioritySlug = '';
        if ($priorityId > 0) {
            $pr = getRecord("SELECT slug FROM tbl_ticket_priorities WHERE id='$priorityId'");
            $prioritySlug = $pr ? $pr['slug'] : 'medium';
        } else {
            $prioritySlug = tkt_esc($conn, $data['priority'] ?? 'medium');
            $pr = tkt_get_priority_by_slug($conn, $prioritySlug);
            $priorityId = $pr ? (int) $pr['id'] : 2;
        }
        $branchId = (int) ($data['branch_id'] ?? 0);
        $reportedBy = (int) ($data['reported_by'] ?? $userId);
        $assignedTo = (int) ($data['assigned_to'] ?? 0);
        if ($assignedTo < 1) {
            $assignedTo = tkt_auto_assign($conn, $deptId, $catId, $priorityId);
        }
        $desc = tkt_esc($conn, $data['description'] ?? '');
        $remarks = tkt_esc($conn, $data['remarks'] ?? '');
        $expDate = !empty($data['expected_resolution_date']) ? tkt_esc($conn, $data['expected_resolution_date']) : '';
        $sla = tkt_compute_sla_dates($conn, $priorityId, $now);
        $statusSlug = 'open';
        $st = tkt_get_status_by_slug($conn, 'open');
        $statusId = $st ? (int) $st['id'] : 1;
        if ($assignedTo > 0) {
            $st2 = tkt_get_status_by_slug($conn, 'assigned');
            if ($st2) {
                $statusSlug = 'assigned';
                $statusId = (int) $st2['id'];
            }
        }
        $statusFinal = tkt_status_for_storage($conn, $statusSlug);

        $sql = "INSERT INTO tbl_tickets SET
            ticket_no='$ticketNo',
            subject='$title',
            department_id='$deptId',
            category_id=" . ($catId ? "'$catId'" : 'NULL') . ",
            priority='$prioritySlug',
            priority_id='$priorityId',
            status='$statusFinal',
            status_id='$statusId',
            description='$desc',
            branch_id=" . ($branchId ? "'$branchId'" : 'NULL') . ",
            reported_by='$reportedBy',
            assigned_to=" . ($assignedTo ? "'$assignedTo'" : 'NULL') . ",
            assigned_at=" . ($assignedTo ? "'$now'" : 'NULL') . ",
            expected_resolution_date=" . ($expDate ? "'$expDate'" : 'NULL') . ",
            remarks='$remarks',
            sla_response_due='" . $sla['sla_response_due'] . "',
            sla_resolution_due='" . $sla['sla_resolution_due'] . "',
            created_by='$userId',
            created_at='$now',
            updated_at='$now',
            source='" . tkt_esc($conn, $source) . "'";
        if (!$conn->query($sql)) {
            $sqlLegacy = "INSERT INTO tbl_tickets SET
                ticket_no='$ticketNo',
                subject='$title',
                department_id='$deptId',
                priority='$prioritySlug',
                status='$statusFinal',
                description='$desc',
                created_by='$userId',
                created_at='$now'";
            if ($assignedTo > 0) {
                $sqlLegacy .= ", assigned_to='$assignedTo'";
            }
            if (!$conn->query($sqlLegacy)) {
                return array('success' => false, 'message' => $conn->error);
            }
        }
        $ticketId = (int) $conn->insert_id;

        $uploaded = tkt_save_uploads($conn, $ticketNo, 'Attachments', $userId, $ticketId);
        if (!empty($uploaded)) {
            $att = tkt_esc($conn, implode(',', $uploaded));
            $conn->query("UPDATE tbl_tickets SET attachments='$att' WHERE id='$ticketId'");
        }

        if ($assignedTo > 0) {
            @$conn->query("INSERT INTO tbl_ticket_assignment_history SET ticket_id='$ticketId', assigned_to='$assignedTo', assigned_by='$userId', assigned_at='$now', remarks='Auto/Manual assignment on create'");
        }

        $mailSent = tkt_send_ticket_created_mail($conn, $ticketId, 'created');
        if (!$mailSent) {
            $lastErr = isset($GLOBALS['ticketMailError']) ? (string) $GLOBALS['ticketMailError'] : '';
            $failMsg = 'Ticket creation email could not be sent';
            if ($lastErr !== '') {
                $failMsg .= ': ' . $lastErr;
            }
            tkt_audit_log($conn, $ticketId, $userId, 'mail_failed', '', '', $failMsg);
        }

        if ($assignedTo > 0) {
            @tkt_notify($conn, $assignedTo, 'assigned', 'Ticket Assigned', "Ticket $ticketNo has been assigned to you.", $ticketId);
        }

        tkt_audit_log($conn, $ticketId, $userId, 'create', '', $ticketNo, 'Ticket created');
        @tkt_notify($conn, $reportedBy, 'created', 'Ticket Created', "Your ticket $ticketNo has been created.", $ticketId);

        $dept = getRecord("SELECT head_user_id FROM tbl_ticket_departments WHERE linked_dept_id='$deptId' AND status=1 LIMIT 1");
        if ($dept && !empty($dept['head_user_id'])) {
            tkt_notify($conn, (int) $dept['head_user_id'], 'created', 'New Ticket', "New ticket $ticketNo in your department.", $ticketId);
        }

        return array('success' => true, 'ticket_id' => $ticketId, 'ticket_no' => $ticketNo, 'mail_sent' => $mailSent);
    }
}

if (!function_exists('tkt_update_ticket_status')) {
    function tkt_update_ticket_status($conn, $ticketId, $newStatusSlug, $userId, $remarks = '')
    {
        $ticketId = (int) $ticketId;
        $ticket = getRecord("SELECT * FROM tbl_tickets WHERE id='$ticketId' LIMIT 1");
        if (!$ticket) {
            return array('success' => false, 'message' => 'Ticket not found');
        }
        $st = tkt_get_status_by_slug($conn, $newStatusSlug);
        if (!$st) {
            return array('success' => false, 'message' => 'Invalid status');
        }
        $now = date('Y-m-d H:i:s');
        $old = $ticket['status'];
        $extra = '';
        if ($newStatusSlug === 'resolved') {
            $extra = ", resolved_at='$now'";
        }
        if ($newStatusSlug === 'closed') {
            $extra = ", closed_at='$now'";
        }
        if ($newStatusSlug === 'reopened' || $newStatusSlug === 'open') {
            $cnt = (int) ($ticket['reopened_count'] ?? 0) + 1;
            $extra = ", reopened_count='$cnt', reopened_by='$userId', reopened_at='$now'";
        }
        $conn->query("UPDATE tbl_tickets SET status='" . tkt_esc($conn, tkt_status_for_storage($conn, $newStatusSlug)) . "', status_id='" . (int) $st['id'] . "', updated_at='$now' $extra WHERE id='$ticketId'");

        $conn->query("INSERT INTO tbl_ticket_actions SET ticket_id='$ticketId', action_by='$userId', action_type='Status Update', remarks='" . tkt_esc($conn, $remarks) . "', status='" . tkt_esc($conn, $newStatusSlug) . "', created_at='$now'");

        tkt_audit_log($conn, $ticketId, $userId, 'status_change', $old, $newStatusSlug, $remarks);

        $notifyUsers = array((int) $ticket['created_by'], (int) ($ticket['assigned_to'] ?? 0));
        foreach (array_unique(array_filter($notifyUsers)) as $uid) {
            if ((int) $uid !== (int) $userId) {
                tkt_notify($conn, $uid, 'status_changed', 'Ticket Status Updated', 'Ticket ' . $ticket['ticket_no'] . ' is now ' . $st['name'], $ticketId);
            }
        }
        return array('success' => true);
    }
}

if (!function_exists('tkt_assign_ticket')) {
    function tkt_assign_ticket($conn, $ticketId, $assignedTo, $assignedBy, $remarks = '')
    {
        $ticketId = (int) $ticketId;
        $assignedTo = (int) $assignedTo;
        $ticket = getRecord("SELECT * FROM tbl_tickets WHERE id='$ticketId' LIMIT 1");
        if (!$ticket) {
            return array('success' => false, 'message' => 'Ticket not found');
        }
        $now = date('Y-m-d H:i:s');
        $st = tkt_get_status_by_slug($conn, 'assigned');
        $statusId = $st ? (int) $st['id'] : 2;
        $storedStatus = tkt_status_for_storage($conn, 'assigned');
        $conn->query("UPDATE tbl_tickets SET assigned_to='$assignedTo', assigned_at='$now', status='$storedStatus', status_id='$statusId', updated_at='$now' WHERE id='$ticketId'");
        $conn->query("INSERT INTO tbl_ticket_assignment_history SET ticket_id='$ticketId', assigned_to='$assignedTo', assigned_by='$assignedBy', assigned_at='$now', remarks='" . tkt_esc($conn, $remarks) . "'");
        tkt_audit_log($conn, $ticketId, $assignedBy, 'assign', $ticket['assigned_to'] ?? '', $assignedTo, $remarks);
        tkt_notify($conn, $assignedTo, 'assigned', 'Ticket Assigned', 'Ticket ' . $ticket['ticket_no'] . ' assigned to you.', $ticketId);
        tkt_send_ticket_assignment_mail($conn, $ticketId, $assignedTo);
        return array('success' => true);
    }
}

if (!function_exists('tkt_add_comment')) {
    function tkt_add_comment($conn, $ticketId, $userId, $comment, $isInternal = 0, $filesKey = 'Attachments')
    {
        $ticketId = (int) $ticketId;
        $ticket = getRecord("SELECT ticket_no, created_by, assigned_to FROM tbl_tickets WHERE id='$ticketId' LIMIT 1");
        if (!$ticket) {
            return array('success' => false, 'message' => 'Ticket not found');
        }
        $now = date('Y-m-d H:i:s');
        $conn->query("INSERT INTO tbl_ticket_comments SET ticket_id='$ticketId', user_id='$userId', comment='" . tkt_esc($conn, $comment) . "', is_internal='" . (int) $isInternal . "', created_at='$now'");
        $commentId = (int) $conn->insert_id;
        $uploaded = tkt_save_uploads($conn, $ticket['ticket_no'], $filesKey, $userId, $ticketId, $commentId);
        if (!empty($uploaded)) {
            $att = tkt_esc($conn, implode(',', $uploaded));
            $conn->query("UPDATE tbl_ticket_comments SET attachments='$att' WHERE id='$commentId'");
        }
        tkt_audit_log($conn, $ticketId, $userId, 'comment', '', $isInternal ? 'internal' : 'public', substr($comment, 0, 200));
        if (!$isInternal) {
            tkt_notify($conn, (int) $ticket['created_by'], 'comment', 'New Comment', 'New comment on ticket ' . $ticket['ticket_no'], $ticketId);
        }
        if (!empty($ticket['assigned_to'])) {
            tkt_notify($conn, (int) $ticket['assigned_to'], 'comment', 'New Comment', 'New comment on ticket ' . $ticket['ticket_no'], $ticketId);
        }
        return array('success' => true, 'comment_id' => $commentId);
    }
}

if (!function_exists('tkt_can_view_ticket')) {
    function tkt_can_view_ticket($conn, $ticket, $userId, $roleType, $row77 = null)
    {
        if (!$ticket) {
            return false;
        }
        if (in_array($roleType, array('admin', 'support'), true)) {
            return true;
        }
        $userId = (int) $userId;
        if ((int) $ticket['created_by'] === $userId || (int) ($ticket['reported_by'] ?? 0) === $userId) {
            return true;
        }
        if ((int) ($ticket['assigned_to'] ?? 0) === $userId) {
            return true;
        }
        if ($roleType === 'dept_head') {
            $userDept = tkt_user_department_id($conn, $userId);
            if ($userDept > 0 && (int) $ticket['department_id'] === $userDept) {
                return true;
            }
            $head = getRecord("SELECT linked_dept_id FROM tbl_ticket_departments WHERE head_user_id='$userId' AND status=1 LIMIT 1");
            if ($head && (int) $ticket['department_id'] === (int) ($head['linked_dept_id'] ?? 0)) {
                return true;
            }
        }
        if ($roleType === 'manager' && $row77) {
            $under = getRow("SELECT id FROM tbl_users WHERE id='" . (int) $ticket['created_by'] . "' AND UnderByUser='$userId'");
            if ($under) {
                return true;
            }
            if (!empty($row77['UnderFrId']) && !empty($ticket['branch_id']) && (int) $ticket['branch_id'] === (int) $row77['UnderFrId']) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('tkt_list_where')) {
    function tkt_list_where($conn, $view, $userId, $roleType, $row77, $filters = array())
    {
        $w = array('1=1');
        $userId = (int) $userId;

        switch ($view) {
            case 'my':
                $w[] = "(t.created_by='$userId' OR t.reported_by='$userId')";
                break;
            case 'assigned':
                $w[] = "t.assigned_to='$userId'";
                break;
            case 'department':
                $headLegacy = 0;
                $head = getRecord("SELECT linked_dept_id FROM tbl_ticket_departments WHERE head_user_id='$userId' AND status=1 LIMIT 1");
                if ($head && (int) ($head['linked_dept_id'] ?? 0) > 0) {
                    $headLegacy = (int) $head['linked_dept_id'];
                }
                if ($headLegacy > 0) {
                    $w[] = "t.department_id='$headLegacy'";
                } else {
                    $dept = tkt_ticket_dept_for_user($conn, $userId);
                    $w[] = $dept > 0 ? "t.department_id='$dept'" : '0';
                }
                break;
            case 'overdue':
                $w[] = "t.is_overdue=1";
                break;
            case 'reopened':
                $w[] = "(t.reopened_count > 0 OR t.status IN ('reopened','open') AND t.reopened_by IS NOT NULL)";
                break;
            case 'forward':
                $w[] = "t.id IN (SELECT DISTINCT ticket_id FROM tbl_ticket_assignment_history WHERE assigned_by='$userId')";
                break;
            case 'all':
            default:
                if (!in_array($roleType, array('admin', 'support', 'dept_head', 'manager'), true)) {
                    $w[] = "(t.created_by='$userId' OR t.reported_by='$userId' OR t.assigned_to='$userId')";
                }
                break;
        }

        if (!empty($filters['from_date'])) {
            $w[] = "DATE(t.created_at)>='" . tkt_esc($conn, $filters['from_date']) . "'";
        }
        if (!empty($filters['to_date'])) {
            $w[] = "DATE(t.created_at)<='" . tkt_esc($conn, $filters['to_date']) . "'";
        }
        if (!empty($filters['department_id']) && $filters['department_id'] !== 'all') {
            $w[] = "t.department_id='" . (int) $filters['department_id'] . "'";
        }
        if (!empty($filters['category_id']) && $filters['category_id'] !== 'all') {
            $w[] = "t.category_id='" . (int) $filters['category_id'] . "'";
        }
        if (!empty($filters['priority_id']) && $filters['priority_id'] !== 'all') {
            $w[] = "t.priority_id='" . (int) $filters['priority_id'] . "'";
        }
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $w[] = "t.status='" . tkt_esc($conn, $filters['status']) . "'";
        }
        if (!empty($filters['branch_id']) && $filters['branch_id'] !== 'all') {
            $w[] = "t.branch_id='" . (int) $filters['branch_id'] . "'";
        }
        if (!empty($filters['assigned_to']) && $filters['assigned_to'] !== 'all') {
            $w[] = "t.assigned_to='" . (int) $filters['assigned_to'] . "'";
        }
        if (!empty($filters['created_by']) && $filters['created_by'] !== 'all') {
            $w[] = "t.created_by='" . (int) $filters['created_by'] . "'";
        }

        return implode(' AND ', $w);
    }
}

if (!function_exists('tkt_run_sla_escalation')) {
    function tkt_run_sla_escalation($conn)
    {
        $now = date('Y-m-d H:i:s');
        $openStatuses = "'open','assigned','in_progress','waiting_for_user','waiting_for_approval','waiting_for_vendor','on_hold','reopened'";

        $q = $conn->query("SELECT t.*, td.head_user_id FROM tbl_tickets t
            LEFT JOIN tbl_ticket_departments td ON td.linked_dept_id=t.department_id AND td.status=1
            WHERE t.status IN ($openStatuses) AND (t.is_overdue=0 OR t.is_overdue IS NULL)
            AND t.sla_resolution_due IS NOT NULL AND t.sla_resolution_due < '$now'");
        while ($q && ($t = $q->fetch_assoc())) {
            $conn->query("UPDATE tbl_tickets SET is_overdue=1, updated_at='$now' WHERE id='" . (int) $t['id'] . "'");
            tkt_audit_log($conn, (int) $t['id'], 0, 'overdue', '', '1', 'SLA resolution breached');
            if (!empty($t['assigned_to'])) {
                tkt_notify($conn, (int) $t['assigned_to'], 'overdue', 'Ticket Overdue', 'Ticket ' . $t['ticket_no'] . ' is overdue.', (int) $t['id']);
            }
            if (!empty($t['head_user_id'])) {
                tkt_notify($conn, (int) $t['head_user_id'], 'overdue', 'Ticket Overdue', 'Ticket ' . $t['ticket_no'] . ' is overdue in your department.', (int) $t['id']);
            }
        }

        $q2 = $conn->query("SELECT t.*, td.head_user_id, p.slug AS priority_slug FROM tbl_tickets t
            LEFT JOIN tbl_ticket_departments td ON td.linked_dept_id=t.department_id AND td.status=1
            LEFT JOIN tbl_ticket_priorities p ON p.id=t.priority_id
            WHERE t.status='open' AND (t.assigned_to IS NULL OR t.assigned_to=0)
            AND t.sla_response_due IS NOT NULL AND t.sla_response_due < '$now'");
        while ($q2 && ($t = $q2->fetch_assoc())) {
            if (!empty($t['head_user_id'])) {
                tkt_notify($conn, (int) $t['head_user_id'], 'escalation', 'Unassigned Ticket', 'Ticket ' . $t['ticket_no'] . ' not assigned within SLA response time.', (int) $t['id']);
            }
        }

        $admins = getList("SELECT id FROM tbl_users WHERE Roll=1 AND Status=1 LIMIT 20");
        if (is_array($admins)) {
            $q3 = $conn->query("SELECT * FROM tbl_tickets WHERE is_overdue=1 AND status NOT IN ('resolved','closed','cancelled','rejected')");
            while ($q3 && ($t = $q3->fetch_assoc())) {
                foreach ($admins as $a) {
                    tkt_notify($conn, (int) $a['id'], 'escalation', 'Overdue Ticket Escalation', 'Ticket ' . $t['ticket_no'] . ' requires admin attention.', (int) $t['id']);
                }
            }
        }
    }
}

if (!function_exists('tkt_auto_close_resolved')) {
    function tkt_auto_close_resolved($conn)
    {
        $days = (int) tkt_get_setting($conn, 'auto_close_days', '7');
        if ($days < 1) {
            return;
        }
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $st = tkt_get_status_by_slug($conn, 'closed');
        $statusId = $st ? (int) $st['id'] : 8;
        $conn->query("UPDATE tbl_tickets SET status='closed', status_id='$statusId', closed_at=NOW(), updated_at=NOW()
            WHERE status='resolved' AND resolved_at IS NOT NULL AND resolved_at < '$cutoff'");
    }
}

if (!function_exists('tkt_unread_notification_count')) {
    function tkt_unread_notification_count($conn, $userId)
    {
        $userId = (int) $userId;
        $row = getRecord("SELECT COUNT(*) AS c FROM tbl_ticket_notifications WHERE user_id='$userId' AND is_read=0");
        return $row ? (int) $row['c'] : 0;
    }
}

if (!function_exists('tkt_badge_class')) {
    function tkt_badge_class($slug)
    {
        $map = array(
            'open' => 'primary', 'assigned' => 'info', 'in_progress' => 'warning',
            'waiting_for_user' => 'info', 'waiting_for_approval' => 'secondary',
            'waiting_for_vendor' => 'secondary', 'resolved' => 'success',
            'closed' => 'dark', 'reopened' => 'danger', 'rejected' => 'danger',
            'cancelled' => 'secondary', 'on_hold' => 'light',
            'low' => 'info', 'medium' => 'warning', 'high' => 'orange', 'critical' => 'danger',
        );
        return $map[$slug] ?? 'secondary';
    }
}

if (!function_exists('tkt_employee_sql_filter')) {
    function tkt_employee_sql_filter($alias = 'u')
    {
        return "CAST($alias.Roll AS UNSIGNED) NOT IN(1,5,55,9,22,23,63,3) AND $alias.OtherEmp=0 AND $alias.Status=1 AND $alias.cofofr=0";
    }
}

if (!function_exists('tkt_employee_passes_filter')) {
    function tkt_employee_passes_filter($conn, $userId)
    {
        $userId = (int) $userId;
        if ($userId < 1) {
            return false;
        }
        $row = getRecord("SELECT id FROM tbl_users u WHERE u.id='$userId' AND " . tkt_employee_sql_filter('u') . " LIMIT 1");
        return !empty($row['id']);
    }
}

if (!function_exists('tkt_get_legacy_dept_id_for_ticket_dept')) {
    function tkt_get_legacy_dept_id_for_ticket_dept($conn, $deptId)
    {
        return tkt_resolve_department_id($conn, (int) $deptId);
    }
}

if (!function_exists('tkt_get_employees_grouped')) {
    /**
     * @param int $legacyDeptId tbl_departments.id via Designation; 0 = all departments
     */
    function tkt_get_employees_grouped($conn, $legacyDeptId = 0)
    {
        $where = tkt_employee_sql_filter('u');
        if ($legacyDeptId > 0) {
            $where .= " AND u.Designation='" . (int) $legacyDeptId . "'";
        }
        $rows = getList("SELECT u.id, TRIM(CONCAT(u.Fname,' ',IFNULL(u.Lname,''))) AS name, u.Designation,
            COALESCE(NULLIF(d.Name,''), 'Other / Unassigned') AS dept_name
            FROM tbl_users u
            LEFT JOIN tbl_departments d ON d.id=u.Designation
            WHERE $where
            ORDER BY dept_name ASC, u.Fname ASC, u.Lname ASC") ?: array();
        $grouped = array();
        foreach ($rows as $r) {
            $grouped[$r['dept_name']][] = array(
                'id' => (int) $r['id'],
                'name' => $r['name'],
            );
        }
        return $grouped;
    }
}

if (!function_exists('tkt_build_employee_select_options')) {
    function tkt_build_employee_select_options($conn, $selectedId = 0, $legacyDeptId = 0, $includeEmpty = false, $emptyLabel = 'Select employee')
    {
        $selectedId = (int) $selectedId;
        $legacyDeptId = (int) $legacyDeptId;
        $html = '';
        if ($includeEmpty) {
            $html .= '<option value="">' . tkt_h($emptyLabel) . '</option>';
        }
        if ($legacyDeptId < 1 && $includeEmpty && $emptyLabel === 'Unassigned') {
            if ($selectedId > 0 && tkt_employee_passes_filter($conn, $selectedId)) {
                $u = getRecord("SELECT u.id, TRIM(CONCAT(u.Fname,' ',IFNULL(u.Lname,''))) AS name, COALESCE(NULLIF(d.Name,''), 'Other') AS dept_name
                    FROM tbl_users u LEFT JOIN tbl_departments d ON d.id=u.Designation
                    WHERE u.id='$selectedId' AND " . tkt_employee_sql_filter('u') . " LIMIT 1");
                if ($u) {
                    $html .= '<option value="' . (int) $u['id'] . '" selected>' . tkt_h($u['name'] . ' — ' . $u['dept_name']) . '</option>';
                }
            }
            return $html;
        }
        $grouped = tkt_get_employees_grouped($conn, $legacyDeptId);
        $foundSelected = false;
        foreach ($grouped as $deptName => $employees) {
            foreach ($employees as $e) {
                if ($selectedId === (int) $e['id']) {
                    $foundSelected = true;
                }
                $sel = ($selectedId === (int) $e['id']) ? ' selected' : '';
                $label = trim($e['name'] . ' — ' . $deptName);
                $html .= '<option value="' . (int) $e['id'] . '"' . $sel . '>' . tkt_h($label) . '</option>';
            }
        }
        if ($selectedId > 0 && !$foundSelected && tkt_employee_passes_filter($conn, $selectedId)) {
            $u = getRecord("SELECT u.id, TRIM(CONCAT(u.Fname,' ',IFNULL(u.Lname,''))) AS name, COALESCE(NULLIF(d.Name,''), 'Other') AS dept_name
                FROM tbl_users u LEFT JOIN tbl_departments d ON d.id=u.Designation
                WHERE u.id='$selectedId' AND " . tkt_employee_sql_filter('u') . " LIMIT 1");
            if ($u) {
                $html .= '<option value="' . (int) $u['id'] . '" selected>' . tkt_h($u['name'] . ' — ' . $u['dept_name']) . '</option>';
            }
        }
        return $html;
    }
}

if (!function_exists('tkt_get_categories_by_department')) {
    function tkt_get_categories_by_department($conn, $ticketDeptId)
    {
        $ticketDeptId = (int) $ticketDeptId;
        if ($ticketDeptId < 1) {
            return array();
        }
        return getList("SELECT id, name FROM tbl_ticket_categories WHERE status=1 AND department_id='$ticketDeptId' ORDER BY sort_order ASC, name ASC") ?: array();
    }
}

if (!function_exists('tkt_build_category_select_options')) {
    function tkt_build_category_select_options($conn, $ticketDeptId, $selectedId = 0)
    {
        $ticketDeptId = (int) $ticketDeptId;
        if ($ticketDeptId < 1) {
            return '<option value="">Select department first</option>';
        }
        $cats = tkt_get_categories_by_department($conn, $ticketDeptId);
        $html = '<option value="">Select category</option>';
        if (empty($cats)) {
            return '<option value="">No categories for this department</option>';
        }
        foreach ($cats as $c) {
            $sel = ((int) $selectedId === (int) $c['id']) ? ' selected' : '';
            $html .= '<option value="' . (int) $c['id'] . '"' . $sel . '>' . tkt_h($c['name']) . '</option>';
        }
        return $html;
    }
}

if (!function_exists('tkt_render_filters')) {
    function tkt_render_filters($conn, $filters = array(), $showAll = true)
    {
        $from = $filters['from_date'] ?? '';
        $to = $filters['to_date'] ?? '';
        ?>
        <form method="post" class="mb-3">
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control" value="<?php echo tkt_h($from); ?>">
                </div>
                <div class="form-group col-md-2">
                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control" value="<?php echo tkt_h($to); ?>">
                </div>
                <div class="form-group col-md-2">
                    <label>Department</label>
                    <select name="department_id" class="form-control">
                        <option value="all">All</option>
                        <?php foreach (tkt_get_departments($conn) as $d) { ?>
                        <option value="<?php echo (int) $d['id']; ?>" <?php echo (($filters['department_id'] ?? '') == $d['id']) ? 'selected' : ''; ?>><?php echo tkt_h($d['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="all">All</option>
                        <?php foreach (getList("SELECT id, name FROM tbl_ticket_categories WHERE status=1 ORDER BY name") ?: array() as $c) { ?>
                        <option value="<?php echo (int) $c['id']; ?>" <?php echo (($filters['category_id'] ?? '') == $c['id']) ? 'selected' : ''; ?>><?php echo tkt_h($c['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Priority</label>
                    <select name="priority_id" class="form-control">
                        <option value="all">All</option>
                        <?php foreach (getList("SELECT id, name FROM tbl_ticket_priorities WHERE status=1 ORDER BY sort_order") ?: array() as $p) { ?>
                        <option value="<?php echo (int) $p['id']; ?>" <?php echo (($filters['priority_id'] ?? '') == $p['id']) ? 'selected' : ''; ?>><?php echo tkt_h($p['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="all">All</option>
                        <?php foreach (getList("SELECT slug, name FROM tbl_ticket_status WHERE status=1 ORDER BY sort_order") ?: array() as $s) { ?>
                        <option value="<?php echo tkt_h($s['slug']); ?>" <?php echo (($filters['status'] ?? '') == $s['slug']) ? 'selected' : ''; ?>><?php echo tkt_h($s['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Outlet / Branch</label>
                    <select name="branch_id" class="form-control">
                        <option value="all">All</option>
                        <?php foreach (getList("SELECT id, ShopName AS name FROM tbl_users WHERE Roll=5 AND Status=1 ORDER BY ShopName LIMIT 500") ?: array() as $b) { ?>
                        <option value="<?php echo (int) $b['id']; ?>" <?php echo (($filters['branch_id'] ?? '') == $b['id']) ? 'selected' : ''; ?>><?php echo tkt_h($b['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Assigned To</label>
                    <select name="assigned_to" class="form-control">
                        <option value="all">All</option>
                        <?php foreach (getList("SELECT id, CONCAT(Fname,' ',Lname) AS name FROM tbl_users WHERE Status=1 AND Roll NOT IN(5,55) ORDER BY Fname LIMIT 500") ?: array() as $e) { ?>
                        <option value="<?php echo (int) $e['id']; ?>" <?php echo (($filters['assigned_to'] ?? '') == $e['id']) ? 'selected' : ''; ?>><?php echo tkt_h($e['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Created By</label>
                    <select name="created_by" class="form-control">
                        <option value="all">All</option>
                        <?php foreach (getList("SELECT id, CONCAT(Fname,' ',Lname) AS name FROM tbl_users WHERE Status=1 AND Roll NOT IN(5,55) ORDER BY Fname LIMIT 500") ?: array() as $e) { ?>
                        <option value="<?php echo (int) $e['id']; ?>" <?php echo (($filters['created_by'] ?? '') == $e['id']) ? 'selected' : ''; ?>><?php echo tkt_h($e['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-2" style="padding-top:25px;">
                    <button type="submit" name="search" value="1" class="btn btn-primary">Search</button>
                    <a href="<?php echo tkt_h($_SERVER['PHP_SELF'] . '?view=' . urlencode($_GET['view'] ?? 'all')); ?>" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
        <?php
    }
}
