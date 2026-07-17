<?php
session_start();
include_once 'config.php';
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "HR-Attendance";
$Page = "Attendance-Hr-Peding-Expense-Request";
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
                $sql7 = "SELECT te.*,tu.Fname,tu.Lname,tu.Photo AS Uphoto FROM tbl_attendance_request te LEFT JOIN tbl_users tu ON tu.id=te.UserId WHERE te.id='$id'";
                $row7 = getRecord($sql7);
                $EmpId = $row7['UserId'];
                $sql = "SELECT PerDaySalary,SalaryType,CreditSalaryStatus FROM tbl_users WHERE id='$EmpId'";
                $row = getRecord($sql);
                $PerDaySalary = $row['PerDaySalary'];
                $SalaryType = $row['SalaryType'];
                $CreditSalaryStatus = $row['CreditSalaryStatus'];

                $AttRoll = $row7['AttRoll'];
                $Latitude = $row7['Latitude'];
                $Longitude = $row7['Longitude'];
                $FromDate = $row7['FromDate'];
                $FromTime = $row7['FromTime'];
                $ToDate = $row7['ToDate'];
                $ToTime = $row7['ToTime'];
                if ($FromTime > '10:10:00') {
                    $Latemark = 1;
                } else {
                    $Latemark = 0;
                }

                if ($FromTime > '14:00:00') {
                    $HalfDay = 1;
                } else {
                    $HalfDay = 0;
                }

                if ($ToTime > '10:10:00') {
                    $Latemark2 = 1;
                } else {
                    $Latemark2 = 0;
                }

                $sql = "SELECT CreatedTime FROM tbl_attendance WHERE UserId='$EmpId' AND CreatedDate='$FromDate' AND Type=1";
                $row = getRecord($sql);
                $StartTime = $row['CreatedTime'];
                $dateTime1 = new DateTime($CreatedTime); // Current date and time
                $dateTime2 = new DateTime($StartTime); // 1 hour 45 minutes ago
                // Calculate the difference between the two dates
                $interval = $dateTime1->diff($dateTime2);
                // Format the difference
                $hours = $interval->h;
                $minutes = $interval->i;

                if ($hours <= 6) {
                    $HalfDay2 = 1;
                } else if ($hours > 6 && $hours <= 10) {
                    $HalfDay2 = 0;
                } else if ($hours > 10) {
                    $HalfDay2 = 2;
                }

                //For Evening
                if ($ToTime >= '12:00:00' && $ToTime <= '23:59:59') {
                    $date =  $FromDate;
                } else {
                    $date = date('Y-m-d', strtotime('-1 day', strtotime($ToDate)));
                }

                if ($ToTime > '10:10:00') {
                    $Latemark3 = 1;
                } else {
                    $Latemark3 = 0;
                }

                if ($ToTime > '14:00:00') {
                    $HalfDay3 = 1;
                } else {
                    $HalfDay3 = 0;
                }

                if (isset($_POST['submit'])) {
                    $ApproveDate = addslashes(trim($_POST["ApproveDate"]));
                    $HrComment = addslashes(trim($_POST["HrComment"]));
                    $HrStatus = addslashes(trim($_POST["HrStatus"]));
                    $Narration = addslashes(trim($_POST["Narration"]));
                    $CreatedDate = date('Y-m-d');
                    $CreatedTime = date('h:i a');
                    $query2 = "UPDATE tbl_attendance_request SET HrApproveDate='$ApproveDate',HrComment='$HrComment',HrStatus='$HrStatus',HrBy='$user_id' WHERE id = '$id'";
                    $conn->query($query2);
                    if ($HrStatus == 1) {
                        if ($AttRoll == 0) {
                            $sql2 = "INSERT INTO tbl_attendance SET att_regul=1,Salary='$PerDaySalary',Status='1',UserId='$EmpId',CreatedDate='$FromDate',Latitude='$Latitude',Longitude='$Longitude',Address='',
                            CreatedTime='$FromTime',Latemark='$Latemark',HalfDay='$HalfDay',Type=1,Photo='',TempPrdId=''";
                            $conn->query($sql2);

                            $sql2 = "INSERT INTO tbl_attendance SET att_regul=1,Salary='$PerDaySalary',Status='1',UserId='$EmpId',CreatedDate='$ToDate',Latitude='$Latitude',Longitude='$Longitude',Address='',
                            CreatedTime='$ToTime',Latemark='$Latemark2',HalfDay='$HalfDay2',Type=2,Photo='',TempPrdId=''";
                            $conn->query($sql2);
                        } else {
                            $sql2 = "INSERT INTO tbl_attendance SET att_regul=1,AttRoll=1,Salary='$PerDaySalary',Status='1',UserId='$EmpId',CreatedDate='$FromDate',Latitude='$Latitude',Longitude='$Longitude',
                            Address='',CreatedTime='$FromTime',Latemark='$Latemark3',HalfDay='$HalfDay3',Type=1,Photo='',TempPrdId=''";
                            $conn->query($sql2);

                            $sql2 = "INSERT INTO tbl_attendance SET att_regul=1,AttRoll=1,Salary='$PerDaySalary',Status='1',UserId='$EmpId',CreatedDate='$ToDate',Latitude='$Latitude',Longitude='$Longitude',
                            Address='',CreatedTime='$ToTime',Latemark='$Latemark3',HalfDay='$HalfDay3',Type=2,Photo='',TempPrdId=''";
                            $conn->query($sql2);
                        }
                    }

                    require_once __DIR__ . '/includes/approval_mail_service.php';
                    @approval_mail_notify($conn, 'attendance', (int)$id, 'hr', $HrStatus, (int)$user_id, $HrComment);
                    echo "<script>alert('Approved Successfully!');window.location.href='hr-pending-attendance-request.php';</script>";


                    //header('Location:courses.php'); 

                }
                ?>

                <div class="layout-content">

                    <div class="container-fluid flex-grow-1 container-p-y">
                        <h4 class="font-weight-bold py-3 mb-0">Approve Attendance Request</h4>

                        <div class="card mb-4">
                            <div class="card-body">
                                <form id="validation-form" method="post" autocomplete="off">
                                    <div class="row">

                                        <div class="col-lg-12">
                                            <div id="alert_message"></div>

                                            <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" id="userid">
                                            <input type="hidden" name="action" value="Save" id="action">
                                            <div class="form-row">



                                                <div class="form-group col-md-6">
                                                    <label class="form-label">Employee Name</label>
                                                    <input type="text" name="TaskDate" class="form-control"
                                                        placeholder="" value="<?php echo $row7['Fname'] . " " . $row7['Lname']; ?>" readonly>
                                                    <div class="clearfix"></div>
                                                </div>


                                                <div class="form-group col-md-3">
                                                    <label class="form-label">Request Date</label>
                                                    <input type="date" name="ReqDate" class="form-control"
                                                        placeholder="" value="<?php echo $row7["ReqDate"]; ?>" readonly>
                                                    <div class="clearfix"></div>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label class="form-label">Attendance Type <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="AttRoll" name="AttRoll" required="">
                                                        <option selected="" disabled="" value="">Select Status</option>
                                                        <option value="1" <?php if ($row7["AttRoll"] == '1') { ?> selected
                                                            <?php } ?>>Night</option>
                                                        <option value="0" <?php if ($row7["AttRoll"] == '0') { ?> selected
                                                            <?php } ?>>Day</option>

                                                    </select>
                                                    <div class="clearfix"></div>
                                                </div>

                                            </div>

                                            <div class="form-row">

                                                <div class="form-group col-md-3">
                                                    <label class="form-label">In Date</label>
                                                    <input type="date" name="FromDate" class="form-control"
                                                        placeholder="" value="<?php echo $row7["FromDate"]; ?>" readonly>
                                                    <div class="clearfix"></div>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label class="form-label">In Time</label>
                                                    <input type="text" name="FromTime" class="form-control"
                                                placeholder="" value="<?php echo date("h:i a", strtotime(str_replace('-', '/',$row7["FromTime"]))); ?>" readonly>
                                                    <div class="clearfix"></div>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label class="form-label">Out Date</label>
                                                    <input type="date" name="ToDate" class="form-control"
                                                        placeholder="" value="<?php echo $row7["ToDate"]; ?>" readonly>
                                                    <div class="clearfix"></div>
                                                </div>

                                                <div class="form-group col-md-3">
                                                    <label class="form-label">Out Time</label>
                                                    <input type="text" name="ToTime" class="form-control"
                                                placeholder="" value="<?php echo date("h:i a", strtotime(str_replace('-', '/',$row7["ToTime"]))); ?>" readonly>
                                                    <div class="clearfix"></div>
                                                </div>

                                                <div class="form-group col-md-12">
                                                    <label class="form-label">Reason/Comment</label>
                                                    <input type="text" name="Narration" class="form-control"
                                                        placeholder="" value="<?php echo $row7["Narration"]; ?>" readonly>
                                                    <div class="clearfix"></div>
                                                </div>



                                                <div class="form-group col-md-6">
                                                    <label class="form-label">Approve Date</label>
                                                    <input type="date" name="ApproveDate" class="form-control"
                                                        placeholder="" value="<?php echo date('Y-m-d'); ?>" required readonly>
                                                    <div class="clearfix"></div>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="HrStatus" name="HrStatus" required="">
                                                        <option selected="" disabled="" value="">Select Status</option>
                                                        <option value="1" <?php if ($row7["HrStatus"] == '1') { ?> selected
                                                            <?php } ?>>Approved</option>
                                                        <option value="0" <?php if ($row7["HrStatus"] == '0') { ?> selected
                                                            <?php } ?>>Pending</option>
                                                        <option value="2" <?php if ($row7["HrStatus"] == '2') { ?> selected
                                                            <?php } ?>>Reject</option>
                                                    </select>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label class="form-label">Comment</label>
                                                    <textarea name="HrComment" class="form-control"
                                                        placeholder=""><?php echo $row7["HrComment"]; ?></textarea>
                                                    <div class="clearfix"></div>
                                                </div>




                                            </div>




                                            <div class="form-row">
                                                <div class="form-group col-md-2">
                                                    <button type="submit" name="submit" class="btn btn-primary btn-finish" id="submit">Approve</button>
                                                </div>


                                            </div>
                                        </div>


                                        <div class="col-lg-5" id="emidetails" style="display:none;">


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