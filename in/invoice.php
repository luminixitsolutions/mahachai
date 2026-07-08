<?php 
session_start();
include_once 'config.php';
//include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = "Customers";
$Page = "View-Customers";
?>
<!DOCTYPE html>
<html lang="en">

<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>
    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Laralink">
    <title>Receipt Invoice</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&amp;display=swap');

    *,
    ::after,
    ::before {
        box-sizing: border-box;
    }

    body {
        color: #000;
        font-size: 12px;
        font-weight: 400;
        line-height: 1.4em;
        margin: 0;
        font-family: 'Inter', sans-serif;
        background-color: #f5f6fa;
    }
    body {color:black;}

    .tm_pos_invoice_wrap {
        max-width: 340px;
        margin: auto;
        margin-top: 30px;
        padding: 30px 20px;
        background-color: #fff;
    }

    .tm_pos_company_logo {
        display: flex;
        justify-content: center;
        margin-bottom: 7px;
    }

    .tm_pos_company_logo img {
        vertical-align: middle;
        border: 0;
        max-width: 100%;
        height: auto;
        max-height: 80px;
    }

    .tm_pos_invoice_top {
        text-align: center;
        margin-bottom: 18px;
    }

    .tm_pos_invoice_heading {
        display: flex;
        justify-content: center;
        position: relative;
        text-transform: uppercase;
        font-size: 12px;
        font-weight: 500;
        margin: 10px 0;
    }

    .tm_pos_invoice_heading:before {
        content: '';
        position: absolute;
        height: 0;
        width: 100%;
        left: 0;
        top: 46%;
        border-top: 1px dashed #666;
    }

    .tm_pos_invoice_heading span {
        display: inline-flex;
        padding: 0 5px;
        background-color: #fff;
        z-index: 1;
        font-weight: 500;
    }

    .tm_list.tm_style1 {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
    }

    .tm_list.tm_style1 li {
        display: flex;
        width: 50%;
        font-size: 12px;
        line-height: 1.2em;
        margin-bottom: 7px;
    }

    .text-right {
        text-align: right;
        justify-content: flex-end;
    }

    .tm_list_title {
        color: #111;
        margin-right: 4px;
        font-weight: 500;
    }

    .tm_invoice_seperator {
        width: 150px;
        border-top: 1px dashed #666;
        margin: 9px 0;
        margin-left: auto;
    }

    .tm_pos_invoice_table {
        width: 100%;
        margin-top: 10px;
        line-height: 1.3em;
    }

    .tm_pos_invoice_table thead th {
        font-weight: 500;
        color: #111;
        text-align: left;
        padding: 8px 3px;
        border-top: 1px dashed #666;
        border-bottom: 1px dashed #666;
    }

    .tm_pos_invoice_table td {
        padding: 4px;
    }

    .tm_pos_invoice_table tbody tr:first-child td {
        padding-top: 10px;
    }

    .tm_pos_invoice_table tbody tr:last-child td {
        padding-bottom: 10px;
        border-bottom: 1px dashed #666;
    }

    .tm_pos_invoice_table th:last-child,
    .tm_pos_invoice_table td:last-child {
        text-align: right;
        padding-right: 0;
    }

    .tm_pos_invoice_table th:first-child,
    .tm_pos_invoice_table td:first-child {
        padding-left: 0;
    }

    .tm_pos_invoice_table tr {
        vertical-align: baseline;
    }

    .tm_bill_list {
        list-style: none;
        margin: 0;
        padding: 12px 0;
        border-bottom: 1px dashed #666;
    }

    .tm_bill_list_in {
        display: flex;
        text-align: right;
        justify-content: flex-end;
        padding: 3px 0;
    }

    .tm_bill_title {
        padding-right: 20px;
    }

    .tm_bill_value {
        width: 90px;
    }

    .tm_bill_value.tm_bill_focus,
    .tm_bill_title.tm_bill_focus {
        font-weight: 500;
        color: #111;
    }

    .tm_pos_invoice_footer {
        text-align: center;
        margin-top: 20px;
    }

    .tm_pos_sample_text {
        text-align: center;
        padding: 12px 0;
        border-bottom: 1px dashed #666;
        line-height: 1.6em;
        color: #9c9c9c;
    }

    .tm_pos_company_name {
        font-weight: 500;
        color: #111;
        font-size: 13px;
        line-height: 1.4em;
    }
    @media print {
  #printPageButton {
    display: none;
  }
}
    </style>
</head>
<?php 
if (!empty($_GET['Unqid'])) {
    $BillNo = $_GET['Unqid'];
} elseif (!empty($_GET['pageurl'])) {
    $BillNo = $_GET['pageurl'];
} else {
    $BillNo = '';
}
$BillNo = mysqli_real_escape_string($conn, $BillNo);
$sql = "SELECT ti.* FROM tbl_customer_invoice_2025 ti LEFT JOIN tbl_users tu ON ti.CustId=tu.id WHERE ti.Unqid='$BillNo'";
$row = getRecord($sql);
if (empty($row)) {
    die('Invoice not found.');
}
$number = $row['NetAmount'];
$id = $row['id'];
include_once 'convert_currancy.php';

 $sql55 = "SELECT * FROM tbl_users WHERE id=5";
            $row55 = getRecord($sql55);

