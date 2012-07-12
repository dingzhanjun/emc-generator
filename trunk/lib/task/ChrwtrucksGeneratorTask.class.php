<?php
class ChrwtrucksGeneratorTask extends sfBaseTask
{
  public function configure()
   {
		$this->namespace = 'generators';
		$this->name      = 'chrwtrucks';
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
		
		$generator = new ChrwtrucksGenerator($config_id, 'Chrwtrucks');
		$generator->execute();
		
		$loads = $generator->getLoads();
        foreach ($loads as $item)
       	    $this->addLoads($item);
  }
  
  private function addLoads($items)
   {	
		$hash = md5(json_encode($items));
		$loads = Doctrine_Core::getTable('Loads')->findOneByHash($hash);
		if ($loads) {
			$this->logSection('info', 'Loads found but exists !');
		} 
		else {
			$loads = new Loads();
			$loads->jobboard_id = Doctrine_Core::getTable('Jobboard')->findOneByName('Chrwtrucks')->id;
			$loads->created_at = date(DATE_ISO8601);
            
            $date = $items[2];              
            $date = substr($date, 0, 12);
			$date = explode("/", strip_tags($date));
            $date[2] = trim(substr($date[2], 0, 4));
			$loads->date = date("Y-m-d", strtotime(trim($date[2]) . "-" . trim($date[0]) . "-" . trim($date[1])));
 
            $deadline = $items[5];
            $deadline = trim($deadline);
            $deadline = substr($deadline, 0, 12);
			$deadline = explode("/", strip_tags($deadline));
            $deadline[2] = trim(substr($deadline[2], 0, 4));        
            $loads->deadline = date("Y-m-d", strtotime(trim($deadline[2]) . "-" . trim($deadline[0]) . "-" . trim($deadline[1])));
            
			$loads->truck_type = $items[8];
			$loads->origin = $items[1];
			$loads->destination = $items[4];
            /*
			$loads->contact = $items[11];
            $loads->company = $items[9]." - ".$items[10];
			
			$distance = $items[12];
            $replace = array(".", ",");
            $distance = str_replace($replace,"",$distance);
            $loads->distance = $distance;
            */
            
            $origin_radius = $items[3]; 
            $origin_radius = strtolower($origin_radius);
            $origin_radius = trim(substr($origin_radius, 0, strrpos($origin_radius, "miles"))) ;
            if(strlen($origin_radius) != 0) 
                $loads->origin_radius = (int)$origin_radius ;
            
            $destination_radius = $items[6]; 
            $destination_radius = strtolower($destination_radius);
            $destination_radius = trim(substr($destination_radius, 0, strrpos($destination_radius, "miles"))) ;
            if(strlen($destination_radius) != 0)
                $loads->destination_radius = (int)$destination_radius ;
            
            $loads->hash = $hash;
			$this->logSection('info', 'Found new loads with hash code '.$hash);
			$loads->save();
		}
	
   }

}
?>