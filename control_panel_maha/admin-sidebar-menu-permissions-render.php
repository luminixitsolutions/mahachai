<?php
/**
 * Render Control Panel Menu Access checkboxes (add-employee.php) aligned with sidebar sections.
 * Grid layout: section headers with checkbox columns (3-col), plus search filter.
 */

function emp_cp_menu_access_allowed_ids()
{
    static $ids = null;
    if ($ids !== null) {
        return $ids;
    }
    $sections = emp_cp_menu_access_sections();
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

function emp_cp_menu_access_sections()
{
    static $sections = null;
    if ($sections !== null) {
        return $sections;
    }
    $employeeConfig = __DIR__ . '/admin-sidebar-menu-permissions-employee-config.php';
    if (is_readable($employeeConfig)) {
        $sections = include $employeeConfig;
        return $sections;
    }
    $sections = include __DIR__ . '/admin-sidebar-menu-permissions-config.php';
    return $sections;
}

function maha_cp_menu_access_sections()
{
    return emp_cp_menu_access_sections();
}

function emp_cp_menu_access_hidden_ids()
{
    return array(
        4, 5, 6, 7, 8,
        89, 90, 91, 92, 93,
    );
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
        164, 165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175, 176, 177, 178,
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

function emp_cp_search_attr($text)
{
    return htmlspecialchars(strtolower(trim((string) $text)), ENT_QUOTES, 'UTF-8');
}

function emp_cp_render_checkbox_grid(array $options, array $selectedOptions)
{
    if (empty($options)) {
        return;
    }

    echo '<div class="row emp-cp-ma-grid">';
    foreach ($options as $result) {
        $label = htmlspecialchars($result['Name'], ENT_QUOTES, 'UTF-8');
        $checked = emp_cp_option_checked_attr($result['id'], $selectedOptions);
        echo '<div class="form-group col-md-4 emp-cp-ma-option-item" data-search-text="' . emp_cp_search_attr($result['Name']) . '">';
        echo '<label class="custom-control custom-checkbox mb-0">';
        echo '<input type="checkbox" class="custom-control-input emp-cp-ma-option" name="Options[]" value="' . (int) $result['id'] . '"' . $checked . '>';
        echo '<span class="custom-control-label">' . $label . '</span>';
        echo '</label>';
        echo '</div>';
    }
    echo '</div>';
}

function emp_cp_render_section_header($title)
{
    $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    echo '<div class="emp-cp-ma-section-head emp-cp-ma-section-toggle" role="button" tabindex="0" aria-expanded="false" data-search-text="' . emp_cp_search_attr($title) . '">';
    echo '<span class="emp-cp-ma-section-title">' . $titleEsc . '</span>';
    echo '<span class="emp-cp-ma-toggle" aria-hidden="true"><span class="emp-cp-ma-icon-plus">+</span><span class="emp-cp-ma-icon-minus">−</span></span>';
    echo '</div>';
}

function emp_cp_render_group_header($title)
{
    $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    echo '<div class="emp-cp-ma-group-head" data-search-text="' . emp_cp_search_attr($title) . '">' . $titleEsc . '</div>';
}

function emp_cp_render_option_block(array $ids, array $selectedOptions)
{
    $options = emp_cp_fetch_options($ids);
    if (empty($options)) {
        return;
    }
    emp_cp_render_checkbox_grid($options, $selectedOptions);
}

function emp_cp_build_group_search_text($group)
{
    $parts = array($group['title']);
    $options = emp_cp_fetch_options(isset($group['ids']) ? $group['ids'] : array());
    foreach ($options as $opt) {
        $parts[] = $opt['Name'];
    }
    return implode(' ', $parts);
}

function emp_cp_build_section_search_text($section)
{
    $parts = array($section['title']);
    if (!empty($section['groups'])) {
        foreach ($section['groups'] as $group) {
            $parts[] = $group['title'];
            $options = emp_cp_fetch_options(isset($group['ids']) ? $group['ids'] : array());
            foreach ($options as $opt) {
                $parts[] = $opt['Name'];
            }
        }
    } elseif (!empty($section['ids'])) {
        $options = emp_cp_fetch_options($section['ids']);
        foreach ($options as $opt) {
            $parts[] = $opt['Name'];
        }
    }
    return implode(' ', $parts);
}

function emp_render_cp_menu_access(array $selectedOptions)
{
    static $rendered = false;
    $selectedOptions = array_map('strval', $selectedOptions);
    $sections = emp_cp_menu_access_sections();
    $usedIds = emp_cp_menu_access_collect_ids($sections);

    echo '<div class="emp-cp-menu-access col-md-12 px-0">';
    echo '<div class="emp-cp-ma-search-wrap">';
    echo '<div class="input-group">';
    echo '<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div>';
    echo '<input type="text" class="form-control emp-cp-ma-search" id="emp-cp-ma-search" placeholder="Search menu &amp; sub-menu access..." autocomplete="off">';
    echo '</div>';
    echo '<p class="emp-cp-ma-search-empty mb-0">No matching menu access found.</p>';
    echo '</div>';
    echo '<div class="emp-cp-ma-sections">';

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

        $sectionSearch = emp_cp_build_section_search_text($section);
        $sectionTitleSearch = emp_cp_search_attr($section['title']);
        echo '<div class="emp-cp-ma-section" data-section-title="' . $sectionTitleSearch . '" data-search-text="' . emp_cp_search_attr($sectionSearch) . '">';
        emp_cp_render_section_header($section['title']);
        echo '<div class="emp-cp-ma-section-body">';

        if (!empty($section['groups'])) {
            foreach ($section['groups'] as $group) {
                if (empty($group['ids'])) {
                    continue;
                }
                echo '<div class="emp-cp-ma-group" data-search-text="' . emp_cp_search_attr(emp_cp_build_group_search_text($group)) . '">';
                emp_cp_render_group_header($group['title']);
                emp_cp_render_option_block($group['ids'], $selectedOptions);
                echo '</div>';
            }
        } else {
            emp_cp_render_option_block($section['ids'], $selectedOptions);
        }

        echo '</div></div>';
    }

    $legacyIds = array_values(array_diff(
        emp_cp_menu_access_legacy_ids(),
        $usedIds,
        emp_cp_menu_access_hidden_ids()
    ));
    if (!empty($legacyIds)) {
        $legacyOptions = emp_cp_fetch_options($legacyIds);
        $legacySearchParts = array('Other Permissions');
        foreach ($legacyOptions as $opt) {
            $legacySearchParts[] = $opt['Name'];
        }
        echo '<div class="emp-cp-ma-section" data-section-title="' . emp_cp_search_attr('Other Permissions') . '" data-search-text="' . emp_cp_search_attr(implode(' ', $legacySearchParts)) . '">';
        emp_cp_render_section_header('Other Permissions');
        echo '<div class="emp-cp-ma-section-body">';
        emp_cp_render_checkbox_grid($legacyOptions, $selectedOptions);
        echo '</div></div>';
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

    function expandSectionsWithChecked(root) {
        root.querySelectorAll('.emp-cp-ma-section').forEach(function (section) {
            if (section.querySelector('input.emp-cp-ma-option:checked')) {
                section.classList.add('is-open');
                var head = section.querySelector('.emp-cp-ma-section-head');
                if (head) {
                    head.setAttribute('aria-expanded', 'true');
                }
            }
        });
    }

    function setSectionOpen(section, open) {
        if (!section) return;
        section.classList.toggle('is-open', open);
        var head = section.querySelector('.emp-cp-ma-section-head');
        if (head) {
            head.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
    }

    function filterMenuAccess(query) {
        var root = document.getElementById('emp-menu-access');
        if (!root) return;
        var q = (query || '').trim().toLowerCase();
        var visibleSections = 0;

        root.querySelectorAll('.emp-cp-ma-section').forEach(function (section) {
            var sectionTitle = (section.getAttribute('data-section-title') || '').toLowerCase();
            var sectionHeaderMatch = q === '' || sectionTitle.indexOf(q) !== -1;
            var visibleGroups = 0;

            section.querySelectorAll('.emp-cp-ma-group').forEach(function (group) {
                var groupHead = group.querySelector('.emp-cp-ma-group-head');
                var groupTitle = (groupHead ? groupHead.getAttribute('data-search-text') : '') || '';
                groupTitle = groupTitle.toLowerCase();
                var groupTitleMatch = q === '' || groupTitle.indexOf(q) !== -1;
                var visibleOptions = 0;

                group.querySelectorAll('.emp-cp-ma-option-item').forEach(function (item) {
                    var itemText = (item.getAttribute('data-search-text') || '').toLowerCase();
                    var show = q === '' || itemText.indexOf(q) !== -1 || groupTitleMatch;
                    item.style.display = show ? '' : 'none';
                    if (show) visibleOptions++;
                });

                var showGroup = q === '' || groupTitleMatch || visibleOptions > 0;
                group.style.display = showGroup ? '' : 'none';
                if (showGroup) visibleGroups++;
            });

            var directVisible = 0;
            section.querySelectorAll('.emp-cp-ma-section-body > .emp-cp-ma-grid > .emp-cp-ma-option-item').forEach(function (item) {
                var itemText = (item.getAttribute('data-search-text') || '').toLowerCase();
                var show = q === '' || itemText.indexOf(q) !== -1;
                item.style.display = show ? '' : 'none';
                if (show) directVisible++;
            });

            var showSection = q === '' || sectionHeaderMatch || visibleGroups > 0 || directVisible > 0;
            section.style.display = showSection ? '' : 'none';
            if (showSection) {
                visibleSections++;
                if (q !== '') {
                    setSectionOpen(section, true);
                }
            }
        });

        var emptyEl = root.querySelector('.emp-cp-ma-search-empty');
        if (emptyEl) {
            emptyEl.style.display = (q !== '' && visibleSections === 0) ? 'block' : 'none';
        }
    }

    function bindMenuAccess() {
        var root = document.getElementById('emp-menu-access');
        if (!root || root.getAttribute('data-cp-ma-bound')) return;
        root.setAttribute('data-cp-ma-bound', '1');

        var searchInput = root.querySelector('.emp-cp-ma-search');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                filterMenuAccess(searchInput.value);
            });
            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    searchInput.value = '';
                    filterMenuAccess('');
                }
            });
        }

        root.addEventListener('click', function (e) {
            var head = e.target.closest('.emp-cp-ma-section-toggle');
            if (!head || !root.contains(head)) return;
            var section = head.closest('.emp-cp-ma-section');
            if (!section) return;
            setSectionOpen(section, !section.classList.contains('is-open'));
        });

        root.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter' && e.key !== ' ') return;
            var head = e.target.closest('.emp-cp-ma-section-toggle');
            if (!head || !root.contains(head)) return;
            e.preventDefault();
            head.click();
        });

        root.addEventListener('change', function (e) {
            if (e.target.classList.contains('emp-cp-ma-option')) {
                collectMenuOptionsCsv();
            }
        });

        expandSectionsWithChecked(root);
        collectMenuOptionsCsv();
    }

    window.empCpMenuAccessCollect = function () {
        collectMenuOptionsCsv();
    };

    window.empCpMenuAccessSync = window.empCpMenuAccessCollect;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindMenuAccess);
    } else {
        bindMenuAccess();
    }
})();
</script>
    <?php
}
