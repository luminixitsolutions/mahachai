<?php 
include_once 'config.php';

session_start();
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "Report-2025";
$Page = "Sell-Product-Report-2025";
?>
<!DOCTYPE html>
<html lang="en" class="default-style">
<head>
<title>Product Wise Sell Report </title>
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




<div class="layout-content">

<div class="container-fluid flex-grow-1 container-p-y">
<h4 class="font-weight-bold py-3 mb-0">Product Wise Sell Report</h4>
<br>

<div class="card">
<div id="accordion2">
<div class="card mb-2">
                                        
                                        <div id="accordion2-2" class="collapse show" data-parent="#accordion2">
                                            <div class="" style="padding: 5px;padding-left: 20px;">
                                                <form id="validation-form" method="post" enctype="multipart/form-data" action="">
<div class="form-row">

<?php
$filterProdType2 = isset($_REQUEST['FilterProdType2']) ? (string) $_REQUEST['FilterProdType2'] : 'all';
$prodType2Sql = '';
if ($filterProdType2 === '1') {
    $prodType2Sql = ' AND ProdType2=1';
} elseif ($filterProdType2 === '2') {
    $prodType2Sql = ' AND ProdType2=2';
}
?>

<div class="form-group col-md-3">
<label class="form-label"> Product<span class="text-danger">*</span></label>
 <select class="form-control" name="ProdId" id="ProdId" required>
<option selected="" value="all">All</option>
 <?php 
  $sql12 = "SELECT * FROM tbl_cust_products_2025 WHERE CreatedBy=$BillSoftFrId AND ProdType=0 AND checkstatus=1 AND delete_flag=0" . $prodType2Sql;
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
<label class="form-label">Product Type</label>
<select class="form-control" name="FilterProdType2" id="FilterProdType2">
<option value="all" <?php echo ($filterProdType2 === 'all') ? 'selected' : ''; ?>>All</option>
<option value="1" <?php echo ($filterProdType2 === '1') ? 'selected' : ''; ?>>MRP</option>
<option value="2" <?php echo ($filterProdType2 === '2') ? 'selected' : ''; ?>>Making</option>
</select>
<div class="clearfix"></div>
</div>

<div class="form-group col-md-2">
<label class="form-label"> Date </label>
<input type="date" name="FromDate" id="FromDate" class="form-control" value="<?php echo $_REQUEST['FromDate'] ?>" autocomplete="off">
</div>
<div class="form-group col-md-2">
<label class="form-label">To Date</label>
<input type="date" name="ToDate" id="ToDate" class="form-control" value="<?php echo $_REQUEST['ToDate'] ?>" autocomplete="off">
</div>
<input type="hidden" name="Search" value="Search">
<div class="form-group col-md-1" style="padding-top:20px;">
<button type="submit" name="submit" class="btn btn-primary btn-finish">Search</button>
</div>
<!--<div class="col-md-1">
<label class="form-label d-none d-md-block">&nbsp;</label>
<button type="button" id="print" class="btn btn-success btn-finish" onClick=printReport('<?php echo $invoice_data;?>')>Print</button>
</div>-->
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
            <th>Product</th>
            <th>Product Type</th>
            <th>Sell Time</th>
            <th>MRP</th>
            <th>Total Qty</th>
            <th>Purchase Amount</th>
            <th>Sell Amount</th>
            <th>Profit Amount</th>
        </tr>
    </thead>
    <tbody>

<?php
$i = 1;

$sql = "
SELECT 
    tcid.ProdId,tc.ProdType2,

    CASE 
        WHEN tc.id IS NULL THEN 
            CONCAT('UNKNOWN PRODUCT (ID:', tcid.ProdId, ')')
        ELSE 
            CONCAT(tc.ProductName, ' (ID:', tcid.ProdId, ')')
    END AS ProductName,

    CASE 
        WHEN tc.id IS NULL THEN 1 ELSE 0
    END AS IsUnknown,

    IFNULL(tc.MinPrice, 0) AS MinPrice,
    IFNULL(tc.PurchasePrice, 0) AS PurchasePrice,

    SUM(tcid.Qty) AS TotQty,
    SUM(tcid.Total) AS SellAmount,

    MIN(
        COALESCE(tci.CreatedTime, tcid.CreatedTime)
    ) AS SellTime

