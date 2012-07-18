<?php if ($value): ?>
  <?php echo image_tag(sfConfig::get('sf_symfony_web_dir').'/images/true.gif', array('alt' => __('Checked', array(), 'sf_admin'), 'title' => __('Checked', array(), 'sf_admin'))) ?>
<?php else: ?>
  &nbsp;
<?php endif; ?>
