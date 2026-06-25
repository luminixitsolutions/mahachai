<?php
session_start();
include_once '../config.php';
include_once '../auth.php';

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    echo '<div class="alert alert-danger mb-0">Invalid expense ID.</div>';
    exit;
}

function vedFmtDate($date)
{
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '—';
    }
    return date('d/m/Y', strtotime($date));
}

function vedApprovalStatus($status, $name, $pendingLabel)
{
    if ($status == '1') {
        return '<span class="text-success font-weight-bold">Approved' . ($name ? ' by ' . htmlspecialchars($name) : '') . '</span>';
    }
    if ($status == '2') {
        return '<span class="text-danger font-weight-bold">Rejected' . ($name ? ' by ' . htmlspecialchars($name) : '') . '</span>';
    }
    return '<span class="text-warning font-weight-bold">' . htmlspecialchars($pendingLabel) . '</span>';
}

$sql = "SELECT te.*,
        tu.Fname, tu.Lname,
        tu_ved.Fname AS VedName, tu_ved.Phone AS VedPhone, tu_ved.TrustedVendor, tu_ved.TradeName,
        tu2.Fname AS MgrName,
        tu4.Fname AS BdmName,
        tu5.Fname AS PurchaseName,
        tu6.Fname AS AdminName,
        tu7.Fname AS PayByName,
        tub.ShopName,
        cm.Name AS TypeOfVendorName
    FROM tbl_vendor_expenses te
    LEFT JOIN tbl_users tu ON tu.id = te.UserId
    LEFT JOIN tbl_users tu_ved ON tu_ved.id = te.VedId
    LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
    LEFT JOIN tbl_users tu4 ON tu4.id = te.BdmBy
    LEFT JOIN tbl_users tu5 ON tu5.id = te.PurchaseBy
    LEFT JOIN tbl_users tu6 ON tu6.id = te.AccBy
    LEFT JOIN tbl_users tu7 ON tu7.id = te.PayBy
    LEFT JOIN tbl_users_bill tub ON tub.id = te.Locations
    LEFT JOIN tbl_common_master cm ON cm.id = te.TypeOfVendor
    WHERE te.id = '$id'";

$row = getRecord($sql);
if (empty($row)) {
    echo '<div class="alert alert-warning mb-0">Expense not found.</div>';
    exit;
}

$created = strtotime($row['CreatedDate']);
$expense = strtotime($row['ExpenseDate']);
$diffDays = ($created && $expense) ? round(($created - $expense) / (60 * 60 * 24)) : '—';

$NewProd = $row['NewProd'];
if ($NewProd == 'Yes') {
    $sqlItems = "SELECT tve.*, tcp.ProductName
        FROM tbl_ved_expense_items tve
        INNER JOIN tbl_cust_products_2025 tcp ON tcp.id = tve.ProdId
        WHERE tve.ExpId = '$id'";
} else {
    $sqlItems = "SELECT tve.*, tcp.ProductName
        FROM tbl_ved_expense_items tve
        INNER JOIN tbl_cust_products2 tcp ON tcp.id = tve.MainProdId
        WHERE tve.ExpId = '$id'";
}
$items = getList($sqlItems);
$grandTotal = 0;
$uploadDir = dirname(__DIR__) . '/../uploads/vendor_expense/';
$uploadUrl = '../uploads/vendor_expense/';
?>

