<?php
/**
 * Shared From/To date filter for All Requests listing pages.
 *
 * Optional vars before include:
 *   $allRequestsFilterMethod — 'post' (default) or 'request'
 *   $allRequestsRequireSearch — if true, table is shown only after Search (approve/reject pages)
 *   $allRequestsDefaultToday — if true, default From/To to today when dates are empty
 */
if (!isset($allRequestsRequireSearch)) {
    $allRequestsRequireSearch = false;
}
if (!isset($allRequestsDefaultToday)) {
    $allRequestsDefaultToday = false;
}
if (!isset($allRequestsFilterMethod)) {
    // Approve/reject pages are opened from menu links with ?FromDate=&ToDate=&Search=Search (GET).
    $allRequestsFilterMethod = $allRequestsRequireSearch ? 'request' : 'post';
}

$arfSource = ($allRequestsFilterMethod === 'request') ? $_REQUEST : $_POST;
$arfFromDate = isset($arfSource['FromDate']) ? trim((string) $arfSource['FromDate']) : '';
$arfToDate = isset($arfSource['ToDate']) ? trim((string) $arfSource['ToDate']) : '';
$arfSearched = isset($arfSource['Search']) && $arfSource['Search'] !== '';
$arfShowTable = !$allRequestsRequireSearch || $arfSearched;

if ($allRequestsDefaultToday && $arfFromDate === '' && $arfToDate === '') {
    $arfFromDate = date('Y-m-d');
    $arfToDate = date('Y-m-d');
    if ($allRequestsRequireSearch) {
        $arfShowTable = true;
    }
}
?>
<div id="accordion2">
    <div class="card mb-2">
        <div id="accordion2-2" class="collapse show" data-parent="#accordion2">
            <div class="" style="padding:5px;">
                <form id="validation-form" method="post" enctype="multipart/form-data" action="">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="FromDate" id="FromDate" class="form-control" value="<?php echo htmlspecialchars($arfFromDate, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="ToDate" id="ToDate" class="form-control" value="<?php echo htmlspecialchars($arfToDate, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
                        </div>
                        <input type="hidden" name="Search" value="Search">
                        <div class="form-group col-md-1" style="padding-top:30px;">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-finish">Search</button>
                        </div>
                        <?php if ($arfSearched) { ?>
                        <div class="form-group col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-info btn-block" data-toggle="tooltip" data-placement="top" data-original-title="Clear Filter">X</a>
                        </div>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
