<?php
if (!function_exists('ua_attendance_filter_value')) {
    function ua_attendance_filter_value(array $source, $key, $default = 'all')
    {
        if (!isset($source[$key]) || $source[$key] === '' || $source[$key] === null) {
            return $default;
        }
        return $source[$key];
    }
}

if (!function_exists('ua_attendance_status_filter')) {
    function ua_attendance_status_filter(array $source, $default = '1')
    {
        $status = ua_attendance_filter_value($source, 'StatusFilter', $default);
        if ($status !== '1' && $status !== '0') {
            return 'all';
        }
        return $status;
    }
}

if (!function_exists('ua_build_attendance_employee_sql')) {
    function ua_build_attendance_employee_sql(array $source)
    {
        global $conn;

        $statusFilter = ua_attendance_status_filter($source, '1');
        $mainZoneId = ua_attendance_filter_value($source, 'MainZoneId', 'all');
        $zoneId = ua_attendance_filter_value($source, 'ZoneId', 'all');
        $subZoneId = ua_attendance_filter_value($source, 'SubZoneId', 'all');
        $frId = ua_attendance_filter_value($source, 'FrId', 'all');

        $sql = "SELECT tu.id, tu.Fname, tu.Phone FROM tbl_users tu
                WHERE tu.Roll NOT IN(1,5,55,9,22,23,63,3) AND tu.OtherEmp=0";

        if ($statusFilter === '1' || $statusFilter === '0') {
            $sql .= " AND tu.Status='" . ($statusFilter === '1' ? '1' : '0') . "'";
        }
        if ($frId !== 'all') {
            $sql .= " AND tu.UnderFrId='" . (int) $frId . "'";
        }
        if ($subZoneId !== 'all') {
            $sql .= " AND tu.SubZoneId='" . (int) $subZoneId . "'";
        }
        if ($zoneId !== 'all') {
            $sql .= " AND tu.ZoneId='" . (int) $zoneId . "'";
        }
        if ($mainZoneId !== 'all') {
            $sql .= " AND tu.MainZoneId='" . (int) $mainZoneId . "'";
        }

        $sql .= ' ORDER BY tu.Fname';
        return $sql;
    }
}

if (!function_exists('ua_load_attendance_filter_lists')) {
    function ua_load_attendance_filter_lists(array $source)
    {
        $mainZoneId = ua_attendance_filter_value($source, 'MainZoneId', 'all');
        $zoneId = ua_attendance_filter_value($source, 'ZoneId', 'all');
        $subZoneId = ua_attendance_filter_value($source, 'SubZoneId', 'all');

        $mainZones = getList("SELECT id, Name FROM tbl_main_zone WHERE Status=1 ORDER BY Name");

        $regionSql = "SELECT id, Name FROM tbl_zone WHERE Status=1";
        if ($mainZoneId !== 'all') {
            $regionSql .= " AND MainZoneId='" . (int) $mainZoneId . "'";
        }
        $regionSql .= ' ORDER BY Name';
        $regions = getList($regionSql);

        $subZoneSql = "SELECT id, Name FROM tbl_sub_zone WHERE Status=1 AND id!=12";
        if ($zoneId !== 'all') {
            $subZoneSql .= " AND CatId='" . (int) $zoneId . "'";
        }
        $subZoneSql .= ' ORDER BY Name';
        $subZones = getList($subZoneSql);

        $frSql = "SELECT id, ShopName FROM tbl_users
                  WHERE Status='1' AND Roll=5 AND ShopName!='' AND id!=8757 AND OwnFranchise NOT IN(2,4)";
        if ($subZoneId !== 'all') {
            $frSql .= " AND SubZoneId='" . (int) $subZoneId . "'";
        } elseif ($zoneId !== 'all') {
            $frSql .= " AND ZoneId='" . (int) $zoneId . "'";
        } elseif ($mainZoneId !== 'all') {
            $frSql .= " AND MainZoneId='" . (int) $mainZoneId . "'";
        }
        $frSql .= ' ORDER BY ShopName';
        $franchises = getList($frSql);

        return array(
            'mainZones' => is_array($mainZones) ? $mainZones : array(),
            'regions' => is_array($regions) ? $regions : array(),
            'subZones' => is_array($subZones) ? $subZones : array(),
            'franchises' => is_array($franchises) ? $franchises : array(),
            'employees' => getList(ua_build_attendance_employee_sql($source)),
        );
    }
}