<style>
.ved-detail-section { margin-bottom: 1.25rem; }
.ved-detail-section h6 {
    font-weight: 700;
    color: #4e73df;
    border-bottom: 2px solid #e3e6f0;
    padding-bottom: 6px;
    margin-bottom: 12px;
}
.ved-detail-table th { background: #f8f9fc; width: 35%; font-weight: 600; }
.ved-detail-table td, .ved-detail-table th { padding: 8px 12px; vertical-align: top; }
.ved-approval-table th { background: #f8f9fc; }
.ved-approval-table td, .ved-approval-table th { padding: 8px 12px; vertical-align: middle; }
</style>

<div class="ved-detail-section">
    <h6>Expense Information</h6>
    <table class="table table-bordered table-sm ved-detail-table mb-0">
        <tr><th>Expense ID</th><td><?= (int) $row['id'] ?></td></tr>
        <tr><th>Uploaded By</th><td><?= htmlspecialchars(trim($row['Fname'] . ' ' . $row['Lname'])) ?></td></tr>
        <tr><th>Upload Date</th><td><?= vedFmtDate($row['CreatedDate']) ?></td></tr>
        <tr><th>Expense / Invoice Date</th><td><?= vedFmtDate($row['ExpenseDate']) ?></td></tr>
        <tr><th>Difference of Days</th><td><?= $diffDays ?></td></tr>
        <tr><th>Location</th><td><?= htmlspecialchars($row['ShopName'] ?: '—') ?></td></tr>
        <tr><th>Invoice No</th><td><?= htmlspecialchars($row['InvoiceNo'] ?: '—') ?></td></tr>
        <tr><th>PO No</th><td><?= htmlspecialchars($row['PoNo'] ?: '—') ?></td></tr>
        <tr><th>Invoice Amount</th><td><strong>₹ <?= number_format((float) $row['Amount'], 2) ?></strong></td></tr>
        <tr><th>Payment Type</th><td><?= htmlspecialchars($row['PaymentMode'] ?: '—') ?></td></tr>
        <tr><th>Advance Amount</th><td><?= $row['AdvAmount'] !== '' && $row['AdvAmount'] !== null ? '₹ ' . number_format((float) $row['AdvAmount'], 2) : '—' ?></td></tr>
        <tr><th>Type Of Invoice</th><td><?= htmlspecialchars($row['InvType'] ?: '—') ?></td></tr>
        <tr><th>Narration</th><td><?= nl2br(htmlspecialchars($row['Narration'] ?: '—')) ?></td></tr>
        <tr><th>UTR No.</th><td><?= htmlspecialchars($row['UtrNo'] ?: '—') ?></td></tr>
    </table>
</div>

<div class="ved-detail-section">
    <h6>Vendor Information</h6>
    <table class="table table-bordered table-sm ved-detail-table mb-0">
        <tr><th>Trade / Business Name</th><td><?= htmlspecialchars($row['TradeName'] ?: '—') ?></td></tr>
        <tr><th>Vendor Name</th><td><?= htmlspecialchars($row['VedName'] ?: '—') ?></td></tr>
        <tr><th>Vendor Mobile</th><td><?= htmlspecialchars($row['VedPhone'] ?: '—') ?></td></tr>
        <tr><th>Type Of Vendor</th><td><?= htmlspecialchars($row['TypeOfVendorName'] ?: '—') ?></td></tr>
        <tr><th>Trusted Vendor</th><td><?= htmlspecialchars($row['TrustedVendor'] ?: '—') ?></td></tr>
    </table>
</div>

<?php if (!empty($items)) { ?>
<div class="ved-detail-section">
    <h6>Products / Items</h6>
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Sr.No</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Purchase Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($items as $item) {
                    $lineTotal = (float) $item['Qty2'] * (float) $item['PurchasePrice'];
                    $grandTotal += $lineTotal;
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= htmlspecialchars($item['ProductName']) ?></td>
                    <td><?= htmlspecialchars($item['Qty2'] . ' ' . $item['Unit2']) ?></td>
                    <td>₹ <?= number_format((float) $item['PurchasePrice'], 2) ?></td>
                    <td>₹ <?= number_format($lineTotal, 2) ?></td>
                </tr>
                <?php $i++; } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Grand Total</th>
                    <th>₹ <?= number_format($grandTotal, 2) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php } ?>

<div class="ved-detail-section">
    <h6>Approval History</h6>
    <div class="table-responsive">
        <table class="table table-bordered table-sm ved-approval-table mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Stage</th>
                    <th>Status</th>
                    <th>Approved Amount</th>
                    <th>Date</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>BDM</th>
                    <?php if ($row['Locations'] == '702') { ?>
                    <td colspan="4" class="text-danger">BDM Approval Not Required</td>
                    <?php } else { ?>
                    <td><?= vedApprovalStatus($row['BdmStatus'], $row['BdmName'], 'Pending By BDM') ?></td>
                    <td><?= $row['BdmAmount'] !== '' && $row['BdmAmount'] !== null ? '₹ ' . number_format((float) $row['BdmAmount'], 2) : '—' ?></td>
                    <td><?= vedFmtDate($row['BdmApproveDate']) ?></td>
                    <td><?= htmlspecialchars($row['BdmComment'] ?: '—') ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <th>Purchase Dept</th>
                    <?php if ($row['TrustedVendor'] == 'Yes') { ?>
                    <td colspan="4" class="text-danger">Approval Not Required (Trusted Vendor)</td>
                    <?php } else { ?>
                    <td><?= vedApprovalStatus($row['PurchaseStatus'], $row['PurchaseName'], 'Pending By Purchase Dept') ?></td>
                    <td><?= $row['PurchaseAmount'] !== '' && $row['PurchaseAmount'] !== null ? '₹ ' . number_format((float) $row['PurchaseAmount'], 2) : '—' ?></td>
                    <td><?= vedFmtDate($row['PurchaseApproveDate']) ?></td>
                    <td><?= htmlspecialchars($row['PurchaseComment'] ?: '—') ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <th>Accountant</th>
                    <td><?= vedApprovalStatus($row['ManagerStatus'], $row['MgrName'], 'Pending By Accountant') ?></td>
                    <td><?= $row['MgrAmount'] !== '' && $row['MgrAmount'] !== null ? '₹ ' . number_format((float) $row['MgrAmount'], 2) : '—' ?></td>
                    <td><?= vedFmtDate($row['ApproveDate']) ?></td>
                    <td><?= htmlspecialchars($row['MannagerComment'] ?: '—') ?></td>
                </tr>
                <tr>
                    <th>Admin</th>
                    <?php if ($row['TrustedVendor'] == 'Yes') { ?>
                    <td colspan="4" class="text-danger">Approval Not Required (Trusted Vendor)</td>
                    <?php } else { ?>
                    <td><?= vedApprovalStatus($row['AdminStatus'], $row['AdminName'], ($row['Amount'] <= 2000 ? 'Pending By Anup Asawani' : 'Pending By Admin')) ?></td>
                    <td><?= $row['AccAmount'] !== '' && $row['AccAmount'] !== null ? '₹ ' . number_format((float) $row['AccAmount'], 2) : '—' ?></td>
                    <td><?= vedFmtDate($row['AdminApproveDate']) ?></td>
                    <td><?= htmlspecialchars($row['AdminComment'] ?: '—') ?></td>
                    <?php } ?>
                </tr>
                <tr>
                    <th>Payment</th>
                    <td>
                        <?php if ($row['PaymentStatus'] == '1') { ?>
                        <span class="text-success font-weight-bold">Payment Done<?= $row['PayByName'] ? ' by ' . htmlspecialchars($row['PayByName']) : '' ?></span>
                        <?php } else { ?>
                        <span class="text-warning font-weight-bold">Payment Pending</span>
                        <?php } ?>
                    </td>
                    <td>₹ <?= number_format((float) $row['Amount'], 2) ?></td>
                    <td><?= vedFmtDate($row['PaymentDate']) ?></td>
                    <td>—</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="ved-detail-section">
    <h6>Receipts</h6>
    <div class="row">
        <div class="col-md-6 mb-2">
            <strong>Invoice / Receipt</strong><br>
            <?php if (!empty($row['Photo']) && file_exists($uploadDir . $row['Photo'])) { ?>
            <a href="<?= $uploadUrl . htmlspecialchars($row['Photo']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-1">View Receipt</a>
            <?php } else { ?>
            <span class="text-danger">No Receipt Found</span>
            <?php } ?>
        </div>
        <div class="col-md-6 mb-2">
            <strong>Payment Receipt</strong><br>
            <?php if (!empty($row['Photo2']) && file_exists($uploadDir . $row['Photo2'])) { ?>
            <a href="<?= $uploadUrl . htmlspecialchars($row['Photo2']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-1">View Payment Receipt</a>
            <?php } else { ?>
            <span class="text-danger">No Receipt Found</span>
            <?php } ?>
        </div>
    </div>
</div>
