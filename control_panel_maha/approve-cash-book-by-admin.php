<?php 
session_start();
include_once 'config.php';
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "View-Cash-Book";
$Page = "View-Cash-Book";
$sessionid = session_id();
?>
<!DOCTYPE html>
<html lang="en" class="default-style">
<head>
<title><?php echo $Proj_Title; ?> - Product</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="description" content="">
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
$sql7 = "SELECT * FROM tbl_cash_book WHERE id='$id'";
$res7 = $conn->query($sql7);
$row7 = $res7->fetch_assoc();
$InvoiceDate = date('Y-m-d');
$frid = $row7['FrId'];
$sql22 = "SELECT SUM(Result) AS Result FROM (SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice WHERE FrId='$frid' AND InvoiceDate='".date('Y-m-d',strtotime("-1 days"))."' AND PayType='Cash' UNION ALL SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice_2025 WHERE FrId='$frid' AND PayType='Cash' AND InvoiceDate='".date('Y-m-d',strtotime("-1 days"))."') as a";
$row22 = getRecord($sql22);

$sql223 = "SELECT SUM(TotalCashAmt) AS TotalCashAmt FROM (SELECT SUM(NetAmount) AS TotalCashAmt FROM tbl_customer_invoice WHERE FrId='$frid'
 AND InvoiceDate<='".date('Y-m-d')."' AND PayType='Cash' 
 UNION ALL SELECT SUM(NetAmount) AS TotalCashAmt FROM tbl_customer_invoice_2025 WHERE FrId='$frid' AND InvoiceDate<='".date('Y-m-d')."'
 AND PayType='Cash') as a";
$row223 = getRecord($sql223);

 $sql222 = "SELECT SUM(TotalCashAmt) AS TotalCashAmt FROM (SELECT SUM(NetAmount) AS TotalCashAmt FROM tbl_customer_invoice WHERE FrId='$frid'
 AND InvoiceDate<='".date('Y-m-d',strtotime("-1 days"))."' AND PayType='Cash' 
 UNION ALL SELECT SUM(NetAmount) AS TotalCashAmt FROM tbl_customer_invoice_2025 WHERE FrId='$frid' AND InvoiceDate<='".date('Y-m-d',strtotime("-1 days"))."'
 AND PayType='Cash') as a";
$row222 = getRecord($sql222);

$sql221 = "SELECT SUM(Amount) AS TotalTransferAmt FROM tbl_cash_book WHERE FrId='$frid' AND ApproveStatus=1";
$row221 = getRecord($sql221);

   
    $tillyest =  $row222["TotalCashAmt"] - $row221['TotalTransferAmt'];
?>

<?php 
  if(isset($_POST['submit'])){
      $ApproveDate = addslashes(trim($_POST["ApproveDate"]));
     $ApproveComment = addslashes(trim($_POST["ApproveComment"]));
    $ApproveStatus = addslashes(trim($_POST["ApproveStatus"]));
    $query2 = "UPDATE tbl_cash_book SET ApproveDate='$ApproveDate',ApproveComment='$ApproveComment',ApproveStatus='$ApproveStatus',ApproveBy='$user_id' WHERE id = '$id'";
    $conn->query($query2);
   require_once __DIR__ . '/includes/approval_mail_service.php';
   @approval_mail_notify($conn, 'cash_book', (int)$id, 'admin', $ApproveStatus, (int)$user_id, $ApproveComment);
   echo "<script>alert('Approved Successfully!');window.location.href='pending-cash-book-request.php';</script>";  

    //header('Location:courses.php'); 

  }
 ?>

<div class="layout-content">

<div class="container-fluid flex-grow-1 container-p-y">
<h4 class="font-weight-bold py-3 mb-0">Approve Cash Book</h4>


<div class="card mb-4">
<div class="card-body">
<div id="alert_message"></div>
<div class="row">
<div class="col-lg-10">
<form id="validation-form" method="post" enctype="multipart/form-data">
<div class="form-row">

<!--<div class="form-group col-md-6">
<label class="form-label">From Date <span class="text-danger">*</span></label>
<input type="date" name="FromDate" id="FromDate" class="form-control" placeholder="" value="<?php echo $row7["FromDate"]; ?>" autocomplete="off" readonly>
<div class="clearfix"></div>
    </div>
    
    <div class="form-group col-md-6">
<label class="form-label">To Date <span class="text-danger">*</span></label>
<input type="date" name="ToDate" id="ToDate" class="form-control" placeholder="" value="<?php echo $row7["ToDate"]; ?>" autocomplete="off" readonly>
<div class="clearfix"></div>
    </div>-->
 
    <div class="form-group col-md-4">
<label class="form-label">Total Cash Amount </label>
<input type="text" name="TotalAmount" id="TotalAmount" class="form-control" placeholder="" value="<?php echo $row223["TotalCashAmt"]-$row221['TotalTransferAmt']; ?>" autocomplete="off" readonly>
<div class="clearfix"></div>
    </div>
    
    <div class="form-group col-md-4">
