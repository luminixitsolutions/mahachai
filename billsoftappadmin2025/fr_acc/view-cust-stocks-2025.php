<?php 
include_once 'config.php';

session_start();
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$AdminLoginId = $_SESSION['Admin']['id'];
$MainPage = "Customer-Products-2025";
$Page = "View-Cust-Stock-2025";
?>
<!DOCTYPE html>
<html lang="en" class="default-style">
<head>
<title><?php echo $Proj_Title; ?> | View Stock List</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="description" content="" />
<meta name="keywords" content="">
<meta name="author" content="" />
<?php include_once 'header_script.php'; ?>
</head>
<body>

 <div class="layout-wrapper layout-1 layout-without-sidenav">
<div class="layout-inner">

<?php include_once 'top_header.php'; include_once 'sidebar.php'; ?>


<div class="layout-container">



<?php
if($_REQUEST["action"]=="delete")
{
  $id = $_REQUEST["id"];
  if (!empty($id) && is_numeric($id)) {
  $sql11 = "DELETE FROM tbl_cust_prod_stock_2025 WHERE id = '$id'";
  $conn->query($sql11);
  
  $url = $_SERVER['REQUEST_URI'];
  $createddate = date('Y-m-d H:i:s');
  $sql = "INSERT INTO tbl_user_logs SET userid='$user_id',frid='$BillSoftFrId',url='$url',action='customer product stock record deleted',invid='$id',createddate='$createddate',roll='customer-product-stock'";
  $conn->query($sql);
  ?>
    <script type="text/javascript">
      alert("Deleted Successfully!");
      window.location.href="view-cust-stocks.php";
    </script>
<?php } } ?>

<div class="layout-content">

<div class="container-fluid flex-grow-1 container-p-y">
<h4 class="font-weight-bold py-3 mb-0">View Stock List
    <?php if(in_array("115", $Options)) {?>   
<span style="float: right;">
<a href="add-cust-stock-2025.php" class="btn btn-secondary btn-round"><i class="ion ion-md-add mr-2"></i> Add New Stock</a></span>
<?php } ?>
</h4>

<div class="card" style="padding: 10px;">
      <div id="accordion2">
<div class="card mb-2">
                                        
                                        <div id="accordion2-2" class="collapse show" data-parent="#accordion2">
                                            <div class="" style="padding:5px;">
                                                <form id="validation-form" method="post" enctype="multipart/form-data" action="">
<div class="form-row">

  <div class="form-group col-md-3">
<label class="form-label">Franchise</label>
<select class="select2-demo form-control" name="FrId" id="FrId">
<option value="all" <?php if(empty($_REQUEST['FrId']) || $_REQUEST['FrId'] == 'all') { ?> selected <?php } ?>>All</option>
<?php
$sqlFr = "SELECT id, ShopName, Phone FROM tbl_users_bill WHERE Roll=5 AND Status=1 AND id='$BillSoftFrId' ORDER BY ShopName";
$rowFr = getList($sqlFr);
foreach ($rowFr as $fr) {
?>
<option value="<?php echo $fr['id']; ?>" <?php if(isset($_REQUEST['FrId']) && $_REQUEST['FrId'] == $fr['id']) { ?> selected <?php } ?>>
<?php echo htmlspecialchars($fr['ShopName'] . ' (' . $fr['Phone'] . ')'); ?></option>
<?php } ?>
</select>
<div class="clearfix"></div>
</div>

  <div class="form-group col-md-3">
<label class="form-label">Vendor</label>
<select class="select2-demo form-control" name="VedId" id="VedId">
<option value="all" <?php if(empty($_REQUEST['VedId']) || $_REQUEST['VedId'] == 'all') { ?> selected <?php } ?>>All</option>
<?php
$sqlVed = "SELECT id, Fname, TradeName FROM tbl_users WHERE Roll=3 AND Status=1 AND Fname!='' ORDER BY Fname";
$rowVed = getList($sqlVed);
foreach ($rowVed as $ved) {
    $vedLabel = !empty($ved['TradeName']) ? $ved['TradeName'] . ' (' . $ved['Fname'] . ')' : $ved['Fname'];
?>
<option value="<?php echo $ved['id']; ?>" <?php if(isset($_REQUEST['VedId']) && $_REQUEST['VedId'] == $ved['id']) { ?> selected <?php } ?>>
<?php echo htmlspecialchars($vedLabel); ?></option>
<?php } ?>
</select>
<div class="clearfix"></div>
</div>

  <div class="form-group col-md-3">
<label class="form-label">Products</label>
 <select class="select2-demo form-control" name="ProdId" id="ProdId">
