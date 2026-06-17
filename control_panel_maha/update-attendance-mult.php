<?php 
session_start();
include_once 'config.php';
require_once __DIR__ . '/includes/update_attendance_filters.php';
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage="Day-Attendance";
$Page = "Day-Attendance";
?> 
<!DOCTYPE html>
<html lang="en" class="default-style">

<head>
    <title>
    </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="" />

    <?php include_once 'header_script.php'; ?>
    <script src="ckeditor/ckeditor.js"></script>
</head>

<body>
    <style type="text/css">
    .password-tog-info {
        display: inline-block;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        position: absolute;
        right: 50px;
        top: 30px;
        text-transform: uppercase;
        z-index: 2;
    }
    </style>
     <style>
  body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #fff;
    margin: 0;
    padding: 0;
  }

  .slip-container {
    max-width: 900px;
    margin: 0 auto;
    background: #fff;
    padding: 30px 40px;
    position: relative;
  }

  /* Header */
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #007b8f;
    padding-bottom: 10px;
    margin-bottom: 20px;
  }

  .header-left {
    display: flex;
    align-items: center;
    gap: 15px;
  }

  .header-left img {
    width: 70px;
    height: 70px;
    object-fit: contain;
  }

  .company-info h2 {
    margin: 0;
    font-size: 22px;
    color: #007b8f;
    font-weight: 700;
  }

  .company-info p {
    margin: 3px 0;
    font-size: 13px;
    color: #333;
  }

  /* Info Section */
  .info-row {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  .employee-info {
    width: 55%;
  }

  .employee-info p {
    margin: 4px 0;
    font-size: 14px;
  }

  .attendance-summary {
    width: 40%;
    border: 1px solid #007b8f;
    border-radius: 4px;
    font-size: 14px;
    background: #f9fcfc;
  }

  .attendance-summary table {
    width: 100%;
    border-collapse: collapse;
  }

  .attendance-summary th,
  .attendance-summary td {
    border: 1px solid #007b8f;
    padding: 6px 10px;
    text-align: right;
    font-size: 13px;
  }

  .attendance-summary th {
    background: #e2f5f7;
    text-align: left;
    font-weight: bold;
    color: #007b8f;
  }

  /* Salary Table */
  table.salary-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }

  .salary-table th,
  .salary-table td {
    border: 1px solid #ccc;
    padding: 8px 12px;
    font-size: 14px;
  }

  .salary-table th {
    background: #e2f5f7;
    color: #007b8f;
    font-weight: bold;
  }

  .salary-table .section-title {
    background: #007b8f;
    color: #fff;
    font-weight: bold;
  }

  .salary-table .right {
    text-align: right;
  }

  /* Calendar Styles */
  .calendar-container {
    margin-top: 30px;
    page-break-before: always;
  }

  .calendar {
    width: 100%;
    border-collapse: collapse;
    text-align: center;
  }

  .calendar th {
    background: #0F5A4A;
    color: #fff;
    padding: 8px;
  }

  .calendar td {
    border: 1px solid #ccc;
    width: 14.2%;
    vertical-align: top;
    height: 0px;
    padding: 6px;
    font-size: 13px;
  }

  .date-cell {
    background: #f9fcfc;
    border-radius: 6px;
    padding: 6px;
    color: #333;
    line-height: 1.4em;
  }

  .date-cell small {
    font-weight: bold;
    color: #007b8f;
  }

  .date-cell.working { background: #e2f5f7; color: #007b8f; }
  .date-cell.off { background: #ffe6e6; }
  .date-cell.absent { background: #f8d7da; color: #721c24; }

  /* Footer on all pages */
  @page {
    size: A4;
    margin: 15mm;
    @bottom-center {
      content: element(footer);
    }
  }

  .footer-note {
    position: running(footer);
    font-size: 12px;
    color: #555;
    border-top: 1px dashed #ccc;
    text-align: center;
    padding-top: 5px;
    margin-top: 10px;
  }

  /* Print Optimization */
  @media print {
    html, body {
      background: #fff !important;
      margin: 0 !important;
      padding: 0 !important;
      overflow: visible !important;
      -webkit-print-color-adjust: exact !important;
      color-adjust: exact !important;
    }

    * {
      box-shadow: none !important;
      text-shadow: none !important;
      filter: none !important;
      transition: none !important;
      animation: none !important;
    }

    .slip-container {
      border: none !important;
      box-shadow: none !important;
      margin: 0 !important;
      padding: 15mm !important;
    }

    table, tr, td, th {
      page-break-inside: avoid !important;
      border-collapse: collapse !important;
    }

    .calendar-container {
      page-break-before: always !important;
    }

    .footer-note {
      border-top: none !important;
      font-size: 11px !important;
      color: #666 !important;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
    }

    .print-button {
      display: none !important;
    }
  }

  .print-button {
    margin-top: 20px;
    text-align: right;
  }

  .print-button button {
    background: #007b8f;
    color: #fff;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
  }

  .print-button button:hover {
    background: #005f6b;
  }
  
  .absent-click {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    background: #dc3545;
    color: #fff;
    font-weight: bold;
    cursor: pointer;        /* âœ… SHOW HAND POINTER */
    transition: all 0.2s ease-in-out;
}

.absent-click:hover {
    background: #b02a37;
    transform: scale(1.08);
    box-shadow: 0 3px 8px rgba(0,0,0,0.25);
}
.date-cell.weekoff-punch {
    background: #fff3cd;
    color: #856404;
    font-weight: bold;
}
.date-cell.weekoff-punch::after {
    content: "Week Off Punch";
    display: none;
    position: absolute;
    background: #333;
    color: #fff;
    font-size: 11px;
    padding: 4px 6px;
    border-radius: 4px;
}

.date-cell.weekoff-punch:hover::after {
    display: block;
}

.date-cell.working { background: #e2f5f7; color: #007b8f; }
.date-cell.weekoff-punch { background: #fff3cd; color: #856404; }
.date-cell.absent { background: #f8d7da; color: #721c24; }
.date-cell.weekoff-punch b {
    font-size: 16px;
}
.present-click,
.weekoff-click,
.off-click,
.absent-click {
    cursor: pointer;
    font-weight: bold;
}

</style>
     <div class="layout-wrapper layout-1 layout-without-sidenav">
        <div class="layout-inner">

             <?php include_once 'top_header.php'; include_once 'sidebar.php'; ?>


            <div class="layout-container">

                

              <?php 
$showCalendar = false;

if(isset($_POST['submit'])){
    $UserId = $_POST['UserId'];
    $month  = $_POST['month'];
    $year   = $_POST['year'];
    $showCalendar = true;
}
?>
                <div class="layout-content">

                    <div class="container-fluid flex-grow-1 container-p-y">
                        <h4 class="font-weight-bold py-3 mb-0">Update Attendance</h4>

                        <div class="card mb-4">
                            <div class="card-body">
                                <form id="validation-form" method="post" autocomplete="off" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" id="userid">
                                    <input type="hidden" name="action" value="Save" id="action">
                                    <div class="form-row">
                                        <?php
                                        $uaFilterSource = $_REQUEST;
                                        ua_render_attendance_filter_fields($uaFilterSource, $_POST['UserId'] ?? '');
                                        ?>

                                     <div class="form-group col-md-2">
<label class="form-label">Month</label>
<select class="form-control" style="width: 100%" name="month" id="month" required>
<option <?php if($_REQUEST['month'] == '01'){?> selected <?php } ?> value="01">Jan</option>
<option <?php if($_REQUEST['month'] == '02'){?> selected <?php } ?> value="02">Feb</option>
<option <?php if($_REQUEST['month'] == '03'){?> selected <?php } ?> value="03">Mar</option>
<option <?php if($_REQUEST['month'] == '04'){?> selected <?php } ?> value="04">Apr</option>
<option <?php if($_REQUEST['month'] == '05'){?> selected <?php } ?> value="05">May</option>
<option <?php if($_REQUEST['month'] == '06'){?> selected <?php } ?> value="06">Jun</option>
<option <?php if($_REQUEST['month'] == '07'){?> selected <?php } ?> value="07">Jul</option>
<option <?php if($_REQUEST['month'] == '08'){?> selected <?php } ?> value="08">Aug</option>
<option <?php if($_REQUEST['month'] == '09'){?> selected <?php } ?> value="09">Sep</option>
<option <?php if($_REQUEST['month'] == '10'){?> selected <?php } ?> value="10">Oct</option>
<option <?php if($_REQUEST['month'] == '11'){?> selected <?php } ?> value="11">Nov</option>
<option <?php if($_REQUEST['month'] == '12'){?> selected <?php } ?> value="12">Dec</option>
  </select>
<div class="clearfix"></div>
</div>

<div class="form-group col-md-2">
<label class="form-label">Year</label>
<select class="form-control" style="width: 100%" name="year" id="year" required>
    <option <?php if($_REQUEST['year'] == '2026'){?> selected <?php } ?> value="2026">2026</option>
     <option <?php if($_REQUEST['year'] == '2025'){?> selected <?php } ?> value="2025">2025</option>
    <option <?php if($_REQUEST['year'] == '2024'){?> selected <?php } ?> value="2024">2024</option>
  </select>
<div class="clearfix"></div>
</div>

<div class="form-group col-md-3" style="padding-top: 30px;">
                                    <button type="submit" class="btn btn-primary btn-finish" name="submit"> Show Calendar</button>
                                    </div>
                                    </div>
                                    <!-- <button id="growl-default" class="btn btn-default">Default</button> -->
                                    
                                </form>
                                
                             <?php if($showCalendar){ 

$start_date = "$year-$month-01";
$total_days = date('t', strtotime($start_date));
$first_day  = date('N', strtotime($start_date));
$day = 1;

/* ===== FETCH ATTENDANCE ===== */
$attendance = [];
$weekoff_dates = [];

// Fetch attendance records
$sqlAtt = "
SELECT CreatedDate,
MAX(CASE WHEN Type=1 THEN CreatedTime END) AS InTime,
MAX(CASE WHEN Type=2 THEN CreatedTime END) AS OutTime
FROM tbl_attendance
WHERE UserId='$UserId'
AND CreatedDate BETWEEN '$start_date' AND '".date('Y-m-t',strtotime($start_date))."'
GROUP BY CreatedDate
";

$res = getList($sqlAtt);

foreach ($res as $r) {
    $cell = ua_build_attendance_day_cell($r['InTime'] ?? '', $r['OutTime'] ?? '');
    if ($cell) {
        $attendance[$r['CreatedDate']] = $cell;
    }
}

// Fetch week off punches
$sqlWeekOff = "SELECT punch_date 
               FROM tbl_week_off_punch 
               WHERE user_id='$UserId' 
               AND punch_date BETWEEN '$start_date' AND '".date('Y-m-t',strtotime($start_date))."'";
$resWeekOff = getList($sqlWeekOff);

foreach($resWeekOff as $wo){
    $weekoff_dates[] = $wo['punch_date'];
    if(!isset($attendance[$wo['punch_date']])){
        $attendance[$wo['punch_date']] = array(
            'type' => 'weekoff-punch',
            'display' => '<b>W</b>'
        );
    }
}

/* ===== MANAGER-APPROVED LEAVE (same rule as attendance-report-month-wise) ===== */
$leaveDays = array();
$end_month = date('Y-m-t', strtotime($start_date));
$uidLeave = (int)$UserId;
$startEsc = $conn->real_escape_string($start_date);
$endEsc = $conn->real_escape_string($end_month);
$sqlLeave = "SELECT FromDate, ToDate, LeaveType FROM tbl_leave_request 
             WHERE UserId = ".$uidLeave." AND ManagerStatus = 1 
             AND DATE(ToDate) >= '".$startEsc."' 
             AND DATE(FromDate) <= '".$endEsc."'";
$resLeave = getList($sqlLeave);
foreach ($resLeave as $lv) {
    $fd = date('Y-m-d', strtotime($lv['FromDate']));
    $td = date('Y-m-d', strtotime($lv['ToDate']));
    $lt = isset($lv['LeaveType']) ? trim((string)$lv['LeaveType']) : '';
    $t = strtotime($fd);
    $end = strtotime($td);
    while ($t <= $end) {
        $ds = date('Y-m-d', $t);
        if ($ds >= $start_date && $ds <= $end_month) {
            $leaveDays[$ds] = $lt;
        }
        $t = strtotime('+1 day', $t);
    }
}

$regulData = ua_load_attendance_regularization_map((int) $UserId, $start_date, $end_month);
$logData = ua_load_attendance_modification_log_map((int) $UserId, $start_date, $end_month);

?>

<style>
.date-cell{
    min-height:110px;
    padding:6px;
    cursor:pointer;
    position:relative;
}
.date-cell.selected{
    border:2px solid #ff9800;
    box-shadow:0 0 6px rgba(0,0,0,.3);
}
.date-cell.absent{background:#f8d7da;}
.date-cell.working{background:#e2f5f7;}
.date-cell.leave-approved{background:#fff3cd;color:#856404;font-weight:bold;}
.date-cell.leave-approved .leave-type-label{display:block;font-size:13px;font-weight:700;margin-top:2px;color:#664d03;letter-spacing:0.02em;}
.regul-by,
.regul-by-label,
.regul-by-approver{
    display:block;
    font-size:8px;
    color:#0d6efd;
    font-style:italic;
    font-weight:600;
    line-height:1.2;
    word-break:break-word;
    overflow-wrap:anywhere;
    text-align:center;
}
.regul-by{
    margin-top:4px;
}
.regul-by-approver{
    margin-top:2px;
}
.updated-by{
    font-size:10px;
    color:#c0392b;
    margin-top:4px;
    font-style:italic;
    line-height:1.25;
    word-break:break-word;
}
.date-cell.working-in-only{
    background:#fff8e1;
    color:#6d4c00;
}
#in_time[readonly]{
    background:#f3f3f3;
    cursor:not-allowed;
}
</style>

<br>

<div class="calendar-container">
    <h3 style="text-align:center;color:#007b8f;font-weight:700;">
        Attendance Calendar - <?php echo date('F Y', strtotime($start_date)); ?>
    </h3>

    <table class="table table-bordered text-center">
<thead>
<tr>
<th>Mon</th><th>Tue</th><th>Wed</th>
<th>Thu</th><th>Fri</th><th>Sat</th><th>Sun</th>
</tr>
</thead>
<tbody>

<tr>
<?php for($i=1;$i<$first_day;$i++) echo "<td></td>"; ?>

<?php
for($i=$first_day;$i<=7;$i++){
    $date_str = sprintf('%04d-%02d-%02d',$year,$month,$day);

    $hasWorking = isset($attendance[$date_str]['type']) && in_array($attendance[$date_str]['type'], array('working', 'working-in-only', 'working-out-only'), true);
    $hasWeekOff = isset($attendance[$date_str]['type']) && $attendance[$date_str]['type'] === 'weekoff-punch';
    $isLeave = array_key_exists($date_str, $leaveDays);
    // Week off punch first; then approved leave (L instead of P/A); then present; else absent
    if ($hasWeekOff) {
        $class = $attendance[$date_str]['type'];
        $display = $attendance[$date_str]['display'];
    } elseif ($isLeave) {
        $class = 'leave-approved';
        $ltShow = isset($leaveDays[$date_str]) ? trim((string)$leaveDays[$date_str]) : '';
        $display = '<b>L</b>';
        if ($ltShow !== '') {
            $display .= '<span class="leave-type-label">'.htmlspecialchars($ltShow).'</span>';
        }
    } elseif ($hasWorking) {
        $class = $attendance[$date_str]['type'];
        $display = $attendance[$date_str]['display'];
    } else {
        $class = 'absent';
        $display = 'A';
    }

    $regulHtml = ua_render_regularization_note($date_str, $regulData);
    $modHtml = ua_render_modification_note($date_str, $logData);

    echo "<td>
    <div class='date-cell $class'
         data-date='$date_str'
         onclick='toggleDateSelection(this)'>
        <small>".date('d M',strtotime($date_str))."</small><br>
        $display
        $modHtml
        $regulHtml
    </div>
    </td>";
    $day++;
}
echo "</tr>";

/* ===== REMAINING ROWS ===== */
while($day <= $total_days){
    echo "<tr>";
    for($i=1;$i<=7;$i++){
        if($day > $total_days){
            echo "<td></td>";
        }else{
            $date_str = sprintf('%04d-%02d-%02d',$year,$month,$day);

            $hasWorking = isset($attendance[$date_str]['type']) && in_array($attendance[$date_str]['type'], array('working', 'working-in-only', 'working-out-only'), true);
            $hasWeekOff = isset($attendance[$date_str]['type']) && $attendance[$date_str]['type'] === 'weekoff-punch';
            $isLeave = array_key_exists($date_str, $leaveDays);
            if ($hasWeekOff) {
                $class = $attendance[$date_str]['type'];
                $display = $attendance[$date_str]['display'];
            } elseif ($isLeave) {
                $class = 'leave-approved';
                $ltShow = isset($leaveDays[$date_str]) ? trim((string)$leaveDays[$date_str]) : '';
                $display = '<b>L</b>';
                if ($ltShow !== '') {
                    $display .= '<span class="leave-type-label">'.htmlspecialchars($ltShow).'</span>';
                }
            } elseif ($hasWorking) {
                $class = $attendance[$date_str]['type'];
                $display = $attendance[$date_str]['display'];
            } else {
                $class = 'absent';
                $display = 'A';
            }

            $regulHtml = ua_render_regularization_note($date_str, $regulData);
            $modHtml = ua_render_modification_note($date_str, $logData);

            echo "<td>
            <div class='date-cell $class'
                 data-date='$date_str'
                 onclick='toggleDateSelection(this)'>
                <small>".date('d M',strtotime($date_str))."</small><br>
                $display
                $modHtml
                $regulHtml
            </div>
            </td>";
            $day++;
        }
    }
    echo "</tr>";
}
?>

</tbody>
</table>
<!-- SELECTED COUNT -->
<div class="text-center mt-2">
    <span id="selectedCount" style="display:none; color:#007b8f; font-weight:bold; font-size:14px;"></span>
</div>

<!-- ACTION BUTTONS -->
<div class="text-center mt-3">
    <button type="button" class="btn btn-secondary mr-2" onclick="clearSelections()">
        Clear Selection
    </button>
    <button class="btn btn-success" onclick="openAttendanceModal()">
        Submit Attendance (<span id="selectedCountBtn">0</span>)
    </button>
</div>

<?php } ?>






                            </div>
                        </div>




<div class="modal fade" id="attendanceModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Mark Attendance</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>

      </div>

     <form id="attendanceForm">

        <div class="modal-body">

          <input type="hidden" name="emp_id" id="emp_id">
          <input type="hidden" name="att_date" id="att_date">
            
             <div class="mb-3">
            <label class="form-label">Type</label>
            <select class="form-control" id="att_type" name="att_type" required>
                <option selected value="">Select Type</option>
                <option value="1">Present Attendance</option>
                <option value="3">Abscent Attendance</option>
                <option value="2">Week Off</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Employee</label>
            <input type="text" id="emp_name" class="form-control" readonly>
          </div>

          <div class="mb-3">
            <label class="form-label">Selected Dates</label>
            <textarea id="show_date" class="form-control" rows="3" readonly></textarea>
            <small class="text-muted">You can mark attendance for multiple dates at once</small>
          </div>

          <div class="mb-3">
    <label class="form-label">In Time</label>
    <input type="time" name="in_time" id="in_time" class="form-control" required value="10:00">
    <small id="in_time_hint" class="text-muted d-none"></small>
</div>

<div class="mb-3">
    <label class="form-label">Out Time</label>
    <input type="time" name="out_time" id="out_time" class="form-control" required value="19:00">
    <small id="out_time_hint" class="text-muted d-none"></small>
</div>


          <div class="mb-3">
            <label class="form-label">Reason</label>
            <textarea name="comment" class="form-control" rows="2" required> </textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Attendance</button>
        </div>

      </form>

    </div>
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

    <script>
// Store selected dates
let selectedDates = [];

// Toggle date selection
function toggleDateSelection(element) {
    const date = $(element).data('date');
    const index = selectedDates.indexOf(date);
    
    if (index > -1) {
        // Deselect
        selectedDates.splice(index, 1);
        $(element).removeClass('selected');
    } else {
        // Select
        selectedDates.push(date);
        $(element).addClass('selected');
    }
    
    // Update selected count display
    updateSelectedCount();
}

// Update selected count display
function updateSelectedCount() {
    const count = selectedDates.length;
    if (count > 0) {
        $('#selectedCount').text(count + ' date(s) selected').show();
        $('#selectedCountBtn').text(count);
    } else {
        $('#selectedCount').hide();
        $('#selectedCountBtn').text('0');
    }
}

// Clear all selections
function clearSelections() {
    selectedDates = [];
    $('.date-cell').removeClass('selected');
    updateSelectedCount();
}

function loadCalendar() {
    let userId = $('#UserId').val();
    let month  = $('#month').val();
    let year   = $('#year').val();

    if(userId && month && year){
        $.ajax({
            url: 'ajax-load-attendance-calendar.php',
            type: 'POST',
            data: { userId:userId, month:month, year:year },
            success:function(data){
                $('#calendar_container').html(data);
                // Clear selections when calendar reloads
                clearSelections();
            }
        });
    }
}

// Auto reload calendar on change
$('#UserId, #month, #year').on('change', function(){
    loadCalendar();
    clearSelections();
});

function formatTime12(time24) {
    if (!time24) return '';
    const parts = time24.split(':');
    if (parts.length < 2) return time24;
    let h = parseInt(parts[0], 10);
    const m = parts[1];
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12;
    if (h === 0) h = 12;
    return String(h).padStart(2, '0') + ':' + m + ' ' + ampm;
}

function resetAttendanceTimeFields() {
    $('#in_time').prop('readonly', false).removeClass('bg-light');
    $('#in_time_hint, #out_time_hint').addClass('d-none').text('');
    $('#in_time').val('10:00');
    $('#out_time').val('19:00');
}

function applyAttendanceTimeFields(data) {
    resetAttendanceTimeFields();

    if (!data || data.status !== 'ok') {
        return;
    }

    if (data.sample_in_time) {
        $('#in_time').val(data.sample_in_time);
    }
    if (data.sample_out_time) {
        $('#out_time').val(data.sample_out_time);
    }

    if (data.all_have_in) {
        $('#in_time').prop('readonly', true).addClass('bg-light');
        $('#in_time_hint')
            .removeClass('d-none')
            .text('Punch in time is read-only. Only punch out time can be updated.');
    } else if (data.any_have_in) {
        $('#in_time_hint')
            .removeClass('d-none')
            .text('Existing punch in times will stay unchanged. In time applies only to dates without punch in.');
    }

    if (data.dates_with_in > 0 && data.dates_with_out === 0) {
        $('#out_time_hint')
            .removeClass('d-none')
            .text('Employee punched in only. Please add punch out time.');
    }
}

function openAttendanceModal(){
    const userId = $('#UserId').val();
    
    if (!userId) {
        alert('Please select an employee first');
        return;
    }
    
    if (selectedDates.length === 0) {
        alert('Please select at least one date from the calendar');
        return;
    }
    
    const empName = $('#UserId option:selected').text();
    const formattedDates = selectedDates.map(date => {
        const d = new Date(date + 'T00:00:00');
        return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }).join('\n');
    
    $('#emp_id').val(userId);
    $('#emp_name').val(empName);
    $('#show_date').val(formattedDates);
    resetAttendanceTimeFields();

    $.ajax({
        url: 'ajax_files/ajax_mult_attendance_times.php',
        type: 'POST',
        dataType: 'json',
        data: {
            emp_id: userId,
            dates: JSON.stringify(selectedDates)
        },
        success: function(data) {
            applyAttendanceTimeFields(data);
            $('#attendanceModal').modal('show');
        },
        error: function() {
            $('#attendanceModal').modal('show');
        }
    });
}

 $(document).ready(function() {
    $("#attendanceForm").submit(function (e) {
        e.preventDefault();
        
        let att_type = $('#att_type').val();
        if(att_type == ''){
            alert("Please Select Mark Type");
            return false;
        }
        
        if(selectedDates.length === 0){
            alert("Please select at least one date from the calendar");
            return false;
        }
        
        // Prepare form data
        let formData = new FormData(this);
        formData.append('dates', JSON.stringify(selectedDates));
        
        // Show loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Saving...');
        
        $.ajax({
            url: "save-mult-attendance.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                console.log(response);
                
                try {
                    const result = JSON.parse(response);
                    
                    if(result.status === 'success'){
                        const datesToUpdate = [...selectedDates];
                        const savedMap = {};
                        if (Array.isArray(result.saved_dates)) {
                            result.saved_dates.forEach(function(item) {
                                savedMap[item.date] = item;
                            });
                        }
                        
                        datesToUpdate.forEach(function(date) {
                            const dateCell = $('.date-cell[data-date="' + date + '"]');
                            const dayText = new Date(date + 'T00:00:00').toLocaleDateString('en-GB', {
                                day: '2-digit',
                                month: 'short'
                            });
                            
                            let in_time = $('input[name="in_time"]').val();
                            let out_time = $('input[name="out_time"]').val();
                            const saved = savedMap[date] || null;
                            if (saved && saved.in_time) {
                                in_time = saved.in_time;
                            }
                            
                            if(att_type == "1"){
                                const inLabel = formatTime12(in_time);
                                const outLabel = formatTime12(out_time);
                                const modNote = saved && saved.log_action
                                    ? "<div class='updated-by'>" + saved.log_action + "</div>"
                                    : "";
                                
                                dateCell
                                    .removeClass('absent weekoff-punch selected working-in-only working-out-only')
                                    .addClass('working')
                                    .css("background", "#e2f5f7");
                                
                                dateCell.html(`
                                    <small>${dayText}</small><br>
                                    In: ${inLabel}<br>
                                    Out: ${outLabel}
                                    ${modNote}
                                `);
                            }
                            else if(att_type == "2"){
                                dateCell
                                    .removeClass('absent working selected working-in-only')
                                    .addClass('weekoff-punch')
                                    .css("background", "#fff3cd");
                                
                                dateCell.html(`
                                    <small>${dayText}</small><br>
                                    <b>W</b>
                                `);
                            }
                            else if(att_type == "3"){
                                dateCell
                                    .removeClass('working weekoff-punch selected working-in-only')
                                    .addClass('absent')
                                    .css("background", "#f8d7da");
                                
                                dateCell.html(`
                                    <small>${dayText}</small><br>
                                    A
                                `);
                            }
                        });
                        
                        // Store count before clearing
                        const savedCount = selectedDates.length;
                        
                        // Clear selections after successful save
                        clearSelections();
                        
                        alert("Attendance saved successfully for " + savedCount + " date(s)!");
                    } else {
                        alert("Error: " + (result.message || "Failed to save attendance"));
                    }
                } catch(e) {
                    console.error("Error parsing response:", e);
                    // If response is not JSON, assume success
                    alert("Attendance saved successfully!");
                }
                
                $('#attendanceModal').modal('hide');
                submitBtn.prop('disabled', false).text(originalText);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
                alert("Error saving attendance. Please try again.");
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
 });
</script>
<?php ua_render_attendance_filter_script(); ?>

</body>

</html>