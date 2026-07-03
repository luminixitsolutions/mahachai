<?php
/**
 * Shared list helpers for HO manager/admin approved & rejected expense pages.
 */

require_once __DIR__ . '/expense_hierarchy_approval.php';

if (!function_exists('ho_expense_acted_select_sql')) {
    function ho_expense_acted_select_sql()
    {
        return "SELECT te.*, tu.Fname, tu.Lname, tu.UnderByUser, tu.Photo AS Uphoto,
            tu2.Fname AS MgrName, tu3.Fname AS AccName, tu4.Fname AS AccountName,
            tu5.Fname AS BhFname, tu5.Lname AS BhLname
            FROM tbl_expense_request te
            INNER JOIN tbl_users tu ON tu.id = te.UserId
            LEFT JOIN tbl_users tu2 ON tu2.id = te.MrgBy
            LEFT JOIN tbl_users tu3 ON tu3.id = te.AccBy
            LEFT JOIN tbl_users tu4 ON tu4.id = te.AccountBy
            LEFT JOIN tbl_users tu5 ON tu5.id = te.BhBy";
    }
}

if (!function_exists('ho_expense_acted_photo_cell')) {
    function ho_expense_acted_photo_cell(array $row)
    {
        if (empty($row['Uphoto'])) {
            echo '<img src="user_icon.jpg" class="d-block ui-w-40 rounded-circle" style="width:40px;height:40px;">';
            return;
        }
        $photo = htmlspecialchars((string) $row['Uphoto']);
        echo '<img src="../uploads/' . $photo . '" class="d-block ui-w-40 rounded-circle" alt="" style="width:40px;height:40px;" onerror="this.src=\'user_icon.jpg\'">';
    }
}

if (!function_exists('ho_expense_acted_level_cells')) {
    function ho_expense_acted_level_cells($conn, array $row)
    {
        $levels = expense_hierarchy_build_level_displays($conn, $row);
        for ($n = 1; $n <= 3; $n++) {
            echo '<td class="maha-ar-level-cell">';
            expense_hierarchy_echo_level_cell($levels[$n]);
            echo '</td>';
        }
    }
}

if (!function_exists('ho_expense_acted_render_manager_row')) {
    function ho_expense_acted_render_manager_row($conn, array $row)
    {
        $expDate = date('d/m/Y', strtotime(str_replace('-', '/', (string) ($row['ExpenseDate'] ?? ''))));
        ?>
            <tr>
                <td><?php echo (int) $row['id']; ?></td>
                <td><?php echo htmlspecialchars($expDate); ?></td>
                <?php ho_expense_acted_level_cells($conn, $row); ?>
                <td><?php ho_expense_acted_photo_cell($row); ?></td>
                <td><?php echo htmlspecialchars(trim(($row['Fname'] ?? '') . ' ' . ($row['Lname'] ?? ''))); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['Amount'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['Narration'] ?? '')); ?></td>
            </tr>
        <?php
    }
}

if (!function_exists('ho_expense_acted_render_admin_row')) {
    function ho_expense_acted_render_admin_row($conn, array $row, $withComments = false)
    {
        $expDate = date('d/m/Y', strtotime(str_replace('-', '/', (string) ($row['ExpenseDate'] ?? ''))));
        ?>
            <tr>
                <td><?php echo (int) $row['id']; ?></td>
                <?php ho_expense_acted_level_cells($conn, $row); ?>
                <?php if ($withComments) { ?>
                <td><?php echo htmlspecialchars((string) ($row['MannagerComment'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['AdminComment'] ?? '')); ?></td>
                <?php } ?>
                <td><?php echo htmlspecialchars($expDate); ?></td>
                <td><?php ho_expense_acted_photo_cell($row); ?></td>
                <td><?php echo htmlspecialchars(trim(($row['Fname'] ?? '') . ' ' . ($row['Lname'] ?? ''))); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['Amount'] ?? '')); ?></td>
                <td><?php echo htmlspecialchars((string) ($row['Narration'] ?? '')); ?></td>
            </tr>
        <?php
    }
}

