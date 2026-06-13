<?php include 'loader.php';

if (empty($LoginUserName) && !empty($user_id) && isset($conn)) {
    $uidEsc = (int) $user_id;
    $rowBrandUser = getRecord("SELECT Fname, Lname FROM tbl_users WHERE id='$uidEsc' LIMIT 1");
    if (!empty($rowBrandUser['Fname'])) {
        $LoginUserName = trim($rowBrandUser['Fname'] . ' ' . ($rowBrandUser['Lname'] ?? ''));
    }
}

$appName = 'control_panel_maha';
if (!empty($user_id) && isset($conn)) {
    $uid = (int) $user_id;
    $conn->query("DELETE FROM tbl_active_admin WHERE app_name='$appName' AND session_id='$uid'");
    $conn->query("INSERT INTO tbl_active_admin (app_name, session_id, last_login)
                  VALUES ('$appName', '$uid', NOW())");
}
?>

<!-- YOUR PAGE HTML / PHP STARTS BELOW -->
<?php
if (!isset($uiPrefs)) {
    require_once __DIR__ . '/user_ui_prefs_functions.php';
    require_once __DIR__ . '/menu-hub-cards.php';
    $uiPrefs = maha_get_user_ui_prefs((int) ($user_id ?? 0));
}
$mahaMenuVertical = !empty($uiPrefs['menu_orientation']) && $uiPrefs['menu_orientation'] === 'vertical';
$mahaThemeDark = !empty($uiPrefs['theme_mode']) && $uiPrefs['theme_mode'] === 'dark';
$mahaNavLight = !empty($uiPrefs['navbar_style']) && $uiPrefs['navbar_style'] === 'light';
// Horizontal menu always uses bg-dark — user colors come from prefs CSS (bg-white breaks open/hover states).
if ($mahaMenuVertical) {
    $sidenavBgClass = ($mahaThemeDark || !$mahaNavLight) ? 'bg-dark' : 'bg-white';
} else {
    $sidenavBgClass = 'bg-dark';
}
$sidenavClass = $mahaMenuVertical
    ? 'layout-sidenav sidenav sidenav-vertical maha-vertical-sidenav'
    : 'layout-sidenav-horizontal sidenav-horizontal flex-grow-0';
?>

<?php if ($mahaMenuVertical) { ?>
<div id="layout-sidenav" class="<?php echo $sidenavClass . ' ' . $sidenavBgClass; ?>">
     <div class="app-brand demo" title="<?php echo isset($LoginUserName) ? htmlspecialchars('Logged in as: ' . $LoginUserName, ENT_QUOTES, 'UTF-8') : 'Maha Chai'; ?>">
                    <span class="app-brand-logo demo">
                        <a href="menu-dashboard.php" title="<?php echo isset($LoginUserName) ? htmlspecialchars('Logged in as: ' . $LoginUserName, ENT_QUOTES, 'UTF-8') : 'Home'; ?>"><img src="logo5.png" alt="Brand Logo" class="img-fluid" style="width: 48px;"></a>
                    </span>
                    <span class="app-brand-text demo font-weight-normal ml-2">Maha Chai</span>
                    <a href="javascript:" class="layout-sidenav-toggle sidenav-link text-large ml-auto d-none d-lg-inline-block">
                        <i class="ion ion-md-menu align-middle"></i>
                    </a>
                </div>
                <div class="sidenav-divider mt-0"></div>
    <ul class="sidenav-inner py-1">
<?php } else { ?>
<div class="sidenav <?php echo $sidenavBgClass; ?>">
<div id="layout-sidenav" class="<?php echo $sidenavClass . ' ' . $sidenavBgClass; ?>" style="padding-left:15px;padding-right:15px;">
     <div class="app-brand demo" title="<?php echo isset($LoginUserName) ? htmlspecialchars('Logged in as: ' . $LoginUserName, ENT_QUOTES, 'UTF-8') : 'Maha Chai'; ?>">
                    <span class="app-brand-logo demo">
                        <a href="menu-dashboard.php" title="<?php echo isset($LoginUserName) ? htmlspecialchars('Logged in as: ' . $LoginUserName, ENT_QUOTES, 'UTF-8') : 'Home'; ?>"><img src="logo5.png" alt="Brand Logo" class="img-fluid" style="width: 60px;"></a> 
                    </span>
                    <h3 class="app-brand-title" style="font-size:18px; padding-left:10px;padding-top:10px;">Maha Chai</h3>
                    <?php if (!empty($LoginUserName)) { ?>
                    <span class="app-brand-login-user d-none d-lg-inline-block" title="<?php echo htmlspecialchars($LoginUserName, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($LoginUserName, ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php } ?>
                   <!-- <a href="dashboard.php" class="app-brand-text demo sidenav-text font-weight-normal ml-2"><?php echo $Proj_Title; ?></a>-->
                    <a href="javascript:" class="layout-sidenav-toggle sidenav-link text-large ml-auto">
                        <i class="ion ion-md-menu align-middle"></i>
                    </a>
                </div>
                <div class="sidenav-divider mt-0"></div>
    <ul class="sidenav-inner">
<?php } ?>
<?php
require_once 'admin-sidebar-menu-helpers.php';
include 'admin-sidebar-menu-organized.php';
?>

    </ul>
<?php if ($mahaMenuVertical) { ?>
</div>
<?php } else { ?>
</div>
</div>
<?php } ?>
<style>
<?php if ($mahaMenuVertical) { ?>
/* ── Vertical sidebar layout ── */
#layout-sidenav.maha-vertical-sidenav {
    display: block !important;
    width: 15.625rem;
    max-width: 15.625rem;
    flex: 0 0 15.625rem;
    height: 100%;
    max-height: 100%;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
    overscroll-behavior: contain;
}
#layout-sidenav.maha-vertical-sidenav .app-brand.demo {
    display: flex !important;
    align-items: center;
    flex-wrap: wrap;
    padding: 0.85rem 1rem;
}
#layout-sidenav.maha-vertical-sidenav .app-brand-text {
    font-size: 1.05rem;
    font-weight: 600;
}
#layout-sidenav.maha-vertical-sidenav .sidenav-inner {
    flex-direction: column !important;
}
#layout-sidenav.maha-vertical-sidenav .sidenav-inner > .sidenav-item,
#layout-sidenav.maha-vertical-sidenav .sidenav-inner > .sidenav-item > .sidenav-link {
    width: 100% !important;
    max-width: 15.625rem !important;
    box-sizing: border-box !important;
}
#layout-sidenav.maha-vertical-sidenav .sidenav-menu .sidenav-item,
#layout-sidenav.maha-vertical-sidenav .sidenav-menu .sidenav-item > .sidenav-link,
#layout-sidenav.maha-vertical-sidenav .sb-flyout-submenu,
#layout-sidenav.maha-vertical-sidenav .sb-flyout-submenu > .sb-submenu-toggle {
    width: 100% !important;
    max-width: 15.625rem !important;
    margin-left: 0 !important;
    box-sizing: border-box !important;
}
#layout-sidenav.maha-vertical-sidenav .sidenav-inner > .sidenav-item > .sidenav-link {
    padding-left: 1.5rem !important;
    padding-right: 1.25rem !important;
    white-space: normal !important;
}
#layout-sidenav.maha-vertical-sidenav .sidenav-item > .sidenav-menu > .sidenav-item > .sidenav-link,
#layout-sidenav.maha-vertical-sidenav .sidenav-item > .sidenav-menu > .sb-flyout-submenu > .sb-submenu-toggle {
    padding-left: 2.25rem !important;
}
#layout-sidenav.maha-vertical-sidenav .sidenav-item > .sidenav-menu .sidenav-menu .sidenav-link {
    padding-left: 3rem !important;
}
#layout-sidenav.maha-vertical-sidenav .sb-flyout-submenu > .sb-submenu-toggle {
    display: flex !important;
    align-items: center !important;
}
#layout-sidenav.maha-vertical-sidenav .sb-submenu-arrow {
    margin-left: auto !important;
    flex-shrink: 0 !important;
}
#layout-sidenav.maha-vertical-sidenav .sidenav-inner > .sidenav-item.open > .sidenav-menu::before {
    display: none !important;
    content: none !important;
}
#layout-sidenav.maha-vertical-sidenav .sidenav-link > div {
    opacity: 1 !important;
    visibility: visible !important;
}
#layout-sidenav.maha-vertical-sidenav .sidenav-item > .sidenav-menu {
    position: static !important;
    box-shadow: none !important;
    background: transparent !important;
}
#layout-sidenav.maha-vertical-sidenav .sb-flyout-submenu > .sidenav-menu.sb-flyout-panel {
    position: static !important;
    display: none !important;
    opacity: 1 !important;
    visibility: visible !important;
    transform: none !important;
    box-shadow: none !important;
    background: rgba(0, 0, 0, 0.12) !important;
    padding-left: 0 !important;
    margin-left: 0 !important;
    width: 100% !important;
    max-width: 15.625rem !important;
}
#layout-sidenav.maha-vertical-sidenav .sb-flyout-submenu.open > .sidenav-menu.sb-flyout-panel {
    display: block !important;
}
#layout-sidenav.maha-vertical-sidenav .sb-submenu-arrow {
    transform: rotate(90deg);
    margin-left: auto !important;
}
#layout-sidenav.maha-vertical-sidenav .sb-flyout-submenu.open > .sb-submenu-toggle .sb-submenu-arrow {
    transform: rotate(-90deg);
}
<?php } else { ?>
/* ── Horizontal nav layout: brand | prev | scroll area | next ── */
#layout-sidenav.sidenav-horizontal {
    display: flex !important;
    flex-wrap: nowrap !important;
    align-items: stretch !important;
    overflow: visible !important;
}
#layout-sidenav.sidenav-horizontal > .app-brand {
    flex: 0 0 auto;
    max-width: 320px;
}
.app-brand-login-user {
    display: inline-block;
    margin-left: 8px;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    background: rgba(255, 255, 255, 0.12);
    border-radius: 999px;
    max-width: 160px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: middle;
}
.app-brand.demo:hover .app-brand-login-user,
.app-brand-logo a:hover + .app-brand-title + .app-brand-login-user,
.app-brand.demo:focus-within .app-brand-login-user {
    background: rgba(255, 255, 255, 0.22);
}
.sidenav.bg-dark {
    overflow: visible !important;
}

