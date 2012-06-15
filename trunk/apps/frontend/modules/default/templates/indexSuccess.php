<div id="signout"><a href="#"><img src='/images/logout.png' onclick="sign_out()" /></a></div>
<div id='main_tabs'>
	<a rel='<?php echo url_for('@config'); ?>' id='grounds_manager' class='tab_icon'><img src='/images/icon_system.png' /><div class='tab_label'>Config</div></a>
    <a rel='<?php echo url_for('@loads'); ?>' id='users_manager' class='tab_icon'><img src='/images/icon_trucks.png' /><div class='tab_label'>Loads</div></a>
    <a rel='<?php //echo url_for('@ground_order'); ?>' id='services_manager' class='tab_icon'><img src='/images/icon_search.png' /><div class='tab_label'>Quick Search</div></a>
<?
/*
    <a rel='<?php //echo url_for('@cms_category_list'); ?>' id='cms_manager' class='tab_icon'><img src='/images/icon_news.png' /><div class='tab_label'>Tin tức</div></a>
    
    <a rel='<?php //echo url_for('@page'); ?>' id='pages_manager' class='tab_icon'><img src='/images/icon_notes.png' /><div class='tab_label'>Nội dung</div></a>
    <a rel='<?php //echo url_for('@facility'); ?>' id='gallery_manager' class='tab_icon'><img src='/images/icon_image.png' /><div class='tab_label'>Dịch vụ khác</div></a>
    <a rel='<?php //echo url_for('@report'); ?>' id='backup_manager' class='tab_icon'><img src='/images/icon_backup.png' /><div class='tab_label'>Báo cáo</div></a>
    <a rel='<?php //echo url_for('@customer'); ?>' id='system_manager' class='tab_icon'><img src='/images/icon_system.png' /><div class='tab_label'>Hệ thống</div></a>
*/
?>
</div>
<script>
	function sign_out()
	{
		$.ajax({
		  type: "POST",
		  url: "<?php echo url_for('@default_logout') ?>",
		  data: { is_log_out: 'yes' },
		  success: function(data) {
		    alert('Đăng xuất thành công');
			window.location = "<?php echo url_for('@default') ?>";
		  }
		});
	}
</script>