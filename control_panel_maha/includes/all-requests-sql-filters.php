<?php
/**
 * Shared WHERE clauses for All Requests / All Admin Request listing pages.
 *
 * pending  — pending at any level (partial or complete); excludes fully approved/rejected
 * approve  — approved at any level (includes partial approvals)
 * reject   — rejected at any level
 */

if (!function_exists('maha_ar_sql_st_pending')) {
    function maha_ar_sql_st_pending($field, $alias = 'te')
    {
        $f = $alias . '.' . $field;
        return "({$f} = 0 OR {$f} = '0' OR {$f} IS NULL OR {$f} = '')";
    }
}

if (!function_exists('maha_ar_sql_st_approved')) {
    function maha_ar_sql_st_approved($field, $alias = 'te')
    {
        $f = $alias . '.' . $field;
        return "({$f} = 1 OR {$f} = '1')";
    }
}

if (!function_exists('maha_ar_sql_st_rejected')) {
    function maha_ar_sql_st_rejected($field, $alias = 'te')
    {
        $f = $alias . '.' . $field;
        return "({$f} = 2 OR {$f} = '2')";
    }
}

if (!function_exists('maha_ar_sql_or_any')) {
    function maha_ar_sql_or_any(array $parts)
    {
        return '(' . implode(' OR ', $parts) . ')';
    }
}

if (!function_exists('maha_ar_sql_append_date')) {
    function maha_ar_sql_append_date($dateField, $alias = 'te', $toEndOfDay = false)
    {
        global $arfFromDate, $arfToDate;
        $sql = '';
        if (!empty($arfFromDate)) {
            $sql .= " AND {$alias}.{$dateField} >= '" . $arfFromDate . "'";
        }
        if (!empty($arfToDate)) {
            $suffix = $toEndOfDay ? ' 23:59:59' : '';
            $sql .= " AND {$alias}.{$dateField} <= '" . $arfToDate . $suffix . "'";
        }
        return $sql;
    }
}

if (!function_exists('maha_ar_sql_where')) {
    /**
     * @param string $type   employee_expense|petty_cash|vendor_expense|nso_vendor_expense|cash_book|manager_hr|penalty|hiring
     * @param string $mode   all|pending|approve|reject
     * @param string $alias  table alias
     * @param string $scope  full|ho_admin_pending — ho_admin_pending = admin queue only (admin status pending, prior steps done)
     */
    function maha_ar_sql_where($type, $mode, $alias = 'te', $scope = 'full', $userAlias = 'tu')
    {
        switch ($type) {
            case 'employee_expense':
                if ($scope === 'ho_admin_pending') {
                    return maha_ar_sql_where_employee_expense_ho_admin_pending($mode, $alias, $userAlias);
                }
                return maha_ar_sql_where_employee_expense($mode, $alias);
            case 'petty_cash':
                return maha_ar_sql_where_pretty_cash($mode, $alias, $scope);
            case 'vendor_expense':
                return maha_ar_sql_where_vendor_expense($mode, $alias, false);
            case 'nso_vendor_expense':
                return maha_ar_sql_where_vendor_expense($mode, $alias, true);
            case 'cash_book':
                return maha_ar_sql_where_cash_book($mode, $alias);
            case 'manager_hr':
                return maha_ar_sql_where_manager_hr($mode, $alias);
            case 'penalty':
                return maha_ar_sql_where_penalty($mode, $alias);
            case 'hiring':
                return maha_ar_sql_where_hiring($mode, $alias);
            default:
                return '1=1';
        }
    }
}

if (!function_exists('maha_ar_sql_where_employee_expense')) {
    function maha_ar_sql_where_employee_expense($mode, $alias = 'te')
    {
        if ($mode === 'all') {
            return "{$alias}.UserId != 0";
        }

        $reject = maha_ar_sql_or_any(array(
            maha_ar_sql_st_rejected('ManagerStatus', $alias),
            maha_ar_sql_st_rejected('AdminStatus', $alias),
            maha_ar_sql_st_rejected('BhStatus', $alias),
            "({$alias}.Gst = 'Yes' AND " . maha_ar_sql_st_rejected('AccountStatus', $alias) . ')',
        ));

        $adminApproved = maha_ar_sql_st_approved('AdminStatus', $alias);

        if ($mode === 'reject') {
            return "{$alias}.UserId != 0 AND {$reject}";
        }
        if ($mode === 'approve') {
            // Admin approval is the final step for All Requests expense listings.
            return "{$alias}.UserId != 0 AND {$adminApproved}";
        }

        // Pending until admin has not approved (admin approval moves record to approve list).
        return "{$alias}.UserId != 0 AND NOT {$reject} AND NOT {$adminApproved}";
    }
}