if (!function_exists('ho_expense_acted_datatable_script')) {
    function ho_expense_acted_datatable_script($orderCol = 0)
    {
        ?>
<script type="text/javascript">
$(document).ready(function() {
    var $table = $('#example');
    if ($.fn.dataTable && $table.length && !$.fn.dataTable.isDataTable($table)) {
        $table.DataTable({
            autoWidth: false,
            scrollX: false,
            order: [[<?php echo (int) $orderCol; ?>, 'desc']],
            language: { emptyTable: 'No records found.' }
        });
    }
});
</script>
        <?php
    }
}

if (!function_exists('ho_expense_all_pending_table_head')) {
    function ho_expense_all_pending_table_head()
    {
        ?>
            <thead>
            <tr>
                <th>Expense Id</th>
                <th>Expense Date</th>
                <th>Employee Name</th>
                <th>Photo</th>
                <th>Amount</th>
                <th>Level 1 Approval</th>
                <th>Level 2 Approval</th>
                <th>Level 3 Approval</th>
                <th>Narration</th>
            </tr>
            </thead>
        <?php
    }
}

if (!function_exists('ho_expense_all_pending_table_row')) {
    function ho_expense_all_pending_table_row($conn, array $row)
    {
        $expIdRow = (int) ($row['id'] ?? 0);
        $empId = (int) ($row['UserId'] ?? 0);
        $empName = trim(($row['Fname'] ?? '') . ' ' . ($row['Lname'] ?? ''));
        if (function_exists('maha_ar_esc')) {
            $empName = maha_ar_esc($empName);
        } else {
            $empName = htmlspecialchars($empName);
        }
        $expDate = function_exists('maha_ar_fmt_date')
            ? maha_ar_fmt_date($row['ExpenseDate'] ?? '')
            : date('d/m/Y', strtotime(str_replace('-', '/', (string) ($row['ExpenseDate'] ?? ''))));
        $amount = number_format((float) ($row['Amount'] ?? 0), 2);
        $narration = function_exists('maha_ar_esc')
            ? maha_ar_esc($row['Narration'] ?? '')
            : htmlspecialchars((string) ($row['Narration'] ?? ''));
        ?>
            <tr>
                <td><?php
                    if (function_exists('maha_ar_id_link')) {
                        echo maha_ar_id_link('employee_expense', $expIdRow);
                    } else {
                        echo $expIdRow;
                    }
                ?></td>
                <td><?php echo $expDate; ?></td>
                <td class="emp-name-cell"><a href="employee-hierarchy.php?id=<?php echo $empId; ?>" target="_blank"><?php echo $empName; ?></a></td>
                <td class="photo-cell"><?php ho_expense_acted_photo_cell($row); ?></td>
                <td class="text-money">&#8377;<?php echo $amount; ?></td>
                <?php ho_expense_acted_level_cells($conn, $row); ?>
                <td class="narration-cell"><?php echo $narration; ?></td>
            </tr>
        <?php
    }
}

if (!function_exists('ho_expense_all_pending_datatable_init')) {
    function ho_expense_all_pending_datatable_init()
    {
        ?>
<script type="text/javascript">
$(document).ready(function() {
    if (!$('#example').length || !$.fn.dataTable) {
        return;
    }
    $('#example').DataTable({
        autoWidth: false,
        scrollX: false,
        dom: 'Bfrtip',
        order: [[0, 'desc']],
        buttons: [{
            extend: 'excelHtml5',
            title: 'All_Pending_Expenses',
            exportOptions: { columns: ':visible' }
        }],
        columnDefs: [
            { targets: 3, orderable: false, searchable: false },
            { targets: 4, className: 'text-money' },
            { targets: [5, 6, 7], orderable: false },
            { targets: 8, className: 'narration-cell' }
        ],
        language: { emptyTable: 'No pending expense requests found.' }
    });
});
</script>
        <?php
    }
}
