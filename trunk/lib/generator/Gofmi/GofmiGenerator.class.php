<?php
// class generator to send a searching request to a given website then take back the loads
class GofmiGenerator
{
	protected $config_id;
	protected $jobboard_name;
	protected $loads= array();
	
	public function __construct($config_id, $jobboard_name)
	{
		$this->jobboard_name = $jobboard_name;
		$this->config_id = $config_id;
	}
	

	private function create_log($filename, $content) {
		$file = dirname(dirname(dirname(dirname(__FILE__)))).'/log/'.$filename;
		file_put_contents($file, $content);
	}
	
	
	private function initialize()
	{
	    // TOTO make it to a sfconfig get
	}
	
	
	private function mapping($value, $mapping)
	{
		if (array_key_exists($value, $mapping))
			return $mapping[$value];
		else return null;
	}
	
	
	public function execute()
	{
		$this->initialize();
		$client = new WebFormClient();
		
		$client->setLogPrefix(dirname(dirname(dirname(dirname(__FILE__)))).'/log/'.$this->jobboard_name.' '.date("Y-m-d H-i-s O", time()));

		$config = Doctrine_Core::getTable('Config')->find($this->config_id);
		if (!$config) {
			$notify_error = new NotifyError("Gofmi - Config not found\n");
			$notify_error->execute();
			return;
		}
		
		$jobboard = Doctrine_Core::getTable('Jobboard')->findOneByName($this->jobboard_name);
		if (!$jobboard) {
			$notify_error = new NotifyError("Gofmi - Jobboard not found\n");
			$notify_error->execute();
			return;
		}
		
		try {
			// ok everything is ready, lets go
			$tag = array();
			$base_url = $jobboard->address;
			$client->get($base_url);
	
			// parsing reponse
			$config_trucks = Doctrine_Query::create()
				->from('ConfigTruck cf')
				->addWhere('cf.config_id = ?', $config->id)
				->execute();
			$trucks = array();
			foreach ($config_trucks as $config_truck) {
				switch ($config_truck->truck_id) {
					case 1:
					 $trucks[] = 'Dry Bulk';
					 break;
					case 2:
					 $trucks[] = 'Container';
					 break;
					case 3:
					 $trucks[] = 'Deck';
					 break;
					case 4:
					 $trucks[] = 'Flatbed';
					 break;
					case 7:
					 $trucks[] = 'Reefer';
					 break;
					case 8:
					 $trucks[] = 'Van';
					 break;
					case 9:
					 $trucks[] = 'Tanker';
					 break;
				}
			}
	
			$doc = new DOMDocument();
			@$doc->loadHTML($client->getBody());
			$xpath = new DOMXpath($doc);
	
			$nodes = $xpath->query('//*[@id="wooMod"]/table/tbody/tr');
			foreach ($nodes as $node) {
				$tds = $xpath->query('td', $node);
				$items = array();
				foreach ($tds as $td)
					$items[] = trim($td->nodeValue);
				$in_config = true;
				
				$type_is_ok = false;
				foreach (explode(" ", $items[2]) as $truck) {
					if (in_array($truck, $trucks))
						$type_is_ok = true;
				}
						
				$loads_type_is_ok = false;
				if ($config->loads_type != 0) {
					if (($config->loads_type == 1 && $items[5] == 'Full') || ($config->loads_type == 2 && $items[5] == 'Partial'))
						$loads_type_is_ok = true;
				} else $loads_type_is_ok = true;
				
				$in_config = $type_is_ok && $loads_type_is_ok;
				if ($in_config)
					$this->addLoads($items);
			}
		} catch (Exception $ex) {
			$notify_error = new NotifyError("Gofmi - Jobboard have been changed. Please contact to VTNS\n");
			$notify_error->execute();
		}
	}
	
	
	private function addLoads($items)
	{
		$this->loads[] = $items;
	}
	
	
	public function getLoads()
	{
		return $this->loads;
	}
}
?>