<?php
class TruckstopGeneratorTask extends sfBaseTask
{
  public function configure()
  {
    $this->namespace = 'generators';
    $this->name      = 'truckstop';
    $this->addOptions(array(
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            new sfCommandOption('exp', null, sfCommandOption::PARAMETER_OPTIONAL, 'The new expiration date', ''),
            ));

    $this->addArgument('config', sfCommandArgument::REQUIRED, 'The config id');
  }
 
  
  public function execute($arguments = array(), $options = array())
  {
    // initialize database connection  
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    date_default_timezone_set('America/Phoenix');
    $client = new WebFormClient();

    // getting config
	$config_id = $arguments['config'];
	$generator = new TruckstopGenerator($config_id, 'Truckstop');
	$generator->execute();
	$loads = $generator->getLoads();
	foreach ($loads as $item)
		$this->addLoads($item);
  }


  private function addLoads($items)
  {
      $loads_values = $items;
	  unset($loads_values[4]);
	  $hash = md5(json_encode($loads_values));
	  $loads = Doctrine_Core::getTable('Loads')->findOneByHash($hash);
      if ($loads) {
			$this->logSection('info', 'Loads found but exists !');
	  } else {
	      $loads = new Loads();
		  $loads->jobboard_id = Doctrine_Core::getTable('Jobboard')->findOneByName('Truckstop')->id;
		  $loads->created_at = date(DATE_ISO8601, strtotime('-'.$items[4].' hour'));
		  $loads->date = date('Y-m-d', strtotime($items[6]));
		  $loads->truck_type = 'Flatbed'; // by default
		  $loads->loads_type = $items[5];
		  $loads->origin_radius = $items[9];
		  $loads->origin = $items[7].' ,'.$items[8];
		  $loads->distance = ($items[20] != "0" ? $items[20] : $items[21]);
		  $loads->destination = $items[10].' ,'.$items[11];
		  $loads->contact = $items[2];
		  $loads->company = $items[18];
		  $loads->hash = $hash;
		  $this->logSection('info', 'Found new loads with hash code '.$hash);
		  $loads->save();
	  }
  }
}
?>