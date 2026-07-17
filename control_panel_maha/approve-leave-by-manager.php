<?php 
session_start();
include_once 'config.php';
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "Leave-Manager-Expenses";
$Page = "Leave-Manager-Peding-Expense-Request";
?>
<!DOCTYPE html>
<html lang="en" class="default-style">

<head>
    <title><?php echo $Proj_Title; ?> - <?php if($_GET['id']) {?>Edit <?php } else{?> Add <?php } ?> Raw Stock
    </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="" />

    <?php include_once 'header_script.php'; ?>
    <script src="ckeditor/ckeditor.js"></script>
</head>

<body>
    <style type="text/css">
    .password-tog-info {
        display: inline-block;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        position: absolute;
        right: 50px;
        top: 30px;
        text-transform: uppercase;
        z-index: 2;
    }
    </style>
     <div class="layout-wrapper layout-1 layout-without-sidenav">
        <div class="layout-inner">

             <?php include_once 'top_header.php'; include_once 'sidebar.php'; ?>


            <div class="layout-container">

                

                <?php 
$id = $_GET['id'];
$sql7 = "SELECT te.*,tu.Fname,tu.Lname,tu.Photo AS Uphoto FROM tbl_leave_request te LEFT JOIN tbl_users tu ON tu.id=te.UserId WHERE te.id='$id'";
$row7 = getRecord($sql7);
$EmpId = $row7['UserId'];
$FromDate= $row7['FromDate'];
$ToDate= $row7['ToDate'];

$sql78 = "
SELECT SUM(TotDays) AS LeavDay
FROM tbl_leave_request
WHERE UserId = '$EmpId'
AND FromDate <= '$ToDate'
AND ToDate >= '$FromDate'
AND ManagerStatus IN (1,3)
";

$row78 = getRecord($sql78);


$sql71 = "SELECT * FROM tbl_users2 WHERE UserId='$EmpId'";
$row71 = getRecord($sql71);
$balleave = ($row7['LeaveType'] == 'PL') ? $row71['El'] : $row71['Cl'];
$usedLeave = $row78['LeavDay'] ? $row78['LeavDay'] : 0;
$TotLeave = $balleave - $usedLeave;

$sql = "SELECT tu.EmailId AS ManagerEmailId,tu.Fname AS ManagerName FROM tbl_users tu INNER JOIN tbl_users tu2 ON tu2.id=tu.UnderUser WHERE tu.id='$EmpId'";
$row = getRecord($sql);
$employeeName = $row['ManagerName'];
$ManagerEmailId = $row['ManagerEmailId'];
$ManagerName = $row['ManagerName'];

if(isset($_POST['submit'])){
    $ApproveDate = date('Y-m-d');
    $MannagerComment = addslashes(trim($_POST["MannagerComment"]));
    $ManagerStatusInput = $_POST["ManagerStatus"];

    $appliedDays = $row7['TotDays'];
    $availableLeave = $TotLeave;

    $approvedDays = 0;
    $pendingDays = 0;
    $finalStatus = $ManagerStatusInput;

    if($ManagerStatusInput == 1){ // If manager selects Approve
        if($appliedDays > $availableLeave){
            $approvedDays = floor($availableLeave);     // 2
            $pendingDays  = $availableLeave - $approvedDays; // 0.5
            $finalStatus  = 3; // Partially Approved
        } else {
            $approvedDays = $appliedDays;
            $pendingDays  = 0;
            $finalStatus  = 1; // Fully Approved
        }
    }

    if($ManagerStatusInput == 2){ // Rejected
        $approvedDays = 0;
        $pendingDays = 0;
        $finalStatus = 2;
    }

    $query2 = "UPDATE tbl_leave_request SET 
        MannagerApproveDate='$ApproveDate',
        MannagerComment='$MannagerComment',
        ManagerStatus='$finalStatus',
        ApprovedDays='$approvedDays',
        PendingDays='$pendingDays',
        MrgBy='$user_id'
        WHERE id = '$id'";
    $conn->query($query2);
        require_once __DIR__ . '/includes/approval_mail_service.php';
        @approval_mail_notify($conn, 'leave', (int)$id, 'manager', $finalStatus, (int)$user_id, $MannagerComment);
echo "<script>alert('Approved Successfully!');window.location.href='manager-pending-leave-request.php';</script>";


    //header('Location:courses.php'); 

  }
?>

<style>
    .card {
    border-radius: 10px;
}
.card-header {
    font-weight: 600;
}

