<?php
// class generator to send a searching request to a given website then take back the loads
class GetloadedGenerator
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
	    // no longer create log
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
		
		$client->setLogPrefix(dirname(dirname(dirname(dirname(__FILE__)))).'/log/'.$this->jobboard_name.' '.date(DATE_ISO8601));
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
		$headers = $client->getHeaders();
		$headers[0] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_4) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.47 Safari/536.11';
		$client->setHeaders($headers);
		$client->get($base_url);
		$client->load(array('id' => 'input', 'name' => 'input'));
        $client->validate(array(
                        'username' => 'input-text',
                        'password' => 'input-password',
                        'autosave' => 'input-checkbox',
                        ));
        $tag['login']['username'] = $jobboard->username;
        $tag['login']['password'] = $jobboard->password;
        $client->fill($tag['login']);
        $client->setHeaders($headers);
        $client->post('https://app.getloaded.com/auth/login.gl');
        // This server allows only 1 access at the same time, we need to confirm our login
        if (preg_match('#gkey=[^"]*#', $client->getBody(), $match)) {
            $confirm_url ='http://app.getloaded.com/auth/multiplelogin.gl?action=login&'.$match[0];
            echo "Confirm_url: ".$confirm_url."\n";
            $client->setHeaders($headers);
            $client->get($confirm_url);
        }
        

        $client->setHeaders($headers);
        $client->get('http://member.getloaded.com/dashboard.php');
        $client->load(array('id' => 'load_search_form'));
        $client->validate(array(
                        'search'              => 'input-hidden',
                        'sc'                  => 'input-hidden',
                        'ss'                  => 'input-hidden',
                        'dc'                  => 'input-hidden',
                        'ds'                  => 'input-hidden',
                        'smask'               => 'input-hidden',
                        'dmask'               => 'input-hidden',
                        'search_type'         => 'input-hidden',
                        'unknown_start'       => 'input-hidden',
                        'unknown_dest'        => 'input-hidden',
                        'langDayNames'        => 'input-hidden',
                        'langDayNamesMin'     => 'input-hidden',
                        'langMonthNames'      => 'input-hidden',
                        'langMonthNamesShort' => 'input-hidden',
                        'prevTxt'             => 'input-hidden',
                        'nextTxt'             => 'input-hidden',
                        'curTxt'              => 'input-hidden',
                        'bttmTxt1'            => 'input-hidden',
                        'bttmTxt2'            => 'input-hidden',
                        'starting_point'      => 'input-text',
                        'destination_point'   => 'input-text',
                        'pickup_start_date'   => 'input',
                        'ttype'               => 'select',
                        ));
        
        $tag['search'] = $client->getData();
        $tag['search']['search'] = 'Find Loads';
        // now we havent supported multistates jet
        $full_origin = str_replace(' ', '', $config->origin);
        $full_origin = preg_split('#,#', $full_origin);
        $tag['search']['sc'] = strtoupper($full_origin[0]);
        $tag['search']['ss'] = strtoupper($full_origin[1]);
        
        $full_destination = str_replace(' ', '', $config->destination);
        $full_destination = preg_split('#,#', $full_destination);
        $tag['search']['dc'] = strtoupper($full_destination[0]);
        $tag['search']['ds'] = strtoupper($full_destination[1]);
        $tag['search']['smask'] = 0;
        $tag['search']['dmask'] = 0;
        $tag['search']['langDayNames'] = 'Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday';
        $tag['search']['langDayNamesMin'] = 'Sun,Mon,Tue,Wed,Thu,Fri,Sat';
        $tag['search']['langMonthNames'] = 'January,February,March,April,May,June,July,August,September,October,November,December';
        $tag['search']['langMonthNamesShort'] = 'Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec';
        $tag['search']['prevTxt'] = 'Prev';
        $tag['search']['nextTxt'] = 'Next';
        $tag['search']['curTxt'] = 'Today';
        $tag['search']['bttmTxt1'] = 'Clear';
        $tag['search']['bttmTxt2'] = 'Close';
        $tag['search']['starting_point'] = $config->origin;
        $tag['search']['destination_point'] = $config->destination;
        $tag['search']['pickup_start_date'] = date('m/d/Y');
        $tag['search']['ttype'] = 64;
        
        $client->fill($tag['search']);
        $client->setHeaders($headers);
        $client->post('http://member.getloaded.com/search/load_search.php');

		// parsing reponse
		$doc = new DOMDocument();
	    @$doc->loadHTML($client->getBody());
	    $xpath = new DOMXpath($doc);

	    $nodes = $xpath->query('//*[@class="result page_1 freight_id"]');
		foreach ($nodes as $node) {
			$tds = $xpath->query('div/table/tr/td/span|em|a', $node);
			$items = array();
	        foreach ($tds as $td)
	            $items[] = trim($td->nodeValue);
	        var_dump($items);
			//$this->addLoads($items);
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