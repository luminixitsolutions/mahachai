<?php 
session_start();
include_once 'config.php';
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "Account-Vendor-Expense-Request";
$Page = "Account-Vendor-Peding-Expense-Request";
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
    
        
     table, td, th {  
  border: 1px solid #ddd;
  text-align: left;
}

table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  padding: 5px;
}
    </style>
     <div class="layout-wrapper layout-1 layout-without-sidenav">
        <div class="layout-inner">

             <?php include_once 'top_header.php'; include_once 'sidebar.php'; ?>


            <div class="layout-container">

                

                <?php 
$id = $_GET['id'];
$sql7 = "SELECT te.*,tu.Fname,tu.Lname,tu.Photo AS Uphoto,tu4.Fname AS BdmName,tu5.Fname AS PurchaseName,tu2.Fname AS MgrName FROM tbl_vendor_expenses te 
LEFT JOIN tbl_users tu ON tu.id=te.VedId 
LEFT JOIN tbl_users tu4 ON tu4.id=te.BdmBy 
LEFT JOIN tbl_users tu5 ON tu5.id=te.PurchaseBy 
LEFT JOIN tbl_users tu2 ON tu2.id=te.MrgBy WHERE te.id='$id'";
$row7 = getRecord($sql7);
$NewProd = $row7['NewProd'];

$sql88 = "SELECT SUM(creditAmt) As Credit,SUM(debitAmt) As Debit FROM (SELECT (case when Status='Cr' then sum(Amount) else 0 end) as creditAmt,(case when Status='Dr' then sum(Amount) else 0 end) as debitAmt FROM wallet WHERE UserId='".$row7['UserId']."' GROUP BY Status) as a";
    $row88 = getRecord($sql88);
    $Wallet = $row88['Credit'] - $row88['Debit'];


if(isset($_POST['submit'])){
    $ApproveDate = addslashes(trim($_POST["ApproveDate"]));
     $MannagerComment = addslashes(trim($_POST["MannagerComment"]));
  $AccAmount = addslashes(trim($_POST["MgrAmount"]));
  $ManagerStatus = addslashes(trim($_POST["ManagerStatus"]));
  $Locations = addslashes(trim($_POST["Locations"]));
  $InvoiceNo  = addslashes(trim($_POST["InvoiceNo"]));
  $TypeOfVendor = addslashes(trim($_POST["TypeOfVendor"]));
  $DuePaymentDate = addslashes(trim($_POST["DuePaymentDate"]));
//TypeOfVendor='$TypeOfVendor',InvoiceNo='$InvoiceNo',
 //$TicketNo= "#".rand(1000,9999);
 $AdminApproveTime = date('H:i:s');
    $query2 = "UPDATE tbl_vendor_expenses SET DuePaymentDate='$DuePaymentDate',AdminApproveTime='$AdminApproveTime',Locations='$Locations',AdminApproveDate='$ApproveDate',AdminComment='$MannagerComment',AccAmount='$AccAmount',AdminStatus='$ManagerStatus',AccBy='$user_id',Status='$ManagerStatus' WHERE id = '$id'";
  $conn->query($query2);
    require_once __DIR__ . '/includes/approval_mail_service.php';
  @approval_mail_notify($conn, 'vendor_expense', (int)$id, 'account', $ManagerStatus, (int)$user_id, $MannagerComment);
echo "<script>window.location.href='account-vendor-pending-expense-request-below.php';</script>";


    //header('Location:courses.php'); 

  }
