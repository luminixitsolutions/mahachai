<?php
session_start();
include_once 'config.php';
include_once 'auth.php';
require_once __DIR__ . '/../includes/petty_limit_helpers.php';

plt_ensure_table($conn);
plt_ensure_menu_options($conn);

$user_id = $_SESSION['Admin']['id'];
$MainPage = 'HO-Admin-Expenses';
$id = (int) ($_GET['id'] ?? 0);

$row7 = getRecord("SELECT te.*,tu.Fname,tu.Lname FROM tbl_petty_limit_request te LEFT JOIN tbl_users tu ON tu.id=te.UserId WHERE te.id='$id'");

$adminStatus = (string) ($row7['AdminStatus'] ?? '0');
$isReadOnly = in_array($adminStatus, array('1', '2'), true);
$Page = 'Ho-Pending-Petty-Limit-Request';
$listBackUrl = 'ho-pending-petty-limit-request.php';

if ($adminStatus === '1') {
    $Page = 'Ho-Approve-Petty-Limit-Request';
    $listBackUrl = 'ho-pending-petty-limit-request.php?status=approved';
}
if ($adminStatus === '2') {
    $Page = 'Ho-Reject-Petty-Limit-Request';
    $listBackUrl = 'ho-pending-petty-limit-request.php?status=rejected';
}

if (isset($_POST['submit']) && $row7 && !$isReadOnly) {
    $ApproveDate = addslashes(trim($_POST['ApproveDate']));
    $AdminComment = addslashes(trim($_POST['AdminComment']));
    $AdminAmount = addslashes(trim($_POST['AdminAmount']));
    $AdminStatus = addslashes(trim($_POST['AdminStatus']));
    $conn->query("UPDATE tbl_petty_limit_request SET AdminApproveDate='$ApproveDate',AdminComment='$AdminComment',AdminAmount='$AdminAmount',AdminStatus='$AdminStatus',AdminBy='$user_id',ModifiedDate=NOW(),ModifiedBy='$user_id' WHERE id='$id' AND AdminStatus='0'");
    require_once __DIR__ . '/includes/approval_mail_service.php';
    @approval_mail_notify($conn, 'petty_limit', (int)$id, 'admin', $AdminStatus, (int)$user_id, $AdminComment);

    $redirectUrl = 'ho-pending-petty-limit-request.php';
    if ($AdminStatus === '1') {
        $redirectUrl = 'ho-pending-petty-limit-request.php?status=approved';
    } elseif ($AdminStatus === '2') {
        $redirectUrl = 'ho-pending-petty-limit-request.php?status=rejected';
    }
    echo "<script>alert('Saved Successfully!');window.location.href='" . $redirectUrl . "';</script>";
    exit;
}

$pageTitle = $isReadOnly ? 'View Admin Petty Cash Limit Request' : 'Admin Approve Petty Cash Limit Request';
$defaultAdminAmt = trim((string) ($row7['MannagerAmount'] ?? ''));
if ($defaultAdminAmt === '') {
    $defaultAdminAmt = trim((string) ($row7['RequestedLimit'] ?? ''));
}
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
        <div class="form-group col-md-4"><label>Manager Approved</label><input class="form-control" readonly value="<?php echo htmlspecialchars($row7['MannagerAmount']); ?>"></div>
        <div class="form-group col-md-4"><label>Admin Approved Amount</label><input type="text" name="AdminAmount" class="form-control" value="<?php
            $adminAmtVal = trim((string) ($row7['AdminAmount'] ?? ''));
            echo htmlspecialchars($adminAmtVal !== '' ? $adminAmtVal : $defaultAdminAmt);
        ?>" <?php echo $isReadOnly ? 'readonly' : ''; ?> required></div>
        <div class="form-group col-md-4"><label>Approve Date</label><input type="date" name="ApproveDate" class="form-control" value="<?php echo htmlspecialchars($row7['AdminApproveDate'] ?: date('Y-m-d')); ?>" readonly required></div>
        <div class="form-group col-md-4"><label>Status</label>
            <select name="AdminStatus" class="form-control" <?php echo $isReadOnly ? 'disabled' : ''; ?> required>
                <option value="1" <?php echo $adminStatus === '1' ? 'selected' : ''; ?>>Approved</option>
                <option value="0" <?php echo $adminStatus === '0' ? 'selected' : ''; ?>>Pending</option>
                <option value="2" <?php echo $adminStatus === '2' ? 'selected' : ''; ?>>Reject</option>
            </select>
            <?php if ($isReadOnly) { ?><input type="hidden" name="AdminStatus" value="<?php echo htmlspecialchars($adminStatus); ?>"><?php } ?>
        </div>
        <div class="form-group col-md-12"><label>Reason</label><input class="form-control" readonly value="<?php echo htmlspecialchars($row7['Narration']); ?>"></div>
        <div class="form-group col-md-12"><label>Comment</label><textarea name="AdminComment" class="form-control" <?php echo $isReadOnly ? 'readonly' : ''; ?>><?php echo htmlspecialchars($row7['AdminComment']); ?></textarea></div>
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
