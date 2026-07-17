<?php 
include_once 'config.php';

session_start();
include_once 'auth.php';
require_once __DIR__ . '/includes/bh_expense_scope.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "BH-Employee-Expense";
$Page = "BH-Approve-Expense-Detail";
/*$sql = "SELECT GROUP_CONCAT(id) AS ids
FROM tbl_expense_request
WHERE Narration LIKE '%''%'";
$row = getRecord($sql);
echo $row['ids'];*/
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
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$returnPage = isset($_GET['page']) ? $_GET['page'] : 'bh';
$sql7 = "SELECT te.*,tu.Fname,tu.Lname,tu.Phone,tu.Photo AS Uphoto FROM tbl_expense_request te LEFT JOIN tbl_users tu ON tu.id=te.UserId WHERE te.id='$id'";
$row7 = getRecord($sql7);
if (!$row7) {
    echo "<script>alert('Expense not found.');window.location.href='ho-business-head-pending-expense-request.php';</script>";
    exit;
}
$EmpId = $row7['UserId'];

if ($row7['ManagerStatus'] != '1') {
    echo "<script>alert('Manager approval is required first.');window.location.href='ho-business-head-pending-expense-request.php';</script>";
    exit;
}
$expCatId = isset($row7['ExpCatId']) ? (int)$row7['ExpCatId'] : 0;
if ($expCatId === 3) {
    $hrSt = isset($row7['HrStatus']) ? (string)$row7['HrStatus'] : '';
    if ($hrSt !== '1') {
        echo "<script>alert('HR approval is required before business head (salary category).');window.location.href='ho-business-head-pending-expense-request.php';</script>";
        exit;
    }
}
if (!isset($row7['BhStatus']) || (string)$row7['BhStatus'] !== '0') {
    echo "<script>alert('This request is not pending business head approval.');window.location.href='ho-business-head-pending-expense-request.php';</script>";
    exit;
}

$allowedBhFrIds = maha_bh_load_assigned_franchise_ids($conn, $user_id);
$frIdSetBh = maha_bh_build_expense_franchise_ids($conn, $id, $row7);
if (!maha_bh_expense_visible_for_scope($frIdSetBh, $allowedBhFrIds)) {
    echo "<script>alert('This expense is not under a franchise assigned to your account.');window.location.href='ho-business-head-pending-expense-request.php';</script>";
    exit;
}

$sql88 = "SELECT SUM(creditAmt) As Credit,SUM(debitAmt) As Debit FROM (SELECT (case when Status='Cr' then sum(Amount) else 0 end) as creditAmt,(case when Status='Dr' then sum(Amount) else 0 end) as debitAmt FROM wallet WHERE UserId='$EmpId' GROUP BY Status) as a";
    $row88 = getRecord($sql88);
    $WalletBal = $row88['Credit'] - $row88['Debit'];

$mgrAmtVal = (float)$row7['MgrAmount'];
$expCatIdPost = isset($row7['ExpCatId']) ? addslashes(trim($row7['ExpCatId'])) : '';