<label class="form-label">Total Cash Amount Till Yesterday</label>
<input type="text" class="form-control" placeholder="" value="<?php echo $tillyest; ?>" autocomplete="off" readonly>
<div class="clearfix"></div>
    </div>
    
     <div class="form-group col-md-4">
<label class="form-label">Yesterday Cash Amount </label>
<input type="text" class="form-control" placeholder="" value="<?php echo $row22["Result"]; ?>" autocomplete="off" readonly>
<div class="clearfix"></div>
    </div>
    
    <div class="form-group col-md-4">
<label class="form-label">Deposited To Be Amount <span class="text-danger">*</span></label>
<input type="text" name="Amount" id="Amount" class="form-control" placeholder="" value="<?php echo $tillyest-$row22["Result"]; ?>" autocomplete="off" readonly>
<div class="clearfix"></div>
    </div>

    <div class="form-group col-md-4">
<label class="form-label">Transfer Amount <span class="text-danger">*</span></label>
<input type="text" name="Amount" id="Amount" class="form-control" placeholder="" value="<?php echo $row7["Amount"]; ?>" autocomplete="off" readonly>
<div class="clearfix"></div>
    </div>

   <div class="form-group col-md-4">
<label class="form-label">Balance Amount <span class="text-danger">*</span></label>
<input type="text" name="BalAmt" id="BalAmt" class="form-control" placeholder="" value="<?php echo $row7["BalAmt"]; ?>" autocomplete="off" readonly>
<div class="clearfix"></div>
    </div>
    
<div class="form-group col-md-4">
<label class="form-label">Date <span class="text-danger">*</span></label>
<input type="date" name="TransferDate" id="TransferDate" class="form-control" placeholder="" value="<?php echo $row7["TransferDate"]; ?>" autocomplete="off" readonly>
<div class="clearfix"></div>
    </div>

<div class="form-group col-md-4">
<label class="form-label">Bank Name <span class="text-danger">*</span></label>
<input type="text" name="BankName" id="BankName" class="form-control" placeholder="" value="<?php echo $row7["BankName"]; ?>" autocomplete="off" readonly>
<div class="clearfix"></div>
    </div>
    
    <div class="form-group col-md-4">
<label class="form-label">Account No <span class="text-danger">*</span></label>
<input type="text" name="AccountNo" id="AccountNo" class="form-control" placeholder="" value="<?php echo $row7["AccountNo"]; ?>" autocomplete="off" readonly>
<div class="clearfix"></div>
    </div>
    
    <div class="form-group col-md-12">
        <?php if($row7['Files']=='') {} else{?><br>
<a href="../uploads/<?php echo $row7['Files'];?>">View Receipt </a>
<?php } ?>
    </div>
    
<div class="form-group col-md-12">
   <label class="form-label">Narration</label>
     <textarea name="Narration" id="Narration" class="form-control"  readonly
                                                ><?php echo $row7['Narration']; ?></textarea>
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
                                            <select class="form-control" id="ApproveStatus" name="ApproveStatus" required="">
                                                <option selected="" disabled="" value="">Select Status</option>
                                                <option value="1" <?php if($row7["ApproveStatus"]=='1') {?> selected
                                                    <?php } ?>>Approved</option>
                                                <option value="0" <?php if($row7["ApproveStatus"]=='0') {?> selected
                                                    <?php } ?>>Pending</option>
                                                    <option value="2" <?php if($row7["ApproveStatus"]=='2') {?> selected
                                                    <?php } ?>>Reject</option>
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
 <div class="form-group col-md-12">
                                            <label class="form-label">Comment</label>
                                            <textarea name="ApproveComment" class="form-control"
                                                placeholder=""><?php echo $row7["ApproveComment"]; ?></textarea>
                                            <div class="clearfix"></div>
                                        </div>
</div>
<button type="submit" name="submit" id="submit" class="btn btn-primary btn-finish">Submit</button>
<span id="error_msg" style="color:red;"></span>

</form>
</div>
<div class="col-lg-4" id="showcart">
    
        
</div>
</div>
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
 <script>
 function balAmt(){
     var TotalAmount = $('#TotalAmount').val();
      var Amount = $('#Amount').val();
      var BalAmt = Number(TotalAmount) - Number(Amount);
       $('#BalAmt').val(parseFloat(BalAmt).toFixed(2));
           
 }
  function getCashAmount(FromDate,ToDate,FrId){
     var action = "getCashAmount";
            $.ajax({
                url: "ajax_files/ajax_customer_invoice.php",
                method: "POST",
                data: {
                    action: action,
                    FromDate: FromDate,
                    ToDate:ToDate,
                    FrId:FrId
                },
                success: function(data) {
                  //alert(data);
                  console.log(data);
                  $('#TotalAmount').val(parseFloat(data).toFixed(2));
                   $('#Amount').val(parseFloat(data).toFixed(2));
                    balAmt();
                }
            });
  }
   
</script>
</body>
</html>