?>

                <div class="layout-content">

                    <div class="container-fluid flex-grow-1 container-p-y">
                        <h4 class="font-weight-bold py-3 mb-0">Pending Expense</h4>

                        <div class="card mb-4">
                            <div class="card-body">
                                 <form id="validation-form" method="post" autocomplete="off">
                                <div class="row">

                                    <div class="col-lg-12">
                                <div id="alert_message"></div>
                               
                                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" id="userid">
                                    <input type="hidden" name="action" value="Save" id="action">
                                    <div class="form-row">
                                    
                                     

                                         <div class="form-group col-md-2">
                                            <label class="form-label">Expense ID</label>
                                            <input type="text" class="form-control"
                                                placeholder="" value="<?php echo $row7['id']; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-5">
                                            <label class="form-label">Trade/Business Name</label>
                                            <input type="text" name="TradeName" class="form-control"
                                                placeholder="" value="<?php echo $row7['TradeName']; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>

                                        <div class="form-group col-md-5">
                                            <label class="form-label">Vendor Name</label>
                                            <input type="text" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7['Fname']; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                       
                                        
                                         <div class="form-group col-md-3">
                                            <label class="form-label">Vendor Contact No</label>
                                            <input type="text" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7['VedPhone']; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-3">
                                            <label class="form-label">Type Of vendor</label>
                                            <select class="form-control" name="TypeOfVendor" id="TypeOfVendor" disabled>
                                                <option value="" selected>...</option>
                                                <?php 
                                                    $sql = "SELECT * FROM tbl_common_master WHERE Roll=1 AND Status=1";
                                                    $row = getList($sql);
                                                    foreach($row as $result){
                                                ?>
                                                <option value="<?php echo $result['id'];?>" <?php if($row7['TypeOfVendor'] == $result['id']){?> selected <?php } ?>><?php echo $result['Name'];?></option>
                                                <?php } ?>
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <!--<div class="form-group col-md-3">
                                            <label class="form-label">Wallet Amount</label>
                                            <input type="text" class="form-control"
                                                placeholder="" value="<?php echo $Wallet; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>-->
                                        
                                         <div class="form-group col-md-3">
                                            <label class="form-label">Invoice No</label>
                                            <input type="text"  class="form-control" name="InvoiceNo"
                                                placeholder="" value="<?php echo $row7["InvoiceNo"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Invoice Date</label>
                                            <input type="date" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["ExpenseDate"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Invoice Amount</label>
                                            <input type="text" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["Amount"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                          
                                       <!-- <div class="form-group col-md-3">
                                            <label class="form-label">Prev Balance Amount</label>
                                            <input type="text" name="PrevAmount" class="form-control"
                                                placeholder="" value="0" readonly>
                                            <div class="clearfix"></div>
                                        </div> -->
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">BDM Approve Amount</label>
                                            <input type="text" class="form-control"
                                                placeholder="" value="<?php echo $row7["BdmAmount"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Purchase dept Approve Amount</label>
                                            <input type="text" class="form-control"
                                                placeholder="" value="<?php echo $row7["PurchaseAmount"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-3">
                                            <label class="form-label">Accountant Approve Amount</label>
                                            <input type="text" class="form-control"
                                                placeholder="" value="<?php echo $row7["MgrAmount"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Approve Amount</label>
                                            <input type="text" name="MgrAmount" class="form-control"
                                                placeholder="" value="<?php echo $row7["AccAmount"]; ?>" required>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                       
                                        
                                      
                                       
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">PO No</label>
                                            <input type="text" name="PoNo" class="form-control"
                                                placeholder="" value="<?php echo $row7["PoNo"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                            
                                            <div class="form-group col-md-3">
                                            <label class="form-label">Locations</label>
                                            <select class="form-control select2-demo" name="Locations" id="Locations" required>
<option selected="" value="" disabled>Select Locations</option>
 

 <?php  
    $sql33 = "SELECT * FROM `tbl_users_bill` WHERE Roll=5 AND ShopName!='' AND Status=1  ORDER BY ShopName";
    $row33 = getList($sql33);
    foreach($row33 as $result){
?>
<option value="<?php echo $result['id'];?>" <?php if($row7["Locations"] == $result['id']) {?> selected <?php } ?>>
   <?php echo $result['ShopName']." (".$result['Phone'].")";?></option>
     <?php } ?>
</select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Payment Type</label>
                                            <select class="form-control" name="PaymentMode" id="PaymentMode" disabled>
<option selected="" value="" disabled>Select Payment Type</option>
  <option value="Advance Payment" <?php if($row7["PaymentMode"] == 'Advance Payment') {?> selected <?php } ?>>
    Advance Payment</option>
<option value="Final Payment" <?php if($row7["PaymentMode"] == 'Final Payment') {?> selected <?php } ?>>
    Final Payment</option>
</select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Advance Amount</label>
                                            <input type="text" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["AdvAmount"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Type Of Invoice</label>
                                            <select class="form-control" name="InvType" id="InvType" disabled>
<option selected="" value="" disabled>Select</option>
  <option value="Proforma Invoice" <?php if($row7["InvType"] == 'Proforma Invoice') {?> selected <?php } ?>>
    Proforma Invoice</option>
<option value="Tax Invoice" <?php if($row7["InvType"] == 'Tax Invoice') {?> selected <?php } ?>>
    Tax Invoice</option>
</select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        
                                         <div class="form-group col-md-12">
                                            <label class="form-label">Narration</label>
                                            <textarea name="TaskDate" class="form-control"
                                                placeholder=""><?php echo $row7['Narration']; ?></textarea>
                                            <div class="clearfix"></div>
                                        </div>
                                        </div>
                                        
                                       <?php 
                                       if($NewProd == 'Yes'){
                                          $sql2 = "SELECT tve.*,tcp.ProductName FROM tbl_ved_expense_items tve INNER JOIN tbl_cust_products_2025 tcp ON tcp.id=tve.ProdId WHERE tve.ExpId='".$_GET['id']."'";   
                                         }
                                         else{
                                        $sql2 = "SELECT tve.*,tcp.ProductName FROM tbl_ved_expense_items tve INNER JOIN tbl_cust_products2 tcp ON tcp.id=tve.MainProdId WHERE tve.ExpId='".$_GET['id']."'";
                                         }
                                        $rncnt2 = getRow($sql2);
                                        if($rncnt2 > 0){?>
                                        <br>
                                            <div class="form-row">
    <h5 style="font-size: 15px;color: blue;padding-left: 10px;">Products</h5>
<div style="overflow-x: auto; width: 100%;">
    <table class="table table-bordered">
  <tr>
    <th>Sr.No</th>
    <th>Product Name</th>
    <th>Qty</th>
    <th>Purchase Price</th>
    <th>Total Price</th>
   
  </tr>
   <?php 
  $i=1;
  
  $row2 = getList($sql2);
  foreach($row2 as $result){
  $total = $result['Qty2'] * $result['PurchasePrice'];
            $grandTotal += $total;
  ?>
  <tr>
    <td><?php echo $i;?></td>
   <td><?php echo $result['ProductName']; ?></td>
            <td><?php echo $result['Qty2'] . " " . $result['Unit2']; ?></td>
            <td><?php echo $result['PurchasePrice']; ?></td>
            <td><?php echo round($total); ?></td>
  
                     
  </tr>
  <?php $i++;} ?>
  <tfoot>
        <tr>
            <td colspan="4" style="text-align: right;"><strong>Grand Total:</strong></td>
            <td><strong><?php echo round($grandTotal); ?></strong></td>
        </tr>
    </tfoot>
</table>
</div>
                                            </div>
<br>
<?php } ?>

<div class="form-row">
    <h5>Approve History</h5>
    <table>
        <tr>
            <th>#</th>
            <th>Approve By</th>
            <th>Approve Date</th>
             <th>Comment</th>
        </tr>
        <tr>
            <th>BDM</th>
            <?php if($row7['Locations']=='702'){?>
            <td style="color:red;">BDM Approval Not Required</td>
            <td>NA</td>
            <td>NA</td>
            <?php } else{?>
            <td>
      <?php $BdmName = $row7['BdmName']; if($row7['BdmStatus']=='1'){ echo "<span style='color:green;'>Approved<br>By $BdmName </span>";} 
      else if($row7['BdmStatus']=='2'){ echo "<span style='color:red;'>Rejected <br> By $BdmName </span>";} 
      else { echo "<span style='color:orange;'>Pending By BDM</span>"; } ?></td>
      
 
            <td><?php echo date("d/m/Y", strtotime(str_replace('-', '/',$row7['BdmApproveDate']))); ?></td>
            <td><?php echo $row7["BdmComment"]; ?></td>
              <?php } ?>
        </tr>
        <tr>
            <th>Purchase</th>
           <td>
      <?php $PurchaseName = $row7['PurchaseName']; if($row7['PurchaseStatus']=='1'){ echo "<span style='color:green;'>Approved<br>By $PurchaseName </span>";} 
      else if($row7['PurchaseStatus']=='2'){ echo "<span style='color:red;'>Rejected <br> By $PurchaseName </span>";} 
      else { echo "<span style='color:orange;'>Pending By Purchase Dept</span>"; } ?></td>
           <td><?php echo date("d/m/Y", strtotime(str_replace('-', '/',$row7['PurchaseApproveDate']))); ?></td>
            <td><?php echo $row7["PurchaseComment"]; ?></td>
        </tr>
        
        <tr>
            <th>Accountant</th>
           <td>
      <?php $AccountName = $row7['MgrName']; if($row7['ManagerStatus']=='1'){ echo "<span style='color:green;'>Approved<br>By $AccountName </span>";} 
      else if($row7['ManagerStatus']=='2'){ echo "<span style='color:red;'>Rejected <br> By $AccountName </span>";} 
      else { echo "<span style='color:orange;'>Pending By Purchase Dept</span>"; } ?></td>
           <td><?php echo date("d/m/Y", strtotime(str_replace('-', '/',$row7['ApproveDate']))); ?></td>
            <td><?php echo $row7["MannagerComment"]; ?></td>
        </tr>
    </table>
    
    </div><br>
    
<div class="form-row">

<div class="form-group col-md-4">
                                            <label class="form-label">Estimate Payment Date <span class="text-danger">*</span></label>
                                            <input type="date" name="DuePaymentDate" class="form-control"
                                                placeholder="" value="<?php echo date('Y-m-d'); ?>" required >
                                            <div class="clearfix"></div>
                                        </div>
                                        
 <div class="form-group col-md-4">
                                            <label class="form-label">Approve Date <span class="text-danger">*</span></label>
                                            <input type="date" name="ApproveDate" class="form-control"
                                                placeholder="" value="<?php echo date('Y-m-d'); ?>" required readonly>
                                            <div class="clearfix"></div>
                                        </div>

 <div class="form-group col-md-4">
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

                            <div class="form-group col-md-6">
                                            <label class="form-label">Invoice</label><br>
                                         <?php if($row7['Photo'] == '') {} else{?>
                        <a href="../uploads/vendor_expense/<?php echo $row7['Photo']; ?>" target="_blank">View</a><?php } ?>
                                            <div class="clearfix"></div>
                                        </div>
                                      
                                        <div class="form-group col-md-6">
                                            <label class="form-label">Payment Receipt</label><br>
                                            <?php if($row7['Photo2'] == '') {} else{?>
                        <a href="../uploads/vendor_expense/<?php echo $row7['Photo2']; ?>" target="_blank">View</a><?php } ?>
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