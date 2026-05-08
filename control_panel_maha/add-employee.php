<?php
// config must load before session_start() so session_name (MAHACPSESSID) matches login; otherwise
// a default PHPSESSID session is used and $_SESSION['Admin'] from login is missing.
include_once 'config.php';
session_start();
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage="Employee";
$Page = "Add-Employee";
?>
<!DOCTYPE html>
<html lang="en" class="default-style">

<head>
    <title><?php echo $Proj_Title; ?> - <?php if($_GET['id']) {?>Edit <?php } else{?> Add <?php } ?> Employee Account
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
        font-size: 14px;
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 2;
        line-height: 1;
    }

    .employee-pw-policy-icon {
        cursor: pointer;
        font-size: 16px;
        vertical-align: middle;
        padding: 0 2px;
        border: none;
        background: transparent;
        line-height: 1;
    }

    .employee-pw-policy-icon:focus {
        outline: none;
    }

    #Password {
        padding-right: 40px !important;
    }

    fieldset legend {
        background: inherit;
        font-family: "Lato", sans-serif;
        color: #650812;
        font-size: 15px;
        left: 10px;
        padding: 0 10px;
        position: absolute;
        top: -12px;
        font-weight: 400;
        width: auto !important;
        border: none !important;
    }

    fieldset {
        background: #ffffff;
        border: 1px solid #4FAFB8;
        border-radius: 5px;
        margin: 20px 0 1px 0;
        padding: 20px;
        position: relative;
    }
    #loader {
    display: none;
    position: fixed;
    z-index: 9999;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(255, 255, 255, 0.7);
    text-align: center;
    padding-top: 20%;
    font-size: 24px;
    font-weight: bold;
    color: #333;
}
    </style>
     <div class="layout-wrapper layout-1 layout-without-sidenav">
        <div class="layout-inner">

             <?php include_once 'top_header.php'; include_once 'sidebar.php'; ?>


            <div class="layout-container">

                

                <?php 
$id = $_GET['id'];
$sql7 = "SELECT * FROM tbl_users WHERE id='$id'";
$row7 = getRecord($sql7);
$row7['Options'] = explode(',', $row7['Options2']);
$row7['ExpCatId'] = explode(',', $row7['ExpCatId']);
$row7['CocoFranchiseAccess'] = explode(',', $row7['CocoFranchiseAccess']);
$row7['AssignFranchiseAttendance'] = explode(',', $row7['AssignFranchiseAttendance']);
$row7['AssignFranchiseVedExp'] = explode(',', $row7['AssignFranchiseVedExp']);
$row7['AssignFranchiseBdm'] = explode(',', $row7['AssignFranchiseBdm']);
$row7['AssignFranchiseNsoVedExp'] = explode(',',$row7['AssignFranchiseNsoVedExp']);
$row7['zone'] = explode(',', $row7['zone']);


$row7['vedzones'] = explode(',', $row7['vedzones']);
$row7['nsovedzones'] = explode(',', $row7['nsovedzones']);
$row7['vedSubzones'] = explode(',', $row7['vedSubzones']);
$row7['nsovedSubzones'] = explode(',', $row7['nsovedSubzones']);

$sql71 = "SELECT * FROM tbl_users2 WHERE UserId='$id'";
$row71 = getRecord($sql71);
if (!is_array($row71)) {
    $row71 = array();
}
$row71['cpsubzones'] = array_filter(array_map('trim', explode(',', isset($row71['cpsubzones']) ? (string) $row71['cpsubzones'] : '')));
$row71['cpzones'] = array_filter(array_map('trim', explode(',', isset($row71['cpzones']) ? (string) $row71['cpzones'] : '')));
$row71['cpfranchise'] = array_filter(array_map('trim', explode(',', isset($row71['cpfranchise']) ? (string) $row71['cpfranchise'] : '')));
$row71['att_zones'] = array_filter(array_map('trim', explode(',', isset($row71['att_zones']) ? (string) $row71['att_zones'] : '')));
$row71['att_subzones'] = array_filter(array_map('trim', explode(',', isset($row71['att_subzones']) ? (string) $row71['att_subzones'] : '')));
$row71['bdm_zones'] = array_filter(array_map('trim', explode(',', isset($row71['bdm_zones']) ? (string) $row71['bdm_zones'] : '')));
$row71['bdm_subzones'] = array_filter(array_map('trim', explode(',', isset($row71['bdm_subzones']) ? (string) $row71['bdm_subzones'] : '')));
?>

                <div class="layout-content">

                    <div class="container-fluid flex-grow-1 container-p-y">
                        <h4 class="font-weight-bold py-3 mb-0"><?php if($_GET['id']) {?>Edit <?php } else{?> Add
                            <?php } ?> Employee Account</h4>

                        <div class="card mb-4">
                            <div class="card-body">
                                <div id="alert_message"></div>
                                <form id="validation-form" method="post" autocomplete="off" action="ajax_files/ajax_employee.php" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>" id="userid">
                                    <input type="hidden" name="action" value="Save" id="action">
                                     <fieldset>
 <legend>Personal Detail</legend>
                                    <div class="form-row">
                                       
                                        <div class="form-group col-md-4">
    <label class="form-label">Aadhar Card No</label>
    <input type="text" name="AadharNo" class="form-control" id="AadharNo"
        placeholder="Enter 12-digit Aadhar No" maxlength="12" value="<?php echo $row7["AadharNo"]; ?>"
        oninput="checkAadharLength(this.value)">
    <div class="clearfix"></div>
</div>

<input type="hidden" id="ref_id">

<div class="form-group col-md-4">
    <label class="form-label">Aadhar Card OTP</label>
    <input type="text" name="AadharOtp" class="form-control" id="AadharOtp"
        placeholder="Enter 6-digit OTP" maxlength="6"
        oninput="checkOtpLength(this.value)">
    <div class="clearfix"></div>
</div>

<div class="form-group col-md-4">
    <label class="form-label">PAN Card No</label>
    <input type="text" name="PanNo" class="form-control" id="PanCardNo"
        placeholder="Enter Pan Card No" value="<?php echo $row7["PanNo"]; ?>" oninput="checkPanCardLength(this.value)">
    <div class="clearfix"></div>
</div>

                                                   
                                       <div class="form-group col-md-4">
                                            <label class="form-label">Employee Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="Fname" id="Fname" class="form-control"
                                                placeholder="" value="<?php echo $row7["Fname"]; ?>"
                                                autocomplete="off">
                                        </div>

                                         <div class="form-group col-md-3">
                                            <label class="form-label">Education</label>
                                            <input type="text" name="Education" id="Education" class="form-control"
                                                placeholder="" value="<?php echo $row7["Education"]; ?>"
                                                autocomplete="off">
                                        </div>

                                         <div class="form-group col-md-2">
                                            <label class="form-label">UAN No </label>
                                            <input type="text" name="UanNo" id="UanNo" class="form-control"
                                                placeholder="" value="<?php echo $row7["UanNo"]; ?>"
                                                autocomplete="off">
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">ESIC No </label>
                                            <input type="text" name="EsicNo" id="EsicNo" class="form-control"
                                                placeholder="" value="<?php echo $row7["EsicNo"]; ?>"
                                                autocomplete="off">
                                        </div>
                                        
                                        <div class="form-group col-md-3">
    <label class="form-label">Gender <span class="text-danger">*</span></label>
    <select class="form-control" name="Gender" id="Gender" required="">
<option selected="" disabled="">Select Gender</option>
  
      <option <?php if('Male'==$row7['Gender']){ ?> selected <?php } ?> value="Male">Male</option>
    <option <?php if('Female'==$row7['Gender']){ ?> selected <?php } ?> value="Female">Female</option>
    <option <?php if('Transgender'==$row7['Gender']){ ?> selected <?php } ?> value="Transgender">Transgender</option>
    
</select>
  </div>
  
                                         <div class="form-group col-md-3">
    <label class="form-label">Country <span class="text-danger">*</span></label>
    <select class="form-control" name="CountryId" id="CountryId" required="">
<option selected="" disabled="">Select Country</option>
    <?php 
      $q = "select * from tbl_country";
      $r = $conn->query($q);
      while($rw = $r->fetch_assoc())
      {
    ?>
      <option <?php if(1==$rw['id']){ ?> selected <?php } ?> value="<?php echo $rw['id']; ?>"><?php echo $rw['Name']; ?></option>
    <?php } ?>
</select>
  </div>
  <div class="form-group col-md-3">
    <label class="form-label">State <span class="text-danger">*</span></label>
<select class="form-control" id="StateId" name="StateId" required="">
<option selected="" disabled="">Select State</option>
 <?php 
        $CountryId = $row7['CountryId'];
        $q = "select * from tbl_state WHERE CountryId='1' ORDER BY Name ASC";
        $r = $conn->query($q);
        while($rw = $r->fetch_assoc())
    {
?>
                <option <?php if($row7['StateId']==$rw['id']){ ?> selected <?php } ?> value="<?php echo $rw['id']; ?>"><?php echo $rw['Name']; ?></option>
              <?php } ?>
</select>
  </div>
  <div class="form-group col-md-3">
      <label class="form-label">City/District </label>
<select class="form-control" id="CityId" name="CityId">
<option selected="" disabled="">Select City/District</option>
  <?php 
 $StateId = $row7['StateId'];
        $q = "select * from tbl_city WHERE StateId='$StateId' ORDER BY Name ASC";
        $r = $conn->query($q);
        while($rw = $r->fetch_assoc())
    {
?>
                <option <?php if($row7['CityId']==$rw['id']){ ?> selected <?php } ?> value="<?php echo $rw['id']; ?>"><?php echo $rw['Name']; ?></option>
              <?php } ?>
</select>
  </div>

 <div class="form-group col-md-12">
                                            <label class="form-label">Permanent Address </label>
                                            <textarea name="Address" class="form-control" placeholder="Address"
                                                autocomplete="off"><?php echo $row7["Address"]; ?></textarea>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        
                                    <div class="form-group col-md-3">
                                            <label class="form-label">Password <span
                                                    class="text-danger">*</span>
                                                <button type="button"
                                                    class="employee-pw-policy-icon employee-password-policy-btn ml-1"
                                                    title="Password requirements" aria-label="Password requirements"><i class="fa fa-info-circle text-info" aria-hidden="true"></i></button>
                                            </label>
                                            <div class="position-relative">
                                                <input type="password" name="Password" id="Password" class="form-control"
                                                    placeholder="Min 8 chars: letters + numbers + symbols" value="<?php echo htmlspecialchars((string)($row7["Password"] ?? ""), ENT_QUOTES, 'UTF-8'); ?>"
                                                    required autocomplete="new-password">
                                                <span class="password-tog-info show2" onclick="myFunction2()" role="button"
                                                    tabindex="0" title="Show / hide password"><i class="fa fa-eye" aria-hidden="true"></i></span>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                       <!-- <input type="hidden" name="Password" id="Password" class="form-control"
                                                placeholder="Password" value="12345">-->
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Mobile No <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="Phone" id="Phone" class="form-control js-phone-10"
                                                placeholder="10 digit mobile" maxlength="10" inputmode="numeric" autocomplete="off"
                                                value="<?php echo $row7["Phone"]; ?>" required>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Another Mobile No</label>
                                            <input type="text" name="Phone2" id="Phone2" class="form-control js-phone-10"
                                                placeholder="10 digit mobile" maxlength="10" inputmode="numeric" autocomplete="off"
                                                value="<?php echo $row7["Phone2"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                         <div class="form-group col-md-3">
                                            <label class="form-label">Email Id </label>
                                            <input type="email" name="EmailId" id="EmailId" class="form-control"
                                                placeholder="Email Id" value="<?php echo $row7["EmailId"]; ?>"
                                                autocomplete="off">
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <!--<div class="form-group col-md-2">
                                            <label class="form-label">Referral code </label>
                                            <input type="text" name="ReferCode" id="ReferCode" class="form-control"
                                                placeholder="" value="<?php echo $row7["ReferCode"]; ?>" oninput="checkReferDetails()">
                                            <div class="clearfix"></div>
                                        </div>-->
                                        
                                        <div class="form-group col-md-3">
