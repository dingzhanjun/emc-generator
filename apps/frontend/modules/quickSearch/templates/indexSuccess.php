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
<div id="SearchForm" class='backend_form'>
  <form id="SearchForm" action="<?php echo url_for('@quick_search') ?>" method="post">
    <table>
      <?php echo $search_form ?>
      <tr><th>
        <input  id="fs" type="submit" value="<?php echo "Go" ?>"/>
      </th></tr>
    </table>
  </form>
</div>

<div id='indexAjax' style='padding-top:10px;'>
	<?php include 'indexAjax.php' ?>
</div>