<?php
class GofmiGeneratorTask extends sfBaseTask
{
  public function configure()
  {
    $this->namespace = 'generators';
    $this->name      = 'gofmi';
    $this->addOptions(array(
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            new sfCommandOption('exp', null, sfCommandOption::PARAMETER_OPTIONAL, 'The new expiration date', ''),
            ));

    //$this->addArgument('config', sfCommandArgument::REQUIRED, 'The config id');
  }
 
  
  public function execute($arguments = array(), $options = array())
  {
    // initialize database connection  
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    date_default_timezone_set('America/Phoenix');
    $client = new WebFormClient();

    // getting config
	//$config_id = $arguments['config'];
	
	$generator = new GofmiGenerator('Gofmi');
	$generator->execute();
	
	$loads = $generator->getLoads();
	$count_loads = 0;
	foreach ($loads as $item) {
		$count_loads ++;
		if ($count_loads > 150) 
			break;
		$this->addLoads($item);
	}
  }


  private function addLoads($items)
  {
	if (!preg_match('#An expanded search found#', $items[0], $match)) {
		$loads_values = $items;
		unset($loads_values[0]);
		$hash = md5(json_encode($loads_values));
		$loads = Doctrine_Core::getTable('Loads')->findOneByHash($hash);
		if ($loads) {
			$this->logSection('info', 'Loads found but exists !');
		} else {
			$loads = new Loads();
			$loads->jobboard_id = Doctrine_Core::getTable('Jobboard')->findOneByName('Gofmi')->id;
			
			$loads->created_at = date(DATE_ISO8601);
			$date = $items[1];
			$loads->date = date('Y-m-d', strtotime($date));
			$loads->truck_type = $items[2];
			$loads->origin = $items[3];
			$loads->destination = $items[4];
			$loads->loads_type = $items[5];
			$loads->contact = $items[6];
			$loads->hash = $hash;
			$this->logSection('info', 'Found new loads with hash code '.$hash);
			$loads->save();

		}
	}
  }
}
?>