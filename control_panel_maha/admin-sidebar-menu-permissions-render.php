<?php
/**
 * Render Control Panel Menu Access checkboxes (add-employee.php) aligned with sidebar sections.
 * Accordion layout: Module / Screen | View (plus/minus expand).
 */

function emp_cp_menu_access_allowed_ids()
{
    static $ids = null;
    if ($ids !== null) {
        return $ids;
    }
    $sections = include __DIR__ . '/admin-sidebar-menu-permissions-config.php';
    $ids = array_values(array_unique(array_merge(
        emp_cp_menu_access_collect_ids($sections),
        emp_cp_menu_access_legacy_ids()
    )));
    return $ids;
}

function emp_user_can_save_menu_access($userId)
{
    return in_array((int) $userId, array(2651, 2650), true);
}

function emp_build_options2_from_post(array $post)
{
    $hasCsvKey = array_key_exists('Options2_csv', $post);
    $hasMenuFlag = !empty($post['cp_menu_access_present']);

    if (!$hasCsvKey && !$hasMenuFlag) {
        return null;
    }

    $allowed = array_flip(emp_cp_menu_access_allowed_ids());
    $optIds = array();

    if ($hasCsvKey) {
        $rawCsv = trim((string) $post['Options2_csv']);
        if ($rawCsv !== '') {
            foreach (explode(',', $rawCsv) as $value) {
                $id = (int) trim($value);
                if ($id > 0 && isset($allowed[$id])) {
                    $optIds[$id] = $id;
                }
            }
        }
        if (empty($optIds)) {
            return '0';
        }
        ksort($optIds, SORT_NUMERIC);
        return implode(',', $optIds);
    }

    if (!empty($post['Options']) && is_array($post['Options'])) {
        foreach ($post['Options'] as $value) {
            $id = (int) $value;
            if ($id > 0 && isset($allowed[$id])) {
                $optIds[$id] = $id;
            }
        }
    }

    if (empty($optIds)) {
        return '0';
    }
    ksort($optIds, SORT_NUMERIC);
    return implode(',', $optIds);
}

/**
 * Resolve Options2 for INSERT only. Returns null on edit when menu data was not posted (preserve existing).
 */
function emp_resolve_options2_for_insert(array $post)
{
    $fromPost = emp_build_options2_from_post($post);
    if ($fromPost !== null) {
        return $fromPost;
    }
    return '0';
}

function emp_persist_user_options2($conn, $employeeId, $options2Value)
{
    return emp_persist_user_menu_access($conn, $employeeId, $options2Value);
}

function emp_persist_user_menu_access($conn, $employeeId, $options2Value)
{
    if (function_exists('maha_ensure_users_options2_column')) {
        maha_ensure_users_options2_column();
    }
    $employeeId = (int) $employeeId;
    if ($employeeId <= 0 || !$conn) {
        return false;
    }

    $stmt = $conn->prepare('UPDATE tbl_users SET Options2 = ?, `Options` = ? WHERE id = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('ssi', $options2Value, $options2Value, $employeeId);
        $ok = $stmt->execute();
        $stmt->close();
        if ($ok) {
            return true;
        }
    }

    $stmt2 = $conn->prepare('UPDATE tbl_users SET Options2 = ? WHERE id = ? LIMIT 1');
    if (!$stmt2) {
        return false;
    }
    $stmt2->bind_param('si', $options2Value, $employeeId);
    $ok2 = $stmt2->execute();
    $stmt2->close();
    return $ok2;
}

function emp_cp_menu_access_legacy_ids()
{
    return array(
        1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
        21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
        41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60,
        61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80,
        81, 82, 83, 84, 85, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101,
        102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121,
        122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133, 135, 136, 137, 138, 139, 140, 141, 142,
        143, 144, 145, 146, 147, 148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161, 163,
        164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174,
    );
}

