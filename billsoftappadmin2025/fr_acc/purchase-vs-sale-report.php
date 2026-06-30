<?php
session_start();
include_once 'config.php';
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = 'Report-2025';
$Page = 'Purchase-Vs-Sale-Report';

$user_id = $_SESSION['fr_admin'];
    $sql77 = "SELECT * FROM tbl_users_bill WHERE id='$user_id'";
	$row77 = getRecord($sql77);
    $Roll = $row77['Roll'];
    
    //$BillSoftFrId = $row77['BillSoftFrId'];
	
    if($Roll == 5){
        $BillSoftFrId = $_SESSION['fr_admin'];
    }
    else{
        $BillSoftFrId = $row77['BillSoftFrId'];
    }
    
$fromDate = isset($_REQUEST['FromDate']) ? trim((string) $_REQUEST['FromDate']) : '';
$toDate = isset($_REQUEST['ToDate']) ? trim((string) $_REQUEST['ToDate']) : '';
$prodId = isset($_REQUEST['ProdId']) ? (string) $_REQUEST['ProdId'] : 'all';
$searched = isset($_REQUEST['Search']) && $_REQUEST['Search'] !== '';

$frIdEsc = (int) $BillSoftFrId;
$dateSqlStock = '';
$dateSqlInvoice = '';
if ($fromDate !== '') {
    $fromEsc = $conn->real_escape_string($fromDate);
    $dateSqlStock .= " AND ts.StockDate >= '$fromEsc'";
    $dateSqlInvoice .= " AND tcid.CreatedDate >= '$fromEsc'";
}
if ($toDate !== '') {
    $toEsc = $conn->real_escape_string($toDate);
    $dateSqlStock .= " AND ts.StockDate <= '$toEsc'";
    $dateSqlInvoice .= " AND tcid.CreatedDate <= '$toEsc'";
}

$prodFilterSql = '';
if ($prodId !== '' && $prodId !== 'all') {
    $prodFilterSql = ' AND p.id = ' . (int) $prodId;
}

$openingStockSql = '';
if ($fromDate !== '') {
    $openingStockSql = " AND ts.StockDate < '$fromEsc'";
}

$stockQtyExpr = "CASE WHEN ts.Qty2 IS NOT NULL AND ts.Qty2 != '' THEN ts.Qty2 ELSE ts.Qty END";
?>
<!DOCTYPE html>
<html lang="en" class="default-style">
<head>
<title><?php echo $Proj_Title; ?> | Purchase Vs Sale Report</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="description" content="" />
<meta name="keywords" content="">
<meta name="author" content="" />
<?php include_once 'header_script.php'; ?>
<style>
.pvs-qty-link {
    color: #007bff;
    cursor: pointer;
    text-decoration: underline;
    font-weight: 600;
}
.pvs-qty-link:hover {
    color: #0056b3;
}
.pvs-qty-muted {
    color: #6c757d;
}
</style>
</head>
<body>

<div class="layout-wrapper layout-1 layout-without-sidenav">
<div class="layout-inner">

<?php include_once 'top_header.php'; include_once 'sidebar.php'; ?>

<div class="layout-container">

<div class="layout-content">

<div class="container-fluid flex-grow-1 container-p-y">
<h4 class="font-weight-bold py-3 mb-0">Purchase Vs Sale Report</h4>
<p class="text-muted mb-0">Balance Qty = Opening Stock Qty + Total Purchase Qty − Total Sale Qty (within selected date range).</p>
<br>

<div class="card">
<div id="accordion2">
<div class="card mb-2">
<div id="accordion2-2" class="collapse show" data-parent="#accordion2">
<div class="" style="padding: 5px;padding-left: 20px;">
<form id="validation-form" method="post" enctype="multipart/form-data" action="">
<div class="form-row">

<div class="form-group col-md-4">
<label class="form-label">Product</label>
<select class="select2-demo form-control" name="ProdId" id="ProdId">
<option value="all" <?php echo ($prodId === 'all') ? 'selected' : ''; ?>>All Products</option>
<?php
$sqlProducts = "SELECT id, ProductName FROM tbl_cust_products_2025
    WHERE CreatedBy='$frIdEsc' AND ProdType=0 AND checkstatus=1 AND delete_flag=0
    ORDER BY ProductName";
$productRows = getList($sqlProducts);
if (is_array($productRows)) {
    foreach ($productRows as $productRow) {
        $pid = (int) $productRow['id'];
        ?>
<option value="<?php echo $pid; ?>" <?php echo ((string) $prodId === (string) $pid) ? 'selected' : ''; ?>><?php echo htmlspecialchars($productRow['ProductName']); ?></option>
        <?php
    }
}
?>
</select>
<div class="clearfix"></div>
</div>

