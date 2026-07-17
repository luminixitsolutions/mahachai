<?php 
session_start();
include_once 'config.php';
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "Pretty-Cash-Manager";
$Page = "Manager-Pending-Pretty-Cash-Request";
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
$sql7 = "SELECT te.*,tu.Fname,tu.Lname,tu.Photo AS Uphoto,tu2.ShopName FROM tbl_prettycash_request te 
    LEFT JOIN tbl_users tu ON tu.id=te.UserId 
    LEFT JOIN tbl_users tu2 ON tu.UnderFrId=tu2.id WHERE te.id='$id'";
$row7 = getRecord($sql7);
$EmpId = $row7['UserId'];

 $sql99 = "SELECT COALESCE(SUM(Amount), 0) AS PendingAmt FROM tbl_expense_request WHERE UserId='$EmpId' AND ManagerStatus IN (1,0) AND AdminStatus='0'";
    $row99 = getRecord($sql99);


$sql88 = "SELECT SUM(creditAmt) As Credit,SUM(debitAmt) As Debit FROM (SELECT (case when Status='Cr' then sum(Amount) else 0 end) as creditAmt,(case when Status='Dr' then sum(Amount) else 0 end) as debitAmt FROM wallet WHERE UserId='$EmpId' GROUP BY Status) as a";
    $row88 = getRecord($sql88);
    $WalletBal = $row88['Credit'] - $row88['Debit'];


if(isset($_POST['submit'])){
    $ApproveDate = addslashes(trim($_POST["ApproveDate"]));
     $MannagerComment = addslashes(trim($_POST["MannagerComment"]));
  $MgrAmount = addslashes(trim($_POST["MgrAmount"]));
  $ManagerStatus = addslashes(trim($_POST["ManagerStatus"]));
$CreatedDate = date('Y-m-d');
    $CreatedTime = date('h:i a');
 //$TicketNo= "#".rand(1000,9999);

     $query2 = "UPDATE tbl_prettycash_request SET AdminApproveDate='$ApproveDate',AdminComment='$MannagerComment',AdminAmount='$MgrAmount',AdminStatus='$ManagerStatus',AdminBy='$user_id' WHERE id = '$id'";
  $conn->query($query2);
  require_once __DIR__ . '/includes/approval_mail_service.php';
  @approval_mail_notify($conn, 'petty_cash', (int)$id, 'admin', $ManagerStatus, (int)$user_id, $MannagerComment);

 echo "<script>alert('Approved Successfully!');window.location.href='ho-pending-pretty-cash-request.php';</script>";
    //header('Location:courses.php'); 

  }
?>

                <div class="layout-content">

                    <div class="container-fluid flex-grow-1 container-p-y">
                        <h4 class="font-weight-bold py-3 mb-0">Approve Pretty Cash</h4>

                        <div class="card mb-4">
                            <div class="card-body">
                                 <form id="validation-form" method="post" autocomplete="off">
                                <div class="row">

                                    <div class="col-lg-12">
                                <div id="alert_message"></div>
                               
                                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" id="userid">
                                    <input type="hidden" name="action" value="Save" id="action">
                                    <div class="form-row">
                                    
                                     

 <div class="form-group col-md-3">
                                            <label class="form-label">Employee Name</label>
                                            <input type="text" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7['Fname']." ".$row7['Lname']; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Outlet Name</label>
                                            <input type="text" class="form-control"
                                                placeholder="" value="<?php echo $row7['ShopName'];?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        
                                       <div class="form-group col-md-3">
                                            <label class="form-label"> Wallet Amount</label>
                                            <input type="text" class="form-control"
                                                placeholder="" value="<?php echo $WalletBal; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-3">
                                            <label class="form-label"> Pending Amount</label>
                                            <input type="text" class="form-control"
                                                placeholder="" value="<?php echo $row99['PendingAmt'];?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        
                                         <div class="form-group col-md-3">
                                            <label class="form-label"> Amount</label>
                                            <input type="text" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["Amount"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label"> Manager Amount</label>
                                            <input type="text" class="form-control"
                                                placeholder="" value="<?php echo $row7["MannagerAmount"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Approve Amount</label>
                                            <input type="text" name="MgrAmount" class="form-control"
                                                placeholder="" value="<?php echo $row7["Amount"]; ?>" required>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Request Date</label>
                                            <input type="date" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["ExpenseDate"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-12">
                                            <label class="form-label">Narration</label>
                                            <input type="text" name="TaskDate" class="form-control"
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
                                            <select class="form-control" id="ManagerStatus" name="ManagerStatus" required="">
                                                <option selected="" disabled="" value="">Select Status</option>
                                                <option value="1" <?php if($row7["AdminStatus"]=='1') {?> selected
                                                    <?php } ?>>Approved</option>
                                                <option value="0" <?php if($row7["AdminStatus"]=='0') {?> selected
                                                    <?php } ?>>Pending</option>
                                                    <option value="2" <?php if($row7["AdminStatus"]=='2') {?> selected
                                                    <?php } ?>>Reject</option>
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
 <div class="form-group col-md-12">
                                            <label class="form-label">Comment</label>
                                            <textarea name="MannagerComment" class="form-control"
                                                placeholder=""><?php echo $row7["AdminComment"]; ?></textarea>
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