if(isset($_POST['submit'])){
    $frIdSetPost = maha_bh_build_expense_franchise_ids($conn, $id, $row7);
    $allowedPost = maha_bh_load_assigned_franchise_ids($conn, $user_id);
    if (!maha_bh_expense_visible_for_scope($frIdSetPost, $allowedPost)) {
        echo "<script>alert('This expense is not under a franchise assigned to your account.');window.location.href='ho-business-head-pending-expense-request.php';</script>";
        exit;
    }

   $conn->begin_transaction();

try {
    
    $ApproveDate = addslashes(trim($_POST["ApproveDate"])); 
    $BhComment = addslashes(trim($_POST["MannagerComment"]));
    $BhAmount = addslashes(trim($_POST["AccAmount"])); 
    $BhDecision = addslashes(trim($_POST["ManagerStatus"]));
    $CreatedDate = date('Y-m-d'); 
    $CreatedTime = date('h:i a');
    
    $selectedIds = isset($_POST['approve_ids']) ? $_POST['approve_ids'] : [];
    $selectedStr = !empty($selectedIds) ? implode(",", array_map('intval', $selectedIds)) : '';

    $finalAtBh = ($mgrAmtVal <= 2000);

    if ($finalAtBh && $BhDecision == '1') {
        $sql_main = "UPDATE tbl_expense_request 
                 SET BhApproveDate='$ApproveDate',
                     BhComment='$BhComment',
                     BhAmount='$BhAmount',
                     BhStatus='$BhDecision',
                     BhBy='$user_id',
                     AdminApproveDate='$ApproveDate',
                     AdminComment='$BhComment',
                     AccAmount='$BhAmount',
                     AdminStatus='$BhDecision',
                     AccBy='$user_id',
                     Status='$BhDecision',
                     ExpCatId='$expCatIdPost'
                 WHERE id = '$id' AND ManagerStatus='1' AND BhStatus='0'";
    } elseif (!$finalAtBh && $BhDecision == '1') {
        $sql_main = "UPDATE tbl_expense_request 
                 SET BhApproveDate='$ApproveDate',
                     BhComment='$BhComment',
                     BhAmount='$BhAmount',
                     BhStatus='$BhDecision',
                     BhBy='$user_id',
                     ExpCatId='$expCatIdPost'
                 WHERE id = '$id' AND ManagerStatus='1' AND BhStatus='0'";
    } else {
        $sql_main = "UPDATE tbl_expense_request 
                 SET BhApproveDate='$ApproveDate',
                     BhComment='$BhComment',
                     BhAmount=NULL,
                     BhStatus='$BhDecision',
                     BhBy='$user_id',
                     ExpCatId='$expCatIdPost'
                 WHERE id = '$id' AND ManagerStatus='1' AND BhStatus='0'";
    }

    if (!$conn->query($sql_main)) {
        throw new Exception("Failed updating main record");
    }
    if ($conn->affected_rows < 1) {
        throw new Exception("No rows updated (already processed?)");
    }


    foreach ($selectedIds as $itemId) {
        $itemId = (int)$itemId;
        $sql_item = "UPDATE tbl_expense_request_items
                     SET BhStatus='$BhDecision',
                         BhApproveDate='$ApproveDate'
                     WHERE id='$itemId'";

        if (!$conn->query($sql_item)) {
            throw new Exception("Failed updating item $itemId");
        }
    }

    if (!empty($selectedIds)) {

        $sql_reject = "UPDATE tbl_expense_request_items
                       SET BhStatus='2',
                           BhApproveDate='$ApproveDate'
                       WHERE ExpId='$id'
                         AND id NOT IN ($selectedStr)";

        if (!$conn->query($sql_reject)) {
            throw new Exception("Failed rejecting unselected items");
        }

    } else {

        $sql_reject_all = "UPDATE tbl_expense_request_items
                           SET BhStatus='2',
                               BhApproveDate='$ApproveDate'
                           WHERE ExpId='$id'";
        if (!$conn->query($sql_reject_all)) {
            throw new Exception("Failed rejecting all items");
        }

    }

    if ($finalAtBh && $BhDecision == '1') {
        $conn->query("DELETE FROM wallet WHERE UserId='$EmpId' AND ExpId='$id'");
        $Narration = "Amount Deduct against Expense For ".addslashes(trim($row7["Narration"]));
        $conn->query("INSERT INTO wallet SET 
                      UserId='$EmpId',
                      Amount='$BhAmount',
                      Narration='$Narration',
                      Status='Dr',
                      CreatedDate='$CreatedDate',
                      CreatedTime='$CreatedTime',
                      ExpId='$id'");
    }

    if ($BhDecision == '2') {
        $conn->query("DELETE FROM tbl_cust_prod_stock_2025 WHERE EmpExpId='$id'");
    }


    $conn->commit();
    require_once __DIR__ . '/includes/approval_mail_service.php';
    @approval_mail_notify($conn, 'employee_expense', (int)$id, 'bh', $BhDecision, (int)$user_id, $BhComment);

if($returnPage == 'ho'){
 echo "<script>alert('Saved successfully!');window.location.href='ho-admin-pending-expense-request.php';</script>";
  }
  else if($returnPage == 'below2k'){
 echo "<script>alert('Saved successfully!');window.location.href='ho-business-head-pending-expense-request.php';</script>";
  }
  else{
    echo "<script>alert('Saved successfully!');window.location.href='ho-business-head-pending-expense-request.php';</script>";  
  }

} catch (Exception $e) {

    $conn->rollback();
    echo "<script>alert('Error: ".$e->getMessage()."');</script>";
}



  }
?>

<style>
    .unchecked-row {
    background-color: #ffe5e5 !important; 
}

</style>
                <div class="layout-content">

                    <div class="container-fluid flex-grow-1 container-p-y">
                        <h4 class="font-weight-bold py-3 mb-0">Business head — 2nd approval</h4>

                        <div class="card mb-4">
                            <div class="card-body">
                                 <form id="validation-form" method="post" autocomplete="off">
                                <div class="row">

                                    <div class="col-lg-12">
                                <div id="alert_message"></div>
                               
                                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" id="userid">
                                    <input type="hidden" name="action" value="Save" id="action">
                                    <input type="hidden" name="UserId" value="<?php echo $EmpId;?>" id="UserId">
                                    <div class="form-row">
                                    
                                     
 <div class="form-group col-md-2">
                                            <label class="form-label">Expense Id</label>
                                            <input type="text" class="form-control"
                                                placeholder="" value="<?php echo $row7['id']; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
 <div class="form-group col-md-2">
                                            <label class="form-label">Employee Name</label>
                                            <input type="text" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7['Fname']." ".$row7['Lname']; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Employee Contact No</label>
                                            <input type="text" class="form-control"
                                                placeholder="" value="<?php echo $row7['Phone']; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-2">
                                            <label class="form-label">Wallet Amount</label>
                                            <input type="text" class="form-control" id="WalletBal"
                                                placeholder="" value="<?php echo $WalletBal; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-2">
                                            <label class="form-label">Expense Amount</label>
                                            <input type="text" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["Amount"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Expense Date</label>
                                            <input type="date" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["ExpenseDate"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Manager Approve Amount</label>
                                            <input type="text" name="MgrAmount" class="form-control"
                                                placeholder="" value="<?php echo $row7["MgrAmount"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        
                                        
                                        
                                        
                                         <div class="form-group col-md-9">
                                            <label class="form-label">Expense For</label>
                                            <input type="text" name="TaskDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["Narration"]; ?>" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        
                            
                            
                        
                         </div>

<?php 
$sql2 = "SELECT tve.*,tu.ShopName 
         FROM tbl_expense_request_items tve 
         LEFT JOIN tbl_users tu ON tu.id=tve.FrId 
         WHERE tve.ExpId='" . $_GET['id'] . "'";

$rowData = getList($sql2);

$approved = [];
$rejected = [];
$pending = [];

foreach ($rowData as $r) {
    if ($r['MgrStatus'] == 1) {
        $approved[] = $r;
    } elseif ($r['MgrStatus'] == 2) {
        $rejected[] = $r;
    } else {
        $pending[] = $r; // 0 or null (not yet decided)
    }
}

function renderExpenseTable($data, $title) {
    if (count($data) == 0) return;

    ?>
    <br>
    <h5 style="font-size: 15px;color: blue;padding-left: 10px;"><?php echo $title; ?></h5>

    <div style="width:100%; overflow-x:auto; white-space: nowrap;">
    <table class="table table-bordered table-hover" style="min-width:1500px; white-space: nowrap;">
        <tr>
            <th><input type="checkbox" class="selectAllTable"></th>
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
        $i = 1;
        foreach ($data as $result) {

            $sql_11 = "SELECT Name FROM tbl_expenses_category WHERE id='" . $result['ExpCatId'] . "'";
            $row_11 = getRecord($sql_11);

            $sql22 = "SELECT * FROM tbl_emp_expense_prod_items 
                      WHERE ExpId='" . $_GET['id'] . "' 
                      AND ExpItemId='" . $result['id'] . "'";
            $rnct22 = getRow($sql22);
        ?>

        <tr>
            <?php if($result['MgrStatus'] == 1){?>
            <td>
                <input type="checkbox"
                       class="expenseCheckbox"
                       name="approve_ids[]"
                       value="<?php echo $result['id']; ?>"
                       data-amount="<?php echo $result['Amount']; ?>"
                       <?php echo ($result['MgrStatus'] == 1) ? 'checked' : ''; ?> >
            </td>
            <?php } else{?>
                <td><input type="checkbox"
                       class="expenseCheckbox"
                       name="approve_ids[]"
                       value="<?php echo $result['id']; ?>"
                       data-amount="<?php echo $result['Amount']; ?>"
                       <?php echo ($result['MgrStatus'] == 1) ? 'checked' : ''; ?> ></td>
            <?php } ?>

            <td><?php echo $i; ?></td>
            <td><?php echo $result['ShopName']; ?></td>
            <td><?php echo $result['Amount']; ?></td>
            <td><?php echo $result['ExpenseDate']; ?></td>

            <td>
                <a href="#"
                   class="edit-category"
                   data-id="<?php echo $result['id']; ?>"
                   data-currentcat="<?php echo $row_11['Name']; ?>"
                   data-catid="<?php echo $result['ExpCatId']; ?>"
                   data-bs-toggle="modal"
                   data-bs-target="#editCategoryModal">
                   <?php echo $row_11['Name']; ?>
                </a>
            </td>

            <?php if ($rnct22 > 0) { ?>
                <td><a href="view-expense-product-list.php?expid=<?php echo $_GET['id']; ?>&expitemid=<?php echo $result['id']; ?>" target="_blank">View Products</a></td>
            <?php } else { ?>
                <td style="color:red;">Product Not Added!</td>
            <?php } ?>

            <td><?php echo $result['PaymentMode']; ?></td>
            <td><?php echo $result['VedPhone']; ?></td>
            
            <td><?php echo $result['Gst']; ?></td>

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
                <?php if ($result["Photo3"] == '') { ?>
                    <span style="color:red;">No File Found</span>
                <?php } else if (file_exists('../uploads/employee_expenses/'.$result["Photo3"])) { ?>
                    <a href="../uploads/employee_expenses/<?php echo $result["Photo3"]; ?>" target="_blank">View File</a>
                <?php } else { ?>
                    <span style="color:red;">No File Found</span>
                <?php } ?>
            </td>

            <td>
                <?php if ($result["Photo"] == '') { ?>
                    <span style="color:red;">No Receipt Found</span>
                <?php } else if (file_exists('../uploads/employee_expenses/'.$result["Photo"])) { ?>
                    <a href="../uploads/employee_expenses/<?php echo $result["Photo"]; ?>" target="_blank">View Receipt</a>
                <?php } else { ?>
                    <span style="color:red;">No Receipt Found</span>
                <?php } ?>
            </td>

            <td>
                <?php if ($result["Photo2"] == '') { ?>
                    <span style="color:red;">No Receipt Found</span>
                <?php } else if (file_exists('../uploads/employee_expenses/'.$result["Photo2"])) { ?>
                    <a href="../uploads/employee_expenses/<?php echo $result["Photo2"]; ?>" target="_blank">View Receipt</a>
                <?php } else { ?>
                    <span style="color:red;">No Receipt Found</span>
                <?php } ?>
            </td>

            <td>
                <?php if ($result["Photo4"] == '') { ?>
                    <span style="color:red;">No Image Found</span>
                <?php } else if (file_exists('../uploads/employee_expenses/'.$result["Photo4"])) { ?>
                    <a href="../uploads/employee_expenses/<?php echo $result["Photo4"]; ?>" target="_blank">View Image</a>
                <?php } else { ?>
                    <span style="color:red;">No Image Found</span>
                <?php } ?>
            </td>
            <td><?php echo $result['Narration']; ?></td>

        </tr>

        <?php $i++; } ?>
    </table>
    </div>
<?php 
}  // END FUNCTION
?>


<!-- RENDER TABLE 1: APPROVED -->
<?php renderExpenseTable($approved, "Approved Expenses"); ?>

<!-- RENDER TABLE 2: REJECTED -->
<?php renderExpenseTable($rejected, "Rejected Expenses"); ?>

<!-- RENDER TABLE 3: PENDING -->
<?php renderExpenseTable($pending, "Pending Approval"); ?>

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
             
                                        
                                        <div class="form-group col-md-4">
                                            <label class="form-label">Approve amount</label>
                                            <input type="text" name="AccAmount" class="form-control"
                                                placeholder="" value="<?php echo $row7["MgrAmount"]; ?>" required readonly>
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
                                                <option value="1" <?php if(isset($row7["BhStatus"]) && $row7["BhStatus"]=='1') {?> selected
                                                    <?php } ?>>Approved</option>
                                                <option value="0" <?php if(!isset($row7["BhStatus"]) || $row7["BhStatus"]==='' || $row7["BhStatus"]=='0') {?> selected
                                                    <?php } ?>>Pending</option>
                                                    <option value="2" <?php if(isset($row7["BhStatus"]) && $row7["BhStatus"]=='2') {?> selected
                                                    <?php } ?>>Reject</option>
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
 <div class="form-group col-md-12">
                                            <label class="form-label">Comment</label>
                                            <textarea name="MannagerComment" class="form-control"
                                                placeholder=""><?php echo isset($row7["BhComment"]) ? $row7["BhComment"] : ''; ?></textarea>
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



<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="editCategoryLabel">Edit Expense Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateCategoryForm">
          <input type="hidden" id="expenseId" name="expenseId">

          <div class="mb-3">
            <label for="currentCategory" class="form-label">Current Category</label>
            <input type="text" id="currentCategory" class="form-control" readonly>
          </div>

          <div class="mb-3">
            <label for="newCategory" class="form-label">Select New Category</label><br>
            <select id="newCategory" name="newCategory" class="form-control" required>
              <option value="">-- Select Category --</option>
              <?php 
                $catList = getList("SELECT id, Name FROM tbl_expenses_category ORDER BY Name ASC");
                foreach($catList as $cat){
                  echo "<option value='".$cat['id']."'>".$cat['Name']."</option>";
                }
              ?>
            </select>
          </div>

          <button type="submit" class="btn btn-success w-100">Update Category</button>
        </form>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
 <script>
 function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.expenseCheckbox:checked').forEach(cb => {
        total += parseFloat(cb.getAttribute("data-amount"));
    });
    document.querySelector("input[name='AccAmount']").value = total;
}