?>
<body>
    <div class="tm_pos_invoice_wrap">
        <div class="tm_pos_invoice_top">
           
            <div class="tm_pos_company_logo">
              <img src="https://mahabuddy.com/billsoftapp/smsdata/logoin.jpg">                
            </div>
           
           
            <div class="tm_pos_company_name"><?php echo $row55['ShopName'];?></div>
            <div class="tm_pos_company_address"><?php echo $row55['Address'];?></div>
            <div class="tm_pos_company_mobile">Mobile: <?php echo $row55['Phone'];?></div>
            <div class="tm_pos_company_mobile">GSTIN: <?php echo $row55['GstNo'];?></div>
            <div class="tm_pos_company_mobile">CIN NO: 12345</div>
           
        </div>
        <div class="tm_pos_invoice_body">
            <div class="tm_pos_invoice_heading"><span>Receipt</span></div>
            <ul class="tm_list tm_style1">
                <li>
                    <div class="tm_list_title">Name:</div>
                    <div class="tm_list_desc"><?php echo $row['CustName'];?></div>
                </li>
                </ul>
                 <ul class="tm_list tm_style1">
                <li>
                    <div class="tm_list_title">Phone No.:</div>
                    <div class="tm_list_desc"><?php echo $row['CellNo'];?></div>
                </li>
                </ul>
                 <ul class="tm_list tm_style1">
                <li>
                    <div class="tm_list_title">Invoice No:</div>
                    <div class="tm_list_desc"><?php echo $row['InvoiceNo'];?></div>
                </li>
                <li class="text-right">
                    <div class="tm_list_title">Pay Mode:</div>
                    <div class="tm_list_desc"><?php echo $row['PayType'];?></div>
                </li>
                <li>
                    <div class="tm_list_title">Date:</div>
                    <div class="tm_list_desc"><?php echo date("d/m/Y", strtotime(str_replace('-', '/',$row['InvoiceDate']))); ?></div>
                </li>
                
                <li class="text-right">
                    <div class="tm_list_title">Time:</div>
                    <div class="tm_list_desc"><?php echo $row['CreatedTime']; ?></div>
                </li>
            </ul>
            <table class="tm_pos_invoice_table">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php 
            $i=1;
            $sql12 = "SELECT * FROM tbl_customer_invoice_details_2025 WHERE ServerInvId='$BillNo'";
            $row12 = getList($sql12);
            foreach($row12 as $result12){
                $sql13 = "SELECT ProductName FROM  tbl_cust_products_2025 WHERE id='".$result12['ProdId']."'";
                $row13 = getRecord($sql13);
                $TotQty+=$result12['Qty'];
                $TotPrice+=$result12['Price'];
                $TotGst+=$result12['GstAmt'];
        ?>
                    <tr>
                        <td><?php echo $i;?>.</td>
                        <td><?php echo $row13['ProductName'];?></td>
                        <td>₹<?php echo $result12['Price'];?></td>
                        <td><?php echo $result12['Qty'];?></td>
                        <td>₹<?php echo $result12['Total'];?></td>
                    </tr>
                    <?php $i++;} ?>
                    <!--<tr>
                        <td>2.</td>
                        <td>Maha Pavbhaji</td>
                        <td>₹30</td>
                        <td>2</td>
                        <td>₹60</td>
                    </tr>
                    <tr>
                        <td>3.</td>
                        <td>Sabudana Vada Premix 250 Gms</td>
                        <td>₹100</td>
                        <td>3</td>
                        <td>₹300</td>
                    </tr>-->
                   
                    
                </tbody>
            </table>
            <div class="tm_bill_list">
                <div class="tm_bill_list_in">
                    <div class="tm_bill_title">Sub-Total:</div>
                    <div class="tm_bill_value">₹<?php echo $row['SubTotal']-$TotGst;?></div>
                </div>
                <div class="tm_bill_list_in">
                    <div class="tm_bill_title">Discount: </div>
                    <div class="tm_bill_value">-₹<?php echo $row['Discount'];?></div>
                </div>
                <?php if($row['PrimeDiscount'] > 0){?>
                <div class="tm_bill_list_in">
                    <div class="tm_bill_title">Prime Discount:</div>
                    <div class="tm_bill_value">-₹<?php echo $row['PrimeDiscount'];?></div>
                </div>
                <?php } ?>
                <div class="tm_invoice_seperator"></div>
                <!--<div class="tm_bill_list_in">
                    <div class="tm_bill_title">Service charge:</div>
                    <div class="tm_bill_value">₹0.00</div>
                </div>-->
                <div class="tm_bill_list_in">
                    <div class="tm_bill_title">CGST 2.5% & SGST 2.5%:</div>
                    <div class="tm_bill_value">₹<?php echo $TotGst;?></div>
                </div>
                
                <div class="tm_invoice_seperator"></div>
                <div class="tm_bill_list_in">
                    <div class="tm_bill_title">Total Bill:</div>
                    <div class="tm_bill_value">₹<?php echo $row['NetAmount'];?></div>
                </div>
               <!-- <div class="tm_bill_list_in">
                    <div class="tm_bill_title">Due :</div>
                    <div class="tm_bill_value">₹<?php echo $row['Balance'];?></div>
                </div>-->
                <!--<div class="tm_bill_list_in">
                    <div class="tm_bill_title tm_bill_focus">Total payable:</div>
                    <div class="tm_bill_value tm_bill_focus">₹<?php echo $row['Advance'];?></div>
                </div>-->
            </div>
         
            <div class="tm_pos_sample_text"> <?php echo $row55['terms_condition'];?></div>
            <div class="tm_pos_invoice_footer">
                <?php echo $row55['bottom_title'];?><br>Registered office<br>FSSAI No<br>
            *Inclusive Of GST</div>
            
           <br>
            <div align="center"><button id="printPageButton" onClick="window.print();">Print</button></div>
        </div>
    </div>
    
    
</body>

</html>