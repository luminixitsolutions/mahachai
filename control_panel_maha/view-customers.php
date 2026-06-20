<?php 
session_start();
include_once 'config.php';
//include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "Customers";
$Page = "View-Customers";

$fr_model_list = getList("SELECT * FROM tbl_fr_model WHERE Status=1 ORDER BY id");
if (!is_array($fr_model_list)) {
    $fr_model_list = array();
}
$frTypeNames = array();
foreach ($fr_model_list as $m) {
    $frTypeNames[(string)$m['id']] = isset($m['Name']) ? $m['Name'] : '';
}

$postMain = isset($_POST['MainZoneId']) ? $_POST['MainZoneId'] : 'all';
$postZone = isset($_POST['ZoneId']) ? $_POST['ZoneId'] : 'all';
$postSub = isset($_POST['SubZoneId']) ? $_POST['SubZoneId'] : 'all';

$main_zone_list = getList("SELECT * FROM tbl_main_zone WHERE Status=1 ORDER BY Name");
if (!is_array($main_zone_list)) {
    $main_zone_list = array();
}

if ($postMain !== '' && $postMain !== 'all') {
    $mid_esc = $conn->real_escape_string($postMain);
    $region_list = getList("SELECT * FROM tbl_zone WHERE Status=1 AND MainZoneId='$mid_esc' ORDER BY Name");
} else {
    $region_list = getList("SELECT * FROM tbl_zone WHERE Status=1 ORDER BY Name");
}
if (!is_array($region_list)) {
    $region_list = array();
}

if ($postZone !== '' && $postZone !== 'all') {
    $zid_esc = $conn->real_escape_string($postZone);
    $subzone_list = getList("SELECT * FROM tbl_sub_zone WHERE Status=1 AND CatId='$zid_esc' ORDER BY Name");
} else {
    $subzone_list = getList("SELECT * FROM tbl_sub_zone WHERE Status=1 ORDER BY Name");
}
if (!is_array($subzone_list)) {
    $subzone_list = array();
}

$franchise_filter_list = getList("SELECT id, ShopName FROM tbl_users_bill WHERE Roll = 5 AND Status = 1 ORDER BY ShopName ASC");
if (!is_array($franchise_filter_list)) {
    $franchise_filter_list = array();
}

/*$sql = "SELECT * FROM `tbl_users` WHERE Roll IN(5) AND IdStatus=0";
$row = getList($sql);
foreach($row as $result){
    $Phone = substr($result['Phone'],0,5);
    $CustomerId = "F".$Phone."".$result['id'];
    $sql = "UPDATE tbl_users SET CustomerId='$CustomerId',IdStatus=1 WHERE id='".$result['id']."'";
    $conn->query($sql);
}*/

?>
<!DOCTYPE html>
<html lang="en" class="default-style">
<head>
<title><?php echo $Proj_Title; ?> | View Customer Account List</title>
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



<?php
if($_REQUEST["action"]=="delete")
{
  $id = $_REQUEST["id"];
  $sql11 = "DELETE FROM tbl_users WHERE id = '$id' AND Roll=5";
  $conn->query($sql11);
  $sql11 = "DELETE FROM tbl_users_bill WHERE id = '$id' AND Roll=5";
  $conn->query($sql11);
  ?>
    <script type="text/javascript">
      alert("Deleted Successfully!");
      window.location.href="view-customers.php";
    </script>
<?php } ?>

<div class="layout-content">

<div class="container-fluid flex-grow-1 container-p-y">
<h4 class="font-weight-bold py-3 mb-0">View Franchise 
     <?php if(in_array("14", $Options)) {?>   
<span style="float: right;">
<a href="add-customer.php" class="btn btn-secondary btn-round"><i class="ion ion-md-add mr-2"></i> Add New</a></span><?php } ?>
</h4>

<div class="card" style="padding: 10px;">

       <div id="accordion2">
<div class="card mb-2">
                                        
                                        <div id="accordion2-2" class="collapse show" data-parent="#accordion2">
                                            <div class="" style="padding:5px;">
                                                <form id="validation-form" method="post" enctype="multipart/form-data" action="">
