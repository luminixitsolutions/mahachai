<?php 
session_start();
include_once 'config.php';
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "Manager-Expense-Request";
$Page = "Manager-Expense-Request";
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
 <?php require_once __DIR__ . '/includes/expense_hierarchy_approval.php'; ?>


            <div class="layout-container">

                

                <?php 
$id = $_GET['id'];
$sql7 = "SELECT te.*,tu.Fname,tu.Lname,tu.Photo AS Uphoto,tu.UnderByUser FROM tbl_expense_request te LEFT JOIN tbl_users tu ON tu.id=te.UserId WHERE te.id='$id'";
$row7 = getRecord($sql7);
$EmpId = $row7['UserId'];
$FrId = $row7['FrId'];
$usesHierarchy = expense_hierarchy_has_levels($conn, $id);
$canApproveHierarchy = $usesHierarchy && expense_hierarchy_user_can_approve($conn, $id, $user_id);
$canApproveLegacy = !$usesHierarchy && (int) ($row7['UnderByUser'] ?? 0) === (int) $user_id && (string) ($row7['ManagerStatus'] ?? '') === '0';
if ($usesHierarchy && !$canApproveHierarchy) {
    echo "<script>alert('You are not the current approver for this expense.');history.back();</script>";
    exit;
}
if (!$usesHierarchy && !$canApproveLegacy && (string) ($row7['ManagerStatus'] ?? '') === '0') {
    echo "<script>alert('You are not authorized to approve this expense.');history.back();</script>";
    exit;
}
 
$sql88 = "SELECT SUM(creditAmt) As Credit,SUM(debitAmt) As Debit FROM (SELECT (case when Status='Cr' then sum(Amount) else 0 end) as creditAmt,(case when Status='Dr' then sum(Amount) else 0 end) as debitAmt FROM wallet WHERE UserId='".$row7['UserId']."' GROUP BY Status) as a";
    $row88 = getRecord($sql88);
    $Wallet = $row88['Credit'] - $row88['Debit'];


