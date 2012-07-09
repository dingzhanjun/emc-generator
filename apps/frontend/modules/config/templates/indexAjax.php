<?php use_helper('Url') ?>
<?php if (isset($pager)): ?>
  <table cellspacing="0" class="root_table">
    <tbody>
      <tr>		  
        <th>
			<span>Website</span>
        </th>
		<th>
			<span>Truck Type</span>
        </th>
		<th>
           	<?php echo link_to_function('<span>Max Loads Age</span>', 'sort(\'max_age\', 1)') ?>
        </th>
		<th>
           	<?php echo link_to_function('<span>Origin</span>', 'sort(\'origin\', 1)') ?>
        </th>
			<th>
	           	<?php echo link_to_function('<span>Origin Radius</span>', 'sort(\'origin_radius\', 1)') ?>
	        </th>
			<th>
	           	<?php echo link_to_function('<span>Destination</span>', 'sort(\'destination\', 1)') ?>
	        </th>
		<th>
           	<?php echo link_to_function('<span>Destination Radius</span>', 'sort(\'destination_radius\', 1)') ?>
        </th>
		<th>
           	<?php echo link_to_function('<span>Length</span>', 'sort(\'length\', 1)') ?>
        </th>
		<th>
           	<?php echo link_to_function('<span>Weight</span>', 'sort(\'weight\', 1)') ?>
        </th>
		<th>
            <?php echo link_to_function('<span>Frequence</span>', 'sort(\'frequence\', 1)') ?>
        </th>
        <th>
           <span>Edit Delete</span></span>
        </th>
        
      </tr>

	<?php foreach ($pager->getResults() as $config) :?>
    <tr>
        <td style="text-align:center"><?php $jobboard_configs = $config->JobboardConfigs; foreach ($jobboard_configs as $jobboard_config) echo $jobboard_config->Jobboard->alias.'; ' ?></td>
		<td style="text-align:center"><?php $truck_types = $config->ConfigTrucks; foreach ($truck_types as $truck_type) echo $truck_type->Truck->name.'; ' ?></td>
		<td style="text-align:center"><?php echo $config->max_age ?></td>
		<td style="text-align:center"><?php echo $config->origin ?></td>
		<td style="text-align:center"><?php echo $config->origin_radius ?></td>
		<td style="text-align:center"><?php echo $config->destination ?></td>
		<td style="text-align:center"><?php echo $config->destination_radius ?></td>
		<td style="text-align:center"><?php echo $config->length ?></td>
		<td style="text-align:center"><?php echo $config->weight ?></td>
		<td style="text-align:center"><?php echo $config->frequence ?></td>
        <td style="text-align:center">
            <?php 
                echo link_to('Edit', '@config_edit?config_id='.$config->id);
        		echo "<br/>";
        		echo link_to('Delete', '@config_delete?config_id='.$config->id);
    		?>
		</td>
    </tr>
  <?php endforeach ?>
    </tbody>
  </table>

  <?php if ($pager->haveToPaginate()): ?>
    <div class="pagination">
      <?php if (!$pager->isFirstPage()): ?>
        <?php echo link_to_function("First page", 'listReload('.$pager->getFirstPage().')', array('class' => 'page')) ?>
        <?php echo link_to_function("Prev", 'listReload('.$pager->getPreviousPage().')', array('class' => 'page')) ?>
      <?php endif ?>

      <?php foreach ($pager->getLinks() as $link): ?>
        <?php if ($link == $pager->getPage()): ?>
          <span class="page selected"><?php echo $link ?></span>
        <?php else: ?>
          <?php echo link_to_function($link, 'listReload('.$link.')', array('class' => 'page')) ?>
        <?php endif ?>
      <?php endforeach ?>

      <?php if (!$pager->isLastPage()): ?>
        <?php echo link_to_function("Next", 'listReload('.$pager->getNextPage().')', array('class' => 'page')) ?>
        <?php echo link_to_function("Last page", 'listReload('.$pager->getLastPage().')', array('class' => 'page')) ?>
      <?php endif ?>
    </div>
  <?php endif ?>
<?php endif ?>