<div class="form-group col-md-2">
<label class="form-label">From Date</label>
<input type="date" name="FromDate" id="FromDate" class="form-control" value="<?php echo htmlspecialchars($fromDate, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
</div>
<div class="form-group col-md-2">
<label class="form-label">To Date</label>
<input type="date" name="ToDate" id="ToDate" class="form-control" value="<?php echo htmlspecialchars($toDate, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
</div>
<input type="hidden" name="Search" value="Search">
<div class="form-group col-md-1" style="padding-top:20px;">
<button type="submit" name="submit" class="btn btn-primary btn-finish">Search</button>
</div>
<?php if ($searched) { ?>
<div class="col-md-1">
<label class="form-label d-none d-md-block">&nbsp;</label>
<a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-info btn-block" data-toggle="tooltip" data-placement="top" data-original-title="Clear Filter">X</a>
</div>
<?php } ?>
</div>
</form>
</div>
</div>
</div>
</div>

<?php if ($searched) { ?>
<div class="card-datatable table-responsive">
<table id="example" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>Product Id</th>
            <th>Product Name</th>
            <th>Barcode No</th>
            <th>Opening Stock Qty</th>
            <th>Total Purchase Qty</th>
            <th>Total Purchase Price</th>
            <th>Total Sale Qty</th>
            <th>Total Sale Amount</th>
            <th>Balance Qty</th>
        </tr>
    </thead>
    <tbody>
<?php
$sql = "SELECT
    p.id AS ProdId,
    p.ProductName,
    p.BarcodeNo,
    COALESCE(opn.OpeningQty, 0) AS OpeningQty,
    COALESCE(pur.PurchaseQty, 0) AS PurchaseQty,
    COALESCE(pur.PurchaseAmount, 0) AS PurchaseAmount,
    COALESCE(inv.SaleQty, 0) AS SaleQty,
    COALESCE(inv.SaleAmount, 0) AS SaleAmount
FROM tbl_cust_products_2025 p
LEFT JOIN (
    SELECT ts.ProdId,
           COALESCE(SUM(CASE WHEN ts.Status='Cr' THEN ($stockQtyExpr) ELSE 0 END), 0)
           - COALESCE(SUM(CASE WHEN ts.Status='Dr' THEN ts.Qty ELSE 0 END), 0) AS OpeningQty
    FROM tbl_cust_prod_stock_2025 ts
    WHERE ts.FrId='$frIdEsc' AND ts.ProdType=0 $openingStockSql
    GROUP BY ts.ProdId
) opn ON opn.ProdId = p.id
LEFT JOIN (
    SELECT ts.ProdId,
           SUM($stockQtyExpr) AS PurchaseQty,
           SUM(($stockQtyExpr) * COALESCE(ts.PurchasePrice, 0)) AS PurchaseAmount
    FROM tbl_cust_prod_stock_2025 ts
    WHERE ts.FrId='$frIdEsc' AND ts.ProdType=0 AND ts.Status='Cr' $dateSqlStock
    GROUP BY ts.ProdId
) pur ON pur.ProdId = p.id
LEFT JOIN (
    SELECT tcid.ProdId,
           SUM(tcid.Qty) AS SaleQty,
           SUM(tcid.Total) AS SaleAmount
    FROM tbl_customer_invoice_details_2025 tcid
    INNER JOIN tbl_customer_invoice_2025 tci ON tci.id = tcid.InvId AND tci.FrId='$frIdEsc'
    WHERE tcid.FrId='$frIdEsc' $dateSqlInvoice
    GROUP BY tcid.ProdId
) inv ON inv.ProdId = p.id
WHERE p.CreatedBy='$frIdEsc' AND p.ProdType=0 AND p.checkstatus=1 AND p.delete_flag=0
      $prodFilterSql";
if ($prodId === '' || $prodId === 'all') {
    $sql .= "
HAVING (OpeningQty != 0 OR PurchaseQty > 0 OR SaleQty > 0 OR SaleAmount > 0)";
}
$sql .= "
ORDER BY p.ProductName ASC";

$res = $conn->query($sql);
$grandOpeningQty = 0;
$grandPurchaseQty = 0;
$grandPurchaseAmt = 0;
$grandSaleQty = 0;
$grandSaleAmt = 0;
$grandBalanceQty = 0;

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $openingQty = (float) $row['OpeningQty'];
        $purchaseQty = (float) $row['PurchaseQty'];
        $purchaseAmt = (float) $row['PurchaseAmount'];
        $saleQty = (float) $row['SaleQty'];
        $saleAmt = (float) $row['SaleAmount'];
        $balanceQty = $openingQty + $purchaseQty - $saleQty;

        $grandOpeningQty += $openingQty;
        $grandPurchaseQty += $purchaseQty;
        $grandPurchaseAmt += $purchaseAmt;
        $grandSaleQty += $saleQty;
        $grandSaleAmt += $saleAmt;
        $grandBalanceQty += $balanceQty;
        ?>
        <tr>
            <td><?php echo (int) $row['ProdId']; ?></td>
            <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
            <td><?php echo htmlspecialchars((string) ($row['BarcodeNo'] ?? '')); ?></td>
            <td><?php echo number_format($openingQty, 2); ?></td>
            <td>
                <?php if ($purchaseQty > 0) { ?>
                <span class="pvs-qty-link js-pvs-purchase-qty"
                    data-prod-id="<?php echo (int) $row['ProdId']; ?>"
                    data-product-name="<?php echo htmlspecialchars($row['ProductName'], ENT_QUOTES, 'UTF-8'); ?>"
                    title="View purchase details"><?php echo number_format($purchaseQty, 2); ?></span>
                <?php } else { ?>
                <span class="pvs-qty-muted"><?php echo number_format($purchaseQty, 2); ?></span>
                <?php } ?>
            </td>
            <td>&#8377;<?php echo number_format($purchaseAmt, 2); ?></td>
            <td>
                <?php if ($saleQty > 0) { ?>
                <span class="pvs-qty-link js-pvs-sale-qty"
                    data-prod-id="<?php echo (int) $row['ProdId']; ?>"
                    data-product-name="<?php echo htmlspecialchars($row['ProductName'], ENT_QUOTES, 'UTF-8'); ?>"
                    title="View sale details"><?php echo number_format($saleQty, 2); ?></span>
                <?php } else { ?>
                <span class="pvs-qty-muted"><?php echo number_format($saleQty, 2); ?></span>
                <?php } ?>
            </td>
            <td>&#8377;<?php echo number_format($saleAmt, 2); ?></td>
            <td><?php echo number_format($balanceQty, 2); ?></td>
        </tr>
        <?php
    }
}
?>
    </tbody>
    <tfoot>
        <tr class="font-weight-bold bg-light">
            <th colspan="3" class="text-right">Grand Total</th>
            <th><?php echo number_format($grandOpeningQty, 2); ?></th>
            <th><?php echo number_format($grandPurchaseQty, 2); ?></th>
            <th>&#8377;<?php echo number_format($grandPurchaseAmt, 2); ?></th>
            <th><?php echo number_format($grandSaleQty, 2); ?></th>
            <th>&#8377;<?php echo number_format($grandSaleAmt, 2); ?></th>
            <th><?php echo number_format($grandBalanceQty, 2); ?></th>
        </tr>
    </tfoot>
