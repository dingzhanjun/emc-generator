<script>
$(function() {
		$( "#config_from_date" ).datepicker({
			showOn: "button",
			dateFormat: "yy-mm-dd"
		});
		$( "#config_to_date" ).datepicker({
			showOn: "button",
			dateFormat: "yy-mm-dd"
		});
	});
</script>
<h1>Edit Config</h1>
<div id="CreateForm" class='backend_form'>
  <form id="createForm" action="<?php echo url_for('@config_create') ?>" method="post">
    <table>
      <?php echo $config_form ?>
      <tr><th>
        <input  id="fs" type="submit" value="<?php echo "Create" ?>"/>
      </th></tr>
    </table>
  </form>
</div>