/* Menu scroll zone — clip horizontal overflow so text does not bleed past arrows */
.sidenav-horizontal .sidenav-horizontal-wrapper {
    overflow: hidden !important;
    flex: 1 1 0 !important;
    width: auto !important;
    min-width: 0 !important;
    position: relative;
    isolation: isolate;
}
.sidenav-horizontal .sidenav-inner {
    overflow: hidden !important;
}
.layout-navbar .navbar-collapse,
.layout-navbar,
#layout-sidenav.sidenav-horizontal {
    overflow: visible !important;
}
/* Sticky scroll arrows — fixed width, sit above scroll track to mask edge bleed */
.sidenav-horizontal .sidenav-horizontal-prev,
.sidenav-horizontal .sidenav-horizontal-next {
    flex: 0 0 36px !important;
    width: 36px !important;
    min-width: 36px !important;
    max-width: 36px !important;
    position: relative;
    z-index: 40;
    display: flex !important;
    align-items: center;
    justify-content: center;
    align-self: stretch;
    background: #0F5A4A !important;
    cursor: pointer;
    text-decoration: none;
    flex-shrink: 0;
}
.sidenav-horizontal .sidenav-horizontal-prev {
    box-shadow: 4px 0 10px rgba(0, 0, 0, 0.2);
}
.sidenav-horizontal .sidenav-horizontal-next {
    box-shadow: -4px 0 10px rgba(0, 0, 0, 0.2);
}
.sidenav-horizontal .sidenav-horizontal-prev.disabled,
.sidenav-horizontal .sidenav-horizontal-next.disabled {
    opacity: 0.35;
    cursor: default !important;
    pointer-events: none;
}

