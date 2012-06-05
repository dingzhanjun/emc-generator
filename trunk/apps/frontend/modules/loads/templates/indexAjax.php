<?php use_helper('Url') ?>
<?php if (isset($pager)): ?>
  <table cellspacing="0" class="root_table">
    <tbody>
      <tr>
		  
        <th>
			<?php echo link_to_function('<span>Website</span>', 'sort(\'jobboard_id\', 1)') ?>
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
           	<?php echo link_to_function('<span>Age</span>', 'sort(\'created_at\', 1)') ?>
        </th>
		<th>
            <?php echo link_to_function('<span>Pickup Date</span>', 'sort(\'date\', 1)') ?>
        </th>
		<th>
           	<?php echo link_to_function('<span>Truck type</span>', 'sort(\'truck_type\', 1)') ?>
        </th>
		<th>
           	<?php echo link_to_function('<span>Loads type</span>', 'sort(\'truck_type\', 1)') ?>
        </th>
		<th>
           	<?php echo link_to_function('<span>Contact</span>', 'sort(\'contact\', 1)') ?>
        </th>
		<th>
           	<?php echo link_to_function('<span>Company</span>', 'sort(\'company\', 1)') ?>
        </th>
		<th>
           	<?php echo link_to_function('<span>Distance (miles)</span>', 'sort(\'distance\', 1)') ?>
        </th>
      </tr>

	<?php foreach ($pager->getResults() as $loads) :?>
    <tr>
        <td style="text-align:center"><?php echo Doctrine_Core::getTable('Jobboard')->find($loads->jobboard_id)->name	?></td>
		<td style="text-align:center"><?php echo $loads->origin ?></td>
		<td style="text-align:center"><?php echo $loads->origin_radius ?></td>
		<td style="text-align:center"><?php echo $loads->destination ?></td>
		<td style="text-align:center"><?php echo $loads->destination_radius ?></td>
		<td style="text-align:center"><?php echo $loads->getCurrentLoadsAge() ?></td>
		<td style="text-align:center"><?php echo $loads->date ?></td>
		<td style="text-align:center"><?php echo $loads->truck_type ?></td>
		<td style="text-align:center"><?php echo $loads->loads_type ?></td>
		<td style="text-align:center"><?php echo $loads->contact ?></td>
		<td style="text-align:center"><?php echo $loads->company ?></td>
		<td style="text-align:center"><?php echo $loads->distance ?></td>
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