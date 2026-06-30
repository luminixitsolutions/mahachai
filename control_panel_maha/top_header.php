<?php 
	$user_id = $_SESSION['Admin']['id'];
	$sql77 = "SELECT * FROM tbl_users WHERE id='$user_id'";
	$row77 = getRecord($sql77);
	$LoginUserName = trim((isset($row77['Fname']) ? $row77['Fname'] : '') . ' ' . (isset($row77['Lname']) ? $row77['Lname'] : ''));
	if ($LoginUserName === '' && !empty($row77)) {
		$LoginUserName = trim((isset($row77['FName']) ? $row77['FName'] : '') . ' ' . (isset($row77['LName']) ? $row77['LName'] : ''));
	}
	if ($LoginUserName === '' && !empty($_SESSION['Admin']['Fname'])) {
		$LoginUserName = trim($_SESSION['Admin']['Fname'] . ' ' . (isset($_SESSION['Admin']['Lname']) ? $_SESSION['Admin']['Lname'] : ''));
	}
	$Roll = $row77['Roll'];
	$UserCat = $row77['CatId'];
	require_once __DIR__ . '/menu-hub-cards.php';
	$Options = maha_normalize_user_options($row77);
	$ExpCatId = $row77['ExpCatId'];
	$BranchId = $row77['BranchId'];
	$CocoFranchiseAccess = $row77['CocoFranchiseAccess'];
	$AssignFranchiseVedExp = $row77['AssignFranchiseVedExp'];
	$AssignFranchiseNsoVedExp = $row77['AssignFranchiseNsoVedExp'];
	$EmpStatus = $row77['EmpStatus'];
	$AssignFranchiseBdm = $row77['AssignFranchiseBdm'];
	
	$sql771 = "SELECT * FROM tbl_users2 WHERE UserId='$user_id'";
	$row771 = getRecord($sql771);
	if (!is_array($row771)) {
		$row771 = array();
	}
	$cpzones = isset($row771['cpzones']) ? $row771['cpzones'] : '';
	$cpsubzones = isset($row771['cpsubzones']) ? $row771['cpsubzones'] : '';
	$cpfranchise = isset($row771['cpfranchise']) ? $row771['cpfranchise'] : '';

	require_once __DIR__ . '/user_ui_prefs_functions.php';
	$uiPrefs = maha_get_user_ui_prefs((int) $user_id);
	maha_render_ui_prefs_assets($uiPrefs);
 ?>
<style>
    .loader {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: rgba(0, 0, 0, 0.4);
 /* background-image: url("04de2e31234507.564a1d23645bf.gif");
  background-repeat: no-repeat;
  background-position: center center; */
  transition: opacity 0.75s, visibility 0.75s;
  z-index:9999;
}

.loader--hidden {
  opacity: 0;
  visibility: hidden;
}

.loader::after {
  content: "";
  width: 75px;
  height: 75px;
  border: 5px solid #dddddd;
  border-top-color: #f26921;
  border-radius: 50%;
  animation: loading 0.75s ease infinite;
}

@keyframes loading {
  from {
    transform: rotate(0turn);
  }
  to {
    transform: rotate(1turn);
  }
}

.layout-navbar .navbar-collapse {
    overflow: visible;
}
#layout-navbar.layout-navbar {
    position: relative;
    z-index: 1100;
}
.layout-navbar .demo-navbar-user {
    position: relative;
    z-index: 1101;
    flex-shrink: 0;
}
.layout-navbar .demo-navbar-user .nav-link {
    color: var(--maha-nav-text, #fff) !important;
    display: flex !important;
    align-items: center;
    flex-shrink: 0;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
}
.layout-navbar .navbar-user-display-name {
    color: var(--maha-nav-text, #fff) !important;
    font-weight: 600;
    font-size: 0.9rem;
    line-height: 1.2;
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    flex-shrink: 0;
    margin-right: 0.5rem;
}
.layout-navbar .demo-navbar-user .dropdown-menu {
    position: absolute;
    z-index: 1102;
    margin-top: 0.25rem;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.18);
}
.layout-navbar .demo-navbar-user.show,
.layout-navbar .demo-navbar-user.open {
    z-index: 1102;
}
</style>

<script>
    window.addEventListener("load", () => {
  const loader = document.querySelector(".loader");
  if (!loader) {
    return;
  }
  loader.classList.add("loader--hidden");

  loader.addEventListener("transitionend", () => {
    if (loader.parentNode) {
      loader.parentNode.removeChild(loader);
    }
  });
});

</script>

<!--<div class="loader"></div>-->
<?php
$mahaVerticalMenu = !empty($uiPrefs['menu_orientation']) && $uiPrefs['menu_orientation'] === 'vertical';
$mahaNavToggleClass = $mahaVerticalMenu ? '' : 'd-lg-none';
?>
<nav class="layout-navbar navbar navbar-expand-lg align-items-lg-center <?php echo (isset($uiPrefs['navbar_style']) && $uiPrefs['navbar_style'] === 'light') ? 'bg-white navbar-light' : 'bg-dark navbar-dark'; ?> container-p-x" id="layout-navbar">
<div class="layout-sidenav-toggle navbar-nav <?php echo $mahaNavToggleClass; ?> align-items-lg-center mr-auto">
<a class="nav-item nav-link px-0 mr-lg-4" href="javascript:">
<i class="ion ion-md-menu text-large align-middle"></i>
</a>
</div>
<a href="dashboard.php" class="navbar-brand app-brand demo d-lg-none py-0 mr-4">
<!--<span class="app-brand-logo demo">-->
<img src="logo5.png" alt="" class="img-fluid" style="width: 40px;">
<!--</span>-->
<span class="app-brand-text demo font-weight-normal ml-2" style="font-size: 22px;"><?php echo $Proj_Title; ?></span>
</a>


<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#layout-navbar-collapse">
<span class="navbar-toggler-icon"></span>
</button>
<div class="navbar-collapse collapse" id="layout-navbar-collapse">

<hr class="d-lg-none w-100 my-2">
<div class="navbar-nav align-items-lg-center ml-auto">


<div class="nav-item d-none d-lg-block text-big font-weight-light line-height-1 opacity-25 mr-3 ml-1">|</div>
<div class="demo-navbar-user nav-item dropdown">
<a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" title="<?php echo htmlspecialchars($LoginUserName, ENT_QUOTES, 'UTF-8'); ?>">
<?php if ($LoginUserName !== '') { ?>
<span class="navbar-user-display-name d-none d-md-inline"><?php echo htmlspecialchars($LoginUserName, ENT_QUOTES, 'UTF-8'); ?></span>
<?php } ?>
    <?php if($row77['Photo']=='') {?>
<img src="user_icon.jpg" alt class="d-block ui-w-30 rounded-circle flex-shrink-0">
<?php } else{?>
    <img src="../uploads/<?php echo $row77['Photo']; ?>" alt class="d-block ui-w-30 rounded-circle flex-shrink-0" style="width: 30px;height: 30px;">
<?php } ?>
</a>
<div class="dropdown-menu dropdown-menu-right">
<a href="my-profile.php" class="dropdown-item">
<i class="feather icon-user text-muted"></i> &nbsp; My Profile</a>
<div class="dropdown-divider"></div>
<a href="change-password.php" class="dropdown-item">
<i class="feather icon-unlock text-muted"></i> &nbsp; Change Password</a>
<div class="dropdown-divider"></div>
<a href="logout.php" class="dropdown-item">
<i class="feather icon-power text-danger"></i> &nbsp; Log Out</a>
</div>
</div>
</div>
</div>
</nav>