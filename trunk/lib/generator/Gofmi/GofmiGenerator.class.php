<?php
// class generator to send a searching request to a given website then take back the loads
class GofmiGenerator
{
	protected $jobboard_name;
	protected $loads= array();
	
	public function __construct($jobboard_name)
	{
		$this->jobboard_name = $jobboard_name;
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
		
		// We don't need config for this generator
		/*
		$config = Doctrine_Core::getTable('Config')->find($this->config_id);
		if (!$config) {
			echo ">>>>>Error<<<<< Config not found\n";
			exit;
		}
		*/
		
		$jobboard = Doctrine_Core::getTable('Jobboard')->findOneByName($this->jobboard_name);
		if (!$jobboard) {
			echo ">>>>>Error<<<<< Jobboard not found\n";
			exit;
		}
		
		// ok everything is ready, lets go
		$tag = array();
		$base_url = $jobboard->address;
		$client->get($base_url);

		// parsing reponse
		$doc = new DOMDocument();
	    @$doc->loadHTML($client->getBody());
	    $xpath = new DOMXpath($doc);

	    $nodes = $xpath->query('//*[@id="wooMod"]/table/tbody/tr');
		foreach ($nodes as $node) {
			$tds = $xpath->query('td', $node);
			$items = array();
	        foreach ($tds as $td)
	            $items[] = trim($td->nodeValue);
			$this->addLoads($items);
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