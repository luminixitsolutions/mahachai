<?php
session_start();
include_once '../config.php';
 // echo "ok";exit();
$qrlib = __DIR__ . '/../../libs/phpqrcode/qrlib.php';
if (file_exists($qrlib)) {
    include_once $qrlib;
}

$user_id = isset($_SESSION['Admin']['id']) ? $_SESSION['Admin']['id'] : null;

require_once __DIR__ . '/employee_log_functions.php';
require_once __DIR__ . '/employee_password_functions.php';
require_once __DIR__ . '/employee_salary_functions.php';

if (!function_exists('ensure_tbl_users2_last_work_date_column')) {
    function ensure_tbl_users2_last_work_date_column()
    {
        global $conn;
        if (!$conn) {
            return;
        }
        $q = @$conn->query("SHOW COLUMNS FROM tbl_users2 LIKE 'LastWorkDate'");
        if ($q && $q->num_rows === 0) {
            @$conn->query("ALTER TABLE tbl_users2 ADD COLUMN `LastWorkDate` DATE NULL DEFAULT NULL");
        }
    }
}

if (!function_exists('ensure_tbl_users2_partial_reporting_column')) {
    function ensure_tbl_users2_partial_reporting_column()
    {
        global $conn;
        if (!$conn) {
            return;
        }
        $q = @$conn->query("SHOW COLUMNS FROM tbl_users2 LIKE 'PartialReporting'");
        if ($q && $q->num_rows === 0) {
            @$conn->query("ALTER TABLE tbl_users2 ADD COLUMN `PartialReporting` INT(11) NOT NULL DEFAULT 0");
        }
    }
}

if (!function_exists('ensure_tbl_users2_company_number_column')) {
    function ensure_tbl_users2_company_number_column()
    {
        global $conn;
        if (!$conn) {
            return;
        }
        $q = @$conn->query("SHOW COLUMNS FROM tbl_users2 LIKE 'CompanyNumber'");
        if ($q && $q->num_rows === 0) {
            @$conn->query("ALTER TABLE tbl_users2 ADD COLUMN `CompanyNumber` VARCHAR(50) NULL DEFAULT NULL");
        }
    }
}

if (!function_exists('maha_ensure_users_options2_column')) {
    function maha_ensure_users_options2_column()
    {
        global $conn;
        if (!$conn) {
            return;
        }
        $q = @$conn->query("SHOW COLUMNS FROM tbl_users LIKE 'Options2'");
        if (!$q || $q->num_rows === 0) {
            @$conn->query("ALTER TABLE tbl_users ADD COLUMN `Options2` MEDIUMTEXT NULL");
            return;
        }
        $row = $q->fetch_assoc();
        $type = strtolower((string) ($row['Type'] ?? ''));
        if (strpos($type, 'text') === false && strpos($type, 'blob') === false) {
            @$conn->query("ALTER TABLE tbl_users MODIFY COLUMN `Options2` MEDIUMTEXT NULL");
        }
    }
}

require_once dirname(__DIR__) . '/admin-sidebar-menu-permissions-render.php';

if (isset($_POST['action']) && $_POST['action'] === 'SaveMenuAccess') {
    header('Content-Type: application/json; charset=utf-8');
    if (!$user_id) {
        http_response_code(403);
        echo json_encode(array('ok' => false, 'error' => 'Session expired'));
        exit;
    }
    if (!emp_user_can_save_menu_access($user_id)) {
        http_response_code(403);
        echo json_encode(array('ok' => false, 'error' => 'You cannot edit menu access'));
        exit;
    }
    $empId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    if ($empId <= 0) {
        http_response_code(400);
        echo json_encode(array('ok' => false, 'error' => 'Invalid employee id'));
        exit;
    }
    maha_ensure_users_options2_column();
    $_POST['cp_menu_access_present'] = '1';
    $built = emp_build_options2_from_post($_POST);
    if ($built === null) {
        http_response_code(400);
        echo json_encode(array('ok' => false, 'error' => 'No menu access data received'));
        exit;
    }
    if (!emp_persist_user_menu_access($conn, $empId, $built)) {
        http_response_code(500);
        echo json_encode(array(
            'ok' => false,
            'error' => 'Database save failed',
            'detail' => isset($conn) && $conn ? $conn->error : '',
        ));
        exit;
    }
    echo json_encode(array(
        'ok' => true,
        'options2' => $built,
        'employee_id' => $empId,
    ));
    exit;
}