<option selected="" value="all">All</option>
 <?php 
  $sql12 = "SELECT * FROM tbl_cust_products_2025 WHERE Status='1' AND CreatedBy='$BillSoftFrId' AND ProdType=0 AND delete_flag=0 AND checkstatus=1";
  $row12 = getList($sql12);
  foreach($row12 as $result){
     ?>
  <option <?php if($_REQUEST["ProdId"] == $result['id']) {?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['ProductName']; ?></option>
<?php } ?>
</select>
<div class="clearfix"></div>
</div>



<div class="form-group col-md-2">
<label class="form-label">Type</label>
<select class="form-control" name="ExpType" id="ExpType">
<option value="all" <?php if(!isset($_REQUEST['ExpType']) || $_REQUEST['ExpType'] == 'all') { ?> selected <?php } ?>>All</option>
<option value="manual" <?php if(isset($_REQUEST['ExpType']) && $_REQUEST['ExpType'] == 'manual') { ?> selected <?php } ?>>Manual</option>
<option value="vendor" <?php if(isset($_REQUEST['ExpType']) && $_REQUEST['ExpType'] == 'vendor') { ?> selected <?php } ?>>Vendor Expense</option>
<option value="employee" <?php if(isset($_REQUEST['ExpType']) && $_REQUEST['ExpType'] == 'employee') { ?> selected <?php } ?>>Employee Expense</option>
</select>
</div>

<div class="form-group col-md-2">
<label class="form-label">Expense Id</label>
<input type="text" name="ExpId" id="ExpId" class="form-control" value="<?php echo isset($_REQUEST['ExpId']) ? htmlspecialchars($_REQUEST['ExpId']) : ''; ?>" placeholder="Expense Id" autocomplete="off">
</div>

<div class="form-group col-md-2">
<label class="form-label">From Date </label>
<input type="date" name="FromDate" id="FromDate" class="form-control" value="<?php echo isset($_REQUEST['FromDate']) ? $_REQUEST['FromDate'] : ''; ?>" autocomplete="off">
</div>
<div class="form-group col-md-2">
<label class="form-label">To Date</label>
<input type="date" name="ToDate" id="ToDate" class="form-control" value="<?php echo isset($_REQUEST['ToDate']) ? $_REQUEST['ToDate'] : ''; ?>" autocomplete="off">
</div>
<input type="hidden" name="Search" value="Search">
<div class="form-group col-md-1" style="padding-top:20px;">
<button type="submit" name="submit" class="btn btn-primary btn-finish">Search</button>
</div>
<?php if(isset($_REQUEST['Search'])) {?>
<div class="col-md-1">
<label class="form-label d-none d-md-block">&nbsp;</label>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-info btn-block" data-toggle="tooltip" data-placement="top" data-original-title="Clear Filter">X</a>
</div>
<?php } ?>
</div>

</form>
                                            </div>
                                        </div>
                                    </div>
   </div>
<div class="card-datatable table-responsive">
<table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
               <th>#</th>
               <th>Product Id</th>
                <th>Product Name</th>
                <th>Stock In Qty</th>
                <th>MRP Price</th>
                <th>Date</th>
                <th>Purchase Price</th>
                <th>Type</th>
                <th>Expense Id</th>
                <th>Vendor Name</th>
               <th>Created By</th>
              
               <?php if(in_array("116", $Options) || in_array("117", $Options)) {?>
               <th>Action</th>
               <?php } ?>
            
            </tr>
        </thead>
        <tbody>
            <?php 
            $i=1;
            $filterFrId = $BillSoftFrId;
            if (!empty($_REQUEST['FrId']) && $_REQUEST['FrId'] != 'all') {
                $reqFrId = (int) $_REQUEST['FrId'];
                if ($reqFrId === (int) $BillSoftFrId) {
                    $filterFrId = $reqFrId;
                }
            }
            $sql = "SELECT ts.*, p.ProductName, p.MinPrice AS ProdMinPrice, u.Fname AS CreatedByName,
                    ved.Fname AS VendorName, ved.TradeName AS VendorTradeName
                    FROM tbl_cust_prod_stock_2025 ts 
                    INNER JOIN tbl_cust_products_2025 p ON ts.ProdId=p.id 
                    LEFT JOIN tbl_users_bill u ON u.id=ts.CreatedBy 
                    LEFT JOIN tbl_vendor_expenses ve ON ve.id=ts.VedExpId AND ts.VedExpItem='Yes'
                    LEFT JOIN tbl_users ved ON ved.id = IF(ts.VedExpItem='Yes' AND ve.VedId IS NOT NULL, ve.VedId, ts.VedId)
                    WHERE ts.FrId='$filterFrId' AND ts.Status='Cr' AND ts.ProdType=0";
            if(isset($_REQUEST['ProdId']) && $_REQUEST['ProdId'] != '' && $_REQUEST['ProdId'] != 'all'){
                $ProdId = mysqli_real_escape_string($conn, $_REQUEST['ProdId']);
                $sql.= " AND ts.ProdId='$ProdId'";
            }
            if(!empty($_REQUEST['ExpId'])){
                $ExpId = mysqli_real_escape_string($conn, trim($_REQUEST['ExpId']));
                $sql.= " AND (ts.VedExpId='$ExpId' OR ts.EmpExpId='$ExpId')";
            }
            if(isset($_REQUEST['ExpType']) && $_REQUEST['ExpType'] != '' && $_REQUEST['ExpType'] != 'all'){
                $ExpType = $_REQUEST['ExpType'];
                if($ExpType == 'manual'){
                    $sql.= " AND (ts.VedExpItem IS NULL OR ts.VedExpItem!='Yes') AND (ts.EmpExpItem IS NULL OR ts.EmpExpItem!='Yes')";
                } elseif($ExpType == 'vendor'){
                    $sql.= " AND ts.VedExpItem='Yes'";
                } elseif($ExpType == 'employee'){
                    $sql.= " AND ts.EmpExpItem='Yes'";
                }
            }
            if (!empty($_REQUEST['VedId']) && $_REQUEST['VedId'] != 'all') {
                $VedId = (int) $_REQUEST['VedId'];
                $sql .= " AND (ts.VedId='$VedId' OR ve.VedId='$VedId')";
            }
            if(!empty($_REQUEST['FromDate'])){
                $FromDate = mysqli_real_escape_string($conn, $_REQUEST['FromDate']);
                $sql.= " AND ts.StockDate>='$FromDate'";
            }
            if(!empty($_REQUEST['ToDate'])){
                $ToDate = mysqli_real_escape_string($conn, $_REQUEST['ToDate']);
                $sql.= " AND ts.StockDate<='$ToDate'";
            }
            $sql.=" ORDER BY ts.id DESC";    
            //echo $sql;
            $res = $conn->query($sql);
            while($row = $res->fetch_assoc())
            {
                $stockType = 'Manual';
                $displayExpId = '-';
                if(!empty($row['VedExpItem']) && $row['VedExpItem'] == 'Yes'){
                    $stockType = 'Vendor Expense';
                    if(!empty($row['VedExpId'])){
                        $displayExpId = $row['VedExpId'];
                    }
                } elseif(!empty($row['EmpExpItem']) && $row['EmpExpItem'] == 'Yes'){
                    $stockType = 'Employee Expense';
                    if(!empty($row['EmpExpId'])){
                        $displayExpId = $row['EmpExpId'];
                    }
                }
                $displayQty = $row['Qty2'];
                if ($displayQty === '' || $displayQty === null) {
                    $displayQty = $row['Qty'];
                }
                $displayVendorName = '-';
                if (!empty($row['VendorTradeName'])) {
                    $displayVendorName = $row['VendorTradeName'];
                } elseif (!empty($row['VendorName'])) {
                    $displayVendorName = $row['VendorName'];
                }
                $displayPurchasePrice = $row['PurchasePrice'];
                $displaySellPrice = $row['SellPrice'];
                if ($displaySellPrice === '' || $displaySellPrice === null) {
                    $displaySellPrice = $row['ProdMinPrice'];
                }
             ?>
            <tr>
               <td><?php echo $i; ?></td>
                <td><?php echo $row['ProdId']; ?></td>
               <td><?php echo $row['ProductName']; ?></td>
               <td><?php echo $displayQty; ?></td>
               <td><?php echo $displaySellPrice !== '' && $displaySellPrice !== null ? number_format((float)$displaySellPrice, 2) : '-'; ?></td>
               <td><?php echo date("d/m/Y", strtotime(str_replace('-', '/',$row['StockDate']))); ?></td>
               <td><?php echo $displayPurchasePrice !== '' && $displayPurchasePrice !== null ? number_format((float)$displayPurchasePrice, 2) : '-'; ?></td>
               <td><?php echo $stockType; ?></td>
               <td><?php echo $displayExpId; ?></td>
               <td><?php echo htmlspecialchars($displayVendorName); ?></td>
               <td><?php echo !empty($row['CreatedByName']) ? $row['CreatedByName'] : '-'; ?></td>
               <?php if(in_array("116", $Options) || in_array("117", $Options)) {?>
            <td>
                 <?php if(in_array("116", $Options)){?>
                <a href="add-cust-stock.php?id=<?php echo $row['id']; ?>" ><i class="lnr lnr-pencil mr-2"></i></a>&nbsp;&nbsp;
                <?php } if(in_array("117", $Options)){?>
              <!--  <a onClick="return confirm('Are you sure you want delete this record?');" href="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $row['id']; ?>&action=delete" ><i class="lnr lnr-trash text-danger"></i></a>-->
                <?php } ?>
            </td>
            
            <?php } ?>
       
              
            </tr>
           <?php $i++;} ?>

          
        </tbody>
    </table>
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

<script type="text/javascript">
 
    $(document).ready(function() {
    $('#example').DataTable({
        "scrollX": true,
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5'
        ]
    });
});
</script>
</body>
</html>
