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
    align-self: stretch;
    min-height: 100%;
    height: auto;
    max-height: none;
    overflow-y: auto;
    overflow-x: hidden;
    overscroll-behavior: contain;
}
#layout-sidenav.maha-vertical-sidenav .sidenav-inner {
    flex-direction: column !important;
    padding-top: 0.5rem;
    min-height: 100%;
    box-sizing: border-box;
    padding-bottom: 1rem;
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
#layout-sidenav.maha-vertical-sidenav .sidenav-item > .sidenav-menu .sidenav-menu .sidenav-link,
#layout-sidenav.maha-vertical-sidenav .sidenav-item > .sidenav-menu .sidenav-menu .sb-flyout-submenu > .sb-submenu-toggle {
    padding-left: 3rem !important;
}
#layout-sidenav.maha-vertical-sidenav .sidenav-item > .sidenav-menu .sidenav-menu .sidenav-menu .sidenav-link {
    padding-left: 3.75rem !important;
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
#layout-sidenav.maha-vertical-sidenav .sidenav-link > div,
#layout-sidenav.maha-vertical-sidenav .sidenav-toggle > div,
#layout-sidenav.maha-vertical-sidenav .sb-submenu-toggle > div {
    opacity: 1 !important;
    visibility: visible !important;
    color: inherit !important;
}
#layout-sidenav.maha-vertical-sidenav > .sidenav-inner > .sidenav-item.open > .sidenav-toggle,
#layout-sidenav.maha-vertical-sidenav .sidenav-item.open > .sidenav-toggle,
#layout-sidenav.maha-vertical-sidenav .sb-flyout-submenu.open > .sb-submenu-toggle {
    background: var(--maha-accent) !important;
    color: #fff !important;
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

