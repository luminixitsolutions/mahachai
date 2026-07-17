<?php 
session_start();
include_once 'config.php';
include_once 'auth.php';
require_once("dbcontroller.php");
$db_handle = new DBController();
$user_id = $_SESSION['Admin']['id'];
$MainPage = "Manager-Vendor-Expense-Request";
$Page = "Manager-Vendor-Peding-Expense-Request";
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
$sql7 = "SELECT te.*,tu.Fname,tu.Lname,tu.Photo AS Uphoto,tu4.Fname AS BdmName,tu5.Fname AS PurchaseName,tu.TrustedVendor FROM tbl_vendor_expenses te 
LEFT JOIN tbl_users tu ON tu.id=te.VedId 
LEFT JOIN tbl_users tu4 ON tu4.id=te.BdmBy 
                LEFT JOIN tbl_users tu5 ON tu5.id=te.PurchaseBy WHERE te.id='$id'";
$row7 = getRecord($sql7);
$VedId = $row7['VedId'];
$NewProd = $row7['NewProd'];

$sql88 = "SELECT SUM(creditAmt) As Credit,SUM(debitAmt) As Debit FROM (SELECT (case when Status='Cr' then sum(Amount) else 0 end) as creditAmt,(case when Status='Dr' then sum(Amount) else 0 end) as debitAmt FROM wallet WHERE UserId='".$row7['UserId']."' GROUP BY Status) as a";
    $row88 = getRecord($sql88);
    $Wallet = $row88['Credit'] - $row88['Debit'];


