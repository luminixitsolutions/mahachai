<?php
/**
 * Combined date + geo + employee filters for All Leave Requests page.
 */
require_once __DIR__ . '/update_attendance_filters.php';

if (!isset($allLeaveFilterMethod)) {
    $allLeaveFilterMethod = 'post';
}

$arfSource = ($allLeaveFilterMethod === 'request') ? $_REQUEST : $_POST;
$arfFromDate = isset($arfSource['FromDate']) ? trim((string) $arfSource['FromDate']) : '';
$arfToDate = isset($arfSource['ToDate']) ? trim((string) $arfSource['ToDate']) : '';
$arfSearched = isset($arfSource['Search']) && $arfSource['Search'] !== '';
$arfMainZoneId = ua_attendance_filter_value($arfSource, 'MainZoneId', 'all');
$arfZoneId = ua_attendance_filter_value($arfSource, 'ZoneId', 'all');
$arfSubZoneId = ua_attendance_filter_value($arfSource, 'SubZoneId', 'all');
$arfFrId = ua_attendance_filter_value($arfSource, 'FrId', 'all');
$arfUserId = isset($arfSource['UserId']) ? trim((string) $arfSource['UserId']) : '';
$arfStatusFilter = ua_attendance_status_filter($arfSource, 'all');

$arfFilterLists = ua_load_attendance_filter_lists($arfSource);

if (!function_exists('maha_ar_sql_append_user_geo')) {
    function maha_ar_sql_append_user_geo($alias = 'tu')
    {
        global $arfMainZoneId, $arfZoneId, $arfSubZoneId, $arfFrId, $arfUserId;
        $sql = '';
        if ($arfFrId !== 'all') {
            $sql .= " AND {$alias}.UnderFrId='" . (int) $arfFrId . "'";
        }
        if ($arfSubZoneId !== 'all') {
            $sql .= " AND {$alias}.SubZoneId='" . (int) $arfSubZoneId . "'";
        }
        if ($arfZoneId !== 'all') {
            $sql .= " AND {$alias}.ZoneId='" . (int) $arfZoneId . "'";
        }
        if ($arfMainZoneId !== 'all') {
            $sql .= " AND {$alias}.MainZoneId='" . (int) $arfMainZoneId . "'";
        }
        if ($arfUserId !== '' && $arfUserId !== 'all') {
            $sql .= " AND {$alias}.id='" . (int) $arfUserId . "'";
        }
        return $sql;
    }
}
?>
<div id="accordion2">
    <div class="card mb-2">
        <div id="accordion2-2" class="collapse show" data-parent="#accordion2">
            <div class="" style="padding:5px;">
                <form id="validation-form" method="post" enctype="multipart/form-data" action="">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" name="FromDate" id="FromDate" class="form-control" value="<?php echo htmlspecialchars($arfFromDate, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" name="ToDate" id="ToDate" class="form-control" value="<?php echo htmlspecialchars($arfToDate, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">Zone</label>
                            <select class="form-control" id="MainZoneId" name="MainZoneId" onchange="uaOnMainZoneChange(this.value)">
                                <option value="all" <?php echo $arfMainZoneId === 'all' ? 'selected' : ''; ?>>All</option>
                                <?php foreach ($arfFilterLists['mainZones'] as $row) { ?>
                                <option value="<?php echo (int) $row['id']; ?>" <?php echo (string) $arfMainZoneId === (string) $row['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['Name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">Region</label>
                            <select class="form-control" id="ZoneId" name="ZoneId" onchange="uaOnZoneChange(this.value)">
                                <option value="all" <?php echo $arfZoneId === 'all' ? 'selected' : ''; ?>>All</option>
                                <?php foreach ($arfFilterLists['regions'] as $row) { ?>
                                <option value="<?php echo (int) $row['id']; ?>" <?php echo (string) $arfZoneId === (string) $row['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['Name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">Sub Zone</label>
                            <select class="form-control" id="SubZoneId" name="SubZoneId" onchange="uaOnSubZoneChange(this.value)">
                                <option value="all" <?php echo $arfSubZoneId === 'all' ? 'selected' : ''; ?>>All</option>
                                <?php foreach ($arfFilterLists['subZones'] as $row) { ?>
                                <option value="<?php echo (int) $row['id']; ?>" <?php echo (string) $arfSubZoneId === (string) $row['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['Name']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">Franchise</label>
                            <select class="select2-demo form-control" name="FrId" id="FrId" onchange="uaReloadEmployees()">
                                <option value="all" <?php echo $arfFrId === 'all' ? 'selected' : ''; ?>>All</option>
                                <?php foreach ($arfFilterLists['franchises'] as $row) { ?>
                                <option value="<?php echo (int) $row['id']; ?>" <?php echo (string) $arfFrId === (string) $row['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['ShopName']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="form-label">Employee Status</label>
                            <select class="form-control" id="StatusFilter" name="StatusFilter" onchange="uaReloadEmployees()">
                                <option value="all" <?php echo $arfStatusFilter === 'all' ? 'selected' : ''; ?>>All</option>
                                <option value="1" <?php echo $arfStatusFilter === '1' ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo $arfStatusFilter === '0' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Employee</label>
                            <select class="select2-demo form-control" name="UserId" id="UserId">
                                <option value="">All Employees</option>
                                <?php
                                $employees = is_array($arfFilterLists['employees']) ? $arfFilterLists['employees'] : array();
                                foreach ($employees as $row) {
                                    $empId = (int) $row['id'];
                                    $label = trim($row['Fname'] . ' (' . $row['Phone'] . ')');
                                    ?>
                                <option value="<?php echo $empId; ?>" <?php echo (string) $arfUserId === (string) $empId ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <input type="hidden" name="Search" value="Search">
                        <div class="form-group col-md-1" style="padding-top:30px;">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-finish">Search</button>
                        </div>
                        <?php if ($arfSearched) { ?>
                        <div class="form-group col-md-1" style="padding-top:30px;">
                            <label class="form-label">&nbsp;</label>
                            <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-info btn-block" data-toggle="tooltip" data-placement="top" data-original-title="Clear Filter">X</a>
                        </div>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php ua_render_attendance_filter_script(); ?>
