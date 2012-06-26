<?php
// class generator to send a searching request to a given website then take back the loads
class LandstarbrokerGenerator
{
	protected $config_id;
	protected $jobboard_name;
	protected $loads= array();
	
	public function __construct($config_id, $jobboard_name)
	{
		$this->config_id = $config_id;
		$this->jobboard_name = $jobboard_name;
	}
	

	private function create_log($filename, $content) {
		$file = dirname(dirname(dirname(dirname(__FILE__)))).'\\log\\'."test.html";
		//echo $file;
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
		
		
		
		$config = Doctrine_Core::getTable('Config')->find($this->config_id);
		if (!$config) {
			echo ">>>>>Error<<<<< Config not found\n";
			exit;
		}
		
		
		$jobboard = Doctrine_Core::getTable('Jobboard')->findOneByName($this->jobboard_name);
		if (!$jobboard) {
			echo ">>>>>Error<<<<< Jobboard not found\n";
			exit;
		}
		
		// ok everything is ready, lets go
		
		$tag = array();
		$base_url = $jobboard->address;
		
		// searching loads
		$client->get($base_url);
		$this->create_log($jobboard->name.'-searching-'.date(DATE_ISO8601).'.html', $client->getBody());		
		
		$tag['search']['Action'] = "Search";
		$tag['search']['UserKey'] = '';
		$tag['search']['hidHomeCity'] = '';
		$tag['search']['hidHomeState'] = '';
		$tag['search']['hidCityHasCounty'] = '';
		
		
		
		$tag['search']['company'] = '';
		
		$origin = explode(',', $config->origin);
		$city = $origin[0];
		$state = $origin[1];
		$origin_radius = $config->origin_radius;
		for ($i = 0; $i < 3-strlen($origin_radius); $i++)
			$origin_radius = "0".$origin_radius;
			
		$tag['search']['Origcity'] = $city; // City
		$tag['search']['origstate'] = $state; //state
		$tag['search']['origdist'] = (($config->origin_radius)?$origin_radius:"000"); // distance
		$tag['search']['origcountry'] = 'US'; // Constant
		
		$city = "";
		$state = "";
		if ($config->destination) {
			$destination = explode(',', $config->destination);
			$city = $destination[0];
			$state = $destination[1];
		}
		
		$destination_radius = $config->destination_radius;
		for ($i = 0; $i < 3-strlen($destination_radius); $i++)
			$destination_radius = "0".$destination_radius;
		
		$tag['search']['destcity'] = $city; 
		$tag['search']['deststate'] = $state; 
		$tag['search']['destdist'] = (($config->destination_radius)?$destination_radius:"000");
		$tag['search']['destcountry'] = 'US'; // Constant
		
		$tag['search']['OrderBy'] = 'PickupDateTime, OriginState, OriginCity'; // Constant
		
		$tag['search']['NumRecs'] = '250'; // Constant
		
		$tag['search']['terminal'] = ''; 
		$tag['search']['Pckdate'] = '=';  // Pickup date
		$tag['search']['datepickup'] = ''; 
		$tag['search']['Deldate'] = '='; 
		$tag['search']['datedelivery'] = ''; 
		$tag['search']['minweight'] = (($config->weight != 0)?$config->weight:''); 
		$tag['search']['maxweight'] = (($config->weight != 0)?$config->weight:'');
		$tag['search']['CSA'] = ''; 
		
		// Config truck 
		
		// only 2 truck types is accepted for this website, we take the first one
        $config_trucks = Doctrine_Query::create()->from('ConfigTruck cf')->addWhere('cf.config_id = ?', $config->id)->execute();
		$count = 0;
        foreach ($config_trucks as $config_truck) {
			$count ++;
            $truck_id = $config_truck->Truck->id;
            // will be remapping later
            $tag['search']['Trlr_grp'.$count] = $this->mapping($truck_id, array(
                                '0' =>  '', // all
                                '1' =>  '', // Dry Bulk
                                '2' =>  'CONT', // containers missing
                                '3' =>  '', // Deck Standard
                                '4' =>  'FLAT', // Flatbed
                                '5' =>  '', // Decks, Specialized
                                '6' =>  '', // other Equipment
                                '7' =>  '',
                                '8' =>  'VAN',
                                '9' =>  '',
                                '10' => '',
                                '11' => '',
            ));
			if ($count >= 2)
            	break;
        }
		
		// if we don't choose truck type, we must set default value for it 
		while ($count <= 2)
		{
			$tag['search']['Trlr_grp'.$count] = '';
			$count ++;
		}
		
		$tag['search']['jit'] = ''; 
		$tag['search']['selTrailerType1'] = ''; 
		$tag['search']['selTrailerType2'] = ''; 
		$tag['search']['ldType'] = ''; 
		$tag['search']['Search'] = 'Search'; 
		
		$client->post('http://www.landstarbroker.com/Loads/LoadCriteria.asp', $tag['search']);
		$this->create_log($jobboard->name.'-loads-'.date(DATE_ISO8601).'.html', $client->getBody());
		
		// parsing reponse
		$doc = new DOMDocument();
	    @$doc->loadHTML($client->getBody());
	    $xpath = new DOMXpath($doc);

	    $nodes = $xpath->query('//*[@id="Form1"]/table/tr');
		$count = 0;
		
		foreach ($nodes as $node) {
			$count ++;
			if ($count > 1)
			{
				$tds = $xpath->query('td', $node);
				$items = array();
				foreach ($tds as $td) {
					$items[] = trim($td->nodeValue);
				}
				//var_dump($items);
				$this->addLoads($items);
			}
		}
		
		
	}
	
	
	private function addLoads($items)
	{
		if (!preg_match('#An expanded search found#', $items[0], $match)) {
			$this->loads[] = $items;
		}
	}
	
	
	public function getLoads()
	{
		return $this->loads;
	}
}
?>