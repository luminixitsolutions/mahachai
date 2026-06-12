<?php include 'loader.php';

$appName = 'control_panel_maha';
if (!empty($user_id) && isset($conn)) {
    $uid = (int) $user_id;
    $conn->query("DELETE FROM tbl_active_admin WHERE app_name='$appName' AND session_id='$uid'");
    $conn->query("INSERT INTO tbl_active_admin (app_name, session_id, last_login)
                  VALUES ('$appName', '$uid', NOW())");
}
?>

<!-- YOUR PAGE HTML / PHP STARTS BELOW -->

<div class="sidenav bg-dark">
<div id="layout-sidenav" class=" <!--container--> layout-sidenav-horizontal sidenav-horizontal flex-grow-0 bg-dark" style="padding-left:15px;padding-right:15px;">
     <div class="app-brand demo">
                    <span class="app-brand-logo demo">
                        <a href="dashboard.php"><img src="logo5.png" alt="Brand Logo" class="img-fluid" style="width: 60px;"></a> 
                    </span>
                    <h3 style="font-size:18px; padding-left:10px;padding-top:10px;">Maha Chai</h3>
                   <!-- <a href="dashboard.php" class="app-brand-text demo sidenav-text font-weight-normal ml-2"><?php echo $Proj_Title; ?></a>-->
                    <a href="javascript:" class="layout-sidenav-toggle sidenav-link text-large ml-auto">
                        <i class="ion ion-md-menu align-middle"></i>
                    </a>
                </div>
                <div class="sidenav-divider mt-0"></div>
    <ul class="sidenav-inner">
<?php
require_once 'admin-sidebar-menu-helpers.php';
include 'admin-sidebar-menu-organized.php';
?>

    </ul>
</div>
</div>
<style>
/* ── Horizontal nav layout: brand | prev | scroll area | next ── */
#layout-sidenav.sidenav-horizontal {
    display: flex !important;
    flex-wrap: nowrap !important;
    align-items: stretch !important;
    overflow: visible !important;
}
#layout-sidenav.sidenav-horizontal > .app-brand {
    flex: 0 0 auto;
}
.sidenav.bg-dark {
    overflow: visible !important;
}

