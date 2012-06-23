<?
if (isset($loads) && $loads)
{
?>
<script>

var sort_by = 'loads_1';
var sort_order = 0;
var current_view = '';
var page = 1;



function listReload(page)
{
	var arr_data = new Array();
	var arr_index = new Array();
	var cnt = 0;	
	$("."+sort_by+"").each(function()
	{
		arr_data[cnt] = $(this).text();
		arr_index[cnt] = $(this).parent().attr('numero');
		cnt ++;
	});
	arr_temp = new Array();
	for (i = 0; i < arr_data.length; i++)
		arr_temp[i] = arr_data[i];
	arr_data.sort();

	if (sort_order) {
		arr_data.reverse();
	}

	html = $("#list_data_temp");
	html.html("");
	
	arr_check = new Array();
	index_check = 0;
	$(".filter_form").css("opacity","0.5");
	for (i = 0; i < cnt; i++)
	{
		for (j = 0; j < cnt; j++)
		{
			if (!in_array(j, arr_check))
			{
				if (arr_data[i] === arr_temp[j])
				{
					index_check ++;
					arr_check[index_check] = j;
					html.append($(".filter_form .tr_loads[numero="+arr_index[j]+"]"));
					tr_loads = html.find(".tr_loads[numero="+arr_index[j]+"]:first");
					if ((index_check <= (page * 30)) && (index_check > ((page-1) * 30)))
					{
						if (tr_loads.hasClass("tr_hide"))
							tr_loads.removeClass("tr_hide");
					}
					else {
						if (!tr_loads.hasClass("tr_hide"))
							tr_loads.addClass("tr_hide");
					}
					$(".filter_form .tr_loads[numero="+arr_index[j]+"]").remove();
					arr_temp[j] = "NODATA";
					break;
				}
			}
		}
	}
	$(".filter_form").find('tbody').html(html.html());
	$(".filter_form").css("opacity","1");
	$(window).scrollTop($(".filter_form").offset().top, 2000);
	html.html("");
	$(".pagination").find('a.page_main').removeClass("page_main");
	$(".pagination a").each(function(index, element) {
		var pag = $(this).html();
        if (parseInt(pag) == parseInt(page))
			$(this).addClass("page_main");
    });
}

function in_array(value, array){  
    for (im = 0; im < array.length / 2 ; im++)
       if(array[2*im] === value || ( im > 0 && array[2*im - 1] === value )) 
          return true;  
    return false;  
}  

function sort_js(type, default_order) {
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
</script>
<div id='list_data_temp' style='display:none;'></div>
<div class='filter_form'>
<table cellpadding="5" cellspacing="0" border="0" width="100%">
	<thead>
    	<tr>
        	<th>
            	<?php echo link_to_function('<span>Website</span>', 'sort_js(\'loads_0\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Age</span>', 'sort_js(\'loads_1\', 1)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Pick-up Date</span>', 'sort_js(\'loads_2\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Truck Type</span>', 'sort_js(\'loads_3\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Full/Partial</span>', 'sort_js(\'loads_4\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>DH(O)</span>', 'sort_js(\'loads_5\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Origin</span>', 'sort_js(\'loads_6\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Trip</span>', 'sort_js(\'loads_7\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Destination</span>', 'sort_js(\'loads_8\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>DH(D)</span>', 'sort_js(\'loads_9\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Contact</span>', 'sort_js(\'loads_10\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Credit Score</span>', 'sort_js(\'loads_11\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Ft</span>', 'sort_js(\'loads_12\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Klbs</span>', 'sort_js(\'loads_13\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Company</span>', 'sort_js(\'loads_14\', 0)') ?>
            </th>
        </tr>
	</thead>
    <tbody>
        <?php		
			if (!isset($page)) $page = 1;
			if (!isset($per_page)) $per_page = 30;
			$indexKey = 0;
			$count = 0;
			foreach ($loads as $jobboard_name => $data) {
				foreach ($data as $index => $loads) {	
					$count ++;
					$check = ($count > ($page - 1) * 30 && $count <= ($page * 30));				
					echo "<tr class='tr_loads ".(($check)?"":"tr_hide")."' numero='".$count."'>";
					$indexKey = 0;
					echo "<td class='loads_".$indexKey."'>".$jobboard_name.'</td>';
					foreach ($loads as $key => $value) {
						if ($indexKey < 14)
						{
							$indexKey ++;
							echo "<td class='loads_".$indexKey."'>".$value.'</td>';
						}
					}
					echo "</tr>";
				}
				
			}
		?>
    </tbody>
</table>
<input type='hidden' id='maxIndexKey' value='<?=$indexKey?>' />
<input type='hidden' id='maxLoads' value='<?=$count?>' />
</div>

<div class="pagination">
  <?php for ($link = 1; $link <= ceil($count/30); $link ++) { ?>
      <?php echo link_to_function($link, 'listReload('.$link.')', array('class' => (($link==$page)?('page_main'):'page'))) ?>
  <?php } ?>
</div>

<style>
	.tr_show {
	}
	.tr_hide {
		display: none;
	}
	.page_main {
		font-weight:bold;
	}
</style>
<?
}
?>