<?php
include_once '../config.php';

session_start();
include_once 'incuserdetails.php';
$user_id = $_SESSION['Admin']['id'];

function esc($conn, $v)
{
    return $conn->real_escape_string(trim((string) $v));
}

if ($_POST['action'] === 'Add') {
    $points = esc($conn, $_POST['points'] ?? '');
    $rs = esc($conn, $_POST['rs'] ?? '');
    $minorder = esc($conn, $_POST['minorder'] ?? '');
    $frid = isset($_POST['frid']) ? (int) $_POST['frid'] : 0;
    $prodtype = isset($_POST['prodtype']) ? (int) $_POST['prodtype'] : 0;

    if ($frid <= 0 || $prodtype <= 0) {
        echo 0;
        exit;
    }

    $q = "SELECT id FROM tbl_points_setting WHERE frid = $frid AND prodtype = $prodtype LIMIT 1";
    $result = $conn->query($q);
    if ($result && $result->num_rows > 0) {
        echo 0;
        exit;
    }

    $qx = "INSERT INTO tbl_points_setting (points, rs, minorder, frid, prodtype) VALUES ('$points', '$rs', '$minorder', $frid, $prodtype)";
    $conn->query($qx);
    echo 1;
    exit;
}

if ($_POST['action'] === 'fetch_record') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode([]);
        exit;
    }
    $query = "SELECT * FROM tbl_points_setting WHERE id = $id LIMIT 1";
    $result = $conn->query($query);
    $row = $result ? $result->fetch_assoc() : null;
    echo json_encode($row ?: []);
    exit;
}

if ($_POST['action'] === 'Edit') {
    $id = (int) ($_POST['id'] ?? 0);
    $points = esc($conn, $_POST['points'] ?? '');
    $rs = esc($conn, $_POST['rs'] ?? '');
    $minorder = esc($conn, $_POST['minorder'] ?? '');
    $frid = isset($_POST['frid']) ? (int) $_POST['frid'] : 0;
    $prodtype = isset($_POST['prodtype']) ? (int) $_POST['prodtype'] : 0;

    if ($id <= 0 || $frid <= 0 || $prodtype <= 0) {
        echo 0;
        exit;
    }

    $q = "SELECT id FROM tbl_points_setting WHERE frid = $frid AND prodtype = $prodtype AND id != $id LIMIT 1";
    $result = $conn->query($q);
    if ($result && $result->num_rows > 0) {
        echo 0;
        exit;
    }

    $query2 = "UPDATE tbl_points_setting SET points = '$points', rs = '$rs', minorder = '$minorder', frid = $frid, prodtype = $prodtype WHERE id = $id";
    $conn->query($query2);
    echo 1;
    exit;
}

if ($_POST['action'] === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        $conn->query("DELETE FROM tbl_points_setting WHERE id = $id");
    }
    echo 'Delete Successfully';
    exit;
}

if ($_POST['action'] === 'view') {
    ?>
<table id="example" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
        <thead>
            <tr>
              <th>#</th>
              <th>Business Partner</th>
              <th>Product type</th>
              <th>Points</th>
              <th>Rs</th>
              <th>Min order</th>
                <?php if (in_array('10', $Options) || in_array('11', $Options)) { ?>
               <th>Action</th>
               <?php } ?>
            </tr>
        </thead>
        <tbody>
          <?php
            $srno = 1;
    $sql = "SELECT p.*, TRIM(CONCAT(IFNULL(u.Fname,''),' ',IFNULL(u.Lname,''))) AS partner_name
            FROM tbl_points_setting p
            LEFT JOIN tbl_users u ON u.id = p.frid
            ORDER BY p.id DESC";
    $rx = $conn->query($sql);
    while ($rx && ($nx = $rx->fetch_assoc())) {
        $pn = $nx['partner_name'] !== '' ? htmlspecialchars($nx['partner_name']) : ('ID ' . (int) $nx['frid']);
        ?>
           <tr>
             <td><?php echo $srno; ?></td>
             <td><?php echo $pn; ?></td>
             <td><?php echo (int) $nx['prodtype']; ?></td>
             <td><?php echo htmlspecialchars((string) $nx['points']); ?></td>
             <td><?php echo htmlspecialchars((string) $nx['rs']); ?></td>
             <td><?php echo htmlspecialchars((string) $nx['minorder']); ?></td>
             <?php if (in_array('10', $Options) || in_array('11', $Options)) { ?>
             <td>
                   <?php if (in_array('10', $Options)) { ?>
                 <a data-id="<?php echo (int) $nx['id']; ?>" href='javascript:void(0);' data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Edit" class="update"><i class="lnr lnr-pencil mr-2"></i></a>&nbsp;&nbsp;
                  <?php } if (in_array('11', $Options)) { ?>
                 <a data-id="<?php echo (int) $nx['id']; ?>" href='javascript:void(0);' data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Delete" class="delete" id="bootbox-confirm"><i class="lnr lnr-trash text-danger"></i></a>
                  <?php } ?>
             </td><?php } ?>
            </tr>
             <?php
             $srno++;
    }
    ?>
        </tbody>
    </table>
    <script type="text/javascript">
      $(document).ready(function() {
      $('#example').DataTable( {
        responsive: true
      });
      });
    </script>
 <?php
}