<label class="form-label"> Referral code</label>
 <select class="select2-demo form-control" name="ReferCode" id="ReferCode" onchange="checkReferDetails(this.value)">
<option selected="" value="0">No Refer</option>

<optgroup label="Referral User">
 <?php 
  $sql12 = "SELECT id,Fname,CustomerId FROM tbl_users WHERE Status='1' AND CustomerId!='' AND Roll NOT IN(1,5,55,9,22,23,63,3)";
  $row12 = getList($sql12);
  foreach($row12 as $result){
     ?>
  <option <?php if($row7["ReferCode"] == $result['CustomerId']) {?> selected <?php } ?> value="<?php echo $result['CustomerId'];?>">
    <?php echo $result['Fname']." (".$result['CustomerId'].")"; ?></option>
<?php } ?>
</optgroup>
</select>
<div class="clearfix"></div>
</div>
                                        
                                       
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Referral Name </label>
                                            <input type="text" name="ReferName" id="ReferName" class="form-control"
                                                placeholder="" value="<?php echo $row7["ReferName"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Reference Mobile No </label>
                                            <input type="text" name="RefPhone" id="RefPhone" class="form-control js-phone-10"
                                                placeholder="10 digit mobile" maxlength="10" inputmode="numeric" autocomplete="off"
                                                value="<?php echo $row7["RefPhone"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Reference Mobile No 2</label>
                                            <input type="text" name="RefPhone2" id="RefPhone2" class="form-control js-phone-10"
                                                placeholder="10 digit mobile" maxlength="10" inputmode="numeric" autocomplete="off"
                                                value="<?php echo $row7["RefPhone2"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        <input type="hidden" id="ReferId" name="ReferId" value="<?php echo $row7["ReferId"]; ?>">
                                        
                                       <!-- <div class="form-group col-md-3">
                                            <label class="form-label"> Father/Mother Contact No</label>
                                            <input type="text" name="FatherPhone" class="form-control"
                                                placeholder="" value="<?php echo $row7["FatherPhone"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>-->
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Reference Email Id </label>
                                            <input type="email" name="RefEmailId" id="RefEmailId" class="form-control"
                                                placeholder="Email Id" value="<?php echo $row7["RefEmailId"]; ?>"
                                                autocomplete="off">
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-6">
                                            <label class="form-label">Defclartion Form Pdf</label>
                                            <label class="custom-file">
                                                <input type="file" class="custom-file-input" name="DeclarationPdf"
                                                    style="opacity: 1;">
                                                <input type="hidden" name="OldDeclarationPdf"
                                                    value="<?php echo $row7['DeclarationPdf'];?>" id="OldDeclarationPdf">
                                                <span class="custom-file-label"></span>
                                            </label>
                                            <?php if($row7['DeclarationPdf']=='') {} else{?>
                                            <span id="show_photo">
                                                <div class="ui-feed-icon-container float-left pt-2 mr-3 mb-3"><a
                                                        href="javascript:void(0)"
                                                        class="ui-icon ui-feed-icon ion ion-md-close bg-secondary text-white"
                                                        id="delete_photo"></a><a href="../uploads/<?php echo $row7['DeclarationPdf'];?>"><?php echo $row7['DeclarationPdf'];?></a></div>
                                            </span>
                                            <?php } ?>
                                        </div>
                                        
                                         <div class="form-group col-md-6">
                                            <label class="form-label">Defclartion Form Photo </label>
                                            <label class="custom-file">
                                                <input type="file" class="custom-file-input" name="DeclarationPhoto"
                                                    style="opacity: 1;">
                                                <input type="hidden" name="OldDeclarationPhoto"
                                                    value="<?php echo $row7['DeclarationPhoto'];?>" id="OldDeclarationPhoto">
                                                <span class="custom-file-label"></span>
                                            </label>
                                            <?php if($row7['DeclarationPhoto']=='') {} else{?>
                                            <span id="show_photo">
                                                <div class="ui-feed-icon-container float-left pt-2 mr-3 mb-3"><a
                                                        href="javascript:void(0)"
                                                        class="ui-icon ui-feed-icon ion ion-md-close bg-secondary text-white"
                                                        id="delete_photo"></a><img
                                                        src="../uploads/<?php echo $row7['DeclarationPhoto'];?>" alt=""
                                                        class="img-fluid ticket-file-img"
                                                        style="width: 64px;height: 64px;"></div>
                                            </span>
                                            <?php } ?>
                                        </div>
                                        
                                                                               
                                        
                                         <div class="form-group col-md-3">
                                            <label class="form-label">Nominee Name </label>
                                            <input type="text" name="NomineeName" id="NomineeName" class="form-control"
                                                placeholder="Nominee Name" value="<?php echo $row7["NomineeName"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Nominee Relation</label>
                                            <input type="text" name="NomineeRelation" class="form-control"
                                                placeholder="Nominee Relation" value="<?php echo $row7["NomineeRelation"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                       <div class="form-group col-md-3">
                                            <label class="form-label"> Nominee Contact No</label>
                                            <input type="text" name="NomineePhone" id="NomineePhone" class="form-control js-phone-10"
                                                placeholder="10 digit mobile" maxlength="10" inputmode="numeric" autocomplete="off"
                                                value="<?php echo $row7["NomineePhone"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Nominee Aadhar Card No </label>
                                            <input type="text" name="NomineeAadharNo" id="NomineeAadharNo" class="form-control"
                                                placeholder="" value="<?php echo $row7["NomineeAadharNo"]; ?>"
                                                autocomplete="off">
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        
                                        
                                         <div class="form-group col-md-3">
                                            <label class="form-label">Department <span
                                                    class="text-danger">*</span></label>
                                            
                                             <select class="form-control" name="Designation" id="Designation" required>
                                                <option selected="" disabled="">Select Department</option>
                                                <?php 
                                        $q = "select * from tbl_departments WHERE Status=1 ORDER BY Name";
                                        $r = $conn->query($q);
                                        while($rw = $r->fetch_assoc())
                                    {
                                ?>
                                                <option <?php if($row7['Designation']==$rw['id']){ ?> selected <?php } ?>
                                                    value="<?php echo $rw['id']; ?>"><?php echo $rw['Name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            
                                          
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                       <div class="form-group col-md-2">
                                            <label class="form-label">Date Of Birth</label>
                                            <input type="date" id="Dob" name="Dob" class="form-control"
                                                placeholder="" value="<?php echo $row7["Dob"]; ?>" required>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Marriage Anniversary</label>
                                            <input type="date" name="AnniversaryDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["AnniversaryDate"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Blood Group</label>
                                            <input type="text" name="BloodGroup" class="form-control"
                                                placeholder="" value="<?php echo $row7["BloodGroup"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <!--<div class="form-group col-md-3">
                                            <label class="form-label">Aadhar Card No</label>
                                            <input type="text" name="AadharNo" class="form-control"
                                                placeholder="" value="<?php echo $row7["AadharNo"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>-->
                                        
                                        

                                            <div class="form-group col-md-3">
                                            <label class="form-label">Date Of Joining</label>
                                            <input type="date" name="JoinDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["JoinDate"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                       
                                        
                                        <!--<div class="form-group col-md-3">
                                            <label class="form-label">Company Email Id </label>
                                            <input type="email" name="EmailId2" id="EmailId2" class="form-control"
                                                placeholder="Email Id" value="<?php echo $row7["EmailId2"]; ?>"
                                                autocomplete="off">
                                            <div class="clearfix"></div>
                                        </div>-->

                                        
                                         <?php if($user_id == 2650 || $user_id == 22170 || $user_id == 2799){?>
                                         <div class="form-group col-md-2">
                                            <label class="form-label">Salary Type <span class="text-danger">*</span></label>
                                            <select class="form-control" id="SalaryType" name="SalaryType" required="" onchange="getMonthSal(this.value,document.getElementById('PerDaySalary').value)">
                                                <!--<option selected="" disabled="" value="">Select</option>
                                                <option value="1" <?php if($row7["SalaryType"]=='1') {?> selected
                                                    <?php } ?>>Daily Basis</option>-->
                                                <option value="2" <?php if($row7["SalaryType"]=='2') {?> selected
                                                    <?php } ?>>Fixed</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Per Day Salary <span class="text-danger">*</span></label>
                                            <input type="text" name="PerDaySalary" id="PerDaySalary" class="form-control"
                                                placeholder="" value="<?php echo $row7["PerDaySalary"]; ?>" oninput="getMonthSal(document.getElementById('SalaryType').value,document.getElementById('PerDaySalary').value)"
                                                autocomplete="off" required>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Monthly Salary <span class="text-danger">*</span></label>
                                            <input type="text" name="MonthlySalary" id="MonthlySalary" class="form-control"
                                                placeholder="" value="<?php echo $row7["MonthlySalary"]; ?>" 
                                                autocomplete="off" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        <?php } ?>
                                        <!--<div class="form-group col-md-3">
                                            <label class="form-label">Credit Salary Status <span class="text-danger">*</span></label>
                                            <select class="form-control" id="CreditSalaryStatus" name="CreditSalaryStatus" required="">
                                               <option selected="" disabled="" value="">Select Status</option>
                                                <option value="1" <?php if($row7["CreditSalaryStatus"]=='1') {?> selected
                                                    <?php } ?>>Active</option>
                                                <option value="0" <?php if($row7["CreditSalaryStatus"]=='0') {?> selected
                                                    <?php } ?>>Inactive</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
                                        -->
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Yearly Week Off <span class="text-danger">*</span></label>
                                            <input type="text" name="YearlyWeekOff" id="YearlyWeekOff" class="form-control"
                                                placeholder="" value="<?php echo $row7["YearlyWeekOff"]; ?>" oninput="getMonthWeekOff(document.getElementById('YearlyWeekOff').value)"
                                                autocomplete="off" required>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Monthly Week Off <span class="text-danger">*</span></label>
                                            <input type="text" name="MonthlyWeekOff" id="MonthlyWeekOff" class="form-control"
                                                placeholder="" value="<?php echo $row7["MonthlyWeekOff"]; ?>" 
                                                autocomplete="off" readonly>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
    <label class="form-label">Week Off Day</label>
    <select class="form-control" id="WeekOffDay" name="WeekOffDay" required>
        <option value="0" selected>No Week Off</option>

        <option value="Monday"    <?php if($row71["WeekOffDay"]=="Monday") echo "selected"; ?>>Monday</option>
        <option value="Tuesday"   <?php if($row71["WeekOffDay"]=="Tuesday") echo "selected"; ?>>Tuesday</option>
        <option value="Wednesday" <?php if($row71["WeekOffDay"]=="Wednesday") echo "selected"; ?>>Wednesday</option>
        <option value="Thursday"  <?php if($row71["WeekOffDay"]=="Thursday") echo "selected"; ?>>Thursday</option>
        <option value="Friday"    <?php if($row71["WeekOffDay"]=="Friday") echo "selected"; ?>>Friday</option>
        <option value="Saturday"  <?php if($row71["WeekOffDay"]=="Saturday") echo "selected"; ?>>Saturday</option>
        <option value="Sunday"    <?php if($row71["WeekOffDay"]=="Sunday") echo "selected"; ?>>Sunday</option>

    </select>
    <div class="clearfix"></div>
</div>

                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Date Of Re-Joining</label>
                                            <input type="date" name="ReJoinDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["ReJoinDate"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Approve By</label>
                                            <input type="text" name="ApproveBy" class="form-control"
                                                placeholder="" value="<?php echo $row7["ApproveBy"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        

                                        <div class="form-group col-md-2">
                                            <label class="form-label">Scheme</label>
                                            
                                             <select class="form-control" name="EmpScheme" id="EmpScheme">
                                                <option selected="" >Select Scheme</option>
                                                <?php 
                                        $q = "select * from tbl_emp_scheme WHERE Status=1 ORDER BY Name";
                                        $r = $conn->query($q);
                                        while($rw = $r->fetch_assoc())
                                    {
                                ?>
                                                <option <?php if($row7['EmpScheme']==$rw['id']){ ?> selected <?php } ?>
                                                    value="<?php echo $rw['id']; ?>"><?php echo $rw['Name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            
                                          
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">COFO Employee </label>
                                            <select class="form-control" id="cofofr" name="cofofr" required="">
                                               <!-- <option selected="" disabled="" value="">Select Status</option>-->
                                                <option value="0" <?php if($row7["cofofr"]=='0') {?> selected
                                                    <?php } ?>>No</option>
                                                <option value="1" <?php if($row7["cofofr"]=='1') {?> selected
                                                    <?php } ?>>Yes</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-2">
                                            <label class="form-label">Ticket Show </label>
                                            <select class="form-control" id="ticketshow" name="ticketshow" required="">
                                             
                                                <option value="0" <?php if($row7["ticketshow"]=='0') {?> selected
                                                    <?php } ?>>No</option>
                                                <option value="1" <?php if($row7["ticketshow"]=='1') {?> selected
                                                    <?php } ?>>Yes</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-2">
                                            <label class="form-label">Attendance Task Show </label>
                                            <select class="form-control" id="att_task_show" name="att_task_show" required="">
                                             
                                                <option value="0" <?php if($row7["att_task_show"]=='0') {?> selected
                                                    <?php } ?>>No</option>
                                                <option value="1" <?php if($row7["att_task_show"]=='1') {?> selected
                                                    <?php } ?>>Yes</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>


                                        <div class="form-group col-md-12">
                                            <label class="form-label">Photo </label>
                                            <label class="custom-file">
                                                <input type="file" class="custom-file-input" name="Photo"
                                                    style="opacity: 1;">
                                                <input type="hidden" name="OldPhoto"
                                                    value="<?php echo $row7['Photo'];?>" id="OldPhoto">
                                                <span class="custom-file-label"></span>
                                            </label>
                                            <?php if($row7['Photo']=='') {} else{?>
                                            <span id="show_photo">
                                                <div class="ui-feed-icon-container float-left pt-2 mr-3 mb-3"><a
                                                        href="javascript:void(0)"
                                                        class="ui-icon ui-feed-icon ion ion-md-close bg-secondary text-white"
                                                        id="delete_photo"></a><img
                                                        src="../uploads/<?php echo $row7['Photo'];?>" alt=""
                                                        class="img-fluid ticket-file-img"
                                                        style="width: 64px;height: 64px;"></div>
                                            </span>
                                            <?php } ?>
                                        </div>



<div class="form-group col-md-12">
   <label class="form-label">Image/Files (Multiple)</label>
<label class="custom-file">
<input type="file" class="custom-file-input" id="Photo2" name="Files[]" style="opacity: 1;" multiple="">
<span class="custom-file-label"></span>
</label>
 <span id="show_photo2">
<?php 
  $id = $_GET['id'];
  if($id!=''){
  $sql2 = "SELECT * FROM tbl_user_files WHERE UserId='$id'";
  $res2 = $conn->query($sql2);
  $rncnt = mysqli_num_rows($res2);
  if($rncnt > 0){
    while($row2 = $res2->fetch_assoc()){?>
    <input type="hidden" name="OldMulImage" id="OldMulImage<?php echo $row2["id"]; ?>" value="<?php echo $row2["Files"]; ?>">
<div class="ui-feed-icon-container float-left pt-2 mr-3 mb-3"><a href="javascript:void(0)" class="ui-icon ui-feed-icon ion ion-md-close bg-secondary text-white" onclick="delete_photo2(<?php echo $row2["id"]; ?>,<?php echo $_GET["id"]; ?>)"></a><a href="../employee_files/<?php echo $_GET["id"]; ?>/<?php echo $row2['Files'];?>" target="_blank"><?php echo $row2['Files'];?></a></div>
<?php }}} ?>
</span>
</div>

<div class="form-group col-md-12">
   <label class="form-label">Attach Files (Multiple) <span class="text-danger">(File size must be less than 2 MB)</span></label>
<label class="custom-file">
<input type="file" class="custom-file-input" id="Files2" name="Files2[]" style="opacity: 1;" multiple="">
<span class="custom-file-label"></span>
</label>
 <span id="show_photo2">
<?php 
  $id = $_GET['id'];
  if($id!=''){
  $sql2 = "SELECT * FROM tbl_user_files2 WHERE UserId='$id'";
  $res2 = $conn->query($sql2);
  $rncnt = mysqli_num_rows($res2);
  if($rncnt > 0){
    while($row2 = $res2->fetch_assoc()){?>
    <input type="hidden" name="OldMulImage" id="OldMulImage<?php echo $row2["id"]; ?>" value="<?php echo $row2["Files"]; ?>">
<div class="ui-feed-icon-container float-left pt-2 mr-3 mb-3"><a href="javascript:void(0)" class="ui-icon ui-feed-icon ion ion-md-close bg-secondary text-white" onclick="delete_photo2(<?php echo $row2["id"]; ?>,<?php echo $_GET["id"]; ?>)"></a>
<a href="../employee_files2/<?php echo $_GET["id"]; ?>/<?php echo $row2['Files'];?>">View File</a></div>
<?php }}} ?>
</span>
</div>
                                     
                                       

                                       
   

</div>

                                   
                                       

<div class="row">

<div class="form-group col-md-3">
                                            <label class="form-label">Designation <span class="text-danger">*</span></label>
                                            <select class="form-control" name="Roll" id="Roll" required>
                                                <option selected="" disabled="">Select Designation</option>
                                                <?php 
                                        $q = "select * from tbl_user_type WHERE Status=1 AND id!=1 ORDER BY Name";
                                        $r = $conn->query($q);
                                        while($rw = $r->fetch_assoc())
                                    {
                                ?>
                                                <option <?php if($row7['Roll']==$rw['id']){ ?> selected <?php } ?>
                                                    value="<?php echo $rw['id']; ?>"><?php echo $rw['Name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        
                                         <div class="form-group col-md-3">
                                            <label class="form-label">Head Office Employee <span class="text-danger">*</span></label>
                                            <select class="form-control" id="MainBrEmp" name="MainBrEmp" required="">
                                                <option selected="" disabled="" value="">Select</option>
                                                <option value="1" <?php if($row7["MainBrEmp"]=='1') {?> selected
                                                    <?php } ?>>Yes</option>
                                                <option value="0" <?php if($row7["MainBrEmp"]=='0') {?> selected
                                                    <?php } ?>>No</option>
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label class="form-label">Restrict start attendance after 10:15 AM <span class="text-muted">(app)</span></label>
                                            <select class="form-control" id="RestrictAttAfter1015" name="RestrictAttAfter1015">
                                                <option value="0" <?php if (empty($row71['RestrictAttAfter1015']) || (string) $row71['RestrictAttAfter1015'] === '0') { ?> selected<?php } ?>>No</option>
                                                <option value="1" <?php if (!empty($row71['RestrictAttAfter1015']) && (string) $row71['RestrictAttAfter1015'] === '1') { ?> selected<?php } ?>>Yes</option>
                                            </select>
                                            <small class="text-muted d-block">If Yes, this employee cannot use <strong>Start Attendance</strong> after 10:15 (server time) until next day.</small>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                       <?php 
if ($_GET['id'] == '') { 
?>
    <div class="form-group col-md-3">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-control" id="Status" name="Status" required>
            <option value="1" <?php if ($row7["Status"] == '1') echo 'selected'; ?>>Active</option>
        </select>
        <div class="clearfix"></div>
    </div>
<?php 
} else { 
    if ($EmpStatus == 1) { 
?>
    <div class="form-group col-md-3">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-control" id="Status" name="Status" required>
            <option selected disabled value="">Select Status</option>
            <option value="1" <?php if ($row7["Status"] == '1') echo 'selected'; ?>>Active</option>
            <option value="0" <?php if ($row7["Status"] == '0') echo 'selected'; ?>>Inactive</option>
        </select>
        <div class="clearfix"></div>
    </div>
<?php 
    } else { 
?>
    <div class="form-group col-md-3">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-control" id="Status" name="Status" required>
            <?php if ($row7["Status"] == '1') { ?>
                <option value="1" selected>Active</option>
            <?php } else { ?>
                <option value="0" selected>Inactive</option>
            <?php } ?>
        </select>
        <div class="clearfix"></div>
    </div>
<?php 
    } 
} 
?>
<?php if (!empty($_GET['id']) && isset($row71['InactiveAt']) && $row71['InactiveAt'] !== '' && $row71['InactiveAt'] !== null) { ?>
    <div class="form-group col-md-6">
        <label class="form-label text-muted">Inactive recorded</label>
        <p class="form-control-plaintext border rounded px-3 py-2 mb-0 bg-light">
            <?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($row71['InactiveAt']))); ?>
            <small class="text-muted d-block">(server time when status was set to Inactive; stored in profile)</small>
        </p>
    </div>
<?php } ?>

                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Reporting Manager <span class="text-danger">*</span></label>
                                            <select class="form-control" id="ReportingMgr" name="ReportingMgr" required="">
                                               <!-- <option selected="" disabled="" value="">Select Status</option>-->
                                                <option value="0" <?php if($row7["ReportingMgr"]=='0') {?> selected
                                                    <?php } ?>>No</option>
                                                <option value="1" <?php if($row7["ReportingMgr"]=='1') {?> selected
                                                    <?php } ?>>Yes</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>


                                         <!--<div class="form-group col-md-4">
                                            <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ReportingMgr" value="1" <?php if(in_array($result["id"],$row7['Options'])) { ?>

                        checked="checked" <?php } ?>>
                                    <span class="custom-control-label">Reporting Manager</span>
                                </label>
                                        </div>-->

                                    
                                    
                                    
                                    <div class="form-group col-md-4">
  <label class="form-label form-check"> 
  <!--<input class="form-check-input" type="checkbox" id="underByCheck" name="underByCheck" value="1">-->
  Under By Reporting Manager</label>

  <!-- Added checkbox -->
  

  <select class="select2-demo form-control" name="UnderUser" id="UnderUser">
    <option selected="" value="0">No Reporting Manager</option>
    <option value="5" <?php if($row7["UnderByUser"] == 5) {?> selected <?php } ?>>Pradeep Kulkarni (Admin)</option>
    <optgroup label="Reporting Manager">
      <?php 
      $sql12 = "SELECT * FROM tbl_users WHERE Status='1' AND ReportingMgr=1";
      $row12 = getList($sql12);
      foreach($row12 as $result){
      ?>
        <option <?php if($row7["UnderUser"] == $result['id']) {?> selected <?php } ?> value="<?php echo $result['id'];?>">
          <?php echo $result['Fname']." (".$result['Phone'].")"; ?>
        </option>
      <?php } ?>
    </optgroup>
  </select>
  <div class="clearfix"></div>
</div>


 <div class="form-group col-md-4">
<label class="form-label"> Under By </label>
 <select class="select2-demo form-control" name="UnderByUser" id="UnderByUser">
<option selected="" value="0">No One</option>
<option value="5" <?php if($row7["UnderByUser"] == 5) {?> selected <?php } ?>>Pradeep Kulkarni (Admin)</option>
<optgroup label="Under BY">
 <?php 
  $sql12 = "SELECT id,Fname,Phone FROM tbl_users WHERE Status='1' AND Roll NOT IN(1,5,55,9,22,23,63)";
  $row12 = getList($sql12);
  foreach($row12 as $result){
     ?>
  <option <?php if($row7["UnderByUser"] == $result['id']) {?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['Fname']." (".$result['Phone'].")"; ?></option>
<?php } ?>
</optgroup>
</select>
<div class="clearfix"></div>
</div>

 
                                    <div class="form-group col-md-4">
<label class="form-label"> Under By BDM</label>
 <select class="select2-demo form-control" name="UnderByBdm" id="UnderByBdm">
<option selected="" value="0">No BDM</option>
<optgroup label="BDM">
 <?php 
  $sql12 = "SELECT * FROM tbl_users WHERE Status='1' AND Roll IN (134,145,181,183)";
  $row12 = getList($sql12);
  foreach($row12 as $result){
     ?>
  <option <?php if($row7["UnderByBdm"] == $result['id']) {?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['Fname']." (".$result['Phone'].")"; ?></option>
<?php } ?>
</optgroup>
</select>
<div class="clearfix"></div>
</div>

 <?php if($user_id == 2651 || $user_id == 2650 || $user_id == 22170 || $user_id == 2799){?>
<div class="form-group col-md-3">
<label class="form-label"> Under By Franchise</label>
 <select class="select2-demo form-control" name="UnderFrId" id="UnderFrId">
<option selected="" value="0">Select Franchise</option>

 <?php 
  $sql12 = "SELECT * FROM tbl_users WHERE Status='1' AND Roll=5";
  $row12 = getList($sql12);
  foreach($row12 as $result){
     ?>
  <option <?php if($row7["UnderFrId"] == $result['id']) {?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['ShopName']." (".$result['Phone'].")"; ?></option>
<?php } ?>

</select>
<div class="clearfix"></div>
</div>
<?php } ?>
<div class="form-group col-md-3">
                                            <label class="form-label">Expenses Approval</label>
                                            <select class="form-control" id="ExpApproval" name="ExpApproval">
                                                <option selected="" disabled="" value="">Select</option>
                                                <option value="1" <?php if($row7["ExpApproval"]=='1') {?> selected
                                                    <?php } ?>>One Way Expenses Approval</option>
                                                <option value="2" <?php if($row7["ExpApproval"]=='2') {?> selected
                                                    <?php } ?>>Two Way Expenses Approval</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Manager Checkpoint </label>
                                            <select class="form-control" id="MgrCheckpoint" name="MgrCheckpoint" required="">
                                               <!-- <option selected="" disabled="" value="">Select Status</option>-->
                                                <option value="0" <?php if($row7["MgrCheckpoint"]=='0') {?> selected
                                                    <?php } ?>>No</option>
                                                <option value="1" <?php if($row7["MgrCheckpoint"]=='1') {?> selected
                                                    <?php } ?>>Yes</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-2">
                                            <label class="form-label">BDM Checkpoint </label>
                                            <select class="form-control" id="BdmCheckpoint" name="BdmCheckpoint" required="">
                                               <!-- <option selected="" disabled="" value="">Select Status</option>-->
                                                <option value="0" <?php if($row7["BdmCheckpoint"]=='0') {?> selected
                                                    <?php } ?>>No</option>
                                                <option value="1" <?php if($row7["BdmCheckpoint"]=='1') {?> selected
                                                    <?php } ?>>Yes</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>

                                        <div class="form-group col-md-2">
                                            <label class="form-label">Resign </label>
                                            <select class="form-control" id="ResignStatus" name="ResignStatus" required="">
                                               <!-- <option selected="" disabled="" value="">Select Status</option>-->
                                                <option value="0" <?php if($row7["ResignStatus"]=='0') {?> selected
                                                    <?php } ?>>No</option>
                                                <option value="1" <?php if($row7["ResignStatus"]=='1') {?> selected
                                                    <?php } ?>>Yes</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                          <div class="form-group col-md-3">
                                            <label class="form-label">Resign Date</label>
                                            <input type="date" name="ResignDate" class="form-control"
                                                placeholder="" value="<?php echo $row7["ResignDate"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-6">
                                            <label class="form-label">Resign Comment</label>
                                            <input type="text" name="ResignComment" class="form-control"
                                                placeholder="" value="<?php echo $row7["ResignComment"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label class="form-label">Notice Period</label>
                                            <input type="text" name="NoticePeriod" class="form-control"
                                                placeholder="e.g. 15 Days" value="<?php echo $row7["NoticePeriod"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-3">
                                            <label class="form-label">NSO Vendor Payment Show </label>
                                            <select class="form-control" id="NsoVedPay" name="NsoVedPay" required="">
                                               <!-- <option selected="" disabled="" value="">Select Status</option>-->
                                                <option value="0" <?php if($row7["NsoVedPay"]=='0') {?> selected
                                                    <?php } ?>>No</option>
                                                <option value="1" <?php if($row7["NsoVedPay"]=='1') {?> selected
                                                    <?php } ?>>Yes</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Increment </label>
                                            <select class="form-control" id="Increment" name="Increment" required="">
                                               <!-- <option selected="" disabled="" value="">Select Status</option>-->
                                                <option value="0" <?php if($row7["Increment"]=='0') {?> selected
                                                    <?php } ?>>No</option>
                                                <option value="1" <?php if($row7["Increment"]=='1') {?> selected
                                                    <?php } ?>>Yes</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Increment % </label>
                                            <select class="form-control" id="IncrementPer" name="IncrementPer" required="">
                                               <!-- <option selected="" disabled="" value="">Select Status</option>-->
                                               <option value="0" <?php if($row7["IncrementPer"]=='0') {?> selected
                                                    <?php } ?>>0%</option>
                                                <option value="10" <?php if($row7["IncrementPer"]=='10') {?> selected
                                                    <?php } ?>>10%</option>
                                                    <option value="12" <?php if($row7["IncrementPer"]=='12') {?> selected
                                                    <?php } ?>>12%</option>
                                                <option value="15" <?php if($row7["IncrementPer"]=='15') {?> selected
                                                    <?php } ?>>15%</option>
                                                    
                                                <option value="20" <?php if($row7["IncrementPer"]=='20') {?> selected
                                                    <?php } ?>>20%</option>
                                                <option value="25" <?php if($row7["IncrementPer"]=='25') {?> selected
                                                    <?php } ?>>25%</option>
                                                <option value="33" <?php if($row7["IncrementPer"]=='33') {?> selected
                                                    <?php } ?>>33%</option>
                                                <option value="45" <?php if($row7["IncrementPer"]=='45') {?> selected
                                                    <?php } ?>>45%</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <?php if($user_id == 5 || $user_id == 415 || $user_id == 2650 || $user_id == 2651){?>
                                        <div class="form-group col-md-2">
                                            <label class="form-label">Petty Cash </label>
                                            <select class="form-control" id="PettyCash" name="PettyCash" required="">
                                               <!-- <option selected="" disabled="" value="">Select Status</option>-->
                                                <option value="No" <?php if($row7["PettyCash"]=='No') {?> selected
                                                    <?php } ?>>No</option>
                                                <option value="Yes" <?php if($row7["PettyCash"]=='Yes') {?> selected
                                                    <?php } ?>>Yes</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                         <div class="form-group col-md-3">
                                            <label class="form-label">Petty Cash Amount</label>
                                            <input type="text" name="PettyAmount" id="PettyAmount" class="form-control js-num-only"
                                                placeholder="e.g. 5000" inputmode="numeric" autocomplete="off"
                                                value="<?php echo $row7["PettyAmount"]; ?>">
                                            <div class="clearfix"></div>
                                        </div>
                                        <?php } ?>
                                        
                                         <div class="form-group col-md-12">
                                            <label class="form-label">Details </label>
                                            <textarea name="Details" class="form-control" placeholder="Details"
                                                autocomplete="off"><?php echo $row7["Details"]; ?></textarea>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        
                                        
                                       <div class="form-group col-md-2">
    <label class="form-label">In Time</label>
    <input type="time" id="InTime" name="OpenTime" class="form-control"
           value="<?php echo $row7["OpenTime"]; ?>">
</div>

<div class="form-group col-md-2">
    <label class="form-label">Out Time</label>
    <input type="time" id="OutTime" name="CloseTime" class="form-control"
           value="<?php echo $row7["CloseTime"]; ?>">
</div>

<div class="form-group col-md-2">
    <label class="form-label">Total Hours</label>
    <input type="text" id="TotalHours" name="TotalHours" value="<?php echo $row7["TotalHours"]; ?>" class="form-control" readonly>
</div>


<div class="form-group col-md-2">
                                            <label class="form-label">Total Working Hrs </label>
                                            <select class="form-control" id="WorkingHrs" name="WorkingHrs" required="">
                                               <option selected="" disabled="" value="">Select</option>
                                               <option value="6" <?php if($row7["WorkingHrs"]=='6') {?> selected
                                                    <?php } ?>>6 Hrs</option>
                                                <option value="8" <?php if($row7["WorkingHrs"]=='8') {?> selected
                                                    <?php } ?>>8 Hrs</option>
                                                <option value="9" <?php if($row7["WorkingHrs"]=='9') {?> selected
                                                    <?php } ?>>9 Hrs</option>
                                                    <option value="10" <?php if($row7["WorkingHrs"]=='10') {?> selected
                                                    <?php } ?>>10 Hrs</option>
                                                <option value="12" <?php if($row7["WorkingHrs"]=='12') {?> selected
                                                    <?php } ?>>12 Hrs</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>
                                        
                                        <div class="form-group col-md-1">
    <label class="form-label">CL <span class="text-danger">*</span></label>
    <input type="text" id="Cl" name="Cl" class="form-control js-num-only"
           inputmode="numeric" autocomplete="off" value="<?php echo $row71["Cl"]; ?>" required>
</div>

<div class="form-group col-md-1">
    <label class="form-label">EL <span class="text-danger">*</span></label>
    <input type="text" id="El" name="El" value="<?php echo $row71["El"]; ?>" class="form-control js-num-only"
           inputmode="numeric" autocomplete="off" required>
</div>

<div class="form-group col-md-2">
                                            <label class="form-label">Grade <span class="text-danger">*</span> </label>
                                            <select class="form-control" id="Grade" name="Grade" required="">
                                               <option selected="" disabled="" value="">Select</option>
                                                <option value="A1" <?php if($row71["Grade"]=='A1') {?> selected
                                                    <?php } ?>>A1</option>
                                                <option value="A2" <?php if($row71["Grade"]=='A2') {?> selected
                                                    <?php } ?>>A2</option>
                                                     <option value="B1" <?php if($row71["Grade"]=='B1') {?> selected
                                                    <?php } ?>>B1</option>
                                                <option value="B2" <?php if($row71["Grade"]=='B2') {?> selected
                                                    <?php } ?>>B2</option>
                                                     <option value="C1" <?php if($row71["Grade"]=='C1') {?> selected
                                                    <?php } ?>>C1</option>
                                                <option value="C2" <?php if($row71["Grade"]=='C2') {?> selected
                                                    <?php } ?>>C2</option>
                                               
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>

</div>
                                    <br>
                                    <?php if ($user_id == 2651 || $user_id == 2650) { ?>
<div class="row">

    <!-- Dashboard -->
    <div class="form-group col-md-12">
        <label class="form-label">Dashboard</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (1,2,3,108,109,110,111,112,113,122,129)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>
    
    <!-- HR Dashboard -->
    <div class="form-group col-md-12">
        <label class="form-label">HR Dashboard</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (95)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>

    <!-- Only For Admin -->
    <div class="form-group col-md-12">
        <label class="form-label">Only For Admin</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (4,5,6,7,8,76,89,90,91,92,93)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>

    <!-- All Expenses (For Employee) -->
    <div class="form-group col-md-12">
        <label class="form-label">All Expenses (For Employee)</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (9,12,13,15,16)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>

    <!-- Other Access -->
    <div class="form-group col-md-12">
        <label class="form-label">Other Access</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (75,85,19,29,30,31,32,33,34,35,36,37,39,49,50,51,60,61,62,74,100,101,107,115,117,121,127,128,164)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>

    <!-- Reports -->
    <div class="form-group col-md-12">
        <label class="form-label">Reports</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (21,22,23,24,25,26,27,77,79,80,81,82,83,84,78,96,97,98,102,104,105,120,131,106,132,133,148,151,163)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>

    <!-- Admin Request -->
    <div class="form-group col-md-12">
        <label class="form-label">Admin Request</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (20,72,73,38,116,119,160)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>

    <!-- Account Request -->
    <div class="form-group col-md-12">
        <label class="form-label">Account Request</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (28,63,17,18,47,48,87,99)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>

    <!-- HR Request -->
    <div class="form-group col-md-12">
        <label class="form-label">HR Request</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (64,45,65,66,67,114,150)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>

    <!-- Manager Request -->
    <div class="form-group col-md-12">
        <label class="form-label">Manager Request</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (44,46,68,69,70,71,118,149,159)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>

    <!-- Other Request -->
    <div class="form-group col-md-12">
        <label class="form-label">Other Request</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (40,41,42,43,103,126)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>
    
   

    <!-- User Accounts -->
    <div class="form-group col-md-12">
        <label class="form-label">User Accounts</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (52,53,54,55,56,57,58,59,94,123,124,125)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>
    
     <div class="form-group col-md-12">
        <label class="form-label">HR Training</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (135,136,137,138,139)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>
    
         <div class="form-group col-md-12">
        <label class="form-label">KPA</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (155,156,157,158)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>

    <!-- Action -->
    <div class="form-group col-md-12">
        <label class="form-label">Action</label>
    </div>
    <?php
    $sql33 = "SELECT * FROM tbl_option_cp WHERE id IN (10,11,14)";
    $row33 = getList($sql33);
    foreach ($row33 as $result) {
    ?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="Options[]" value="<?php echo $result['id']; ?>" <?php if (in_array($result["id"], $row7['Options'])) echo 'checked="checked"'; ?>>
            <span class="custom-control-label"><?php echo $result['Name']; ?></span>
        </label>
    </div>
    <?php } ?>

   
</div>
<?php } ?>
<div class="row">
 <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="InternshipEmp" value="1" <?php if ($row7['InternshipEmp'] == 1) echo 'checked="checked"'; ?>>
            <span class="custom-control-label">Internship Employee</span>
        </label>
    </div>
    
     <!-- Special Flags -->
    <?php if ($user_id == 2651 || $user_id == 2650){?>
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="MarkAttendance" value="1" <?php if ($row7['MarkAttendance'] == 1) echo 'checked="checked"'; ?>>
            <span class="custom-control-label">Anywhere</span>
        </label>
    </div>

    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="VendorExpSecOpt" value="1" <?php if ($row7['VendorExpSecOpt'] == 1) echo 'checked="checked"'; ?>>
            <span class="custom-control-label">Vendor Expense 2nd Option</span>
        </label>
    </div>

    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="EmpStatus" value="1" <?php if ($row7['EmpStatus'] == 1) echo 'checked="checked"'; ?>>
            <span class="custom-control-label">Employee Active/Inactive</span>
        </label>
    </div>
    
     <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="EmpAppDashboard" value="1" <?php if ($row7['EmpAppDashboard'] == 1) echo 'checked="checked"'; ?>>
            <span class="custom-control-label">Employee App Dashboard</span>
        </label>
    </div>
    
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="OtherEmp" value="1" <?php if ($row7['OtherEmp'] == 1) echo 'checked="checked"'; ?>>
            <span class="custom-control-label">Other Employee</span>
        </label>
    </div>
    
     <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="CashHandover" value="1" <?php if ($row7['CashHandover'] == 1) echo 'checked="checked"'; ?>>
            <span class="custom-control-label">Cash Handover</span>
        </label>
    </div>
    
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="DisabledAttPhoto" value="1" <?php if ($row7['DisabledAttPhoto'] == 1) echo 'checked="checked"'; ?>>
            <span class="custom-control-label">Disabled Attendance Photo</span>
        </label>
    </div>
    
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="ReceipeSosReply" value="1" <?php if ($row7['ReceipeSosReply'] == 1) echo 'checked="checked"'; ?>>
            <span class="custom-control-label">Receipe SOP Reply</span>
        </label>
    </div>
    
    <div class="form-group col-md-4">
        <label class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="ProfitLossReport" value="1" <?php if ($row7['ProfitLossReport'] == 1) echo 'checked="checked"'; ?>>
            <span class="custom-control-label">P & L Report Show</span>
        </label>
    </div>
<?php } ?>
     
</div>
                                    
                                    
                                    <!--<hr>
                                    
                                    <div class="row">
                                          <div class="form-group col-md-12">
                                       <label class="form-label">Expense Category </label>
    </div>
                                    <?php  
                                        $sql33 = "SELECT * FROM tbl_expenses_category WHERE Status=1";
                                        $row33 = getList($sql33);
                                        foreach($row33 as $result){
                                        ?>
                                        <div class="form-group col-md-4">
                                            <label class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="ExpCatId[]" value="<?php echo $result['id'];?>" <?php if(in_array($result["id"],$row7['ExpCatId'])) { ?>

                        checked="checked" <?php } ?>>
                                    <span class="custom-control-label"><?php echo $result['Name'];?></span>
                                </label>
                                        </div>
                                    <?php } ?>
                                    
                                  
                                    </div>-->
                                    
                                      </fieldset>
                                      
                                      <fieldset>
 <legend>Assign Zone/Sub Zone/Franchise</legend>
<div class="form-row">               
<div class="form-group col-lg-12">
<label class="form-label">Zone </label>
<select class="select2-demo form-control" name="cpzones[]" id="cpzones" multiple>


 <?php 
  $sql12 = "SELECT * FROM tbl_zone";
  $row12 = getList($sql12);
  foreach($row12 as $result){
     ?>
  <option <?php if(in_array($result["id"],$row71['cpzones'])) { ?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['Name'];?></option>
<?php } ?>
</select>
<div class="clearfix"></div>
</div>


<div class="form-group col-lg-12">
<label class="form-label">Sub Zone </label>
<select class="select2-demo form-control" name="cpsubzones[]" id="cpsubzones" multiple>


 <?php 
  $sql12 = "SELECT * FROM tbl_sub_zone";
  $row12 = getList($sql12);
  foreach($row12 as $result){
     ?>
  <option <?php if(in_array($result["id"],$row71['cpsubzones'])) { ?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['Name'];?></option>
<?php } ?>
</select>
<div class="clearfix"></div>
</div>

<div class="form-group col-lg-12">
    <label class="form-label">Franchise </label>
                                     <select class="select2-demo form-control" name="cpfranchise[]" id="cpfranchise" multiple>
 <?php  
                                        $sql33 = "SELECT * FROM  tbl_users WHERE Roll=5 AND Status=1";
                                        $row33 = getList($sql33);
                                        foreach($row33 as $result){
                                        ?>
  <option <?php if(in_array($result["id"],$row71['cpfranchise'])) { ?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['ShopName']; ?></option>
<?php } ?>
</optgroup>
</select>
</div>

                            </div>  
                                     </fieldset>        -
                                     



 <fieldset>
 <legend>Assign Franchise For Vendor Expenses</legend>
 <div class="form-row">               
<div class="form-group col-lg-12">
<label class="form-label">Zone <span class="text-danger">*</span></label>
<select class="select2-demo form-control" name="vedzones[]" id="vedzones" multiple>


 <?php 
  $sql12 = "SELECT * FROM tbl_zone";
  $row12 = getList($sql12);
  foreach($row12 as $result){
     ?>
  <option <?php if(in_array($result["id"],$row7['vedzones'])) { ?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['Name'];?></option>
<?php } ?>
</select>
<div class="clearfix"></div>
</div>

<?php 
$zoneIds = $row7['vedzones']; // ARRAY of selected zones
$selectedSubZones = $row7['vedSubzones']; // ARRAY of selected sub zones

?>
<div class="form-group col-lg-12">
<label class="form-label">Sub Zone <span class="text-danger">*</span></label>

<select class="select2-demo form-control" name="vedSubzones[]" id="vedSubzones" multiple>
<?php 
if (!empty($zoneIds) && is_array($zoneIds)) {

    // Convert array into comma-separated values for SQL
    $zoneIdString = implode(",", $zoneIds);

    // Fetch sub zones linked to selected zones
    if($zoneIdString!=''){
    $sql = "SELECT * FROM tbl_sub_zone WHERE CatId IN ($zoneIdString)";
    $rows = getList($sql);

    foreach ($rows as $result) {

        // Check selected values in edit mode
        $selected = (in_array($result["id"], $selectedSubZones)) ? "selected" : "";
        ?>
        
        <option value="<?= $result['id']; ?>" <?= $selected ?>>
            <?= $result['Name']; ?>
        </option>

        <?php
    }
    }
}
?>
</select>

<div class="clearfix"></div>
</div>


                            </div>
                            
 <div class="form-row">               
                                    <div class="form-group col-lg-12">
                                        <div class="d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center" style="padding-left: 20px;">
        <label class="form-label mb-0 me-2">Franchise <span class="text-danger">*</span></label>
        <input type="checkbox" id="vedfranchiseCheck" name="vedfranchiseCheck" value="1" <?php if($row7['vedfranchiseCheck'] == 1){?> checked <?php } ?> class="form-check-input mt-0">
      </div>
    </div>

                                     <select class="select2-demo form-control" name="AssignFranchiseVedExp[]" id="AssignFranchiseVedExp" multiple>
 <?php  
                                        $sql33 = "SELECT * FROM  tbl_users_bill WHERE Roll=5 AND Status=1 AND OwnFranchise!=2";
                                        $row33 = getList($sql33);
                                        foreach($row33 as $result){
                                        ?>
  <option <?php if(in_array($result["id"],$row7['AssignFranchiseVedExp'])) { ?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['ShopName']; ?></option>
<?php } ?>
</optgroup>
</select>
</div>
</div>


                                    </fieldset>
        
        <fieldset>
 <legend>Assign Franchise For NSO Vendor Expenses</legend>
  <div class="form-row">               
<div class="form-group col-lg-12">
<label class="form-label">Zone <span class="text-danger">*</span></label>
<select class="select2-demo form-control" name="nsovedzones[]" id="nsovedzones" multiple>


 <?php 
  $sql12 = "SELECT * FROM tbl_zone";
  $row12 = getList($sql12);
  foreach($row12 as $result){
     ?>
  <option <?php if(in_array($result["id"],$row7['nsovedzones'])) { ?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['Name'];?></option>
<?php } ?>
</select>
<div class="clearfix"></div>
</div>


<?php 
$zoneIds = $row7['nsovedzones']; // ARRAY of selected zones
$selectedSubZones = $row7['nsovedSubzones']; // ARRAY of selected sub zones

?>
<div class="form-group col-lg-12">
<label class="form-label">Sub Zone <span class="text-danger">*</span></label>

<select class="select2-demo form-control" name="nsovedSubzones[]" id="nsovedSubzones" multiple>
<?php 
if (!empty($zoneIds) && is_array($zoneIds)) {

    // Convert array into comma-separated values for SQL
    $zoneIdString = implode(",", $zoneIds);

    // Fetch sub zones linked to selected zones
    if($zoneIdString!=''){
    $sql = "SELECT * FROM tbl_sub_zone WHERE CatId IN ($zoneIdString)";
    $rows = getList($sql);

    foreach ($rows as $result) {

        // Check selected values in edit mode
        $selected = (in_array($result["id"], $selectedSubZones)) ? "selected" : "";
        ?>
        
        <option value="<?= $result['id']; ?>" <?= $selected ?>>
            <?= $result['Name']; ?>
        </option>

        <?php
    }
    }
}
?>
</select>

<div class="clearfix"></div>
</div>

                            </div>
 <div class="form-row">               
                                    <div class="form-group col-lg-12">
                                        <div class="d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center" style="padding-left: 20px;">
        <label class="form-label mb-0 me-2">Franchise <span class="text-danger">*</span></label>
        <input type="checkbox" id="nsovedfranchiseCheck" name="nsovedfranchiseCheck"  value="1" <?php if($row7['nsovedfranchiseCheck'] == 1){?> checked <?php } ?> class="form-check-input mt-0">
      </div>
    </div>
                                     <select class="select2-demo form-control" name="AssignFranchiseNsoVedExp[]" id="AssignFranchiseNsoVedExp" multiple>
 <?php  
                                        $sql33 = "SELECT * FROM  tbl_users_bill WHERE Roll=5 AND Status=1 AND OwnFranchise!=2";
                                        $row33 = getList($sql33);
                                        foreach($row33 as $result){
                                        ?>
  <option <?php if(in_array($result["id"],$row7['AssignFranchiseNsoVedExp'])) { ?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['ShopName']; ?></option>
<?php } ?>
</optgroup>
</select>
</div>
</div>


                                    </fieldset>
                                    
         <fieldset>
 <legend>Assign Franchise For Attendace</legend>
<div class="form-row">
<div class="form-group col-lg-12">
<label class="form-label">Zone</label>
<select class="select2-demo form-control" name="attzones[]" id="attzones" multiple>
 <?php 
  $sql12 = "SELECT * FROM tbl_zone WHERE Status='1' ORDER BY Name";
  $row12 = getList($sql12);
  foreach($row12 as $result){
     ?>
  <option <?php if(in_array($result["id"], $row71['att_zones'])) { ?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['Name'];?></option>
<?php } ?>
</select>
<div class="clearfix"></div>
</div>

<?php 
$attZoneIds = !empty($row71['att_zones']) ? $row71['att_zones'] : array();
$attSelectedSubZones = !empty($row71['att_subzones']) ? $row71['att_subzones'] : array();
?>
<div class="form-group col-lg-12">
<label class="form-label">Sub Zone</label>
<select class="select2-demo form-control" name="attsubzones[]" id="attsubzones" multiple>
<?php 
if (!empty($attZoneIds) && is_array($attZoneIds)) {
    $zoneIdString = implode(",", array_map('intval', $attZoneIds));
    if($zoneIdString!=''){
    $sql = "SELECT * FROM tbl_sub_zone WHERE CatId IN ($zoneIdString) ORDER BY Name";
    $rows = getList($sql);
    if (is_array($rows)) {
    foreach ($rows as $result) {
        $selected = in_array($result["id"], $attSelectedSubZones) ? "selected" : "";
        ?>
        <option value="<?= $result['id']; ?>" <?= $selected ?>>
            <?= htmlspecialchars($result['Name']); ?>
        </option>
        <?php
    }
    }
    }
}
?>
</select>
<div class="clearfix"></div>
</div>

<div class="form-group col-lg-12">
<label class="form-label">Franchise</label>
                                     <select class="select2-demo form-control" name="AssignFranchiseAttendance[]" id="AssignFranchiseAttendance" multiple>
 <?php  
                                        $sql33 = "SELECT * FROM  tbl_users_bill WHERE Roll=5 AND Status=1 AND OwnFranchise!=2 ORDER BY ShopName";
                                        $row33 = getList($sql33);
                                        foreach($row33 as $result){
                                        ?>
  <option <?php if(in_array($result["id"],$row7['AssignFranchiseAttendance'])) { ?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['ShopName']; ?></option>
<?php } ?>
</select>
</div>
</div>
</fieldset>                           
    <fieldset>
 <legend>Assign Franchise Only For BDM</legend>
<div class="form-row">
<div class="form-group col-lg-12">
<label class="form-label">Zone</label>
<select class="select2-demo form-control" name="bdmzones[]" id="bdmzones" multiple>
 <?php 
  $sql12 = "SELECT * FROM tbl_zone WHERE Status='1' ORDER BY Name";
  $row12 = getList($sql12);
  foreach($row12 as $result){
     ?>
  <option <?php if(in_array($result["id"], $row71['bdm_zones'])) { ?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['Name'];?></option>
<?php } ?>
</select>
<div class="clearfix"></div>
</div>

<?php 
$bdmZoneIds = !empty($row71['bdm_zones']) ? $row71['bdm_zones'] : array();
$bdmSelectedSubZones = !empty($row71['bdm_subzones']) ? $row71['bdm_subzones'] : array();
?>
<div class="form-group col-lg-12">
<label class="form-label">Sub Zone</label>
<select class="select2-demo form-control" name="bdmsubzones[]" id="bdmsubzones" multiple>
<?php 
if (!empty($bdmZoneIds) && is_array($bdmZoneIds)) {
    $zoneIdString = implode(",", array_map('intval', $bdmZoneIds));
    if($zoneIdString!=''){
    $sql = "SELECT * FROM tbl_sub_zone WHERE CatId IN ($zoneIdString) ORDER BY Name";
    $rows = getList($sql);
    if (is_array($rows)) {
    foreach ($rows as $result) {
        $selected = in_array($result["id"], $bdmSelectedSubZones) ? "selected" : "";
        ?>
        <option value="<?= $result['id']; ?>" <?= $selected ?>>
            <?= htmlspecialchars($result['Name']); ?>
        </option>
        <?php
    }
    }
    }
}
?>
</select>
<div class="clearfix"></div>
</div>

<div class="form-group col-lg-12">
<label class="form-label">Franchise</label>
                                     <select class="select2-demo form-control" name="AssignFranchiseBdm[]" id="AssignFranchiseBdm" multiple>
 <?php  
                                        $sql33 = "SELECT * FROM  tbl_users_bill WHERE Roll=5 AND Status=1 AND OwnFranchise!=2 ORDER BY ShopName";
                                        $row33 = getList($sql33);
                                        foreach($row33 as $result){
                                        ?>
  <option <?php if(in_array($result["id"],$row7['AssignFranchiseBdm'])) { ?> selected <?php } ?> value="<?php echo $result['id'];?>">
    <?php echo $result['ShopName']; ?></option>
<?php } ?>
</select>
</div>
</div>
</fieldset>                                
                                    
<fieldset>
 <legend>Bank Account Detail</legend>
<div class="form-row">             
<div class="form-group col-md-3">
<label class="form-label">Account No </label>
<input type="text" name="AccountNo" id="AccountNo" class="form-control" placeholder="" value="<?php echo $row7["AccountNo"]; ?>" oninput="getBankDetails()" required>
<div class="clearfix"></div>
</div>

<div class="form-group col-md-3">
<label class="form-label">IFSC Code </label>
<input type="text" name="IfscCode" id="IfscCode" class="form-control" placeholder="" value="<?php echo $row7["IfscCode"]; ?>" oninput="getBankDetails()" required>
<div class="clearfix"></div>
</div>


                                    
                                       <div class="form-group col-md-6">
<label class="form-label">Bank Holder Name </label>
<input type="text" name="AccountName" id="AccountName" class="form-control" placeholder="" value="<?php echo $row7["AccountName"]; ?>" required>
<div class="clearfix"></div>
</div>

<div class="form-group col-md-4">
<label class="form-label">Bank Name </label>
<input type="text" name="BankName" id="BankName" class="form-control" placeholder="" value="<?php echo $row7["BankName"]; ?>" required>
<div class="clearfix"></div>
</div>



<div class="form-group col-md-4">
<label class="form-label">Branch </label>
<input type="text" name="Branch" id="Branch" class="form-control" placeholder="" value="<?php echo $row7["Branch"]; ?>">
<div class="clearfix"></div>
</div>



<div class="form-group col-md-4">
<label class="form-label">UPI ID </label>
<input type="text" name="UpiNo" id="UpiNo" class="form-control" placeholder="" value="<?php echo $row7["UpiNo"]; ?>">
<div class="clearfix"></div>
</div>


<!--<div class="form-group col-md-6">
                                            <label class="form-label">Other Employee <span class="text-danger">*</span></label>
                                            <select class="form-control" id="OtherEmp" name="OtherEmp" required="">
                                                
                                                <option value="0" <?php if($row7["OtherEmp"]=='0') {?> selected
                                                    <?php } ?>>No</option>
                                                <option value="1" <?php if($row7["OtherEmp"]=='1') {?> selected
                                                    <?php } ?>>Yes</option>
                                                
                                            </select>
                                            <div class="clearfix"></div>
                                        </div>-->
 


                                    </div> 
                                     </fieldset>
                                     
                                     
                                   <!-- <fieldset>
    <legend>ESIC coverage</legend>
    <?php for ($i = 1; $i <= 6; $i++) { ?>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label class="form-label">Name Of the Family Member</label>
                <input type="text" name="FamilyName<?php echo $i;?>" class="form-control" value="<?php echo $row7['FamilyName'.$i]; ?>">
            </div>

            <div class="form-group col-md-6">
                <label class="form-label">Mobile Of the Family Member</label>
                <input type="text" name="FamilyMobile<?php echo $i;?>" class="form-control" value="<?php echo $row7['FamilyMobile'.$i]; ?>">
            </div>

            <div class="form-group col-md-4">
                <label class="form-label">Relation With the Employee</label>
                <input type="text" name="EmpRelation<?php echo $i;?>" class="form-control" value="<?php echo $row7['EmpRelation'.$i]; ?>">
            </div>

            <div class="form-group col-md-4">
                <label class="form-label">DOB</label>
                <input type="date" name="FamilyDob<?php echo $i;?>" class="form-control" value="<?php echo $row7['FamilyDob'.$i]; ?>">
            </div>

            <div class="form-group col-md-4">
                <label class="form-label">Whether Resident With Him/Her</label>
                <input type="text" name="FamilyResident<?php echo $i;?>" class="form-control" value="<?php echo $row7['FamilyResident'.$i]; ?>">
            </div>

            <div class="form-group col-md-6">
                <label class="form-label">City</label>
                <input type="text" name="FamilyCity<?php echo $i;?>" class="form-control" value="<?php echo $row7['FamilyCity'.$i]; ?>">
            </div>

            <div class="form-group col-md-6">
                <label class="form-label">State</label>
                <input type="text" name="FamilyState<?php echo $i;?>" class="form-control" value="<?php echo $row7['FamilyState'.$i]; ?>">
            </div>
        </div>

        <?php if ($i < 6) { ?>
            <hr style="border-top: 1px solid #999; margin: 20px 0;">
        <?php } ?>
    <?php } ?>
</fieldset>-->

                                     
                                    <!-- <button id="growl-default" class="btn btn-default">Default</button> -->
                                    <button type="submit" class="btn btn-primary btn-finish" id="submit">Save</button>
                                    <span id="ageError" style="color:red; margin-left:10px; font-weight:500;"></span>
                                </form>

                                <!-- Password policy (shown from Password field info icon) -->
                                <div class="modal fade" id="employeePasswordPolicyModal" tabindex="-1" role="dialog"
                                    aria-labelledby="employeePasswordPolicyTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="employeePasswordPolicyTitle">Password requirements</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mb-2">Choose a strong password that cannot be guessed easily:</p>
                                                <ul class="mb-0 pl-3">
                                                    <li>At least <strong>8 characters</strong> long.</li>
                                                    <li>At least one letter (<strong>A&ndash;Z</strong> or <strong>a&ndash;z</strong>).</li>
                                                    <li>At least one digit (<strong>0&ndash;9</strong>).</li>
                                                    <li>At least one symbol (e.g. <strong>! @ # $ % & *</strong> or other punctuation).</li>
                                                    <li>Use a mixture of characters (letters + numbers + symbols together).</li>
                                                </ul>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                                            </div>
                                        </div>
                                    </div>
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
<div id="loader">Please wait...</div>

    <?php include_once 'footer_script.php'; ?>

    <script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {

    const dobInput = document.getElementById("Dob");
    const submitBtn = document.getElementById("submit");
    const ageError = document.getElementById("ageError");

    function validateAge(showAlert = false) {

        const dobValue = dobInput.value;

        if (!dobValue) {
            submitBtn.disabled = true;
            ageError.textContent = "";
            return false;
        }

        const dob = new Date(dobValue);
        const today = new Date();

        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();

        if (monthDiff < 0 || 
           (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        if (age < 18) {
            ageError.textContent = "Age must be 18 years or above.";
            submitBtn.disabled = true;

            if (showAlert) {
                alert("Age must be 18 years or above.");
            }

            return false;
        } else {
            ageError.textContent = "";
            submitBtn.disabled = false;
            return true;
        }
    }

    // ✅ Validate immediately on page load (important for edit mode)
    validateAge(false);

    // Validate on date change
    dobInput.addEventListener("change", function () {
        validateAge(true);
    });

    // Prevent invalid form submit
    document.querySelector("form").addEventListener("submit", function (e) {
        if (!validateAge(true)) {
            e.preventDefault();
        }
    });

});
    $(document).ready(function() {

    var zsfSelect2Opts = { placeholder: 'Select value' };

    function zsfDestroySelect2($sel) {
        if ($sel.length && $sel.hasClass('select2-hidden-accessible')) {
            $sel.select2('destroy');
        }
    }

    function zsfInitSelect2($sel) {
        $sel.select2($.extend({}, zsfSelect2Opts, { dropdownParent: $sel.parent() }));
    }

    function zsfClearSelectSilent($sel) {
        zsfDestroySelect2($sel);
        $sel.html('');
        zsfInitSelect2($sel);
    }

    function zsfApplyFranchiseOptions($fr, html) {
        zsfDestroySelect2($fr);
        $fr.html(html);
        zsfInitSelect2($fr);
        var vals = $fr.find('option').map(function() {
            return $(this).val();
        }).get().filter(function(v) {
            return v !== '' && v != null;
        }).map(String);
        if (vals.length) {
            $fr.val(vals).trigger('change');
        } else {
            $fr.val(null).trigger('change');
        }
    }

    function zsfApplySubzoneOptions($sub, html, selectAll) {
        zsfDestroySelect2($sub);
        $sub.html(html);
        zsfInitSelect2($sub);
        var vals = $sub.find('option').map(function() {
            return $(this).val();
        }).get().filter(function(v) {
            return v !== '' && v != null;
        }).map(String);
        if (selectAll && vals.length > 0) {
            $sub.val(vals).trigger('change');
        } else {
            $sub.val(null).trigger('change');
        }
    }

    function zsfBindZoneSubFranchise(cfg) {
        var $zone = $(cfg.zone);
        var $sub = $(cfg.sub);
        var $fr = $(cfg.franchise);
        if (!$zone.length || !$sub.length || !$fr.length) {
            return;
        }
        var urlFrSub = cfg.urlFrSub || 'get_franchise.php';
        var urlFrZone = cfg.urlFrZone || 'get_franchise_by_zones.php';

        $zone.on('change', function() {
            var zone_ids = $(this).val() || [];
            zsfClearSelectSilent($sub);
            zsfClearSelectSilent($fr);
            if (zone_ids.length > 0) {
                $.ajax({
                    url: 'get_subzones.php',
                    type: 'POST',
                    data: { zone_ids: zone_ids },
                    success: function(response) {
                        zsfApplySubzoneOptions($sub, response, true);
                    }
                });
            }
        });

        $sub.on('change', function() {
            var subzone_ids = $(this).val() || [];
            var zone_ids = $zone.val() || [];
            zsfClearSelectSilent($fr);
            if (subzone_ids.length > 0) {
                $.ajax({
                    url: urlFrSub,
                    type: 'POST',
                    data: { subzone_ids: subzone_ids },
                    success: function(response) {
                        zsfApplyFranchiseOptions($fr, response);
                    }
                });
            } else if (zone_ids.length > 0) {
                $.ajax({
                    url: urlFrZone,
                    type: 'POST',
                    data: { zone_ids: zone_ids },
                    success: function(response) {
                        zsfApplyFranchiseOptions($fr, response);
                    }
                });
            }
        });
    }

    zsfBindZoneSubFranchise({
        zone: '#cpzones',
        sub: '#cpsubzones',
        franchise: '#cpfranchise',
        urlFrSub: 'get_franchise.php',
        urlFrZone: 'get_franchise_by_zones.php'
    });

    zsfBindZoneSubFranchise({
        zone: '#vedzones',
        sub: '#vedSubzones',
        franchise: '#AssignFranchiseVedExp',
        urlFrSub: 'get_franchise_bill.php',
        urlFrZone: 'get_franchise_bill_by_zones.php'
    });

    zsfBindZoneSubFranchise({
        zone: '#nsovedzones',
        sub: '#nsovedSubzones',
        franchise: '#AssignFranchiseNsoVedExp',
        urlFrSub: 'get_franchise_bill.php',
        urlFrZone: 'get_franchise_bill_by_zones.php'
    });

    zsfBindZoneSubFranchise({
        zone: '#attzones',
        sub: '#attsubzones',
        franchise: '#AssignFranchiseAttendance',
        urlFrSub: 'get_franchise_bill.php',
        urlFrZone: 'get_franchise_bill_by_zones.php'
    });

    zsfBindZoneSubFranchise({
        zone: '#bdmzones',
        sub: '#bdmsubzones',
        franchise: '#AssignFranchiseBdm',
        urlFrSub: 'get_franchise_bill.php',
        urlFrZone: 'get_franchise_bill_by_zones.php'
    });

});

     $(document).ready(function() {
       
        //$(document).on("click", ".btn-finish", function(event){
        $('#validation-form').on('submit', function(e) {
            e.preventDefault();
            if ($('#validation-form').valid()) {

                $.ajax({
                    url: "ajax_files/ajax_employee.php",
                    method: "POST",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#submit').attr('disabled', 'disabled');
                        $('#submit').text('Please Wait...');
                    },
                    complete: function() {
                        $('#submit').attr('disabled', false);
                        $('#submit').text('Save');
                    },
                    success: function(data) {

                        if (data == 0) {
                            error_toast();

                        } else {
                            success_toast();
                            setTimeout(function() {
                                window.location.href = 'view-employee.php';
                            }, 2000);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var msg = 'Please enter a proper password: at least 8 characters including letters, numbers, and symbols.';
                            try {
                                var j = (typeof xhr.responseJSON !== 'undefined' && xhr.responseJSON) ? xhr.responseJSON : JSON.parse(xhr.responseText || '{}');
                                if (j && j.code === 'PASSWORD_POLICY_ERROR' && j.message) {
                                    msg = j.message;
                                }
                            } catch (ex) {
                                /* keep default msg */
                            }
                            var isRtl = $('body').attr('dir') === 'rtl' || $('html').attr('dir') === 'rtl';
                            $.growl.error({
                                title: 'Password not accepted',
                                message: msg,
                                location: isRtl ? 'tl' : 'tr'
                            });
                        }
                    }
                })



            } else {
                //$('#Fname').focus();
                return false;
            }
        });

        window.__mahaEmployeePasswordInitial = <?php echo json_encode(isset($row7['Password']) ? (string) $row7['Password'] : '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

        function mahaEmployeePwdNeedsStrength() {
            var uid = ($('#userid').val() || '').toString().trim();
            if (!uid) return true;
            var cur = $('#Password').val() || '';
            return cur !== (window.__mahaEmployeePasswordInitial || '');
        }

        if ($('#Password').length) {
            if (!$.validator.methods.employee_strong_password) {
                $.validator.addMethod('employee_strong_password', function(value, element) {
                    if (!mahaEmployeePwdNeedsStrength()) {
                        return true;
                    }
                    if (!value || value.length < 8) {
                        return false;
                    }
                    if (!/[a-zA-Z]/.test(value)) {
                        return false;
                    }
                    if (!/\d/.test(value)) {
                        return false;
                    }
                    if (!/[^a-zA-Z0-9]/.test(value)) {
                        return false;
                    }

                    return true;
                }, 'Password must be at least 8 characters and include letters, numbers, and symbols (e.g. !@#$).');
            }

            $('#Password').rules('add', {
                employee_strong_password: true
            });

            $('#Password').on('input blur', function () {
                $(this).valid();
            });
        }

        $(document).on('click', '.employee-password-policy-btn', function (ev) {
            ev.preventDefault();
            $('#employeePasswordPolicyModal').modal('show');
        });

        $(document).on("click", "#delete_photo", function(event) {
            event.preventDefault();
            if (confirm("Are you sure you want to delete Profile Photo?")) {
                var action = "deletePhoto";
                var id = $('#userid').val();
                var Photo = $('#OldPhoto').val();
                $.ajax({
                    url: "ajax_files/ajax_employee.php",
                    method: "POST",
                    data: {
                        action: action,
                        id: id,
                        Photo: Photo
                    },
                    success: function(data) {

                        $('#show_photo').hide();
                        var isRtl = $('body').attr('dir') === 'rtl' || $('html').attr(
                            'dir') === 'rtl';
                        $.growl.success({
                            title: 'Success',
                            message: data,
                            location: isRtl ? 'tl' : 'tr'
                        });

                    }
                });
            }

        });
        $(document).on("change", "#CountryId", function(event) {
            var val = this.value;
            var action = "getState";
            $.ajax({
                url: "ajax_files/ajax_dropdown.php",
                method: "POST",
                data: {
                    action: action,
                    id: val
                },
                success: function(data) {
                    $('#StateId').html(data);
                }
            });

        });

        $(document).on("change", "#StateId", function(event) {
            var val = this.value;
            var action = "getCity";
            $.ajax({
                url: "ajax_files/ajax_dropdown.php",
                method: "POST",
                data: {
                    action: action,
                    id: val
                },
                success: function(data) {
                    $('#CityId').html(data);
                }
            });

        });
        
    });
    
    function calculateHours() {
    let inTime = document.getElementById("InTime").value;
    let outTime = document.getElementById("OutTime").value;

    if (inTime && outTime) {
        let start = new Date("1970-01-01T" + inTime + ":00");
        let end   = new Date("1970-01-01T" + outTime + ":00");

        // Handle overnight shift (e.g., 22:00 to 06:00)
        if (end < start) {
            end.setDate(end.getDate() + 1);
        }

        let diffMs = end - start;
        let diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
        let diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

        document.getElementById("TotalHours").value =
            diffHrs + " hrs " + diffMins + " mins";
    } else {
        document.getElementById("TotalHours").value = "";
    }
}

// Run calculation live while typing/selecting
document.getElementById("InTime").addEventListener("input", calculateHours);
document.getElementById("OutTime").addEventListener("input", calculateHours);

    function showLoader() {
    document.getElementById('loader').style.display = 'block';
}

function hideLoader() {
    document.getElementById('loader').style.display = 'none';
}

    function checkAadharLength(value) {
    if (value.length === 12) {
        showLoader();
        sentOtp();
    }
}

function checkOtpLength(value) {
    if (value.length === 6) {
        showLoader();
        otpVerify();
    }
}

function checkPanCardLength(value) {
    if (value.length === 10) {
        showLoader();
        sentPanOtp();
    }
}

function getBankDetails(){
     showLoader();
  var AccountNo = $('#AccountNo').val();
  var IfscCode = $('#IfscCode').val();
   var action = "BankAccountVerify";
   $.ajax({
        url: "ajax_files/ajax_api.php",
        method: "POST",
        data: {
            action: action,
            AccountNo: AccountNo,
            IfscCode:IfscCode
        },
        dataType: "json",
        success: function(data) {
            console.log("Response:", data); 
           hideLoader();
            
            if (data.account_status === 'VALID') {
                 $('#BankName').val(data.bank_name);
                 $('#AccountName').val(data.name_at_bank);
                 $('#Branch').val(data.branch);
                
            } else {
                // alert("Failed to send OTP: " + (data.message || "Unknown error"));
            }
        },
        error: function(xhr, status, error) {
            hideLoader();
            console.error("AJAX Error:", error);
            alert("An error occurred while sending OTP. Please try again.");
        }
    });
}

function sentPanOtp() {
    var PanCardNo = $('#PanCardNo').val();
    var action = "panOtpVerify";

    if (PanCardNo.length !== 10) {
        alert("PAN number must be 10 digits");
        hideLoader();
        return;
    }

    $.ajax({
        url: "ajax_files/ajax_api.php",
        method: "POST",
        data: {
            action: action,
            PanCardNo: PanCardNo
        },
        dataType: "json",
        success: function(data) {
            console.log("Response:", data);
            hideLoader();
            
            if (data.type != 'validation_error') {
                if (data.registered_name) {
                    $('#Fname').val(data.registered_name);
                } else {
                    alert("PAN verified, but no registered name found.");
                }
            } else {
                alert("Failed to send OTP: " + (data.message || "Unknown error"));
            }
        },
        error: function(xhr, status, error) {
            hideLoader();
            console.error("AJAX Error:", error);
            alert("An error occurred while sending OTP. Please try again.");
        }
    });
}


    function sentOtp() {
    var AadharNo = $('#AadharNo').val();
    var action = "sentAadharOtp";
if (AadharNo.length !== 12) {
        alert("Aadhar number must be 12 digits");
        hideLoader();
        return;
    }
    else{
    $.ajax({
        url: "ajax_files/ajax_api.php",
        method: "POST",
        data: {
            action: action,
            AadharNo: AadharNo
        },
         beforeSend: function() {
                        $('#sent_aadhar_otp_verify').attr('disabled', 'disabled');
                        $('#sent_aadhar_otp_verify').text('Please Wait...');
                    },
        dataType: "json",  // ✅ Expect JSON
        success: function(data) {
            console.log(data);  // ✅ Shows the parsed object
            hideLoader();
 $('#sent_aadhar_otp_verify').attr('disabled', false);
                        $('#sent_aadhar_otp_verify').text('SENT OTP');
            if(data.status === 'SUCCESS') {
                $('#ref_id').val(data.ref_id);
                //alert("OTP sent! Ref ID: " + data.ref_id);
            } else {
                alert("Failed to send OTP: " + data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
    }
}

 function otpVerify() {
    var AadharOtp = $('#AadharOtp').val();
    var ref_id = $('#ref_id').val();
    var action = "aadharOtpVerify";
 if (AadharOtp.length !== 6) {
        alert("OTP must be 6 digits");
        hideLoader();
        return;
    }
    else{
    $.ajax({
        url: "ajax_files/ajax_api.php",
        method: "POST",
        data: {
            action: action,
            AadharOtp: AadharOtp,
            ref_id: ref_id
        },
         beforeSend: function() {
                        $('#aadhar_otp_verify').attr('disabled', 'disabled');
                        $('#aadhar_otp_verify').text('Please Wait...');
                    },
        dataType: "json",
        success: function(data) {
            console.log("Response:", data);
            hideLoader();
 $('#aadhar_otp_verify').attr('disabled', false);
                        $('#aadhar_otp_verify').text('OTP Verify');
            if (data.status === "SUCCESS") {
                alert("✅ OTP verified successfully!\nRef ID: " + data.ref_id);
                // You can redirect or update UI here if needed
            } else if (data.status === "ERROR") {
                alert("❌ Verification failed: " + data.message);
            } else {
                $('#Address').val(data.address)
                $('#Fname').val(data.name);
                $('#EmailId').val(data.email);
                if (data.dob) {
    var parts = data.dob.split('-');
    if (parts.length === 3) {
        var formatted = parts[2] + '-' + parts[1] + '-' + parts[0];
        $('#Dob').val(formatted);
    }
}
                $('#NomineeName').val(data.care_of);
                //$('#').val(split_address.pincode);
                //alert("⚠ Unexpected response: " + JSON.stringify(data));
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", error);
            alert("❌ AJAX error: " + error);
        }
    });
    }
}
   function checkReferDetails(ReferCode) {
    //var ReferCode = $('#ReferCode').val();
    var action = "getReferDetails";

    $.ajax({
        url: "ajax_files/ajax_employee.php",
        method: "POST",
        data: {
            action: action,
            ReferCode: ReferCode
        },
        success: function(data) {
            var res = JSON.parse(data);
            $('#ReferId').val(res.id);
            $('#ReferName').val(res.Fname);
            $('#RefPhone').val(res.Phone);
            $('#RefPhone2').val(res.Phone2);
            $('#RefEmailId').val(res.EmailId);
        }
    });
}
    
    function getMonthWeekOff(val){
        var MonthlyWeekOff = Number(val)/12;
        $('#MonthlyWeekOff').val(Math.round(MonthlyWeekOff));
    }
    function getMonthSal(saltype,salary){
        if(saltype == 1){
            var MonthlySalary = Number(salary)*30;
            $('#MonthlySalary').val(MonthlySalary);
        }
        else{
            $('#MonthlySalary').val(salary);
        }
    }
    function myFunction2() {

        var x = document.getElementById("Password");
        if (x.type === "password") {
            x.type = "text";
            $('.show2').html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
        } else {
            x.type = "password";
            $('.show2').html('<i class="fa fa-eye" aria-hidden="true"></i>');
        }
    }

    function error_toast() {
        var isRtl = $('body').attr('dir') === 'rtl' || $('html').attr('dir') === 'rtl';
        $.growl.error({
            title: 'Error',
            message: 'Email Id / Phone No Already Exists',
            location: isRtl ? 'tl' : 'tr'
        });
    }

    function success_toast() {
        var isRtl = $('body').attr('dir') === 'rtl' || $('html').attr('dir') === 'rtl';
        $.growl.success({
            title: 'Success',
            message: 'Saved Successfully...',
            location: isRtl ? 'tl' : 'tr'
        });
    }

    /** Mobile: text inputs, digits only, max 10. Amounts (CL/EL/Petty): text, digits only. */
    $(function () {
        function cleanPhone10(el) {
            el.value = (el.value || '').replace(/\D/g, '').substring(0, 10);
        }
        function cleanDigits(el) {
            el.value = (el.value || '').replace(/\D/g, '');
        }
        $('.js-phone-10').each(function () { cleanPhone10(this); });
        $('.js-num-only').each(function () { cleanDigits(this); });
        $(document).on('input', '.js-phone-10', function () {
            cleanPhone10(this);
        });
        $(document).on('input', '.js-num-only', function () {
            cleanDigits(this);
        });
    });

    </script>
     <script>
        CKEDITOR.replace( 'editor1');
</script>
</body>

</html>