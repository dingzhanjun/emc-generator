<div id="signout"><a href="#"><img src='/images/logout.png' onclick="sign_out()" /></a></div>
<div id='left_tabs'>
    <a href='<?php echo url_for('@config'); ?>' id='below_grounds_manager' class='below_tab_icon <?php echo ($sf_context->getModuleName() == 'config')?"active":"" ?>'><img src='/images/icon_system.png' height="96" /><div class='below_tab_label'>Config</div></a>
    <a href='<?php echo url_for('@loads'); ?>' id='below_users_manager' class='below_tab_icon <?php echo ($sf_context->getModuleName() == 'loads')?"active":"" ?>'><img src='/images/icon_trucks.png' height="96" /><div class='below_tab_label'>Loads</div></a>
    <a href='<?php //echo url_for('@ground_order'); ?>' id='below_services_manager' class='below_tab_icon <?php // echo ($sf_context->getModuleName() == 'groundOrder')?"active":"" ?>'><img src='/images/icon_search.png' height="96" /><div class='below_tab_label'>Quick Search</div></a>
<?
/*
    <a href='<?php //echo url_for('@cms_category_list'); ?>' id='below_cms_manager' class='below_tab_icon <?php echo ($sf_context->getModuleName() == 'cms')?"active":"" ?>'><img src='/images/icon_news.png' height="96" /><div class='below_tab_label'>Tin tức</div></a>
*/
?>
</div>
<?
/*
<div id="right_tabs">    
    <a href='<?php //echo url_for('@page'); ?>' id='below_pages_manager' class='below_tab_icon <?php echo ($sf_context->getModuleName() == 'page')?"active":"" ?>'><img src='/images/icon_notes.png' height="96" /><div class='below_tab_label'>Nội dung</div></a>
    <a href='<?php //echo url_for('@facility'); ?>' id='gallery_manager_below' class='below_tab_icon <?php echo ($sf_context->getModuleName() == 'facility')?"active":"" ?>'><img src='/images/icon_image.png' height="96" /><div class='below_tab_label'>Dịch vụ khác</div></a>
    <a href='<?php //echo url_for('@report'); ?>' id='below_backup_manager' class='below_tab_icon <?php echo ($sf_context->getModuleName() == 'report')?"active":"" ?>'><img src='/images/icon_backup.png' height="96" /><div class='below_tab_label'>Báo cáo</div></a>
    <a href='<?php //echo url_for('@customer'); ?>' id='below_system_manager' class='below_tab_icon <?php echo ($sf_context->getModuleName() == 'user')?"active":"" ?>'><img src='/images/icon_system.png' height="96" /><div class='below_tab_label'>Hệ thống</div></a>
    <div style='clear:both'></div>
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
*/
?>
<style>
	.bg_main_content
	{
		
		-webkit-border-top-right-radius: 10px;
		-webkit-border-top-left-radius: 10px;
		-moz-border-radius-topright: 10px;
		-moz-border-radius-topleft: 10px;
		border-radius: 10px 10px 0 0;

	   -moz-box-shadow:    inset 0 0 10px #00366f;
	   -webkit-box-shadow: inset 0 0 10px #00366f;
	   box-shadow:         inset 0 0 10px #00366f;

		background-image: linear-gradient(bottom, #346599 3%, #c0d5eb 42%, #72a2d5 76%);
		background-image: -o-linear-gradient(bottom, #346599 3%, #c0d5eb 42%, #72a2d5 76%);
		background-image: -moz-linear-gradient(bottom, #346599 3%, #c0d5eb 42%, #72a2d5 76%);
		background-image: -webkit-linear-gradient(bottom, #346599 3%, #c0d5eb 42%, #72a2d5 76%);
		background-image: -ms-linear-gradient(bottom, #346599 3%, #c0d5eb 42%, #72a2d5 76%);
		
		background-image: -webkit-gradient(
			linear,
			left bottom,
			left top,
			color-stop(0.03, #346599),
			color-stop(0.42, #c0d5eb),
			color-stop(0.76, #72a2d5)
		);
	}
</style>