FROM tbl_customer_invoice_details_2025 tcid

LEFT JOIN tbl_customer_invoice_2025 tci 
    ON tci.id = tcid.InvId
    AND tci.FrId = '$BillSoftFrId'

LEFT JOIN tbl_cust_products_2025 tc 
    ON tc.id = tcid.ProdId
    AND tc.ProdType = 0
    AND tc.checkstatus = 1
    AND tc.delete_flag = 0

WHERE tcid.FrId = '$BillSoftFrId'
";




if (!empty($_REQUEST['FromDate'])) {
    $sql .= " AND tcid.CreatedDate >= '".$_REQUEST['FromDate']."'";
}

if (!empty($_REQUEST['ToDate'])) {
    $sql .= " AND tcid.CreatedDate <= '".$_REQUEST['ToDate']."'";
}

if (!empty($_REQUEST['ProdId']) && $_REQUEST['ProdId'] != 'all') {
    $sql .= " AND tcid.ProdId = '".intval($_REQUEST['ProdId'])."'";
}

if ($filterProdType2 === '1') {
    $sql .= " AND tc.ProdType2 = 1";
} elseif ($filterProdType2 === '2') {
    $sql .= " AND tc.ProdType2 = 2";
}

$sql .= "
GROUP BY 
    tcid.ProdId,
    tc.ProductName,
    tc.MinPrice,
    tc.PurchasePrice
ORDER BY 
    IsUnknown DESC,
    ProductName ASC
";


 //echo $sql; // debug

$res = $conn->query($sql);

$grandTotQty = 0;
$grandPurchase = 0;
$grandSell = 0;
$grandProfit = 0;

