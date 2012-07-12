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
		if (!$(this).parent().hasClass('tr_filter_hide'))
		{
			arr_data[cnt] = $(this).text();
			arr_index[cnt] = $(this).parent().attr('numero');
			cnt ++;
		}
	});
	var numberOfPage = Math.ceil(cnt / 30);
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
	html.append($(".filter_form tbody:first").html());
	$(".filter_form").find('tbody').html(html.html());
	$(".filter_form").css("opacity","1");
	$(window).scrollTop($(".filter_form").offset().top, 2000);
	html.html("");
	
	$(".pagination").find('a.page_main').removeClass("page_main");
	$(".pagination a").each(function(index, element) {
		var pag = $(this).html();
        if (parseInt(pag) == parseInt(page))
			$(this).addClass("page_main");
		if (parseInt(pag) > numberOfPage)
			$(this).hide();
		else
			$(this).show();
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
            	<?php echo link_to_function('<span>Deadline</span>', 'sort_js(\'loads_2\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Pick-up Date</span>', 'sort_js(\'loads_3\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Truck Type</span>', 'sort_js(\'loads_4\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Full/Partial</span>', 'sort_js(\'loads_5\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>DH(O)</span>', 'sort_js(\'loads_6\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Origin</span>', 'sort_js(\'loads_7\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Trip</span>', 'sort_js(\'loads_8\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Destination</span>', 'sort_js(\'loads_9\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>DH(D)</span>', 'sort_js(\'loads_10\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Contact</span>', 'sort_js(\'loads_11\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Company</span>', 'sort_js(\'loads_12\', 0)') ?>
            </th>
            <th>
            	<?php echo link_to_function('<span>Rate</span>', 'sort_js(\'loads_13\', 0)') ?>
            </th>
        </tr>
	</thead>
    <tbody>
        <?php		
			if (!isset($page)) $page = 1;
			if (!isset($per_page)) $per_page = 30;
			$indexKey = 0;
			$count = 0;
			foreach ($loads as $jobboard_alias => $data) {
				foreach ($data as $index => $loads) {	
					$count ++;
					$check = ($count > ($page - 1) * 30 && $count <= ($page * 30));				
					echo "<tr class='tr_loads ".(($check)?"":"tr_hide")."' numero='".$count."'>";
					$indexKey = 0;
					echo "<td class='loads_".$indexKey."'>".$jobboard_alias.'</td>';
					if ($jobboard_alias == 'TE') {
					    $indexKey = 1;
						echo "<td class='loads_".$indexKey."'>".$loads[0].'</td>';
						$indexKey ++;
						echo "<td class='loads_".$indexKey."'>".$loads[1].'</td>';
						$indexKey ++;
						echo "<td class='loads_".$indexKey."'>--</td>";
						$indexKey ++;
						echo "<td class='loads_".$indexKey."'>".$loads[2].'</td>';
						$indexKey ++;
						echo "<td class='loads_".$indexKey."'>".$loads[3].'</td>';
						$indexKey ++;
						echo "<td class='loads_".$indexKey."'>".$loads[4].'</td>';
						$indexKey ++;
						echo "<td class='loads_".$indexKey."'>".$loads[5].'</td>';
						$indexKey ++;
						echo "<td class='loads_".$indexKey."'>".$loads[6].'</td>';
						$indexKey ++;
						echo "<td class='loads_".$indexKey."'>".$loads[7].'</td>';
						$indexKey ++;
						echo "<td class='loads_".$indexKey."'>".$loads[8].'</td>';
						$indexKey ++;
						echo "<td class='loads_".$indexKey."'>".$loads[9].'</td>';
						$indexKey ++;
						echo "<td class='loads_".$indexKey."'>".$loads[13].'</td>';
						$indexKey++;
						echo "<td class='loads_".$indexKey."'>".$loads['rate']."</td>";
				    } elseif ($jobboard_alias == 'FV') {
				        $indexKey = 1;
				        echo "<td class='loads_".$indexKey."'>--</td>"; // no age
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[6].'</td>'; // deadline
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[1].'</td>'; // pickup date
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[9].'</td>'; // truck type
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>--</td>"; // no loads type
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>--</td>"; // no DH(O)
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[2].' '.$loads[3].'</td>';
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[8].'</td>';
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[4].' '.$loads[5].'</td>';
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>--</td>";
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>--</td>";
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[7].' Ref:'.$loads[0].'</td>';
				    	$indexKey++;
				        echo "<td class='loads_".$indexKey."'>--</td>";
					} elseif ($jobboard_alias == 'GF') {
						$indexKey = 1;
						echo "<td class='loads_".$indexKey."'>--</td>"; // no age
						$indexKey++;
						echo "<td class='loads_".$indexKey."'>--</td>"; // no deadline
						$indexKey++;
						echo "<td class='loads_".$indexKey."'>".$loads[1].'</td>'; // pickup date
						$indexKey++;
						echo "<td class='loads_".$indexKey."'>".$loads[2].'</td>'; // truck type
						$indexKey++;
						echo "<td class='loads_".$indexKey."'>".$loads[5]."</td>"; // loads type
						$indexKey++;
						echo "<td class='loads_".$indexKey."'>--</td>"; // no DH(O)
						$indexKey++;
						echo "<td class='loads_".$indexKey."'>".$loads[3]."</td>";
						$indexKey++;
						echo "<td class='loads_".$indexKey."'>--</td>"; // no trip
						$indexKey++;
						echo "<td class='loads_".$indexKey."'>".$loads[4].'</td>';
						$indexKey++;
						echo "<td class='loads_".$indexKey."'>--</td>"; // no DH(D)
						$indexKey++;
						$contact = $loads[6];
						$company = "";
						for ($index_contact = 0; $index_contact < strlen($contact); $index_contact++)
							if (($contact[$index_contact] >= 'a' && $contact[$index_contact] <= 'z') || ($contact[$index_contact] >= 'A' && $contact[$index_contact] <= 'Z')) {
								$company = trim(substr($contact, $index_contact)); 
								$contact = trim(substr($contact, 0, $index_contact-1));
								break;
							}
						echo "<td class='loads_".$indexKey."'>".$contact."</td>"; // contact
						$indexKey++;
						echo "<td class='loads_".$indexKey."'>".$company."</td>"; // Company
						$indexKey++;
				        echo "<td class='loads_".$indexKey."'>--</td>";
					} else if ($jobboard_alias == 'LS') {
						$indexKey = 1;
				        echo "<td class='loads_".$indexKey."'>--</td>"; // no age
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>--</td>"; // no deadline
				        $indexKey++;
						$date = explode("/", strip_tags(trim($loads[3])));
				        echo "<td class='loads_".$indexKey."'>".date('Y-m-d', strtotime($date[0]."/".$date[1]."/".date("Y"))).'</td>'; // pickup date
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[5].'</td>'; // truck type
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>--</td>"; // no loads type
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>--</td>"; // no DH(O)
						$location = trim($loads[4]);
						$location = preg_replace('/\s+/',' ', $location);
						$location = preg_replace("/[^A-Za-z0-9, ]/i", "+", $location);
						$temp = $location;
						$location = explode("+", $location);
						$indexLocation = 1;
						for ($indexLocation = 1; $indexLocation < sizeof($location); $indexLocation ++)
						{
							if ($location[$indexLocation])
							{
								$destination = $location[$indexLocation];
								break;
							}
						}
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$location[0].'</td>'; // origin
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[7]."</td>"; // trip
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$destination.'</td>'; // destination
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>--</td>"; // DH(D)
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[2]."</td>"; // Contact
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[0]."</td>"; // Company
						$indexKey++;
				        echo "<td class='loads_".$indexKey."'>--</td>";
					} elseif ($jobboard_alias == 'TS') {
					    $indexKey = 1;
					    echo "<td class='loads_".$indexKey."'>".$loads[4]."</td>"; // age
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>--</td>"; // no deadline
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>".$loads[6]."</td>";
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>Flatbed</td>";
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>".$loads[5]."</td>";
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>".$loads[9]."</td>";
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>".$loads[7].' '.$loads[8]."</td>"; 
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>-".($loads[20] == "0" ? $loads[21] : $loads[20])."-</td>";
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>".$loads[10].' '.$loads[11]."</td>";
					    
                        $indexKey++;
					    echo "<td class='loads_".$indexKey."'>".$loads[12]."</td>";
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>".$loads[2]."</td>";
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>".$loads[18]."</td>";
						
						$indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[13]."</td>";
                    }elseif ($jobboard_alias == 'CH') {
					    $indexKey = 1;
					    echo "<td class='loads_".$indexKey."'>--</td>"; // age
					    
                        $indexKey++;
                        $deadline = $loads[2];              
                        $deadline1 = substr($deadline, 0,9);
                        $deadline2 = substr($deadline,9,strlen($deadline));
            			$deadline1 = explode("/", strip_tags($deadline1));
                        $deadline1[2] = trim(substr($deadline1[2], 0, 4));
            			$deadline1 = date("Y-m-d", strtotime(trim($deadline1[2]) . "-" . trim($deadline1[0]) . "-" . trim($deadline1[1])));
					    echo "<td class='loads_".$indexKey."'>".$deadline1."<br/>".$deadline2."</td>"; // pick up
					    
					    $indexKey++;
                        $date = $loads[2];              
                        $date1 = substr($date, 0,9);
                        $date2 = substr($date,9,strlen($date));
            			$date1 = explode("/", strip_tags($date1));
                        $date1[2] = trim(substr($date1[2], 0, 4));
            			$date1 = date("Y-m-d", strtotime(trim($date1[2]) . "-" . trim($date1[0]) . "-" . trim($date1[1])));
					    echo "<td class='loads_".$indexKey."'>".$date1."<br/>".$date2."</td>"; // pick up
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>".$loads[8]."</td>"; // truck type
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>---</td>"; // no Full/partial
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>---</td>"; // no DH(O)
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>".$loads[1]."</td>"; // origin
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>---</td>"; // no trip
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>".$loads[4]."</td>"; // destination
					    
                        $indexKey++;
					    echo "<td class='loads_".$indexKey."'>---</td>"; // no DH(D)
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>---</td>"; //no Contact
					    
					    $indexKey++;
					    echo "<td class='loads_".$indexKey."'>---</td>"; // no Company
						
						$indexKey++;
				        echo "<td class='loads_".$indexKey."'>---</td>"; // no Rate
                                             
					} elseif ($jobboard_alias == 'GL') {
						$indexKey = 1;
				        echo "<td class='loads_".$indexKey."'>--</td>"; // no age
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>--</td>"; // no deadline
				        $indexKey++;
						$date = date('Y').'/'.$loads[7];
						$date = str_replace('/', '-', $date);
				        echo "<td class='loads_".$indexKey."'>".$date.'</td>'; // pickup date
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[25].'</td>'; // truck type
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[13]."</td>"; // no loads type
						$cities = preg_split("/\sto\s/", $loads[1]);
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[9]."</td>"; // no DH(O)
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$cities[0].'</td>'; // origin
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>--</td>"; // trip
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$cities[1].'</td>'; // destination
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>--</td>"; // DH(D)
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[19]."</td>"; // Contact
				        $indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[17]."</td>"; // Company
						$indexKey++;
				        echo "<td class='loads_".$indexKey."'>".$loads[5]."</td>";
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
	.tr_filter_hide {
		display: none;
	}
	.page_main {
		font-weight:bold;
	}
</style>
<?
}
?>
<script>
	$(window).load(function()
	{
		$(".pagination a:first").click();
	});
</script>