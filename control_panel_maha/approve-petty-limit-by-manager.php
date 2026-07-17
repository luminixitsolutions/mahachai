<?php
session_start();
include_once 'config.php';
include_once 'auth.php';
require_once __DIR__ . '/../includes/petty_limit_helpers.php';

plt_ensure_table($conn);
plt_ensure_menu_options($conn);

$user_id = $_SESSION['Admin']['id'];
$MainPage = 'HO-Manager-Expenses';
$id = (int) ($_GET['id'] ?? 0);

$sql7 = "SELECT te.*,tu.Fname,tu.Lname,tu.Photo AS Uphoto FROM tbl_petty_limit_request te
    LEFT JOIN tbl_users tu ON tu.id=te.UserId WHERE te.id='$id'";
$row7 = getRecord($sql7);

$managerStatus = (string) ($row7['ManagerStatus'] ?? '0');
$isReadOnly = in_array($managerStatus, array('1', '2'), true);
$Page = 'Manager-Pending-Petty-Limit-Request';
$listBackUrl = 'manager-pending-petty-limit-request.php';

if ($managerStatus === '1') {
    $Page = 'Manager-Approve-Petty-Limit-Request';
    $listBackUrl = 'manager-pending-petty-limit-request.php?status=approved';
}
if ($managerStatus === '2') {
    $Page = 'Manager-Reject-Petty-Limit-Request';
    $listBackUrl = 'manager-pending-petty-limit-request.php?status=rejected';
}

if (isset($_POST['submit']) && $row7 && !$isReadOnly) {
    $ApproveDate = addslashes(trim($_POST['ApproveDate']));
    $MannagerComment = addslashes(trim($_POST['MannagerComment']));
    $MgrAmount = addslashes(trim($_POST['MgrAmount']));
    $ManagerStatus = addslashes(trim($_POST['ManagerStatus']));
    $conn->query("UPDATE tbl_petty_limit_request SET MannagerApproveDate='$ApproveDate',MannagerComment='$MannagerComment',MannagerAmount='$MgrAmount',ManagerStatus='$ManagerStatus',MrgBy='$user_id',ModifiedDate=NOW(),ModifiedBy='$user_id' WHERE id='$id' AND ManagerStatus='0'");
    require_once __DIR__ . '/includes/approval_mail_service.php';
    @approval_mail_notify($conn, 'petty_limit', (int)$id, 'manager', $ManagerStatus, (int)$user_id, $MannagerComment);

    $redirectUrl = 'manager-pending-petty-limit-request.php';
    if ($ManagerStatus === '1') {
        $redirectUrl = 'manager-pending-petty-limit-request.php?status=approved';
    } elseif ($ManagerStatus === '2') {
        $redirectUrl = 'manager-pending-petty-limit-request.php?status=rejected';
    }
    echo "<script>alert('Saved Successfully!');window.location.href='" . $redirectUrl . "';</script>";
    exit;
}

$pageTitle = $isReadOnly ? 'View Petty Cash Limit Request' : 'Approve Petty Cash Limit Request';
?>
<!DOCTYPE html>
<html lang="en" class="default-style">
<head>
    <title><?php echo $Proj_Title; ?> - <?php echo htmlspecialchars($pageTitle); ?></title>
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
<h4 class="font-weight-bold py-3 mb-0"><?php echo htmlspecialchars($pageTitle); ?></h4>
<div class="card mb-4"><div class="card-body">
<?php if (!$row7) { ?><div class="alert alert-warning">Request not found.</div><?php } else { ?>
<form method="post">
    <div class="form-row">
        <div class="form-group col-md-6"><label>Employee</label><input class="form-control" readonly value="<?php echo htmlspecialchars($row7['Fname'].' '.$row7['Lname']); ?>"></div>
        <div class="form-group col-md-3"><label>Current Limit</label><input class="form-control" readonly value="<?php echo number_format((float)$row7['CurrentLimit'],2); ?>"></div>
        <div class="form-group col-md-3"><label>Requested Limit</label><input class="form-control" readonly value="<?php echo number_format((float)$row7['RequestedLimit'],2); ?>"></div>
        <div class="form-group col-md-4"><label>Manager Approved Amount</label><input type="text" name="MgrAmount" class="form-control" value="<?php
            $mgrAmtVal = trim((string) ($row7['MannagerAmount'] ?? ''));
            echo htmlspecialchars($mgrAmtVal !== '' ? $mgrAmtVal : $row7['RequestedLimit']);
        ?>" <?php echo $isReadOnly ? 'readonly' : ''; ?> required></div>
        <div class="form-group col-md-4"><label>Approve Date</label><input type="date" name="ApproveDate" class="form-control" value="<?php echo htmlspecialchars($row7['MannagerApproveDate'] ?: date('Y-m-d')); ?>" readonly required></div>
        <div class="form-group col-md-4"><label>Status</label>
            <select name="ManagerStatus" class="form-control" <?php echo $isReadOnly ? 'disabled' : ''; ?> required>
                <option value="1" <?php echo $managerStatus === '1' ? 'selected' : ''; ?>>Approved</option>
                <option value="0" <?php echo $managerStatus === '0' ? 'selected' : ''; ?>>Pending</option>
                <option value="2" <?php echo $managerStatus === '2' ? 'selected' : ''; ?>>Reject</option>
            </select>
            <?php if ($isReadOnly) { ?><input type="hidden" name="ManagerStatus" value="<?php echo htmlspecialchars($managerStatus); ?>"><?php } ?>
        </div>
        <div class="form-group col-md-12"><label>Reason / Narration</label><input class="form-control" readonly value="<?php echo htmlspecialchars($row7['Narration']); ?>"></div>
        <div class="form-group col-md-12"><label>Comment</label><textarea name="MannagerComment" class="form-control" <?php echo $isReadOnly ? 'readonly' : ''; ?>><?php echo htmlspecialchars($row7['MannagerComment']); ?></textarea></div>
        <div class="form-group col-md-12">
            <?php if (!$isReadOnly) { ?><button type="submit" name="submit" class="btn btn-primary">Save</button><?php } ?>
            <a href="<?php echo htmlspecialchars($listBackUrl); ?>" class="btn btn-secondary ml-2">Back to List</a>
        </div>
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
