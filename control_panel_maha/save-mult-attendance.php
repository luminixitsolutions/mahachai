<?php
session_start();
include_once 'config.php';
require_once __DIR__ . '/includes/update_attendance_filters.php';

global $conn;

try {
    $conn->begin_transaction();

    $user_id = (int) ($_SESSION['Admin']['id'] ?? 0);
    $emp_id = isset($_POST['emp_id']) ? (int) $_POST['emp_id'] : 0;
    $modifierName = ua_get_modifier_display_name($user_id);

    if ($emp_id === 0) {
        throw new Exception('Employee ID is required');
    }

    $dates_json = isset($_POST['dates']) ? $_POST['dates'] : '[]';
    $dates = json_decode($dates_json, true);
    $in_time = isset($_POST['in_time']) ? trim($_POST['in_time']) : '10:30';
    $out_time = isset($_POST['out_time']) ? trim($_POST['out_time']) : '19:30';
    $comment = isset($_POST['comment']) ? addslashes(trim($_POST['comment'])) : '';
    $att_type = isset($_POST['att_type']) ? (int) $_POST['att_type'] : 0;
    $modifieddate = date('Y-m-d');

    if (!$dates || !is_array($dates) || count($dates) === 0) {
        throw new Exception('No dates selected');
    }

    if ($att_type === 0) {
        throw new Exception('Attendance type is required');
    }

    if ($att_type === 1 && $out_time === '') {
        throw new Exception('Out time is required');
    }

    $success_count = 0;
    $saved_dates = array();

    foreach ($dates as $date) {
        $date = date('Y-m-d', strtotime($date));
        if (!$date || $date === '1970-01-01') {
            continue;
        }

        $dateEsc = mysqli_real_escape_string($conn, $date);
        $inEsc = mysqli_real_escape_string($conn, $in_time);
        $outEsc = mysqli_real_escape_string($conn, $out_time);

        if ($att_type === 1) {
            $res_in = $conn->query("SELECT id, CreatedTime FROM tbl_attendance
                WHERE UserId='$emp_id' AND CreatedDate='$dateEsc' AND Type=1 LIMIT 1");
            $had_in = $res_in && $res_in->num_rows > 0;
            $existingInTime = '';
            if ($had_in) {
                $rowIn = $res_in->fetch_assoc();
                $existingInTime = trim((string) ($rowIn['CreatedTime'] ?? ''));
            }

            $res_out = $conn->query("SELECT id FROM tbl_attendance
                WHERE UserId='$emp_id' AND CreatedDate='$dateEsc' AND Type=2 LIMIT 1");
            $had_out = $res_out && $res_out->num_rows > 0;

            $conn->query("DELETE FROM tbl_week_off_punch WHERE user_id='$emp_id' AND punch_date='$dateEsc'");

            if (!$had_in) {
                $insert_in = "INSERT INTO tbl_attendance
                    (UserId,CreatedDate,CreatedTime,Comment,Type,Status,CreatedBy,CreatedOn)
                    VALUES ('$emp_id','$dateEsc','$inEsc','$comment',1,1,'$user_id',NOW())";
                if (!$conn->query($insert_in)) {
                    throw new Exception('Error inserting in time: ' . $conn->error);
                }
            }

            if ($had_out) {
                $rowOut = $res_out->fetch_assoc();
                $update_out = "UPDATE tbl_attendance
                    SET CreatedTime='$outEsc', Comment='$comment',
                        ModifiedBy='$user_id', ModifiedDate=NOW()
                    WHERE id='{$rowOut['id']}'";
                if (!$conn->query($update_out)) {
                    throw new Exception('Error updating out time: ' . $conn->error);
                }
            } else {
                $insert_out = "INSERT INTO tbl_attendance
                    (UserId,CreatedDate,CreatedTime,Comment,Type,Status,CreatedBy,CreatedOn)
                    VALUES ('$emp_id','$dateEsc','$outEsc','$comment',2,1,'$user_id',NOW())";
                if (!$conn->query($insert_out)) {
                    throw new Exception('Error inserting out time: ' . $conn->error);
                }
            }

            if ($had_in) {
                $logAction = 'modified punchout time by - ' . $modifierName;
            } else {
                $logAction = 'modified full by - ' . $modifierName;
            }

            $logActionEsc = mysqli_real_escape_string($conn, $logAction);
            $log_sql = "INSERT INTO tbl_attendance_log
                SET AttDate='$dateEsc', userid='$emp_id',
                    modifiedby='$user_id', modifieddate='$modifieddate',
                    action='$logActionEsc'";
            if (!$conn->query($log_sql)) {
                throw new Exception('Error logging attendance: ' . $conn->error);
            }

            $displayIn = $had_in ? $existingInTime : $in_time;
            $saved_dates[] = array(
                'date' => $date,
                'log_action' => $logAction,
                'in_time' => substr($displayIn, 0, 5),
                'out_time' => substr($out_time, 0, 5),
            );
            $success_count++;
        } elseif ($att_type === 3) {
            $logActionEsc = mysqli_real_escape_string($conn, 'Mark AS Absent');
            $conn->query("INSERT INTO tbl_attendance_log
                SET AttDate='$dateEsc', userid='$emp_id',
                    modifiedby='$user_id', modifieddate='$modifieddate',
                    action='$logActionEsc'");

            $conn->query("DELETE FROM tbl_week_off_punch WHERE user_id='$emp_id' AND punch_date='$dateEsc'");
            $conn->query("DELETE FROM tbl_attendance WHERE UserId='$emp_id' AND CreatedDate='$dateEsc'");
            $success_count++;
        } elseif ($att_type === 2) {
            $logActionEsc = mysqli_real_escape_string($conn, 'Mark AS Week Off');
            $conn->query("INSERT INTO tbl_attendance_log
                SET AttDate='$dateEsc', userid='$emp_id',
                    modifiedby='$user_id', modifieddate='$modifieddate',
                    action='$logActionEsc'");

            $conn->query("DELETE FROM tbl_attendance WHERE UserId='$emp_id' AND CreatedDate='$dateEsc'");
            $conn->query("DELETE FROM tbl_week_off_punch WHERE user_id='$emp_id' AND punch_date='$dateEsc'");
            $conn->query("INSERT INTO tbl_week_off_punch
                SET user_id='$emp_id', punch_date='$dateEsc',
                    punch_time='$inEsc', reason='$comment', status='active'");
            $success_count++;
        }
    }

    $conn->commit();

    echo json_encode(array(
        'status' => 'success',
        'message' => 'Attendance saved successfully for ' . $success_count . ' date(s)',
        'modifier_name' => $modifierName,
        'saved_dates' => $saved_dates,
    ));
} catch (Exception $e) {
    $conn->rollback();
    error_log('Error saving attendance: ' . $e->getMessage());
    echo json_encode(array(
        'status' => 'error',
        'message' => $e->getMessage(),
    ));
}