if (isset($_POST['action']) && $_POST['action'] == 'Save') {
    if (!$user_id) {
        header('HTTP/1.1 403 Forbidden');
        echo "<script>alert('Session expired. Please login again.');window.location.href='../index.php';</script>";
        exit;
    }
try {

ensure_tbl_users2_partial_reporting_column();
ensure_tbl_users2_last_work_date_column();
ensure_tbl_users2_company_number_column();
maha_ensure_users_options2_column();

    $id = isset($_POST['id']) ? $_POST['id'] : '';
$Fname = addslashes(trim($_POST['Fname'] ?? ''));
$Mname = addslashes(trim($_POST['Mname'] ?? ''));
$Lname = addslashes(trim($_POST['Lname'] ?? ''));
$Phone = $_POST['Phone'] ?? '';
$EmailId = $_POST['EmailId'] ?? '';
$Phone2 = $_POST['Phone2'] ?? '';
$passwordPlain = isset($_POST['Password']) ? (string) $_POST['Password'] : '';
$Password = addslashes($passwordPlain);
$CountryId = addslashes($_POST['CountryId'] ?? '');
$StateId = addslashes($_POST['StateId'] ?? '');
$CityId = addslashes($_POST['CityId'] ?? '');
$Address = addslashes(trim($_POST['Address'] ?? ''));
$GstNo = addslashes(trim($_POST['GstNo'] ?? ''));
$Pincode = trim($_POST['Pincode'] ?? '');
$Details = addslashes(trim($_POST['Details'] ?? ''));

$FatherPhone = addslashes(trim($_POST['FatherPhone'] ?? ''));
$Designation = addslashes(trim($_POST['Designation'] ?? ''));
$Dob = addslashes(trim($_POST['Dob'] ?? ''));
$AadharNo = addslashes(trim($_POST['AadharNo'] ?? ''));
$BloodGroup = addslashes(trim($_POST['BloodGroup'] ?? ''));
$JoinDate = addslashes(trim($_POST['JoinDate'] ?? ''));
$EmailId2 = addslashes(trim($_POST['EmailId2'] ?? ''));
$PerDaySalary = addslashes(trim($_POST['PerDaySalary'] ?? ''));
$SalaryEffectiveFrom = addslashes(trim($_POST['SalaryEffectiveFrom'] ?? ''));

$Status = $_POST['Status'] ?? '1';
$CatId = $_POST['CatId'] ?? '';
$Roll = $_POST['Roll'] ?? '';

$canSaveMenuAccess = emp_user_can_save_menu_access($user_id);
$isEditEmployee = ($id !== '' && $id !== null);
$Options2FromPost = null;
$Options2 = '0';
if (!$isEditEmployee) {
    $Options2FromPost = emp_build_options2_from_post($_POST);
    $Options2 = emp_resolve_options2_for_insert($_POST);
}

if(!empty($_POST['ExpCatId']) && is_array($_POST['ExpCatId'])){
$ExpCatId = implode(",", $_POST['ExpCatId']);
}
else{
   $ExpCatId = 0; 
}

/*if($_POST['CocoFranchiseAccess']!=''){
$CocoFranchiseAccess = implode(",", $_POST['CocoFranchiseAccess']);
}
else{
   $CocoFranchiseAccess = 0; 
}*/

if(!empty($_POST['AssignFranchiseAttendance']) && is_array($_POST['AssignFranchiseAttendance'])){
$AssignFranchiseAttendance = implode(",", $_POST['AssignFranchiseAttendance']);
}
else{
   $AssignFranchiseAttendance = 0; 
}

if(!empty($_POST['AssignFranchiseBdm']) && is_array($_POST['AssignFranchiseBdm'])){
$AssignFranchiseBdm = implode(",", $_POST['AssignFranchiseBdm']);
}
else{
   $AssignFranchiseBdm = 0; 
}




if(!empty($_POST['zone']) && is_array($_POST['zone'])){
$zone = implode(",", $_POST['zone']);
$sql = "SELECT frids FROM tbl_assign_fr_to_zone WHERE zone IN($zone)";
$row = getRecord($sql);
$CocoFranchiseAccess = isset($row['frids']) ? $row['frids'] : 0;
}
else{
   $zone = 0; 
   $CocoFranchiseAccess = 0; 
}

if(!empty($_POST['cpzones']) && is_array($_POST['cpzones'])){
$cpzones = implode(",", $_POST['cpzones']);
}
else{
   $cpzones = 0; 
}

if(!empty($_POST['cpsubzones']) && is_array($_POST['cpsubzones'])){
$cpsubzones = implode(",", $_POST['cpsubzones']);
}
else{
   $cpsubzones = 0; 
}

if(!empty($_POST['cpfranchise']) && is_array($_POST['cpfranchise'])){
$cpfranchise = implode(",", $_POST['cpfranchise']);
}
else{
   $cpfranchise = 0; 
}

if(!empty($_POST['attzones']) && is_array($_POST['attzones'])){
    $att_zones = implode(",", array_map('intval', $_POST['attzones']));
} else {
    $att_zones = 0;
}
if(!empty($_POST['attsubzones']) && is_array($_POST['attsubzones'])){
    $att_subzones = implode(",", array_map('intval', $_POST['attsubzones']));
} else {
    $att_subzones = 0;
}
if(!empty($_POST['bdmzones']) && is_array($_POST['bdmzones'])){
    $bdm_zones = implode(",", array_map('intval', $_POST['bdmzones']));
} else {
    $bdm_zones = 0;
}
if(!empty($_POST['bdmsubzones']) && is_array($_POST['bdmsubzones'])){
    $bdm_subzones = implode(",", array_map('intval', $_POST['bdmsubzones']));
} else {
    $bdm_subzones = 0;
}

if(!empty($_POST['vedzones'])){
    $vedzones = implode(",", $_POST['vedzones']);
} else {
    $vedzones = 0;
}

if(!empty($_POST['nsovedzones'])){
    $nsovedzones = implode(",", $_POST['nsovedzones']);
} else {
    $nsovedzones = 0;
}


if(!empty($_POST['vedSubzones'])){
    $vedSubzones = implode(",", $_POST['vedSubzones']);
} else {
    $vedSubzones = 0;
}

if(!empty($_POST['nsovedSubzones'])){
    $nsovedSubzones = implode(",", $_POST['nsovedSubzones']);
} else {
    $nsovedSubzones = 0;
}

$vedfranchiseCheck = isset($_POST['vedfranchiseCheck']) ? $_POST['vedfranchiseCheck'] : 0;
$nsovedfranchiseCheck = isset($_POST['nsovedfranchiseCheck']) ? $_POST['nsovedfranchiseCheck'] : 0;

// Increase concat length to avoid truncation
$conn->query("SET SESSION group_concat_max_len = 1000000");

// VED Franchise Logic
if($vedfranchiseCheck == 1){
    $AssignFranchiseVedExp = (!empty($_POST['AssignFranchiseVedExp']) && is_array($_POST['AssignFranchiseVedExp'])) 
        ? implode(",", $_POST['AssignFranchiseVedExp']) 
        : 0;
} else {
    $sqlVed = "SELECT GROUP_CONCAT(id) AS FrId 
               FROM tbl_users 
               WHERE SubZoneId IN ($vedSubzones) AND Status=1";
    $rowVed = getRecord($sqlVed);
    $AssignFranchiseVedExp = isset($rowVed['FrId']) && $rowVed['FrId'] !== null ? $rowVed['FrId'] : 0;
}

// NSO VED Franchise Logic
if($nsovedfranchiseCheck == 1){
    $AssignFranchiseNsoVedExp = (!empty($_POST['AssignFranchiseNsoVedExp']) && is_array($_POST['AssignFranchiseNsoVedExp']))
        ? implode(",", $_POST['AssignFranchiseNsoVedExp'])
        : 0;
} else {
    $sqlNso = "SELECT GROUP_CONCAT(id) AS FrId 
               FROM tbl_users 
               WHERE SubZoneId IN ($nsovedSubzones) AND Status=1";
    $rowNso = getRecord($sqlNso);
    $AssignFranchiseNsoVedExp = isset($rowNso['FrId']) && $rowNso['FrId'] !== null ? $rowNso['FrId'] : 0;
}


$PanNo = addslashes(trim($_POST['PanNo'] ?? ''));
$CompId = addslashes(trim($_POST['CompId'] ?? ''));
$BranchId = addslashes(trim($_POST['BranchId'] ?? ''));
$CreatedDate = date('Y-m-d');
$pageval = addslashes(trim($_POST['pageval'] ?? ''));

$AccountName = addslashes(trim($_POST['AccountName'] ?? ''));
$BankName = addslashes(trim($_POST['BankName'] ?? ''));
$AccountNo = addslashes(trim($_POST['AccountNo'] ?? ''));
$IfscCode = addslashes(trim($_POST['IfscCode'] ?? ''));
$Branch = addslashes(trim($_POST['Branch'] ?? ''));
$UpiNo = addslashes(trim($_POST['UpiNo'] ?? ''));
$UnderUser = addslashes(trim($_POST['UnderUser'] ?? ''));
$ReportingMgr = addslashes(trim($_POST['ReportingMgr'] ?? ''));
$ResignStatus = addslashes(trim($_POST['ResignStatus'] ?? ''));
$ResignDate = addslashes(trim($_POST['ResignDate'] ?? ''));
$LastWorkDate = addslashes(trim($_POST['LastWorkDate'] ?? ''));
$ResignComment = addslashes(trim($_POST['ResignComment'] ?? ''));
$SalaryType = addslashes(trim($_POST['SalaryType'] ?? ''));
$CreditSalaryStatus = addslashes(trim($_POST['CreditSalaryStatus'] ?? ''));

$MainBrEmp = addslashes(trim($_POST['MainBrEmp'] ?? ''));
$RestrictAttAfter1015 = (isset($_POST['RestrictAttAfter1015']) && (string) $_POST['RestrictAttAfter1015'] === '1') ? '1' : '0';
$UnderByUser = addslashes(trim($_POST['UnderByUser'] ?? ''));

$PersonalName1 = addslashes(trim($_POST['PersonalName1'] ?? ''));
$PersonalRelation1 = addslashes(trim($_POST['PersonalRelation1'] ?? ''));
$PersonalPhone1 = addslashes(trim($_POST['PersonalPhone1'] ?? ''));
$PersonalName2 = addslashes(trim($_POST['PersonalName2'] ?? ''));
$PersonalRelation2 = addslashes(trim($_POST['PersonalRelation2'] ?? ''));
$PersonalPhone2 = addslashes(trim($_POST['PersonalPhone2'] ?? ''));
$ProfName1 = addslashes(trim($_POST['ProfName1'] ?? ''));
$ProfDesignation1 = addslashes(trim($_POST['ProfDesignation1'] ?? ''));
$ProfPhone1 = addslashes(trim($_POST['ProfPhone1'] ?? ''));
$ProfName2 = addslashes(trim($_POST['ProfName2'] ?? ''));
$ProfDesignation2 = addslashes(trim($_POST['ProfDesignation2'] ?? ''));
$ProfPhone2 = addslashes(trim($_POST['ProfPhone2'] ?? ''));
$CompanyNumber = addslashes(trim($_POST['CompanyNumber'] ?? ''));

$NomineeName = addslashes(trim($_POST['NomineeName'] ?? ''));
$NomineeRelation = addslashes(trim($_POST['NomineeRelation'] ?? ''));
$NomineePhone = addslashes(trim($_POST['NomineePhone'] ?? ''));
$NomineeAadharNo = addslashes(trim($_POST['NomineeAadharNo'] ?? ''));
$MonthlySalary = addslashes(trim($_POST['MonthlySalary'] ?? ''));
$MgrCheckpoint = addslashes(trim($_POST['MgrCheckpoint'] ?? ''));
$OtherEmp = addslashes(trim($_POST['OtherEmp'] ?? ''));
$InternshipEmp = addslashes(trim($_POST['InternshipEmp'] ?? ''));
$NoticePeriod = addslashes(trim($_POST['NoticePeriod'] ?? ''));
$ReferCode = addslashes(trim($_POST['ReferCode'] ?? ''));
$ReferId = addslashes(trim($_POST['ReferId'] ?? ''));

$Education = addslashes(trim($_POST['Education'] ?? ''));
$UanNo = addslashes(trim($_POST['UanNo'] ?? ''));
$NsoVedPay = addslashes(trim($_POST['NsoVedPay'] ?? ''));
$YearlyWeekOff = addslashes(trim($_POST['YearlyWeekOff'] ?? ''));
$MonthlyWeekOff = addslashes(trim($_POST['MonthlyWeekOff'] ?? ''));
$ReJoinDate = addslashes(trim($_POST['ReJoinDate'] ?? ''));
$ApproveBy = addslashes(trim($_POST['ApproveBy'] ?? ''));
$UnderByBdm = addslashes(trim($_POST['UnderByBdm'] ?? ''));
$AnniversaryDate = addslashes(trim($_POST['AnniversaryDate'] ?? ''));
$Increment = addslashes(trim($_POST['Increment'] ?? ''));
$IncrementPer = addslashes(trim($_POST['IncrementPer'] ?? ''));

$PettyCash = addslashes(trim($_POST['PettyCash'] ?? ''));
$PettyAmount = addslashes(trim($_POST['PettyAmount'] ?? ''));
$MarkAttendance = addslashes(trim($_POST['MarkAttendance'] ?? ''));
$VendorExpSecOpt = addslashes(trim($_POST['VendorExpSecOpt'] ?? ''));
$EmpStatus = addslashes(trim($_POST['EmpStatus'] ?? ''));
$EmpScheme = addslashes(trim($_POST['EmpScheme'] ?? ''));
$EsicNo = addslashes(trim($_POST['EsicNo'] ?? ''));
$BdmCheckpoint = addslashes(trim($_POST['BdmCheckpoint'] ?? ''));
$EmpAppDashboard = addslashes(trim($_POST['EmpAppDashboard'] ?? ''));
$CashHandover = addslashes(trim($_POST['CashHandover'] ?? ''));
$WorkingHrs = addslashes(trim($_POST['WorkingHrs'] ?? ''));
$TotalHours = addslashes(trim($_POST['TotalHours'] ?? ''));
$DisabledAttPhoto = addslashes(trim($_POST['DisabledAttPhoto'] ?? ''));
$cofofr = addslashes(trim($_POST['cofofr'] ?? ''));
$Gender = addslashes(trim($_POST['Gender'] ?? ''));

$WeekOffDay = addslashes(trim($_POST['WeekOffDay'] ?? ''));
$Grade = addslashes(trim($_POST['Grade'] ?? ''));
$Cl = addslashes(trim($_POST['Cl'] ?? ''));
$El = addslashes(trim($_POST['El'] ?? ''));
$PartialReporting = (int) ($_POST['PartialReporting'] ?? 0);
$ticketshow = addslashes(trim($_POST['ticketshow'] ?? ''));
$att_task_show = addslashes(trim($_POST['att_task_show'] ?? ''));
 
$OpenTime = trim($_POST['OpenTime'] ?? '09:00');
$CloseTime = trim($_POST['CloseTime'] ?? '18:00');
$OpenTime24  = date("H:i", strtotime($OpenTime) ?: time());
$CloseTime24 = date("H:i", strtotime($CloseTime) ?: time());

$ReceipeSosReply = addslashes(trim($_POST['ReceipeSosReply'] ?? 0));
$ProfitLossReport = addslashes(trim($_POST['ProfitLossReport'] ?? 0));




$Photo = isset($_POST['OldPhoto']) ? $_POST['OldPhoto'] : '';
if (!empty($_FILES['Photo']['tmp_name']) && $_FILES['Photo']['error'] === UPLOAD_ERR_OK && !empty($_FILES['Photo']['name'])) {
    $randno = rand(1,100);
    $src = $_FILES['Photo']['tmp_name'];
    $fnm = pathinfo($_FILES['Photo']['name'], PATHINFO_FILENAME);
    $fnm = str_replace(" ","_", $fnm);
    $ext = pathinfo($_FILES['Photo']['name'], PATHINFO_EXTENSION);
    if ($ext !== '') $ext = '.' . $ext;
    $dest = '../../uploads/'. $randno . "_". $fnm . $ext;
    $imagepath = $randno . "_". $fnm . $ext;
    if (move_uploaded_file($src, $dest)) {
        $Photo = $imagepath;
    }
}

$Photo2 = isset($_POST['OldPhoto2']) ? $_POST['OldPhoto2'] : '';
if (!empty($_FILES['Photo2']['tmp_name']) && $_FILES['Photo2']['error'] === UPLOAD_ERR_OK && !empty($_FILES['Photo2']['name'])) {
    $randno2 = rand(1,100);
    $src2 = $_FILES['Photo2']['tmp_name'];
    $fnm2 = str_replace(" ","_", pathinfo($_FILES['Photo2']['name'], PATHINFO_FILENAME));
    $ext2 = pathinfo($_FILES['Photo2']['name'], PATHINFO_EXTENSION);
    if ($ext2 !== '') $ext2 = '.' . $ext2;
    $dest2 = '../../uploads/'. $randno2 . "_". $fnm2 . $ext2;
    $imagepath2 = $randno2 . "_". $fnm2 . $ext2;
    if (move_uploaded_file($src2, $dest2)) {
        $Photo2 = $imagepath2;
    }
}


$Photo3 = isset($_POST['OldPhoto3']) ? $_POST['OldPhoto3'] : '';
if (!empty($_FILES['Photo3']['tmp_name']) && $_FILES['Photo3']['error'] === UPLOAD_ERR_OK && !empty($_FILES['Photo3']['name'])) {
    $randno3 = rand(1,100);
    $src3 = $_FILES['Photo3']['tmp_name'];
    $fnm3 = str_replace(" ","_", pathinfo($_FILES['Photo3']['name'], PATHINFO_FILENAME));
    $ext3 = pathinfo($_FILES['Photo3']['name'], PATHINFO_EXTENSION);
    if ($ext3 !== '') $ext3 = '.' . $ext3;
    $dest3 = '../../uploads/'. $randno3 . "_". $fnm3 . $ext3;
    $imagepath3 = $randno3 . "_". $fnm3 . $ext3;
    if (move_uploaded_file($src3, $dest3)) {
        $Photo3 = $imagepath3;
    }
}

$DeclarationPdf = isset($_POST['OldDeclarationPdf']) ? $_POST['OldDeclarationPdf'] : '';
if (!empty($_FILES['DeclarationPdf']['tmp_name']) && $_FILES['DeclarationPdf']['error'] === UPLOAD_ERR_OK && !empty($_FILES['DeclarationPdf']['name'])) {
    $randno4 = rand(1,100);
    $src4 = $_FILES['DeclarationPdf']['tmp_name'];
    $fnm4 = str_replace(" ","_", pathinfo($_FILES['DeclarationPdf']['name'], PATHINFO_FILENAME));
    $ext4 = pathinfo($_FILES['DeclarationPdf']['name'], PATHINFO_EXTENSION);
    if ($ext4 !== '') $ext4 = '.' . $ext4;
    $dest4 = '../../uploads/'. $randno4 . "_". $fnm4 . $ext4;
    $imagepath4 = $randno4 . "_". $fnm4 . $ext4;
    if (move_uploaded_file($src4, $dest4)) {
        $DeclarationPdf = $imagepath4;
    }
}

$DeclarationPhoto = isset($_POST['OldDeclarationPhoto']) ? $_POST['OldDeclarationPhoto'] : '';
if (!empty($_FILES['DeclarationPhoto']['tmp_name']) && $_FILES['DeclarationPhoto']['error'] === UPLOAD_ERR_OK && !empty($_FILES['DeclarationPhoto']['name'])) {
    $randno5 = rand(1,100);
    $src5 = $_FILES['DeclarationPhoto']['tmp_name'];
    $fnm5 = str_replace(" ","_", pathinfo($_FILES['DeclarationPhoto']['name'], PATHINFO_FILENAME));
    $ext5 = pathinfo($_FILES['DeclarationPhoto']['name'], PATHINFO_EXTENSION);
    if ($ext5 !== '') $ext5 = '.' . $ext5;
    $dest5 = '../../uploads/'. $randno5 . "_". $fnm5 . $ext5;
    $imagepath5 = $randno5 . "_". $fnm5 . $ext5;
    if (move_uploaded_file($src5, $dest5)) {
        $DeclarationPhoto = $imagepath5;
    }
}


$paymentData = [];

for ($i = 1; $i <= 6; $i++) {
    $paymentData[$i] = [
        'FamilyName'   => addslashes(trim($_POST["FamilyName$i"] ?? '')),
        'FamilyMobile'  => addslashes(trim($_POST["FamilyMobile$i"] ?? '')),
        'EmpRelation'   => addslashes(trim($_POST["EmpRelation$i"] ?? '')),
        'FamilyDob'  => addslashes(trim($_POST["FamilyDob$i"] ?? '')),
        'FamilyResident'     => addslashes(trim($_POST["FamilyResident$i"] ?? '')),
        'FamilyCity'     => addslashes(trim($_POST["FamilyCity$i"] ?? '')),
        'FamilyState'     => addslashes(trim($_POST["FamilyState$i"] ?? '')),
    ];
}

$updateFields = [];

foreach ($paymentData as $i => $data) {
    $updateFields[] = "FamilyName$i='{$data['FamilyName']}'";
    $updateFields[] = "FamilyMobile$i='{$data['FamilyMobile']}'";
    $updateFields[] = "EmpRelation$i='{$data['EmpRelation']}'";
    $updateFields[] = "FamilyDob$i='{$data['FamilyDob']}'";
    $updateFields[] = "FamilyResident$i='{$data['FamilyResident']}'";
    $updateFields[] = "FamilyCity$i='{$data['FamilyCity']}'";
    $updateFields[] = "FamilyState$i='{$data['FamilyState']}'";
}

 $updateString = implode(', ', $updateFields);
 
$UnderFrId = addslashes(trim($_POST['UnderFrId'] ?? ''));
$editorsRequiringUnderFr = array(2651, 2650, 22170, 2799);
if (in_array((int) $user_id, $editorsRequiringUnderFr, true) && (int) $UnderFrId <= 0) {
    echo '0';
    exit;
}
$ZoneId = '';
if ($UnderFrId !== '') {
    $sql55 = "SELECT ZoneId FROM tbl_users WHERE id='$UnderFrId'";
    $row55 = getRecord($sql55);
    $ZoneId = isset($row55['ZoneId']) ? $row55['ZoneId'] : '';
}

$tempDir = __DIR__ . '/../../barcodes/';
if (!is_dir($tempDir)) {
    @mkdir($tempDir, 0755, true);
}

$idInt = ($id !== '' && $id !== null) ? (int) $id : 0;
$enforceStrongPassword = true;
if ($idInt > 0) {
    $escId = mysqli_real_escape_string($conn, (string) $id);
    $existingPwRow = getRecord("SELECT Password FROM tbl_users WHERE id='$escId' LIMIT 1");
    if (is_array($existingPwRow) && array_key_exists('Password', $existingPwRow)
        && (string) $existingPwRow['Password'] === $passwordPlain) {
        $enforceStrongPassword = false;
    }
}
if ($enforceStrongPassword && !maha_employee_password_is_strong($passwordPlain)) {
    http_response_code(422);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok' => false,
        'code' => 'PASSWORD_POLICY_ERROR',
        'message' => 'Please enter a proper password: at least 8 characters including letters, numbers, and symbols (for example ! @ # $).',
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if($id == ''){
    try {
        maha_ensure_employee_salary_history_table();

        // Begin transaction
        $conn->begin_transaction();
        
        // Block only if an active employee already uses this phone
        $PhoneEsc = addslashes(trim($Phone));
        $sql2 = "SELECT id FROM tbl_users WHERE Phone='$PhoneEsc' AND Status='1' AND Roll NOT IN(1,5,55,9,22,23,63)";
        $rncnt2 = getRow($sql2);
        if($rncnt2 > 0){
            $conn->rollback();
            if($pageval == 'lead'){
                echo "<script>alert('Phone No Already Exists With Active Employee!');window.location.href='../add-sales-lead-manager.php';</script>";  
            }
            else if($pageval == 'flexi'){
                echo "<script>alert('Phone No Already Exists With Active Employee!');window.location.href='../add-flexi-manager.php';</script>";
            }
            else if($pageval == 'service'){
                echo "<script>alert('Phone No Already Exists With Active Employee!');window.location.href='../add-service-manager.php';</script>";   
            }
            else{
                echo "<script>alert('Phone No Already Exists With Active Employee!');window.location.href='../add-employee.php';</script>";
            }
            exit;
        }
        
        // Prepare new data for logging
        $new_data = array(
            'Fname' => $Fname,
            'Mname' => $Mname,
            'Lname' => $Lname,
            'Phone' => $Phone,
            'EmailId' => $EmailId,
            'Roll' => $Roll,
            'Status' => $Status,
            'Designation' => $Designation,
            'JoinDate' => $JoinDate
        );

        $inactiveAtSqlVal = ((int) $Status === 0)
            ? "'" . mysqli_real_escape_string($conn, date('Y-m-d H:i:s')) . "'"
            : 'NULL';

        $Options2Esc = mysqli_real_escape_string($conn, (string) $Options2);
        $sql = "INSERT INTO tbl_users SET ProfitLossReport='$ProfitLossReport',ReceipeSosReply='$ReceipeSosReply',att_task_show='$att_task_show',ticketshow='$ticketshow',Gender='$Gender',cofofr='$cofofr',vedSubzones='$vedSubzones',nsovedSubzones='$nsovedSubzones',DisabledAttPhoto='$DisabledAttPhoto',vedfranchiseCheck='$vedfranchiseCheck',nsovedfranchiseCheck='$nsovedfranchiseCheck',vedzones='$vedzones',nsovedzones='$nsovedzones',cpzones='$cpzones',AssignFranchiseNsoVedExp='$AssignFranchiseNsoVedExp',TotalHours='$TotalHours',OpenTime='$OpenTime',CloseTime='$CloseTime',OpenTime24='$OpenTime24',CloseTime24='$CloseTime24',WorkingHrs='$WorkingHrs',CashHandover='$CashHandover',InternshipEmp='$InternshipEmp',EmpAppDashboard='$EmpAppDashboard',AssignFranchiseBdm='$AssignFranchiseBdm',BdmCheckpoint='$BdmCheckpoint',EsicNo='$EsicNo',EmpScheme='$EmpScheme',EmpStatus='$EmpStatus',VendorExpSecOpt='$VendorExpSecOpt',MarkAttendance='$MarkAttendance',PettyCash='$PettyCash',PettyAmount='$PettyAmount',ReferId='$ReferId',IncrementPer='$IncrementPer',Increment='$Increment',AnniversaryDate='$AnniversaryDate',UnderByBdm='$UnderByBdm',ApproveBy='$ApproveBy',ReJoinDate='$ReJoinDate',YearlyWeekOff='$YearlyWeekOff',MonthlyWeekOff='$MonthlyWeekOff',NsoVedPay='$NsoVedPay',AssignFranchiseVedExp='$AssignFranchiseVedExp',AssignFranchiseAttendance='$AssignFranchiseAttendance',Education='$Education',UanNo='$UanNo',ReferCode='$ReferCode',NoticePeriod='$NoticePeriod',OtherEmp='$OtherEmp',MgrCheckpoint='$MgrCheckpoint',DeclarationPhoto='$DeclarationPhoto',ZoneId='$ZoneId',MonthlySalary='$MonthlySalary',DeclarationPdf='$DeclarationPdf',NomineeName='$NomineeName',NomineeRelation='$NomineeRelation',NomineePhone='$NomineePhone',NomineeAadharNo='$NomineeAadharNo',zone='$zone',CocoFranchiseAccess='$CocoFranchiseAccess',SalaryType='$SalaryType',CreditSalaryStatus='$CreditSalaryStatus',Fname='$Fname',Mname='$Mname',Lname='$Lname',Phone='$Phone',EmailId='$EmailId',Password='$Password',Phone2='$Phone2',CountryId='$CountryId',StateId='$StateId',CityId='$CityId',Address='$Address',Pincode='$Pincode',Status='$Status',Photo='$Photo',Roll='$Roll',CreatedDate='$CreatedDate',CreatedBy='$user_id',GstNo='$GstNo',Photo2='$Photo2',Photo3='$Photo3',Details='$Details',CatId='$CatId',PanNo='$PanNo',Options2='$Options2Esc',CompId='$CompId',BranchId='$BranchId',FatherPhone='$FatherPhone',Designation='$Designation',Dob='$Dob',AadharNo='$AadharNo',BloodGroup='$BloodGroup',JoinDate='$JoinDate',EmailId2='$EmailId2',PerDaySalary='$PerDaySalary',AccountName='$AccountName',BankName='$BankName',AccountNo='$AccountNo',IfscCode='$IfscCode',Branch='$Branch',UpiNo='$UpiNo',UnderUser='$UnderUser',ReportingMgr='$ReportingMgr',ResignStatus='$ResignStatus',ResignDate='$ResignDate',ResignComment='$ResignComment',UnderFrId='$UnderFrId',ExpCatId='$ExpCatId',MainBrEmp='$MainBrEmp',UnderByUser='$UnderByUser',$updateString";
        
        if (!$conn->query($sql)) {
            throw new Exception("Error inserting employee: " . $conn->error);
        }
        $EmpId = mysqli_insert_id($conn);

        if ($canSaveMenuAccess) {
            if (!emp_persist_user_options2($conn, (int) $EmpId, (string) $Options2)) {
                throw new Exception('Error saving menu access permissions');
            }
        }

        $salaryEffectiveFrom = maha_resolve_salary_effective_from($SalaryEffectiveFrom, $JoinDate);
        if (!maha_insert_employee_salary_history($EmpId, $SalaryType, $PerDaySalary, $MonthlySalary, $salaryEffectiveFrom, (int) $user_id)) {
            throw new Exception('Error inserting employee salary history');
        }

        // Insert week off day
        $sql = "INSERT INTO tbl_users2 (UserId, RestrictAttAfter1015, WeekOffDay,cpzones,cpsubzones,cpfranchise,Grade,Cl,El,att_zones,att_subzones,bdm_zones,bdm_subzones,PartialReporting,InactiveAt,LastWorkDate,CompanyNumber,PersonalName1,PersonalRelation1,PersonalPhone1,PersonalName2,PersonalRelation2,PersonalPhone2,ProfName1,ProfDesignation1,ProfPhone1,ProfName2,ProfDesignation2,ProfPhone2)
                VALUES ('$EmpId', '$RestrictAttAfter1015', '$WeekOffDay','$cpzones','$cpsubzones','$cpfranchise','$Grade','$Cl','$El','$att_zones','$att_subzones','$bdm_zones','$bdm_subzones','$PartialReporting',$inactiveAtSqlVal,'$LastWorkDate','$CompanyNumber','$PersonalName1','$PersonalRelation1','$PersonalPhone1','$PersonalName2','$PersonalRelation2','$PersonalPhone2','$ProfName1','$ProfDesignation1','$ProfPhone1','$ProfName2','$ProfDesignation2','$ProfPhone2')
                ON DUPLICATE KEY UPDATE 
                RestrictAttAfter1015='$RestrictAttAfter1015',WeekOffDay='$WeekOffDay',cpsubzones='$cpsubzones',cpzones='$cpzones',cpfranchise='$cpfranchise',Grade='$Grade',Cl='$Cl',El='$El',att_zones='$att_zones',att_subzones='$att_subzones',bdm_zones='$bdm_zones',bdm_subzones='$bdm_subzones',PartialReporting='$PartialReporting',InactiveAt=$inactiveAtSqlVal,LastWorkDate='$LastWorkDate',CompanyNumber='$CompanyNumber',PersonalName1='$PersonalName1',PersonalRelation1='$PersonalRelation1',PersonalPhone1='$PersonalPhone1',PersonalName2='$PersonalName2',PersonalRelation2='$PersonalRelation2',PersonalPhone2='$PersonalPhone2',ProfName1='$ProfName1',ProfDesignation1='$ProfDesignation1',ProfPhone1='$ProfPhone1',ProfName2='$ProfName2',ProfDesignation2='$ProfDesignation2',ProfPhone2='$ProfPhone2'";
        
        if (!$conn->query($sql)) {
            throw new Exception("Error inserting week off day: " . $conn->error);
        }
        
        // Insert bank details
        $sql2 = "INSERT INTO tbl_bank_details SET AccountName='$AccountName',BankName='$BankName',AccountNo='$AccountNo',IfscCode='$IfscCode',Branch='$Branch',Status='1',CreatedBy='$user_id',CreatedDate='$CreatedDate',userid='$EmpId',type=1";
        if (!$conn->query($sql2)) {
            throw new Exception("Error inserting bank details: " . $conn->error);
        }
        
        // Log bank details creation
        if (!empty($AccountNo)) {
            logEmployeeAction($EmpId, 'BANK_UPDATE', "Bank details created (A/C: $AccountNo, Bank: $BankName)", null, array('AccountNo' => $AccountNo, 'BankName' => $BankName, 'IfscCode' => $IfscCode));
        }

        // Handle file uploads
        if (isset($_FILES['Files']) && !empty($_FILES['Files']['name'][0])) {
            $desired_dir = "../../employee_files/" . $EmpId . "/";
            
            // Ensure base folder exists
            if (!is_dir("../../employee_files/")) {
                mkdir("../../employee_files/", 0777, true);
            }
            
            // Ensure employee folder exists
            if (!is_dir($desired_dir)) {
                mkdir($desired_dir, 0777, true);
            }
            
            $uploaded_files = array();
            foreach ($_FILES['Files']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['Files']['name'][$key];
                $file_size = $_FILES['Files']['size'][$key];
                $file_tmp  = $_FILES['Files']['tmp_name'][$key];
                
                if (!empty($file_name) && $file_size > 0) {
                    $query = "INSERT INTO tbl_user_files SET UserId='$EmpId', Files='$file_name', FileName='$file_name'";
                    
                    $target_path = $desired_dir . $file_name;
                    
                    // Move file
                    if (!file_exists($target_path)) {
                        move_uploaded_file($file_tmp, $target_path);
                    } else {
                        $new_file = $desired_dir . time() . "_" . $file_name;
                        move_uploaded_file($file_tmp, $new_file);
                    }
                    
                    if (!$conn->query($query)) {
                        throw new Exception("Error uploading file: " . $conn->error);
                    }
                    $uploaded_files[] = $file_name;
                }
            }
            
            // Log file upload
            if (!empty($uploaded_files)) {
                $log_message = "Files uploaded: " . implode(", ", $uploaded_files);
                logEmployeeAction($EmpId, 'FILE_UPLOAD', $log_message, null, array('Files' => $uploaded_files));
            }
        }



        // Handle Files2 uploads
        if (isset($_FILES['Files2']) && !empty($_FILES['Files2']['name'][0])) {
            $desired_dir = "../../employee_files2/" . $EmpId . "/";
            
            // Ensure base folder exists
            if (!is_dir("../../employee_files2/")) {
                mkdir("../../employee_files2/", 0777, true);
            }
            
            // Ensure employee folder exists
            if (!is_dir($desired_dir)) {
                mkdir($desired_dir, 0777, true);
            }
            
            $uploaded_files2 = array();
            foreach ($_FILES['Files2']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['Files2']['name'][$key];
                $file_size = $_FILES['Files2']['size'][$key];
                $file_tmp  = $_FILES['Files2']['tmp_name'][$key];
                
                if (!empty($file_name) && $file_size > 0) {
                    $query = "INSERT INTO tbl_user_files2 SET UserId='$EmpId', Files='$file_name', FileName='$file_name'";
                    
                    $target_path = $desired_dir . $file_name;
                    
                    if (!file_exists($target_path)) {
                        move_uploaded_file($file_tmp, $target_path);
                    } else {
                        $new_file = $desired_dir . time() . "_" . $file_name;
                        move_uploaded_file($file_tmp, $new_file);
                    }
                    
                    if (!$conn->query($query)) {
                        throw new Exception("Error uploading file2: " . $conn->error);
                    }
                    $uploaded_files2[] = $file_name;
                }
            }
            
            // Log file upload
            if (!empty($uploaded_files2)) {
                $log_message = "Files2 uploaded: " . implode(", ", $uploaded_files2);
                logEmployeeAction($EmpId, 'FILE_UPLOAD', $log_message, null, array('Files' => $uploaded_files2));
            }
        }






        // Insert customer address
        $AreaId = isset($_POST['AreaId']) ? addslashes(trim($_POST['AreaId'])) : '';
        $sql3 = "INSERT INTO customer_address SET UserId='$EmpId',Fname='$Fname',Lname='$Lname',Phone='$Phone',EmailId='$EmailId',CountryId='$CountryId',StateId='$StateId',CityId='$CityId',AreaId='$AreaId',Address='$Address',Pincode='$Pincode',Status='1',CreatedDate='$CreatedDate'";
        if (!$conn->query($sql3)) {
            throw new Exception("Error inserting customer address: " . $conn->error);
        }
        
        // Generate QR Code
        $filename = $EmpId.".png";
        $codeContents = $Phone;
        $Barcode = '';
        if (class_exists('QRcode')) {
            @QRcode::png($codeContents, $tempDir . $filename, QR_ECLEVEL_L, 5);
            $Barcode = $filename;
        }
        
        $CustomerId = "MH".$EmpId;
        $sql3 = "UPDATE tbl_users SET Barcode='$Barcode',CustomerId='$CustomerId' WHERE id='$EmpId'";
        if (!$conn->query($sql3)) {
            throw new Exception("Error updating barcode: " . $conn->error);
        }
        
        // Log employee creation
        $new_data['id'] = $EmpId;
        $new_data['CustomerId'] = $CustomerId;
        $full_name = trim("$Fname $Mname $Lname");
        $log_message = "Employee created: $full_name (Phone: $Phone, Roll: $Roll)";
        logEmployeeAction($EmpId, 'CREATE', $log_message, null, $new_data);
        
        // Commit transaction
        $conn->commit();

        // Redirect based on pageval
        if($pageval == 'lead'){
            echo "<script>alert('Record Created Successfully!');window.location.href='../view-sales-lead-manager.php';</script>";
        }
        else if($pageval == 'flexi'){
            echo "<script>alert('Record Created Successfully!');window.location.href='../view-flexi-manager.php';</script>";
        }
        else if($pageval == 'service'){
            echo "<script>alert('Record Created Successfully!');window.location.href='../view-service-manager.php';</script>";
        }
        else{
            echo "<script>alert('Record Created Successfully!');window.location.href='../view-employee.php';</script>";
        }
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Error creating employee: " . $e->getMessage());
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');history.back();</script>";
    }
} else {
    try {
        maha_ensure_employee_salary_history_table();

        // Begin transaction
        $conn->begin_transaction();
        
        // Get old data before update for logging
        $sql_old = "SELECT * FROM tbl_users WHERE id='$id'";
        $old_record = getRecord($sql_old);
        $old_data = $old_record ? $old_record : null;

        $empShowAllTabs = in_array((int) $user_id, array(2650, 2651), true);
        if (!$empShowAllTabs && $old_data) {
            $oldU2 = getRecord("SELECT * FROM tbl_users2 WHERE UserId='$id'");
            $preserveEsc = function ($v) {
                return addslashes((string) ($v ?? ''));
            };
            if (is_array($oldU2)) {
                $cpzones = $preserveEsc($oldU2['cpzones'] ?? '0');
                $cpsubzones = $preserveEsc($oldU2['cpsubzones'] ?? '0');
                $cpfranchise = $preserveEsc($oldU2['cpfranchise'] ?? '0');
                $att_zones = $preserveEsc($oldU2['att_zones'] ?? '0');
                $att_subzones = $preserveEsc($oldU2['att_subzones'] ?? '0');
                $bdm_zones = $preserveEsc($oldU2['bdm_zones'] ?? '0');
                $bdm_subzones = $preserveEsc($oldU2['bdm_subzones'] ?? '0');
            }
            $AssignFranchiseAttendance = $preserveEsc($old_data['AssignFranchiseAttendance'] ?? '0');
            $AssignFranchiseBdm = $preserveEsc($old_data['AssignFranchiseBdm'] ?? '0');
            $AssignFranchiseVedExp = $preserveEsc($old_data['AssignFranchiseVedExp'] ?? '0');
            $AssignFranchiseNsoVedExp = $preserveEsc($old_data['AssignFranchiseNsoVedExp'] ?? '0');
            $vedzones = $preserveEsc($old_data['vedzones'] ?? '0');
            $nsovedzones = $preserveEsc($old_data['nsovedzones'] ?? '0');
            $vedSubzones = $preserveEsc($old_data['vedSubzones'] ?? '0');
            $nsovedSubzones = $preserveEsc($old_data['nsovedSubzones'] ?? '0');
            $vedfranchiseCheck = $preserveEsc($old_data['vedfranchiseCheck'] ?? '0');
            $nsovedfranchiseCheck = $preserveEsc($old_data['nsovedfranchiseCheck'] ?? '0');
            $InternshipEmp = $preserveEsc($old_data['InternshipEmp'] ?? '0');
            $OtherEmp = $preserveEsc($old_data['OtherEmp'] ?? '0');
            $zone = $preserveEsc($old_data['zone'] ?? '0');
            $CocoFranchiseAccess = $preserveEsc($old_data['CocoFranchiseAccess'] ?? '0');
            if (!empty($old_data['cpzones'])) {
                $cpzones = $preserveEsc($old_data['cpzones']);
            }
        }

        $PhoneEsc = addslashes(trim($Phone));
        $sqlPhoneDup = "SELECT id FROM tbl_users WHERE Phone='$PhoneEsc' AND Status='1' AND Roll NOT IN(1,5,55,9,22,23,63) AND id!='$id'";
        if (getRow($sqlPhoneDup) > 0) {
            $conn->rollback();
            echo "<script>alert('Phone No Already Exists With Active Employee!');window.location.href='../add-employee.php?id=$id';</script>";
            exit;
        }
        
        // Generate QR Code
        $filename = $id.".png";
        $codeContents = $Phone;
        $Barcode = $filename;
        if (class_exists('QRcode')) {
            @QRcode::png($codeContents, $tempDir . $filename, QR_ECLEVEL_L, 5);
        }

        // Build UPDATE query
        $sql = "UPDATE tbl_users SET att_task_show='$att_task_show',ticketshow='$ticketshow',Gender='$Gender',cofofr='$cofofr',cpzones='$cpzones',AssignFranchiseNsoVedExp='$AssignFranchiseNsoVedExp',TotalHours='$TotalHours',OpenTime='$OpenTime',CloseTime='$CloseTime',OpenTime24='$OpenTime24',CloseTime24='$CloseTime24',WorkingHrs='$WorkingHrs',InternshipEmp='$InternshipEmp',AssignFranchiseBdm='$AssignFranchiseBdm',BdmCheckpoint='$BdmCheckpoint',EsicNo='$EsicNo',EmpScheme='$EmpScheme',ReferId='$ReferId',IncrementPer='$IncrementPer',Increment='$Increment',AnniversaryDate='$AnniversaryDate',UnderByBdm='$UnderByBdm',ApproveBy='$ApproveBy',ReJoinDate='$ReJoinDate',YearlyWeekOff='$YearlyWeekOff',MonthlyWeekOff='$MonthlyWeekOff',NsoVedPay='$NsoVedPay',AssignFranchiseVedExp='$AssignFranchiseVedExp',AssignFranchiseAttendance='$AssignFranchiseAttendance',Education='$Education',UanNo='$UanNo',ReferCode='$ReferCode',NoticePeriod='$NoticePeriod',OtherEmp='$OtherEmp',MgrCheckpoint='$MgrCheckpoint',DeclarationPhoto='$DeclarationPhoto',ZoneId='$ZoneId',DeclarationPdf='$DeclarationPdf',NomineeName='$NomineeName',NomineeRelation='$NomineeRelation',NomineePhone='$NomineePhone',NomineeAadharNo='$NomineeAadharNo',zone='$zone',CocoFranchiseAccess='$CocoFranchiseAccess',CreditSalaryStatus='$CreditSalaryStatus',Barcode='$Barcode',Fname='$Fname',Mname='$Mname',Lname='$Lname',Phone='$Phone',EmailId='$EmailId',Password='$Password',Phone2='$Phone2',CountryId='$CountryId',StateId='$StateId',CityId='$CityId',Address='$Address',Pincode='$Pincode',Status='$Status',Photo='$Photo',Roll='$Roll',ModifiedDate='$CreatedDate',ModifiedBy='$user_id',GstNo='$GstNo',Photo2='$Photo2',Photo3='$Photo3',Details='$Details',CatId='$CatId',PanNo='$PanNo',CompId='$CompId',BranchId='$BranchId',FatherPhone='$FatherPhone',Designation='$Designation',Dob='$Dob',AadharNo='$AadharNo',BloodGroup='$BloodGroup',JoinDate='$JoinDate',EmailId2='$EmailId2',AccountName='$AccountName',BankName='$BankName',AccountNo='$AccountNo',IfscCode='$IfscCode',Branch='$Branch',UpiNo='$UpiNo',UnderUser='$UnderUser',ReportingMgr='$ReportingMgr',ResignStatus='$ResignStatus',ResignDate='$ResignDate',ResignComment='$ResignComment',ExpCatId='$ExpCatId',MainBrEmp='$MainBrEmp',UnderByUser='$UnderByUser',$updateString";
      
        if($canSaveMenuAccess){
            $sql.=",PettyCash='$PettyCash',PettyAmount='$PettyAmount',UnderFrId='$UnderFrId'";
        }
        if($user_id == 2651 || $user_id == 2650){
            $sql.=",ProfitLossReport='$ProfitLossReport',ReceipeSosReply='$ReceipeSosReply',vedSubzones='$vedSubzones',nsovedSubzones='$nsovedSubzones',DisabledAttPhoto='$DisabledAttPhoto',vedfranchiseCheck='$vedfranchiseCheck',nsovedfranchiseCheck='$nsovedfranchiseCheck',vedzones='$vedzones',nsovedzones='$nsovedzones',CashHandover='$CashHandover',EmpAppDashboard='$EmpAppDashboard',EmpStatus='$EmpStatus',VendorExpSecOpt='$VendorExpSecOpt',MarkAttendance='$MarkAttendance'";
        }
        if($user_id == 2650 || $user_id == 22170 || $user_id == 2799 || $user_id == 2651 || $user_id == 19957){
        $sql.=",MonthlySalary='$MonthlySalary',PerDaySalary='$PerDaySalary',SalaryType='$SalaryType',UnderFrId='$UnderFrId'";
        }

        $sql.=" WHERE id='$id'";
        
        if (!$conn->query($sql)) {
            throw new Exception("Error updating employee: " . $conn->error);
        }

        // Menu access on edit is saved only via SaveMenuAccess (before main form submit).
        // Do not touch Options2 here — the large form POST often drops Options2_csv and was resetting permissions to 0.

        $canEditSalary = ($user_id == 2650 || $user_id == 22170 || $user_id == 2799);
        if ($canEditSalary && $old_data) {
            $salaryChanged = (
                (string) ($old_data['SalaryType'] ?? '') !== (string) $SalaryType
                || (string) ($old_data['PerDaySalary'] ?? '') !== (string) $PerDaySalary
                || (string) ($old_data['MonthlySalary'] ?? '') !== (string) $MonthlySalary
            );
            if ($salaryChanged) {
                $salaryEffectiveFrom = maha_resolve_salary_effective_from($SalaryEffectiveFrom, $JoinDate);
                $closeToDate = date('Y-m-d', strtotime($salaryEffectiveFrom . ' -1 day'));
                maha_close_active_employee_salary((int) $id, $closeToDate);
                if (!maha_insert_employee_salary_history((int) $id, $SalaryType, $PerDaySalary, $MonthlySalary, $salaryEffectiveFrom, (int) $user_id)) {
                    throw new Exception('Error inserting employee salary history');
                }
            }
        }

                
        // Update tbl_users2 (InactiveAt stored here when Status toggles active/inactive)
        $inactiveAtUpdateFrag = '';
        if ($old_data) {
            $oldStatus = (int) ($old_data['Status'] ?? 1);
            $newStatus = (int) $Status;
            if ($oldStatus === 1 && $newStatus === 0) {
                $inactiveAtUpdateFrag = ",InactiveAt='" . mysqli_real_escape_string($conn, date('Y-m-d H:i:s')) . "'";
            } elseif ($oldStatus === 0 && $newStatus === 1) {
                $inactiveAtUpdateFrag = ',InactiveAt=NULL';
            }
        }

         $sql = "INSERT INTO tbl_users2 (UserId, RestrictAttAfter1015, WeekOffDay,cpzones,cpsubzones,cpfranchise,Grade,Cl,El,att_zones,att_subzones,bdm_zones,bdm_subzones,PartialReporting,LastWorkDate,CompanyNumber,PersonalName1,PersonalRelation1,PersonalPhone1,PersonalName2,PersonalRelation2,PersonalPhone2,ProfName1,ProfDesignation1,ProfPhone1,ProfName2,ProfDesignation2,ProfPhone2)
                VALUES ('$id', '$RestrictAttAfter1015', '$WeekOffDay','$cpzones','$cpsubzones','$cpfranchise','$Grade','$Cl','$El','$att_zones','$att_subzones','$bdm_zones','$bdm_subzones','$PartialReporting','$LastWorkDate','$CompanyNumber','$PersonalName1','$PersonalRelation1','$PersonalPhone1','$PersonalName2','$PersonalRelation2','$PersonalPhone2','$ProfName1','$ProfDesignation1','$ProfPhone1','$ProfName2','$ProfDesignation2','$ProfPhone2')
                ON DUPLICATE KEY UPDATE 
                RestrictAttAfter1015='$RestrictAttAfter1015',WeekOffDay='$WeekOffDay',cpsubzones='$cpsubzones',cpzones='$cpzones',cpfranchise='$cpfranchise',Grade='$Grade',Cl='$Cl',El='$El',att_zones='$att_zones',att_subzones='$att_subzones',bdm_zones='$bdm_zones',bdm_subzones='$bdm_subzones',PartialReporting='$PartialReporting',LastWorkDate='$LastWorkDate',CompanyNumber='$CompanyNumber',PersonalName1='$PersonalName1',PersonalRelation1='$PersonalRelation1',PersonalPhone1='$PersonalPhone1',PersonalName2='$PersonalName2',PersonalRelation2='$PersonalRelation2',PersonalPhone2='$PersonalPhone2',ProfName1='$ProfName1',ProfDesignation1='$ProfDesignation1',ProfPhone1='$ProfPhone1',ProfName2='$ProfName2',ProfDesignation2='$ProfDesignation2',ProfPhone2='$ProfPhone2'" . $inactiveAtUpdateFrag;
        if (!$conn->query($sql)) {
            throw new Exception("Error updating week off day: " . $conn->error);
        }
        
        // Update customer address
        $AreaId = isset($_POST['AreaId']) ? addslashes(trim($_POST['AreaId'])) : '';
        $sql3 = "UPDATE customer_address SET Fname='$Fname',Lname='$Lname',Phone='$Phone',EmailId='$EmailId',CountryId='$CountryId',StateId='$StateId',CityId='$CityId',AreaId='$AreaId',Address='$Address',Pincode='$Pincode',Status='1',CreatedDate='$CreatedDate' WHERE UserId='$id'";
        if (!$conn->query($sql3)) {
            throw new Exception("Error updating customer address: " . $conn->error);
        }

        // Handle file uploads for update
        if (isset($_FILES['Files']) && !empty($_FILES['Files']['name'][0])) {
            $EmpId = $id;  
            $desired_dir = "../../employee_files/" . $EmpId . "/";
            
            // Ensure base folder exists
            if (!is_dir("../../employee_files/")) {
                mkdir("../../employee_files/", 0777, true);
            }
            
            // Ensure employee folder exists
            if (!is_dir($desired_dir)) {
                mkdir($desired_dir, 0777, true);
            }
            
            $uploaded_files = array();
            foreach ($_FILES['Files']['tmp_name'] as $key => $tmp_name) {
                //echo $_FILES['Files']['name'][$key];exit();
                $file_name = $_FILES['Files']['name'][$key];
                $file_tmp  = $_FILES['Files']['tmp_name'][$key];
                $file_size = $_FILES['Files']['size'][$key];
                
                if (!empty($file_name) && $file_size > 0) {
                   $query = "INSERT INTO tbl_user_files SET UserId='$EmpId', Files='$file_name', FileName='$file_name'";
                    
                    $target_path = $desired_dir . $file_name;
                    
                    // Move file
                    if (!file_exists($target_path)) {
                        move_uploaded_file($file_tmp, $target_path);
                    } else {
                        $new_file = $desired_dir . time() . "_" . $file_name;
                        move_uploaded_file($file_tmp, $new_file);
                    }
                    
                    if (!$conn->query($query)) {
                        throw new Exception("Error uploading file: " . $conn->error);
                    }
                    $uploaded_files[] = $file_name;
                }
            }
            
            // Log file upload
            if (!empty($uploaded_files)) {
                $log_message = "Files uploaded: " . implode(", ", $uploaded_files);
                logEmployeeAction($EmpId, 'FILE_UPLOAD', $log_message, null, array('Files' => $uploaded_files));
            }
        }



        // Handle Files2 uploads for update
        if (isset($_FILES['Files2']) && !empty($_FILES['Files2']['name'][0])) {
            $EmpId = $id;  
            $desired_dir = "../../employee_files2/" . $EmpId . "/";
            
            // Ensure base folder exists
            if (!is_dir("../../employee_files2/")) {
                mkdir("../../employee_files2/", 0777, true);
            }
            
            // Ensure employee folder exists
            if (!is_dir($desired_dir)) {
                mkdir($desired_dir, 0777, true);
            }
            
            $uploaded_files2 = array();
            foreach ($_FILES['Files2']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['Files2']['name'][$key];
                $file_tmp  = $_FILES['Files2']['tmp_name'][$key];
                $file_size = $_FILES['Files2']['size'][$key];
                
                if (!empty($file_name) && $file_size > 0) {
                    $query = "INSERT INTO tbl_user_files2 SET UserId='$EmpId', Files='$file_name', FileName='$file_name'";
                    
                    $target_path = $desired_dir . $file_name;
                    
                    // Move file
                    if (!file_exists($target_path)) {
                        move_uploaded_file($file_tmp, $target_path);
                    } else {
                        $new_file = $desired_dir . time() . "_" . $file_name;
                        move_uploaded_file($file_tmp, $new_file);
                    }
                    
                    if (!$conn->query($query)) {
                        throw new Exception("Error uploading file2: " . $conn->error);
                    }
                    $uploaded_files2[] = $file_name;
                }
            }
            
            // Log file upload
            if (!empty($uploaded_files2)) {
                $log_message = "Files2 uploaded: " . implode(", ", $uploaded_files2);
                logEmployeeAction($EmpId, 'FILE_UPLOAD', $log_message, null, array('Files' => $uploaded_files2));
            }
        }


        // Handle bank details update/insert
        $sql3 = "SELECT * FROM tbl_bank_details WHERE AccountNo='$AccountNo' AND IfscCode='$IfscCode' AND type=1 AND userid='$id'";
        $rncnt3 = getRow($sql3);
        $old_bank_data = null;
        if($rncnt3 > 0){
            $old_bank_record = getRecord($sql3);
            $old_bank_data = $old_bank_record ? $old_bank_record : null;
            
            $sql2 = "UPDATE tbl_bank_details SET AccountName='$AccountName',BankName='$BankName',AccountNo='$AccountNo',IfscCode='$IfscCode',Branch='$Branch',Status='1',ModifiedBy='$user_id',ModifiedDate='$CreatedDate' WHERE userid='$id' AND type=1";
            if (!$conn->query($sql2)) {
                throw new Exception("Error updating bank details: " . $conn->error);
            }
            
            // Log bank update
            if (!empty($AccountNo) && $old_bank_data) {
                logEmployeeAction($id, 'BANK_UPDATE', "Bank details updated (A/C: $AccountNo, Bank: $BankName)", $old_bank_data, array('AccountNo' => $AccountNo, 'BankName' => $BankName, 'IfscCode' => $IfscCode));
            }
        }
        else{
            $sql2 = "INSERT INTO tbl_bank_details SET AccountName='$AccountName',BankName='$BankName',AccountNo='$AccountNo',IfscCode='$IfscCode',Branch='$Branch',Status='1',CreatedBy='$user_id',CreatedDate='$CreatedDate',userid='$id',type=1";
            if (!$conn->query($sql2)) {
                throw new Exception("Error inserting bank details: " . $conn->error);
            }
            
            // Log bank creation
            if (!empty($AccountNo)) {
                logEmployeeAction($id, 'BANK_UPDATE', "Bank details created (A/C: $AccountNo, Bank: $BankName)", null, array('AccountNo' => $AccountNo, 'BankName' => $BankName, 'IfscCode' => $IfscCode));
            }
        }
        
        // Get new data after update for logging
        $sql_new = "SELECT * FROM tbl_users WHERE id='$id'";
        $new_record = getRecord($sql_new);
        $new_data = $new_record ? $new_record : null;
        
        // Log employee update with old and new data
        if ($old_data && $new_data) {
            $full_name = trim("$Fname $Mname $Lname");
            $status_change = "";
            if (isset($old_data['Status']) && isset($new_data['Status']) && $old_data['Status'] != $new_data['Status']) {
                $status_change = " (Status: " . ($old_data['Status'] == 1 ? 'Active' : 'Inactive') . " → " . ($new_data['Status'] == 1 ? 'Active' : 'Inactive') . ")";
                logEmployeeAction($id, 'STATUS', "Employee status changed: $full_name (Phone: $Phone)" . $status_change, array('Status' => $old_data['Status']), array('Status' => $new_data['Status']));
            }
            
            $log_message = "Employee updated: $full_name (Phone: $Phone, Roll: $Roll)";
            logEmployeeAction($id, 'UPDATE', $log_message, $old_data, $new_data);
        }
        
        // Commit transaction
        $conn->commit();
        
        // Redirect based on pageval
        if($pageval == 'lead'){
            echo "<script>alert('Record Updated Successfully!');window.location.href='../view-sales-lead-manager.php';</script>";
        }
        else if($pageval == 'flexi'){
            echo "<script>alert('Record Updated Successfully!');window.location.href='../view-flexi-manager.php';</script>";
        }
        else if($pageval == 'service'){
            echo "<script>alert('Record Updated Successfully!');window.location.href='../view-service-manager.php';</script>";
        }
        else{
            echo "<script>alert('Record Updated Successfully!');window.location.href='../view-employee.php';</script>";
        }
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Error updating employee: " . $e->getMessage());
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');history.back();</script>";
    }
}
} catch (Throwable $e) {
    if (isset($conn) && $conn && method_exists($conn, 'rollback')) {
        @$conn->rollback();
    }
    error_log("ajax_employee Save error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    echo "<script>alert('Unable to save. Please try again or contact support.');history.back();</script>";
}
}

if(isset($_POST['action']) && $_POST['action'] == 'deletePhoto'){
    try {
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $Photo = isset($_POST['Photo']) ? $_POST['Photo'] : '';
        
        // Get old data for logging
        $sql_old = "SELECT * FROM tbl_users WHERE id='$id'";
        $old_record = getRecord($sql_old);
        
        $q = "UPDATE tbl_users SET Photo='' WHERE id=$id";
        if (!$conn->query($q)) {
            throw new Exception("Error deleting photo: " . $conn->error);
        }
        
        // Log the change
        $new_data = $old_record;
        $new_data['Photo'] = '';
        if ($old_record) {
            $full_name = trim($old_record['Fname'] . " " . $old_record['Mname'] . " " . $old_record['Lname']);
            logEmployeeAction($id, 'FILE_UPLOAD', "Photo deleted for employee: $full_name (ID: $id)", $old_record, $new_data);
        }
        
        echo "File Deleted Successfully";
    } catch (Exception $e) {
        error_log("Error deleting photo: " . $e->getMessage());
        echo "Error: " . $e->getMessage();
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'getUserDetails'){
$id = isset($_POST['id']) ? $_POST['id'] : '';
$sql = "SELECT tu.*,tu2.Fname AS AgentName FROM tbl_users tu LEFT JOIN tbl_users tu2 ON tu.UnderUser=tu2.id WHERE tu.id='$id'";
$row = getRecord($sql);
echo json_encode($row ?: []);
}

if(isset($_POST['action']) && $_POST['action'] == 'getUserDetails2'){
$CellNo = isset($_POST['CellNo']) ? $_POST['CellNo'] : '';
$sql = "SELECT tu.*,tu2.Fname AS AgentName FROM tbl_users tu LEFT JOIN tbl_users tu2 ON tu.UnderUser=tu2.id WHERE tu.Phone='$CellNo'";
$row = getRecord($sql);
echo json_encode($row ?: []);
}

if(isset($_POST['action']) && $_POST['action'] == 'getReferDetails'){
$ReferCode = isset($_POST['ReferCode']) ? $_POST['ReferCode'] : '';
$sql = "SELECT id,Fname,Phone,Phone2,EmailId FROM tbl_users WHERE CustomerId='$ReferCode'";
$row = getRecord($sql);
if ($row) {
    echo json_encode(array('id'=>$row['id'],'Fname'=>$row['Fname'],'Phone'=>$row['Phone'],'Phone2'=>$row['Phone2'],'EmailId'=>$row['EmailId']));
} else {
    echo json_encode(array('id'=>'','Fname'=>'','Phone'=>'','Phone2'=>'','EmailId'=>''));
}
}

if(isset($_POST['action']) && $_POST['action'] == 'getEmpDetails'){
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $month = isset($_POST['month']) ? (int) $_POST['month'] : (int) date('m');
    $year = isset($_POST['year']) ? (int) $_POST['year'] : (int) date('Y');
    $periodStart = date('Y-m-01', strtotime("$year-$month-01"));
    $periodEnd = date('Y-m-t', strtotime("$year-$month-01"));

    $sql = "SELECT tu.id,tu.Fname,tu.CustomerId,tu.MonthlySalary,tu.Phone,tu.JoinDate,tut.Name AS Designation,td.Name AS Department,tu.UanNo,tu.EsicNo FROM tbl_users tu 
            LEFT JOIN tbl_user_type tut ON tut.id=tu.Roll 
            LEFT JOIN tbl_departments td ON td.id=tu.Designation WHERE tu.id='$id'";
    $row = getRecord($sql);
    if (is_array($row)) {
        $salaryRow = maha_get_employee_salary_for_period((int) $id, $periodStart, $periodEnd);
        if (is_array($salaryRow)) {
            $row['MonthlySalary'] = maha_employee_monthly_salary_amount($salaryRow, (int) date('t', strtotime($periodStart)));
            $row['PerDaySalary'] = $salaryRow['PerDaySalary'] ?? $row['PerDaySalary'] ?? '';
            $row['SalaryType'] = $salaryRow['SalaryType'] ?? $row['SalaryType'] ?? '';
        }
    }
    echo json_encode($row ?: []); 
}

if(isset($_POST['action']) && $_POST['action'] == 'calAttendance') {
    $UserId = intval($_POST['UserId']);
    $month  = intval($_POST['month']);
    $year   = intval($_POST['year']);
    
    // Calculate first and last day of the month
    $startDate = date("Y-m-01", strtotime("$year-$month-01"));
    $endDate   = date("Y-m-t", strtotime("$year-$month-01"));

    // Total days in selected month
    $totalDays = date("t", strtotime("$year-$month-01"));
    
    $sql2 = "SELECT MainBrEmp FROM tbl_users WHERE id='$UserId'";
    $row2 = getRecord($sql2);
    $MainBrEmp = isset($row2['MainBrEmp']) ? intval($row2['MainBrEmp']) : 0;

    // Get total present days
    $sqlPresent = "SELECT COUNT(*) as present 
                   FROM tbl_attendance 
                   WHERE CreatedDate >= '$startDate' 
                     AND CreatedDate <= '$endDate' 
                     AND UserId = '$UserId' 
                     AND Type = 2";
    $rowPresent = getRecord($sqlPresent);
    $totalPresent = isset($rowPresent['present']) ? (int)$rowPresent['present'] : 0;

    // Calculate absent days
    $totalAbsent = $totalDays - $totalPresent;
    
    $sql2 = "SELECT COUNT(*) AS LeaveReq FROM `tbl_leave_request` WHERE FromDate>='$startDate' AND ToDate<='$endDate' AND HrStatus=1 AND UserId='$UserId'";
    $row2 = getRecord($sql2);
    
    $sql3 = "SELECT COUNT(*) AS WeekOff FROM tbl_week_off_punch WHERE punch_date>='$startDate' AND punch_date<='$endDate' AND user_id='$UserId'";
    $row3 = getRecord($sql3);
    
    $sql4 = "SELECT IFNULL(SUM(AdvanceSalary), 0) AS AdvanceSalary FROM `tbl_advance_salary` WHERE HrStatus=1 AND AdvanceDate>='$startDate' AND AdvanceDate<='$endDate' AND UserId='$UserId'";
    $row4 = getRecord($sql4);
    // Prepare response
    $response = [
        "total_days"    => $totalDays,
        "total_present" => $totalPresent,
        "total_absent"  => $totalAbsent,
        "total_leavs"   => isset($row2['LeaveReq']) ? $row2['LeaveReq'] : 0,
        "total_weekoff"   => isset($row3['WeekOff']) ? $row3['WeekOff'] : 0,
        "total_advance"   => isset($row4['AdvanceSalary']) ? $row4['AdvanceSalary'] : 0
    ];

    echo json_encode($response);
}

if(isset($_POST['action']) && $_POST['action'] == 'checkExistingSlip'){
    $UserId = isset($_POST['UserId']) ? $_POST['UserId'] : '';
    $month = isset($_POST['month']) ? $_POST['month'] : '';
    $year = isset($_POST['year']) ? $_POST['year'] : '';

    $sql = "SELECT id FROM tbl_salary_slip WHERE UserId='$UserId' AND Month='$month' AND Year='$year'";
    $rncnt = getRow($sql);
    
    if($rncnt > 0){
        $row = getRecord($sql);
        echo $row['id'];
    } else {
        echo 0;
    }
    exit;
}
?>