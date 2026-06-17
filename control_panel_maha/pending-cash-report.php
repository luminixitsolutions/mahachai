<?php 
session_start();
include_once 'config.php';
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "Cash-Book";
$Page = "Franchise-Outstanding";
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
</head>
<body>

 <div class="layout-wrapper layout-1 layout-without-sidenav">
<div class="layout-inner">

 <?php include_once 'top_header.php'; include_once 'sidebar.php'; ?>


<div class="layout-container">





<div class="layout-content">

<div class="container-fluid flex-grow-1 container-p-y">
<h4 class="font-weight-bold py-3 mb-0">Pending Cash Report
  
</h4>

<div class="card" style="padding: 10px;">
    
     <div id="accordion2">
<div class="card mb-2">
                                        
                                        <div id="accordion2-2" class="collapse show" data-parent="#accordion2">
                                            <div class="" style="padding:5px;">
                                                <form id="validation-form" method="post" enctype="multipart/form-data" action="">
<div class="form-row">

       

  <div class="form-group col-md-4">
<label class="form-label"> Franchise<span class="text-danger">*</span></label>
 <select class="select2-demo form-control" name="UserId" id="UserId" required>
<option selected="" value="all">All</option>

 <?php 
  $sql12 = "SELECT * FROM tbl_users_bill WHERE Roll=5 AND Status=1";
  $row12 = getList($sql12);
  foreach($row12 as $result){
     ?>
  <option <?php if($_REQUEST["UserId"] == $result['id']) {?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['ShopName']." (".$result['Phone'].")"; ?></option>
<?php } ?>
</select>
<div class="clearfix"></div>
</div>

<!--<div class="form-group col-md-3">
<label class="form-label">From Date </label>
<input type="date" name="FromDate" id="FromDate" class="form-control" value="<?php echo $_POST['FromDate'] ?>" autocomplete="off">
</div>
<div class="form-group col-md-3">
<label class="form-label">To Date</label>
<input type="date" name="ToDate" id="ToDate" class="form-control" value="<?php echo $_POST['ToDate'] ?>" autocomplete="off">
</div>-->
<input type="hidden" name="Search" value="Search">
<div class="form-group col-md-1">
    <label class="form-label">&nbsp;</label>
<button type="submit" name="submit" class="btn btn-primary btn-finish">Search</button>
</div>
<?php if($_REQUEST['Search'] == 'Search') {?>
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
    <?php
    function calAmount($frid,$val){
        global $conn;
        //total
        if($val == 'totalincome'){
        $sql = "SELECT SUM(Result) AS Result FROM (SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice WHERE FrId='$frid' UNION ALL SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice_2025 WHERE FrId='$frid') as a";
        }
        if($val == 'yesterdayincome'){
        $sql = "SELECT SUM(Result) AS Result FROM (SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice WHERE FrId='$frid' AND InvoiceDate='".date('Y-m-d',strtotime("-1 days"))."' UNION ALL SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice_2025 WHERE FrId='$frid' AND InvoiceDate='".date('Y-m-d',strtotime("-1 days"))."') as a";
        }
        if($val == 'todayincome'){
        $sql = "SELECT SUM(Result) AS Result FROM (SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice WHERE FrId='$frid' AND InvoiceDate='".date('Y-m-d')."' UNION ALL SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice_2025 WHERE FrId='$frid' AND InvoiceDate='".date('Y-m-d')."') as a";
        }
        
        //cash
        if($val == 'totalcashincome'){
        $sql = "SELECT SUM(Result) AS Result FROM (SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice WHERE FrId='$frid' AND PayType='Cash' UNION ALL SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice_2025 WHERE FrId='$frid' AND PayType='Cash' ) as a";
        }
        if($val == 'yesterdaycashincome'){
        $sql = "SELECT SUM(Result) AS Result FROM (SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice WHERE FrId='$frid' AND InvoiceDate='".date('Y-m-d',strtotime("-1 days"))."' AND PayType='Cash' UNION ALL SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice_2025 WHERE FrId='$frid' AND InvoiceDate='".date('Y-m-d',strtotime("-1 days"))."' AND PayType='Cash') as a";
        }
        if($val == 'todaycashincome'){
        $sql = "SELECT SUM(Result) AS Result FROM (SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice WHERE FrId='$frid' AND InvoiceDate='".date('Y-m-d')."' AND PayType='Cash' UNION ALL SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice_2025 WHERE FrId='$frid' AND InvoiceDate='".date('Y-m-d')."' AND PayType='Cash') as a";
        }
        
        //upi
        if($val == 'totalupiincome'){
        $sql = "SELECT SUM(Result) AS Result FROM (SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice WHERE FrId='$frid' AND PayType IN ('Phone Pay','UPI','Paytm','Other UPI','Online Payment') UNION ALL SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice_2025 WHERE FrId='$frid' AND PayType IN ('Phone Pay','UPI','Paytm','Other UPI','Online Payment')) as a";
        }
        if($val == 'yesterdayupiincome'){
        $sql = "SELECT SUM(Result) AS Result FROM (SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice WHERE FrId='$frid' AND InvoiceDate='".date('Y-m-d',strtotime("-1 days"))."' AND PayType IN ('Phone Pay','UPI','Paytm','Other UPI','Online Payment') UNION ALL SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice_2025 WHERE FrId='$frid' AND InvoiceDate='".date('Y-m-d',strtotime("-1 days"))."' AND PayType IN ('Phone Pay','UPI','Paytm','Other UPI','Online Payment')) as a";
        }
        if($val == 'todayupiincome'){
        $sql = "SELECT SUM(Result) AS Result FROM (SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice WHERE FrId='$frid' AND InvoiceDate='".date('Y-m-d')."' AND PayType IN ('Phone Pay','UPI','Paytm','Other UPI','Online Payment') UNION ALL SELECT SUM(NetAmount) AS Result FROM tbl_customer_invoice_2025 WHERE FrId='$frid' AND InvoiceDate='".date('Y-m-d')."' AND PayType IN ('Phone Pay','UPI','Paytm','Other UPI','Online Payment')) as a";
        }
        
        //transfercash
        if($val == 'transfercash'){
        $sql = "SELECT SUM(Amount) AS Result FROM tbl_cash_book WHERE FrId='$frid' AND ApproveStatus='1' ";
        }
        
        $res = $conn->query($sql);
	    $row = $res->fetch_assoc();
	    if($row['Result']==''){
	        $Result = 0;
	    }
	    else{
	        $Result = $row['Result'];
	    }
	    return $Result;
    }
    ?>
<div class="card-datatable maha-dt-wrap">
<table id="example" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Fr Id</th>
                <th>Franchise Name</th>
                <th>Zone</th>
                <th>Sub Zone</th>
                <th>BDM Name</th>
                <th>Balance Cash</th>
                <th>Last Deposite Date</th>
                <th>Day From Last Cash Deposite</th>
                <th>Penalty amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
           
            $i=1;
            $sql = "SELECT tu.id, tu.ShopName, tu.MainZoneId, tu.ZoneId, tu.SubZoneId, tu.UnderByBdm,
                    mz.Name AS ZoneName, tsz.Name AS SubZoneName, bdm.Fname AS BdmName
                    FROM tbl_users tu
                    LEFT JOIN tbl_main_zone mz ON mz.id = tu.MainZoneId
                    LEFT JOIN tbl_sub_zone tsz ON tsz.id = tu.SubZoneId
                    LEFT JOIN tbl_users bdm ON bdm.id = tu.UnderByBdm
                    WHERE tu.Roll = 5 AND tu.Status = 1";
             if($_REQUEST['UserId']){
                $ExeId = $_REQUEST['UserId'];
                if($ExeId == 'all'){
                    $sql.= " ";
                }
                else{
                $sql.= " AND tu.id='" . mysqli_real_escape_string($conn, $ExeId) . "'";
                }
            }
           
            $sql.=" ORDER BY tu.id";
            //echo $sql;
            $res = $conn->query($sql);
            while($row = $res->fetch_assoc())
            {
                if($row['id'] == 5){
                    $frid = 0;
                }
                else{
                    $frid = $row['id'];
                }
                
                $sql2 = "SELECT TransferDate FROM tbl_cash_book WHERE FrId='$frid' AND TransferDate is not null ORDER BY TransferDate DESC LIMIT 1";
                $rncnt2 = getRow($sql2);
                $row2 = getRecord($sql2);
                
                $balcash = calAmount($frid,'totalcashincome') - calAmount($frid,'transfercash');
                $sql3 = "SELECT Fname,Phone FROM tbl_users WHERE Roll=29 AND UnderFrId='$frid' AND Status=1 LIMIT 1";
                $row3 = getRecord($sql3);
                $Phone = $row3['Phone'];  
                
                $date1 = $row2['TransferDate'];
$date2 = date('Y-m-d');

$diff = strtotime($date2) - strtotime($date1);

// 1 day = 86400 seconds
$days = round($diff / 86400);

if($rncnt2 > 0) {
    $interval = $days;
    if($interval > 2){
    $penaltyamt = ($interval - 2)*100;
    $smstxt = "Dear ".$row3['Fname']." , please deposit ".$balcash." today which is showing as pending in Kwick bill, Avoid penalty of per day 100 by ensuring timely deposit. - Maha Chai";
  $dltentityid = "1501701120000037351";
  $dlttempid = "1707174462502393526";
 include '../incsmsapi.php';  
    }
    else{
        $penaltyamt = 0;
    }
}
else{
    $interval = 0;$penaltyamt = 0;
}



 
  
 
             ?>
            <tr>
                <td><?php echo (int) $frid; ?></td>
                <td><?php echo htmlspecialchars($row['ShopName'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['ZoneName'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($row['SubZoneName'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($row['BdmName'] ?? ($row3['Fname'] ?? '-')); ?></td>
                <td><?php echo number_format(calAmount($frid,'totalcashincome') - calAmount($frid,'transfercash'), 2); ?></td>
                <td><?php if ($rncnt2 > 0) { echo date('d-m-Y', strtotime($row2['TransferDate'])); } ?></td>
                <td><?php echo (int) $interval; ?></td>
                <td><?php echo (int) $penaltyamt; ?></td>
            </tr>
           <?php $i++;} ?>
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

    $(document).ready(function() {
    var pendingCashTable = $('#example').DataTable({
        scrollX: false,
        responsive: false,
        autoWidth: true,
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5'
        ],
        order: [[0, 'asc']]
    });
    pendingCashTable.columns.adjust().draw(false);
    $(window).on('resize', function() {
        pendingCashTable.columns.adjust();
    });
});
</script>
</body>
</html>