function emp_cp_menu_access_collect_ids(array $sections)
{
    $ids = array();
    foreach ($sections as $section) {
        if (!empty($section['ids'])) {
            $ids = array_merge($ids, $section['ids']);
        }
        if (!empty($section['groups'])) {
            foreach ($section['groups'] as $group) {
                if (!empty($group['ids'])) {
                    $ids = array_merge($ids, $group['ids']);
                }
            }
        }
    }
    return array_values(array_unique(array_map('intval', $ids)));
}

function emp_cp_fetch_options(array $ids)
{
    $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
    if (empty($ids)) {
        return array();
    }
    $idList = implode(',', $ids);
    return getList("SELECT * FROM tbl_option_cp WHERE id IN ($idList) ORDER BY FIELD(id, $idList)");
}

function emp_cp_option_checked_attr($id, array $selectedOptions)
{
    return in_array((string) $id, $selectedOptions, true) ? ' checked="checked"' : '';
}

function emp_cp_render_select_all($ids, array $selectedOptions)
{
    $ids = array_map('strval', $ids);
    $checkedCount = 0;
    foreach ($ids as $id) {
        if (in_array($id, $selectedOptions, true)) {
            $checkedCount++;
        }
    }
    $checked = ($checkedCount === count($ids) && count($ids) > 0) ? ' checked="checked"' : '';
    $indeterminate = ($checkedCount > 0 && $checkedCount < count($ids)) ? ' data-indeterminate="1"' : '';
    echo '<label class="emp-cp-ma-check custom-control custom-checkbox mb-0" onclick="event.stopPropagation();">';
    echo '<input type="checkbox" class="custom-control-input emp-cp-ma-select-all"' . $checked . $indeterminate . '>';
    echo '<span class="custom-control-label"></span>';
    echo '</label>';
}

function emp_cp_render_option_row(array $result, array $selectedOptions)
{
    $checked = emp_cp_option_checked_attr($result['id'], $selectedOptions);
    echo '<div class="emp-cp-ma-row">';
    echo '<span class="emp-cp-ma-row-label">' . htmlspecialchars($result['Name'], ENT_QUOTES, 'UTF-8') . '</span>';
    echo '<label class="emp-cp-ma-check custom-control custom-checkbox mb-0">';
    echo '<input type="checkbox" class="custom-control-input emp-cp-ma-option" name="Options[]" value="' . (int) $result['id'] . '"' . $checked . '>';
    echo '<span class="custom-control-label"></span>';
    echo '</label>';
    echo '</div>';
}

function emp_cp_render_accordion_trigger($title, array $ids, array $selectedOptions, $level = 1)
{
    $levelClass = $level > 1 ? ' emp-cp-ma-trigger-sub' : '';
    echo '<div class="emp-cp-ma-trigger' . $levelClass . '" role="button" tabindex="0" aria-expanded="false">';
    echo '<span class="emp-cp-ma-toggle" aria-hidden="true"><span class="emp-cp-ma-icon-plus">+</span><span class="emp-cp-ma-icon-minus">−</span></span>';
    echo '<span class="emp-cp-ma-title">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</span>';
    emp_cp_render_select_all($ids, $selectedOptions);
    echo '</div>';
}

function emp_cp_render_accordion_panel_open($level = 1)
{
    $levelClass = $level > 1 ? ' emp-cp-ma-panel-sub' : '';
    echo '<div class="emp-cp-ma-panel' . $levelClass . '">';
}

function emp_cp_render_accordion_panel_close()
{
    echo '</div>';
}

function emp_cp_render_subheader()
{
    echo '<div class="emp-cp-ma-subheader">';
    echo '<span>Sub-menu</span>';
    echo '<span>View</span>';
    echo '</div>';
}

function emp_cp_render_option_list(array $ids, array $selectedOptions)
{
    $options = emp_cp_fetch_options($ids);
    if (empty($options)) {
        return;
    }
    emp_cp_render_subheader();
    foreach ($options as $result) {
        emp_cp_render_option_row($result, $selectedOptions);
    }
}