if(isset($_POST['submit'])){
    $ApproveDate = addslashes(trim($_POST["ApproveDate"]));
     $MannagerComment = addslashes(trim($_POST["MannagerComment"]));
  $MgrAmount = addslashes(trim($_POST["MgrAmount"]));
  $ManagerStatus = addslashes(trim($_POST["ManagerStatus"]));
  $Locations = addslashes(trim($_POST["Locations"]));
   $InvoiceNo  = addslashes(trim($_POST["InvoiceNo"]));
  $TypeOfVendor = addslashes(trim($_POST["TypeOfVendor"]));
//TypeOfVendor='$TypeOfVendor',InvoiceNo='$InvoiceNo',
 //$TicketNo= "#".rand(1000,9999);
    $query2 = "UPDATE tbl_vendor_expenses SET Locations='$Locations',ApproveDate='$ApproveDate',MannagerComment='$MannagerComment',MgrAmount='$MgrAmount',ManagerStatus='$ManagerStatus',MrgBy='$user_id' WHERE id = '$id'";
  $conn->query($query2);
  
     require_once __DIR__ . '/includes/approval_mail_service.php';
  @approval_mail_notify($conn, 'vendor_expense', (int)$id, 'manager', $ManagerStatus, (int)$user_id, $MannagerComment);
$sql = "SELECT Fname,Phone FROM tbl_users WHERE id='$VedId'";
  $row = getRecord($sql);
  $VedName = $row['Fname'];
  $VedPhone = $row['Phone'];
  if($ManagerStatus == 2){
      /*$query2 = "UPDATE tbl_vendor_expenses SET PurchaseStatus='0',BdmStatus='0',ManagerStatus='0' WHERE id = '$id'";
  $conn->query($query2);*/
//   $smstxt = "Dear ".$VedName.", We regret to inform you that, your Bills has been rejected during the final review stage. Please contact us. - Mahachai";
//   $dltentityid = "1501701120000037351";
//   $dlttempid = "1707174567055761401";
  }
  $Phone = $VedPhone;
  include '../incsmsapi.php';  

    
   $SaveInvId = $id;
     
/*  if($_GET['value'] == 'reject'){
      echo "<script>alert('Rejected Successfully!');window.location.href='manager-vendor-reject-expense-request.php';</script>";
  }
  else if($_GET['value'] == 'approve'){
      echo "<script>alert('Approved Successfully!');window.location.href='manager-vendor-approve-expense-request.php';</script>";
  }
  else{
  echo "<script>alert('Pending Successfully!');window.location.href='manager-vendor-pending-expense-request.php';</script>";
}*/

echo "<script>window.location.href='manager-vendor-pending-expense-request.php';</script>";

    //header('Location:courses.php'); 

  }
  
 unset($_SESSION["cart_item1"]);
  /* if($_GET['id']!=''){
    $ExpId = $_GET['id'];
    $sql = "SELECT * FROM tbl_ved_expense_items WHERE ExpId='$ExpId'";
    $row = getList($sql);
    foreach($row as $result){
        $ProdType = $result['ProdType'];
        $prdsrno = 1;
        if($NewProd == 'Yes'){
            $ProdId = $result['ProdId']; 
        }
        else{
        $ProdId = $result['MainProdId'];
        }
        $Unit = $result['Unit2'];
        //$Qty2 = $result['Qty2'];
        if($NewProd == 'Yes'){
          $productByCode = $db_handle->runQuery("SELECT id,code,ProductName,ProdType FROM tbl_cust_products_2025 WHERE id='$ProdId'");  
        }
        else{
         $productByCode = $db_handle->runQuery("SELECT id,code,ProductName,ProdType FROM tbl_cust_products2 WHERE id='$ProdId'");
        }
     $itemArray = array($productByCode[0]["code"]=>array('code'=>$productByCode[0]["code"],'prdsrno'=>$prdsrno,'id'=>$ProdId,'ProductName'=>$productByCode[0]["ProductName"],'PurchasePrice'=>$result["PurchasePrice"],'SellPrice'=>$result["SellPrice"],'Qty'=>$result["Qty2"],'ProdType'=>$productByCode[0]["ProdType"],'Unit'=>$Unit));
      if(!empty($_SESSION["cart_item$prdsrno"])) {
        if(in_array($productByCode[0]["code"],$_SESSION["cart_item$prdsrno"])) {
          foreach($_SESSION["cart_item$prdsrno"] as $k => $v) {
              if($productByCode[0]["code"] == $k)
                $_SESSION["cart_item$prdsrno"][$k]["quantity"] = $_POST["quantity"];
          }
        } else {
          $_SESSION["cart_item$prdsrno"] = array_merge($_SESSION["cart_item$prdsrno"],$itemArray);
        }
      } else {
        $_SESSION["cart_item$prdsrno"] = $itemArray;
      }
    }
}*/
?>

                <div class="layout-content">

                    <div class="container-fluid flex-grow-1 container-p-y">
                        <h4 class="font-weight-bold py-3 mb-0">Approve Expense</h4>

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
                                        
                                        <div class="form-group col-md-3">
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
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Approve Amount</label>
                                            <input type="text" name="MgrAmount" class="form-control"
                                                placeholder="" value="<?php  if($row7['MgrAmount']==''){ echo $row7["PurchaseAmount"];} else { echo $row7["MgrAmount"];} ?>" required>
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
    $sql33 = "SELECT * FROM `tbl_users_bill` WHERE Roll=5 AND ShopName!='' AND Status=1 ORDER BY ShopName";
    $row33 = getList($sql33);
    foreach($row33 as $result){
?>
<option value="<?php echo $result['id'];?>" <?php if($row7["Locations"] == $result['id']) {?> selected <?php } ?>>
   <?php echo $result['ShopName']." (".$result['Phone'].")";?></option>
     <?php } ?>
</select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
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
                                        
                                        <div class="form-group col-md-3">
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

<?php } ?>

<div class="form-row">

<!--<div class="form-group col-md-4">
                                            <label class="form-label">Did You Purchase Inventory Product?<span class="text-danger">*</span></label>
                                            <select class="form-control" id="Product" name="Product" required="">
                                               
                                                <option value="1" <?php if($row7["Product"]=='Yes') {?> selected
                                                    <?php } ?>>Yes</option>
                                                <option value="0" <?php if($row7["Product"]=='No') {?> selected
                                                    <?php } ?>>No</option>
                                                  
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>-->
                                        
                                       <!-- <div class="form-group col-md-3 showbutton" id="showbutton" style="display:block;    padding-top: 30px;">
                                    <button type="button" class="btn btn-info openmodal" onclick="openModal(1)">+ Add More Inventory</button>
                                </div>
                              
                                <div class="col-lg-12 showcart" id="showcart21"  style="overflow-x: auto; width: 100%;padding-top: 10px;">


                                </div>-->