if (isset($_POST['submit'])) {

    $ApproveDate       = addslashes(trim($_POST["ApproveDate"]));
    $MannagerComment   = addslashes(trim($_POST["MannagerComment"]));
    $MgrAmount         = addslashes(trim($_POST["MgrAmount"]));
    $ManagerStatus     = addslashes(trim($_POST["ManagerStatus"]));
    $CreatedDate       = date('Y-m-d');
    $CreatedTime       = date('h:i a');

    // Start Transaction
    $conn->begin_transaction();

    try {
        $expCatId = isset($row7['ExpCatId']) ? (int)$row7['ExpCatId'] : 0;
        $pageHo = isset($_GET['page']) && $_GET['page'] === 'ho';

        if ($usesHierarchy) {
            $hResult = expense_hierarchy_process_approval(
                $conn,
                $id,
                $user_id,
                $ManagerStatus,
                $MannagerComment,
                $ApproveDate,
                $MgrAmount
            );
            if (!$hResult['ok']) {
                throw new Exception($hResult['message'] ?? 'Hierarchy approval failed');
            }
        } elseif(!$pageHo && $user_id == 7873 && $MgrAmount<=2000 && $expCatId !== 3){
            
            // --- UPDATE MAIN EXPENSE REQUEST --- //
        $sql_main = "UPDATE tbl_expense_request 
                        SET ApproveDate='$ApproveDate',
                            MannagerComment='$MannagerComment',
                            MgrAmount='$MgrAmount',
                            ManagerStatus='$ManagerStatus',
                            MrgBy='$user_id',AdminApproveDate='$ApproveDate',
                     AdminComment='$MannagerComment',
                     AccAmount='$MgrAmount',
                     AdminStatus='$ManagerStatus',
                     AccBy='$user_id'
                     WHERE id='$id'";
                     if (!$conn->query($sql_main)) {
            throw new Exception("Failed to update main expense record");
        }

                     
                     if ($ManagerStatus == 1) {
        $conn->query("DELETE FROM wallet WHERE UserId='$EmpId' AND ExpId='$id'");
        $Narration = "Amount Deduct against Expense For ".addslashes(trim($row7["Narration"]));
        $conn->query("INSERT INTO wallet SET 
                      UserId='$EmpId',
                      Amount='$MgrAmount',
                      Narration='$Narration',
                      Status='Dr',
                      CreatedDate='$CreatedDate',
                      CreatedTime='$CreatedTime',
                      ExpId='$id'");
    }
        }
        else{
        // --- UPDATE MAIN EXPENSE REQUEST --- //
        $hrClause = '';
        if ($expCatId === 3) {
            if ($ManagerStatus == '1') {
                $hrClause = ", HrStatus='0'";
            } elseif ($ManagerStatus == '2') {
                $hrClause = ", HrStatus=NULL";
            }
        }
        $bhClause = '';
        if ($pageHo) {
            if ($ManagerStatus == '1') {
                if ($expCatId !== 3) {
                    $bhClause = ", BhStatus='0'";
                }
            } elseif ($ManagerStatus == '2') {
                $bhClause = ", BhStatus=NULL";
            }
        }
        $sql_main = "UPDATE tbl_expense_request 
                        SET ApproveDate='$ApproveDate',
                            MannagerComment='$MannagerComment',
                            MgrAmount='$MgrAmount',
                            ManagerStatus='$ManagerStatus',
                            MrgBy='$user_id'
                            $hrClause
                            $bhClause
                     WHERE id='$id'";
                     
                     if (!$conn->query($sql_main)) {
            throw new Exception("Failed to update main expense record");
        }

        }
        


       // --- UPDATE SELECTED EXPENSE ITEMS --- //
$selectedIds = isset($_POST['approve_ids']) ? $_POST['approve_ids'] : [];

if (!empty($selectedIds)) {

    // Convert array to comma separated for SQL
    $selectedIdsStr = implode(",", $selectedIds);

    // 1️⃣ Update only selected items with chosen status
    foreach ($selectedIds as $itemId) {

        $sql_item = "UPDATE tbl_expense_request_items
                     SET MgrStatus='$ManagerStatus',
                         MgrApproveDate='$ApproveDate'
                     WHERE id='$itemId'";

        if (!$conn->query($sql_item)) {
            throw new Exception("Failed to update expense item: $itemId");
        }
    }

    // 2️⃣ Set all remaining (unchecked) items to REJECT (MgrStatus = 2)
    $sql_reject_remaining = "
        UPDATE tbl_expense_request_items
        SET MgrStatus='2', 
            MgrApproveDate='$ApproveDate'
        WHERE ExpId='$id'
          AND id NOT IN ($selectedIdsStr)
    ";

    if (!$conn->query($sql_reject_remaining)) {
        throw new Exception("Failed to reject unselected expense items");
    }

} elseif (!$usesHierarchy) {

    // 3️⃣ If nothing selected → ALL items become REJECT
    $sql_reject_all = "
        UPDATE tbl_expense_request_items
        SET MgrStatus='2',
            MgrApproveDate='$ApproveDate'
        WHERE ExpId='$id'
    ";

    if (!$conn->query($sql_reject_all)) {
        throw new Exception("Failed to reject all items (none selected)");
    }
}



        // --- DELETE STOCK (IF REJECTED) --- //
        if ($ManagerStatus == 2 && !empty($id) && !empty($FrId)) {

            $sql_delete = "DELETE FROM tbl_cust_prod_stock_2025 
                           WHERE EmpExpId='$id' AND Status='Cr' AND UserId='$FrId'";

            if (!$conn->query($sql_delete)) {
                throw new Exception("Failed to delete stock for rejected items");
            }

        }


        // --- SUCCESS: COMMIT ALL CHANGES --- //
        $conn->commit();
        require_once __DIR__ . '/includes/approval_mail_service.php';
        @approval_mail_notify($conn, 'employee_expense', (int)$id, 'manager', $ManagerStatus, (int)$user_id, $MannagerComment);

echo "<script>alert('Updated Successfully!');";

        if ($_GET['page'] == 'ho') {
            echo "window.location.href='ho-manager-pending-expense-request.php';</script>";
        } 
        else if ($_GET['page'] == 'cocoless') {
            echo "window.location.href='coco-manager-less-than-pending-expense-request.php';</script>";
        } 
        else if ($_GET['page'] == 'cocomore') {
            echo "window.location.href='coco-manager-more-than-pending-expense-request.php';</script>";
        }
        else if ($_GET['page'] == 'newexe') {
            echo "window.location.href='new-execution-manager-pending-expense-request.php';</script>";
        }
        else if ($_GET['page'] == 'regexe') {
            echo "window.location.href='regular-manager-pending-expense-request.php';</script>";
        }
        else {
            echo "window.location.href='manager-pending-expense-request.php';</script>";
        }

    } catch (Exception $ex) {

        // Something failed → rollback all updates
        $conn->rollback();

        echo "<script>alert('Error: ".$ex->getMessage()."');</script>";
    }

}

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
                                    
                                     

 <div class="form-group col-md-3">
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
                                        
                                        
                            
                               <!--<div class="form-group col-md-4">
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
                                  
                            </div>-->
                            
                             

   
                                     <!--  <div class="form-group col-md-3">
                                              <label class="form-label">Expense Category</label>
                               <select class="form-control" name="ExpCatId" id="ExpCatId" >
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
                            
 <div class="form-group col-md-2">
       <label class="form-label">Vendor Mobile No</label>
                            <input type="number" class="form-control"  value="<?php echo $row7['VedPhone']; ?>" readonly>
                                                
                        </div>
                        -->
                         </div>
                                        
                                        <?php 
                                        $sql2 = "SELECT tve.*,tu.ShopName FROM tbl_expense_request_items tve LEFT JOIN tbl_users tu ON tu.id=tve.FrId WHERE tve.ExpId='".$_GET['id']."'";
                                        $rncnt2 = getRow($sql2);
                                        if($rncnt2 > 0){?>
                                        <br>
                                            <div class="form-row">
    <h5 style="font-size: 15px;color: blue;padding-left: 10px;">Expenses</h5>
<!-- SCROLLABLE RESPONSIVE WRAPPER -->
<div style="width:100%; overflow-x:auto; white-space: nowrap;">

<table class="table table-bordered table-hover" style="min-width:1500px; white-space: nowrap;">
  <tr>
    <th><input type="checkbox" id="selectAll"> </th>
    <th>Sr.No</th>
    <th>Franchise Name</th>
    <th>Amount</th>
    <th>Expense Date</th>
    <th>Expense Category</th>
    <th>Inventory Products</th>
    <th>Payment Type</th>
    <th>Vendor Mobile No</th>
    <th>GST</th>
    <th>Download PDF</th>
    <th>Attach PDF</th>
    <th>Receipt</th>
    <th>Payment Receipt</th>
    
    <th>Product Image</th>
    
    <th>Narration</th>
  </tr>

  <?php 
  $i=1;
  $row2 = getList($sql2);
  foreach($row2 as $result){
    $sql_11 = "SELECT Name FROM tbl_expenses_category WHERE id='".$result['ExpCatId']."'";
    $row_11 = getRecord($sql_11);

    $sql22 = "SELECT * FROM tbl_emp_expense_prod_items 
              WHERE ExpId='".$_GET['id']."' AND ExpItemId='".$result['id']."'";
    $rnct22 = getRow($sql22);
  ?>

  <tr>
    <td>
      <input type="checkbox" 
       class="expenseCheckbox" 
       name="approve_ids[]" 
       value="<?php echo $result['id']; ?>" 
       data-amount="<?php echo $result['Amount']; ?>">
    </td>

    <td><?php echo $i;?></td>
    <td><?php echo $result['ShopName'];?></td>
    <td><?php echo $result['Amount'];?></td>
    <td><?php echo $result['ExpenseDate'];?></td>
    <td><?php echo $row_11['Name'];?></td>

    <?php if($rnct22 > 0){?>
      <td><a href="view-expense-product-list.php?expid=<?php echo $_GET['id'];?>&expitemid=<?php echo $result['id'];?>" target="_blank">View Products</a></td>
    <?php } else { ?>
      <td style="color:red;">Product Not Found!</td>
    <?php } ?>

    <td><?php echo $result['PaymentMode'];?></td>
    <td><?php echo $result['VedPhone'];?></td>
    <td><?php echo $result['Gst'];?></td>
    
   <td>
<?php 
if (empty($result['PdfLink'])) { 
?>
    <span style="color:red;">No PDF Found</span>
<?php 
} else { 

    $pdf = $result['PdfLink'];

    // If full URL already exists, use it directly
    if (strpos($pdf, 'http') === 0) {
        $pdfUrl = $pdf;
    } else {
        $pdfUrl = '../pdffiles/' . $pdf;
    }
?>
    <a href="<?php echo $pdfUrl; ?>" target="_blank">Download</a>
<?php } ?>
</td>
    
    <td>
      <?php if($result["Photo3"] == '' || !file_exists('../uploads/employee_expenses/'.$result["Photo3"])){?>
        <span style="color:red;">No File Found</span>
      <?php } else { ?>
        <a href="../uploads/employee_expenses/<?php echo $result["Photo3"];?>" target="_blank">View File</a>
      <?php } ?>
    </td>
    
    <td>
      <?php if($result["Photo"] == '' || !file_exists('../uploads/employee_expenses/'.$result["Photo"])){?>
        <span style="color:red;">No Receipt Found</span>
      <?php } else { ?>
        <a href="../uploads/employee_expenses/<?php echo $result["Photo"];?>" target="_blank">View Receipt</a>
      <?php } ?>
    </td>

    <td>
      <?php if($result["Photo2"] == '' || !file_exists('../uploads/employee_expenses/'.$result["Photo2"])){?>
        <span style="color:red;">No Receipt Found</span>
      <?php } else { ?>
        <a href="../uploads/employee_expenses/<?php echo $result["Photo2"];?>" target="_blank">View Receipt</a>
      <?php } ?>
    </td>

    

    <td>
      <?php if($result["Photo4"] == '' || !file_exists('../uploads/employee_expenses/'.$result["Photo4"])){?>
        <span style="color:red;">No Image Found</span>
      <?php } else { ?>
        <a href="../uploads/employee_expenses/<?php echo $result["Photo4"];?>" target="_blank">View Image</a>
      <?php } ?>
    </td>

    

    <td><?php echo $result['Narration'];?></td>
  </tr>

  <?php $i++; } ?>
</table>

  </div>
                                            </div>

<?php } ?>


 <?php 
                                        $sql2 = "SELECT tve.*,tcp.ProductName FROM tbl_emp_expense_prod_items tve INNER JOIN tbl_cust_products2 tcp ON tcp.id=tve.MainProdId WHERE tve.ExpId='".$_GET['id']."'";
                                        $rncnt2 = getRow($sql2);
                                        if($rncnt2 > 0){?>
                                        <br>
                                            <div class="form-row">
    <h5 style="font-size: 15px;color: blue;padding-left: 10px;">All Expense Products</h5>
<table>
  <tr>
            <th>Sr.No</th>
            <th>Product Name</th>
            <th>Qty</th>
            <th>Purchase Price (Per Qty)</th>
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
<br>
<?php } ?>
<div class="form-row">
    <!--<div class="form-group col-md-4">
                                            <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                                            <select class="form-control" disabled>
                                                <option selected="" disabled="" value="">Select Status</option>
                                                <option value="Cash" <?php if($row7["PaymentMode"]=='Cash') {?> selected
                                                    <?php } ?>>By Cash</option>
                                                <option value="Online" <?php if($row7["PaymentMode"]=='Online') {?> selected
                                                    <?php } ?>>By Online</option>
                                                  
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>-->

<div class="form-group col-md-4">
                                            <label class="form-label">Approve Amount</label>
                                            <input type="text" name="MgrAmount" class="form-control"
                                                placeholder="" value="<?php echo $row7["Amount"]; ?>" required readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
 <div class="form-group col-md-4">
                                            <label class="form-label">Approve Date</label>
                                            <input type="date" name="ApproveDate" class="form-control"
                                                placeholder="" value="<?php echo date('Y-m-d'); ?>" required readonly>
                                            <div class="clearfix"></div>
                                        </div>

 <div class="form-group col-md-4">
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

<!--<div class="form-group col-md-6">
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

                                <div class="form-group col-md-6">
                                            <label class="form-label">Upload PDF</label><br>
                                            <?php if($row7['Photo3'] == '') {} else{?>
                        <a href="../uploads/<?php echo $row7['Photo3']; ?>" target="_blank">Download Pdf</a><?php } ?>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        
                                        
                                      <div class="form-group col-md-6">
                                            <label class="form-label">Upload Product Image</label><br>
                                            <?php if($row7['Photo4'] == '') {} else{?>
                        <a href="../uploads/<?php echo $row7['Photo4']; ?>" target="_blank"><img src="../uploads/<?php echo $row7['Photo4']; ?>" style="height:100px;"></a><?php } ?>
                                            <div class="clearfix"></div>
                                        </div>-->
                                        
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

<script>
// Auto calculate approve amount
function calculateApproveAmount() {
    let total = 0;

    document.querySelectorAll('.expenseCheckbox:checked').forEach(cb => {
        total += parseFloat(cb.getAttribute('data-amount'));
    });

    document.querySelector("input[name='MgrAmount']").value = total;
}

// When selecting individual items
document.querySelectorAll('.expenseCheckbox').forEach(cb => {
    cb.addEventListener('change', calculateApproveAmount);
});

// Select All functionality + amount update
document.getElementById('selectAll').addEventListener('change', function() {
    let checked = this.checked;
    document.querySelectorAll('.expenseCheckbox').forEach(cb => {
        cb.checked = checked;
    });
    calculateApproveAmount();
});
</script>

 
</body>

</html>