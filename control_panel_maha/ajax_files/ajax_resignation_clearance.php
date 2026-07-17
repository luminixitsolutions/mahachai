<?php
session_start();
include_once '../config.php';

$admin_id = $_SESSION['Admin']['id'] ?? 0;
$currentDate = date('Y-m-d H:i:s');
$user_id = (int) $admin_id;

if (!function_exists('approval_mail_notify_clearance')) {
    function approval_mail_notify_clearance($conn, $clearanceId, $stage, $status, $actorId, $remarks)
    {
        require_once dirname(__DIR__) . '/includes/approval_mail_service.php';
        @approval_mail_notify($conn, 'resignation_clearance', (int) $clearanceId, $stage, $status, (int) $actorId, $remarks);
    }
}

if($_POST['action'] == 'approveManager') {
    $resignId = intval($_POST['resign_id']);
    $userId = intval($_POST['user_id']);
    $status = intval($_POST['status']);
    $remarks = addslashes(trim($_POST['remarks'] ?? ''));
    
    // Update resign request
    $sql = "UPDATE tbl_resign_request SET ManagerStatus = '$status' WHERE id = '$resignId'";
    $conn->query($sql);
    
    if($status == 1) {
        // Get user department
        $sqlUser = "SELECT DeptId FROM tbl_users WHERE id = '$userId'";
        $userData = getRecord($sqlUser);
        $deptId = $userData['DeptId'] ?? 0;
        
        // Get last working day from resign request
        $sqlResign = "SELECT LastWorkingDay FROM tbl_resign_request WHERE id = '$resignId'";
        $resignData = getRecord($sqlResign);
        $lastWorkingDay = $resignData['LastWorkingDay'];
        
        // Create clearance record
        $sqlInsert = "INSERT INTO tbl_resignation_clearance SET 
            ResignRequestId = '$resignId',
            UserId = '$userId',
            DepartmentId = '$deptId',
            ManagerStatus = 1,
            ManagerApprovedBy = '$admin_id',
            ManagerApprovedDate = '$currentDate',
            ManagerRemarks = '$remarks',
            AttendanceDisabled = 1,
            AttendanceDisabledDate = '$currentDate',
            AttendanceDisabledBy = '$admin_id',
            LastWorkingDay = '$lastWorkingDay',
            CreatedDate = '$currentDate',
            CreatedBy = '$admin_id'";
        $conn->query($sqlInsert);
        $clearanceId = $conn->insert_id;
        
        // Update resign request with clearance ID
        $sql = "UPDATE tbl_resign_request SET ClearanceInitiated = 1, ClearanceId = '$clearanceId' WHERE id = '$resignId'";
        $conn->query($sql);
        
        // Auto-disable attendance app for the user
        $sqlUpdateUser = "UPDATE tbl_users SET AttendanceAppStatus = 0 WHERE id = '$userId'";
        $conn->query($sqlUpdateUser);
        
        // Create default checklist items
        $masterItems = getList("SELECT * FROM tbl_clearance_checklist_master WHERE IsActive = 1");
        foreach($masterItems as $item) {
            $itemName = addslashes($item['ItemName']);
            $itemType = $item['ClearanceType'];
            $sqlChecklist = "INSERT INTO tbl_clearance_checklist SET 
                ClearanceId = '$clearanceId',
                ClearanceType = '$itemType',
                ItemName = '$itemName',
                ItemStatus = 0";
            $conn->query($sqlChecklist);
        }
        
        // Log activity
        $logAction = "Manager approved resignation";
        $sqlLog = "INSERT INTO tbl_clearance_log SET 
            ClearanceId = '$clearanceId',
            Action = '$logAction',
            ActionBy = '$admin_id',
            ActionDate = '$currentDate',
            Remarks = '$remarks'";
        $conn->query($sqlLog);
        
        // Log attendance disabled
        $logAction = "Attendance app automatically disabled";
        $sqlLog = "INSERT INTO tbl_clearance_log SET 
            ClearanceId = '$clearanceId',
            Action = '$logAction',
            ActionBy = '$admin_id',
            ActionDate = '$currentDate'";
        $conn->query($sqlLog);

        // IT Assets: auto-generate return requests to Main IT Godown (does not break clearance flow)
        $itaHelper = dirname(__DIR__) . '/includes/it_asset_helpers.php';
        if (is_file($itaHelper)) {
            require_once $itaHelper;
            if (function_exists('ita_tables_ready') && ita_tables_ready() && function_exists('ita_generate_returns_for_resignation')) {
                $itaResults = ita_generate_returns_for_resignation($userId, $resignId, $clearanceId, $admin_id);
                $itaCount = is_array($itaResults) ? count($itaResults) : 0;
                $sqlLog = "INSERT INTO tbl_clearance_log SET 
                    ClearanceId = '$clearanceId',
                    Action = 'IT asset return requests generated ($itaCount)',
                    ActionBy = '$admin_id',
                    ActionDate = '$currentDate',
                    Remarks = 'Assets must return to Main IT Godown before IT clearance'";
                $conn->query($sqlLog);
            }
        }
        
        require_once dirname(__DIR__) . '/includes/approval_mail_service.php';
        @approval_mail_notify($conn, 'resign', (int) $resignId, 'manager', $status, (int) $admin_id, $remarks);
        echo 1;
    } else {
        require_once dirname(__DIR__) . '/includes/approval_mail_service.php';
        @approval_mail_notify($conn, 'resign', (int) $resignId, 'manager', $status, (int) $admin_id, $remarks);
        echo 1;
    }
}