<input type="hidden" id="TotalAmt1" class="form-control">
</div>

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
            <?php if($row7['TrustedVendor'] == 'Yes'){?>
            <td colspan="3" style="color:red;">Purchase Approval Not Required. (Trusted Vendor)</td>
            <?php } else{?>
           <td>
      <?php $PurchaseName = $row7['PurchaseName']; if($row7['PurchaseStatus']=='1'){ echo "<span style='color:green;'>Approved<br>By $PurchaseName </span>";} 
      else if($row7['PurchaseStatus']=='2'){ echo "<span style='color:red;'>Rejected <br> By $PurchaseName </span>";} 
      else { echo "<span style='color:orange;'>Pending By Purchase Dept</span>"; } ?></td>
           <td><?php echo date("d/m/Y", strtotime(str_replace('-', '/',$row7['PurchaseApproveDate']))); ?></td>
            <td><?php echo $row7["PurchaseComment"]; ?></td>
            <?php } ?>
        </tr>
    </table>
    
    </div><br>
<div class="form-row">

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

                            <div class="form-group col-md-6">
                                            <label class="form-label">Invoice</label><br>
                                         <?php if($row7['Photo'] == '') {} else{?>
                        <a href="../uploads/vendor_expense/<?php echo $row7['Photo']; ?>" target="_blank"><img src="../uploads/vendor_expense/<?php echo $row7['Photo']; ?>"  style="height:100px;"></a><?php } ?>
                                            <div class="clearfix"></div>
                                        </div>
                                      
                                        <div class="form-group col-md-6">
                                            <label class="form-label">Payment Receipt</label><br>
                                            <?php if($row7['Photo2'] == '') {} else{?>
                        <a href="../uploads/vendor_expense/<?php echo $row7['Photo2']; ?>" target="_blank">View Pdf</a><?php } ?>
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

<div id="myModal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">

                            <h4 class="modal-title">Add Product</h4>
                        </div>
                        <div class="modal-body">

                            <div class="card">
                                <div class="row">
    <!-- Product Name -->
    <div class="form-group col-md-9 col-12">
        <label class="form-label">Product Name</label>
        <input type="text" class="form-control" name="ProductName" id="ProductName" placeholder=""
            value="" autocomplete="off" onclick="this.select();">
        <div id="autocomplete-list3" class="autocomplete-list" style="display: none; position: absolute;"></div>
        <div class="clearfix"></div>
    </div>

    <!-- Product ID -->
    <div class="form-group col-md-3 col-12">
        <label class="form-label">Product ID</label>
        <input type="text" class="form-control" name="ProdId" id="ProdId" value="" readonly>
        <div class="clearfix"></div>
    </div>

    <!-- Stock In Qty and Unit -->
    <div class="form-group col-md-4 col-12">
        <label class="form-label">Stock In Qty</label>
        <div class="input-group">
            <input type="text" name="Qty" id="Qty" class="form-control" placeholder=""
                value="<?php echo $row7["Qty"]; ?>" autocomplete="off">
            <input type="text" name="Unit" id="Unit" class="form-control" placeholder=""
                value="<?php echo $row7["Unit"]; ?>" autocomplete="off" style="max-width: 60px;" readonly>
        </div>
        <div class="clearfix"></div>
    </div>

    <!-- Purchase Price -->
    <div class="form-group col-md-4 col-12">
        <label class="form-label">Purchase Price (Per Qty)</label>
        <input type="text" name="PurchasePrice" id="PurchasePrice" class="form-control" placeholder=""
            value="" autocomplete="off" required>
        <div class="clearfix"></div>
    </div>

    <!-- Add Button -->
    <div class="form-group col-md-3 col-12">
        <label class="form-label">&nbsp;</label>
        <button type="button" id="add" class="btn btn-success btn-finish w-100" onclick="addToCart()">Add</button>
        <div class="clearfix"></div>
    </div>

    <!-- Hidden Fields -->
    <input type="hidden" name="AvailStock" id="AvailStock" value="" readonly>
    <input type="hidden" name="SellPrice" id="SellPrice" value="<?php echo $row7["SellPrice"]; ?>" readonly>
    <input type="hidden" name="code" id="code" value="" readonly>
    <input type="hidden" name="prdsrno" id="prdsrno" value="1" readonly>
     <input type="hidden" name="NewProd" id="NewProd" value="<?php echo $NewProd;?>" readonly>
