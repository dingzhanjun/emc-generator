<div id="signout"><a href="#"><img src='/images/logout.png' onclick="sign_out()" /></a></div>
<script>
	function sign_out()
	{
		$.ajax({
		  type: "POST",
		  url: "<?php echo url_for('@default_logout') ?>",
		  data: { is_log_out: 'yes' },
		  success: function(data) {
		    alert('�ang xu?t th�nh c�ng');
			window.location = "<?php echo url_for('@default') ?>";
		  }
		});
	}
</script>