if (!function_exists('maha_ar_sql_where_employee_expense_ho_admin_pending')) {
    /**
     * HO admin expense queue — show only when admin approval is pending and earlier steps are done.
     */
    function maha_ar_sql_where_employee_expense_ho_admin_pending($mode, $alias = 'te', $userAlias = 'tu')
    {
        if ($mode !== 'pending') {
            return maha_ar_sql_where_employee_expense($mode, $alias);
        }

        $adminPending = maha_ar_sql_st_pending('AdminStatus', $alias);
        $notRejected = 'NOT ' . maha_ar_sql_or_any(array(
            maha_ar_sql_st_rejected('ManagerStatus', $alias),
            maha_ar_sql_st_rejected('AdminStatus', $alias),
            maha_ar_sql_st_rejected('BhStatus', $alias),
            "({$alias}.Gst = 'Yes' AND " . maha_ar_sql_st_rejected('AccountStatus', $alias) . ')',
        ));

        $bhApproved = '('
            . maha_ar_sql_st_approved('BhStatus', $alias)
            . " OR (({$alias}.BhStatus IS NULL OR {$alias}.BhStatus = '') AND {$alias}.BhBy IS NOT NULL AND {$alias}.BhBy != 0)"
            . ')';

        $mgrApproved = maha_ar_sql_st_approved('ManagerStatus', $alias);
        $accApproved = maha_ar_sql_st_approved('AccountStatus', $alias);
        $noMgr = "({$userAlias}.UnderByUser IN (5, 384, 415))";

        $withManager = "({$userAlias}.UnderByUser NOT IN (5, 384, 415) AND {$mgrApproved} AND (({$alias}.Gst != 'Yes' OR {$alias}.Gst IS NULL) OR ({$alias}.Gst = 'Yes' AND {$accApproved})))";
        $withoutManager = "({$noMgr} AND (({$alias}.Gst != 'Yes' OR {$alias}.Gst IS NULL) OR {$accApproved}))";

        return "{$alias}.UserId != 0 AND {$notRejected} AND {$adminPending} AND {$bhApproved} AND ({$withManager} OR {$withoutManager})";
    }
}

if (!function_exists('maha_ar_sql_where_pretty_cash')) {
    /**
     * @param string $scope full — manager + admin + accountant; admin — manager + admin only (HO admin pages); accountant — final account approve only
     */
    function maha_ar_sql_where_pretty_cash($mode, $alias = 'te', $scope = 'full')
    {
        if ($mode === 'all') {
            return "{$alias}.UserId != 0";
        }

        $fields = ($scope === 'admin')
            ? array('ManagerStatus', 'AdminStatus')
            : array('ManagerStatus', 'AdminStatus', 'AccStatus');

        $rejectParts = array();
        $approvedParts = array();
        $pendingParts = array();
        foreach ($fields as $field) {
            $rejectParts[] = maha_ar_sql_st_rejected($field, $alias);
            $approvedParts[] = maha_ar_sql_st_approved($field, $alias);
            $pendingParts[] = maha_ar_sql_st_pending($field, $alias);
        }
        $reject = maha_ar_sql_or_any($rejectParts);
        $anyApproved = maha_ar_sql_or_any($approvedParts);
        $anyPending = maha_ar_sql_or_any($pendingParts);
        $fullyApproved = '(' . implode(' AND ', $approvedParts) . ')';

        if ($mode === 'reject') {
            return "{$alias}.UserId != 0 AND {$reject}";
        }
        if ($mode === 'approve') {
            if ($scope === 'admin') {
                return "{$alias}.UserId != 0 AND " . maha_ar_sql_st_approved('AdminStatus', $alias);
            }
            if ($scope === 'accountant') {
                return "{$alias}.UserId != 0 AND " . maha_ar_sql_st_approved('AccStatus', $alias);
            }
            return "{$alias}.UserId != 0 AND {$anyApproved}";
        }

        return "{$alias}.UserId != 0 AND NOT {$reject} AND NOT " . maha_ar_sql_st_approved('AccStatus', $alias) . " AND NOT {$fullyApproved} AND {$anyPending}";
    }
}

if (!function_exists('maha_ar_sql_where_pretty_cash_ho')) {
    /**
     * HO / All Admin petty cash — manager + admin only; no-manager employees (ExpApproval=1) skip manager step.
     * Requires tbl_users joined as $userAlias (default tu).
     */
    function maha_ar_sql_where_pretty_cash_ho($mode, $alias = 'te', $userAlias = 'tu')
    {
        $mgrOk = maha_ar_sql_st_approved('ManagerStatus', $alias);
        $mgrPending = maha_ar_sql_st_pending('ManagerStatus', $alias);
        $mgrReject = maha_ar_sql_st_rejected('ManagerStatus', $alias);
        $admOk = maha_ar_sql_st_approved('AdminStatus', $alias);
        $admPending = maha_ar_sql_st_pending('AdminStatus', $alias);
        $admReject = maha_ar_sql_st_rejected('AdminStatus', $alias);
        $noMgr = "({$userAlias}.ExpApproval = 1)";

        $adminDone = "({$admOk} AND ({$mgrOk} OR {$noMgr}))";

        if ($mode === 'reject') {
            return "{$alias}.UserId != 0 AND ({$mgrReject} OR {$admReject})";
        }
        if ($mode === 'approve') {
            return "{$alias}.UserId != 0 AND {$admOk}";
        }

        return "{$alias}.UserId != 0
            AND NOT ({$mgrReject} OR {$admReject})
            AND NOT {$adminDone}
            AND ({$admPending} OR ({$mgrPending} AND NOT {$noMgr}))";
    }
}