while ($row = $res->fetch_assoc()) {
    
    $PurchaseAmount = $row['PurchasePrice'] * $row['TotQty'];
    $ProfitAmount   = $row['SellAmount'] - $PurchaseAmount;

    $grandTotQty += (float) $row['TotQty'];
    $grandPurchase += $PurchaseAmount;
    $grandSell += (float) $row['SellAmount'];
    $grandProfit += $ProfitAmount;

    $rowStyle = '';

    // Highlight unknown product
    /*$rowStyle = ($row['IsUnknown'] == 1) 
        ? "style='background:#ffe6e6;color:#b30000;font-weight:bold'" 
        : "";*/
        
        if($row['IsUnknown'] == 1){
        $sql33 = "SELECT ProductName FROM tbl_cust_products_2025 WHERE id='".$row['ProdId']."'";
        $row33 = getRecord($sql33);
        $ProductName = $row33['ProductName'];
        }
        else{
            $ProductName = $row['ProductName'];
        }
        
        if($row['ProdType2'] == 1){
            $ProdType = "MRP";
        }
        else{
           $ProdType = "Making"; 
        }
?>
<tr <?= $rowStyle; ?>>
    <td><?= $i++; ?></td>
    <td><?= $ProductName; ?></td>
    <td><?= $ProdType;?></td>
    <td><?= date("h:i A", strtotime($row['SellTime'])); ?></td>
    <td><?= number_format($row['MinPrice'],2); ?></td>
    <td><?= $row['TotQty']; ?></td>
    <td><?= number_format($PurchaseAmount,2); ?></td>
    <td><?= number_format($row['SellAmount'],2); ?></td>
    <td><?= number_format($ProfitAmount,2); ?></td>
</tr>
<?php } ?>


    </tbody>
    <tfoot>
        <tr class="font-weight-bold bg-light">
            <th colspan="5" class="text-right">Grand Total</th>
            <th><?= $grandTotQty; ?></th>
            <th><?= number_format($grandPurchase, 2); ?></th>
            <th><?= number_format($grandSell, 2); ?></th>
            <th><?= number_format($grandProfit, 2); ?></th>
        </tr>
    </tfoot>
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
<?php
$exFrom = !empty($_REQUEST['FromDate']) ? preg_replace('/[^0-9\-]/', '', (string) $_REQUEST['FromDate']) : 'all';
$exTo = !empty($_REQUEST['ToDate']) ? preg_replace('/[^0-9\-]/', '', (string) $_REQUEST['ToDate']) : 'all';
$excelFilename = 'Product_Wise_Sell_Report_' . $exFrom . '_to_' . $exTo;
if (!empty($_REQUEST['ProdId']) && $_REQUEST['ProdId'] !== 'all') {
    $excelFilename .= '_Prod' . intval($_REQUEST['ProdId']);
}
if ($filterProdType2 === '1') {
    $excelFilename .= '_MRP';
} elseif ($filterProdType2 === '2') {
    $excelFilename .= '_Making';
}
$excelMessageTop = 'Product Wise Sell Report';
if (!empty($_REQUEST['FromDate']) || !empty($_REQUEST['ToDate'])) {
    $excelMessageTop .= ' — From ' . (!empty($_REQUEST['FromDate']) ? preg_replace('/[^0-9\-]/', '', (string) $_REQUEST['FromDate']) : '—');
    $excelMessageTop .= ' to ' . (!empty($_REQUEST['ToDate']) ? preg_replace('/[^0-9\-]/', '', (string) $_REQUEST['ToDate']) : '—');
}
if (!empty($_REQUEST['ProdId']) && $_REQUEST['ProdId'] !== 'all') {
    $excelMessageTop .= ' — Product ID: ' . intval($_REQUEST['ProdId']);
} else {
    $excelMessageTop .= ' — All products';
}
if ($filterProdType2 === '1') {
    $excelMessageTop .= ' — Type: MRP';
} elseif ($filterProdType2 === '2') {
    $excelMessageTop .= ' — Type: Making';
} else {
    $excelMessageTop .= ' — Type: All';
}
?>
<script type="text/javascript">
   function printReport(invdata){
     console.log(invdata);
      Android.printReport(''+invdata+'');
 }
	$(document).ready(function() {
    var $prod = $('#ProdId');
    if ($prod.length) {
      if ($prod.hasClass('select2-hidden-accessible')) {
        $prod.select2('destroy');
      }
      if (!$prod.parent().hasClass('position-relative')) {
        $prod.wrap('<div class="position-relative" id="prodid-select2-wrap"></div>');
      }
      $prod.select2({
        width: '100%',
        placeholder: 'Search or select product',
        minimumResultsForSearch: 0,
        dropdownParent: $('#prodid-select2-wrap')
      });
    }

    $('#example').DataTable({
        "pageLength":100,
      "scrollX": true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Excel',
                title: 'Product Wise Sell Report',
                messageTop: <?php echo json_encode($excelMessageTop, JSON_UNESCAPED_UNICODE); ?>,
                filename: <?php echo json_encode($excelFilename, JSON_UNESCAPED_UNICODE); ?>,
                sheetName: 'SellByProduct',
                exportOptions: {
                    columns: ':visible',
                    footer: true
                }
            }
        ]
    });

    $(document).on("change", "#CustId", function(event) {
                var val = this.value;
                var action = "getInvoiceNos";
                $.ajax({
                    url: "ajax_files/ajax_dropdown.php",
                    method: "POST",
                    data: {
                        action: action,
                        id: val
                    },
                    success: function(data) {
                        $('#InvNo').html(data);
                       
                    }
                });

            });
});
</script>
</body>
</html>
