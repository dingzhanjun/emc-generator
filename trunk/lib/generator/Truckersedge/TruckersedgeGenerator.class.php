<?php
// class generator to send a searching request to a given website then take back the loads
class TruckersedgeGenerator
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
		$client->get($base_url);
		$client->load(array('id' => 'aspnetForm', 'name' => 'aspnetForm'));
	    $client->validate(array(
	                    '__LASTFOCUS'                        => 'input-hidden',
	                    'VAM_Group'                          => 'input-hidden',
	                    '__VIEWSTATE'                        => 'input-hidden',
	                    '__EVENTTARGET'                      => 'input-hidden',
	                    '__EVENTARGUMENT'                    => 'input-hidden',
	                    '__EVENTVALIDATION'                  => 'input-hidden',
	                    'ctl00$cphMain$txtUserName'          => 'input-text',
	                    'ctl00$cphMain$txtPassword'          => 'input-password',
	                    'ctl00$cphMain$chkRememberIdentity'  => 'input-checkbox',
	                    'ctl00$cphMain$btnSubmit'            => 'input-submit',
	                    'NoJS'                               => 'input-hidden',
	                    'ctl00$cphMain$hfUserTimezoneOffset' => 'input-hidden',
	                    ));
	    $client->removeField('NoJS');
	    $tag['login'] = $client->getData();
	
	    // login step
		$tag['login']['ctl00$cphMain$txtUserName'] = $jobboard->username;
	    $tag['login']['ctl00$cphMain$txtPassword'] = $jobboard->password;
	    $tag['login']['ctl00$cphMain$hfUserTimezoneOffset'] = -420;
		$client->fill($tag['login']);
	    $client->post('https://www.truckersedge.net/a/secure/login.aspx?app=truckersedge&');
	    $client->get('http://www.truckersedge.net/a/app/default.aspx');
	    $this->create_log($jobboard->name.'-login-'.date(DATE_ISO8601).'.html', $client->getBody());
	    if (!preg_match('#TruckersEdge.net - My Overview#', $client->getBody(), $match)) {
	        $this->logSection('info', "Login fail");
	        exit;
	    }
		
		// second step, searching loads
		$client->get('http://www.truckersedge.net/a/app/Search.aspx');
		$this->create_log($jobboard->name.'-searching-'.date(DATE_ISO8601).'.html', $client->getBody());
		$client->load(array('id' => 'aspnetForm', 'name' => 'aspnetForm'));
		$client->validate(array(
		                '__EVENTTARGET'                                             => 'input-hidden',
		                '__EVENTARGUMENT'                                           => 'input-hidden',
		                'ctl00_cphMain_sManager_HiddenField'                        => 'input-hidden',
		                'VAM_JSE'                                                   => 'input-hidden',
		                'VAM_Group'                                                 => 'input-hidden',
		                '__VIEWSTATE'                                               => 'input-hidden',
		                '__EVENTVALIDATION'                                         => 'input-hidden',
		                'ctl00$cphMain$favoriteSearchesList$ddlFavorites'           => 'select',
		                'ctl00$cphMain$favoriteSearchesList$favoritePostBackTarget' => 'input-hidden',
		                'ctl00$cphMain$recentSearchesList$ddlRecentSearches'        => 'select',
		                'ctl00$cphMain$recentSearchesList$recentPostBackTarget'     => 'input-hidden',
		                'ctl00$cphMain$ddcTruckType$txtEntry'                       => 'input-text',
		                'ctl00$cphMain$locOrigin$txtLocationEntry'                  => 'input-text',
		                'ctl00$cphMain$locOrigin$btnCityRegionDirOpen'              => 'input-button',
		                'ctl00$cphMain$locOrigin$hdnNGL'                            => 'input-hidden',
		                'ctl00$cphMain$txtOriginRadius'                             => 'input-text',
		                'ctl00$cphMain$locDestination$txtLocationEntry'             => 'input-text',
		                'ctl00$cphMain$locDestination$btnCityRegionDirOpen'         => 'input-button',
		                'ctl00$cphMain$locDestination$hdnNGL'                       => 'input-hidden',
		                'ctl00$cphMain$txtDestinationRadius'                        => 'input-text',
		                'ctl00$cphMain$ddlLoadType'                                 => 'select',
		                'ctl00$cphMain$txtLength'                                   => 'input-text',
		                'ctl00$cphMain$txtWeight'                                   => 'input-text',
		                'ctl00$cphMain$txtDateFrom'                                 => 'input-text',
		                'ctl00$cphMain$txtDateTo'                                   => 'input-text',
		                'ctl00$cphMain$txtAge'                                      => 'input-text',
		                'ctl00$cphMain$btnSearch'                                   => 'input-submit',
		                'ctl00$cphMain$btnClear'                                    => 'input-button',
		                ));

		$client->removeField('ctl00$cphMain$btnClear');
		$tag['search'] = $client->getData();
		$tag['search']['VAM_JSE'] = 1;
		$tag['search']['ctl00$cphMain$locDestination$hdnNGL'] = '';
		$tag['search']['ctl00$cphMain$txtAge'] = $config->max_age;
		$tag['search']['ctl00$cphMain$locOrigin$txtLocationEntry'] = $config->origin;
		$tag['search']['ctl00$cphMain$txtOriginRadius'] = $config->origin_radius;
		$tag['search']['ctl00$cphMain$locDestination$txtLocationEntry'] = $config->destination;
		$tag['search']['ctl00$cphMain$txtDestinationRadius'] = $config->destination_radius;
		if ($config->type == 0) { // native run
			$tag['search']['ctl00$cphMain$txtDateFrom'] = date('d/m/y');
			$tag['search']['ctl00$cphMain$txtDateTo'] = date('d/m/y');
		} elseif ($config->type == 1) {
			$tag['search']['ctl00$cphMain$txtDateFrom'] = date('y/m/d', strtotime($config->from_date));
			$tag['search']['ctl00$cphMain$txtDateTo'] = date('y/m/d', strtotime($config->to_date));
		}
	
		$tag['search']['ctl00$cphMain$locOrigin$hdnNGL'] = '';
		$tag['search']['ctl00$cphMain$ddlLoadType'] = $this->mapping($config->loads_type, array( '0'  =>  'Both',
																								 '1'  =>  'Full',
																								 '2'  =>  'Partial'));
		$client->fill($tag['search']);
		$client->post('http://www.truckersedge.net/a/app/Search.aspx');
		$this->create_log($jobboard->name.'-loads-'.date(DATE_ISO8601).'.html', $client->getBody());
		
		// parsing reponse
		$doc = new DOMDocument();
	    @$doc->loadHTML($client->getBody());
	    $xpath = new DOMXpath($doc);

	    $nodes = $xpath->query('//*[@id="resultSet"]/tbody/tr');
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