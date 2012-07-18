<form action="#" method="post" onsubmit="return false" id="FilterValues" class='backend_form'>
<table>
  <tr>
    <th>Website</th>
    <td>
    	<select name='filter_website' id='filter_website' index='0' multiple="multiple" />
        	<option value="">All</option>
            <?
			foreach ($jobboards as $jobboard)
			{
			?>
            <option value="<?=$jobboard->alias?>"><?=$jobboard->alias?></option>
            <?
			}
			?>
        </select>
    </td>
  </tr>
  <tr>
    <th>Age</th>
    <td>
        <input type='text' name='filter_age' id='filter_age' index='1' />
    </td>
  </tr>
  <tr>
    <th>Deadline</th>
    <td>
        <input type='text' name='filter_deadline' id='filter_deadline' index='2' />
    </td>
  </tr>
  <tr>
    <th>Origin City</th>
    <td>
        <input type='text' name='filter_origin' id='filter_origin' index='7' />
    </td>
  </tr>
  <tr>
    <th>Destination</th>
    <td>
        <input type='text' name='filter_destination' id='filter_destination' index='9' />
    </td>
  </tr>
  <tr>
    <th>Truck type</th>
    <td>
        <input type='text' name='filter_truck_type' id='filter_truck_type' index='4' />
    </td>
  </tr>
  <tr>
    <th>
        <input  id="btn_filter" type="submit" value="<?php echo "Filter" ?>"/>
    </th>
  </tr>
</table>
</form>

<script>
	
	$(document).ready(function()
	{
		$("#btn_filter").live("click", function()
		{
			$(".filter_form .tr_filter_hide").removeClass("tr_filter_hide");
			
			$("#FilterValues input").each(function()
			{
				var filter_values = $.trim($(this).val()).toLowerCase();
				var index = $(this).attr('index');
				if (filter_values != '')
				{
					$(".loads_"+index+"").each(function()
					{
						tr_parent = $(this).parent();
						if (!tr_parent.hasClass('tr_filter_hide')) 
						{
							str = $(this).text().toLowerCase();
							if (str.indexOf(filter_values) == -1) 
								tr_parent.addClass('tr_filter_hide');
						}
					});
				}
			});
			
			$("#FilterValues select").each(function()
			{
				var filter_values = new Array();
				var index_filter = 0;
				$(this).find("option").each(function()
				{
					if ($(this).is(":selected"))
					{
						filter_values[index_filter] = $.trim($(this).attr("value").toLowerCase());
						index_filter ++;
					}
				});
				
				var index = $(this).attr('index');
				if (index_filter > 0)
				{
					index_filter --;
					$(".loads_"+index+"").each(function()
					{
						tr_parent = $(this).parent();
						if (!tr_parent.hasClass('tr_filter_hide')) 
						{
							str = $(this).text().toLowerCase();
							check_filter = false;
							for (i = 0; i <= index_filter; i++)
							{
								filter_value = filter_values[i];
								if ((str.indexOf(filter_value) != -1) || filter_value == "") 
									check_filter = true;
							}
							if (!check_filter)
								tr_parent.addClass('tr_filter_hide');
						}
					});
				}
			});
			
			listReload(1);
		});
	});
</script>