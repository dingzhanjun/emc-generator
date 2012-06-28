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

function check_config_form()
{
    var jobboard = $("#config_jobboard_id").val();
    var trucktype = $("#config_truck_type").val();
    var kt = true;
    if (jobboard == null)
    {
        $("#config_jobboard_error").html("<ul><li>Require</li></ul>");
        kt = false;
    }
     
    if (trucktype == null)
    {
        $("#config_truck_type_error").html("<ul><li>Require</li></ul>");
        kt = false;
    }
    
    return kt;
}
</script>
<?php if (isset($config_id)) { ?>
<h1>Edit Config</h1>
<?php } else { ?>
<h1>Create Config</h1>
<?php } ?>
<div id="CreateForm" class='backend_form'>
  <form id="createForm" action="<?php if(isset($config_id))
                                          echo url_for('@config_edit?config_id='.$config_id);
                                       else 
                                         echo  url_for('@config_create'); ?> " method="post" onsubmit="return check_config_form()">
    <table>
            <th>
                <label for="config_jobboard_id">Website</label>
            </th>
            <td>
                <div id="config_jobboard_error"></div>
                 <select name="jobboard[]" multiple="multiple" id="config_jobboard_id">
                   <option value="0">All</option>
                    <?php
                    foreach($jobboards as $jobboard) {
                    ?>
                        <option value="<?php echo $jobboard->id?>" 
                        <?php foreach($jobboard_configs as $jobboard_config)
                            {
                                if($jobboard->id == $jobboard_config->jobboard_id) 
                                  echo 'selected="selected"';
                            } 
                        ?>><?php echo $jobboard->name ?></option>
                    <?php              
                    }
                ?>
            </td>
        </select>
        </tr>
         <tr>
            <th>
                <label for="config_truck_type">Truck Type</label>
            </th>
            <td>
                <div id="config_truck_type_error"></div>
                <select name="trucktype[]" multiple="multiple" id="config_truck_type">     
                
                    <?php
                    foreach($trucks as $truck)
                     {
                    ?>
                        <option value="<?php echo $truck->id?>" 
                        <?php foreach($config_trucks as $config_truck)
                        {
                            if($truck->id == $config_truck->truck_id) echo 'selected="selected"';
                        } 
                        ?>><?php echo $truck->name ?></option>
                    <?php              
                    }
                ?>
            </td>
        </select>
        </tr>
        <tr>
      <?php echo $config_form ?>
      <tr><th>
      <?php 
      if (isset($config_id))
      {
      ?> 
        <input  id="fs" type="submit" value="<?php echo "Save" ?>"/>
      <?php 
      }
      else
      { 
      ?> 
        <input  id="fs" type="submit" value="<?php echo "Create" ?>"/>
      <?php 
      }
      ?>  
      </th></tr>
    </table>
  </form>
</div>
