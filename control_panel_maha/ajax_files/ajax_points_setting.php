<?php
include_once '../config.php';

session_start();
include_once 'incuserdetails.php';
$user_id = $_SESSION['Admin']['id'];

function esc($conn, $v)
{
    return $conn->real_escape_string(trim((string) $v));
}

if ($_POST['action'] === 'single_save') {
    if (!in_array('10', $Options) && !in_array('14', $Options)) {
        echo 0;
        exit;
    }

    $prodtype = (int) ($_POST['prodtype'] ?? 0);
    if (!in_array($prodtype, [1, 2, 4], true)) {
        echo 0;
        exit;
    }

    $points = esc($conn, $_POST['points'] ?? '');
    $rs = esc($conn, $_POST['rs'] ?? '');
    $minorder = esc($conn, $_POST['minorder'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);

    if ($id > 0) {
        $ok = $conn->query("UPDATE tbl_points_setting SET points='$points', rs='$rs', minorder='$minorder', prodtype=$prodtype WHERE id=$id LIMIT 1");
        echo $ok ? 1 : 0;
        exit;
    }

    $ok = $conn->query("INSERT INTO tbl_points_setting (points, rs, minorder, frid, prodtype) VALUES ('$points', '$rs', '$minorder', 0, $prodtype)");
    echo $ok ? 1 : 0;
    exit;
}

echo 0;
