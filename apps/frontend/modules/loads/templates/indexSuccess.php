<script>
<!--
var sort_by = '';
var sort_order = 0;
var current_view = '';
var page = 1;

function listReload(page)
{
    $('#indexAjax').html('<div align="center"><div class="loader_admin"></div></div>');
    $('#indexAjax').load('<?php echo url_for('@loads') ?>?'+$('#filterSearch').serialize(), {
        sort_by:      sort_by,
        sort_order:   sort_order,
        page:         page,
        view:         current_view,
    });
}

function sort(type, default_order) {
    if (type != sort_by) {
        sort_by = type;
        sort_order = default_order;
    } else if(sort_order) {
        sort_order = 0
    } else {
        sort_order = 1;
    }
    listReload(1);
}

/*$(function() {
		$( "#ground_order_reserved_date" ).datepicker({
			showOn: "button",
			minDate: new Date(),
		});
	});*/
-->
</script>
<div style='font-weight:bold; color:#f00; padding:5px 0; font-size:13px;'>
<?php
	if (sizeof($notifies) > 0)
	{
		echo "<ul>";
		foreach ($notifies as $notify)
		{
			echo "<li>".$notify['content']." - ".$notify['created_at']."</li>";
		}
		echo "</ul>";
	}
?>
</div>
<h1>Loads list</h1>
<div id="SearchForm" class='backend_form'>
  <form id="filterSearch" action="<?php echo url_for('@loads') ?>" method="post">
    <table>
      <?php echo $loads_form ?>
      <tr><th>
        <input  id="fs" type="submit" value="<?php echo "Search" ?>"/>
      </th></tr>
    </table>
  </form>
</div>
      
<div style='padding-top:10px;'>
<div class="UITable">
  <div id="indexAjax" class='filter_form'>
    <?php include 'indexAjax.php' ?>
  </div>
</div>
</div>
