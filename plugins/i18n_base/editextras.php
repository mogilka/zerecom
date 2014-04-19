<?php
  global $id;
?>
<script type="text/javascript">
  // <![CDATA[
  $(function() {
<?php if (!$id && isset($_GET['newid'])) { ?>
    $('#post-id').val(<?php echo json_encode($_GET['newid']); ?>);
<?php } ?>
<?php if (!$id && isset($_GET['title'])) { ?>
    $('#post-title').val(<?php echo json_encode($_GET['title']); ?>);
<?php } ?>
<?php if (!$id && isset($_GET['metak'])) { ?>
    $('#post-metak').val(<?php echo json_encode($_GET['metak']); ?>);
<?php } ?>
<?php if (!$id && isset($_GET['metad'])) { ?>
    $('#post-metad').val(<?php echo json_encode($_GET['metad']); ?>);
<?php } ?>
  });
  // ]]>
</script>
