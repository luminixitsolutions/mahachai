<?php
session_start();
include_once 'config.php';
include_once 'auth.php';
include_once 'includes/all-requests-view-helpers.php';
include_once 'includes/all-requests-sql-filters.php';
include_once 'includes/all-expenses-menu-registry.php';
include_once 'includes/all-expenses-view-data.php';
require_once __DIR__ . '/admin-sidebar-menu-helpers.php';

$user_id = $_SESSION['Admin']['id'];
$registry = maha_ae_menu_registry();
$type = isset($_GET['type']) ? trim((string) $_GET['type']) : '';
$mode = isset($_GET['mode']) ? trim((string) $_GET['mode']) : 'all';

if (!isset($registry[$type])) {
    header('HTTP/1.0 404 Not Found');
    echo 'Invalid request type.';
    exit;
}

$cfg = $registry[$type];
if (!sb_has_opt($cfg['perms'])) {
    header('Location: index.php');
    exit;
}

if ($type === 'outlet_closure') {
    $url = $cfg['pages'][$mode] ?? $cfg['pages']['all'];
    header('Location: ' . $url);
    exit;
}

if ($type !== 'penalty' && $type !== 'hiring' && $mode !== 'all') {
    $url = $cfg['pages'][$mode] ?? '';
    if ($url !== '' && strpos($url, 'all-expenses-view.php') === false) {
        header('Location: ' . $url);
        exit;
    }
}

$MainPage = 'All-Expenses';
$Page = $cfg['page_ids'][$mode] ?? 'All-Expenses-View';
$allRequestsDefaultToday = ($mode === 'all');
$allRequestsRequireSearch = false;
$title = $cfg['label'] . ' — ' . maha_ae_mode_label($mode);
?>
<!DOCTYPE html>
<html lang="en" class="default-style">
<head>
<title><?php echo htmlspecialchars($Proj_Title . ' — ' . $title, ENT_QUOTES, 'UTF-8'); ?></title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<?php include_once 'header_script.php'; ?>
<?php maha_ar_pretty_cash_request_table_styles(); ?>
</head>
<body>

<div class="layout-wrapper layout-1 layout-without-sidenav">
<div class="layout-inner">
<?php include_once 'top_header.php'; include_once 'sidebar.php'; ?>
<div class="layout-container">
<div class="layout-content">
<div class="container-fluid flex-grow-1 container-p-y">
<h4 class="font-weight-bold py-3 mb-0"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h4>

<div class="card" style="padding:10px;">
<?php
if (in_array($type, array('employee_expense', 'petty_cash', 'vendor_expense', 'nso_vendor_expense', 'advance', 'attendance', 'resign'), true)) {
    include_once 'includes/all-requests-date-filter.php';
}
?>
<?php if (!isset($arfShowTable) || $arfShowTable) { ?>
<div class="card-datatable table-responsive maha-wide-dt-wrap">
<table id="example" class="table table-striped table-bordered maha-pc-request-table" style="width:100%">
<?php maha_ae_render_list($conn, $type, $mode); ?>
</table>
</div>
<?php } ?>
</div>
</div>

<?php include_once 'footer.php'; ?>
</div>
</div>
</div>
<div class="layout-overlay layout-sidenav-toggle"></div>
</div>

<?php include_once 'footer_script.php'; ?>
<?php if (!isset($arfShowTable) || $arfShowTable) { ?>
<script type="text/javascript">
$(document).ready(function() {
    if ($('#example').length && $.fn.dataTable) {
        $('#example').DataTable({
            autoWidth: false,
            scrollX: false,
            dom: 'Bfrtip',
            order: [[0, 'desc']],
            buttons: [{
                extend: 'excelHtml5',
                title: <?php echo json_encode(str_replace(' ', '_', $title)); ?>,
                exportOptions: { columns: ':visible' }
            }],
            language: { emptyTable: 'No records found.' }
        });
    }
});
</script>
<?php } ?>
</body>
</html>
