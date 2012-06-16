<?php
class TruckersedgeGeneratorTask extends sfBaseTask
{
  public function configure()
  {
    $this->namespace = 'generators';
    $this->name      = 'truckersedge';
    $this->addOptions(array(
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            new sfCommandOption('exp', null, sfCommandOption::PARAMETER_OPTIONAL, 'The new expiration date', ''),
            ));

    $this->addArgument('config', sfCommandArgument::REQUIRED, 'The config id');
  }

  public function create_log($filename, $content) {
	$file = dirname(dirname(dirname(__FILE__))).'/log/'.$filename;
	file_put_contents($file, $content);
  }
 
  public function execute($arguments = array(), $options = array())
  {
    // initialize database connection  
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    date_default_timezone_set('Asia/Bangkok');
    $client = new WebFormClient();

    // getting config
	$config_id = $arguments['config'];
	$config = Doctrine_Core::getTable('Config')->find($config_id);
	if (!$config) {
		$this->logSection('Error', 'Config not found');
		exit;
	}

	$this->logSection('info', 'Executing generator with config: '.$config->id);
    $client->get('https://www.truckersedge.net/a/secure/login.aspx?app=truckersedge');
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
	$jobboard = Doctrine_Core::getTable('Jobboard')->findOneByName('Truckersedge');
    $tag['login']['ctl00$cphMain$txtUserName'] = $jobboard->username;
    $tag['login']['ctl00$cphMain$txtPassword'] = $jobboard->password;
    $tag['login']['ctl00$cphMain$hfUserTimezoneOffset'] = -420;
    $client->fill($tag['login']);
    $client->post('https://www.truckersedge.net/a/secure/login.aspx?app=truckersedge&');
    $client->get('http://www.truckersedge.net/a/app/default.aspx');
    $this->create_log('login'.date(DATE_ISO8601).'.html', $client->getBody());
    if (!preg_match('#TruckersEdge.net - My Overview#', $client->getBody(), $match)) {
        $this->logSection('info', "Login fail");
        exit;
    }
    $this->logSection('info', 'login success !!! Redirecting to seaching page');

    $client->get('http://www.truckersedge.net/a/app/Search.aspx');
	$this->create_log('searching_page'.date(DATE_ISO8601).'.html', $client->getBody());
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
	$tag['search']['ctl00$cphMain$txtDateFrom'] = date('d/m/y', strtotime($config->from_date));
	$tag['search']['ctl00$cphMain$txtDateTo'] = date('d/m/y', strtotime($config->to_date));
	$tag['search']['ctl00$cphMain$locOrigin$hdnNGL'] = '';
	$tag['search']['ctl00$cphMain$ddlLoadType'] = $this->mapping($config->loads_type, array( '0'  =>  'Both',
																							 '1'  =>  'Full',
																							 '2'  =>  'Partial'));
	$client->fill($tag['search']);
	$client->post('http://www.truckersedge.net/a/app/Search.aspx');
	$this->create_log('step1'.date(DATE_ISO8601).'.html', $client->getBody());
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


  private function mapping($value, $mapping)
  {
	if (array_key_exists($value, $mapping))
		return $mapping[$value];
	else return null;
  }
}
?>