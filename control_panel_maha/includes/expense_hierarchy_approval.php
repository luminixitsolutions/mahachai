<?php
/**
 * Dynamic multi-level expense approval based on employee reporting hierarchy (UnderByUser).
 * Used by mult=2 employee expenses (add-expenses-mult-prod).
 */

if (!defined('EXPENSE_HIERARCHY_AMOUNT_THRESHOLD')) {
    define('EXPENSE_HIERARCHY_AMOUNT_THRESHOLD', 2000);
}

function expense_hierarchy_table_exists($conn)
{
    static $exists = null;
    if ($exists !== null) {
        return $exists;
    }
    $res = @$conn->query("SHOW TABLES LIKE 'tbl_expense_approval_levels'");
    $exists = ($res && $res->num_rows > 0);
    return $exists;
}

function expense_hierarchy_ensure_table($conn)
{
    if (expense_hierarchy_table_exists($conn)) {
        return true;
    }
    $sql = "CREATE TABLE IF NOT EXISTS `tbl_expense_approval_levels` (
      `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
      `ExpId` int UNSIGNED NOT NULL,
      `LevelNo` tinyint UNSIGNED NOT NULL,
      `ApproverUserId` int UNSIGNED NOT NULL,
      `Status` enum('waiting','pending','approved','rejected') NOT NULL DEFAULT 'waiting',
      `IsFinal` tinyint(1) NOT NULL DEFAULT 0,
      `Remarks` text,
      `ApprovedDate` date DEFAULT NULL,
      `CreatedDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_eal_expid` (`ExpId`),
      KEY `idx_eal_approver_pending` (`ApproverUserId`, `Status`),
      KEY `idx_eal_exp_level` (`ExpId`, `LevelNo`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    if (@$conn->query($sql)) {
        return true;
    }
    return false;
}

function expense_hierarchy_required_levels($amount)
{
    return ((float) $amount > EXPENSE_HIERARCHY_AMOUNT_THRESHOLD) ? 3 : 2;
}

/** True when tbl_users.Status is active (1). */
function expense_hierarchy_user_is_active($userRow)
{
    if (empty($userRow) || !is_array($userRow)) {
        return false;
    }
    $st = null;
    if (array_key_exists('Status', $userRow)) {
        $st = $userRow['Status'];
    } elseif (array_key_exists('status', $userRow)) {
        $st = $userRow['status'];
    }
    return (string) $st === '1' || (int) $st === 1;
}

/**
 * Full reporting manager chain (active only; inactive managers are skipped).
 *
 * @return array<int, array<string, mixed>>
 */
function expense_hierarchy_get_reporting_chain_managers($conn, $employeeUserId)
{
    $managers = array();
    $visited = array();
    $currentId = (int) $employeeUserId;
    $maxHops = 40;

    while ($maxHops-- > 0) {
        $esc = $conn->real_escape_string((string) $currentId);
        $row = getRecord("SELECT id, UnderByUser FROM tbl_users WHERE id='$esc' LIMIT 1");
        if (empty($row['id'])) {
            break;
        }

        $managerId = (int) ($row['UnderByUser'] ?? 0);
        if ($managerId <= 0) {
            break;
        }

        $nextId = $managerId;
        $found = null;
        while ($nextId > 0) {
            if (isset($visited[$nextId])) {
                break;
            }
            $visited[$nextId] = true;

            $escMgr = $conn->real_escape_string((string) $nextId);
            $mgr = getRecord("SELECT tu.id, tu.UnderByUser, tu.Status, tu.Fname, tu.Lname, tut.Name AS RoleName
                FROM tbl_users tu
                LEFT JOIN tbl_user_type tut ON tut.id = tu.Roll
                WHERE tu.id = '$escMgr' LIMIT 1");
            if (empty($mgr['id'])) {
                break;
            }

            if (expense_hierarchy_user_is_active($mgr)) {
                $found = $mgr;
                break;
            }

            $nextId = (int) ($mgr['UnderByUser'] ?? 0);
        }

        if (!$found) {
            break;
        }

        $managers[] = $found;
        $currentId = (int) $found['id'];
    }

    return $managers;
}

/**
 * Walk reporting chain upward; skip inactive users (Status != 1).
 *
 * @return array<int, array<string, mixed>>
 */
function expense_hierarchy_get_active_approvers($conn, $employeeUserId, $requiredLevels)
{
    $chain = expense_hierarchy_get_reporting_chain_managers($conn, $employeeUserId);
    if (empty($chain)) {
        return array();
    }
    return array_slice($chain, 0, max(1, (int) $requiredLevels));
}

function expense_hierarchy_has_levels($conn, $expId)
{
    expense_hierarchy_ensure_table($conn);
    if (!expense_hierarchy_table_exists($conn)) {
        return false;
    }
    $expId = (int) $expId;
    if ($expId < 1) {
        return false;
    }
    return getRow("SELECT id FROM tbl_expense_approval_levels WHERE ExpId='$expId' LIMIT 1") > 0;
}

function expense_hierarchy_delete_levels($conn, $expId)
{
    if (!expense_hierarchy_table_exists($conn)) {
        return;
    }
    $expId = (int) $expId;
    if ($expId > 0) {
        $conn->query("DELETE FROM tbl_expense_approval_levels WHERE ExpId='$expId'");
    }
}

function expense_hierarchy_create_levels($conn, $expId, $employeeUserId, $amount)
{
    expense_hierarchy_ensure_table($conn);
    if (!expense_hierarchy_table_exists($conn)) {
        return false;
    }

    $expId = (int) $expId;
    $employeeUserId = (int) $employeeUserId;
    if ($expId < 1 || $employeeUserId < 1) {
        return false;
    }

    expense_hierarchy_delete_levels($conn, $expId);

    $required = expense_hierarchy_required_levels($amount);
    $approvers = expense_hierarchy_get_active_approvers($conn, $employeeUserId, $required);
    if (empty($approvers)) {
        return false;
    }

    $total = count($approvers);
    foreach ($approvers as $idx => $approver) {
        $levelNo = $idx + 1;
        $approverId = (int) $approver['id'];
        $isFinal = ($levelNo === $total) ? 1 : 0;
        $status = ($levelNo === 1) ? 'pending' : 'waiting';
        $conn->query("INSERT INTO tbl_expense_approval_levels
            SET ExpId='$expId',
                LevelNo='$levelNo',
                ApproverUserId='$approverId',
                Status='$status',
                IsFinal='$isFinal'");
    }

    return true;
}

/**
 * Rebuild approval levels when pending/waiting approvers are inactive or chain changed.
 */
function expense_hierarchy_rebuild_stale_levels($conn, $expId)
{
    if (!expense_hierarchy_has_levels($conn, $expId)) {
        return false;
    }

    $expId = (int) $expId;
    $expRow = getRecord("SELECT * FROM tbl_expense_request WHERE id='$expId' LIMIT 1");
    if (empty($expRow['id'])) {
        return false;
    }

    $levels = expense_hierarchy_get_levels($conn, $expId);
    if (empty($levels)) {
        return false;
    }

    $required = expense_hierarchy_required_levels($expRow['Amount']);
    $freshChain = expense_hierarchy_get_active_approvers($conn, (int) $expRow['UserId'], $required);

    $needsRebuild = false;
    foreach ($levels as $lv) {
        $st = (string) ($lv['Status'] ?? '');
        if ($st !== 'pending' && $st !== 'waiting') {
            continue;
        }
        $uid = (int) ($lv['ApproverUserId'] ?? 0);
        $u = getRecord("SELECT Status FROM tbl_users WHERE id='$uid' LIMIT 1");
        if (!expense_hierarchy_user_is_active($u)) {
            $needsRebuild = true;
            break;
        }
    }

    if (!$needsRebuild) {
        $lastApproved = 0;
        foreach ($levels as $lv) {
            if ((string) ($lv['Status'] ?? '') === 'approved') {
                $lastApproved = max($lastApproved, (int) ($lv['LevelNo'] ?? 0));
            }
        }
        for ($i = $lastApproved; $i < $required; $i++) {
            $freshId = isset($freshChain[$i]) ? (int) $freshChain[$i]['id'] : 0;
            $storedId = 0;
            $storedSt = '';
            foreach ($levels as $lv) {
                if ((int) ($lv['LevelNo'] ?? 0) === $i + 1) {
                    $storedId = (int) ($lv['ApproverUserId'] ?? 0);
                    $storedSt = (string) ($lv['Status'] ?? '');
                    break;
                }
            }
            if ($storedSt === 'approved') {
                continue;
            }
            if ($freshId > 0 && $storedId > 0 && $freshId !== $storedId) {
                $needsRebuild = true;
                break;
            }
        }
    }

    if (!$needsRebuild) {
        return false;
    }

    $lastApproved = 0;
    foreach ($levels as $lv) {
        if ((string) ($lv['Status'] ?? '') === 'approved') {
            $lastApproved = max($lastApproved, (int) ($lv['LevelNo'] ?? 0));
        }
    }

    if ($lastApproved === 0) {
        return expense_hierarchy_create_levels($conn, $expId, (int) $expRow['UserId'], (float) $expRow['Amount']);
    }

    $remaining = array_slice($freshChain, $lastApproved);
    if (empty($remaining)) {
        return false;
    }

    $conn->query("DELETE FROM tbl_expense_approval_levels WHERE ExpId='$expId' AND LevelNo > '$lastApproved'");
    foreach ($remaining as $idx => $approver) {
        $levelNo = $lastApproved + $idx + 1;
        if ($levelNo > $required) {
            break;
        }
        $approverId = (int) $approver['id'];
        $isFinal = ($levelNo === $required) ? 1 : 0;
        $status = ($idx === 0) ? 'pending' : 'waiting';
        $conn->query("INSERT INTO tbl_expense_approval_levels
            SET ExpId='$expId',
                LevelNo='$levelNo',
                ApproverUserId='$approverId',
                Status='$status',
                IsFinal='$isFinal'");
    }

    return true;
}

function expense_hierarchy_get_levels($conn, $expId)
{
    if (!expense_hierarchy_table_exists($conn)) {
        return array();
    }
    $expId = (int) $expId;
    if ($expId < 1) {
        return array();
    }
    $rows = getList("SELECT eal.*, tu.Fname, tu.Lname, tut.Name AS RoleName
        FROM tbl_expense_approval_levels eal
        LEFT JOIN tbl_users tu ON tu.id = eal.ApproverUserId
        LEFT JOIN tbl_user_type tut ON tut.id = tu.Roll
        WHERE eal.ExpId='$expId'
        ORDER BY eal.LevelNo ASC");
    return is_array($rows) ? $rows : array();
}

function expense_hierarchy_get_active_level($conn, $expId)
{
    if (!expense_hierarchy_table_exists($conn)) {
        return null;
    }
    $expId = (int) $expId;
    expense_hierarchy_rebuild_stale_levels($conn, $expId);
    $row = getRecord("SELECT * FROM tbl_expense_approval_levels WHERE ExpId='$expId' AND Status='pending' ORDER BY LevelNo ASC LIMIT 1");
    return !empty($row['id']) ? $row : null;
}

function expense_hierarchy_user_can_approve($conn, $expId, $userId)
{
    $active = expense_hierarchy_get_active_level($conn, $expId);
    return $active && (int) ($active['ApproverUserId'] ?? 0) === (int) $userId;
}

function expense_hierarchy_level1_approved($conn, $expId)
{
    if (!expense_hierarchy_has_levels($conn, $expId)) {
        return false;
    }
    $expId = (int) $expId;
    $row = getRecord("SELECT Status FROM tbl_expense_approval_levels WHERE ExpId='$expId' AND LevelNo='1' LIMIT 1");
    return (string) ($row['Status'] ?? '') === 'approved';
}

function expense_hierarchy_manager_gate_ok($conn, array $expenseRow)
{
    if (expense_hierarchy_has_levels($conn, (int) ($expenseRow['id'] ?? 0))) {
        return expense_hierarchy_level1_approved($conn, (int) ($expenseRow['id'] ?? 0));
    }
    return (string) ($expenseRow['ManagerStatus'] ?? '') === '1';
}

function expense_hierarchy_activate_next_level($conn, $expId, $afterLevelNo)
{
    $expId = (int) $expId;
    $afterLevelNo = (int) $afterLevelNo;
    $next = getRecord("SELECT id FROM tbl_expense_approval_levels
        WHERE ExpId='$expId' AND LevelNo='" . ($afterLevelNo + 1) . "' AND Status='waiting' LIMIT 1");
    if (!empty($next['id'])) {
        $conn->query("UPDATE tbl_expense_approval_levels SET Status='pending' WHERE id='" . (int) $next['id'] . "'");
        return true;
    }
    return false;
}

function expense_hierarchy_sync_legacy_on_final($conn, $expId, $approverUserId, $approveDate, $remarks, $mgrAmount, $expenseRow)
{
    $expId = (int) $expId;
    $approverUserId = (int) $approverUserId;
    $escDate = $conn->real_escape_string($approveDate);
    $escRemarks = $conn->real_escape_string($remarks);
    $escAmt = $conn->real_escape_string((string) $mgrAmount);
    $empId = (int) ($expenseRow['UserId'] ?? 0);

    $conn->query("UPDATE tbl_expense_request SET
        ManagerStatus='1',
        MrgBy='$approverUserId',
        ApproveDate='$escDate',
        MannagerComment='$escRemarks',
        MgrAmount='$escAmt',
        Status='1'
        WHERE id='$expId'");

    $conn->query("UPDATE tbl_expense_request SET
        BhStatus='1',
        BhBy='$approverUserId',
        BhApproveDate='$escDate',
        BhComment='$escRemarks',
        BhAmount='$escAmt',
        AdminStatus='1',
        AccBy='$approverUserId',
        AdminApproveDate='$escDate',
        AdminComment='$escRemarks',
        AccAmount='$escAmt'
        WHERE id='$expId'");

    $createdDate = date('Y-m-d');
    $createdTime = date('h:i a');
    $conn->query("DELETE FROM wallet WHERE UserId='$empId' AND ExpId='$expId'");
    $narration = $conn->real_escape_string('Amount Deduct against Expense For ' . ($expenseRow['Narration'] ?? ''));
    $conn->query("INSERT INTO wallet SET UserId='$empId',Amount='$escAmt',Narration='$narration',Status='Dr',CreatedDate='$createdDate',CreatedTime='$createdTime',ExpId='$expId'");
}

function expense_hierarchy_sync_legacy_on_reject($conn, $expId, $approverUserId, $approveDate, $remarks)
{
    $expId = (int) $expId;
    $approverUserId = (int) $approverUserId;
    $escDate = $conn->real_escape_string($approveDate);
    $escRemarks = $conn->real_escape_string($remarks);

    $conn->query("UPDATE tbl_expense_request SET
        ManagerStatus='2',
        MrgBy='$approverUserId',
        ApproveDate='$escDate',
        MannagerComment='$escRemarks',
        BhStatus='2',
        BhBy='$approverUserId',
        BhApproveDate='$escDate',
        AdminStatus='2',
        AccBy='$approverUserId',
        AdminApproveDate='$escDate'
        WHERE id='$expId'");
}

/**
 * Process approve/reject for the current hierarchy level.
 *
 * @param string $decision '1' approved, '2' rejected
 */
function expense_hierarchy_process_approval($conn, $expId, $approverUserId, $decision, $remarks, $approveDate, $mgrAmount = null)
{
    if (!expense_hierarchy_table_exists($conn)) {
        return array('ok' => false, 'message' => 'Approval levels table not found.');
    }

    $expId = (int) $expId;
    $approverUserId = (int) $approverUserId;
    $decision = (string) $decision;

    $expenseRow = getRecord("SELECT * FROM tbl_expense_request WHERE id='$expId' LIMIT 1");
    if (empty($expenseRow['id'])) {
        return array('ok' => false, 'message' => 'Expense not found.');
    }

    $active = expense_hierarchy_get_active_level($conn, $expId);
    if (!$active || (int) ($active['ApproverUserId'] ?? 0) !== $approverUserId) {
        return array('ok' => false, 'message' => 'You are not the current approver for this expense.');
    }

    $levelId = (int) $active['id'];
    $levelNo = (int) $active['LevelNo'];
    $isFinal = (int) ($active['IsFinal'] ?? 0) === 1;
    $expCatId = (int) ($expenseRow['ExpCatId'] ?? 0);
    $escRemarks = $conn->real_escape_string($remarks);
    $escDate = $conn->real_escape_string($approveDate);

    if ($decision === '2') {
        $conn->query("UPDATE tbl_expense_approval_levels SET Status='rejected', Remarks='$escRemarks', ApprovedDate='$escDate' WHERE id='$levelId'");
        expense_hierarchy_sync_legacy_on_reject($conn, $expId, $approverUserId, $approveDate, $remarks);
        return array('ok' => true, 'final' => true, 'rejected' => true);
    }

    if ($decision !== '1') {
        return array('ok' => false, 'message' => 'Invalid approval decision.');
    }

    $conn->query("UPDATE tbl_expense_approval_levels SET Status='approved', Remarks='$escRemarks', ApprovedDate='$escDate' WHERE id='$levelId'");

    if ($levelNo === 1) {
        $escMgrAmt = $conn->real_escape_string((string) (($mgrAmount !== null && $mgrAmount !== '') ? $mgrAmount : ($expenseRow['Amount'] ?? 0)));
        $conn->query("UPDATE tbl_expense_request SET
            MrgBy='$approverUserId',
            ApproveDate='$escDate',
            MannagerComment='$escRemarks',
            MgrAmount='$escMgrAmt'
            WHERE id='$expId'");
        if ($expCatId === 3) {
            $conn->query("UPDATE tbl_expense_request SET HrStatus='0' WHERE id='$expId'");
            return array('ok' => true, 'final' => false, 'await_hr' => true);
        }
    }

    if ($isFinal) {
        $finalAmt = ($mgrAmount !== null && $mgrAmount !== '') ? $mgrAmount : ($expenseRow['Amount'] ?? 0);
        expense_hierarchy_sync_legacy_on_final($conn, $expId, $approverUserId, $approveDate, $remarks, $finalAmt, $expenseRow);
        return array('ok' => true, 'final' => true, 'approved' => true);
    }

    expense_hierarchy_activate_next_level($conn, $expId, $levelNo);
    return array('ok' => true, 'final' => false);
}

/** After HR approves salary category expense, activate hierarchy level 2+. */
function expense_hierarchy_activate_after_hr($conn, $expId)
{
    if (!expense_hierarchy_has_levels($conn, $expId)) {
        return false;
    }
    $expId = (int) $expId;
    $level1 = getRecord("SELECT id, Status FROM tbl_expense_approval_levels WHERE ExpId='$expId' AND LevelNo='1' LIMIT 1");
    if (empty($level1['id']) || (string) ($level1['Status'] ?? '') !== 'approved') {
        return false;
    }
    return expense_hierarchy_activate_next_level($conn, $expId, 1);
}

function expense_hierarchy_overall_status($conn, array $expenseRow)
{
    $expId = (int) ($expenseRow['id'] ?? 0);
    if ($expId < 1 || !expense_hierarchy_has_levels($conn, $expId)) {
        return null;
    }

    $levels = expense_hierarchy_get_levels($conn, $expId);
    foreach ($levels as $lv) {
        if ((string) ($lv['Status'] ?? '') === 'rejected') {
            return 'rejected';
        }
    }

    foreach ($levels as $lv) {
        if ((int) ($lv['IsFinal'] ?? 0) === 1 && (string) ($lv['Status'] ?? '') === 'approved') {
            return 'approved';
        }
    }

    if ((string) ($expenseRow['ManagerStatus'] ?? '') === '2'
        || (string) ($expenseRow['AdminStatus'] ?? '') === '2'
        || (string) ($expenseRow['BhStatus'] ?? '') === '2') {
        return 'rejected';
    }

    return 'pending';
}

function expense_hierarchy_timeline_dot($status)
{
    if ($status === 'approved') {
        return 'ok';
    }
    if ($status === 'rejected') {
        return 'no';
    }
    return 'wait';
}

function expense_hierarchy_timeline_symbol($dot)
{
    if ($dot === 'ok') {
        return '&#10003;';
    }
    if ($dot === 'no') {
        return '&times;';
    }
    return '&ndash;';
}

function expense_hierarchy_timeline_date_label($dot, $dateRaw, $isFinalLevel, $status)
{
    if ($dot === 'wait' && $status === 'pending') {
        return 'Waiting';
    }
    if ($dot === 'wait' && $isFinalLevel && $status === 'waiting') {
        return 'Final';
    }
    if ($dot === 'wait') {
        return 'Waiting';
    }
    if (empty($dateRaw)) {
        return ($dot === 'ok' || $dot === 'no') ? 'Done' : 'Waiting';
    }
    $ts = strtotime((string) $dateRaw);
    return $ts ? date('d/m/y', $ts) : htmlspecialchars((string) $dateRaw);
}

/**
 * Build timeline steps for view-expenses-mult-prod.php
 *
 * @return array<int, array<string, string>>
 */
function expense_hierarchy_build_timeline($conn, array $expenseRow)
{
    $expId = (int) ($expenseRow['id'] ?? 0);
    expense_hierarchy_rebuild_stale_levels($conn, $expId);
    $levels = expense_hierarchy_get_levels($conn, $expId);
    if (empty($levels)) {
        return array();
    }

    $steps = array();
    $expCatId = (int) ($expenseRow['ExpCatId'] ?? 0);
    $hrInserted = false;

    foreach ($levels as $lv) {
        $name = trim(($lv['Fname'] ?? '') . ' ' . ($lv['Lname'] ?? ''));
        $role = trim($lv['RoleName'] ?? 'Approver');
        $status = (string) ($lv['Status'] ?? 'waiting');
        $isFinal = (int) ($lv['IsFinal'] ?? 0) === 1;
        $levelNo = (int) ($lv['LevelNo'] ?? 0);

        if ($status === 'approved') {
            $text = 'Approved by ' . $name;
        } elseif ($status === 'rejected') {
            $text = 'Rejected by ' . $name;
        } elseif ($status === 'pending') {
            $text = 'Pending by ' . $name . ($isFinal ? ' (final approver)' : '');
        } else {
            $prevApproved = true;
            foreach ($levels as $check) {
                if ((int) ($check['LevelNo'] ?? 0) >= $levelNo) {
                    break;
                }
                if ((string) ($check['Status'] ?? '') !== 'approved') {
                    $prevApproved = false;
                    break;
                }
            }
            if (!$prevApproved) {
                $text = 'After previous level approval';
            } elseif ($expCatId === 3 && $levelNo > 1 && !$hrInserted) {
                $text = 'After HR approval';
            } else {
                $text = 'Pending by ' . $name . ($isFinal ? ' (final approver)' : '');
            }
        }

        $dot = expense_hierarchy_timeline_dot($status);
        if ($text === 'After previous level approval' || $text === 'After HR approval') {
            $dot = 'wait';
        }

        $steps[] = array(
            'role' => 'Level ' . $levelNo . ' — ' . $role,
            'by' => $text,
            'date' => expense_hierarchy_timeline_date_label($dot, $lv['ApprovedDate'] ?? '', $isFinal, $status),
            'dot' => $dot,
            'symbol' => expense_hierarchy_timeline_symbol($dot),
        );

        if ($expCatId === 3 && $levelNo === 1 && !$hrInserted) {
            $hrSt = (string) ($expenseRow['HrStatus'] ?? '');
            $hrName = trim(($expenseRow['HrFname'] ?? '') . ' ' . ($expenseRow['HrLname'] ?? ''));
            if ($hrSt === '1') {
                $hrDot = 'ok';
                $hrText = $hrName !== '' ? 'Approved by ' . $hrName : 'Approved';
            } elseif ($hrSt === '2') {
                $hrDot = 'no';
                $hrText = $hrName !== '' ? 'Rejected by ' . $hrName : 'Rejected';
            } elseif ($status !== 'approved') {
                $hrDot = 'wait';
                $hrText = 'After Level 1 approval';
            } else {
                $hrDot = 'wait';
                $hrText = 'Pending HR approval';
            }
            $steps[] = array(
                'role' => 'HR',
                'by' => $hrText,
                'date' => expense_hierarchy_timeline_date_label(
                    $hrDot,
                    $expenseRow['HrApproveDate'] ?? '',
                    false,
                    $hrSt === '1' ? 'approved' : ($hrSt === '2' ? 'rejected' : 'pending')
                ),
                'dot' => $hrDot,
                'symbol' => expense_hierarchy_timeline_symbol($hrDot),
            );
            $hrInserted = true;
        }
    }

    return $steps;
}

function expense_hierarchy_status_label($status)
{
    $map = array(
        'approved' => array('label' => 'Approved', 'class' => 'success'),
        'rejected' => array('label' => 'Rejected', 'class' => 'danger'),
        'pending' => array('label' => 'Pending', 'class' => 'warning'),
        'waiting' => array('label' => 'Waiting', 'class' => 'secondary'),
    );
    $st = (string) $status;
    return $map[$st] ?? array('label' => ucfirst($st), 'class' => 'secondary');
}

/**
 * HTML approval tree for admin modal (hierarchy + legacy fallback).
 */
function expense_hierarchy_render_history_html($conn, array $expenseRow)
{
    $expId = (int) ($expenseRow['id'] ?? 0);
    expense_hierarchy_rebuild_stale_levels($conn, $expId);
    $html = '<div class="exp-approval-history-tree">';

    if (expense_hierarchy_has_levels($conn, $expId)) {
        $levels = expense_hierarchy_get_levels($conn, $expId);
        $expCatId = (int) ($expenseRow['ExpCatId'] ?? 0);
        $hrInserted = false;

        foreach ($levels as $lv) {
            $name = htmlspecialchars(trim(($lv['Fname'] ?? '') . ' ' . ($lv['Lname'] ?? '')));
            $role = htmlspecialchars(trim($lv['RoleName'] ?? 'Approver'));
            $status = (string) ($lv['Status'] ?? 'waiting');
            $levelNo = (int) ($lv['LevelNo'] ?? 0);
            $isFinal = (int) ($lv['IsFinal'] ?? 0) === 1;
            $stInfo = expense_hierarchy_status_label($status);
            $remarks = trim((string) ($lv['Remarks'] ?? ''));
            $dateRaw = $lv['ApprovedDate'] ?? '';
            $dateFmt = $dateRaw ? date('d/m/Y', strtotime((string) $dateRaw)) : '—';

            $html .= '<div class="exp-approval-node border-left-' . $stInfo['class'] . '">';
            $html .= '<div class="exp-approval-node-head"><strong>Level ' . $levelNo . '</strong> — ' . $role;
            if ($isFinal) {
                $html .= ' <span class="badge badge-info">Final</span>';
            }
            $html .= '</div>';
            $html .= '<div class="exp-approval-node-body">';
            $html .= '<div><strong>' . ($name !== '' ? $name : 'Approver') . '</strong> ';
            $html .= '<span class="badge badge-' . $stInfo['class'] . '">' . $stInfo['label'] . '</span></div>';
            $html .= '<div class="text-muted small mt-1">Date: ' . htmlspecialchars($dateFmt) . '</div>';
            $html .= '<div class="mt-1"><em>Comment:</em> ' . ($remarks !== '' ? htmlspecialchars($remarks) : '<span class="text-muted">—</span>') . '</div>';
            $html .= '</div></div>';

            if ($expCatId === 3 && $levelNo === 1 && !$hrInserted) {
                $hrSt = (string) ($expenseRow['HrStatus'] ?? '');
                $hrName = htmlspecialchars(trim(($expenseRow['HrFname'] ?? '') . ' ' . ($expenseRow['HrLname'] ?? '')));
                $hrStInfo = expense_hierarchy_status_label($hrSt === '1' ? 'approved' : ($hrSt === '2' ? 'rejected' : ($hrSt === '0' ? 'pending' : 'waiting')));
                $hrDate = !empty($expenseRow['HrApproveDate']) ? date('d/m/Y', strtotime((string) $expenseRow['HrApproveDate'])) : '—';
                $hrComment = trim((string) ($expenseRow['HrComment'] ?? ''));

                $html .= '<div class="exp-approval-node border-left-' . $hrStInfo['class'] . '">';
                $html .= '<div class="exp-approval-node-head"><strong>HR</strong> — Salary / Adhoc</div>';
                $html .= '<div class="exp-approval-node-body">';
                $html .= '<div><strong>' . ($hrName !== '' ? $hrName : 'HR') . '</strong> ';
                $html .= '<span class="badge badge-' . $hrStInfo['class'] . '">' . $hrStInfo['label'] . '</span></div>';
                $html .= '<div class="text-muted small mt-1">Date: ' . htmlspecialchars($hrDate) . '</div>';
                $html .= '<div class="mt-1"><em>Comment:</em> ' . ($hrComment !== '' ? htmlspecialchars($hrComment) : '<span class="text-muted">—</span>') . '</div>';
                $html .= '</div></div>';
                $hrInserted = true;
            }
        }
    } else {
        $mgrName = htmlspecialchars(trim($expenseRow['MgrName'] ?? ''));
        $bhName = htmlspecialchars(trim(($expenseRow['BhFname'] ?? '') . ' ' . ($expenseRow['BhLname'] ?? '')));
        $accName = htmlspecialchars(trim($expenseRow['AccName'] ?? ''));

        $legacySteps = array(
            array(
                'title' => 'Level 1 — Manager',
                'name' => $mgrName,
                'status' => (string) ($expenseRow['ManagerStatus'] ?? '0'),
                'date' => $expenseRow['ApproveDate'] ?? '',
                'comment' => $expenseRow['MannagerComment'] ?? '',
            ),
            array(
                'title' => 'Level 2 — Business Head',
                'name' => $bhName,
                'status' => (string) ($expenseRow['BhStatus'] ?? ''),
                'date' => $expenseRow['BhApproveDate'] ?? '',
                'comment' => $expenseRow['BhComment'] ?? '',
            ),
            array(
                'title' => 'Level 3 — Admin',
                'name' => $accName,
                'status' => (string) ($expenseRow['AdminStatus'] ?? '0'),
                'date' => $expenseRow['AdminApproveDate'] ?? '',
                'comment' => $expenseRow['AdminComment'] ?? '',
            ),
        );

        foreach ($legacySteps as $step) {
            $st = $step['status'];
            $stKey = ($st === '1') ? 'approved' : (($st === '2') ? 'rejected' : (($st === '0' || $st === '') ? 'waiting' : 'waiting'));
            $stInfo = expense_hierarchy_status_label($stKey);
            $dateFmt = !empty($step['date']) ? date('d/m/Y', strtotime((string) $step['date'])) : '—';
            $comment = trim((string) $step['comment']);

            $html .= '<div class="exp-approval-node border-left-' . $stInfo['class'] . '">';
            $html .= '<div class="exp-approval-node-head"><strong>' . htmlspecialchars($step['title']) . '</strong></div>';
            $html .= '<div class="exp-approval-node-body">';
            $html .= '<div><strong>' . ($step['name'] !== '' ? $step['name'] : '—') . '</strong> ';
            $html .= '<span class="badge badge-' . $stInfo['class'] . '">' . $stInfo['label'] . '</span></div>';
            $html .= '<div class="text-muted small mt-1">Date: ' . htmlspecialchars($dateFmt) . '</div>';
            $html .= '<div class="mt-1"><em>Comment:</em> ' . ($comment !== '' ? htmlspecialchars($comment) : '<span class="text-muted">—</span>') . '</div>';
            $html .= '</div></div>';
        }
    }

    $html .= '</div>';
    return $html;
}

/**
 * SQL fragment: expenses where $userId approved or rejected (hierarchy + legacy).
 *
 * @param string $mode 'approved' or 'rejected'
 */
function expense_hierarchy_sql_user_acted_clause($conn, $userId, $mode)
{
    $userId = (int) $userId;
    $mode = ($mode === 'rejected') ? 'rejected' : 'approved';
    $parts = array();

    if (expense_hierarchy_table_exists($conn)) {
        $hStatus = ($mode === 'approved') ? 'approved' : 'rejected';
        $parts[] = "te.id IN (SELECT ExpId FROM tbl_expense_approval_levels WHERE ApproverUserId='$userId' AND Status='$hStatus')";
    }

    if ($mode === 'approved') {
        $parts[] = "(te.MrgBy='$userId' AND te.ManagerStatus='1')";
        $parts[] = "(te.BhBy='$userId' AND te.BhStatus='1')";
        $parts[] = "(te.AccBy='$userId' AND te.AdminStatus='1')";
    } else {
        $parts[] = "(te.MrgBy='$userId' AND te.ManagerStatus='2')";
        $parts[] = "(te.BhBy='$userId' AND te.BhStatus='2')";
        $parts[] = "(te.AccBy='$userId' AND te.AdminStatus='2')";
    }

    return '(' . implode(' OR ', $parts) . ')';
}

/**
 * Whether the logged-in user approved/rejected this expense at any hierarchy or legacy level.
 *
 * @param string $mode 'approved' or 'rejected'
 */
function expense_hierarchy_user_acted_on_expense($conn, array $expenseRow, $userId, $mode)
{
    $userId = (int) $userId;
    $expId = (int) ($expenseRow['id'] ?? 0);
    $mode = ($mode === 'rejected') ? 'rejected' : 'approved';
    $hStatus = ($mode === 'approved') ? 'approved' : 'rejected';
    $legacyStatus = ($mode === 'approved') ? '1' : '2';

    if ($expId > 0 && expense_hierarchy_has_levels($conn, $expId)) {
        $cnt = getRow("SELECT COUNT(*) FROM tbl_expense_approval_levels
            WHERE ExpId='$expId' AND ApproverUserId='$userId' AND Status='$hStatus'");
        if ((int) $cnt > 0) {
            return true;
        }
    }

    if ((int) ($expenseRow['MrgBy'] ?? 0) === $userId && (string) ($expenseRow['ManagerStatus'] ?? '') === $legacyStatus) {
        return true;
    }
    if ((int) ($expenseRow['BhBy'] ?? 0) === $userId && (string) ($expenseRow['BhStatus'] ?? '') === $legacyStatus) {
        return true;
    }
    if ((int) ($expenseRow['AccBy'] ?? 0) === $userId && (string) ($expenseRow['AdminStatus'] ?? '') === $legacyStatus) {
        return true;
    }

    return false;
}

/**
 * Level 1–3 display data for list tables (hierarchy or legacy).
 *
 * @return array<int, array<string, mixed>>
 */
function expense_hierarchy_build_level_displays($conn, array $expenseRow)
{
    $expId = (int) ($expenseRow['id'] ?? 0);
    $amount = (float) ($expenseRow['Amount'] ?? 0);
    $required = expense_hierarchy_required_levels($amount);
    $displays = array();

    for ($n = 1; $n <= 3; $n++) {
        $displays[$n] = array(
            'status' => 'waiting',
            'name' => '',
            'date' => '',
            'comment' => '',
        );
    }

    if ($expId > 0 && expense_hierarchy_has_levels($conn, $expId)) {
        $levels = expense_hierarchy_get_levels($conn, $expId);
        $byLevel = array();
        $rejectedBefore = false;

        foreach ($levels as $lv) {
            $byLevel[(int) ($lv['LevelNo'] ?? 0)] = $lv;
        }

        for ($n = 1; $n <= 3; $n++) {
            if ($n > $required) {
                $displays[$n]['status'] = 'not_required';
                continue;
            }
            if ($rejectedBefore) {
                $displays[$n]['status'] = 'blocked';
                continue;
            }
            if (!isset($byLevel[$n])) {
                $displays[$n]['status'] = 'waiting';
                continue;
            }
            $lv = $byLevel[$n];
            $st = (string) ($lv['Status'] ?? 'waiting');
            $displays[$n] = array(
                'status' => $st,
                'name' => trim(($lv['Fname'] ?? '') . ' ' . ($lv['Lname'] ?? '')),
                'date' => $lv['ApprovedDate'] ?? '',
                'comment' => $lv['Remarks'] ?? '',
            );
            if ($st === 'rejected') {
                $rejectedBefore = true;
            }
        }

        return $displays;
    }

    $mgrName = trim($expenseRow['MgrName'] ?? '');
    $bhName = trim(($expenseRow['BhFname'] ?? '') . ' ' . ($expenseRow['BhLname'] ?? ''));
    $accName = trim($expenseRow['AccName'] ?? '');
    $mgrSt = (string) ($expenseRow['ManagerStatus'] ?? '0');
    $bhSt = isset($expenseRow['BhStatus']) ? (string) $expenseRow['BhStatus'] : '';
    $admSt = (string) ($expenseRow['AdminStatus'] ?? '0');
    $expAmtForAdmin = (float) (($expenseRow['MgrAmount'] ?? '') !== '' && $expenseRow['MgrAmount'] !== null
        ? $expenseRow['MgrAmount'] : $amount);
    $adminRequired = ($expAmtForAdmin > EXPENSE_HIERARCHY_AMOUNT_THRESHOLD);

    $legacy = array(
        1 => array(
            'status' => ($mgrSt === '1') ? 'approved' : (($mgrSt === '2') ? 'rejected' : 'pending'),
            'name' => $mgrName,
            'date' => $expenseRow['ApproveDate'] ?? '',
            'comment' => $expenseRow['MannagerComment'] ?? '',
        ),
        2 => array(
            'status' => ($bhSt === '1') ? 'approved' : (($bhSt === '2') ? 'rejected' : (($bhSt === '0') ? 'pending' : 'waiting')),
            'name' => $bhName,
            'date' => $expenseRow['BhApproveDate'] ?? '',
            'comment' => $expenseRow['BhComment'] ?? '',
        ),
        3 => array(
            'status' => $adminRequired
                ? (($admSt === '1') ? 'approved' : (($admSt === '2') ? 'rejected' : 'pending'))
                : 'not_required',
            'name' => $accName,
            'date' => $expenseRow['AdminApproveDate'] ?? '',
            'comment' => $expenseRow['AdminComment'] ?? '',
        ),
    );

    $rejectedBefore = false;
    for ($n = 1; $n <= 3; $n++) {
        if ($rejectedBefore) {
            $displays[$n]['status'] = 'blocked';
            continue;
        }
        $displays[$n] = $legacy[$n];
        if ($displays[$n]['status'] === 'rejected') {
            $rejectedBefore = true;
        }
        if ($n === 2 && $mgrSt === '2') {
            $displays[2]['status'] = 'blocked';
            $displays[3]['status'] = 'blocked';
            break;
        }
    }

    return $displays;
}

/**
 * Echo table cell HTML for one approval level.
 */
function expense_hierarchy_echo_level_cell(array $display)
{
    $status = (string) ($display['status'] ?? 'waiting');
    $name = htmlspecialchars(trim((string) ($display['name'] ?? '')));
    $dateRaw = $display['date'] ?? '';
    $date = '';
    if ($dateRaw !== '' && $dateRaw !== '0000-00-00') {
        $ts = strtotime(str_replace('-', '/', (string) $dateRaw));
        $date = $ts ? date('d/m/Y', $ts) : '';
    }

    if ($status === 'not_required') {
        echo '<span style="color:#6c757d;">Not required</span>';
        if ($name === '') {
            echo '<br><span style="font-size:11px;color:#888;">≤ ₹' . (int) EXPENSE_HIERARCHY_AMOUNT_THRESHOLD . ', final at level 2</span>';
        }
        return;
    }
    if ($status === 'blocked') {
        echo '<span style="font-size:12px;color:#999;">N/A (previous level rejected)</span>';
        return;
    }
    if ($status === 'approved' || $status === '1') {
        echo "<span style='color:green;'>Approved<br>By " . ($name !== '' ? $name : '—');
        if ($date !== '') {
            echo ' | ' . htmlspecialchars($date);
        }
        echo '</span>';
        return;
    }
    if ($status === 'rejected' || $status === '2') {
        echo "<span style='color:red;'>Rejected<br>By " . ($name !== '' ? $name : '—');
        if ($date !== '') {
            echo ' | ' . htmlspecialchars($date);
        }
        echo '</span>';
        return;
    }
    if ($status === 'pending') {
        echo '<span style="color:orange;">Pending</span>';
        if ($name !== '') {
            echo '<br><span style="font-size:12px;color:#495057;">' . $name . '</span>';
        }
        return;
    }

    echo '<span style="color:#adb5bd;">Waiting</span>';
    if ($name !== '') {
        echo '<br><span style="font-size:12px;color:#495057;">' . $name . '</span>';
    }
}
