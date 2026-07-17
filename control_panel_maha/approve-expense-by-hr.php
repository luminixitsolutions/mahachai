<?php 
session_start();
include_once 'config.php';
include_once 'auth.php';
require_once __DIR__ . '/includes/expense_hierarchy_approval.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "HR-Expense-Request";
$Page = "HR-Expense-Request";
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
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql7 = "SELECT te.*,tu.Fname,tu.Lname,tu.Photo AS Uphoto FROM tbl_expense_request te LEFT JOIN tbl_users tu ON tu.id=te.UserId WHERE te.id='$id'";
$row7 = getRecord($sql7);
if (!$row7 || (int)$row7['ExpCatId'] !== 3 || !expense_hierarchy_manager_gate_ok($conn, $row7)) {
    echo "<script>alert('Invalid expense or manager approval required.');window.location.href='hr-pending-expense-request.php';</script>";
    exit;
}
$hrSt = isset($row7['HrStatus']) ? (string)$row7['HrStatus'] : '';
if ($hrSt !== '' && $hrSt !== '0') {
    echo "<script>alert('This request is not pending HR approval.');window.location.href='hr-pending-expense-request.php';</script>";
    exit;
}

$sql88 = "SELECT SUM(creditAmt) As Credit,SUM(debitAmt) As Debit FROM (SELECT (case when Status='Cr' then sum(Amount) else 0 end) as creditAmt,(case when Status='Dr' then sum(Amount) else 0 end) as debitAmt FROM wallet WHERE UserId='".$row7['UserId']."' GROUP BY Status) as a";
    $row88 = getRecord($sql88);
    $Wallet = $row88['Credit'] - $row88['Debit'];