if($_POST['action'] == 'processITClearance') {
    $clearanceId = intval($_POST['clearance_id']);
    $resignId = intval($_POST['resign_id']);
    $status = intval($_POST['status']);
    $remarks = addslashes(trim($_POST['remarks'] ?? ''));

    // Block IT cleared (status=1) while assigned IT assets are not fully returned + inspected
    if ($status == 1) {
        $clearanceRow = getRecord("SELECT UserId FROM tbl_resignation_clearance WHERE id='$clearanceId' LIMIT 1");
        $empIdForIt = (int) ($clearanceRow['UserId'] ?? 0);
        $itaHelper = dirname(__DIR__) . '/includes/it_asset_helpers.php';
        if ($empIdForIt > 0 && is_file($itaHelper)) {
            require_once $itaHelper;
            if (function_exists('ita_tables_ready') && ita_tables_ready() && function_exists('ita_can_complete_it_clearance')) {
                $gate = ita_can_complete_it_clearance($empIdForIt);
                if (empty($gate['ok'])) {
                    header('Content-Type: application/json; charset=utf-8');
                    $pendingCodes = array();
                    foreach (($gate['pending'] ?? array()) as $p) {
                        $pendingCodes[] = ($p['asset_code'] ?? '') . ' (' . ($p['status'] ?? '') . ')';
                    }
                    echo json_encode(array(
                        'success' => false,
                        'message' => 'Cannot complete IT clearance until all assets are received in Main IT Godown and inspected. Pending: ' . implode(', ', $pendingCodes),
                    ));
                    exit;
                }
            }
        }
    }
    
    $sql = "UPDATE tbl_resignation_clearance SET 
        ITClearanceStatus = '$status',
        ITClearedBy = '$admin_id',
        ITClearedDate = '$currentDate',
        ITRemarks = '$remarks',
        ModifiedDate = '$currentDate',
        ModifiedBy = '$admin_id'
        WHERE id = '$clearanceId'";
    $conn->query($sql);
    
    $logAction = $status == 1 ? "IT Clearance completed" : "IT Clearance issue reported";
    $sqlLog = "INSERT INTO tbl_clearance_log SET 
        ClearanceId = '$clearanceId',
        Action = '$logAction',
        ActionBy = '$admin_id',
        ActionDate = '$currentDate',
        Remarks = '$remarks'";
    $conn->query($sqlLog);
    approval_mail_notify_clearance($conn, $clearanceId, 'it', $status, $admin_id, $remarks);
    echo 1;
}

if($_POST['action'] == 'processDeptClearance') {
    $clearanceId = intval($_POST['clearance_id']);
    $resignId = intval($_POST['resign_id']);
    $status = intval($_POST['status']);
    $remarks = addslashes(trim($_POST['remarks'] ?? ''));
    
    $sql = "UPDATE tbl_resignation_clearance SET 
        DeptClearanceStatus = '$status',
        DeptClearedBy = '$admin_id',
        DeptClearedDate = '$currentDate',
        DeptRemarks = '$remarks',
        ModifiedDate = '$currentDate',
        ModifiedBy = '$admin_id'
        WHERE id = '$clearanceId'";
    $conn->query($sql);
    
    $logAction = $status == 1 ? "Department Clearance completed by HOD" : "Department Clearance issue reported";
    $sqlLog = "INSERT INTO tbl_clearance_log SET 
        ClearanceId = '$clearanceId',
        Action = '$logAction',
        ActionBy = '$admin_id',
        ActionDate = '$currentDate',
        Remarks = '$remarks'";
    $conn->query($sqlLog);
    
    approval_mail_notify_clearance($conn, $clearanceId, 'department', $status, $admin_id, $remarks);
    echo 1;
}

if($_POST['action'] == 'processAccountsClearance') {
    $clearanceId = intval($_POST['clearance_id']);
    $resignId = intval($_POST['resign_id']);
    $status = intval($_POST['status']);
    $remarks = addslashes(trim($_POST['remarks'] ?? ''));
    $settlementAmount = floatval($_POST['settlement_amount'] ?? 0);
    $pendingDues = floatval($_POST['pending_dues'] ?? 0);
    
    $sql = "UPDATE tbl_resignation_clearance SET 
        AccountsClearanceStatus = '$status',
        AccountsClearedBy = '$admin_id',
        AccountsClearedDate = '$currentDate',
        AccountsRemarks = '$remarks',
        SettlementAmount = '$settlementAmount',
        PendingDues = '$pendingDues',
        ModifiedDate = '$currentDate',
        ModifiedBy = '$admin_id'
        WHERE id = '$clearanceId'";
    $conn->query($sql);
    
    $logAction = $status == 1 ? "Accounts Clearance completed (Settlement: ₹$settlementAmount, Dues: ₹$pendingDues)" : "Accounts Clearance issue reported";
    $sqlLog = "INSERT INTO tbl_clearance_log SET 
        ClearanceId = '$clearanceId',
        Action = '$logAction',
        ActionBy = '$admin_id',
        ActionDate = '$currentDate',
        Remarks = '$remarks'";
    $conn->query($sqlLog);
    
    approval_mail_notify_clearance($conn, $clearanceId, 'account', $status, $admin_id, $remarks);
    echo 1;
}