if (!function_exists('ua_load_attendance_regularization_map')) {
    function ua_load_attendance_regularization_map($userId, $startDate, $endDate)
    {
        global $conn;

        $userId = (int) $userId;
        $startDate = $conn->real_escape_string((string) $startDate);
        $endDate = $conn->real_escape_string((string) $endDate);
        $map = array();

        $sql = "SELECT ar.FromDate, ar.HrStatus, ar.ManagerStatus, ar.BdmStatus,
                       tu_mgr.Fname AS MgrName, tu_hr.Fname AS HrName, tu_bdm.Fname AS BdmName
                FROM tbl_attendance_request ar
                LEFT JOIN tbl_users tu_mgr ON tu_mgr.id = ar.MrgBy
                LEFT JOIN tbl_users tu_hr ON tu_hr.id = ar.HrBy
                LEFT JOIN tbl_users tu_bdm ON tu_bdm.id = ar.BdmBy
                WHERE ar.UserId = '$userId'
                AND (ar.HrStatus = '1' OR ar.ManagerStatus = '1' OR ar.BdmStatus = '1')
                AND DATE(ar.FromDate) >= '$startDate'
                AND DATE(ar.FromDate) <= '$endDate'
                ORDER BY ar.id DESC";

        $rows = getList($sql);
        if (!is_array($rows)) {
            return $map;
        }

        foreach ($rows as $row) {
            $dateKey = date('Y-m-d', strtotime($row['FromDate']));
            if (isset($map[$dateKey])) {
                continue;
            }

            $approver = '';
            if (($row['HrStatus'] ?? '') === '1' && !empty($row['HrName'])) {
                $approver = $row['HrName'];
            } elseif (($row['ManagerStatus'] ?? '') === '1' && !empty($row['MgrName'])) {
                $approver = $row['MgrName'];
            } elseif (($row['BdmStatus'] ?? '') === '1' && !empty($row['BdmName'])) {
                $approver = $row['BdmName'];
            }

            $map[$dateKey] = array(
                'approver' => $approver,
            );
        }

        return $map;
    }
}

if (!function_exists('ua_render_regularization_note')) {
    function ua_render_regularization_note($dateStr, array $regulMap)
    {
        if (!isset($regulMap[$dateStr])) {
            return '';
        }

        $approver = trim((string) ($regulMap[$dateStr]['approver'] ?? ''));
        $html = "<div class='regul-by'><span class='regul-by-label'>By Attendance Regularization</span>";
        if ($approver !== '') {
            $html .= '<span class="regul-by-approver">Approved: ' . htmlspecialchars($approver, ENT_QUOTES, 'UTF-8') . '</span>';
        }
        $html .= '</div>';

        return $html;
    }
}

if (!function_exists('ua_format_attendance_time')) {
    function ua_format_attendance_time($time)
    {
        $time = trim((string) $time);
        if ($time === '') {
            return '';
        }

        return date('h:i A', strtotime($time));
    }
}

if (!function_exists('ua_build_attendance_day_cell')) {
    function ua_build_attendance_day_cell($inTime, $outTime)
    {
        $in = trim((string) $inTime);
        $out = trim((string) $outTime);

        if ($in === '' && $out === '') {
            return null;
        }

        if ($in !== '' && $out !== '') {
            return array(
                'type' => 'working',
                'display' => 'In: ' . ua_format_attendance_time($in) . '<br>Out: ' . ua_format_attendance_time($out),
                'in_time' => $in,
                'out_time' => $out,
            );
        }

        if ($in !== '') {
            return array(
                'type' => 'working-in-only',
                'display' => 'In: ' . ua_format_attendance_time($in) . '<br>Out: -',
                'in_time' => $in,
                'out_time' => '',
            );
        }

        return array(
            'type' => 'working-out-only',
            'display' => 'In: -<br>Out: ' . ua_format_attendance_time($out),
            'in_time' => '',
            'out_time' => $out,
        );
    }
}