if(isset($_POST['submit'])){
    $ApproveDate = addslashes(trim($_POST["ApproveDate"]));
    $HrComment = addslashes(trim($_POST["HrComment"]));
    $HrStatus = addslashes(trim($_POST["HrStatus"]));

    if ($HrStatus == '1') {
        $bhAfterHr = expense_hierarchy_has_levels($conn, $id) ? '' : ", BhStatus='0'";
    } elseif ($HrStatus == '2') {
        $bhAfterHr = ", BhStatus=NULL";
    } else {
        $bhAfterHr = "";
    }
    $usesHierarchyHr = expense_hierarchy_has_levels($conn, $id);
    $mgrWhere = $usesHierarchyHr ? '1=1' : "ManagerStatus='1'";
    $query2 = "UPDATE tbl_expense_request SET 
        HrApproveDate='$ApproveDate',
        HrComment='$HrComment',
        HrStatus='$HrStatus',
        HrBy='$user_id'
        $bhAfterHr
        WHERE id = '$id' AND ExpCatId=3 AND $mgrWhere AND (HrStatus='0' OR HrStatus IS NULL)";
  if ($conn->query($query2) && $conn->affected_rows > 0) {
      if ($HrStatus == '1') {
          expense_hierarchy_activate_after_hr($conn, $id);
      }
            require_once __DIR__ . '/includes/approval_mail_service.php';
      @approval_mail_notify($conn, 'employee_expense', (int)$id, 'hr', $HrStatus, (int)$user_id, $HrComment);
echo "<script>alert('Saved successfully!');window.location.href='hr-pending-expense-request.php';</script>";
  } else {
      echo "<script>alert('Could not update (already processed or invalid state).');window.location.href='hr-pending-expense-request.php';</script>";
  }
  exit;
}
?>

                <div class="layout-content">

                    <div class="container-fluid flex-grow-1 container-p-y">
                        <h4 class="font-weight-bold py-3 mb-0">HR — Salary / Adhoc expense (category 3)</h4>

                        <div class="card mb-4">
                            <div class="card-body">
                                 <form id="validation-form" method="post" autocomplete="off">
                                <div class="row">

                                    <div class="col-lg-12">
                                <div id="alert_message"></div>
                               
                                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" id="userid">
                                    <input type="hidden" name="action" value="Save" id="action">
                                    <div class="form-row">
                                    
                                     

 <div class="form-group col-md-12">
                                            <label class="form-label">Employee Name</label>
                                            <input type="text" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7['Fname']." ".$row7['Lname']; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Wallet Amount</label>
                                            <input type="text" class="form-control"
                                                placeholder="" value="<?php echo $Wallet; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-3">
                                            <label class="form-label">Expense Amount</label>
                                            <input type="text" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["Amount"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Amount (manager approved)</label>
                                            <input type="text" name="HrAmountDisplay" class="form-control"
                                                placeholder="" value="<?php echo htmlspecialchars($row7["MgrAmount"] !== '' && $row7["MgrAmount"] !== null ? $row7["MgrAmount"] : $row7["Amount"]); ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Expense Date</label>
                                            <input type="date" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["ExpenseDate"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-12">
                                            <label class="form-label">Expense For</label>
                                            <input type="text" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["Narration"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-3">
                                              <label class="form-label">Expense Category</label>
                               <select class="form-control" disabled >
<option selected="" value="" disabled>Select Expense Category</option>
 

 <?php 
    $sql33 = "SELECT * FROM `tbl_expenses_category` WHERE Status=1";
    $row33 = getList($sql33);
    foreach($row33 as $result){
?>
<option value="<?php echo $result['id'];?>" <?php if($row7["ExpCatId"] == $result['id']) {?> selected <?php } ?>>
   <?php echo $result['Name'];?></option>
     <?php } ?>
</select>
                                  
                            </div>
                            
                               <div class="form-group col-md-4">
                                    <label class="form-label">Franchise</label>
                               <select class="form-control" disabled>
<option selected="" value="0" <?php if($row7["FrId"] == 0) {?> selected <?php } ?>>MAHA CHAI PVT LTD KHAMALA Branch (Main)</option>
 

 <?php 
    $sql33 = "SELECT * FROM `tbl_users_bill` WHERE Roll=5 AND ShopName!=''";
    $row33 = getList($sql33);
    foreach($row33 as $result){
?>
<option value="<?php echo $result['id'];?>" <?php if($row7["FrId"] == $result['id']) {?> selected <?php } ?>>
   <?php echo $result['ShopName']." (".$result['Phone'].")";?></option>
     <?php } ?>
</select>
                                  
                            </div>
                            
                               <div class="form-group col-md-3">
                                    <label class="form-label">Locations</label>
                               <select class="form-control" disabled>
<option selected="" value="" disabled>Select Locations</option>
 

 <?php 
    $sql33 = "SELECT * FROM `tbl_locations` WHERE Status=1";
    $row33 = getList($sql33);
    foreach($row33 as $result){
?>
<option value="<?php echo $result['id'];?>" <?php if($row7["Locations"] == $result['id']) {?> selected <?php } ?>>
   <?php echo $result['Name'];?></option>
     <?php } ?>
</select>
                                 
                            </div>

  <div class="form-group col-md-2">
       <label class="form-label">Vendor Mobile No</label>
                            <input type="number" class="form-control"  value="<?php echo $row7['VedPhone']; ?>" readonly>
                                                
                        </div>

 <div class="form-group col-md-6">
                                            <label class="form-label">Approve Date</label>
                                            <input type="date" name="ApproveDate" class="form-control"
                                                placeholder="" value="<?php echo date('Y-m-d'); ?>" required readonly>
                                            <div class="clearfix"></div>
                                        </div>

 <div class="form-group col-md-6">
                                            <label class="form-label">HR decision <span class="text-danger">*</span></label>
                                            <select class="form-control" id="HrStatus" name="HrStatus" required="">
                                                <option value="" selected disabled>Select decision</option>
                                                <option value="1">Approve (send to Account)</option>
                                                <option value="2">Reject</option>
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
 <div class="form-group col-md-12">
                                            <label class="form-label">HR comment</label>
                                            <textarea name="HrComment" class="form-control"
                                                placeholder=""><?php echo isset($row7["HrComment"]) ? htmlspecialchars($row7["HrComment"]) : ''; ?></textarea>
                                            <div class="clearfix"></div>
                                        </div>

<div class="form-group col-md-6">
                                            <label class="form-label">Receipt</label><br>
                                         <?php if($row7['Photo'] == '') {} else{?>
                        <a href="../uploads/<?php echo $row7['Photo']; ?>" target="_blank"><img src="../uploads/<?php echo $row7['Photo']; ?>"  style="height:100px;"></a><?php } ?>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-6">
                                            <label class="form-label">Payment Receipt</label><br>
                                            <?php if($row7['Photo2'] == '') {} else{?>
                        <a href="../uploads/<?php echo $row7['Photo2']; ?>" target="_blank"><img src="../uploads/<?php echo $row7['Photo2']; ?>" style="height:100px;"></a><?php } ?>
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