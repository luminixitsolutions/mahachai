<?php 
session_start();
include_once 'config.php';
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "Resign-Manager-Expenses";
$Page = "Resign-Manager-Peding-Expense-Request";
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
$sql7 = "SELECT te.*,tu.Fname,tu.Lname,tu.Photo AS Uphoto FROM tbl_resign_request te LEFT JOIN tbl_users tu ON tu.id=te.UserId WHERE te.id='$id'";
$row7 = getRecord($sql7);
$EmpId = $row7['UserId'];


if(isset($_POST['submit'])){
    $ApproveDate = addslashes(trim($_POST["ApproveDate"]));
     $MannagerComment = addslashes(trim($_POST["MannagerComment"]));
  $ManagerStatus = addslashes(trim($_POST["ManagerStatus"]));
$CreatedDate = date('Y-m-d');
    $CreatedTime = date('h:i a');
 //$TicketNo= "#".rand(1000,9999);

    // Final approval: manager approval is final; when manager approves, set HrStatus=1 so no HR step needed
    $query2 = "UPDATE tbl_resign_request SET MannagerApproveDate='$ApproveDate',MannagerComment='$MannagerComment',ManagerStatus='$ManagerStatus',MrgBy='$user_id' WHERE id = '$id'";
    if ($ManagerStatus == '1') {
        $query2 = "UPDATE tbl_resign_request SET MannagerApproveDate='$ApproveDate',MannagerComment='$MannagerComment',ManagerStatus='$ManagerStatus',MrgBy='$user_id',HrStatus='1',HrApproveDate='$ApproveDate',HrBy='$user_id' WHERE id = '$id'";
        $Narration = isset($_POST["Narration"]) ? addslashes(trim($_POST["Narration"])) : '';
        $conn->query("UPDATE tbl_users SET ResignStatus='1',ResignDate='$ApproveDate',ResignComment='$Narration' WHERE id='$EmpId'");
    }
  $conn->query($query2);
  require_once __DIR__ . '/includes/approval_mail_service.php';
  @approval_mail_notify($conn, 'resign', (int)$id, 'manager', $ManagerStatus, (int)$user_id, $MannagerComment);
  echo "<script>alert('Approved Successfully!');window.location.href='manager-pending-resign-request.php';</script>";


    //header('Location:courses.php'); 

  }
