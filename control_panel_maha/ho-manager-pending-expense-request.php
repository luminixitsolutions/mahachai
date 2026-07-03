<?php 
session_start();
include_once 'config.php';
include_once 'auth.php';
require_once __DIR__ . '/includes/expense_hierarchy_approval.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "HO-Manager-Expenses";
$Page = "HO-Manager-Peding-Expense-Request";
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
<style>
.exp-approval-history-tree { padding: 4px 0; }
.exp-approval-node {
    border-left: 4px solid #cbd5e0;
    padding: 10px 12px 10px 16px;
    margin-bottom: 12px;
    background: #f8f9fa;
    border-radius: 0 8px 8px 0;
}
.exp-approval-node.border-left-success { border-left-color: #28a745; }
.exp-approval-node.border-left-danger { border-left-color: #dc3545; }
.exp-approval-node.border-left-warning { border-left-color: #ffc107; }
.exp-approval-node.border-left-secondary { border-left-color: #adb5bd; }
.exp-approval-node-head { font-size: 13px; color: #495057; margin-bottom: 6px; }
.exp-approval-node-body { font-size: 14px; }
.btn-view-approval-history {
    font-size: 12px;
    padding: 4px 10px;
    white-space: nowrap;
}
.maha-pending-expense-dt-wrap {
    overflow: visible !important;
}
.maha-pending-expense-dt-wrap .dataTables_scrollHead,
.maha-pending-expense-dt-wrap .dataTables_scrollFoot {
    display: none !important;
}
.maha-pending-expense-dt-wrap .dataTables_scrollBody {
    overflow: visible !important;
}
#example.maha-pending-expense-table { width: 100% !important; table-layout: auto; }
#example.maha-pending-expense-table thead th,
#example.maha-pending-expense-table tbody td {
    vertical-align: middle !important;
    white-space: normal !important;
    word-break: break-word;
}
#example.maha-pending-expense-table .col-exp-id { width: 90px; white-space: nowrap !important; }
#example.maha-pending-expense-table .col-exp-date { width: 110px; white-space: nowrap !important; }
#example.maha-pending-expense-table .col-employee { min-width: 180px; }
#example.maha-pending-expense-table .col-amount { width: 100px; white-space: nowrap !important; text-align: right !important; }
#example.maha-pending-expense-table thead th.col-amount { text-align: right !important; }
#example.maha-pending-expense-table .col-narration { min-width: 140px; max-width: 220px; }
#example.maha-pending-expense-table .col-photo { width: 70px; text-align: center !important; white-space: nowrap !important; }
#example.maha-pending-expense-table .col-approval { min-width: 160px; max-width: 220px; }
#example.maha-pending-expense-table .col-history { width: 150px; white-space: nowrap !important; text-align: center !important; }
#example.maha-pending-expense-table thead th.col-history { text-align: center !important; }
</style>
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
  $sql11 = "DELETE FROM tbl_expense_request WHERE id = '$id'";
  $conn->query($sql11);
  ?>
    <script type="text/javascript">
      alert("Deleted Successfully!");
      window.location.href="expense-request.php";
    </script>
<?php } 

if($_REQUEST['action'] == 'changestatus'){
    $id = $_REQUEST["id"];
    $val = $_REQUEST["val"];
    $CreatedDate = date('Y-m-d');
    $CreatedTime = date('h:i a');
    $sql3 = "SELECT * FROM tbl_expense_request WHERE id = '$id'";
    $row3 = getRecord($sql3);
    $UserId = $row3['UserId'];
    $Amount = $row3['Amount'];
    if($val == 0){
        $sql = "UPDATE tbl_expense_request SET Status=1 WHERE id='$id'";
        $conn->query($sql);
        $sql2 = "INSERT INTO wallet SET ExpId='$id',UserId='$UserId',Amount='$Amount',Narration='Expense Amount Approved',Status='Dr',CreatedDate='$CreatedDate',CreatedTime='$CreatedTime'";
        $conn->query($sql2);
    }
    else{
        $sql = "UPDATE tbl_expense_request SET Status=0 WHERE id='$id'";
        $conn->query($sql);
        $sql2 = "DELETE FROM wallet WHERE ExpId='$id'";
        $conn->query($sql2);
    }
 ?>
    <script type="text/javascript">
      alert("Record Saved Successfully");
      window.location.href="expense-request.php";
    </script>
<?php   
}
?>

<div class="layout-content">

<div class="container-fluid flex-grow-1 container-p-y">
<h4 class="font-weight-bold py-3 mb-0">Pending Expense Request
  
</h4>

<div class="card" style="padding: 10px;">
<div class="card-datatable table-responsive maha-pending-expense-dt-wrap">
<table id="example" class="table table-striped table-bordered maha-pending-expense-table" style="width:100%">
        <thead>
            <tr>
                <th class="col-exp-id">Expense Id</th>
                <th class="col-approval">Approval</th>
                <th class="col-history">View Approval History</th>
                <th class="col-exp-date">Expense Date</th>
                <th class="col-employee">Employee Name</th>
                <th class="col-amount">Amount</th>
                <th class="col-narration">Narration</th>
                <th class="col-photo">Photo</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $rowCount = 0;
               $hierarchyPendingSql = expense_hierarchy_table_exists($conn)
                   ? " OR te.id IN (SELECT ExpId FROM tbl_expense_approval_levels WHERE ApproverUserId='$user_id' AND Status='pending')"
                   : '';
               $sql = "SELECT te.*,tu.Fname,tu.Lname,tu.UnderByUser,tu.Photo AS Uphoto,tu2.Fname AS MgrName,tec.Name As ExpCatName,tub.ShopName,tl.Name AS ExpLocation,tu3.Fname AS AccName,tu5.Fname AS BhFname,tu5.Lname AS BhLname,tu6.Fname AS HrFname,tu6.Lname AS HrLname FROM tbl_expense_request te 
                INNER JOIN tbl_users tu ON tu.id=te.UserId 
                LEFT JOIN tbl_users tu2 ON tu2.id=te.MrgBy 
                LEFT JOIN tbl_users tu3 ON tu3.id=te.AccBy 
                LEFT JOIN tbl_users tu5 ON tu5.id=te.BhBy 
                LEFT JOIN tbl_users tu6 ON tu6.id=te.HrBy
                LEFT JOIN tbl_expenses_category tec ON tec.id=te.ExpCatId 
                LEFT JOIN tbl_users_bill tub ON tub.id=te.FrId 
                LEFT JOIN tbl_locations tl ON tl.id=te.Locations WHERE te.AdminStatus='0' AND te.UserId!=0 AND te.SendToApproval='Yes'
                AND (te.ManagerStatus='0'" . $hierarchyPendingSql . ")"; 
            /*if($user_id == 1322){
              $sql.=" AND te.Amount<=5000";
          }*/
            if($Roll != 1){
                $sql.=" AND te.UserId!='$user_id'";
            }
            /*if($ExpCatId!=''){
                $sql.=" AND te.ExpCatId IN($ExpCatId)";
            }*/
            if($user_id != 2727){
                //$sql.="  AND te.ExpCatId!=3";
            }
            $sql.=" AND te.ExpenseDate>='2026-04-01' ORDER BY te.ExpenseDate DESC";
            //echo $sql;
            $res = $conn->query($sql);
            while($row = $res->fetch_assoc())
            {
                    $MgrName = $row['MgrName'];
                    $UnderByUser = (int) ($row['UnderByUser'] ?? 0);
                    $expRowId = (int) $row['id'];
                    $hasHierarchy = expense_hierarchy_has_levels($conn, $expRowId);
                    $activeLvl = null;
                    $showRow = false;
                    if (!$hasHierarchy && $UnderByUser === (int) $user_id && (string) $row['ManagerStatus'] === '0') {
                        $showRow = true;
                    } elseif ($hasHierarchy) {
                        expense_hierarchy_rebuild_stale_levels($conn, $expRowId);
                        $activeLvl = getRecord("SELECT * FROM tbl_expense_approval_levels WHERE ExpId='$expRowId' AND Status='pending' ORDER BY LevelNo ASC LIMIT 1");
                        if (empty($activeLvl['id'])) {
                            $activeLvl = null;
                        }
                        $showRow = $activeLvl && (int) ($activeLvl['ApproverUserId'] ?? 0) === (int) $user_id;
                    }
                    if ($showRow) {
                        $rowCount++;
                        $empName = htmlspecialchars(trim($row['Fname'] . ' ' . $row['Lname']));
                        $expAmount = number_format((float) $row['Amount'], 2);
                        $expNarration = htmlspecialchars((string) ($row['Narration'] ?? ''));
                        $expDate = date('d/m/Y', strtotime(str_replace('-', '/', $row['ExpenseDate'])));
             ?>
            <tr>
                <td class="col-exp-id"><?php echo $expRowId; ?></td>
                <td class="col-approval" id="showstatus<?php echo $expRowId; ?>">
                    <a href="approve-expense-by-manager.php?id=<?php echo $expRowId; ?>&page=ho"><?php
                      if ($row['ManagerStatus'] == '1' && !$hasHierarchy) {
                          echo "<span style='color:green;'>Approved<br>By " . htmlspecialchars($MgrName) . "</span>";
                      } elseif ($row['ManagerStatus'] == '2') {
                          echo "<span style='color:red;'>Rejected<br>By " . htmlspecialchars($MgrName) . "</span>";
                      } elseif ($hasHierarchy) {
                          if ($activeLvl && (int) ($activeLvl['ApproverUserId'] ?? 0) === (int) $user_id) {
                              echo "<span style='color:orange;'>Pending — Level " . (int) $activeLvl['LevelNo'] . " (your approval)</span>";
                          } else {
                              echo "<span style='color:orange;'>Hierarchy approval in progress</span>";
                          }
                      } else {
                          echo "<span style='color:orange;'>Pending By Manager</span>";
                      }
                    ?></a>
                </td>
                <td class="col-history">
                    <button type="button" class="btn btn-sm btn-outline-primary btn-view-approval-history"
                            data-exp-id="<?php echo $expRowId; ?>"
                            data-exp-label="Expense #<?php echo $expRowId; ?> — <?php echo $empName; ?>">
                        View Approval History
                    </button>
                </td>
                <td class="col-exp-date"><?php echo $expDate; ?></td>
                <td class="col-employee"><?php echo $empName; ?></td>
                <td class="col-amount">&#8377;<?php echo $expAmount; ?></td>
                <td class="col-narration"><?php echo $expNarration !== '' ? $expNarration : '—'; ?></td>
                <td class="col-photo"><?php if ($row['Uphoto'] == '') { ?>
                    <img src="user_icon.jpg" class="rounded-circle" alt="" style="width:40px;height:40px;">
                    <?php } else { ?>
                    <img src="../uploads/<?php echo htmlspecialchars($row['Uphoto']); ?>" class="rounded-circle" alt="" style="width:40px;height:40px;" onerror="this.src='user_icon.jpg'">
                    <?php } ?></td>
            </tr>
           <?php $i++; } } ?>
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

<div class="modal fade" id="approvalHistoryModal" tabindex="-1" role="dialog" aria-labelledby="approvalHistoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="approvalHistoryModalLabel">Approval History</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="approvalHistoryModalBody">
        <p class="text-muted mb-0">Loading…</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php include_once 'footer_script.php'; ?>

<script type="text/javascript">
 $(document).ready(function() {
    var $table = $('#example');
    if ($.fn.dataTable && $table.length && !$.fn.dataTable.isDataTable($table)) {
        $table.DataTable({
            autoWidth: false,
            scrollX: false,
            scrollCollapse: false,
            order: [[0, 'desc']],
            dom: 'Bfrtip',
            language: {
                emptyTable: 'No pending expense requests found.'
            },
            columnDefs: [
                { targets: 1, orderable: false },
                { targets: 2, orderable: false, searchable: false },
                { targets: 5, className: 'text-right' },
                { targets: 7, orderable: false, searchable: false }
            ],
            buttons: [{
                extend: 'excelHtml5',
                title: 'Manager_Pending_Expense_Request',
                exportOptions: { columns: ':visible' }
            }]
        });
    }

    $(document).on('click', '.btn-view-approval-history', function(e) {
        e.preventDefault();
        var expId = $(this).data('exp-id');
        var label = $(this).data('exp-label') || ('Expense #' + expId);
        $('#approvalHistoryModalLabel').text('Approval History — ' + label);
        $('#approvalHistoryModalBody').html('<p class="text-muted mb-0">Loading…</p>');
        $('#approvalHistoryModal').modal('show');

        $.ajax({
            url: 'ajax_expense_approval_history.php',
            type: 'GET',
            data: { expId: expId },
            cache: false,
            success: function(html) {
                $('#approvalHistoryModalBody').html(html);
            },
            error: function() {
                $('#approvalHistoryModalBody').html('<p class="text-danger mb-0">Could not load approval history. Please try again.</p>');
            }
        });
    });
});
</script>
</body>
</html>