if (!function_exists('maha_ar_sql_where_vendor_expense')) {
    function maha_ar_sql_where_vendor_expense($mode, $alias = 'te', $nso = false)
    {
        if ($mode === 'all') {
            return "{$alias}.UserId != 0";
        }

        $fields = array('BdmStatus', 'PurchaseStatus', 'ManagerStatus', 'AdminStatus');
        $rejectParts = array();
        $approvedParts = array();
        $pendingParts = array();
        foreach ($fields as $field) {
            $rejectParts[] = maha_ar_sql_st_rejected($field, $alias);
            $approvedParts[] = maha_ar_sql_st_approved($field, $alias);
            $pendingParts[] = maha_ar_sql_st_pending($field, $alias);
        }
        $reject = maha_ar_sql_or_any($rejectParts);
        $anyApproved = maha_ar_sql_or_any($approvedParts);
        $anyPending = maha_ar_sql_or_any($pendingParts);
        $fullyApproved = '(' . implode(' AND ', $approvedParts) . ')';

        if ($mode === 'reject') {
            return "{$alias}.UserId != 0 AND {$reject}";
        }
        if ($mode === 'approve') {
            return "{$alias}.UserId != 0 AND {$anyApproved}";
        }

        return "{$alias}.UserId != 0 AND NOT {$reject} AND NOT {$fullyApproved} AND {$anyPending}";
    }
}

if (!function_exists('maha_ar_sql_where_cash_book')) {
    function maha_ar_sql_where_cash_book($mode, $alias = 'tcb')
    {
        if ($mode === 'all') {
            return '1=1';
        }
        if ($mode === 'reject') {
            return maha_ar_sql_st_rejected('ApproveStatus', $alias);
        }
        if ($mode === 'approve') {
            return maha_ar_sql_st_approved('ApproveStatus', $alias);
        }

        return maha_ar_sql_st_pending('ApproveStatus', $alias);
    }
}

if (!function_exists('maha_ar_sql_where_manager_hr')) {
    function maha_ar_sql_where_manager_hr($mode, $alias = 'te')
    {
        if ($mode === 'all') {
            return "{$alias}.UserId != 0";
        }

        $reject = maha_ar_sql_or_any(array(
            maha_ar_sql_st_rejected('ManagerStatus', $alias),
            maha_ar_sql_st_rejected('HrStatus', $alias),
        ));
        $anyApproved = maha_ar_sql_or_any(array(
            maha_ar_sql_st_approved('ManagerStatus', $alias),
            maha_ar_sql_st_approved('HrStatus', $alias),
        ));
        $anyPending = maha_ar_sql_or_any(array(
            maha_ar_sql_st_pending('ManagerStatus', $alias),
            maha_ar_sql_st_pending('HrStatus', $alias),
        ));
        $fullyApproved = '('
            . maha_ar_sql_st_approved('ManagerStatus', $alias)
            . ' AND ' . maha_ar_sql_st_approved('HrStatus', $alias)
            . ')';

        if ($mode === 'reject') {
            return "{$alias}.UserId != 0 AND {$reject}";
        }
        if ($mode === 'approve') {
            return "{$alias}.UserId != 0 AND {$anyApproved}";
        }

        return "{$alias}.UserId != 0 AND NOT {$reject} AND NOT {$fullyApproved} AND {$anyPending}";
    }
}

if (!function_exists('maha_ar_sql_where_penalty')) {
    function maha_ar_sql_where_penalty($mode, $alias = 'p')
    {
        if ($mode === 'all') {
            return '1=1';
        }
        if ($mode === 'reject') {
            return "({$alias}.bdm_status = 'rejected' OR {$alias}.bh_status = 'rejected')";
        }
        if ($mode === 'approve') {
            return "({$alias}.bdm_status = 'approved' OR {$alias}.bh_status = 'approved')";
        }
        return "({$alias}.bdm_status = 'pending' OR ({$alias}.bdm_status = 'approved' AND {$alias}.bh_status = 'pending'))";
    }
}

if (!function_exists('maha_ar_sql_where_hiring')) {
    function maha_ar_sql_where_hiring($mode, $alias = 'hr')
    {
        if ($mode === 'all') {
            return '1=1';
        }
        if ($mode === 'reject') {
            return "{$alias}.Status = 2";
        }
        if ($mode === 'approve') {
            return "{$alias}.Status IN (1, 3, 4)";
        }
        return "{$alias}.Status = 0";
    }
}