.sidenav.bg-dark,
.sidenav.bg-white {
    overflow: visible !important;
}
/* Menu scroll zone — allow dropdown below navbar (beats theme .sidenav-horizontal-wrapper { overflow:hidden }) */
.sidenav-horizontal .sidenav-horizontal-wrapper,
.sidenav-horizontal .sidenav-inner,
#layout-sidenav .sidenav-horizontal-wrapper,
#layout-sidenav .sidenav-inner {
    overflow: visible !important;
}
.sidenav-horizontal .sidenav-horizontal-wrapper {
    flex: 1 1 0 !important;
    width: auto !important;
    min-width: 0 !important;
    position: relative;
}
.layout-navbar .navbar-collapse,
.layout-navbar,
#layout-sidenav.sidenav-horizontal {
    overflow: visible !important;
}
.layout-navbar {
    position: relative;
    z-index: 1090;
}
#layout-sidenav.sidenav-horizontal {
    position: relative;
    z-index: 1090;
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
    overflow: visible !important;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu {
    display: none;
    flex-direction: column;
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    right: auto !important;
    min-width: 240px !important;
    max-width: 320px;
    width: max-content;
    max-height: 75vh;
    overflow-x: visible !important;
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch;
    background: #0F5A4A !important;
    border-radius: 0 0 4px 4px;
    padding: 0.35rem 0;
    z-index: 99999 !important;
    box-sizing: border-box;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
    visibility: hidden;
    opacity: 0;
    pointer-events: none;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu.sb-top-menu-fixed {
    position: absolute !important;
    top: 100% !important;
    z-index: 1095 !important;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item.sb-menu-align-right > .sidenav-menu {
    left: auto !important;
    right: 0 !important;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu.sb-dropdown-scrolling {
    overflow-y: auto !important;
    overflow-x: visible !important;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu > .sidenav-item,
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu > .sb-flyout-submenu {
    background: #0F5A4A !important;
    flex-shrink: 0;
    width: 100%;
    min-width: 240px;
    max-width: 320px;
    box-sizing: border-box;
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
.sidenav-horizontal .sidenav-inner > .sidenav-item.open > .sidenav-menu {
    display: flex !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
    overflow: visible !important;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item:not(.open) > .sidenav-menu {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    pointer-events: none !important;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item:not(.open) > .sidenav-menu.sb-top-menu-fixed {
    display: none !important;
    visibility: hidden !important;
    pointer-events: none !important;
}
body > .sb-flyout-portal:not(.sb-flyout-open) {
    display: none !important;
    pointer-events: none !important;
}

/* ── Flyout parent row (e.g. Employee Master >) ── */
.sidenav-horizontal .sb-flyout-submenu {
    position: relative !important;
    width: 100%;
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
    position: relative;
}
.sidenav-horizontal .sb-flyout-submenu.open:not(.sb-flyout-left) > .sb-submenu-toggle::after,
.sb-flyout-portal .sb-flyout-submenu.open:not(.sb-flyout-left) > .sb-submenu-toggle::after {
    content: '';
    position: absolute;
    top: 0;
    right: -20px;
    width: 20px;
    height: 100%;
    background: transparent;
    pointer-events: auto;
}
.sidenav-horizontal .sb-flyout-submenu.open.sb-flyout-left > .sb-submenu-toggle::after,
.sb-flyout-portal .sb-flyout-submenu.open.sb-flyout-left > .sb-submenu-toggle::after {
    content: '';
    position: absolute;
    top: 0;
    left: -20px;
    width: 20px;
    height: 100%;
    background: transparent;
    pointer-events: auto;
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

/* Nested flyout panels — beside parent row (absolute), not over parent list */
.sidenav-horizontal .sidenav-menu .sidenav-menu.sb-flyout-panel {
    position: absolute !important;
    left: 100% !important;
    top: 0 !important;
    right: auto !important;
    width: max-content !important;
    min-width: 240px !important;
    max-width: 320px !important;
    max-height: 75vh;
    overflow-x: visible !important;
    overflow-y: auto !important;
    margin: 0 0 0 2px !important;
    padding: 0.35rem 0 !important;
    background: #0F5A4A !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.22) !important;
    border-radius: 4px;
    z-index: 1100 !important;
    list-style: none;
    flex-direction: column !important;
    display: none !important;
    visibility: hidden;
    opacity: 0;
    pointer-events: none;
    box-sizing: border-box;
}
.sidenav-horizontal .sb-flyout-panel .sb-flyout-panel {
    z-index: 1105 !important;
}
.sidenav-horizontal .sb-flyout-panel .sb-flyout-panel .sb-flyout-panel {
    z-index: 1110 !important;
}
/* Portaled / fixed flyout when parent scroll container would clip */
.sidenav-horizontal .sb-flyout-submenu > .sidenav-menu.sb-flyout-panel.sb-flyout-nested-fixed,
.sb-flyout-portal.sb-flyout-panel.sb-flyout-nested-fixed {
    position: fixed !important;
    left: auto !important;
    top: auto !important;
    right: auto !important;
    display: none !important;
    margin: 0 !important;
    min-width: 240px !important;
    max-width: 320px !important;
    width: max-content !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.22) !important;
    border-radius: 4px;
    z-index: 1100 !important;
}
.sidenav-horizontal .sb-flyout-submenu.open > .sidenav-menu.sb-flyout-panel.sb-flyout-nested-fixed,
.sb-flyout-portal.sb-flyout-panel.sb-flyout-open.sb-flyout-nested-fixed {
    display: flex !important;
}

/* Show flyout only while branch is open */
.sidenav-horizontal .sb-flyout-submenu.open > .sidenav-menu.sb-flyout-panel {
    display: flex !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
}
.sidenav-horizontal .sb-flyout-submenu:not(.open) > .sidenav-menu.sb-flyout-panel,
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu .sb-flyout-submenu:not(.open) > .sidenav-menu.sb-flyout-panel {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    pointer-events: none !important;
}

.sb-flyout-portal .sb-flyout-submenu.open > .sidenav-menu.sb-flyout-panel {
    display: flex !important;
}
.sb-flyout-portal .sb-flyout-submenu:not(.open) > .sidenav-menu.sb-flyout-panel {
    display: none !important;
}

.sb-flyout-portal .sb-flyout-submenu {
    position: relative !important;
}
.sb-flyout-portal.sb-flyout-panel {
    position: fixed !important;
    display: none !important;
    flex-direction: column !important;
    min-width: 240px !important;
    max-width: 320px !important;
    width: max-content !important;
    max-height: 75vh;
    overflow-y: auto;
    overflow-x: visible;
    margin: 0 !important;
    padding: 0.35rem 0 !important;
    background: #0F5A4A !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.22) !important;
    border-radius: 4px;
    z-index: 1100 !important;
    list-style: none;
    box-sizing: border-box;
}
.sb-flyout-portal.sb-flyout-panel.sb-flyout-open {
    display: flex !important;
}
/* Invisible hover bridge between parent row and flyout panel (prevents menu closing in the gap) */
.sb-flyout-portal.sb-flyout-panel.sb-flyout-open::before,
.sidenav-horizontal .sb-flyout-submenu > .sidenav-menu.sb-flyout-panel.sb-flyout-nested-fixed::before {
    content: '';
    position: absolute;
    top: 0;
    width: 16px;
    height: 100%;
    background: transparent;
    pointer-events: auto;
}
.sb-flyout-portal.sb-flyout-panel.sb-flyout-open::before {
    left: -16px;
}
.sidenav-horizontal .sb-flyout-submenu:not(.sb-flyout-left) > .sidenav-menu.sb-flyout-panel.sb-flyout-nested-fixed::before {
    left: -16px;
}
.sidenav-horizontal .sb-flyout-submenu.sb-flyout-left > .sidenav-menu.sb-flyout-panel.sb-flyout-nested-fixed::before {
    left: auto;
    right: -16px;
}
.sidenav-horizontal .sb-flyout-submenu > .sidenav-menu.sb-flyout-panel.sb-flyout-nested-fixed {
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch;
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

.sb-flyout-portal .sb-flyout-submenu > .sidenav-menu.sb-flyout-panel {
    min-width: 240px !important;
    max-width: 320px !important;
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
.sidenav-horizontal .sb-flyout-panel .sb-flyout-submenu.open > .sb-submenu-toggle {
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

    var STATE_CLASSES = ['open', 'show', 'active', 'sb-menu-pinned', 'sb-flyout-pinned'];
    var UI_CLASSES = STATE_CLASSES.concat([
        'sb-flyout-open', 'sb-flyout-nested-fixed', 'sb-top-menu-fixed',
        'sb-dropdown-scrolling', 'sb-menu-align-right', 'sb-flyout-left'
    ]);
    var MENU_STYLE_PROPS = [
        'top', 'left', 'right', 'min-width', 'max-width', 'width', 'max-height', 'overflow', 'overflow-y',
        'visibility', 'display', 'position', 'z-index', 'pointer-events', 'margin'
    ];
    var MENU_Z_BASE = 1095;
    var FLYOUT_Z_BASE = 1100;
    var lastPointerX = null;
    var lastPointerY = null;

    function stripState(el) {
        if (!el || !el.classList) return;
        STATE_CLASSES.forEach(function (cls) {
            el.classList.remove(cls);
        });
    }

    function stripUiClasses(el) {
        if (!el || !el.classList) return;
        UI_CLASSES.forEach(function (cls) {
            el.classList.remove(cls);
        });
    }

    function clearMenuInlineStyles(el) {
        if (!el || !el.style) return;
        MENU_STYLE_PROPS.forEach(function (prop) {
            el.style.removeProperty(prop);
        });
    }

    function getFlyoutAncestorChain(item) {
        var chain = [];
        var el = item;
        while (el && el !== sidenav) {
            if (el.classList && el.classList.contains('sb-flyout-submenu')) {
                chain.unshift(el);
            }
            el = el.parentElement;
        }
        return chain;
    }

    function getPanel(item) {
        return item._sbFlyoutPanel || item.querySelector(':scope > .sb-flyout-panel');
    }

    function getTopMenuItem(el) {
        return el ? el.closest('.sidenav-inner > .sidenav-item') : null;
    }

    function isNavZone(el) {
        if (!el || !el.closest) return false;
        if (el.closest('#layout-sidenav')) return true;
        if (el.closest('body > .sb-flyout-portal.sb-flyout-panel.sb-flyout-open')) return true;
        if (el.closest('.sidenav-menu.sb-flyout-panel.sb-flyout-nested-fixed')) return true;
        return false;
    }

    function isPointerOverOpenFlyout(x, y) {
        var hit = document.elementFromPoint(x, y);
        if (!hit || !hit.closest) return false;
        var topOpen = hit.closest('.sidenav-inner > .sidenav-item.open');
        if (!topOpen) return false;
        return !!hit.closest(
            '.sb-flyout-submenu.open, .sb-flyout-portal.sb-flyout-open, ' +
            '.sidenav-menu.sb-flyout-panel.sb-flyout-nested-fixed, ' +
            '.sidenav-inner > .sidenav-item.open > .sidenav-menu'
        );
    }

    function isNavigableLink(link) {
        if (!link || !link.classList) return false;
        if (link.classList.contains('sb-submenu-toggle') || link.classList.contains('sidenav-toggle')) {
            return false;
        }
        var href = (link.getAttribute('href') || '').trim();
        return href && href !== '#' && href.indexOf('javascript:') !== 0;
    }

    function getFlyoutOwner(panel) {
        if (!panel) return null;
        if (panel._sbFlyoutOwner) return panel._sbFlyoutOwner;
        var parent = panel.parentElement;
        if (parent && parent.classList && parent.classList.contains('sb-flyout-submenu')) {
            return parent;
        }
        return null;
    }

    function isFlyoutRelated(item, relatedTarget) {
        if (!item || !relatedTarget) return false;
        if (item === relatedTarget || item.contains(relatedTarget)) return true;
        var panel = getPanel(item);
        if (panel && (panel === relatedTarget || panel.contains(relatedTarget))) return true;
        if (relatedTarget.closest) {
            if (relatedTarget.closest('.sb-flyout-submenu') === item) return true;
            if (panel && relatedTarget.closest('.sb-flyout-panel') === panel) return true;
        }
        return false;
    }

    function cleanupOrphanPortals() {
        document.querySelectorAll('body > .sb-flyout-portal').forEach(function (panel) {
            var owner = panel._sbFlyoutOwner;
            if (owner) {
                restoreFlyoutPortal(owner);
            } else {
                panel.remove();
            }
        });
    }

    function forceRestoreAllBodyPortals() {
        var owners = [];
        sidenav.querySelectorAll('.sb-flyout-submenu').forEach(function (item) {
            if (item._sbFlyoutPanel) {
                owners.push(item);
            }
        });
        owners.forEach(function (item) {
            restoreFlyoutPortal(item);
        });
        document.querySelectorAll('body > .sb-flyout-portal').forEach(function (panel) {
            stripUiClasses(panel);
            clearMenuInlineStyles(panel);
            panel.classList.remove('sb-flyout-portal');
            if (panel.parentNode === document.body) {
                panel.remove();
            }
        });
    }

    function restoreAllPortaledFlyouts() {
        forceRestoreAllBodyPortals();
        cleanupOrphanPortals();
    }

    function resetTopMenu(item) {
        var menu = item.querySelector(':scope > .sidenav-menu');
        stripUiClasses(item);
        if (!menu) return;
        stripUiClasses(menu);
        clearMenuInlineStyles(menu);
    }

    function restoreFlyoutPortal(item) {
        var panel = item._sbFlyoutPanel || item.querySelector(':scope > .sb-flyout-panel');
        if (!panel || !panel.classList.contains('sb-flyout-portal')) return;

        stripUiClasses(panel);
        clearMenuInlineStyles(panel);
        panel.classList.remove('sb-flyout-portal');

        if (item._sbFlyoutAnchor && item._sbFlyoutAnchor.parentNode) {
            item._sbFlyoutAnchor.parentNode.insertBefore(panel, item._sbFlyoutAnchor.nextSibling);
        }

        item._sbFlyoutPanel = null;
        if (panel._sbFlyoutOwner === item) {
            panel._sbFlyoutOwner = null;
        }
    }

    function resetFlyoutSide(item) {
        stripUiClasses(item);
        var panel = getPanel(item);
        if (panel) {
            stripUiClasses(panel);
            clearMenuInlineStyles(panel);
        }
        restoreFlyoutPortal(item);
        item._sbFlyoutPanel = null;
    }

    function purgeAllMenuUI() {
        forceRestoreAllBodyPortals();

        sidenav.querySelectorAll('.sidenav-inner > .sidenav-item').forEach(function (el) {
            resetTopMenu(el);
            stripState(el);
        });

        sidenav.querySelectorAll('.sb-flyout-submenu').forEach(function (el) {
            resetFlyoutSide(el);
            stripState(el);
        });

        document.querySelectorAll(
            '.sb-flyout-open, .sb-flyout-nested-fixed, .sb-top-menu-fixed, .sb-flyout-portal'
        ).forEach(function (el) {
            stripUiClasses(el);
            clearMenuInlineStyles(el);
        });
    }

    function closeFlyoutBranch(item) {
        if (!item) return;
        item.querySelectorAll('.sb-flyout-submenu').forEach(function (nested) {
            closeFlyoutBranch(nested);
        });
        item.classList.remove('open', 'show', 'active', 'sb-flyout-left');
        hideFlyoutPanel(item);
        restoreFlyoutPortal(item);
        item._sbFlyoutPanel = null;
    }

    function closeFlyoutDescendants(item) {
        if (!item || !item.querySelectorAll) return;
        item.querySelectorAll(':scope > .sb-flyout-submenu').forEach(function (nested) {
            closeFlyoutBranch(nested);
        });
    }

    function closeSiblingFlyouts(item) {
        var parent = item && item.parentElement;
        if (!parent || !parent.querySelectorAll) return;
        parent.querySelectorAll(':scope > .sb-flyout-submenu').forEach(function (el) {
            if (el === item) return;
            closeFlyoutBranch(el);
        });
    }

    function showTopMenuPanel(item) {
        var menu = item && item.querySelector(':scope > .sidenav-menu');
        if (!menu) return;
        menu.style.setProperty('display', 'flex', 'important');
        menu.style.setProperty('visibility', 'visible', 'important');
        menu.style.setProperty('opacity', '1', 'important');
        menu.style.setProperty('pointer-events', 'auto', 'important');
    }

    function hideTopMenuPanel(item) {
        var menu = item && item.querySelector(':scope > .sidenav-menu');
        if (!menu) return;
        menu.style.setProperty('display', 'none', 'important');
        menu.style.setProperty('visibility', 'hidden', 'important');
        menu.style.setProperty('opacity', '0', 'important');
        menu.style.setProperty('pointer-events', 'none', 'important');
    }

    function showFlyoutPanel(item) {
        var panel = item && item.querySelector(':scope > .sidenav-menu.sb-flyout-panel');
        if (!panel) return;
        panel.style.setProperty('display', 'flex', 'important');
        panel.style.setProperty('visibility', 'visible', 'important');
        panel.style.setProperty('opacity', '1', 'important');
        panel.style.setProperty('pointer-events', 'auto', 'important');
    }

    function hideFlyoutPanel(item) {
        var panel = item && item.querySelector(':scope > .sidenav-menu.sb-flyout-panel');
        if (!panel) return;
        panel.style.setProperty('display', 'none', 'important');
        panel.style.setProperty('visibility', 'hidden', 'important');
        panel.style.setProperty('opacity', '0', 'important');
        panel.style.setProperty('pointer-events', 'none', 'important');
    }

    function closeOtherTopMenus(activeItem) {
        sidenav.querySelectorAll('.sidenav-inner > .sidenav-item').forEach(function (el) {
            if (activeItem && el === activeItem) return;
            el.classList.remove('open', 'show', 'active', 'sb-menu-align-right');
            hideTopMenuPanel(el);
            el.querySelectorAll('.sb-flyout-submenu').forEach(function (fly) {
                closeFlyoutBranch(fly);
            });
        });
    }

    function closeAllTopMenus() {
        sidenav.querySelectorAll('.sidenav-inner > .sidenav-item').forEach(function (el) {
            el.classList.remove('open', 'show', 'active', 'sb-menu-align-right');
            hideTopMenuPanel(el);
            el.querySelectorAll('.sb-flyout-submenu').forEach(function (fly) {
                closeFlyoutBranch(fly);
            });
        });
    }

    function closeAllMenus() {
        closeAllTopMenus();
        sidenav.querySelectorAll('.sb-flyout-submenu').forEach(function (fly) {
            closeFlyoutBranch(fly);
        });
        document.querySelectorAll('body > .sb-flyout-portal').forEach(function (panel) {
            panel.remove();
        });
    }

    function closeTopMenu(item) {
        if (!item) return;
        item.classList.remove('open', 'show', 'active', 'sb-menu-align-right');
        hideTopMenuPanel(item);
        item.querySelectorAll('.sb-flyout-submenu').forEach(function (fly) {
            closeFlyoutBranch(fly);
        });
    }

    function maybeCloseMenusFromPointer() {
        window.requestAnimationFrame(function () {
            if (lastPointerX == null || lastPointerY == null) {
                return;
            }
            if (isNavZone(document.elementFromPoint(lastPointerX, lastPointerY))) {
                return;
            }
            closeAllMenus();
        });
    }

    function positionTopMenu(item) {
        var menu = item.querySelector(':scope > .sidenav-menu');
        var toggle = item.querySelector(':scope > .sidenav-toggle');
        if (!menu || !item.classList.contains('open')) return;

        showTopMenuPanel(item);

        var itemRect = item.getBoundingClientRect();
        var maxH = Math.max(180, window.innerHeight - itemRect.bottom - 12);
        menu.style.setProperty('max-height', maxH + 'px', 'important');

        var toggleRect = toggle ? toggle.getBoundingClientRect() : itemRect;
        var menuWidth = Math.min(320, Math.max(240, menu.offsetWidth || 260));
        var viewportPad = 8;
        if (toggleRect.left + menuWidth > window.innerWidth - viewportPad) {
            item.classList.add('sb-menu-align-right');
        } else {
            item.classList.remove('sb-menu-align-right');
        }
    }

    function needsPortalFlyout(item) {
        return false;
    }

    function measureFlyoutWidth(panel) {
        if (!panel) return 260;
        var hadOpen = panel.classList.contains('sb-flyout-open');
        panel.style.setProperty('display', 'flex', 'important');
        panel.style.setProperty('visibility', 'hidden', 'important');
        panel.style.setProperty('position', 'absolute', 'important');
        panel.style.setProperty('left', '-9999px', 'important');
        var width = panel.scrollWidth || panel.offsetWidth || 260;
        panel.style.removeProperty('visibility');
        panel.style.removeProperty('position');
        panel.style.removeProperty('left');
        if (!hadOpen && !panel.classList.contains('sb-flyout-open')) {
            panel.style.removeProperty('display');
        }
        return Math.min(320, Math.max(240, width));
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
        return needsPortalFlyout(item);
    }

    function ensureFlyoutAncestorsOpen(item) {
        var topItem = getTopMenuItem(item);
        if (topItem) {
            topItem.classList.add('open');
            showTopMenuPanel(topItem);
            positionTopMenu(topItem);
        }
        getFlyoutAncestorChain(item).forEach(function (fly) {
            fly.classList.add('open');
            showFlyoutPanel(fly);
            positionFlyoutPanel(fly);
        });
    }

    function positionFlyoutPanel(item) {
        if (!item || !item.classList.contains('open')) return;

        restoreFlyoutPortal(item);
        item._sbFlyoutPanel = item.querySelector(':scope > .sidenav-menu.sb-flyout-panel');

        var panel = item.querySelector(':scope > .sidenav-menu.sb-flyout-panel');
        var toggle = item.querySelector(':scope > .sb-submenu-toggle');
        if (!panel || !toggle) return;

        showFlyoutPanel(item);

        panel.classList.remove('sb-flyout-nested-fixed', 'sb-flyout-portal', 'sb-flyout-open');
        panel.style.removeProperty('margin');
        panel.style.removeProperty('width');
        panel.style.removeProperty('max-height');
        panel.style.removeProperty('overflow-y');

        var topItem = getTopMenuItem(item);
        var forceLeft = !!(topItem && topItem.classList.contains('sb-menu-align-right'));

        item.classList.remove('sb-flyout-left');
        var toggleRect = toggle.getBoundingClientRect();
        var panelWidth = Math.min(320, Math.max(240, panel.offsetWidth || panel.scrollWidth || 260));
        var gap = 2;
        var viewportPad = 8;
        var overflowRight = toggleRect.right + gap + panelWidth > window.innerWidth - viewportPad;
        var overflowLeft = toggleRect.left - gap - panelWidth < viewportPad;

        if (forceLeft || (overflowRight && !overflowLeft) || (overflowRight && overflowLeft)) {
            item.classList.add('sb-flyout-left');
        }

        panel.style.setProperty('position', 'absolute', 'important');
        panel.style.setProperty('top', '0', 'important');
        if (item.classList.contains('sb-flyout-left')) {
            panel.style.setProperty('left', 'auto', 'important');
            panel.style.setProperty('right', '100%', 'important');
        } else {
            panel.style.setProperty('left', '100%', 'important');
            panel.style.setProperty('right', 'auto', 'important');
        }
        panel.style.setProperty('z-index', String(FLYOUT_Z_BASE + getFlyoutDepth(item) * 5), 'important');
    }

    function bindPortalPanelEvents(item, panel) {
        if (panel._sbFlyoutBound) return;
        panel._sbFlyoutBound = true;
        panel.addEventListener('mouseenter', function () {
            var topItem = getTopMenuItem(item);
            closeOtherTopMenus(topItem);
            if (topItem) {
                topItem.classList.add('open');
                showTopMenuPanel(topItem);
                positionTopMenu(topItem);
            }
            getFlyoutAncestorChain(item).forEach(function (fly) {
                fly.classList.add('open');
                showFlyoutPanel(fly);
                positionFlyoutPanel(fly);
            });
        });
        panel.addEventListener('mouseleave', function (e) {
            if (isFlyoutRelated(item, e.relatedTarget)) return;
            maybeCloseMenusFromPointer();
        });
    }

    function portalFlyout(item) {
        restoreFlyoutPortal(item);
        item._sbFlyoutPanel = item.querySelector(':scope > .sb-flyout-panel');
    }

    function positionFlyoutPanelFixed(item, panel, toggle) {
        positionFlyoutPanel(item);
    }

    function adjustFlyoutSide(item) {
        positionFlyoutPanel(item);
    }

    function openTopMenu(item) {
        if (!item) return;
        closeOtherTopMenus(item);
        item.classList.add('open');
        showTopMenuPanel(item);
        positionTopMenu(item);
    }

    function openFlyout(item) {
        if (!item) return;
        var topItem = getTopMenuItem(item);

        closeOtherTopMenus(topItem);

        if (topItem) {
            topItem.classList.add('open');
            showTopMenuPanel(topItem);
            positionTopMenu(topItem);
        }

        getFlyoutAncestorChain(item).forEach(function (fly) {
            if (fly === item) return;
            fly.classList.add('open');
            showFlyoutPanel(fly);
            positionFlyoutPanel(fly);
        });

        item.classList.add('open');
        showFlyoutPanel(item);
        positionFlyoutPanel(item);

        closeSiblingFlyouts(item);
    }

    function toggleFlyout(item) {
        var isOpen = item.classList.contains('open');
        if (isOpen) {
            closeFlyoutBranch(item);
            maybeCloseMenusFromPointer();
            return;
        }
        openFlyout(item);
    }

    function onPageScrollClose(e) {
        if (!sidenav.querySelector('.sidenav-inner > .sidenav-item.open')) {
            return;
        }
        var target = e && e.target;
        if (target && (target === sidenav || sidenav.contains(target))) {
            return;
        }
        if (target && target.closest && target.closest('body > .sb-flyout-portal.sb-flyout-open')) {
            return;
        }
        closeAllMenus();
    }

    function bindPageScrollClose(el) {
        if (!el || el._mahaScrollCloseBound) return;
        el._mahaScrollCloseBound = true;
        el.addEventListener('scroll', onPageScrollClose, { passive: true });
    }

    function bindAllPageScrollClose() {
        document.querySelectorAll('.layout-content, .layout-wrapper, .layout-inner, .layout-container, main, .maha-dt-xscroll').forEach(bindPageScrollClose);
    }

    function fixHorizontalMenuOverflow() {
        sidenav.querySelectorAll('.sidenav-horizontal-wrapper, .sidenav-inner').forEach(function (el) {
            el.style.setProperty('overflow', 'visible', 'important');
        });
        var outerSidenav = sidenav.closest('.sidenav');
        if (outerSidenav) {
            outerSidenav.style.setProperty('overflow', 'visible', 'important');
        }
    }

    function patchThemeSidenavInstance() {
        fixHorizontalMenuOverflow();
        var inst = sidenav.sidenavInstance;
        if (!inst || inst._mahaTopMenuPatched) {
            return;
        }
        inst._mahaTopMenuPatched = true;
        if (typeof inst.closeAll === 'function') {
            inst.closeAll = closeAllMenus;
        }
        if (inst._wrapper) {
            inst._wrapper.style.setProperty('overflow', 'visible', 'important');
        }
    }

    closeAllTopMenus();
    fixHorizontalMenuOverflow();

    document.addEventListener('mousemove', function (e) {
        lastPointerX = e.clientX;
        lastPointerY = e.clientY;
    }, { passive: true });

    sidenav.querySelectorAll('.sb-flyout-submenu').forEach(function (item) {
        var toggle = item.querySelector(':scope > .sb-submenu-toggle');
        if (toggle) {
            toggle.addEventListener('mouseenter', function () {
                openFlyout(item);
            });
        }
        item.addEventListener('mouseenter', function () {
            openFlyout(item);
        });
        item.addEventListener('mouseleave', function (e) {
            if (isFlyoutRelated(item, e.relatedTarget)) return;
            maybeCloseMenusFromPointer();
        });
    });

    sidenav.querySelectorAll('.sb-flyout-panel').forEach(function (panel) {
        if (panel._sbPanelHoverBound) return;
        panel._sbPanelHoverBound = true;
        panel.addEventListener('mouseenter', function () {
            var owner = getFlyoutOwner(panel);
            if (!owner) return;
            openFlyout(owner);
        });
        panel.addEventListener('mouseleave', function (e) {
            var owner = getFlyoutOwner(panel);
            if (!owner) return;
            if (isFlyoutRelated(owner, e.relatedTarget)) return;
            maybeCloseMenusFromPointer();
        });
    });

    sidenav.querySelectorAll('.sidenav-inner > .sidenav-item > .sidenav-toggle').forEach(function (toggle) {
        var item = toggle.closest('.sidenav-inner > .sidenav-item');
        if (!item) return;

        toggle.addEventListener('mouseenter', function () {
            openTopMenu(item);
        });

        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (item.classList.contains('open')) {
                closeTopMenu(item);
            } else {
                openTopMenu(item);
            }
        }, true);
    });

    sidenav.querySelectorAll('.sidenav-inner > .sidenav-item > .sidenav-menu').forEach(function (menu) {
        menu.addEventListener('mouseenter', function () {
            var item = menu.closest('.sidenav-inner > .sidenav-item');
            if (item) openTopMenu(item);
        });
        menu.addEventListener('mouseleave', function (e) {
            if (e.relatedTarget && menu.contains(e.relatedTarget)) return;
            maybeCloseMenusFromPointer();
        });
    });

    sidenav.querySelectorAll('.sidenav-inner > .sidenav-item').forEach(function (item) {
        if (!item.querySelector(':scope > .sidenav-toggle')) return;

        item.addEventListener('mouseleave', function (e) {
            var menu = item.querySelector(':scope > .sidenav-menu');
            if (menu && e.relatedTarget && menu.contains(e.relatedTarget)) return;
            if (e.relatedTarget && item.contains(e.relatedTarget)) return;
            maybeCloseMenusFromPointer();
        });
    });

    sidenav.addEventListener('mouseleave', maybeCloseMenusFromPointer);

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
        var flyoutItem = toggle.closest('.sb-flyout-submenu');
        if (flyoutItem) toggleFlyout(flyoutItem);
    }, true);

    sidenav.addEventListener('click', function (e) {
        var link = e.target.closest('a.sidenav-link');
        if (isNavigableLink(link)) closeAllMenus();
    }, true);

    document.addEventListener('click', function (e) {
        var link = e.target.closest('.sb-flyout-portal a.sidenav-link, a.sidenav-link');
        if (isNavigableLink(link)) {
            closeAllMenus();
            return;
        }
        if (!isNavZone(e.target)) {
            closeAllMenus();
        }
    }, true);

    bindAllPageScrollClose();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindAllPageScrollClose);
    }
    window.addEventListener('load', bindAllPageScrollClose);

    document.addEventListener('wheel', function (e) {
        if (!sidenav.querySelector('.sidenav-inner > .sidenav-item.open')) {
            return;
        }
        if (e.target.closest('#layout-sidenav, body > .sb-flyout-portal.sb-flyout-open')) {
            var scroller = e.target.closest('.sidenav-menu.sb-dropdown-scrolling, .sb-flyout-panel, .sb-flyout-portal.sb-flyout-panel');
            if (scroller && scroller.scrollHeight > scroller.clientHeight + 1) {
                var delta = e.deltaY || 0;
                var atTop = scroller.scrollTop <= 0;
                var atBottom = scroller.scrollTop + scroller.clientHeight >= scroller.scrollHeight - 1;
                if ((delta < 0 && !atTop) || (delta > 0 && !atBottom)) {
                    return;
                }
            }
            return;
        }
        closeAllMenus();
    }, { passive: true });

    window.addEventListener('resize', closeAllMenus);

    window.layoutSidenav = window.layoutSidenav || {};
    window.layoutSidenav.closeAll = closeAllMenus;

    window.addEventListener('load', patchThemeSidenavInstance);
    if (document.readyState === 'complete') {
        patchThemeSidenavInstance();
    }
})();
</script>
<?php if ($mahaMenuVertical) { ?>
<script>
(function () {
    var root = document.getElementById('layout-sidenav');
    if (!root || !root.classList.contains('maha-vertical-sidenav')) return;

    var STATE_CLASSES = ['open', 'show', 'active', 'sb-menu-pinned', 'sb-flyout-pinned'];
    var lastPointerX = null;
    var lastPointerY = null;

    function stripState(el) {
        if (!el || !el.classList) return;
        STATE_CLASSES.forEach(function (cls) {
            el.classList.remove(cls);
        });
    }

    function isNavigableLink(link) {
        if (!link || !link.classList) return false;
        if (link.classList.contains('sb-submenu-toggle') || link.classList.contains('sidenav-toggle')) {
            return false;
        }
        var href = (link.getAttribute('href') || '').trim();
        return href && href !== '#' && href.indexOf('javascript:') !== 0;
    }

    function closeFlyoutBranch(item) {
        if (!item) return;
        item.querySelectorAll('.sb-flyout-submenu').forEach(function (nested) {
            stripState(nested);
        });
        stripState(item);
    }

    function closeSiblingFlyouts(item) {
        var parent = item && item.parentElement;
        if (!parent) return;
        parent.querySelectorAll(':scope > .sb-flyout-submenu').forEach(function (sibling) {
            if (sibling === item) return;
            closeFlyoutBranch(sibling);
        });
    }

    function closeOtherTopMenus(activeItem) {
        root.querySelectorAll('.sidenav-inner > .sidenav-item').forEach(function (item) {
            if (activeItem && item === activeItem) return;
            stripState(item);
            item.querySelectorAll('.sb-flyout-submenu').forEach(closeFlyoutBranch);
        });
    }

    function cleanupStaleState() {
        root.querySelectorAll('.sidenav-item, .sb-flyout-submenu').forEach(function (el) {
            el.classList.remove('sb-menu-pinned', 'sb-flyout-pinned', 'show');
        });
        document.querySelectorAll('body > .sb-flyout-portal').forEach(function (panel) {
            panel.remove();
        });
    }

    function closeAllMenus() {
        root.querySelectorAll('.sidenav-item, .sb-flyout-submenu').forEach(stripState);
        document.querySelectorAll('body > .sb-flyout-portal').forEach(function (panel) {
            panel.remove();
        });
    }

    function ensureAncestorMenusOpen(item) {
        var el = item ? item.parentElement : null;
        while (el && el !== root) {
            if (el.classList.contains('sidenav-item')) {
                el.classList.add('open');
            }
            el = el.parentElement;
        }
    }

    function maybeCloseMenusFromPointer() {
        window.requestAnimationFrame(function () {
            if (lastPointerX == null || lastPointerY == null) {
                closeAllMenus();
                return;
            }
            var hit = document.elementFromPoint(lastPointerX, lastPointerY);
            if (!hit || !root.contains(hit)) {
                closeAllMenus();
            }
        });
    }

    function isScrollInsideSidebar(target) {
        return target === root || !!(target && root.contains(target));
    }

    cleanupStaleState();

    document.addEventListener('mousemove', function (e) {
        lastPointerX = e.clientX;
        lastPointerY = e.clientY;
    }, { passive: true });

    root.addEventListener('mouseleave', maybeCloseMenusFromPointer);

    root.addEventListener('click', function (e) {
        var topToggle = e.target.closest('.sidenav-inner > .sidenav-item > .sidenav-toggle');
        if (topToggle && root.contains(topToggle)) {
            var topItem = topToggle.closest('.sidenav-inner > .sidenav-item');
            if (!topItem) return;
            e.preventDefault();
            e.stopPropagation();
            var isOpen = topItem.classList.contains('open');
            closeOtherTopMenus(topItem);
            if (isOpen) {
                stripState(topItem);
                topItem.querySelectorAll('.sb-flyout-submenu').forEach(closeFlyoutBranch);
            } else {
                topItem.classList.add('open');
            }
            return;
        }

        var toggle = e.target.closest('.sb-submenu-toggle');
        if (!toggle || !root.contains(toggle)) return;
        if (toggle.classList.contains('sidenav-toggle')) return;

        e.preventDefault();
        e.stopPropagation();

        var item = toggle.closest('.sb-flyout-submenu');
        if (!item) return;

        var isOpen = item.classList.contains('open');
        closeSiblingFlyouts(item);
        if (isOpen) {
            closeFlyoutBranch(item);
        } else {
            ensureAncestorMenusOpen(item);
            item.classList.add('open');
        }
    }, true);

    root.addEventListener('click', function (e) {
        var link = e.target.closest('a.sidenav-link');
        if (isNavigableLink(link)) closeAllMenus();
    }, true);

    document.addEventListener('click', function (e) {
        if (root.contains(e.target)) return;
        closeAllMenus();
    }, true);

    document.addEventListener('scroll', function (e) {
        if (isScrollInsideSidebar(e.target)) return;
        closeAllMenus();
    }, true);

    document.querySelectorAll('.layout-content, .layout-wrapper, .layout-inner, .layout-container, main, .maha-dt-xscroll').forEach(function (el) {
        el.addEventListener('scroll', closeAllMenus, { passive: true, capture: true });
    });

    window.addEventListener('resize', closeAllMenus);

    window.layoutSidenav = window.layoutSidenav || {};
    window.layoutSidenav.closeAll = closeAllMenus;
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

