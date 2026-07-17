<?php
session_start();
include_once 'config.php';
include_once 'auth.php';
require_once __DIR__ . '/../includes/petty_limit_helpers.php';

plt_ensure_table($conn);
plt_ensure_menu_options($conn);

$user_id = $_SESSION['Admin']['id'];
$MainPage = 'Pretty-Cash-Account';
$Page = 'Account-Pending-Petty-Limit-Request';
$id = (int) ($_GET['id'] ?? 0);

$row7 = getRecord("SELECT te.*,tu.Fname,tu.Lname FROM tbl_petty_limit_request te LEFT JOIN tbl_users tu ON tu.id=te.UserId WHERE te.id='$id'");
$EmpId = $row7 ? (int) $row7['UserId'] : 0;

if (isset($_POST['submit']) && $row7) {
    $ApproveDate = addslashes(trim($_POST['ApproveDate']));
    $AccComment = addslashes(trim($_POST['AccComment']));
    $AccAmount = addslashes(trim($_POST['AccAmount']));
    $AccStatus = addslashes(trim($_POST['AccStatus']));
    $conn->query("UPDATE tbl_petty_limit_request SET AccApproveDate='$ApproveDate',AccComment='$AccComment',AccAmount='$AccAmount',AccStatus='$AccStatus',AccBy='$user_id',ModifiedDate=NOW(),ModifiedBy='$user_id' WHERE id='$id'");

    if ((string) $AccStatus === '1') {
        $newLimit = (float) $AccAmount;
        if ($newLimit > 0) {
            $conn->query("UPDATE tbl_users SET PettyAmount='$newLimit' WHERE id='$EmpId'");
        }
    }

    require_once __DIR__ . '/includes/approval_mail_service.php';
    @approval_mail_notify($conn, 'petty_limit', (int)$id, 'account', $AccStatus, (int)$user_id, $AccComment);
    echo "<script>alert('Saved Successfully!');window.location.href='account-pending-petty-limit-request.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="default-style">
<head>
    <title><?php echo $Proj_Title; ?> - Account Approve Petty Limit</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <?php include_once 'header_script.php'; ?>
</head>
<body>
<div class="layout-wrapper layout-1 layout-without-sidenav">
<div class="layout-inner">
<?php include_once 'top_header.php'; include_once 'sidebar.php'; ?>
<div class="layout-container">
<div class="layout-content">
<div class="container-fluid flex-grow-1 container-p-y">
<h4 class="font-weight-bold py-3 mb-0">Account Approve Petty Cash Limit Request</h4>
<div class="card mb-4"><div class="card-body">
<?php if (!$row7) { ?><div class="alert alert-warning">Request not found.</div><?php } else { ?>
<form method="post">
    <div class="form-row">
        <div class="form-group col-md-6"><label>Employee</label><input class="form-control" readonly value="<?php echo htmlspecialchars($row7['Fname'].' '.$row7['Lname']); ?>"></div>
        <div class="form-group col-md-3"><label>Current Limit</label><input class="form-control" readonly value="<?php echo number_format((float)$row7['CurrentLimit'],2); ?>"></div>
        <div class="form-group col-md-3"><label>Requested Limit</label><input class="form-control" readonly value="<?php echo number_format((float)$row7['RequestedLimit'],2); ?>"></div>
        <div class="form-group col-md-4"><label>Admin Approved</label><input class="form-control" readonly value="<?php echo htmlspecialchars($row7['AdminAmount']); ?>"></div>
        <div class="form-group col-md-4"><label>Final Approve Limit</label><input type="text" name="AccAmount" class="form-control" value="<?php echo htmlspecialchars($row7['AdminAmount'] ?: $row7['RequestedLimit']); ?>" required></div>
        <div class="form-group col-md-4"><label>Approve Date</label><input type="date" name="ApproveDate" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly required></div>
        <div class="form-group col-md-4"><label>Status</label>
            <select name="AccStatus" class="form-control" required>
                <option value="1">Approved</option>
                <option value="0">Pending</option>
                <option value="2">Reject</option>
            </select>
        </div>
        <div class="form-group col-md-12"><label>Reason</label><input class="form-control" readonly value="<?php echo htmlspecialchars($row7['Narration']); ?>"></div>
        <div class="form-group col-md-12"><label>Comment</label><textarea name="AccComment" class="form-control"><?php echo htmlspecialchars($row7['AccComment']); ?></textarea></div>
        <div class="form-group col-md-12"><button type="submit" name="submit" class="btn btn-primary">Save &amp; Update Limit</button></div>
    </div>
</form>
<?php } ?>
</div></div>
</div>
<?php include_once 'footer.php'; ?>
</div></div></div>
<?php include_once 'footer_script.php'; ?>
</body>
</html>
