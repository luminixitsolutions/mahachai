<?php
/**
 * Admin sidebar menu rendering helpers.
 * Expects globals: $Options, $Page, $MainPage, $Roll (optional).
 */

function sb_has_opt($ids)
{
    global $Options, $user_id;

    if (!function_exists('maha_user_has_full_menu_access')) {
        require_once dirname(__FILE__) . '/menu-hub-cards.php';
    }
    if (!empty($user_id) && maha_user_has_full_menu_access($user_id)) {
        return true;
    }

    if (!is_array($Options)) {
        return false;
    }
    if (!is_array($ids)) {
        $ids = array($ids);
    }
    $allowed = array();
    foreach ($Options as $opt) {
        $opt = trim((string) $opt);
        if ($opt !== '' && $opt !== '0') {
            $allowed[$opt] = true;
        }
    }
    if ($allowed === array()) {
        return false;
    }
    foreach ($ids as $id) {
        if (isset($allowed[(string) $id])) {
            return true;
        }
    }
    return false;
}

function sb_section_title_aliases()
{
    return array(
        'Account Approval' => 'Accounts Approval',
        'Approvals' => 'Approvals (Advance & Leave)',
    );
}

function sb_section_perm_ids($title)
{
    static $map = null;
    if ($map === null) {
        $map = array();
        if (!function_exists('maha_cp_menu_access_sections')) {
            require_once dirname(__FILE__) . '/admin-sidebar-menu-permissions-render.php';
        }
        if (!function_exists('maha_menu_hub_collect_section_ids')) {
            require_once dirname(__FILE__) . '/menu-hub-cards.php';
        }
        foreach (maha_cp_menu_access_sections() as $section) {
            $sectionTitle = isset($section['title']) ? $section['title'] : '';
            if ($sectionTitle !== '') {
                $map[$sectionTitle] = maha_menu_hub_collect_section_ids($section);
            }
        }
    }
    $aliases = sb_section_title_aliases();
    if (isset($aliases[$title], $map[$aliases[$title]])) {
        return $map[$aliases[$title]];
    }
    return isset($map[$title]) ? $map[$title] : array();
}

function sb_section_visible($title, $fallbackIds = null)
{
    $ids = $fallbackIds !== null ? (array) $fallbackIds : sb_section_perm_ids($title);
    if (empty($ids)) {
        return false;
    }
    return sb_has_opt($ids);
}

function sb_is_active_page($pageMatch)
{
    global $Page;

    if ($pageMatch === null) {
        return false;
    }
    if (is_callable($pageMatch)) {
        return (bool) $pageMatch();
    }
    if (is_array($pageMatch)) {
        foreach ($pageMatch as $match) {
            if (sb_is_active_page($match)) {
                return true;
            }
        }
        return false;
    }
    return isset($Page) && $Page == $pageMatch;
}

function sb_badge($pageMatch)
{
    if (!sb_is_active_page($pageMatch)) {
        return '';
    }
    return '<div class="pl-1 ml-auto"><span class="badge badge-dot badge-primary"></span></div>';
}

function sb_link($href, $label, $pageMatch = null)
{
    echo '<li class="sidenav-item">';
    echo '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" class="sidenav-link">';
    echo '<div>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</div>';
    echo sb_badge($pageMatch);
    echo '</a></li>';
}

function sb_group($title, $mainPage, $childrenHtml, $visible = true, $extraActiveCheck = null, $slug = null)
{
    if (!$visible) {
        return;
    }

    global $MainPage;

    $isOpen = isset($MainPage) && $MainPage == $mainPage;
    if (!$isOpen && is_callable($extraActiveCheck)) {
        $isOpen = (bool) $extraActiveCheck();
    }

    $openClass = $isOpen ? ' open active ' : ' ';
    if ($slug === null && function_exists('maha_menu_title_to_slug')) {
        $slug = maha_menu_title_to_slug($title);
    }
    $slugAttr = $slug ? ' data-menu-slug="' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . '"' : '';

    echo '<li class="sidenav-item' . $openClass . '"' . $slugAttr . '>';
    echo '<a href="javascript:" class="sidenav-link sidenav-toggle">';
    echo '<div>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</div>';
    echo '</a>';
    echo '<ul class="sidenav-menu">';
    echo $childrenHtml;
    echo '</ul></li>';
}

function sb_render_if_visible($permIds, $callback)
{
    if (sb_has_opt($permIds)) {
        if (is_callable($callback)) {
            $callback();
        }
    }
}

function sb_capture($callback)
{
    ob_start();
    if (is_callable($callback)) {
        $callback();
    }
    return ob_get_clean();
}

/**
 * Clickable submenu — opens flyout panel on the right side.
 */
function sb_submenu($title, $callback, $startOpen = false)
{
    $openClass = $startOpen ? ' open' : '';

    echo '<li class="sidenav-item sb-flyout-submenu' . $openClass . '">';
    echo '<a href="javascript:" class="sidenav-link sb-submenu-toggle">';
    echo '<div>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</div>';
    echo '<i class="sb-submenu-arrow ion ion-ios-arrow-forward ml-auto"></i>';
    echo '</a>';
    echo '<ul class="sidenav-menu sb-flyout-panel">';
    if (is_callable($callback)) {
        $callback();
    }
    echo '</ul></li>';
}

/**
 * Static section label (non-clickable) — prefer sb_submenu when items exist below.
 */
function sb_section($label)
{
    echo '<li class="sidenav-item sb-menu-section">';
    echo '<span class="sidenav-link py-1 d-block" style="cursor:default;opacity:0.7;font-size:11px;text-transform:uppercase;letter-spacing:0.04em;">';
    echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
    echo '</span></li>';
}

function sb_divider()
{
    echo '<li class="sidenav-divider my-1"></li>';
}

/**
 * Standard expense approval status links (Pending / Approved / Reject).
 */
function sb_expense_status_links($pendingHref, $approvedHref, $rejectedHref, $pendingPage, $approvedPage, $rejectedPage)
{
    sb_link($pendingHref, 'Pending', $pendingPage);
    sb_link($approvedHref, 'Approved', $approvedPage);
    sb_link($rejectedHref, 'Reject', $rejectedPage);
}

function sb_single_item($title, $mainPage, $href, $pageMatch = null, $visible = true, $extraActiveCheck = null, $itemClass = '', $slug = null)
{
    if (!$visible) {
        return;
    }

    global $MainPage;

    $isOpen = isset($MainPage) && $MainPage == $mainPage;
    if (!$isOpen && is_callable($extraActiveCheck)) {
        $isOpen = (bool) $extraActiveCheck();
    }

    $class = 'sidenav-item';
    if ($itemClass) {
        $class .= ' ' . trim($itemClass);
    } elseif ($isOpen) {
        $class .= ' active';
    }
    if ($slug === null && function_exists('maha_menu_title_to_slug')) {
        $slug = maha_menu_title_to_slug($title);
    }
    $slugAttr = $slug ? ' data-menu-slug="' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . '"' : '';

    echo '<li class="' . $class . '"' . $slugAttr . '>';
    echo '<a href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" class="sidenav-link">';
    echo '<div>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</div>';
    echo sb_badge($pageMatch);
    echo '</a></li>';
}
