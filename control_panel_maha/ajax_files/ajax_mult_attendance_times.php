<?php
session_start();
include_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$empId = isset($_POST['emp_id']) ? (int) $_POST['emp_id'] : 0;
$datesJson = isset($_POST['dates']) ? $_POST['dates'] : '[]';
$dates = json_decode($datesJson, true);

if ($empId <= 0 || !is_array($dates) || count($dates) === 0) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Employee and dates are required',
    ));
    exit;
}

$normalizedDates = array();
foreach ($dates as $date) {
    $date = date('Y-m-d', strtotime($date));
    if ($date && $date !== '1970-01-01') {
        $normalizedDates[] = $date;
    }
}

if (count($normalizedDates) === 0) {
    echo json_encode(array(
        'status' => 'error',
        'message' => 'No valid dates selected',
    ));
    exit;
}

$datesWithIn = 0;
$datesWithOut = 0;
$sampleIn = '';
$sampleOut = '';
$perDate = array();

foreach ($normalizedDates as $date) {
    $dateEsc = mysqli_real_escape_string($conn, $date);
    $row = getRecord("SELECT
            MAX(CASE WHEN Type=1 THEN CreatedTime END) AS InTime,
            MAX(CASE WHEN Type=2 THEN CreatedTime END) AS OutTime
        FROM tbl_attendance
        WHERE UserId='$empId' AND CreatedDate='$dateEsc'");

    $in = trim((string) ($row['InTime'] ?? ''));
    $out = trim((string) ($row['OutTime'] ?? ''));
    $hasIn = $in !== '';
    $hasOut = $out !== '';

    if ($hasIn) {
        $datesWithIn++;
        if ($sampleIn === '') {
            $sampleIn = substr($in, 0, 5);
        }
    }
    if ($hasOut) {
        $datesWithOut++;
        if ($sampleOut === '') {
            $sampleOut = substr($out, 0, 5);
        }
    }

    $perDate[$date] = array(
        'has_in' => $hasIn,
        'has_out' => $hasOut,
        'in_time' => $hasIn ? substr($in, 0, 5) : '',
        'out_time' => $hasOut ? substr($out, 0, 5) : '',
    );
}

$total = count($normalizedDates);
$allHaveIn = $datesWithIn === $total;
$anyHaveIn = $datesWithIn > 0;

echo json_encode(array(
    'status' => 'ok',
    'total_dates' => $total,
    'dates_with_in' => $datesWithIn,
    'dates_with_out' => $datesWithOut,
    'all_have_in' => $allHaveIn,
    'any_have_in' => $anyHaveIn,
    'in_readonly' => $allHaveIn,
    'sample_in_time' => $sampleIn,
    'sample_out_time' => $sampleOut,
    'per_date' => $perDate,
));
