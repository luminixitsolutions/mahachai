<?php
session_start();
include_once 'config.php';
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "HR-Leave";
$Page = "Leave-Hr-Peding-Expense-Request";
?>
<!DOCTYPE html>
<html lang="en" class="default-style">

<head>
    <title><?php echo $Proj_Title; ?> - <?php if ($_GET['id']) { ?>Edit <?php } else { ?> Add <?php } ?> Raw Stock
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
                $sql = "SELECT tu.PerDaySalary,tu.SalaryType,tu.CreditSalaryStatus,tc.Name AS CityName FROM tbl_users tu LEFT JOIN tbl_city tc ON tc.id=tu.CityId WHERE tu.id='$EmpId'";
                $row = getRecord($sql);
                $PerDaySalary = $row['PerDaySalary'];
                $SalaryType = $row['SalaryType'];
                $CreditSalaryStatus = $row['CreditSalaryStatus'];
                $CityName = $row['CityName'];

                $AttRoll = $row7['AttRoll'];
                $Latitude = $row7['Latitude'];
                $Longitude = $row7['Longitude'];
                $FromDate = $row7['FromDate'];
                $FromTime = $row7['FromTime'];
                $ToDate = $row7['ToDate'];
                $ToTime = $row7['ToTime'];
                

                if (isset($_POST['submit'])) {
                    $ApproveDate = addslashes(trim($_POST["ApproveDate"]));
                    $HrComment = addslashes(trim($_POST["HrComment"]));
                    $HrStatus = addslashes(trim($_POST["HrStatus"]));
                    $Narration = addslashes(trim($_POST["Narration"]));
                    $CreatedDate = date('Y-m-d');
                    $CreatedTime = date('h:i a');
                    $query2 = "UPDATE tbl_leave_request SET HrApproveDate='$ApproveDate',HrComment='$HrComment',HrStatus='$HrStatus',HrBy='$user_id',ManagerStatus=1,behalfofmanager=1 WHERE id = '$id'";
                    $conn->query($query2);
                    require_once __DIR__ . '/includes/approval_mail_service.php';
                    @approval_mail_notify($conn, 'leave', (int)$id, 'hr', $HrStatus, (int)$user_id, $HrComment);

                    echo "<script>alert('Approved Successfully!');window.location.href='hr-pending-leave-request.php';</script>";


                    //header('Location:courses.php'); 

                }
                ?>

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

    <!--<div class="col-md-4">
        <label class="form-label font-weight-bold">Location</label>
        <input type="text" class="form-control"
        value="<?php echo $CityName; ?>" readonly>
    </div>-->
</div>

<hr>

<!-- ================= LEAVE DETAILS ================= -->
<div class="row mb-3">

    <div class="col-md-3">
        <label class="form-label">Request Date</label>
        <input type="date" class="form-control"
        value="<?php echo $row7['ReqDate']; ?>" readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label">Leave Type</label>
        <input type="text" class="form-control"
        value="<?php echo $row7['LeaveType']; ?>" readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label">Leave Duration</label>
        <input type="text" class="form-control"
        value="<?php echo ($row7['TotDays'] == 0.5 ? 'Half Day (4.5 Hrs)' : 'Full Day'); ?>" readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label">Total Days</label>
        <input type="text" class="form-control"
        value="<?php echo $row7['TotDays']; ?>" readonly>
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
        <input type="date" class="form-control"
        value="<?php echo $row7['FromDate']; ?>" readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label">To Date</label>
        <input type="date" class="form-control"
        value="<?php echo $row7['ToDate']; ?>" readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label">Available Leave (At Request)</label>
        <input type="text" class="form-control"
        value="<?php echo $row7['AvailLeave']; ?>" readonly>
    </div>

    <div class="col-md-3">
        <label class="form-label">Manager Status</label>
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

<!-- ================= HR APPROVAL ================= -->
<div class="row mb-3">

    <div class="col-md-6">
        <label class="form-label">Approve Date</label>
        <input type="date" name="ApproveDate" class="form-control"
        value="<?php echo date('Y-m-d'); ?>" readonly>
    </div>

    <div class="col-md-6">
        <label class="form-label">HR Status <span class="text-danger">*</span></label>
        <select class="form-control" name="HrStatus" required>
            <option value="">Select Status</option>
            <option value="1" <?php if($row7['HrStatus']==1) echo 'selected'; ?>>Approve</option>
            <option value="2" <?php if($row7['HrStatus']==2) echo 'selected'; ?>>Reject</option>
            <option value="0" <?php if($row7['HrStatus']==0) echo 'selected'; ?>>Pending</option>
        </select>
    </div>

</div>

<div class="row mb-4">
    <div class="col-md-12">
        <label class="form-label">HR Comment</label>
        <textarea name="HrComment" class="form-control" rows="2"><?php echo $row7['HrComment']; ?></textarea>
    </div>
</div>

<!-- ================= ACTION ================= -->
<div class="row">
    <div class="col-md-12 text-right">
        <button type="submit" name="submit" class="btn btn-primary" id="submit">
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