<?php
class LandstarbrokerGeneratorTask extends sfBaseTask
{
  public function configure()
  {
    $this->namespace = 'generators';
    $this->name      = 'landstarbroker';
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
	
	$generator = new LandstarbrokerGenerator($config_id, 'Landstarbroker');
	$generator->execute();
	
	$loads = $generator->getLoads();
	foreach ($loads as $item) {
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
		$str = "";
		if ($loads) {
			$this->logSection('info', 'Loads found but exists !');
		} else {
			$loads = new Loads();
			$loads->jobboard_id = Doctrine_Core::getTable('Jobboard')->findOneByName('Landstarbroker')->id;
			$loads->created_at = date(DATE_ISO8601);
			$date = explode("/", strip_tags(trim($items[3])));
			$loads->date = date('Y-m-d', strtotime($date[0]."/".$date[1]."/".date("Y")));
			$loads->truck_type = $items[6];
			$location = trim($items[4]);
			$location = preg_replace('/\s+/',' ', $location);
			$location = preg_replace("/[^A-Za-z0-9, ]/i", "+", $location);
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
			$loads->origin = trim($location[0]);
			$loads->destination = trim($destination);
			$loads->distance = $items[5];
			$loads->contact = $items[2];
			$loads->company = $items[0];
			
			$loads->hash = $hash;
			$this->logSection('info', 'Found new loads with hash code '.$hash);
			$loads->save();
		}
	}
  }
}
?>