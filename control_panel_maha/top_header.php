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

if (!function_exists('maha_cp_header_url')) {
    function maha_cp_header_url($path = '')
    {
        $path = ltrim((string) $path, '/');
        if (function_exists('maha_site_base_url')) {
            return maha_site_base_url() . $path;
        }
        $rel = defined('MAHA_CP_REL') ? MAHA_CP_REL : '';
        return $rel . $path;
    }
}
if (!function_exists('maha_cp_uploads_url')) {
    function maha_cp_uploads_url($filename = '')
    {
        $filename = ltrim((string) $filename, '/');
        if (function_exists('maha_site_base_url')) {
            $cpBase = rtrim(maha_site_base_url(), '/');
            $siteRoot = preg_replace('#/control_panel_maha[^/]*$#i', '', $cpBase);
            return $siteRoot . '/uploads/' . $filename;
        }
        $rel = defined('MAHA_CP_REL') ? MAHA_CP_REL : '';
        return $rel . '../uploads/' . $filename;
    }
}
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
.maha-header-notif {
    position: relative;
    z-index: 1101;
    flex-shrink: 0;
}
.maha-header-notif .nav-link {
    color: var(--maha-nav-text, #fff) !important;
    padding: 0.5rem 0.65rem !important;
    position: relative;
}
.maha-header-notif .maha-notif-badge {
    position: absolute;
    top: 2px;
    right: 2px;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 999px;
    background: #e74c3c;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    line-height: 18px;
    text-align: center;
    display: none;
}
.maha-header-notif .dropdown-menu {
    min-width: 260px;
    padding: 0;
    border: 0;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.18);
}
.maha-notif-menu-head {
    padding: 0.75rem 1rem;
    font-weight: 700;
    border-bottom: 1px solid rgba(24, 28, 33, 0.08);
}
.maha-notif-item {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.7rem 1rem;
    color: #364152;
    text-decoration: none !important;
    border-bottom: 1px solid rgba(24, 28, 33, 0.05);
}
.maha-notif-item:hover {
    background: rgba(15, 90, 74, 0.06);
    color: #0F5A4A;
}
.maha-notif-item i {
    font-size: 1.1rem;
    color: #0F5A4A;
    width: 1.25rem;
    text-align: center;
}
.maha-notif-item-body {
    flex: 1;
    min-width: 0;
}
.maha-notif-item-title {
    font-weight: 600;
    font-size: 0.88rem;
}
.maha-notif-item-sub {
    font-size: 0.75rem;
    color: #6c757d;
}
.maha-notif-count-pill {
    min-width: 22px;
    height: 22px;
    padding: 0 6px;
    border-radius: 999px;
    background: #e74c3c;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.maha-notif-empty {
    padding: 1rem;
    text-align: center;
    color: #6c757d;
    font-size: 0.85rem;
}
.maha-header-search {
    position: relative;
    max-width: 420px;
    min-width: 180px;
    flex: 1 1 auto;
}
.maha-header-search-input-wrap {
    position: relative;
}
.maha-header-search-input-wrap .form-control {
    border: 0;
    border-radius: 999px;
    padding: 0.45rem 0.85rem 0.45rem 2.25rem;
    font-size: 0.875rem;
    background: rgba(255, 255, 255, 0.14);
    color: var(--maha-nav-text, #fff);
    box-shadow: none;
}
.maha-header-search-input-wrap .form-control::placeholder {
    color: rgba(255, 255, 255, 0.65);
}
.maha-header-search-input-wrap .form-control:focus {
    background: rgba(255, 255, 255, 0.22);
    color: var(--maha-nav-text, #fff);
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.18);
}
.layout-navbar.bg-white .maha-header-search-input-wrap .form-control,
.layout-navbar.navbar-light .maha-header-search-input-wrap .form-control {
    background: #f1f4f2;
    color: #364152;
}
.layout-navbar.bg-white .maha-header-search-input-wrap .form-control::placeholder,
.layout-navbar.navbar-light .maha-header-search-input-wrap .form-control::placeholder {
    color: #8a94a6;
}
.layout-navbar.bg-white .maha-header-search-input-wrap .form-control:focus,
.layout-navbar.navbar-light .maha-header-search-input-wrap .form-control:focus {
    background: #eaeeec;
    box-shadow: 0 0 0 2px rgba(15, 90, 74, 0.15);
}
.maha-header-search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255, 255, 255, 0.75);
    font-size: 0.95rem;
    pointer-events: none;
}
.layout-navbar.bg-white .maha-header-search-icon,
.layout-navbar.navbar-light .maha-header-search-icon {
    color: #6c757d;
}
.maha-header-search-results {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    max-height: 320px;
    overflow-y: auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 32px rgba(15, 23, 42, 0.18);
    z-index: 1200;
    border: 1px solid rgba(24, 28, 33, 0.08);
}
.maha-header-search-results.is-open {
    display: block;
}
.maha-header-search-item {
    display: block;
    width: 100%;
    text-align: left;
    border: 0;
    background: transparent;
    padding: 0.65rem 0.85rem;
    color: #364152;
    text-decoration: none !important;
    border-bottom: 1px solid rgba(24, 28, 33, 0.05);
    cursor: pointer;
}
.maha-header-search-item:last-child {
    border-bottom: 0;
}
.maha-header-search-item:hover,
.maha-header-search-item.is-active {
    background: rgba(15, 90, 74, 0.07);
    color: #0F5A4A;
}
.maha-header-search-item-title {
    font-weight: 600;
    font-size: 0.88rem;
    line-height: 1.3;
}
.maha-header-search-item-sub {
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 0.1rem;
}
.maha-header-search-empty {
    padding: 0.85rem;
    text-align: center;
    color: #6c757d;
    font-size: 0.85rem;
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

require_once __DIR__ . '/menu-hub-loader.php';
$mahaMenuSearchIndex = maha_menu_hub_flatten_search_index($Options, $Roll, $user_id);
?>
<nav class="layout-navbar navbar navbar-expand-lg align-items-lg-center <?php echo (isset($uiPrefs['navbar_style']) && $uiPrefs['navbar_style'] === 'light') ? 'bg-white navbar-light' : 'bg-dark navbar-dark'; ?> container-p-x" id="layout-navbar">
<div class="layout-sidenav-toggle navbar-nav <?php echo $mahaNavToggleClass; ?> align-items-lg-center mr-auto">
<a class="nav-item nav-link px-0 mr-lg-4" href="javascript:">
<i class="ion ion-md-menu text-large align-middle"></i>
</a>
</div>
<a href="<?php echo maha_cp_header_url('dashboard.php'); ?>" class="navbar-brand app-brand demo d-lg-none py-0 mr-4">
<!--<span class="app-brand-logo demo">-->
<img src="<?php echo maha_cp_header_url('logo5.png'); ?>" alt="" class="img-fluid" style="width: 40px;">
<!--</span>-->
<span class="app-brand-text demo font-weight-normal ml-2" style="font-size: 22px;"><?php echo $Proj_Title; ?></span>
</a>


<div class="maha-header-search d-none d-lg-block mx-lg-3" id="maha-header-search-desktop">
<div class="maha-header-search-input-wrap">
<i class="feather icon-search maha-header-search-icon" aria-hidden="true"></i>
<input type="search" class="form-control" id="mahaMenuSearchInput" placeholder="Search menus..." autocomplete="off" aria-label="Search menus" aria-expanded="false" aria-controls="mahaMenuSearchResults">
<div class="maha-header-search-results" id="mahaMenuSearchResults" role="listbox"></div>
</div>
</div>

<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#layout-navbar-collapse">
<span class="navbar-toggler-icon"></span>
</button>
<div class="navbar-collapse collapse" id="layout-navbar-collapse">

<hr class="d-lg-none w-100 my-2">
<div class="maha-header-search d-lg-none mb-2 px-2 w-100" id="maha-header-search-mobile">
<div class="maha-header-search-input-wrap">
<i class="feather icon-search maha-header-search-icon" aria-hidden="true"></i>
<input type="search" class="form-control" id="mahaMenuSearchInputMobile" placeholder="Search menus..." autocomplete="off" aria-label="Search menus">
<div class="maha-header-search-results" id="mahaMenuSearchResultsMobile" role="listbox"></div>
</div>
</div>
<div class="navbar-nav align-items-lg-center ml-auto">

<?php
require_once __DIR__ . '/includes/header_notification_helpers.php';
$mahaHdrNotif = array('total' => 0, 'chat_count' => 0, 'ticket_count' => 0, 'ticket_notify_count' => 0, 'can_chat' => false);
$mahaHdrNotifTotal = 0;
$mahaHdrChatCount = 0;
$mahaHdrTicketCount = 0;
$mahaHdrCanChat = false;
try {
    if (isset($conn) && !empty($user_id)) {
        $mahaHdrNotif = maha_header_notifications($conn, (int) $user_id);
        $mahaHdrNotifTotal = (int) ($mahaHdrNotif['total'] ?? 0);
        $mahaHdrChatCount = (int) ($mahaHdrNotif['chat_count'] ?? 0);
        $mahaHdrTicketCount = max(
            (int) ($mahaHdrNotif['ticket_count'] ?? 0),
            (int) ($mahaHdrNotif['ticket_notify_count'] ?? 0)
        );
        $mahaHdrCanChat = !empty($mahaHdrNotif['can_chat']);
    }
} catch (Throwable $e) {
    // Keep header usable if notification queries fail
}
?>
<div class="maha-header-notif nav-item dropdown" id="maha-header-notif">
<a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" title="Notifications" aria-label="Notifications">
<i class="feather icon-bell" style="font-size:1.15rem;"></i>
<span class="maha-notif-badge" id="maha-notif-badge"<?php echo $mahaHdrNotifTotal > 0 ? '' : ' style="display:none;"'; ?>><?php echo $mahaHdrNotifTotal > 99 ? '99+' : $mahaHdrNotifTotal; ?></span>
</a>
<div class="dropdown-menu dropdown-menu-right">
<div class="maha-notif-menu-head">Notifications</div>
<?php if ($mahaHdrCanChat) { ?>
<a href="<?php echo maha_cp_header_url('chat/index.php'); ?>" class="maha-notif-item" id="maha-notif-chat-link">
<i class="ion ion-md-chatbubbles"></i>
<div class="maha-notif-item-body">
<div class="maha-notif-item-title">Chat</div>
<div class="maha-notif-item-sub">Unread messages</div>
</div>
<span class="maha-notif-count-pill" id="maha-notif-chat-count"<?php echo $mahaHdrChatCount > 0 ? '' : ' style="display:none;"'; ?>><?php echo $mahaHdrChatCount > 99 ? '99+' : $mahaHdrChatCount; ?></span>
</a>
<?php } ?>
<a href="<?php echo maha_cp_header_url('ticket_management/ticket-list.php?view=assigned'); ?>" class="maha-notif-item" id="maha-notif-ticket-link">
<i class="feather icon-tag"></i>
<div class="maha-notif-item-body">
<div class="maha-notif-item-title">Tickets</div>
<div class="maha-notif-item-sub">Assigned to you</div>
</div>
<span class="maha-notif-count-pill" id="maha-notif-ticket-count"<?php echo $mahaHdrTicketCount > 0 ? '' : ' style="display:none;"'; ?>><?php echo $mahaHdrTicketCount > 99 ? '99+' : $mahaHdrTicketCount; ?></span>
</a>
<button type="button" class="maha-notif-item" id="maha-enable-desktop-notifications" style="width:100%;border:0;background:#fff;text-align:left;cursor:pointer;">
<i class="feather icon-monitor"></i>
<div class="maha-notif-item-body">
<div class="maha-notif-item-title">Desktop alerts</div>
<div class="maha-notif-item-sub" id="maha-desktop-notification-status">Enable approval notifications</div>
</div>
</button>
<?php if ($mahaHdrNotifTotal < 1) { ?>
<div class="maha-notif-empty" id="maha-notif-empty">No new notifications</div>
<?php } else { ?>
<div class="maha-notif-empty d-none" id="maha-notif-empty">No new notifications</div>
<?php } ?>
</div>
</div>

<div class="nav-item d-none d-lg-block text-big font-weight-light line-height-1 opacity-25 mr-3 ml-1">|</div>
<div class="demo-navbar-user nav-item dropdown">
<a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" title="<?php echo htmlspecialchars($LoginUserName, ENT_QUOTES, 'UTF-8'); ?>">
<?php if ($LoginUserName !== '') { ?>
<span class="navbar-user-display-name d-none d-md-inline"><?php echo htmlspecialchars($LoginUserName, ENT_QUOTES, 'UTF-8'); ?></span>
<?php } ?>
    <?php if($row77['Photo']=='') {?>
<img src="<?php echo maha_cp_header_url('user_icon.jpg'); ?>" alt class="d-block ui-w-30 rounded-circle flex-shrink-0">
<?php } else{?>
    <img src="<?php echo maha_cp_uploads_url($row77['Photo']); ?>" alt class="d-block ui-w-30 rounded-circle flex-shrink-0" style="width: 30px;height: 30px;">
<?php } ?>
</a>
<div class="dropdown-menu dropdown-menu-right">
<a href="<?php echo maha_cp_header_url('my-profile.php'); ?>" class="dropdown-item">
<i class="feather icon-user text-muted"></i> &nbsp; My Profile</a>
<div class="dropdown-divider"></div>
<a href="<?php echo maha_cp_header_url('change-password.php'); ?>" class="dropdown-item">
<i class="feather icon-unlock text-muted"></i> &nbsp; Change Password</a>
<div class="dropdown-divider"></div>
<a href="<?php echo maha_cp_header_url('logout.php'); ?>" class="dropdown-item">
<i class="feather icon-power text-danger"></i> &nbsp; Log Out</a>
</div>
</div>
</div>
</div>
</nav>
<script>
(function () {
  var pollUrl = <?php echo json_encode(maha_cp_header_url('ajax_header_notifications.php')); ?>;
  var desktopPollUrl = <?php echo json_encode(maha_cp_header_url('ajax_approval_desktop_notifications.php')); ?>;
  var notificationIcon = <?php echo json_encode(maha_cp_header_url('logo5.png')); ?>;
  var pollMs = 30000;

  function setPill($el, count) {
    count = parseInt(count, 10) || 0;
    if (!$el.length) return count;
    if (count > 0) {
      $el.text(count > 99 ? '99+' : count).show();
    } else {
      $el.hide();
    }
    return count;
  }

  function refreshHeaderNotifications() {
    if (!window.jQuery) return;
    jQuery.getJSON(pollUrl).done(function (res) {
      if (!res || !res.success) return;
      var chat = setPill(jQuery('#maha-notif-chat-count'), res.chat_count || 0);
      var ticket = Math.max(parseInt(res.ticket_count, 10) || 0, parseInt(res.ticket_notify_count, 10) || 0);
      ticket = setPill(jQuery('#maha-notif-ticket-count'), ticket);
      var total = chat + ticket;
      setPill(jQuery('#maha-notif-badge'), total);
      var $empty = jQuery('#maha-notif-empty');
      if ($empty.length) {
        if (total > 0) {
          $empty.addClass('d-none');
        } else {
          $empty.removeClass('d-none');
        }
      }
    });
  }

  function updateDesktopPermissionUi() {
    var $button = jQuery('#maha-enable-desktop-notifications');
    var $status = jQuery('#maha-desktop-notification-status');
    if (!('Notification' in window)) {
      $status.text('Not supported by this browser');
      $button.prop('disabled', true).css('opacity', '.6');
      return;
    }
    if (Notification.permission === 'granted') {
      $status.text('Enabled');
      $button.hide();
    } else if (Notification.permission === 'denied') {
      $status.text('Blocked — allow in browser settings');
      $button.show().prop('disabled', true).css('opacity', '.65');
    } else {
      $status.text('Click to enable approval notifications');
      $button.show().prop('disabled', false).css('opacity', '1');
    }
  }

  function acknowledgeDesktopNotifications(ids) {
    if (!ids.length) return;
    jQuery.post(desktopPollUrl, { action: 'ack', ids: ids });
  }

  function refreshDesktopNotifications() {
    if (!('Notification' in window) || Notification.permission !== 'granted') return;
    jQuery.getJSON(desktopPollUrl, { action: 'list' }).done(function (res) {
      if (!res || !res.success || !Array.isArray(res.notifications)) return;
      var displayedIds = [];
      res.notifications.forEach(function (item) {
        try {
          var notification = new Notification(item.title || 'Maha Chai Approval', {
            body: item.message || '',
            icon: notificationIcon,
            badge: notificationIcon,
            tag: 'maha-approval-' + item.id,
            requireInteraction: true
          });
          notification.onclick = function () {
            window.focus();
            if (item.view_url) {
              window.location.href = item.view_url;
            }
            notification.close();
          };
          displayedIds.push(item.id);
        } catch (e) {
          // Keep the record pending so a later poll can retry.
        }
      });
      acknowledgeDesktopNotifications(displayedIds);
    });
  }

  if (window.jQuery) {
    jQuery(function () {
      refreshHeaderNotifications();
      updateDesktopPermissionUi();
      refreshDesktopNotifications();
      setInterval(refreshHeaderNotifications, pollMs);
      setInterval(refreshDesktopNotifications, pollMs);

      jQuery('#maha-enable-desktop-notifications').on('click', function () {
        if (!('Notification' in window) || Notification.permission === 'denied') return;
        Notification.requestPermission().then(function () {
          updateDesktopPermissionUi();
          refreshDesktopNotifications();
        });
      });
    });
  }
})();
</script>
<script>
(function () {
  var menuIndex = <?php echo json_encode($mahaMenuSearchIndex, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE); ?>;
  var menuBase = <?php echo json_encode(maha_cp_header_url('')); ?>;
  var maxResults = 15;

  function initSearch(inputId, resultsId) {
    var input = document.getElementById(inputId);
    var resultsEl = document.getElementById(resultsId);
    if (!input || !resultsEl) return;

    var activeIdx = -1;
    var visibleItems = [];

    function closeResults() {
      resultsEl.classList.remove('is-open');
      resultsEl.innerHTML = '';
      input.setAttribute('aria-expanded', 'false');
      activeIdx = -1;
      visibleItems = [];
    }

    function goTo(href) {
      if (!href) return;
      if (/^https?:\/\//i.test(href) || href.charAt(0) === '/') {
        window.location.href = href;
        return;
      }
      window.location.href = menuBase + href.replace(/^\.\//, '');
    }

    function setActive(idx) {
      var buttons = resultsEl.querySelectorAll('.maha-header-search-item');
      buttons.forEach(function (btn, i) {
        btn.classList.toggle('is-active', i === idx);
      });
      activeIdx = idx;
      if (idx >= 0 && buttons[idx]) {
        buttons[idx].scrollIntoView({ block: 'nearest' });
      }
    }

    function renderResults(query) {
      query = (query || '').toLowerCase().replace(/\s+/g, ' ').trim();
      if (!query) {
        closeResults();
        return;
      }

      visibleItems = [];
      for (var i = 0; i < menuIndex.length; i++) {
        var item = menuIndex[i];
        if ((item.blob || '').indexOf(query) !== -1) {
          visibleItems.push(item);
          if (visibleItems.length >= maxResults) break;
        }
      }

      resultsEl.innerHTML = '';
      if (visibleItems.length === 0) {
        resultsEl.innerHTML = '<div class="maha-header-search-empty">No matching menus found</div>';
      } else {
        visibleItems.forEach(function (item, idx) {
          var btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'maha-header-search-item';
          btn.setAttribute('role', 'option');
          btn.dataset.href = item.href;
          btn.innerHTML =
            '<div class="maha-header-search-item-title"></div>' +
            '<div class="maha-header-search-item-sub"></div>';
          btn.querySelector('.maha-header-search-item-title').textContent = item.label;
          btn.querySelector('.maha-header-search-item-sub').textContent =
            item.module + (item.group ? ' · ' + item.group : '');
          btn.addEventListener('mousedown', function (e) {
            e.preventDefault();
            goTo(item.href);
          });
          btn.addEventListener('mouseenter', function () {
            setActive(idx);
          });
          resultsEl.appendChild(btn);
        });
      }

      resultsEl.classList.add('is-open');
      input.setAttribute('aria-expanded', 'true');
      activeIdx = visibleItems.length ? 0 : -1;
      setActive(activeIdx);
    }

    input.addEventListener('input', function () {
      renderResults(input.value);
    });

    input.addEventListener('keydown', function (e) {
      if (!resultsEl.classList.contains('is-open') || !visibleItems.length) {
        if (e.key === 'Enter') {
          e.preventDefault();
        }
        return;
      }

      if (e.key === 'ArrowDown') {
        e.preventDefault();
        setActive(Math.min(activeIdx + 1, visibleItems.length - 1));
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        setActive(Math.max(activeIdx - 1, 0));
      } else if (e.key === 'Enter') {
        e.preventDefault();
        if (activeIdx >= 0 && visibleItems[activeIdx]) {
          goTo(visibleItems[activeIdx].href);
        }
      } else if (e.key === 'Escape') {
        closeResults();
      }
    });

    input.addEventListener('focus', function () {
      if ((input.value || '').trim()) {
        renderResults(input.value);
      }
    });

    document.addEventListener('click', function (e) {
      if (!input.contains(e.target) && !resultsEl.contains(e.target)) {
        closeResults();
      }
    });
  }

  initSearch('mahaMenuSearchInput', 'mahaMenuSearchResults');
  initSearch('mahaMenuSearchInputMobile', 'mahaMenuSearchResultsMobile');
})();
</script>