<div class="form-row">

<div class="form-group col-md-2">
                                            <label class="form-label">Zone</label>
                                            <select class="form-control" id="MainZoneId" name="MainZoneId">
                                                <option value="all" <?php echo ($postMain === 'all' || $postMain === '') ? 'selected' : ''; ?>>All</option>
                                                <?php foreach ($main_zone_list as $mz) { ?>
                                                <option value="<?php echo htmlspecialchars($mz['id']); ?>" <?php echo ((string)$postMain === (string)$mz['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($mz['Name']); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

<div class="form-group col-md-2">
                                            <label class="form-label">Region</label>
                                            <select class="form-control" id="ZoneId" name="ZoneId">
                                                <option value="all" <?php echo ($postZone === 'all' || $postZone === '') ? 'selected' : ''; ?>>All</option>
                                                <?php foreach ($region_list as $rz) { ?>
                                                <option value="<?php echo htmlspecialchars($rz['id']); ?>" <?php echo ((string)$postZone === (string)$rz['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($rz['Name']); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

<div class="form-group col-md-2">
                                            <label class="form-label">Sub Zone</label>
                                            <select class="form-control" id="SubZoneId" name="SubZoneId">
                                                <option value="all" <?php echo ($postSub === 'all' || $postSub === '') ? 'selected' : ''; ?>>All</option>
                                                <?php foreach ($subzone_list as $sz) { ?>
                                                <option value="<?php echo htmlspecialchars($sz['id']); ?>" <?php echo ((string)$postSub === (string)$sz['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($sz['Name']); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

<div class="form-group col-md-2">
                                            <label class="form-label">Franchise Type</label>
                                            <select class="form-control" id="OwnFranchise" name="OwnFranchise">
                                                <option value="all" <?php echo (!isset($_POST['OwnFranchise']) || $_POST['OwnFranchise'] === 'all' || $_POST['OwnFranchise'] === '') ? 'selected' : ''; ?>>All</option>
                                                <?php foreach ($fr_model_list as $fm) { ?>
                                                <option value="<?php echo htmlspecialchars($fm['id']); ?>" <?php echo (isset($_POST['OwnFranchise']) && (string)$_POST['OwnFranchise'] === (string)$fm['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($fm['Name']); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

<div class="form-group col-md-2">
                                            <label class="form-label">Franchise</label>
                                            <select class="form-control select2-demo" id="FrId" name="FrId">
                                                <option value="all" <?php echo (!isset($_POST['FrId']) || $_POST['FrId'] === 'all' || $_POST['FrId'] === '') ? 'selected' : ''; ?>>All</option>
                                                <?php foreach ($franchise_filter_list as $fr) { ?>
                                                <option value="<?php echo htmlspecialchars($fr['id']); ?>" <?php echo (isset($_POST['FrId']) && (string)$_POST['FrId'] === (string)$fr['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($fr['ShopName']); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

<input type="hidden" name="Search" value="Search">
<div class="form-group col-md-1" style="padding-top:28px;">
<button type="submit" name="submit" class="btn btn-primary btn-finish">Search</button>
</div>
<?php if(isset($_POST['Search'])) {?>
<div class="form-group col-md-1">
<label class="form-label">&nbsp;</label>
<a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-info btn-block" data-toggle="tooltip" data-placement="top" data-original-title="Clear Filter">X</a>
</div>
<?php } ?>
</div>

</form>
                                            </div>
                                        </div>
                                    </div>
   </div>

<style>
  .nowrap { white-space: nowrap; }
  .badge-pill { border-radius: 50rem; padding: .35rem .6rem; font-weight: 600; }

</style>

<?php
// Helpers
function franchiseBadge($code, $frTypeNames = array()) {
    $k = (string)$code;
    if ($k !== '' && isset($frTypeNames[$k])) {
        return '<span class="badge badge-pill" style="background:#5c6bc0;color:#fff;">' . htmlspecialchars($frTypeNames[$k]) . '</span>';
    }
    switch ($k) {
        case '1': return '<span class="badge badge-pill" style="background:#28a745;color:#fff;">COCO</span>';
        case '2': return '<span class="badge badge-pill" style="background:#ff9800;color:#fff;">FOFO</span>';
        case '3': return '<span class="badge badge-pill" style="background:#17a2b8;color:#fff;">FOCO</span>';
        case '4': return '<span class="badge badge-pill" style="background:#dc3545;color:#fff;">COFO</span>';
        default:  return '<span class="badge badge-pill" style="background:#6c757d;color:#fff;">Not Assigned</span>';
    }
}
function statusBadge($v) {
  return ($v=='1')
    ? '<span class="badge badge-pill" style="background:#28a745;color:#fff;">Active</span>'
    : '<span class="badge badge-pill" style="background:red;color:#fff;">In-active</span>';
}
function inr($n){ return number_format((float)$n, 2); }

function view_customers_tri_state_label($v) {
    $v = trim((string)$v);
    if ($v === 'Yes') {
        return 'Show &amp; calculate';
    }
    if ($v === 'No' || $v === '') {
        return 'not show';
    }
    $map = array(
        'not_show' => 'not show',
        'show_no_calc' => 'Show but not calculate',
        'show_calc' => 'Show &amp; calculate',
    );
    return isset($map[$v]) ? $map[$v] : htmlspecialchars($v);
}

function view_customers_map_link($lat, $lng, $display = null) {
    $lat = trim((string)$lat);
    $lng = trim((string)$lng);
    $label = $display !== null ? trim((string)$display) : $lat;
    if ($label === '') {
        $label = '-';
    }
    if ($lat === '' || $lng === '' || !is_numeric($lat) || !is_numeric($lng)) {
        return htmlspecialchars($label === '' ? '-' : $label);
    }
    $url = 'https://www.google.com/maps?q=' . rawurlencode($lat . ',' . $lng);
    return '<a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener noreferrer" class="text-primary" title="Open in Google Maps">' . htmlspecialchars($label) . '</a>';
}

function view_customers_resolve_bdm_zone($row, $mainZoneMap, $bdmMap) {
    $mainZoneId = (int)($row['MainZoneId'] ?? 0);
    $underByBdm = (int)($row['UnderByBdm'] ?? 0);

    $normalBdm = ($underByBdm > 0 && isset($bdmMap[$underByBdm])) ? $bdmMap[$underByBdm] : '';
    $normalZone = ($mainZoneId > 0 && isset($mainZoneMap[$mainZoneId])) ? $mainZoneMap[$mainZoneId] : '';
    $swapBdm = ($mainZoneId > 0 && isset($bdmMap[$mainZoneId])) ? $bdmMap[$mainZoneId] : '';
    $swapZone = ($underByBdm > 0 && isset($mainZoneMap[$underByBdm])) ? $mainZoneMap[$underByBdm] : '';

    if ($normalBdm !== '' && $normalZone !== '') {
        $mainZoneNames = array();
        foreach ($mainZoneMap as $name) {
            $mainZoneNames[strtoupper(trim((string)$name))] = true;
        }
        $bdmNames = array();
        foreach ($bdmMap as $name) {
            $bdmNames[strtoupper(trim((string)$name))] = true;
        }
        if (isset($mainZoneNames[strtoupper($normalBdm)]) && isset($bdmNames[strtoupper($normalZone)])) {
            return array(
                'bdm' => $swapBdm !== '' ? $swapBdm : $normalZone,
                'zone' => $swapZone !== '' ? $swapZone : $normalBdm,
            );
        }
        return array('bdm' => $normalBdm, 'zone' => $normalZone);
    }

    if ($swapBdm !== '' && $swapZone !== '') {
        return array('bdm' => $swapBdm, 'zone' => $swapZone);
    }

    return array(
        'bdm' => $normalBdm !== '' ? $normalBdm : ($swapBdm !== '' ? $swapBdm : '-'),
        'zone' => $normalZone !== '' ? $normalZone : ($swapZone !== '' ? $swapZone : '-'),
    );
}
?>

<div class="card-datatable maha-dt-wrap">
<table id="example" class="table table-striped table-bordered">
  <thead>
    <tr>
         <?php if(in_array("10", $Options) || in_array("11", $Options)) { ?>
        <th class="min-col">Action</th>
      <?php } ?>
      <th class="min-col">ID</th>
      <th class="min-col">Franchise ID</th>
     
       <th>Franchise Name</th>
      <th>Shop Name</th>
       <th>Today Sale</th>
       <th class="min-col">Outlet Status</th>
        <th class="min-col">Franchise Type</th>
         <th class="min-col">Space Partner</th>
       <th class="min-col">Under By BDM</th>
        <th>Zone</th>
       <th>Region</th>
      <th>Sub Zone</th>
      <th>State</th>
      <th>City</th>
      
      
     
      <th class="min-col">Sell Amount/Capex Cost <span class="text-danger">*</span></th>
      <th class="min-col">Monthly Rent &amp; Electricity</th>
      <th class="min-col">COFO MRP Share %</th>
      <th class="min-col">COFO Making Share %</th>
      <th class="min-col">FICO MRP Share %</th>
      <th class="min-col">FICO Making Share %</th>
      <th class="min-col">Capex EMI Amount</th>
      <th class="min-col">Capex EMI month &amp; year expiry</th>
      <th class="min-col">Property Revenue share %</th>
      <th class="min-col">NSO Expense show</th>
      <th class="min-col">Salary</th>
      <th class="min-col">Godown Transfer amount</th>
      <th class="min-col">Minimum Purchase Amount For FOFO</th>
      
     
      <th class="min-col">Model Type</th>
      <th class="min-col">Contact No</th>
      <th class="min-col">FSSAI No</th>
      <th>Address</th>
      <th>Pincode</th>
      <th class="min-col">Status</th>
      <th class="min-col">Opening Date</th>
       <th class="min-col">Opening Time</th>
      <th class="min-col">Closing Time</th>
       <th class="min-col">Reporting Manager</th>
     
      <th class="min-col">Latitude</th>
      <th class="min-col">Longitude</th>
      <th class="min-col">Zomato/Swiggy</th>
      <th class="min-col">Alliance Partner Name</th>
      <th class="min-col">Alliance Partner Phone</th>
      <th class="min-col">Alliance Partner Email</th>
      <th class="min-col">Commission %</th>
     
    </tr>
  </thead>
  <tbody>
  <?php 
    
      $sql = "SELECT tu.*, tut.Name AS VcUserType, tu2.Fname AS VcManager,
              tc.Name AS VcModelType, ts.Name AS VcStateName, tc2.Name AS VcCityName
              FROM tbl_users tu 
              LEFT JOIN tbl_user_type tut ON tu.UserType=tut.id 
              LEFT JOIN tbl_users tu2 ON tu2.id=tu.UnderUser 
              LEFT JOIN tbl_common_master tc ON tu.ModelType=tc.id 
              LEFT JOIN tbl_state ts ON ts.id=tu.StateId 
              LEFT JOIN tbl_city tc2 ON tc2.id=tu.CityId 
              WHERE tu.Roll=5 AND tu.id!=8757";
   
    if (!empty($_POST['MainZoneId']) && $_POST['MainZoneId'] !== 'all') {
        $sql .= " AND tu.MainZoneId='" . mysqli_real_escape_string($conn, $_POST['MainZoneId']) . "'";
    }
    if (!empty($_POST['ZoneId']) && $_POST['ZoneId'] !== 'all') {
        $sql .= " AND tu.ZoneId='" . mysqli_real_escape_string($conn, $_POST['ZoneId']) . "'";
    }
    if (!empty($_POST['SubZoneId']) && $_POST['SubZoneId'] !== 'all') {
        $sql .= " AND tu.SubZoneId='" . mysqli_real_escape_string($conn, $_POST['SubZoneId']) . "'";
    }
    if (!empty($_POST['OwnFranchise']) && $_POST['OwnFranchise'] !== 'all') {
        $sql .= " AND tu.OwnFranchise='" . mysqli_real_escape_string($conn, $_POST['OwnFranchise']) . "'";
    }
    if (!empty($_POST['FrId']) && $_POST['FrId'] !== 'all') {
        $sql .= " AND tu.id='" . mysqli_real_escape_string($conn, $_POST['FrId']) . "'";
    }

    $sql .= " ORDER BY tu.id DESC";
    $res = $conn->query($sql);
    $allRows = array();
    if ($res) {
        while ($tmp = $res->fetch_assoc()) {
            $allRows[] = $tmp;
        }
    }

    $mainZoneMap = array();
    $zoneMap = array();
    $subZoneMap = array();
    $bdmMap = array();

    $mainZoneIds = array_unique(array_filter(array_merge(
        array_map('intval', array_column($allRows, 'MainZoneId')),
        array_map('intval', array_column($allRows, 'UnderByBdm'))
    )));
    $zoneIds = array_unique(array_filter(array_map('intval', array_column($allRows, 'ZoneId'))));
    $subZoneIds = array_unique(array_filter(array_map('intval', array_column($allRows, 'SubZoneId'))));
    $bdmIds = array_unique(array_filter(array_merge(
        array_map('intval', array_column($allRows, 'UnderByBdm')),
        array_map('intval', array_column($allRows, 'MainZoneId'))
    )));

    if (!empty($mainZoneIds)) {
        $rows = getList("SELECT id, Name FROM tbl_main_zone WHERE id IN (" . implode(',', $mainZoneIds) . ")");
        if (is_array($rows)) {
            foreach ($rows as $item) {
                $mainZoneMap[(int)$item['id']] = $item['Name'];
            }
        }
    }
    if (!empty($zoneIds)) {
        $rows = getList("SELECT id, Name FROM tbl_zone WHERE id IN (" . implode(',', $zoneIds) . ")");
        if (is_array($rows)) {
            foreach ($rows as $item) {
                $zoneMap[(int)$item['id']] = $item['Name'];
            }
        }
    }
    if (!empty($subZoneIds)) {
        $rows = getList("SELECT id, Name FROM tbl_sub_zone WHERE id IN (" . implode(',', $subZoneIds) . ")");
        if (is_array($rows)) {
            foreach ($rows as $item) {
                $subZoneMap[(int)$item['id']] = $item['Name'];
            }
        }
    }
    if (!empty($bdmIds)) {
        $rows = getList("SELECT id, Fname FROM tbl_users WHERE id IN (" . implode(',', $bdmIds) . ")");
        if (is_array($rows)) {
            foreach ($rows as $item) {
                $bdmMap[(int)$item['id']] = $item['Fname'];
            }
        }
    }

    foreach ($allRows as $row) {
        $zoneBdm = view_customers_resolve_bdm_zone($row, $mainZoneMap, $bdmMap);
        $ZomatoSwiggy = $row['ZomatoSwiggy'];
      $sellDate = $row['SellDate'] ? date("d/m/Y", strtotime(str_replace('-', '/',$row['SellDate']))) : '-';
       $row23 = getRecord("SELECT GROUP_CONCAT(Name) AS Zomato FROM tbl_common_master WHERE id IN($ZomatoSwiggy)");
      
      $row33 = getRecord("SELECT SUM(NetAmount) AS TotalSale FROM tbl_customer_invoice_2025 WHERE FrId='".$row['id']."' AND InvoiceDate='".date('Y-m-d')."'");
  ?>
    <tr>
         <?php if(in_array("10", $Options) || in_array("11", $Options)) { ?>
     <td>
               <?php if(in_array("10", $Options)){?>
              <a href="add-customer.php?id=<?php echo $row['id']; ?>"><i class="lnr lnr-pencil mr-2"></i></a>&nbsp;&nbsp;
            <?php } if(in_array("11", $Options)){?>
              <a onClick="return confirm('Are you sure you want delete this customer account?\nNote : Delete all record related this customer (Y/N)');" href="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $row['id']; ?>&action=delete"><i class="lnr lnr-trash text-danger"></i></a>
             <?php } ?>
            </td>
      <?php } ?>
      <td class="nowrap"><?php echo htmlspecialchars($row['id']); ?></td>
      <td class="nowrap"><?php echo htmlspecialchars($row['CustomerId']); ?></td>
      
      <td>
        <a class="font-weight-bold text-primary" href="franchise-details.php?id=<?php echo (int)$row['id']; ?>" target="_blank" rel="noopener noreferrer">
          <?php echo htmlspecialchars(trim($row['Fname']." ".$row['Lname'])); ?>
        </a>
      </td>
      <td><?php echo htmlspecialchars($row['ShopName']); ?></td>
       <td><?php echo $row33['TotalSale'];?></td>
      <td><?php echo htmlspecialchars($row['OutletStatus']); ?></td>
       <td class="text-center"><?php echo franchiseBadge($row['OwnFranchise'], $frTypeNames); ?></td>
         <td><?php echo htmlspecialchars($row['SpacePartner']); ?></td>
       <td class="nowrap"><?php echo htmlspecialchars($zoneBdm['bdm']); ?></td>
       <td><?php echo htmlspecialchars($zoneBdm['zone']); ?></td>
      <td><?php echo htmlspecialchars($zoneMap[(int)$row['ZoneId']] ?? '-'); ?></td>
      <td><?php echo htmlspecialchars($subZoneMap[(int)$row['SubZoneId']] ?? '-'); ?></td>
      <td><?php echo htmlspecialchars($row['VcStateName'] ?? ''); ?></td>
      <td><?php echo htmlspecialchars($row['VcCityName'] ?? ''); ?></td>
     
      
      
      <td class="text-right font-weight-semibold"><?php echo round($row['SellAmt'] !== '' && $row['SellAmt'] !== null ? $row['SellAmt'] : '-'); ?></td>
      <td class="text-right font-weight-semibold"><?php echo round($row['MonthlyRent']); ?></td>
      <?php
      $u2 = getRecord("SELECT * FROM tbl_users2 WHERE UserId='" . intval($row['id']) . "' LIMIT 1");
      if (!is_array($u2)) {
          $u2 = array();
      }
      $capexExpiry = '';
      if (!empty($u2['capex_emi_expiry']) && $u2['capex_emi_expiry'] !== '0000-00-00') {
          $capexExpiry = htmlspecialchars(substr($u2['capex_emi_expiry'], 0, 7));
      }
      ?>
      <td class="text-right"><?php echo round(isset($u2['mrp_share_percent']) && $u2['mrp_share_percent'] !== '' && $u2['mrp_share_percent'] !== null ? htmlspecialchars($u2['mrp_share_percent']) : '-'); ?></td>
      <td class="text-right"><?php echo round(isset($u2['making_share_percent']) && $u2['making_share_percent'] !== '' && $u2['making_share_percent'] !== null ? htmlspecialchars($u2['making_share_percent']) : '-'); ?></td>
      <td class="text-right"><?php echo round(isset($u2['mrp_share_fico_percent']) && $u2['mrp_share_fico_percent'] !== '' && $u2['mrp_share_fico_percent'] !== null ? htmlspecialchars($u2['mrp_share_fico_percent']) : '-'); ?></td>
      <td class="text-right"><?php echo round(isset($u2['making_share_fico_percent']) && $u2['making_share_fico_percent'] !== '' && $u2['making_share_fico_percent'] !== null ? htmlspecialchars($u2['making_share_fico_percent']) : '-'); ?></td>
      <td class="text-right"><?php echo round(isset($u2['ho_cost_amount']) && $u2['ho_cost_amount'] !== '' && $u2['ho_cost_amount'] !== null ? $u2['ho_cost_amount'] : '-'); ?></td>
      <td class="nowrap"><?php echo $capexExpiry !== '' ? $capexExpiry : '-'; ?></td>
      <td class="text-right"><?php echo round(isset($u2['revenue_share_percent']) && $u2['revenue_share_percent'] !== '' && $u2['revenue_share_percent'] !== null ? htmlspecialchars($u2['revenue_share_percent']) : '-'); ?></td>
      <td class="nowrap"><?php echo isset($u2['nso_expense_show']) ? view_customers_tri_state_label($u2['nso_expense_show']) : '-'; ?></td>
      <td class="nowrap"><?php echo isset($u2['salary_show']) ? view_customers_tri_state_label($u2['salary_show']) : '-'; ?></td>
      <td class="nowrap"><?php echo isset($u2['godown_transfer_show']) ? view_customers_tri_state_label($u2['godown_transfer_show']) : '-'; ?></td>
      <td class="text-right"><?php echo round(isset($u2['min_purchase_amount_fofo']) && $u2['min_purchase_amount_fofo'] !== '' && $u2['min_purchase_amount_fofo'] !== null ? $u2['min_purchase_amount_fofo'] : '-'); ?></td>
     
      <td><?php echo htmlspecialchars($row['VcModelType'] ?? ''); ?></td>
      <td class="nowrap">
    <?php 
        $phones = array_filter([trim($row['Phone']), trim($row['Phone2'])]);
        echo $phones ? implode("<br>", array_map('htmlspecialchars', $phones)) : '-';
    ?>
</td>
      <td class="nowrap"><code><?php echo htmlspecialchars($row['FssaiNo']); ?></code></td>
      <td><div class="text-truncate-2" title="<?php echo htmlspecialchars($row['Address']); ?>">
        <?php echo htmlspecialchars($row['Address']); ?></div>
      </td>
      <td class="nowrap"><?php echo htmlspecialchars($row['Pincode']); ?></td>
      <td class="text-center"><?php echo statusBadge($row['Status']); ?></td>
      <td class="nowrap"><?php echo $sellDate; ?></td>
      <td class="nowrap"><?php echo htmlspecialchars($row['OpenTime']); ?></td>
      <td class="nowrap"><?php echo htmlspecialchars($row['CloseTime']); ?></td>
      <td class="nowrap"><?php echo htmlspecialchars($row['VcManager'] ?? ''); ?></td>
     
      <td class="nowrap"><?php echo view_customers_map_link($row['Lattitude'], $row['Longitude'], $row['Lattitude']); ?></td>
      <td class="nowrap"><?php echo view_customers_map_link($row['Lattitude'], $row['Longitude'], $row['Longitude']); ?></td>
<td class="nowrap"><?php echo htmlspecialchars($row23['Zomato']); ?></td>
      <td class="nowrap"><?php echo htmlspecialchars($row['AlianceName'] ?? ''); ?></td>
      <td class="nowrap"><?php echo htmlspecialchars($row['AliancePhone'] ?? ''); ?></td>
      <td class="nowrap"><?php echo htmlspecialchars($row['AlianceEmailId'] ?? ''); ?></td>
      <td class="nowrap"><?php echo htmlspecialchars($row['AliancePer'] ?? ''); ?></td>
     
    </tr>
  <?php } ?>
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

<script type="text/javascript">
 function chageSurveyDetails(val,id){
   var action = "chageSurveyDetails";
            $.ajax({
                url: "ajax_files/ajax_customer_account.php",
                method: "POST",
                data: {
                    action: action,
                    id: id,
                    val:val
                },
                success: function(data) {
                    alert("Survey Details Changed.");
                  
                }
            });
 }
    $(document).ready(function() {
    var customerTable = $('#example').DataTable({
        scrollX: false,
        responsive: false,
        autoWidth: true,
        dom: 'Bfrtip',
        order: [[<?php echo (in_array("10", $Options) || in_array("11", $Options)) ? '1' : '0'; ?>, 'desc']],
        buttons: [
            'excelHtml5'
        ]
    });

    customerTable.columns.adjust().draw(false);
    $(window).on('resize', function() {
        customerTable.columns.adjust();
    });
});
</script>
</body>
</html>