?>

                <div class="layout-content">

                    <div class="container-fluid flex-grow-1 container-p-y">
                        <h4 class="font-weight-bold py-3 mb-0">Approve Resign Request</h4>

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
                                                placeholder="" value="<?php echo $row7['Fname']." ".$row7['Lname']; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                       
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Request Date</label>
                                            <input type="date" name="ReqDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["ReqDate"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label class="form-label">Notice Period</label>
                                            <input type="text" name="NoticePeriod" class="form-control"
                                                placeholder="" value="<?php echo $row7["NoticePeriod"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-12">
                                            <label class="form-label">Reason For Resign</label>
                                            <input type="text" name="Narration" class="form-control"
                                                placeholder="" value="<?php echo $row7["Narration"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>

                                        <?php
                                        $exit_json = isset($row7['exit_interview_data']) ? $row7['exit_interview_data'] : '';
                                        $exit_filled = false;
                                        $exit_data = array();
                                        if ($exit_json !== '' && $exit_json !== null) {
                                            $exit_data = @json_decode($exit_json, true);
                                            if (is_array($exit_data)) {
                                                $has_any = !empty($exit_data['reason']) || trim((string)($exit_data['helped_perform'] ?? '')) !== '' || trim((string)($exit_data['hindered_perform'] ?? '')) !== '' || trim((string)($exit_data['superior_feedback'] ?? '')) !== '' || trim((string)($exit_data['peers_coop'] ?? '')) !== '' || trim((string)($exit_data['salary_increase'] ?? '')) !== '' || trim((string)($exit_data['suggestions'] ?? '')) !== '' || trim((string)($exit_data['new_address'] ?? '')) !== '';
                                                $exit_filled = $has_any;
                                            }
                                        }
                                        ?>
                                        <div class="form-group col-md-12">
                                            <label class="form-label font-weight-bold">Exit Interview Form</label>
                                            <?php if ($exit_filled): ?>
                                            <div class="border rounded p-3 bg-light small">
                                                <p class="mb-1 font-weight-bold">1. Reason for leaving:</p>
                                                <p class="mb-2 ml-3"><?php echo is_array($exit_data['reason'] ?? null) ? implode(', ', $exit_data['reason']) : '—'; ?></p>
                                                <p class="mb-1 font-weight-bold">2. What helped you perform well:</p>
                                                <p class="mb-2 ml-3"><?php echo htmlspecialchars($exit_data['helped_perform'] ?? ''); ?></p>
                                                <p class="mb-1 font-weight-bold">3. What hindered performance:</p>
                                                <p class="mb-2 ml-3"><?php echo htmlspecialchars($exit_data['hindered_perform'] ?? ''); ?></p>
                                                <p class="mb-1 font-weight-bold">4. Feedback about superior:</p>
                                                <p class="mb-2 ml-3"><?php echo htmlspecialchars($exit_data['superior_feedback'] ?? ''); ?></p>
                                                <p class="mb-1 font-weight-bold">5. Ratings:</p>
                                                <p class="mb-2 ml-3"><strong>Peers</strong> : <?php echo htmlspecialchars($exit_data['peers_coop'] ?? '—'); ?> | <strong>Other Dept</strong>: <?php echo htmlspecialchars($exit_data['other_dept'] ?? '—'); ?> | <strong>Performance</strong>: <?php echo htmlspecialchars($exit_data['perf_system'] ?? '—'); ?> | <strong>New Ideas</strong>: <?php echo htmlspecialchars($exit_data['new_ideas'] ?? '—'); ?> | <strong>Training</strong>: <?php echo htmlspecialchars($exit_data['training'] ?? '—'); ?></p>
                                                <p class="mb-1 font-weight-bold">6. Salary increase in new job:</p>
                                                <p class="mb-2 ml-3"><?php echo htmlspecialchars($exit_data['salary_increase'] ?? ''); ?></p>
                                                <p class="mb-1 font-weight-bold">7. Suggestions:</p>
                                                <p class="mb-2 ml-3"><?php echo htmlspecialchars($exit_data['suggestions'] ?? ''); ?></p>
                                                <p class="mb-1 font-weight-bold">8. New Address:</p>
                                                <p class="mb-0 ml-3"><?php echo htmlspecialchars($exit_data['new_address'] ?? ''); ?></p>
                                            </div>
                                            <?php else: ?>
                                            <p class="text-muted mb-0">No form filled.</p>
                                            <?php endif; ?>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        

 <div class="form-group col-md-6">
                                            <label class="form-label">Approve Date</label>
                                            <input type="date" name="ApproveDate" class="form-control"
                                                placeholder=""value="<?php echo date('Y-m-d'); ?>" required readonly>
                                            <div class="clearfix"></div>
                                        </div>

 <div class="form-group col-md-6">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-control" id="ManagerStatus" name="ManagerStatus" required="">
                                                <option selected="" disabled="" value="">Select Status</option>
                                                <option value="1" <?php if($row7["ManagerStatus"]=='1') {?> selected
                                                    <?php } ?>>Approved</option>
                                                <option value="0" <?php if($row7["ManagerStatus"]=='0') {?> selected
                                                    <?php } ?>>Pending</option>
                                                    <option value="2" <?php if($row7["ManagerStatus"]=='2') {?> selected
                                                    <?php } ?>>Reject</option>
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
 <div class="form-group col-md-12">
                                            <label class="form-label">Comment</label>
                                            <textarea name="MannagerComment" class="form-control"
                                                placeholder=""><?php echo $row7["MannagerComment"]; ?></textarea>
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