document.getElementById("submit").addEventListener("click", function (e) {

    let isAnyChecked = document.querySelectorAll('.expenseCheckbox:checked').length > 0;

    if (!isAnyChecked) {
        e.preventDefault(); // Stop form submit
        alert("Please check at least one expense before submitting!");
        return false;
    }

});


document.addEventListener('DOMContentLoaded', function () {

  // --- Helper: safe call to calculateTotal if it's defined ---
  function safeCalculateTotal(){
    if (typeof calculateTotal === 'function') {
      calculateTotal();
    }
  }

  // Attach change listeners to ALL expense checkboxes (global across tables).
  document.querySelectorAll('.expenseCheckbox').forEach(cb => {
    cb.addEventListener('change', function () {
      // update the select-all checkbox for this row's parent table (if any)
      const table = this.closest('table');
      if (table) {
        const header = table.querySelector('.selectAllTable');
        if (header) {
          const boxes = Array.from(table.querySelectorAll('.expenseCheckbox'));
          const allChecked = boxes.every(b => b.checked);
          const noneChecked = boxes.every(b => !b.checked);
          header.checked = allChecked;
          header.indeterminate = !allChecked && !noneChecked;
        }
      }
      safeCalculateTotal();
    });
  });

  // Attach listeners to each table's "select all" checkbox
  document.querySelectorAll('.selectAllTable').forEach(headerCb => {
    headerCb.addEventListener('change', function () {
      const table = this.closest('table');
      if (!table) return;
      const checked = this.checked;
      table.querySelectorAll('.expenseCheckbox').forEach(cb => cb.checked = checked);
      // clear indeterminate when user clicks header
      this.indeterminate = false;
      safeCalculateTotal();
    });

    // initialize header state based on current rows (checked / indeterminate)
    (function initHeaderState(h) {
      const table = h.closest('table');
      if (!table) return;
      const boxes = Array.from(table.querySelectorAll('.expenseCheckbox'));
      if (boxes.length === 0) {
        h.checked = false;
        h.indeterminate = false;
        return;
      }
      const allChecked = boxes.every(b => b.checked);
      const noneChecked = boxes.every(b => !b.checked);
      h.checked = allChecked;
      h.indeterminate = !allChecked && !noneChecked;
    })(headerCb);
  });

  // Final initial total calc
  safeCalculateTotal();

});
     function checkWalletBal(){
         var UserId = $('#UserId').val();
         
         var action = "checkWalletBal";
            $.ajax({
                url: "ajax_files/ajax_wallet.php",
                method: "POST",
                data: {
                    action: action,
                    UserId: UserId
                    
                },
                success: function(data) {
                  //alert(data);
                  //console.log(data);
                    $('#WalletBal').val(data);
                }
            });
     }
     
      $(document).ready(function() {
   setInterval(function(){  
           checkWalletBal();
        }, 1000);
    });
    
    $(document).ready(function(){

  // When user clicks category link
  $('.edit-category').click(function(){
    var expid = $(this).data('id');
    var catname = $(this).data('currentcat');
    var catid = $(this).data('catid');
    $('#expenseId').val(expid);
    $('#currentCategory').val(catname);
    $('#newCategory').val(catid);
  });

  // When user submits the form
  $('#updateCategoryForm').submit(function(e){
    e.preventDefault();

    $.ajax({
      url: 'update_expense_category.php',
      type: 'POST',
      data: $(this).serialize(),
      success: function(response){
         //console.log(response);
        if(response.trim() === 'success'){
          alert('Category updated successfully!');
          location.reload(); // Reload page to reflect changes
        } else {
          alert('Error updating category: ' + response);
        }
      }
    });
  });

});
 </script>
</body>

</html>