function emp_cp_render_accordion_item($title, array $ids, array $selectedOptions, callable $bodyRenderer, $level = 1)
{
    static $idx = 0;
    $idx++;
    $ids = array_values(array_unique(array_map('intval', array_filter($ids))));
    if (empty($ids)) {
        return;
    }

    echo '<div class="emp-cp-ma-item" data-level="' . (int) $level . '">';
    emp_cp_render_accordion_trigger($title, $ids, $selectedOptions, $level);
    emp_cp_render_accordion_panel_open($level);
    $bodyRenderer($ids, $selectedOptions);
    emp_cp_render_accordion_panel_close();
    echo '</div>';
}

function emp_render_cp_menu_access(array $selectedOptions)
{
    static $rendered = false;
    $selectedOptions = array_map('strval', $selectedOptions);
    $sections = include __DIR__ . '/admin-sidebar-menu-permissions-config.php';
    $usedIds = emp_cp_menu_access_collect_ids($sections);

    echo '<div class="emp-cp-menu-access col-md-12 px-0">';
    echo '<div class="emp-cp-ma-table-head">';
    echo '<span>Module / Screen</span>';
    echo '<span>View</span>';
    echo '</div>';
    echo '<div class="emp-cp-ma-list">';

    foreach ($sections as $section) {
        $sectionIds = array();
        if (!empty($section['ids'])) {
            $sectionIds = $section['ids'];
        }
        if (!empty($section['groups'])) {
            foreach ($section['groups'] as $group) {
                if (!empty($group['ids'])) {
                    $sectionIds = array_merge($sectionIds, $group['ids']);
                }
            }
        }
        if (empty($sectionIds)) {
            continue;
        }

        $sectionTitle = $section['title'];
        $sectionGroups = !empty($section['groups']) ? $section['groups'] : null;

        emp_cp_render_accordion_item(
            $sectionTitle,
            $sectionIds,
            $selectedOptions,
            function ($ids, $selected) use ($sectionGroups, $section) {
                if ($sectionGroups) {
                    foreach ($sectionGroups as $group) {
                        if (empty($group['ids'])) {
                            continue;
                        }
                        emp_cp_render_accordion_item(
                            $group['title'],
                            $group['ids'],
                            $selected,
                            function ($groupIds, $sel) {
                                emp_cp_render_option_list($groupIds, $sel);
                            },
                            2
                        );
                    }
                } else {
                    emp_cp_render_option_list($section['ids'], $selected);
                }
            },
            1
        );
    }

    $legacyIds = emp_cp_menu_access_legacy_ids();
    $otherIds = array_values(array_diff($legacyIds, $usedIds));
    if (!empty($otherIds)) {
        emp_cp_render_accordion_item(
            'Other Permissions',
            $otherIds,
            $selectedOptions,
            function ($ids, $selected) {
                emp_cp_render_option_list($ids, $selected);
            },
            1
        );
    }

    echo '</div></div>';

    if (!$rendered) {
        $rendered = true;
        emp_render_cp_menu_access_scripts();
    }
}

