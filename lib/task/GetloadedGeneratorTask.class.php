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
	foreach ($loads as $item)
		$this->addLoads($item);
  }


  private function addLoads($items)
  {	
	$loads_values = $items;
	unset($loads_values[0]);
	$hash = md5(json_encode($loads_values));
	$loads = Doctrine_Core::getTable('Loads')->findOneByHash($hash);
	if ($loads) {
		$this->logSection('info', 'Loads found but exists !');
	} else {
		$loads = new Loads();
		$loads->jobboard_id = Doctrine_Core::getTable('Jobboard')->findOneByName('Getloaded')->id;
		$age = explode(' ', $items[23]);
		if ($age[2] == 'pm') 
			$age[1] += 12;
		$loads->created_at = date("Y-m-d H:i", strtotime(date("Y").$age[0]." ".$age[1].":00"));
		
		$date = date('Y').'/'.$items[7];
		$date = str_replace('/', '-', $date);
		$loads->date = date('Y-m-d', strtotime($date));
		$cities = preg_split("/\sto\s/", $items[1]);
		$loads->origin = trim($cities[0]);
		$loads->origin_radius = $items[9];
		$loads->destination = trim($cities[1]);
		$loads->truck_type = $items[25];
		$loads->loads_type = $items[13];
		$loads->distance = $items[3];
		$loads->contact = $items[19];
		$loads->company = $items[17];
		$loads->hash = $hash;
		$this->logSection('info', 'Found new loads with hash code '.$hash);
		$loads->save();
	}
  }
}
?>