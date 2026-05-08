<?php
include_once 'config.php';

session_start();
include_once 'auth.php';
$user_id = $_SESSION['Admin']['id'];
$MainPage = 'Masters';
$Page = 'Points-Setting';

$rp = getRecord('SELECT * FROM tbl_points_setting ORDER BY id ASC LIMIT 1');
if (!is_array($rp)) {
    $rp = [];
}
$rid = isset($rp['id']) ? (int) $rp['id'] : 0;
$pt = isset($rp['prodtype']) ? (int) $rp['prodtype'] : 1;
if (!in_array($pt, [1, 2, 4], true)) {
    $pt = 1;
}
?>
<!DOCTYPE html>
<html lang="en" class="default-style">
<head>
<title><?php echo $Proj_Title; ?> | Points Setting</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta name="description" content="" />
<meta name="keywords" content="">
<meta name="author" content="" />
<?php include_once 'header_script.php'; ?>
</head>
<body>

 <div class="layout-wrapper layout-1 layout-without-sidenav">
<div class="layout-inner">

 <?php include_once 'top_header.php'; include_once 'sidebar.php'; ?>


<div class="layout-container">

<div class="layout-content">

<div class="container-fluid flex-grow-1 container-p-y">
<h4 class="font-weight-bold py-3 mb-0">Points Setting</h4>
<p class="text-muted mb-4">Single global settings record. Edit values below and save.</p>

<div class="card">
<div class="card-body">
<form id="points-form" method="post" autocomplete="off">
  <input type="hidden" name="action" value="single_save">
  <input type="hidden" name="id" id="record_id" value="<?php echo $rid; ?>">

  <div class="form-row">
    <div class="form-group col-md-6">
      <label class="form-label">Product type <span class="text-danger">*</span></label>
      <select class="form-control" name="prodtype" id="prodtype" required>
        <option value="1" <?php echo $pt === 1 ? 'selected' : ''; ?>>MRP</option>
        <option value="2" <?php echo $pt === 2 ? 'selected' : ''; ?>>Making</option>
        <option value="4" <?php echo $pt === 4 ? 'selected' : ''; ?>>Both</option>
      </select>
    </div>
    <div class="form-group col-md-6">
      <label class="form-label">Points</label>
      <input type="text" name="points" class="form-control" id="points" value="<?php echo isset($rp['points']) ? htmlspecialchars((string) $rp['points']) : ''; ?>">
    </div>
  </div>

  <div class="form-row">
    <div class="form-group col-md-6">
      <label class="form-label">Rs</label>
      <input type="text" name="rs" class="form-control" id="rs" value="<?php echo isset($rp['rs']) ? htmlspecialchars((string) $rp['rs']) : ''; ?>">
    </div>
    <div class="form-group col-md-6">
      <label class="form-label">Min order</label>
      <input type="text" name="minorder" class="form-control" id="minorder" value="<?php echo isset($rp['minorder']) ? htmlspecialchars((string) $rp['minorder']) : ''; ?>">
    </div>
  </div>

  <?php if (in_array('10', $Options) || in_array('14', $Options)) { ?>
  <button type="submit" class="btn btn-primary" id="btn-save">Update</button>
  <?php } else { ?>
  <p class="text-muted mb-0">You do not have permission to change these settings.</p>
  <?php } ?>
</form>
</div>
</div>

</div>

<?php include_once 'footer.php'; ?>

</div>

</div>

</div>

<div class="layout-overlay layout-sidenav-toggle"></div>
</div>

<?php include_once 'footer_script.php'; ?>

<script type="text/javascript">
$(document).ready(function() {
  $('#points-form').on('submit', function(e) {
    e.preventDefault();
    var rtl = $('body').attr('dir') === 'rtl' || $('html').attr('dir') === 'rtl';
    $.ajax({
      url: 'ajax_files/ajax_points_setting.php',
      method: 'POST',
      data: $(this).serialize(),
      beforeSend: function() {
        $('#btn-save').prop('disabled', true).text('Please wait...');
      },
      success: function(data) {
        if (data == 1) {
          $.growl.success({ title: 'Success', message: 'Points setting saved.', location: rtl ? 'tl' : 'tr' });
          var newId = $('#record_id').val();
          if (!newId || newId === '0') {
            window.location.reload();
          }
        } else {
          $.growl.error({ title: 'Error', message: 'Could not save. Check permissions or values.', location: rtl ? 'tl' : 'tr' });
        }
      },
      complete: function() {
        $('#btn-save').prop('disabled', false).text('Update');
      }
    });
  });
});
</script>
</body>
</html>