</style>
                <div class="layout-content">

                    <div class="container-fluid flex-grow-1 container-p-y">
                        <h4 class="font-weight-bold py-3 mb-0">Approve Leave Request</h4>

                        <div class="card mb-4">
                            <div class="card-body">
                                <form id="validation-form" method="post" autocomplete="off">
<div class="row">

<div class="col-lg-12">
<div id="alert_message"></div>

<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
<input type="hidden" name="action" value="Save">

<!-- ================= EMPLOYEE INFO ================= -->
<div class="row mb-3">
    <div class="col-md-12">
        <label class="form-label font-weight-bold">Employee Name</label>
        <input type="text" class="form-control"
        value="<?php echo $row7['Fname'].' '.$row7['Lname']; ?>" readonly>
    </div>
</div>

<hr>

<!-- ================= LEAVE DETAILS ================= -->
<div class="row mb-3">

    <div class="col-md-3">
        <label class="form-label">Request Date</label>
        <input type="date" class="form-control" value="<?php echo $row7['ReqDate']; ?>" readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label">Leave Type</label>
        <input type="text" class="form-control" value="<?php echo $row7['LeaveType']; ?>" readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label">Leave Duration</label>
        <input type="text" class="form-control"
        value="<?php echo ($row7['TotDays'] == 0.5 ? 'Half Day' : 'Full Day'); ?>" readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label">Total Days</label>
        <input type="text" class="form-control" value="<?php echo $row7['TotDays']; ?>" readonly>
    </div>

</div>

<?php if($row7['TotDays'] == 0.5){ ?>
<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Half Day Session</label>
        <input type="text" class="form-control"
        value="<?php echo ($row7['HalfType']=='FIRST') ? '1st Half (10:00 – 2:30)' : '2nd Half (2:30 – 7:00)'; ?>" readonly>
    </div>
</div>
<?php } ?>

<div class="row mb-3">

    <div class="col-md-3">
        <label class="form-label">From Date</label>
        <input type="date" class="form-control" value="<?php echo $row7['FromDate']; ?>" readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label">To Date</label>
        <input type="date" class="form-control" value="<?php echo $row7['ToDate']; ?>" readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label">Available Leave (At Request)</label>
        <input type="text" class="form-control" value="<?php echo $TotLeave; ?>" readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label">Current Status</label>
        <input type="text" class="form-control"
        value="<?php
            if($row7['ManagerStatus']==1) echo 'Approved';
            elseif($row7['ManagerStatus']==2) echo 'Rejected';
            else echo 'Pending';
        ?>" readonly>
    </div>

</div>

<div class="row mb-4">
    <div class="col-md-12">
        <label class="form-label">Leave Reason</label>
        <textarea class="form-control" rows="2" readonly><?php echo $row7['Narration']; ?></textarea>
    </div>
</div>

<hr>

<!-- ================= APPROVAL SECTION ================= -->
<div class="row mb-3">

    <div class="col-md-6">
        <label class="form-label">Approve Date</label>
        <input type="date" name="ApproveDate" class="form-control"
        value="<?php echo date('Y-m-d'); ?>" readonly>
    </div>

    <div class="col-md-6">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-control" name="ManagerStatus" required>
            <option value="">Select Status</option>
            <option value="1" <?php if($row7['ManagerStatus']==1) echo 'selected'; ?>>Approve</option>
            <option value="2" <?php if($row7['ManagerStatus']==2) echo 'selected'; ?>>Reject</option>
            <option value="0" <?php if($row7['ManagerStatus']==0) echo 'selected'; ?>>Pending</option>
        </select>
    </div>

</div>

<div class="row mb-4">
    <div class="col-md-12">
        <label class="form-label">Manager Comment</label>
        <textarea name="MannagerComment" class="form-control" rows="2"><?php echo $row7['MannagerComment']; ?></textarea>
    </div>
</div>

<!-- ================= ACTION ================= -->
<div class="row">
    <div class="col-md-12 text-right">
        <button type="submit" class="btn btn-success" id="submit" name="submit">
            <i class="fa fa-check"></i> Submit Decision
        </button>
    </div>
</div>

</div>
</div>
</form>







                            </div>
                        </div>



</div>


                   


                    <?php include_once 'footer.php'; ?>
                </div>

            </div>

        </div>

        <div class="layout-overlay layout-sidenav-toggle"></div>
    </div>


    <?php include_once 'footer_script.php'; ?>

 
</body>

</html>