function emp_render_cp_menu_access_scripts()
{
    ?>
<script>
(function () {
    function getDirectPanel(item) {
        if (!item) return null;
        for (var i = 0; i < item.children.length; i++) {
            if (item.children[i].classList.contains('emp-cp-ma-panel')) {
                return item.children[i];
            }
        }
        return null;
    }

    function updateSelectAll(scope) {
        var root = scope || document.getElementById('emp-menu-access');
        if (!root) return;
        root.querySelectorAll('.emp-cp-ma-item').forEach(function (item) {
            var master = item.querySelector(':scope > .emp-cp-ma-trigger .emp-cp-ma-select-all');
            var panel = getDirectPanel(item);
            if (!master || !panel) return;
            var children = panel.querySelectorAll('input.emp-cp-ma-option[name="Options[]"]');
            if (!children.length) return;
            var checked = 0;
            children.forEach(function (c) { if (c.checked) checked++; });
            master.checked = checked === children.length;
            master.indeterminate = checked > 0 && checked < children.length;
        });
    }

    function expandCheckedSections(root) {
        root.querySelectorAll('.emp-cp-ma-item').forEach(function (item) {
            var hasChecked = item.querySelector('input.emp-cp-ma-option[name="Options[]"]:checked');
            if (hasChecked) {
                item.classList.add('is-open');
                for (var i = 0; i < item.children.length; i++) {
                    if (item.children[i].classList.contains('emp-cp-ma-trigger')) {
                        item.children[i].setAttribute('aria-expanded', 'true');
                        break;
                    }
                }
            }
        });
    }

    function bindMenuAccessAccordion() {
        var root = document.getElementById('emp-menu-access');
        if (!root || root.getAttribute('data-cp-ma-bound')) return;
        root.setAttribute('data-cp-ma-bound', '1');

        root.addEventListener('click', function (e) {
            var trigger = e.target.closest('.emp-cp-ma-trigger');
            if (!trigger || !root.contains(trigger)) return;
            if (e.target.closest('.emp-cp-ma-check')) return;
            var item = trigger.closest('.emp-cp-ma-item');
            if (!item) return;
            var open = item.classList.toggle('is-open');
            trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        root.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            var trigger = e.target.closest('.emp-cp-ma-trigger');
            if (!trigger || !root.contains(trigger)) return;
            e.preventDefault();
            trigger.click();
        });

        root.addEventListener('change', function (e) {
            var t = e.target;
            if (!root.contains(t)) return;
            if (t.classList.contains('emp-cp-ma-select-all')) {
                var item = t.closest('.emp-cp-ma-item');
                var panel = getDirectPanel(item);
                if (panel) {
                    panel.querySelectorAll('input.emp-cp-ma-option[name="Options[]"]').forEach(function (cb) {
                        cb.checked = t.checked;
                    });
                    panel.querySelectorAll('.emp-cp-ma-item').forEach(function (nested) {
                        var nestedMaster = nested.querySelector(':scope > .emp-cp-ma-trigger .emp-cp-ma-select-all');
                        if (nestedMaster && nestedMaster !== t) {
                            nestedMaster.checked = t.checked;
                            nestedMaster.indeterminate = false;
                        }
                        nested.querySelectorAll('input.emp-cp-ma-option[name="Options[]"]').forEach(function (cb) {
                            cb.checked = t.checked;
                        });
                    });
                }
                updateSelectAll(root);
                collectMenuOptionsCsv();
                return;
            }
            if (t.classList.contains('emp-cp-ma-option')) {
                updateSelectAll(root);
                collectMenuOptionsCsv();
            }
        });

        root.querySelectorAll('.emp-cp-ma-select-all[data-indeterminate="1"]').forEach(function (el) {
            el.indeterminate = true;
        });
        expandCheckedSections(root);
        updateSelectAll(root);
        collectMenuOptionsCsv();
    }

    function collectMenuOptionsCsv() {
        var hidden = document.getElementById('cp_options2_csv');
        if (!hidden) {
            return;
        }
        var form = document.getElementById('validation-form');
        var scope = form || document;
        var ids = [];
        scope.querySelectorAll('input.emp-cp-ma-option[name="Options[]"]:checked').forEach(function (cb) {
            var id = parseInt(cb.value, 10);
            if (id > 0) {
                ids.push(id);
            }
        });
        ids.sort(function (a, b) { return a - b; });
        hidden.value = ids.join(',');
    }

    window.empCpMenuAccessCollect = function () {
        var root = document.getElementById('emp-menu-access');
        if (root) {
            updateSelectAll(root);
        }
        collectMenuOptionsCsv();
    };

    window.empCpMenuAccessSync = window.empCpMenuAccessCollect;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindMenuAccessAccordion);
    } else {
        bindMenuAccessAccordion();
    }
})();
</script>
    <?php
}
