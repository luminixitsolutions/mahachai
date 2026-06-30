<?php
session_start();
include_once '../config.php';
include_once '../auth.php';

header('Content-Type: application/json; charset=utf-8');

$user_id = $_SESSION['fr_admin'];
$sql77 = "SELECT * FROM tbl_users_bill WHERE id='$user_id'";
$row77 = getRecord($sql77);
$Roll = $row77['Roll'];
if ($Roll == 5) {
    $BillSoftFrId = $_SESSION['fr_admin'];
} else {
    $BillSoftFrId = $row77['BillSoftFrId'];
}

$action = isset($_POST['action']) ? (string) $_POST['action'] : '';
$prodId = isset($_POST['prodId']) ? (int) $_POST['prodId'] : 0;
$fromDate = isset($_POST['fromDate']) ? trim((string) $_POST['fromDate']) : '';
$toDate = isset($_POST['toDate']) ? trim((string) $_POST['toDate']) : '';
$frIdEsc = (int) $BillSoftFrId;

if ($prodId <= 0 || ($action !== 'purchase_details' && $action !== 'sale_details')) {
    echo json_encode(array('ok' => false, 'message' => 'Invalid request'));
    exit;
}

$stockQtyExpr = "CASE WHEN ts.Qty2 IS NOT NULL AND ts.Qty2 != '' THEN ts.Qty2 ELSE ts.Qty END";
$rows = array();
$totalQty = 0;
$totalAmount = 0;

if ($action === 'purchase_details') {
    $sql = "SELECT
        CASE
            WHEN ts.VedExpItem = 'Yes' AND ts.VedExpId IS NOT NULL AND ts.VedExpId != '' AND ts.VedExpId != 0 THEN ts.VedExpId
            WHEN ts.EmpExpItem = 'Yes' AND ts.EmpExpId IS NOT NULL AND ts.EmpExpId != '' AND ts.EmpExpId != 0 THEN ts.EmpExpId
            ELSE '-'
        END AS expense_id,
        ts.StockDate AS row_date,
        ($stockQtyExpr) AS qty,
        COALESCE(ts.PurchasePrice, 0) AS price,
        (($stockQtyExpr) * COALESCE(ts.PurchasePrice, 0)) AS total_price
    FROM tbl_cust_prod_stock_2025 ts
    WHERE ts.FrId='$frIdEsc' AND ts.ProdId='$prodId' AND ts.ProdType=0 AND ts.Status='Cr'";

    if ($fromDate !== '') {
        $sql .= " AND ts.StockDate >= '" . $conn->real_escape_string($fromDate) . "'";
    }
    if ($toDate !== '') {
        $sql .= " AND ts.StockDate <= '" . $conn->real_escape_string($toDate) . "'";
    }
    $sql .= ' ORDER BY ts.StockDate DESC, ts.id DESC';

    $res = $conn->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $dateStr = '';
            if (!empty($row['row_date']) && $row['row_date'] !== '0000-00-00') {
                $dateStr = date('d/m/Y', strtotime(str_replace('-', '/', $row['row_date'])));
            }
            $qtyVal = (float) $row['qty'];
            $amtVal = (float) $row['total_price'];
            $totalQty += $qtyVal;
            $totalAmount += $amtVal;
            $rows[] = array(
                'expense_id' => (string) $row['expense_id'],
                'date' => $dateStr,
                'qty' => number_format($qtyVal, 2, '.', ''),
                'price' => number_format((float) $row['price'], 2, '.', ''),
                'total_price' => number_format($amtVal, 2, '.', ''),
            );
        }
    }
}

if ($action === 'sale_details') {
    $sql = "SELECT
        COALESCE(NULLIF(tci.InvoiceNo, ''), CAST(tci.id AS CHAR)) AS invoice_no,
        COALESCE(NULLIF(tci.InvoiceDate, ''), tcid.CreatedDate) AS row_date,
        tcid.Qty AS qty,
        COALESCE(tcid.Price, 0) AS price,
        COALESCE(tcid.Total, 0) AS total_price
    FROM tbl_customer_invoice_details_2025 tcid
    INNER JOIN tbl_customer_invoice_2025 tci ON tci.id = tcid.InvId AND tci.FrId='$frIdEsc'
    WHERE tcid.FrId='$frIdEsc' AND tcid.ProdId='$prodId'";

    if ($fromDate !== '') {
        $sql .= " AND tcid.CreatedDate >= '" . $conn->real_escape_string($fromDate) . "'";
    }
    if ($toDate !== '') {
        $sql .= " AND tcid.CreatedDate <= '" . $conn->real_escape_string($toDate) . "'";
    }
    $sql .= ' ORDER BY tcid.CreatedDate DESC, tcid.id DESC';

    $res = $conn->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $dateStr = '';
            if (!empty($row['row_date']) && $row['row_date'] !== '0000-00-00') {
                $dateStr = date('d/m/Y', strtotime(str_replace('-', '/', $row['row_date'])));
            }
            $qtyVal = (float) $row['qty'];
            $amtVal = (float) $row['total_price'];
            $totalQty += $qtyVal;
            $totalAmount += $amtVal;
            $rows[] = array(
                'invoice_no' => (string) $row['invoice_no'],
                'date' => $dateStr,
                'qty' => number_format($qtyVal, 2, '.', ''),
                'price' => number_format((float) $row['price'], 2, '.', ''),
                'total_price' => number_format($amtVal, 2, '.', ''),
            );
        }
    }
}

echo json_encode(array(
    'ok' => true,
    'rows' => $rows,
    'count' => count($rows),
    'total_qty' => number_format($totalQty, 2, '.', ''),
    'total_amount' => number_format($totalAmount, 2, '.', ''),
));
