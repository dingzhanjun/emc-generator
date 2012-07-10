<?php
class GetloadedGeneratorTask extends sfBaseTask
{
  public function configure()
  {
    $this->namespace = 'generators';
    $this->name      = 'getloaded';
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
	$generator = new GetloadedGenerator($config_id, 'Getloaded');
	$generator->execute();
	$loads = $generator->getLoads();
	//foreach ($loads as $item)
		//$this->addLoads($item);
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
			$loads->jobboard_id = Doctrine_Core::getTable('Jobboard')->findOneByName('Truckersedge')->id;
			$age = preg_split('/:/', $items[0]);
			$loads->created_at = date(DATE_ISO8601, strtotime('-'.$age[0].' hour -'.$age[1].' minutes'));
			$date = date('Y').'-'.$items[1];
			$date = str_replace('/', '-', $date);
			$loads->date = date('Y-m-d', strtotime($date));
			$loads->truck_type = $items[2];
			$loads->loads_type = $items[3];
			$loads->origin_radius = $items[4];
			$loads->origin = $items[5];
			$loads->distance = $items[6];
			$loads->destination = $items[7];
			$loads->contact = $items[9];
			$loads->company = $items[13];
			$loads->hash = $hash;
			$this->logSection('info', 'Found new loads with hash code '.$hash);
			$loads->save();
		}
	}
  }
}
?>