if (!function_exists('ua_load_attendance_modification_log_map')) {
    function ua_load_attendance_modification_log_map($userId, $startDate, $endDate)
    {
        global $conn;

        $userId = (int) $userId;
        $startDate = $conn->real_escape_string((string) $startDate);
        $endDate = $conn->real_escape_string((string) $endDate);
        $map = array();

        $sql = "SELECT l.AttDate, l.action, l.modifieddate, u.Fname AS modified_by
                FROM tbl_attendance_log l
                LEFT JOIN tbl_users u ON u.id = l.modifiedby
                WHERE l.userid = '$userId'
                AND DATE(l.AttDate) >= '$startDate'
                AND DATE(l.AttDate) <= '$endDate'
                ORDER BY l.id DESC";

        $rows = getList($sql);
        if (!is_array($rows)) {
            return $map;
        }

        foreach ($rows as $row) {
            $dateKey = date('Y-m-d', strtotime($row['AttDate']));
            if (isset($map[$dateKey])) {
                continue;
            }

            $action = trim((string) ($row['action'] ?? ''));
            if ($action === '') {
                continue;
            }

            $map[$dateKey] = array(
                'action' => $action,
                'name' => trim((string) ($row['modified_by'] ?? '')),
                'date' => $row['modifieddate'] ?? '',
            );
        }

        return $map;
    }
}

if (!function_exists('ua_render_modification_note')) {
    function ua_render_modification_note($dateStr, array $logMap)
    {
        if (!isset($logMap[$dateStr])) {
            return '';
        }

        $action = trim((string) ($logMap[$dateStr]['action'] ?? ''));
        if ($action === '') {
            return '';
        }

        return "<div class='updated-by'>" . htmlspecialchars($action, ENT_QUOTES, 'UTF-8') . '</div>';
    }
}

if (!function_exists('ua_get_modifier_display_name')) {
    function ua_get_modifier_display_name($userId)
    {
        $userId = (int) $userId;
        if ($userId <= 0) {
            return 'Admin';
        }

        $row = getRecord("SELECT Fname, Lname FROM tbl_users WHERE id='$userId' LIMIT 1");
        $name = trim(trim($row['Fname'] ?? '') . ' ' . trim($row['Lname'] ?? ''));

        return $name !== '' ? $name : 'Admin';
    }
}

