<?php 
session_start();
include_once 'config.php';
include_once 'auth.php';
require_once __DIR__ . '/includes/ho-expense-acted-list-helpers.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "HO-Admin-Expenses";
$Page = "HO-Admin-Approve-Expense-Request";
?>
<!DOCTYPE html>
<html lang="en" class="default-style">
<head>
<title><?php echo $Proj_Title; ?> </title>
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
<h4 class="font-weight-bold py-3 mb-0">Approve expense (your approvals)</h4>

<div class="card" style="padding: 10px;">
<div class="card-datatable table-responsive maha-wide-dt-wrap">
<table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Expense Id</th>
                <th>Level 1 Approval</th>
                <th>Level 2 Approval</th>
                <th>Level 3 Approval</th>
                <th>Expense Date</th>
                <th>Photo</th>
                <th>Employee Name</th>
                <th>Amount</th>
                <th>Narration</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = ho_expense_acted_select_sql();
            $sql .= " WHERE te.UserId!=0 AND te.Amount>" . (int) EXPENSE_HIERARCHY_AMOUNT_THRESHOLD . "
                AND te.ExpenseDate>='2026-04-01'
                AND " . expense_hierarchy_sql_user_acted_clause($conn, $user_id, 'approved');
            $sql .= " ORDER BY te.ExpenseDate DESC, te.id DESC";
            $res = $conn->query($sql);
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    if (!expense_hierarchy_user_acted_on_expense($conn, $row, $user_id, 'approved')) {
                        continue;
                    }
                    ho_expense_acted_render_admin_row($conn, $row, false);
                }
            }
            ?>
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
<?php ho_expense_acted_datatable_script(0); ?>
</body>
</html>