</div>


                                <div class="form-row">

                                    <div class="col-lg-12 showcart" id="showcart" style="overflow-x: auto; width: 100%;">


                                    </div>

                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>

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
      function edit_prod(code,prdsrno) {
    // Assuming you have access to session data via AJAX (recommended)
    $.ajax({
        url: 'ajax_files/ajax_exp_mult_products.php',
        method: 'POST',
        data: { action: 'getProduct', code: code,prdsrno:prdsrno },
        success: function(response) {
            console.log(response);
            var data = JSON.parse(response);
            $('#ProductName').val(data.ProductName);
            $('#ProdId').val(data.id);
            $('#Qty').val(data.Qty);
            $('#PurchasePrice').val(data.PurchasePrice);
            $('#Unit').val(data.Unit);
            $('#SellPrice').val(data.SellPrice);
            $('#code').val(data.code); // Important to track the product

            $('#myModal').modal('show');
        }
    });
}
      function openModal(srno) {
            $('#myModal').modal('show');
            $('#prdsrno').val(srno);
            displayCart();
        }
        
        let currentFocus3 = -1;

        $(document).ready(function() {
            displayCart();
            $("#ProductName").on("input", function() {
                let ProductName = $(this).val();
                if (ProductName.length === 0) {
                    $("#autocomplete-list3").hide();
                    return;
                }
                var action = "getProductList";
                var FrId = $('#Locations').val();
                var NewProd = $('#NewProd').val();
                //alert(FrId);
                $.ajax({
                    url: "ajax_files/ajax_dropdown.php",
                    method: "POST",
                    data: {
                        action: action,
                        ProductName: ProductName,
                        FrId: FrId,
                        NewProd:NewProd
                    },
                    success: function(data) {
                        console.log(data);
                        $("#autocomplete-list3").empty().show();
                        currentFocus3 = -1;

                        if (data.length === 0) {
                            $("#autocomplete-list3").hide();
                            return;
                        }

                        data.forEach(function(item) {
                            $("#autocomplete-list3").append(`<div class="autocomplete-item" onclick="getAvailProdStock(${item.id})">${item.ProductName}</div>`);
                        });

                        $(".autocomplete-item").on("click", function() {
                            $("#ProductName").val($(this).text());
                            $("#autocomplete-list3").hide();
                        });
                    }
                });
            });

            $("#ProductName").on("keydown", function(e) {
                let items = $(".autocomplete-item");

                if (e.key === "ArrowDown") {
                    currentFocus3++;
                    if (currentFocus3 >= items.length) currentFocus3 = 0;
                    setActive(items);
                    e.preventDefault();
                } else if (e.key === "ArrowUp") {
                    currentFocus3--;
                    if (currentFocus3 < 0) currentFocus3 = items.length - 1;
                    setActive(items);
                    e.preventDefault();
                } else if (e.key === "Enter") {
                    e.preventDefault();
                    if (currentFocus3 > -1 && items[currentFocus3]) {
                        items.eq(currentFocus3).click();
                    }
                }
            });

            function setActive(items) {
                items.removeClass("active");
                if (currentFocus3 >= 0 && currentFocus3 < items.length) {
                    items.eq(currentFocus3).addClass("active");
                    items.eq(currentFocus3)[0].scrollIntoView({
                        block: "nearest"
                    });
                }
            }

            $(document).click(function(e) {
                if (!$(e.target).closest("#ProductName, #autocomplete-list3").length) {
                    $("#autocomplete-list3").hide();
                }
            });
        });

        function addToCart() {
            var action = "addToCart";
            var code = $('#code').val();
            var ProdId = $('#ProdId').val();
            var quantity = $('#Qty').val();
            var Unit = $('#Unit').val();
            var PurchasePrice = $('#PurchasePrice').val();
            var SellPrice = $('#SellPrice').val();
            var prdsrno = $('#prdsrno').val();
            var NewProd = $('#NewProd').val();
            if (ProdId == '') {
                alert("Please Select Product");
            } else if (quantity == '') {
                alert("Please Enter Qty");
            } else {
                $.ajax({
                    url: "ajax_files/ajax_exp_mult_products.php",
                    method: "POST",
                    data: {
                        action: action,
                        code: code,
                        quantity: quantity,
                        id: ProdId,
                        PurchasePrice: PurchasePrice,
                        SellPrice: SellPrice,
                        prdsrno: prdsrno,
                        Unit:Unit,
                        NewProd:NewProd
                    },
                    beforeSend: function() {
                        $('#add').attr('disabled', 'disabled');
                        $('#add').text('Please Wait...');
                    },
                    success: function(data) {
                        //alert(data);
                        console.log(data);
                        displayCart();
                        $('#add').attr('disabled', false);
                        $('#add').text('Add');
                        $('#code').val('');
                        $('#ProductName').val('');
                        $('#ProdId').val(0).attr("selected", true);
                        $('#Qty').val('');
                        $('#Unit').val('');
                        $('#PurchasePrice').val('');
                        $('#SellPrice').val('');
                        $('#AvailStock').val('');
                        
                    }
                });
            }
        }

        function displayCart() {
            var action = "displayCart";
            var prdsrno = $('#prdsrno').val();
            $.ajax({
                url: "ajax_files/ajax_exp_mult_products.php",
                type: "POST",
                data: {
                    action: action,
                    prdsrno: prdsrno
                },
                success: function(data) {
                    console.log(data);
                    $('#showcart').html(data);
                    $('#showcart2' + prdsrno).html(data);
                    calTotalQty();
                    calTotal_Amt();
                    calTotAmt();
                },

            });
        }

        function calTotalQty() {
             var prdsrno = $('#prdsrno').val();
            var action = "calTotalQty";
            $.ajax({
                url: "ajax_files/ajax_exp_mult_products.php",
                type: "POST",
                data: {
                    action: action,
                    prdsrno:prdsrno
                },
                success: function(data) {
                    console.log(data);
                    if(data <= 0){
                        $('#submit').prop('disabled', false);
                    }
                    else{
                        $('#submit').prop('disabled', false);
                    }
                    //$('#TotQty').val(data);
                },

            });
        }
        
        function calTotal_Amt() {
             var prdsrno = $('#prdsrno').val();
            var action = "calTotalAmt";
            $.ajax({
                url: "ajax_files/ajax_exp_mult_products.php",
                type: "POST",
                data: {
                    action: action,
                    prdsrno:prdsrno
                },
                success: function(data) {
                   //alert(data);
                   $('#TotalAmt'+ prdsrno).val(data);
                },

            });
        }
        
        

        function delete_prod(code) {
            var prdsrno = $('#prdsrno').val();
            if (confirm("Are you sure you want to delete Record?")) {
                var action = "delete_cart_prod";
                $.ajax({
                    url: "ajax_files/ajax_exp_mult_products.php",
                    type: "POST",
                    data: {
                        action: action,
                        code: code,
                        prdsrno: prdsrno
                    },
                    success: function(data) {
                        console.log(data);
                        displayCart();

                    },

                });
            }
        }

        function getProdDetails(id) {
            var action = "getProdDetails";
            var NewProd = $('#NewProd').val();
            $.ajax({
                url: "ajax_files/ajax_exp_mult_products.php",
                method: "POST",
                data: {
                    action: action,
                    id: id,
                    NewProd:NewProd
                },
                success: function(data) {
                    //alert(data);
                    console.log(data);
                    var res = JSON.parse(data);
                    $('#PurchasePrice').val(res.PurchasePrice);
                    $('#SellPrice').val(res.MinPrice);
                    $('#code').val(res.code);
                    $('#Unit').val(res.Unit);

                }
            });
        }

        function getAvailProdStock(id) {
            $('#ProdId').val(id);
            getProdDetails(id);
            var action = "getAvailProdStock";
            $.ajax({
                url: "ajax_files/ajax_exp_mult_products.php",
                method: "POST",
                data: {
                    action: action,
                    id: id
                },
                success: function(data) {
                    //alert(data);
                    console.log(data);
                    $('#AvailStock').val(data);

                }
            });
        }
 </script>
</body>

</html>