if (!function_exists('ua_render_attendance_filter_fields')) {
    function ua_render_attendance_filter_fields(array $source, $selectedUserId = '')
    {
        $statusFilter = ua_attendance_status_filter($source, '1');
        $mainZoneId = ua_attendance_filter_value($source, 'MainZoneId', 'all');
        $zoneId = ua_attendance_filter_value($source, 'ZoneId', 'all');
        $subZoneId = ua_attendance_filter_value($source, 'SubZoneId', 'all');
        $frId = ua_attendance_filter_value($source, 'FrId', 'all');
        $lists = ua_load_attendance_filter_lists($source);
        $selectedUserId = (string) $selectedUserId;
        ?>
        <div class="form-group col-md-2">
            <label class="form-label">Zone</label>
            <select class="form-control" id="MainZoneId" name="MainZoneId" onchange="uaOnMainZoneChange(this.value)">
                <option value="all" <?php echo $mainZoneId === 'all' ? 'selected' : ''; ?>>All</option>
                <?php foreach ($lists['mainZones'] as $row) { ?>
                <option value="<?php echo (int) $row['id']; ?>" <?php echo (string) $mainZoneId === (string) $row['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['Name']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group col-md-2">
            <label class="form-label">Region</label>
            <select class="form-control" id="ZoneId" name="ZoneId" onchange="uaOnZoneChange(this.value)">
                <option value="all" <?php echo $zoneId === 'all' ? 'selected' : ''; ?>>All</option>
                <?php foreach ($lists['regions'] as $row) { ?>
                <option value="<?php echo (int) $row['id']; ?>" <?php echo (string) $zoneId === (string) $row['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['Name']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group col-md-2">
            <label class="form-label">Sub Zone</label>
            <select class="form-control" id="SubZoneId" name="SubZoneId" onchange="uaOnSubZoneChange(this.value)">
                <option value="all" <?php echo $subZoneId === 'all' ? 'selected' : ''; ?>>All</option>
                <?php foreach ($lists['subZones'] as $row) { ?>
                <option value="<?php echo (int) $row['id']; ?>" <?php echo (string) $subZoneId === (string) $row['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['Name']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group col-md-4">
            <label class="form-label">Franchise</label>
            <select class="select2-demo form-control" name="FrId" id="FrId" onchange="uaReloadEmployees()">
                <option value="all" <?php echo $frId === 'all' ? 'selected' : ''; ?>>All</option>
                <?php foreach ($lists['franchises'] as $row) { ?>
                <option value="<?php echo (int) $row['id']; ?>" <?php echo (string) $frId === (string) $row['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['ShopName']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group col-md-2">
            <label class="form-label">Status</label>
            <select class="form-control" id="StatusFilter" name="StatusFilter" onchange="uaReloadEmployees()">
                <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>All</option>
                <option value="1" <?php echo $statusFilter === '1' ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo $statusFilter === '0' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <div class="form-group col-md-4">
            <label class="form-label">Employee <span class="text-danger">*</span></label>
            <select class="select2-demo form-control" name="UserId" id="UserId" required>
                <option value="">Select Employee</option>
                <?php
                $employees = is_array($lists['employees']) ? $lists['employees'] : array();
                foreach ($employees as $row) {
                    $empId = (int) $row['id'];
                    $label = trim($row['Fname'] . ' (' . $row['Phone'] . ')');
                    ?>
                <option value="<?php echo $empId; ?>" <?php echo (string) $selectedUserId === (string) $empId ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                <?php } ?>
            </select>
            <div class="clearfix"></div>
        </div>
        <?php
    }
}

if (!function_exists('ua_render_attendance_filter_script')) {
    function ua_render_attendance_filter_script()
    {
        ?>
<script>
function uaReloadEmployees(selectedId) {
    $.ajax({
        url: 'ajax_files/ajax_dropdown.php',
        method: 'POST',
        data: {
            action: 'getAttendanceEmployee',
            mainZoneId: $('#MainZoneId').val() || 'all',
            zoneId: $('#ZoneId').val() || 'all',
            subZoneId: $('#SubZoneId').val() || 'all',
            id: $('#FrId').val() || 'all',
            status: $('#StatusFilter').val() || 'all',
            selectedId: selectedId || $('#UserId').val() || ''
        },
        success: function(data) {
            $('#UserId').html(data);
            if ($('#UserId').hasClass('select2-hidden-accessible')) {
                $('#UserId').trigger('change.select2');
            }
        }
    });
}

function uaOnMainZoneChange(mainZoneId) {
    $.ajax({
        url: 'ajax_files/ajax_dropdown.php',
        method: 'POST',
        data: { action: 'getZoneByMainZone', id: mainZoneId },
        success: function(data) {
            $('#ZoneId').html(data);
            $('#SubZoneId').html('<option value="all" selected>All</option>');
            uaReloadFranchises();
        }
    });
}

function uaOnZoneChange(zoneId) {
    $.ajax({
        url: 'ajax_files/ajax_dropdown.php',
        method: 'POST',
        data: { action: 'getSubZone2', id: zoneId },
        success: function(data) {
            $('#SubZoneId').html(data);
            uaReloadFranchises();
        }
    });
}

function uaOnSubZoneChange(subZoneId) {
    uaReloadFranchises(subZoneId);
}

function uaReloadFranchises(subZoneId) {
    $.ajax({
        url: 'ajax_files/ajax_dropdown.php',
        method: 'POST',
        data: {
            action: 'getFranchisesByLocation',
            mainZoneId: $('#MainZoneId').val() || 'all',
            zoneId: $('#ZoneId').val() || 'all',
            subZoneId: (typeof subZoneId !== 'undefined' ? subZoneId : ($('#SubZoneId').val() || 'all'))
        },
        success: function(data) {
            $('#FrId').html(data);
            uaReloadEmployees('');
        }
    });
}
</script>
        <?php
    }
}
