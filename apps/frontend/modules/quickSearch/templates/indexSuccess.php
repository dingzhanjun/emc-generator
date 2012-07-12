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
function reload_config_form(config_id)
{
	if (config_id == 0)
	{
		$("#config_max_age").val("");
		$("#config_origin").val("");
		$("#config_origin_radius").val("");
		$("#config_destination").val("");
		$("#config_destination_radius").val("");
		$("#config_loads_type").val("");
		$("#config_length").val("");
		$("#config_weight").val("");
		$("#config_from_date").val("");
		$("#config_to_date").val("");
		$("#config_name").val("");
		$("#config_jobboard_id option:selected").removeAttr('selected');
		$("#config_truck_type option:selected").removeAttr('selected');
	}
	else
	{
		$.post("<?php echo url_for('@quick_search_reload') ?>", { config_id: config_id }, function(data) {
			var json = eval('('+data+')');
			var jobboards = json["config[jobboard_id][]"];
			var trucks = json["config[truck_type][]"];
			var jobboard_arr = jobboards.split(";");
			var truck_arr = trucks.split(";");
			for (index_jobboard in jobboard_arr)
			{
				$("#config_jobboard_id option[value="+jobboard_arr[index_jobboard]+"]").attr('selected','selected');
			}
			
			for (index_truck in truck_arr)
			{
				$("#config_truck_type option[value="+truck_arr[index_truck]+"]").attr('selected','selected');
			}
			
			$("#config_max_age").val(json["config[max_age]"]);
			$("#config_origin").val(json["config[origin]"]);
			$("#config_origin_radius").val(json["config[origin_radius]"]);
			$("#config_destination").val(json["config[destination]"]);
			$("#config_destination_radius").val(json["config[destination_radius]"]);
			$("#config_loads_type").val(json["config[loads_type]"]);
			$("#config_length").val(json["config[length]"]);
			$("#config_weight").val(json["config[weight]"]);
			$("#config_from_date").val(json["config[from_date]"]);
			$("#config_to_date").val(json["config[to_date]"]);
			$("#config_name").val($("#config_id option[value="+config_id+"]").text());
		});
	}
}

function check_config_name()
{
	if ($("#config_save").is(':checked'))
	{
		if ($.trim($("#config_name").val()) == '')
		{
			$("#config_name_error").html("<ul><li>Require</li></ul>");
			return false;
		}
	}
	return true;
}
</script>
<div style='font-weight:bold; color:#f00; padding:5px 0; font-size:13px;'>
<?php
	if (sizeof($notifies) > 0)
	{
		echo "<ul>";
		foreach ($notifies as $notify)
		{
			echo "<li>".$notify['content']."  ".$notify['created_at']."</li>";
		}
		echo "</ul>";
	}
?>
</div>
<table cellpadding="5" width="100%" border="0">
<tr>
    <td width="50%">
    	<h1>Edit Config</h1>
    </td>
    <td>
    	<h1>Filter</h1>
    </td>
</tr>
<tr>
    <td>
        <div id="SearchForm" class='backend_form'>
          <form id="SearchForm" action="<?php echo url_for('@quick_search') ?>" method="post" onsubmit="return check_config_name()">
            <table>
              <tr>
                <th>Config</th>
                <td>
                    <select name='config_id' id='config_id' onchange="reload_config_form(this.value)">
                        <option value='0'>Choose Config</option>
                    <?
                        foreach ($configs as $config) {
                            echo "<option value='".$config->id."' ".((isset($config_id)&&($config_id==$config->id))?"selected='selected'":"").">".$config->name."</option>";
                        }
                    ?>
                    </select>
                </td>
              </tr>
              <?php echo $search_form ?>
              <tr>
                <th>Config name</th>
                <td>
                	<div id='config_name_error'></div>
                    <input type='text' name='config_name' id='config_name' value='<?=(isset($config_name))?$config_name:""?>' />
                </td>
              </tr>
              <tr>
                <th>Save</th>
                <td>
                    <input type="checkbox" name='config_save' id='config_save' <?=(isset($config_save) && $config_save == 'on')?"checked='checked'":""?> />
                </td>
              </tr>
              <tr><th>
                <input  id="fs" type="submit" value="<?php echo "Go" ?>"/>
              </th></tr>
            </table>
          </form>
        </div>
    </td>
    <td style="vertical-align:top">
        <div id='FilterForm'>
        	<? include('indexFilter.php'); ?>
        </div>
    </td>
</tr>
</table>
<div id='indexAjax' style='padding-top:10px;'>
	<?php include 'indexAjax.php' ?>
</div>

<div id='temp'></div>