/* Sticky scroll arrows — fixed width, never overlap menu text */
.sidenav-horizontal .sidenav-horizontal-prev,
.sidenav-horizontal .sidenav-horizontal-next {
    flex: 0 0 36px !important;
    width: 36px !important;
    min-width: 36px !important;
    max-width: 36px !important;
    position: relative;
    z-index: 30;
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

/* Menu scroll zone — clip horizontal bleed only */
.sidenav-horizontal .sidenav-horizontal-wrapper {
    overflow-x: hidden !important;
    overflow-y: visible !important;
    flex: 1 1 0 !important;
    width: auto !important;
    min-width: 0 !important;
    position: relative;
}
.sidenav-horizontal .sidenav-inner {
    overflow: visible !important;
}

/* ── Top-level dropdown (opens below nav bar) ── */
.sidenav-horizontal .sidenav-inner > .sidenav-item {
    position: relative;
}
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu {
    display: none;
    flex-direction: column;
    position: fixed !important;
    top: auto !important;
    left: auto !important;
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
.sidenav-horizontal .sidenav-inner > .sidenav-item > .sidenav-menu.sb-dropdown-scrolling {
    overflow-y: auto !important;
    overflow-x: visible !important;
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
    max-height: 75vh;
    overflow-y: auto;
    overflow-x: visible;
    margin: 0 !important;
    padding: 0.35rem 0 !important;
    background: #0F5A4A !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.22) !important;
    border-radius: 0 0 4px 4px;
    z-index: 1060 !important;
    list-style: none;
    flex-direction: column !important;
    display: none !important;
}

/* Show flyout on hover, click, or when pinned */
.sidenav-horizontal .sb-flyout-submenu:hover > .sidenav-menu.sb-flyout-panel,
.sidenav-horizontal .sb-flyout-submenu.open > .sidenav-menu.sb-flyout-panel,
.sidenav-horizontal .sb-flyout-submenu.sb-flyout-pinned > .sidenav-menu.sb-flyout-panel {
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

/* Top-level flyouts portaled to body — always on top, never clipped */
.sb-flyout-portal.sb-flyout-panel {
    position: fixed !important;
    display: flex !important;
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
    border-radius: 0 0 4px 4px;
    z-index: 1080 !important;
    list-style: none;
    box-sizing: border-box;
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

/* Auto-flip: open on LEFT when not enough space on right (e.g. Reports menu) */
.sidenav-horizontal .sb-flyout-submenu.sb-flyout-left:not(.sb-flyout-fixed) > .sidenav-menu.sb-flyout-panel {
    left: auto !important;
    right: 100% !important;
}
.sidenav-horizontal .sb-flyout-submenu.sb-flyout-left > .sb-submenu-toggle .sb-submenu-arrow {
    transform: scaleX(-1);
}

/* Never show flyout inline when parent is closed (unless pinned) */
.sidenav-horizontal .sb-flyout-submenu:not(:hover):not(.open):not(.sb-flyout-pinned) > .sidenav-menu.sb-flyout-panel {
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
</style>
<script>
(function () {
    var sidenav = document.getElementById('layout-sidenav');
    if (!sidenav) return;

    function getPanel(item) {
        return item._sbFlyoutPanel || item.querySelector(':scope > .sb-flyout-panel');
    }

    function getTopMenuItem(el) {
        return el ? el.closest('.sidenav-inner > .sidenav-item') : null;
    }

    function closeAllExceptTopMenu(activeTopItem) {
        sidenav.querySelectorAll('.sb-flyout-submenu.open, .sb-flyout-submenu.sb-flyout-pinned').forEach(function (flyout) {
            var topItem = getTopMenuItem(flyout);
            if (activeTopItem && topItem === activeTopItem) return;
            flyout.classList.remove('open', 'sb-flyout-pinned');
            restoreFlyoutPortal(flyout);
            resetFlyoutSide(flyout);
        });

        document.querySelectorAll('body > .sb-flyout-portal').forEach(function (panel) {
            var owner = panel._sbFlyoutOwner;
            if (!owner || getTopMenuItem(owner) !== activeTopItem) {
                if (owner) {
                    owner.classList.remove('open', 'sb-flyout-pinned');
                    owner._sbFlyoutPanel = panel;
                    restoreFlyoutPortal(owner);
                } else {
                    panel.classList.remove('sb-flyout-portal');
                    panel.remove();
                }
            }
        });

        sidenav.querySelectorAll('.sidenav-inner > .sidenav-item').forEach(function (el) {
            if (activeTopItem && el === activeTopItem) return;
            el.classList.remove('open', 'sb-menu-pinned');
            resetTopMenu(el);
        });
    }

    function isPinned(item) {
        return item && item.classList.contains('sb-flyout-pinned');
    }

    function hasPinnedFlyout() {
        return !!document.querySelector('.sb-flyout-submenu.sb-flyout-pinned, .sidenav-inner > .sidenav-item.sb-menu-pinned');
    }

    function isFlyoutInteractionActive() {
        return hasPinnedFlyout() || !!document.querySelector('.sb-flyout-submenu.open, .sb-flyout-portal:hover');
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
        (root || sidenav).querySelectorAll('.sb-flyout-submenu.open, .sb-flyout-submenu.sb-flyout-pinned').forEach(function (el) {
            el.classList.remove('open');
            el.classList.remove('sb-flyout-pinned');
            restoreFlyoutPortal(el);
            resetFlyoutSide(el);
        });
        document.querySelectorAll('body > .sb-flyout-portal').forEach(function (panel) {
            var owner = panel._sbFlyoutOwner;
            if (owner) {
                restoreFlyoutPortal(owner);
            } else {
                panel.remove();
            }
        });
        sidenav.querySelectorAll('.sidenav-inner > .sidenav-item.sb-menu-pinned, .sidenav-inner > .sidenav-item.open').forEach(function (el) {
            el.classList.remove('sb-menu-pinned');
            if (!el.matches(':hover')) {
                el.classList.remove('open');
                resetTopMenu(el);
            }
        });
    }

    function positionTopMenu(item) {
        var menu = item.querySelector(':scope > .sidenav-menu');
        var toggle = item.querySelector(':scope > .sidenav-toggle');
        if (!menu || !toggle) return;

        var rect = toggle.getBoundingClientRect();
        var viewportPad = 8;
        var availableHeight = window.innerHeight - rect.bottom - viewportPad;

        menu.style.top = rect.bottom + 'px';
        menu.style.left = rect.left + 'px';
        menu.style.right = 'auto';
        menu.style.minWidth = Math.max(rect.width, 240) + 'px';
        menu.style.maxHeight = Math.max(180, availableHeight) + 'px';

        requestAnimationFrame(function () {
            var menuRect = menu.getBoundingClientRect();
            if (menuRect.right > window.innerWidth - viewportPad) {
                menu.style.left = Math.max(viewportPad, window.innerWidth - menuRect.width - viewportPad) + 'px';
            }
            if (menu.scrollHeight > menu.clientHeight + 1) {
                menu.classList.add('sb-dropdown-scrolling');
            } else {
                menu.classList.remove('sb-dropdown-scrolling');
            }
        });
    }

    function resetTopMenu(item) {
        var menu = item.querySelector(':scope > .sidenav-menu');
        if (!menu) return;
        menu.style.top = '';
        menu.style.left = '';
        menu.style.right = '';
        menu.style.minWidth = '';
        menu.style.maxHeight = '';
        menu.classList.remove('sb-dropdown-scrolling');
    }

    function needsFixedFlyout(item) {
        var menu = item.parentElement;
        return menu
            && menu.classList.contains('sidenav-menu')
            && !menu.classList.contains('sb-flyout-panel')
            && menu.parentElement
            && menu.parentElement.closest('.sidenav-inner > .sidenav-item');
    }

    function isNestedInPortal(item) {
        return !!item.closest('.sb-flyout-portal');
    }

    function portalFlyout(item) {
        if (!needsFixedFlyout(item)) return;

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
            panel.addEventListener('mouseleave', function () {
                if (isPinned(item)) return;
                window.setTimeout(function () {
                    if (isPinned(item)) return;
                    if (!item.matches(':hover') && !panel.matches(':hover')) {
                        item.classList.remove('open');
                        restoreFlyoutPortal(item);
                        resetFlyoutSide(item);
                    }
                }, 120);
            });
        }
    }

    function restoreFlyoutPortal(item) {
        var panel = item._sbFlyoutPanel || item.querySelector(':scope > .sb-flyout-panel');
        if (!panel || !panel.classList.contains('sb-flyout-portal')) return;

        panel.classList.remove('sb-flyout-portal');
        panel.classList.remove('sb-flyout-nested-fixed');
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

        item.classList.remove('sb-flyout-left');

        panel.style.visibility = 'hidden';
        panel.style.display = 'flex';

        var toggleRect = toggle.getBoundingClientRect();
        var panelWidth = panel.offsetWidth || 280;
        var gap = 2;
        var viewportPad = 8;
        var overflowRight = toggleRect.right + gap + panelWidth > window.innerWidth - viewportPad;
        var overflowLeft = toggleRect.left - gap - panelWidth < viewportPad;

        if (overflowRight && !overflowLeft) {
            item.classList.add('sb-flyout-left');
        } else if (overflowRight && overflowLeft) {
            item.classList.add('sb-flyout-left');
        }

        if (needsFixedFlyout(item) || panel.classList.contains('sb-flyout-portal')) {
            var top = toggleRect.top;
            var left = item.classList.contains('sb-flyout-left')
                ? toggleRect.left - gap - panelWidth
                : toggleRect.right + gap;
            left = Math.max(viewportPad, Math.min(left, window.innerWidth - panelWidth - viewportPad));
            panel.style.position = 'fixed';
            panel.style.top = top + 'px';
            panel.style.left = left + 'px';
            panel.style.right = 'auto';
            panel.style.maxHeight = Math.max(120, window.innerHeight - top - viewportPad) + 'px';
            panel.classList.remove('sb-flyout-nested-fixed');
        } else if (isNestedInPortal(item)) {
            var nestedTop = toggleRect.top;
            var nestedLeft = item.classList.contains('sb-flyout-left')
                ? toggleRect.left - gap - panelWidth
                : toggleRect.right + gap;
            nestedLeft = Math.max(viewportPad, Math.min(nestedLeft, window.innerWidth - panelWidth - viewportPad));
            panel.classList.add('sb-flyout-nested-fixed');
            panel.style.position = 'fixed';
            panel.style.top = nestedTop + 'px';
            panel.style.left = nestedLeft + 'px';
            panel.style.right = 'auto';
            panel.style.maxHeight = Math.max(120, window.innerHeight - nestedTop - viewportPad) + 'px';
        } else {
            panel.classList.remove('sb-flyout-nested-fixed');
            panel.style.position = '';
            panel.style.top = '';
            panel.style.left = '';
            panel.style.right = '';
            panel.style.maxHeight = '';
        }

        panel.style.display = 'flex';
        panel.style.visibility = 'visible';
    }

    function resetFlyoutSide(item) {
        item.classList.remove('sb-flyout-left');
        var panel = getPanel(item);
        if (!panel || panel.classList.contains('sb-flyout-portal')) return;
        panel.classList.remove('sb-flyout-nested-fixed');
        panel.style.position = '';
        panel.style.top = '';
        panel.style.left = '';
        panel.style.right = '';
        panel.style.maxHeight = '';
    }

    function openFlyout(item, shouldPin) {
        var activeTop = getTopMenuItem(item);
        sidenav.querySelectorAll('.sb-flyout-submenu.open, .sb-flyout-submenu.sb-flyout-pinned').forEach(function (el) {
            if (getTopMenuItem(el) === activeTop) return;
            el.classList.remove('open', 'sb-flyout-pinned');
            restoreFlyoutPortal(el);
            resetFlyoutSide(el);
        });

        var parent = item.parentElement;
        if (parent && parent.querySelectorAll) {
            parent.querySelectorAll(':scope > .sb-flyout-submenu.open, :scope > .sb-flyout-submenu.sb-flyout-pinned').forEach(function (el) {
                if (el === item) return;
                if (isPinned(el) && !shouldPin) return;
                el.classList.remove('open');
                el.classList.remove('sb-flyout-pinned');
                restoreFlyoutPortal(el);
                resetFlyoutSide(el);
            });
        }
        item.classList.add('open');
        if (shouldPin) {
            pinFlyout(item);
        }
        requestAnimationFrame(function () {
            adjustFlyoutSide(item);
        });
    }

    function toggleFlyout(item) {
        if (item.classList.contains('open') && isPinned(item)) {
            item.classList.remove('open');
            unpinFlyout(item);
            restoreFlyoutPortal(item);
            resetFlyoutSide(item);
            return;
        }
        if (item.classList.contains('open')) {
            pinFlyout(item);
            requestAnimationFrame(function () {
                adjustFlyoutSide(item);
            });
            return;
        }
        openFlyout(item, true);
    }

    sidenav.querySelectorAll('.sb-flyout-submenu').forEach(function (item) {
        item.addEventListener('mouseenter', function () {
            openFlyout(item, false);
        });
        item.addEventListener('mouseleave', function () {
            if (isPinned(item)) return;
            window.setTimeout(function () {
                if (isPinned(item)) return;
                var panel = getPanel(item);
                if (!item.matches(':hover') && !(panel && panel.matches(':hover'))) {
                    item.classList.remove('open');
                    restoreFlyoutPortal(item);
                    resetFlyoutSide(item);
                }
            }, 120);
        });
    });

    sidenav.querySelectorAll('.sidenav-inner > .sidenav-item > .sidenav-menu').forEach(function (menu) {
        menu.addEventListener('scroll', function () {
            menu.querySelectorAll('.sb-flyout-submenu.open').forEach(adjustFlyoutSide);
        });
    });

    sidenav.querySelectorAll('.sidenav-inner > .sidenav-item').forEach(function (item) {
        if (!item.querySelector(':scope > .sidenav-toggle')) return;

        item.addEventListener('mouseenter', function () {
            closeAllExceptTopMenu(item);
            item.classList.add('open');
            requestAnimationFrame(function () {
                positionTopMenu(item);
            });
        });

        var topToggle = item.querySelector(':scope > .sidenav-toggle');
        if (topToggle) {
            topToggle.addEventListener('click', function () {
                closeAllExceptTopMenu(item);
            }, true);
        }
    });

    sidenav.addEventListener('mouseleave', function () {
        window.setTimeout(function () {
            if (hasPinnedFlyout()) return;
            if (document.querySelector('.sb-flyout-portal:hover, .sb-flyout-submenu.open:hover')) return;
            sidenav.querySelectorAll('.sidenav-inner > .sidenav-item.open:not(.sb-menu-pinned)').forEach(function (el) {
                el.classList.remove('open');
                resetTopMenu(el);
            });
            sidenav.querySelectorAll('.sb-flyout-submenu.open:not(.sb-flyout-pinned)').forEach(function (el) {
                el.classList.remove('open');
                restoreFlyoutPortal(el);
                resetFlyoutSide(el);
            });
        }, 120);
    });

    window.addEventListener('resize', function () {
        sidenav.querySelectorAll('.sidenav-inner > .sidenav-item.open').forEach(positionTopMenu);
        sidenav.querySelectorAll('.sb-flyout-submenu.open').forEach(adjustFlyoutSide);
    });

    sidenav.addEventListener('click', function (e) {
        if (e.target.closest('.sidenav-horizontal-prev, .sidenav-horizontal-next')) {
            requestAnimationFrame(function () {
                sidenav.querySelectorAll('.sidenav-inner > .sidenav-item.open').forEach(positionTopMenu);
            });
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

    document.addEventListener('click', function (e) {
        if (e.target.closest('.sb-flyout-submenu, .sb-flyout-portal, .sidenav-inner > .sidenav-item > .sidenav-menu')) return;
        closeFlyouts();
    });

    window.addEventListener('load', function () {
        sidenav.addEventListener('click', function (e) {
            if (e.target.closest('.sb-submenu-toggle, .sb-flyout-portal')) {
                e.stopImmediatePropagation();
            }
        }, true);

        if (window.layoutSidenav && typeof window.layoutSidenav.closeAll === 'function') {
            var nativeCloseAll = window.layoutSidenav.closeAll.bind(window.layoutSidenav);
            window.layoutSidenav.closeAll = function () {
                if (hasPinnedFlyout()) return;
                if (document.querySelector('.sb-flyout-portal:hover, .sb-flyout-submenu.open')) return;
                return nativeCloseAll();
            };
        }
    });
})();
</script>
<script>
    setInterval(function() {
        console.log('ddd');
  fetch('ping.php'); // this can be a blank PHP file that only starts the session
}, 10000);
</script>