/* ── Top-level dropdown (opens below nav bar) ── */
.sidenav-horizontal .sidenav-inner > .sidenav-item {
    position: relative;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu {
    display: none;
    flex-direction: column;
    position: absolute !important;
    top: 100% !important;
    min-width: 240px;
    max-height: 75vh;
    overflow: visible !important;
    -webkit-overflow-scrolling: touch;
    background: #0F5A4A !important;
    border-radius: 0 0 4px 4px;
    padding: 0.35rem 0;
    z-index: 1050;
    box-sizing: border-box;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu:not(.sb-top-menu-fixed) {
    left: 0 !important;
    right: auto !important;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu.sb-top-menu-fixed {
    position: fixed !important;
    z-index: 1050 !important;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu.sb-menu-align-right:not(.sb-top-menu-fixed) {
    left: auto !important;
    right: 0 !important;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu.sb-dropdown-scrolling {
    overflow-y: auto !important;
    overflow-x: visible !important;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu.sb-dropdown-scrolling > .sidenav-item,
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu.sb-dropdown-scrolling > .sb-flyout-submenu {
    flex-shrink: 0;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu > .sidenav-item {
    background: #0F5A4A !important;
    flex-shrink: 0;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu::-webkit-scrollbar {
    width: 6px;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.35);
    border-radius: 3px;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.15);
}
.sidenav-horizontal .sidenav-inner > .sidenav-item:hover > .sidenav-menu,
.sidenav-horizontal .sidenav-inner > .sidenav-item.open > .sidenav-menu {
    display: flex !important;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item:not(:hover):not(.open) > .sidenav-menu {
    display: none !important;
}

/* ── Flyout parent row (e.g. Employee Master >) ── */
.sidenav-horizontal .sb-flyout-submenu {
    position: relative !important;
}
.sidenav-horizontal .sb-flyout-submenu > .sb-submenu-toggle {
    color: #fff !important;
    background: transparent !important;
    font-weight: 400;
    font-size: 13px;
    text-transform: none;
    letter-spacing: normal;
    padding: 0.5rem 1rem !important;
    display: flex !important;
    align-items: center;
    gap: 0.35rem;
    white-space: nowrap;
}
.sidenav-horizontal .sb-flyout-submenu > .sb-submenu-toggle:hover,
.sidenav-horizontal .sb-flyout-submenu.open > .sb-submenu-toggle,
.sidenav-horizontal .sb-flyout-submenu:hover > .sb-submenu-toggle {
    background: rgba(255, 255, 255, 0.1) !important;
    color: #fff !important;
}
.sidenav-horizontal .sb-flyout-submenu .sb-submenu-arrow {
    font-size: 14px;
    opacity: 0.85;
    margin-left: auto;
}

/* ── Override theme: nested menus must NOT stack inline (position:static) ── */
.sidenav-horizontal .sidenav-menu .sidenav-menu.sb-flyout-panel {
    position: absolute !important;
    left: 100% !important;
    top: 0 !important;
    width: auto !important;
    min-width: 240px;
    max-width: 320px;
    max-height: none !important;
    overflow: visible !important;
    margin: 0 !important;
    padding: 0.35rem 0 !important;
    background: #0F5A4A !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.22) !important;
    border-radius: 4px;
    z-index: 1060 !important;
    list-style: none;
    flex-direction: column !important;
    display: none !important;
}
.sidenav-horizontal .sb-flyout-panel .sb-flyout-panel {
    z-index: 1070 !important;
}
.sidenav-horizontal .sb-flyout-panel .sb-flyout-panel .sb-flyout-panel {
    z-index: 1080 !important;
}
.sidenav-horizontal .sb-flyout-submenu > .sidenav-menu.sb-flyout-panel.sb-flyout-nested-fixed {
    position: fixed !important;
    display: none !important;
    margin: 0 !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.22) !important;
    border-radius: 4px;
}
.sidenav-horizontal .sb-flyout-submenu:hover > .sidenav-menu.sb-flyout-panel.sb-flyout-nested-fixed,
.sidenav-horizontal .sb-flyout-submenu.open > .sidenav-menu.sb-flyout-panel.sb-flyout-nested-fixed {
    display: flex !important;
}

/* Show flyout on hover or click only */
.sidenav-horizontal .sb-flyout-submenu:hover > .sidenav-menu.sb-flyout-panel,
.sidenav-horizontal .sb-flyout-submenu.open > .sidenav-menu.sb-flyout-panel {
    display: flex !important;
}

.sb-flyout-portal .sb-flyout-submenu {
    position: relative !important;
}
.sb-flyout-portal .sb-flyout-submenu:hover > .sidenav-menu.sb-flyout-panel,
.sb-flyout-portal .sb-flyout-submenu.open > .sidenav-menu.sb-flyout-panel,
.sb-flyout-portal .sb-flyout-submenu.sb-flyout-pinned > .sidenav-menu.sb-flyout-panel {
    display: flex !important;
}
.sb-flyout-portal .sb-flyout-submenu:not(:hover):not(.open):not(.sb-flyout-pinned) > .sidenav-menu.sb-flyout-panel {
    display: none !important;
}

.sb-flyout-portal .sb-flyout-submenu > .sidenav-menu.sb-flyout-panel.sb-flyout-nested-fixed {
    position: fixed !important;
    left: auto !important;
    top: auto !important;
    min-width: 240px;
    background: #0F5A4A !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.22) !important;
    border-radius: 4px;
    z-index: 1090 !important;
}

/* Keep top dropdown open while a flyout is pinned */
.sidenav-horizontal .sidenav-inner > .sidenav-item.sb-menu-pinned > .sidenav-menu {
    display: flex !important;
}

/* Top-level flyouts portaled to body — visible only while open */
.sb-flyout-portal.sb-flyout-panel {
    position: fixed !important;
    display: none !important;
    flex-direction: column !important;
    min-width: 240px;
    max-width: 320px;
    max-height: 75vh;
    overflow-y: auto;
    overflow-x: visible;
    margin: 0 !important;
    padding: 0.35rem 0 !important;
    background: #0F5A4A !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.22) !important;
    border-radius: 4px;
    z-index: 1080 !important;
    list-style: none;
    box-sizing: border-box;
}
.sb-flyout-portal.sb-flyout-panel.sb-flyout-open {
    display: flex !important;
}

/* Portaled flyout links — must not depend on .sidenav-horizontal ancestor */
.sb-flyout-portal.sb-flyout-panel > .sidenav-item {
    background: #0F5A4A !important;
    list-style: none;
    margin: 0;
    padding: 0;
}
.sb-flyout-portal.sb-flyout-panel > .sidenav-item:hover {
    background: #0F5A4A !important;
}
.sb-flyout-portal.sb-flyout-panel > .sidenav-item > .sidenav-link > div {
    color: inherit;
}
.sb-flyout-portal.sb-flyout-panel > .sidenav-item > .sidenav-link {
    display: flex !important;
    align-items: center;
    text-decoration: none !important;
    color: #fff !important;
    background: transparent !important;
    padding: 0.5rem 1rem !important;
    font-size: 13px;
    font-weight: 400;
    white-space: nowrap;
    line-height: 1.4;
    border: 0;
    box-shadow: none;
}
.sb-flyout-portal.sb-flyout-panel > .sidenav-item > .sidenav-link:hover,
.sb-flyout-portal.sb-flyout-panel > .sidenav-item > .sidenav-link:focus {
    background: rgba(255, 255, 255, 0.1) !important;
    color: #fff !important;
    text-decoration: none !important;
}
.sb-flyout-portal.sb-flyout-panel > .sidenav-item.active > .sidenav-link {
    background: rgba(255, 255, 255, 0.14) !important;
    color: #fff !important;
}
.sb-flyout-portal.sb-flyout-panel .sb-flyout-submenu > .sb-submenu-toggle {
    display: flex !important;
    align-items: center;
    text-decoration: none !important;
    color: #fff !important;
    background: transparent !important;
    padding: 0.5rem 1rem !important;
    font-size: 13px;
    white-space: nowrap;
}
.sb-flyout-portal.sb-flyout-panel .sb-flyout-submenu > .sb-submenu-toggle:hover,
.sb-flyout-portal.sb-flyout-panel .sb-flyout-submenu.open > .sb-submenu-toggle,
.sb-flyout-portal.sb-flyout-panel .sb-flyout-submenu:hover > .sb-submenu-toggle {
    background: rgba(255, 255, 255, 0.1) !important;
    color: #fff !important;
}
.sb-flyout-portal.sb-flyout-panel .sb-flyout-submenu .sb-submenu-arrow {
    font-size: 14px;
    opacity: 0.85;
    margin-left: auto;
}
.sb-flyout-portal.sb-flyout-panel .sb-flyout-submenu > .sidenav-menu.sb-flyout-panel {
    position: absolute !important;
    left: 100% !important;
    top: 0 !important;
    min-width: 240px;
    background: #0F5A4A !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.22) !important;
    border-radius: 4px;
    z-index: 1090 !important;
}
.sb-flyout-portal.sb-flyout-panel .sb-flyout-submenu.sb-flyout-left > .sidenav-menu.sb-flyout-panel {
    left: auto !important;
    right: 100% !important;
}
.sb-flyout-portal.sb-flyout-panel .sb-flyout-submenu > .sidenav-menu.sb-flyout-panel > .sidenav-item > .sidenav-link {
    color: #fff !important;
}
.sb-flyout-portal.sb-flyout-panel .sb-flyout-submenu > .sidenav-menu.sb-flyout-panel > .sidenav-item > .sidenav-link:hover {
    background: rgba(255, 255, 255, 0.1) !important;
    color: #fff !important;
}
.sb-flyout-portal.sb-flyout-panel .sidenav-divider {
    border-color: rgba(255, 255, 255, 0.15) !important;
    margin: 0.25rem 0;
}

/* Auto-flip: open on LEFT when not enough space on right */
.sidenav-horizontal .sb-flyout-submenu.sb-flyout-left > .sidenav-menu.sb-flyout-panel,
.sidenav-horizontal .sidenav-inner > .sidenav-item.sb-menu-align-right .sb-flyout-submenu > .sidenav-menu.sb-flyout-panel {
    left: auto !important;
    right: 100% !important;
}
.sidenav-horizontal .sb-flyout-submenu.sb-flyout-left > .sb-submenu-toggle .sb-submenu-arrow,
.sidenav-horizontal .sidenav-inner > .sidenav-item.sb-menu-align-right .sb-flyout-submenu > .sb-submenu-toggle .sb-submenu-arrow {
    transform: scaleX(-1);
}

/* Never show flyout when parent is closed */
.sidenav-horizontal .sb-flyout-submenu:not(:hover):not(.open) > .sidenav-menu.sb-flyout-panel,
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu.sb-top-menu-fixed .sb-flyout-submenu:not(:hover):not(.open) > .sidenav-menu.sb-flyout-panel {
    display: none !important;
}

/* Links inside flyout box */
.sidenav-horizontal .sb-flyout-panel > .sidenav-item {
    background: #0F5A4A !important;
}
.sidenav-horizontal .sb-flyout-panel > .sidenav-item:hover {
    background: #0F5A4A !important;
}
.sidenav-horizontal .sb-flyout-panel > .sidenav-item > .sidenav-link {
    color: #fff !important;
    background: transparent !important;
    padding: 0.5rem 1rem !important;
    font-size: 13px;
    white-space: nowrap;
    text-decoration: none !important;
}
.sidenav-horizontal .sb-flyout-panel > .sidenav-item > .sidenav-link:hover {
    background: rgba(255, 255, 255, 0.1) !important;
    color: #fff !important;
}

/* Nested flyout inside flyout (3rd level) */
.sidenav-horizontal .sb-flyout-panel .sb-flyout-submenu > .sb-submenu-toggle {
    color: #fff !important;
    background: transparent !important;
    padding: 0.5rem 1rem !important;
    text-decoration: none !important;
}
.sidenav-horizontal .sb-flyout-panel .sb-flyout-submenu > .sb-submenu-toggle:hover,
.sidenav-horizontal .sb-flyout-panel .sb-flyout-submenu.open > .sb-submenu-toggle,
.sidenav-horizontal .sb-flyout-panel .sb-flyout-submenu:hover > .sb-submenu-toggle {
    background: rgba(255, 255, 255, 0.1) !important;
    color: #fff !important;
}
.sidenav-horizontal .sb-flyout-panel .sb-flyout-submenu > .sidenav-menu.sb-flyout-panel {
    z-index: 1070 !important;
    background: #0F5A4A !important;
}

/* Direct links in main green dropdown (no submenu) */
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu > .sidenav-item:not(.sb-flyout-submenu) > .sidenav-link {
    color: #fff !important;
    background: transparent !important;
    padding: 0.5rem 1rem !important;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu > .sidenav-item:not(.sb-flyout-submenu) > .sidenav-link:hover {
    background: rgba(255, 255, 255, 0.1) !important;
}
<?php } ?>
</style>
<script>
(function () {
    if (document.documentElement.classList.contains('maha-menu-vertical')) {
        return;
    }
    var sidenav = document.getElementById('layout-sidenav');
    if (!sidenav) return;

    function getPanel(item) {
        return item._sbFlyoutPanel || item.querySelector(':scope > .sb-flyout-panel');
    }

    function getTopMenuItem(el) {
        return el ? el.closest('.sidenav-inner > .sidenav-item') : null;
    }

    var lastPointerX = null;
    var lastPointerY = null;
    var hoverCloseTimer = null;

    function isInsideMenuSystem(el) {
        if (!el || !el.closest) return false;
        return !!el.closest(
            '#layout-sidenav, .sidenav, .sidenav-horizontal, ' +
            '.sidenav-inner > .sidenav-item, .sidenav-menu, ' +
            '.sb-flyout-submenu, .sb-flyout-panel, body > .sb-flyout-portal'
        );
    }

    function restoreAllPortaledFlyouts() {
        document.querySelectorAll('body > .sb-flyout-portal').forEach(function (panel) {
            var owner = panel._sbFlyoutOwner;
            if (owner) {
                restoreFlyoutPortal(owner);
            } else {
                panel.remove();
            }
        });
    }

    function closeAllMenus() {
        if (hoverCloseTimer) {
            window.clearTimeout(hoverCloseTimer);
            hoverCloseTimer = null;
        }

        sidenav.querySelectorAll('.sidenav-inner > .sidenav-item').forEach(function (el) {
            el.classList.remove('open', 'sb-menu-pinned', 'active', 'show');
            resetTopMenu(el);
        });

        sidenav.querySelectorAll('.sb-flyout-submenu').forEach(function (el) {
            el.classList.remove('open', 'sb-flyout-pinned', 'active', 'show');
            resetFlyoutSide(el);
        });

        restoreAllPortaledFlyouts();
    }

    function closeOtherTopMenus(activeItem) {
        sidenav.querySelectorAll('.sidenav-inner > .sidenav-item').forEach(function (el) {
            if (activeItem && el === activeItem) return;
            el.classList.remove('open', 'sb-menu-pinned', 'active', 'show');
            resetTopMenu(el);
            el.querySelectorAll('.sb-flyout-submenu.open').forEach(function (fly) {
                fly.classList.remove('open', 'sb-flyout-pinned', 'active', 'show');
                resetFlyoutSide(fly);
            });
        });
    }

    function closeFlyoutDescendants(item) {
        if (!item || !item.querySelectorAll) return;
        item.querySelectorAll(':scope > .sb-flyout-submenu.open').forEach(function (nested) {
            closeFlyoutDescendants(nested);
            nested.classList.remove('open', 'sb-flyout-pinned', 'active', 'show');
            resetFlyoutSide(nested);
        });
    }

    function closeSiblingFlyouts(item) {
        var parent = item && item.parentElement;
        if (!parent || !parent.querySelectorAll) return;
        parent.querySelectorAll(':scope > .sb-flyout-submenu.open').forEach(function (el) {
            if (el === item) return;
            closeFlyoutDescendants(el);
            el.classList.remove('open', 'sb-flyout-pinned', 'active', 'show');
            resetFlyoutSide(el);
        });
    }

    function scheduleCloseHoverMenus() {
        if (hoverCloseTimer) {
            window.clearTimeout(hoverCloseTimer);
        }
        hoverCloseTimer = window.setTimeout(closeHoverMenus, 120);
    }

    function closeHoverMenus() {
        hoverCloseTimer = null;
        if (lastPointerX != null && lastPointerY != null) {
            var hit = document.elementFromPoint(lastPointerX, lastPointerY);
            if (isInsideMenuSystem(hit)) return;
        }
        closeAllMenus();
    }

    function closeMenusOnPageScroll() {
        closeAllMenus();
    }

    function bindPageScrollClose(el) {
        if (!el || el._mahaScrollCloseBound) return;
        el._mahaScrollCloseBound = true;
        el.addEventListener('scroll', closeMenusOnPageScroll, { passive: true, capture: true });
    }

    function bindAllPageScrollClose() {
        bindPageScrollClose(window);
        bindPageScrollClose(document);
        bindPageScrollClose(document.documentElement);
        bindPageScrollClose(document.body);
        document.querySelectorAll('.layout-content, .layout-wrapper, .layout-inner, .layout-container, main, .maha-dt-xscroll').forEach(bindPageScrollClose);
    }

    function handlePointerLeave(e) {
        if (isInsideMenuSystem(e.relatedTarget)) return;
        scheduleCloseHoverMenus();
    }

    function isPinned(item) {
        return false;
    }

    function hasPinnedFlyout() {
        return false;
    }

    function isFlyoutInteractionActive() {
        return false;
    }

    function isPointerInsideMenuSystem() {
        if (lastPointerX == null || lastPointerY == null) return false;
        return isInsideMenuSystem(document.elementFromPoint(lastPointerX, lastPointerY));
    }

    function hasOpenMenus() {
        return !!document.querySelector(
            '.sidenav-inner > .sidenav-item.open, .sb-flyout-submenu.open'
        );
    }

    function closeOpenHoverMenus(includePinned) {
        closeAllMenus();
    }

    function closeAllExceptTopMenu(activeTopItem) {
        closeOtherTopMenus(activeTopItem);
    }

    function pinTopMenuForItem(item) {
        var topItem = item.closest('.sidenav-inner > .sidenav-item');
        if (!topItem) return;
        topItem.classList.add('sb-menu-pinned', 'open');
        requestAnimationFrame(function () {
            positionTopMenu(topItem);
        });
    }

    function unpinTopMenuIfEmpty() {
        sidenav.querySelectorAll('.sidenav-inner > .sidenav-item.sb-menu-pinned').forEach(function (topItem) {
            if (!topItem.querySelector('.sb-flyout-submenu.sb-flyout-pinned')) {
                topItem.classList.remove('sb-menu-pinned');
            }
        });
    }

    function pinFlyout(item) {
        item.classList.add('sb-flyout-pinned');
        pinTopMenuForItem(item);
    }

    function unpinFlyout(item) {
        item.classList.remove('sb-flyout-pinned');
        unpinTopMenuIfEmpty();
    }

    function closeFlyouts(root) {
        if (root && root !== sidenav) {
            root.querySelectorAll('.sb-flyout-submenu.open').forEach(function (el) {
                el.classList.remove('open', 'sb-flyout-pinned', 'active', 'show');
                resetFlyoutSide(el);
            });
            return;
        }
        closeAllMenus();
    }

    function positionTopMenu(item) {
        var menu = item.querySelector(':scope > .sidenav-menu');
        var toggle = item.querySelector(':scope > .sidenav-toggle');
        if (!menu) return;

        menu.classList.add('sb-top-menu-fixed');
        menu.style.setProperty('min-width', '240px', 'important');
        menu.style.setProperty('max-height', Math.max(180, window.innerHeight - item.getBoundingClientRect().bottom - 8) + 'px', 'important');
        menu.style.setProperty('overflow', 'visible', 'important');

        menu.style.setProperty('visibility', 'hidden', 'important');
        menu.style.setProperty('display', 'flex', 'important');

        var toggleRect = toggle ? toggle.getBoundingClientRect() : item.getBoundingClientRect();
        var menuWidth = menu.offsetWidth || 240;
        var viewportPad = 8;
        var overflowRight = toggleRect.left + menuWidth > window.innerWidth - viewportPad;

        menu.style.setProperty('top', toggleRect.bottom + 'px', 'important');

        if (overflowRight) {
            item.classList.add('sb-menu-align-right');
            menu.style.removeProperty('left');
            menu.style.setProperty('right', (window.innerWidth - toggleRect.right) + 'px', 'important');
        } else {
            item.classList.remove('sb-menu-align-right');
            menu.style.removeProperty('right');
            menu.style.setProperty('left', toggleRect.left + 'px', 'important');
        }

        menu.style.removeProperty('visibility');

        if (menu.scrollHeight > menu.clientHeight + 1) {
            menu.classList.add('sb-dropdown-scrolling');
        } else {
            menu.classList.remove('sb-dropdown-scrolling');
        }
    }

    function resetTopMenu(item) {
        var menu = item.querySelector(':scope > .sidenav-menu');
        if (!menu) return;
        item.classList.remove('sb-menu-align-right');
        menu.classList.remove('sb-top-menu-fixed', 'sb-dropdown-scrolling');
        menu.style.removeProperty('top');
        menu.style.removeProperty('left');
        menu.style.removeProperty('right');
        menu.style.removeProperty('min-width');
        menu.style.removeProperty('max-height');
        menu.style.removeProperty('overflow');
        menu.style.removeProperty('visibility');
        menu.style.removeProperty('display');
    }

    function needsFixedFlyout(item) {
        var menu = item.parentElement;
        if (!menu || !menu.classList.contains('sidenav-menu') || menu.classList.contains('sb-flyout-panel')) {
            return false;
        }
        var topItem = menu.parentElement && menu.parentElement.closest('.sidenav-inner > .sidenav-item');
        if (!topItem) return false;
        var topMenu = topItem.querySelector(':scope > .sidenav-menu');
        if (topMenu && topMenu.classList.contains('sb-top-menu-fixed')) {
            return false;
        }
        return true;
    }

    function getFlyoutDepth(item) {
        var depth = 0;
        var el = item && item.parentElement;
        while (el && el !== sidenav) {
            if (el.classList && el.classList.contains('sb-flyout-panel')) {
                depth++;
            }
            el = el.parentElement;
        }
        return depth;
    }

    function isNestedFlyout(item) {
        var parentMenu = item && item.parentElement;
        if (!parentMenu || !parentMenu.classList.contains('sidenav-menu')) {
            return false;
        }
        if (parentMenu.classList.contains('sb-flyout-panel')) {
            return true;
        }
        return parentMenu.classList.contains('sb-top-menu-fixed');
    }

    function isMovingToFlyoutPanel(item, relatedTarget) {
        if (!relatedTarget || !item) return false;
        if (item.contains(relatedTarget)) return true;
        var panel = getPanel(item);
        return !!(panel && (panel === relatedTarget || panel.contains(relatedTarget)));
    }

    function keepFlyoutAncestorsOpen(item) {
        var el = item;
        while (el && el !== sidenav) {
            if (el.classList && el.classList.contains('sb-flyout-submenu')) {
                el.classList.add('open');
            }
            if (el.matches && el.matches('.sidenav-inner > .sidenav-item')) {
                el.classList.add('open');
                requestAnimationFrame(function () {
                    positionTopMenu(el);
                });
                break;
            }
            el = el.parentElement;
        }
    }

    function portalFlyout(item) {
        if (!needsFixedFlyout(item)) {
            restoreFlyoutPortal(item);
            item._sbFlyoutPanel = item.querySelector(':scope > .sb-flyout-panel');
            return;
        }

        var panel = item.querySelector(':scope > .sb-flyout-panel');
        if (!panel || panel.classList.contains('sb-flyout-portal')) {
            item._sbFlyoutPanel = panel;
            return;
        }

        if (!item._sbFlyoutAnchor) {
            item._sbFlyoutAnchor = document.createComment('sb-flyout');
            panel.parentNode.insertBefore(item._sbFlyoutAnchor, panel);
        }

        item._sbFlyoutPanel = panel;
        panel._sbFlyoutOwner = item;
        panel.classList.add('sb-flyout-portal');
        document.body.appendChild(panel);

        if (!panel._sbFlyoutBound) {
            panel._sbFlyoutBound = true;
            panel.addEventListener('mouseenter', function () {
                item.classList.add('open');
            });
            panel.addEventListener('mouseleave', handlePointerLeave);
        }
    }

    function restoreFlyoutPortal(item) {
        var panel = item._sbFlyoutPanel || item.querySelector(':scope > .sb-flyout-panel');
        if (!panel || !panel.classList.contains('sb-flyout-portal')) return;

        panel.classList.remove('sb-flyout-portal', 'sb-flyout-open', 'sb-flyout-nested-fixed');
        panel.style.top = '';
        panel.style.left = '';
        panel.style.right = '';
        panel.style.maxHeight = '';
        panel.style.display = '';
        panel.style.position = '';

        if (item._sbFlyoutAnchor && item._sbFlyoutAnchor.parentNode) {
            item._sbFlyoutAnchor.parentNode.insertBefore(panel, item._sbFlyoutAnchor.nextSibling);
        }

        item._sbFlyoutPanel = null;
        if (panel._sbFlyoutOwner === item) {
            panel._sbFlyoutOwner = null;
        }
    }

    function adjustFlyoutSide(item) {
        portalFlyout(item);

        var panel = getPanel(item);
        var toggle = item.querySelector(':scope > .sb-submenu-toggle');
        if (!panel || !toggle) return;

        var topItem = getTopMenuItem(item);
        var forceLeft = !!(topItem && topItem.classList.contains('sb-menu-align-right'));

        item.classList.remove('sb-flyout-left');
        panel.classList.remove('sb-flyout-nested-fixed', 'sb-flyout-open');
        panel.style.position = '';
        panel.style.top = '';
        panel.style.left = '';
        panel.style.right = '';
        panel.style.maxHeight = '';
        panel.style.visibility = '';
        panel.style.pointerEvents = '';
        panel.style.display = '';

        if (!item.classList.contains('open')) {
            return;
        }

        if (panel.classList.contains('sb-flyout-portal')) {
            panel.classList.add('sb-flyout-open');
        }
        panel.style.visibility = 'hidden';
        panel.style.pointerEvents = 'none';

        var toggleRect = toggle.getBoundingClientRect();
        var panelWidth = panel.offsetWidth || 280;
        var gap = 4;
        var viewportPad = 8;
        var overflowRight = toggleRect.right + gap + panelWidth > window.innerWidth - viewportPad;
        var overflowLeft = toggleRect.left - gap - panelWidth < viewportPad;

        if (forceLeft || (overflowRight && !overflowLeft) || (overflowRight && overflowLeft)) {
            item.classList.add('sb-flyout-left');
        }

        var useFixed = (needsFixedFlyout(item) && panel.classList.contains('sb-flyout-portal')) || isNestedFlyout(item);
        if (useFixed) {
            var openLeft = item.classList.contains('sb-flyout-left');
            var top = toggleRect.top;
            var left = openLeft
                ? toggleRect.left - gap - panelWidth
                : toggleRect.right + gap;
            left = Math.max(viewportPad, Math.min(left, window.innerWidth - panelWidth - viewportPad));
            panel.classList.add('sb-flyout-nested-fixed');
            panel.style.position = 'fixed';
            panel.style.top = top + 'px';
            panel.style.left = left + 'px';
            panel.style.right = 'auto';
            panel.style.maxHeight = Math.max(120, window.innerHeight - top - viewportPad) + 'px';
            panel.style.zIndex = String(1060 + getFlyoutDepth(item) * 10);
        } else {
            panel.classList.remove('sb-flyout-nested-fixed');
        }

        panel.style.visibility = 'visible';
        panel.style.pointerEvents = '';
    }

    function resetFlyoutSide(item) {
        item.classList.remove('sb-flyout-left');
        var panel = getPanel(item);
        if (panel) {
            panel.classList.remove('sb-flyout-nested-fixed', 'sb-flyout-open');
            panel.style.position = '';
            panel.style.top = '';
            panel.style.left = '';
            panel.style.right = '';
            panel.style.maxHeight = '';
            panel.style.visibility = '';
            panel.style.pointerEvents = '';
            panel.style.display = '';
            panel.style.zIndex = '';
        }
        restoreFlyoutPortal(item);
    }

    function openFlyout(item, shouldPin) {
        var activeTop = getTopMenuItem(item);
        closeOtherTopMenus(activeTop);
        closeSiblingFlyouts(item);
        item.classList.add('open');
        requestAnimationFrame(function () {
            adjustFlyoutSide(item);
        });
    }

    function toggleFlyout(item) {
        var isOpen = item.classList.contains('open');
        closeSiblingFlyouts(item);
        if (isOpen) {
            item.classList.remove('open', 'sb-flyout-pinned', 'active', 'show');
            resetFlyoutSide(item);
            return;
        }
        openFlyout(item, false);
    }

    restoreAllPortaledFlyouts();

    document.addEventListener('mousemove', function (e) {
        lastPointerX = e.clientX;
        lastPointerY = e.clientY;
    }, { passive: true });

    sidenav.querySelectorAll('.sb-flyout-submenu').forEach(function (item) {
        item.addEventListener('mouseenter', function () {
            openFlyout(item, false);
        });
        item.addEventListener('mouseleave', function (e) {
            if (isMovingToFlyoutPanel(item, e.relatedTarget)) return;
            closeFlyoutDescendants(item);
            handlePointerLeave(e);
        });
    });

    sidenav.querySelectorAll('.sb-flyout-panel').forEach(function (panel) {
        if (panel._sbPanelHoverBound) return;
        panel._sbPanelHoverBound = true;
        panel.addEventListener('mouseenter', function () {
            if (hoverCloseTimer) {
                window.clearTimeout(hoverCloseTimer);
                hoverCloseTimer = null;
            }
            var owner = panel.parentElement;
            if (owner && owner.classList.contains('sb-flyout-submenu')) {
                keepFlyoutAncestorsOpen(owner);
            }
        });
        panel.addEventListener('mouseleave', function (e) {
            var owner = panel.parentElement;
            if (!owner || !owner.classList.contains('sb-flyout-submenu')) return;
            if (isMovingToFlyoutPanel(owner, e.relatedTarget)) return;
            closeFlyoutDescendants(owner);
            handlePointerLeave(e);
        });
    });

    sidenav.querySelectorAll('.sidenav-inner > .sidenav-item > .sidenav-menu').forEach(function (menu) {
        menu.addEventListener('mouseenter', function () {
            var item = menu.closest('.sidenav-inner > .sidenav-item');
            if (!item) return;
            if (hoverCloseTimer) {
                window.clearTimeout(hoverCloseTimer);
                hoverCloseTimer = null;
            }
            item.classList.add('open');
            requestAnimationFrame(function () {
                positionTopMenu(item);
            });
        });
        menu.addEventListener('mouseleave', handlePointerLeave);
    });

    sidenav.querySelectorAll('.sidenav-inner > .sidenav-item').forEach(function (item) {
        if (!item.querySelector(':scope > .sidenav-toggle')) return;

        item.addEventListener('mouseenter', function () {
            closeOtherTopMenus(item);
            item.classList.add('open');
            requestAnimationFrame(function () {
                positionTopMenu(item);
            });
        });

        item.addEventListener('mouseleave', handlePointerLeave);

        var topToggle = item.querySelector(':scope > .sidenav-toggle');
        if (topToggle) {
            topToggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var isOpen = item.classList.contains('open');
                closeOtherTopMenus(item);
                if (isOpen) {
                    item.classList.remove('open', 'active', 'show');
                    resetTopMenu(item);
                } else {
                    item.classList.add('open');
                    positionTopMenu(item);
                }
            }, true);
        }
    });

    sidenav.addEventListener('mouseleave', handlePointerLeave);

    document.addEventListener('scroll', closeMenusOnPageScroll, { passive: true, capture: true });
    bindAllPageScrollClose();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindAllPageScrollClose);
    }
    window.addEventListener('load', bindAllPageScrollClose);

    document.addEventListener('wheel', function (e) {
        if (!hasOpenMenus()) return;
        var menuScroller = e.target.closest('.sidenav-menu, .sb-flyout-panel');
        if (menuScroller) {
            var delta = e.deltaY || 0;
            var atTop = menuScroller.scrollTop <= 0;
            var atBottom = menuScroller.scrollTop + menuScroller.clientHeight >= menuScroller.scrollHeight - 1;
            if ((delta < 0 && !atTop) || (delta > 0 && !atBottom)) {
                return;
            }
        }
        closeMenusOnPageScroll();
    }, { passive: true });

    window.addEventListener('resize', function () {
        closeAllMenus();
    });

    document.addEventListener('click', function (e) {
        if (isInsideMenuSystem(e.target)) return;
        closeAllMenus();
    });

    sidenav.addEventListener('click', function (e) {
        if (e.target.closest('.sidenav-horizontal-prev, .sidenav-horizontal-next')) {
            closeAllMenus();
        }
    });

    sidenav.addEventListener('click', function (e) {
        var toggle = e.target.closest('.sb-submenu-toggle');
        if (!toggle) return;

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        var item = toggle.closest('.sb-flyout-submenu');
        if (!item) return;

        toggleFlyout(item);
    }, true);

    window.addEventListener('load', function () {
        restoreAllPortaledFlyouts();
        if (window.layoutSidenav && typeof window.layoutSidenav.closeAll === 'function') {
            window.layoutSidenav.closeAll = function () {
                closeAllMenus();
            };
        }
    });
})();
</script>
<?php if ($mahaMenuVertical) { ?>
<script>
(function () {
    var root = document.getElementById('layout-sidenav');
    if (!root || !root.classList.contains('maha-vertical-sidenav')) return;

    function closeVerticalSubmenus(exceptItem) {
        root.querySelectorAll('.sb-flyout-submenu.open').forEach(function (el) {
            if (exceptItem && el === exceptItem) return;
            el.classList.remove('open', 'active', 'show');
        });
    }

    root.addEventListener('click', function (e) {
        var toggle = e.target.closest('.sb-submenu-toggle');
        if (!toggle || !root.contains(toggle)) return;
        if (toggle.classList.contains('sidenav-toggle')) return;

        e.preventDefault();
        e.stopPropagation();

        var item = toggle.closest('.sb-flyout-submenu');
        if (!item) return;

        var isOpen = item.classList.contains('open');
        closeVerticalSubmenus(item);
        if (isOpen) {
            item.classList.remove('open', 'active', 'show');
        } else {
            item.classList.add('open');
        }
    });

    root.addEventListener('mouseleave', function (e) {
        if (e.relatedTarget && root.contains(e.relatedTarget)) return;
        closeVerticalSubmenus(null);
    });

    document.addEventListener('click', function (e) {
        if (root.contains(e.target)) return;
        closeVerticalSubmenus(null);
    });

    document.addEventListener('scroll', function () {
        closeVerticalSubmenus(null);
    }, true);
})();
</script>
<?php } ?>
<script>
    setInterval(function() {
        fetch('ping.php');
    }, 10000);
</script>
<?php
if (function_exists('maha_render_menu_theme_styles')) {
    maha_render_menu_theme_styles($uiPrefs);
}
if (function_exists('maha_render_menu_reorder_script')) {
    maha_render_menu_reorder_script();
}
?>