</table>
</div>
<?php } ?>
</div>
</div>

<div class="modal fade" id="pvsDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title" id="pvsDetailModalTitle">Details</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
<div id="pvsDetailLoading" class="text-center py-4" style="display:none;">Loading...</div>
<div class="table-responsive">
<table class="table table-striped table-bordered mb-0" id="pvsDetailTable">
<thead id="pvsDetailHead"></thead>
<tbody id="pvsDetailBody"></tbody>
<tfoot id="pvsDetailFoot"></tfoot>
</table>
</div>
<div id="pvsDetailEmpty" class="text-muted text-center py-3" style="display:none;">No records found.</div>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

<input type="hidden" id="pvsFilterFromDate" value="<?php echo htmlspecialchars($fromDate, ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" id="pvsFilterToDate" value="<?php echo htmlspecialchars($toDate, ENT_QUOTES, 'UTF-8'); ?>">

<?php include_once 'footer.php'; ?>

</div>
</div>
</div>

<div class="layout-overlay layout-sidenav-toggle"></div>
</div>

<?php include_once 'footer_script.php'; ?>
<script type="text/javascript">
function pvsFmtMoney(val) {
    return '\u20B9' + parseFloat(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function pvsResetDetailModal() {
    $('#pvsDetailHead').empty();
    $('#pvsDetailBody').empty();
    $('#pvsDetailFoot').empty();
    $('#pvsDetailEmpty').hide();
    $('#pvsDetailTable').show();
}

function pvsRenderPurchaseRows(rows, totals) {
    var head = '<tr><th>Expense Id</th><th>Date</th><th>Qty</th><th>Price</th><th>Total Price</th></tr>';
    var body = '';
    rows.forEach(function(row) {
        body += '<tr>'
            + '<td>' + $('<div>').text(row.expense_id || '-').html() + '</td>'
            + '<td>' + $('<div>').text(row.date || '-').html() + '</td>'
            + '<td>' + row.qty + '</td>'
            + '<td>' + pvsFmtMoney(row.price) + '</td>'
            + '<td>' + pvsFmtMoney(row.total_price) + '</td>'
            + '</tr>';
    });
    var totQty = totals && totals.total_qty ? parseFloat(totals.total_qty) : 0;
    var totAmt = totals && totals.total_amount ? parseFloat(totals.total_amount) : 0;
    var foot = '<tr class="font-weight-bold bg-light"><th colspan="2" class="text-right">Total (date filter)</th>'
        + '<th>' + totQty.toFixed(2) + '</th><th></th><th>' + pvsFmtMoney(totAmt) + '</th></tr>';
    $('#pvsDetailHead').html(head);
    $('#pvsDetailBody').html(body);
    $('#pvsDetailFoot').html(foot);
}

function pvsRenderSaleRows(rows, totals) {
    var head = '<tr><th>Invoice No</th><th>Date</th><th>Qty</th><th>Price</th><th>Total Price</th></tr>';
    var body = '';
    rows.forEach(function(row) {
        body += '<tr>'
            + '<td>' + $('<div>').text(row.invoice_no || '-').html() + '</td>'
            + '<td>' + $('<div>').text(row.date || '-').html() + '</td>'
            + '<td>' + row.qty + '</td>'
            + '<td>' + pvsFmtMoney(row.price) + '</td>'
            + '<td>' + pvsFmtMoney(row.total_price) + '</td>'
            + '</tr>';
    });
    var totQty = totals && totals.total_qty ? parseFloat(totals.total_qty) : 0;
    var totAmt = totals && totals.total_amount ? parseFloat(totals.total_amount) : 0;
    var foot = '<tr class="font-weight-bold bg-light"><th colspan="2" class="text-right">Total (date filter)</th>'
        + '<th>' + totQty.toFixed(2) + '</th><th></th><th>' + pvsFmtMoney(totAmt) + '</th></tr>';
    $('#pvsDetailHead').html(head);
    $('#pvsDetailBody').html(body);
    $('#pvsDetailFoot').html(foot);
}

function pvsOpenDetailModal(action, prodId, productName) {
    pvsResetDetailModal();
    var title = action === 'purchase_details' ? 'Purchase Details' : 'Sale Details';
    $('#pvsDetailModalTitle').text(title + ' - ' + productName);
    $('#pvsDetailLoading').show();
    $('#pvsDetailModal').modal('show');

    $.ajax({
        url: 'ajax_files/ajax_purchase_vs_sale_report.php',
        method: 'POST',
        dataType: 'json',
        data: {
            action: action,
            prodId: prodId,
            fromDate: $('#pvsFilterFromDate').val() || '',
            toDate: $('#pvsFilterToDate').val() || ''
        }
    }).done(function(resp) {
        $('#pvsDetailLoading').hide();
        if (!resp || !resp.ok) {
            $('#pvsDetailTable').hide();
            $('#pvsDetailEmpty').text('Unable to load details.').show();
            return;
        }
        if (!resp.rows || resp.rows.length < 1) {
            $('#pvsDetailTable').hide();
            $('#pvsDetailEmpty').show();
            return;
        }
        if (action === 'purchase_details') {
            pvsRenderPurchaseRows(resp.rows, resp);
        } else {
            pvsRenderSaleRows(resp.rows, resp);
        }
    }).fail(function() {
        $('#pvsDetailLoading').hide();
        $('#pvsDetailTable').hide();
        $('#pvsDetailEmpty').text('Unable to load details.').show();
    });
}

$(document).ready(function() {
    if ($('#example').length) {
        $('#example').DataTable({
            dom: 'Bfrtip',
            buttons: ['excelHtml5'],
            order: [[1, 'asc']]
        });
    }

    $(document).on('click', '.js-pvs-purchase-qty', function() {
        pvsOpenDetailModal('purchase_details', $(this).data('prod-id'), $(this).data('product-name'));
    });

    $(document).on('click', '.js-pvs-sale-qty', function() {
        pvsOpenDetailModal('sale_details', $(this).data('prod-id'), $(this).data('product-name'));
    });
});
</script>
</body>
</html>