if($_POST['action'] == 'processHRClearance') {
    $clearanceId = intval($_POST['clearance_id']);
    $resignId = intval($_POST['resign_id']);
    $userId = intval($_POST['user_id']);
    $status = intval($_POST['status']);
    $remarks = addslashes(trim($_POST['remarks'] ?? ''));
    $exitInterview = isset($_POST['exit_interview']) ? 1 : 0;
    $exitNotes = addslashes(trim($_POST['exit_notes'] ?? ''));
    $relievingLetter = isset($_POST['relieving_letter']) ? 1 : 0;
    
    $overallStatus = $status == 1 ? 1 : 0;
    $employeeReactivated = $status == 1 ? 1 : 0;
    $reactivatedDate = $status == 1 ? $currentDate : null;
    $relievingLetterDate = $relievingLetter ? date('Y-m-d') : null;
    
    $sql = "UPDATE tbl_resignation_clearance SET 
        HRClearanceStatus = '$status',
        HRClearedBy = '$admin_id',
        HRClearedDate = '$currentDate',
        HRRemarks = '$remarks',
        ExitInterviewDone = '$exitInterview',
        ExitInterviewNotes = '$exitNotes',
        RelievingLetterIssued = '$relievingLetter',
        RelievingLetterDate = " . ($relievingLetterDate ? "'$relievingLetterDate'" : "NULL") . ",
        OverallStatus = '$overallStatus',
        EmployeeReactivated = '$employeeReactivated',
        ReactivatedDate = " . ($reactivatedDate ? "'$reactivatedDate'" : "NULL") . ",
        ModifiedDate = '$currentDate',
        ModifiedBy = '$admin_id'
        WHERE id = '$clearanceId'";
    $conn->query($sql);
    
    // Update HR status in resign request
    if($status == 1) {
        $sqlResign = "UPDATE tbl_resign_request SET HrStatus = 1 WHERE id = '$resignId'";
        $conn->query($sqlResign);
        
        // Reactivate employee (enable attendance app again)
        $sqlUser = "UPDATE tbl_users SET AttendanceAppStatus = 1 WHERE id = '$userId'";
        $conn->query($sqlUser);
    }
    
    $logAction = $status == 1 ? "HR Final Clearance completed - Employee Reactivated" : "HR Clearance issue reported";
    $sqlLog = "INSERT INTO tbl_clearance_log SET 
        ClearanceId = '$clearanceId',
        Action = '$logAction',
        ActionBy = '$admin_id',
        ActionDate = '$currentDate',
        Remarks = '$remarks'";
    $conn->query($sqlLog);
    
    if($exitInterview) {
        $logAction = "Exit Interview completed";
        $sqlLog = "INSERT INTO tbl_clearance_log SET 
            ClearanceId = '$clearanceId',
            Action = '$logAction',
            ActionBy = '$admin_id',
            ActionDate = '$currentDate',
            Remarks = '$exitNotes'";
        $conn->query($sqlLog);
    }
    
    if($relievingLetter) {
        $logAction = "Relieving Letter issued";
        $sqlLog = "INSERT INTO tbl_clearance_log SET 
            ClearanceId = '$clearanceId',
            Action = '$logAction',
            ActionBy = '$admin_id',
            ActionDate = '$currentDate'";
        $conn->query($sqlLog);
    }
    
    approval_mail_notify_clearance($conn, $clearanceId, 'hr', $status, $admin_id, $remarks);
    echo 1;
}

if($_POST['action'] == 'updateChecklist') {
    $clearanceId = intval($_POST['clearance_id']);
    $type = addslashes($_POST['type']);
    $item = addslashes($_POST['item']);
    $status = intval($_POST['status']);
    
    // Check if item exists
    $sqlCheck = "SELECT id FROM tbl_clearance_checklist WHERE ClearanceId = '$clearanceId' AND ClearanceType = '$type' AND ItemName = '$item'";
    $existing = getRecord($sqlCheck);
    
    if($existing) {
        $sql = "UPDATE tbl_clearance_checklist SET 
            ItemStatus = '$status',
            CheckedBy = '$admin_id',
            CheckedDate = '$currentDate'
            WHERE id = '".$existing['id']."'";
        $conn->query($sql);
    } else {
        $sql = "INSERT INTO tbl_clearance_checklist SET 
            ClearanceId = '$clearanceId',
            ClearanceType = '$type',
            ItemName = '$item',
            ItemStatus = '$status',
            CheckedBy = '$admin_id',
            CheckedDate = '$currentDate'";
        $conn->query($sql);
    }
    
    echo 1;
}
?>
