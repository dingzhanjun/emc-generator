<?php
// class generator to send a searching request to a given website then take back the loads
class TruckstopGenerator
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
		//$this->create_log($jobboard->name.'-login-'.date(DATE_ISO8601).'.html', $client->getBody());
		$client->load(array('id' => 'aspnetForm', 'name' => 'aspnetForm'));
        $client->validate(array(
                '__EVENTTARGET'                                       => 'input-hidden',
                '__EVENTARGUMENT'                                     => 'input-hidden',
                '__VIEWSTATE'                                         => 'input-hidden',
                '__EVENTVALIDATION'                                   => 'input-hidden',
                'ctl00_RadWindowManager_ClientState'                  => 'input-hidden',
                'ctl00$Navigation$LogoutButton'                       => 'input-button',
                'ctl00$ContentPlaceHolder$login$textUserName'         => 'input-text',
                'ctl00$ContentPlaceHolder$login$textCompanyAccount'   => 'input-text',
                'ctl00$ContentPlaceHolder$login$textPassword'         => 'input-password',
                'ctl00$ContentPlaceHolder$login$hiddenPassword'       => 'input-hidden',
                'ctl00$ContentPlaceHolder$login$checkSaveCredentials' => 'input-checkbox',
                'ctl00$ContentPlaceHolder$login$buttonLogin'          => 'input-submit',
                ));
        $client->removeField('ctl00$ContentPlaceHolder$login$checkSaveCredentials');
    	$client->removeField('ctl00$Navigation$LogoutButton');
	    $tag['login'] = $client->getData();

	    // login step
	    $tag['login']['ctl00$ContentPlaceHolder$login$textUserName'] = 'Howard DO';
		$tag['login']['ctl00$ContentPlaceHolder$login$textCompanyAccount'] = $jobboard->username;
	    $tag['login']['ctl00$ContentPlaceHolder$login$textPassword'] = $jobboard->password;
		$client->fill($tag['login']);
	    $client->post('http://truckstop.com/lite/');
	    $this->create_log($jobboard->name.'-login-'.date(DATE_ISO8601).'.html', $client->getBody());
	    
	    // authenticate by handle
	    if (preg_match("#This login is already in use by someone#", $client->getBody(), $match)) {
	        $client->load(array('id' => 'aspnetForm', 'name' => 'aspnetForm'));
            $client->validate(array(
                    '__LASTFOCUS'                                                     => 'input-hidden',
                    '__EVENTTARGET'                                                   => 'input-hidden',
                    '__EVENTARGUMENT'                                                 => 'input-hidden',
                    '__VIEWSTATE'                                                     => 'input-hidden',
                    '__EVENTVALIDATION'                                               => 'input-hidden',
                    'ctl00_RadWindowManager_ClientState'                              => 'input-hidden',
                    'ctl00$Navigation$LogoutButton'                                   => 'input-button',
                    'ctl00$ContentPlaceHolder$textHandle'                             => 'input-text',
                    'ctl00_ContentPlaceHolder_textHandleTip_ClientState'              => 'input-hidden',
                    'ctl00_ContentPlaceHolder_linkHandleHelpTip_ClientState'          => 'input-hidden',
                    'ctl00_ContentPlaceHolder_linkHandleHelpTipMouseOver_ClientState' => 'input-hidden',
                    'ctl00$ContentPlaceHolder$hiddenPassword'                         => 'input-hidden',
                    'ctl00$ContentPlaceHolder$buttonSubmit'                           => 'input-submit',
                    'ctl00$ContentPlaceHolder$checkSaveCredentials'                   => 'input-checkbox',
                    ));
            $client->removeField('ctl00$Navigation$LogoutButton');
            $tag['handle'] = $client->getData();
            $tag['ctl00$ContentPlaceHolder$checkSaveCredentials'] = 'on';
            $client->post('http://truckstop.com/AuthenticateByHandle.aspx?redirect=/Lite/FindFreight.aspx');
            $this->create_log($jobboard->name.'-authenHandle-'.date(DATE_ISO8601).'.html', $client->getBody());
	    } elseif (!preg_match('#Once you accept the Terms and Conditions, you will be taken to the application page#', $client->getBody(), $match)) {
	        $client->load(array('id' => 'aspnetForm', 'name' => 'aspnetForm'));
            $client->validate(array(
                    '__EVENTTARGET'                         => 'input-hidden',
                    '__EVENTARGUMENT'                       => 'input-hidden',
                    '__VIEWSTATE'                           => 'input-hidden',
                    '__EVENTVALIDATION'                     => 'input-hidden',
                    'ctl00_RadWindowManager_ClientState'    => 'input-hidden',
                    'ctl00$NavigationNonAuth$LogoutButton'  => 'input-button',
                    'ctl00$ContentPlaceHolder$buttonAccept' => 'input-submit',
                    ));
            $client->removeField('ctl00$NavigationNonAuth$LogoutButton');
            $tag['term'] = $client->getData();
            $tag['term']['ctl00$ContentPlaceHolder$buttonAccept'] = 'OK';
            $client->fill($tag['term']);
            $client->post('http://truckstop.com/AUP.aspx?redirect=/Lite/FindFreight.aspx');
            $this->create_log($jobboard->name.'-term-'.date(DATE_ISO8601).'.html', $client->getBody());
	    } else {
	        echo "Login Fail\n";
	        exit();
	    }

		// second step, searching loads
		$client->get('http://truckstop.com/Lite/FindFreight.aspx');
		$client->get('http://truckstop.com/Lite/Searches/SuperSearch.aspx');
	    if (!preg_match("#Supersearch for Freight#", $client->getBody(), $match)) {
	        echo "Can't get to searching page\n";
	        exit();
	    }
	    
		$this->create_log($jobboard->name.'-searching-'.date(DATE_ISO8601).'.html', $client->getBody());
		$client->load(array('id' => 'aspnetForm', 'name' => 'aspnetForm'));
        $client->validate(array(
                '__EVENTTARGET'                                                                                       => 'input-hidden',
                '__EVENTARGUMENT'                                                                                     => 'input-hidden',
                '__LASTFOCUS'                                                                                         => 'input-hidden',
                '__VIEWSTATE'                                                                                         => 'input-hidden',
                '__EVENTVALIDATION'                                                                                   => 'input-hidden',
                'ctl00_RadWindowManager_ClientState'                                                                  => 'input-hidden',
                'ctl00$Navigation$LogoutButton'                                                                       => 'input-button',
                'ctl00$ContentPlaceHolder$originDestinationControl$checkOriginMultiState'                             => 'input-checkbox',
                'ctl00$ContentPlaceHolder$originDestinationControl$cityStateOrigin$comboCountry'                      => 'input-text',
                'ctl00_ContentPlaceHolder_originDestinationControl_cityStateOrigin_comboCountry_ClientState'          => 'input-hidden',
                'ctl00$ContentPlaceHolder$originDestinationControl$cityStateOrigin$textCity'                          => 'input-text',
                'ctl00$ContentPlaceHolder$originDestinationControl$cityStateOrigin$textState'                         => 'input-text',
                'ctl00_ContentPlaceHolder_originDestinationControl_cityStateOrigin_tooltipSpellings_ClientState'      => 'input-hidden',
                'ctl00$ContentPlaceHolder$originDestinationControl$hiddenOriginMultiStateCountry'                     => 'input-hidden',
                'ctl00$ContentPlaceHolder$originDestinationControl$textOriginMultiState'                              => 'textarea',
                'ctl00_ContentPlaceHolder_originDestinationControl_textOriginRange_text'                              => 'input-text',
                'ctl00$ContentPlaceHolder$originDestinationControl$textOriginRange'                                   => 'input-text',
                'ctl00_ContentPlaceHolder_originDestinationControl_textOriginRange_ClientState'                       => 'input-hidden',
                'ctl00$ContentPlaceHolder$originDestinationControl$checkDestinationMultiState'                        => 'input-checkbox',
                'ctl00$ContentPlaceHolder$originDestinationControl$cityStateDestination$comboCountry'                 => 'input-text',
                'ctl00_ContentPlaceHolder_originDestinationControl_cityStateDestination_comboCountry_ClientState'     => 'input-hidden',
                'ctl00$ContentPlaceHolder$originDestinationControl$cityStateDestination$textCity'                     => 'input-text',
                'ctl00$ContentPlaceHolder$originDestinationControl$cityStateDestination$textState'                    => 'input-text',
                'ctl00_ContentPlaceHolder_originDestinationControl_cityStateDestination_tooltipSpellings_ClientState' => 'input-hidden',
                'ctl00$ContentPlaceHolder$originDestinationControl$hiddenDestinationMultiStateCountry'                => 'input-hidden',
                'ctl00$ContentPlaceHolder$originDestinationControl$textDestinationMultiState'                         => 'textarea',
                'ctl00_ContentPlaceHolder_originDestinationControl_textDestinationRange_text'                         => 'input-text',
                'ctl00$ContentPlaceHolder$originDestinationControl$textDestinationRange'                              => 'input-text',
                'ctl00_ContentPlaceHolder_originDestinationControl_textDestinationRange_ClientState'                  => 'input-hidden',
                'ctl00$ContentPlaceHolder$ucCriteria$dateInput$textDate'                                              => 'input-text',
                'ctl00_ContentPlaceHolder_ucCriteria_dateInput_calendarDate_SD'                                       => 'input-hidden',
                'ctl00_ContentPlaceHolder_ucCriteria_dateInput_calendarDate_AD'                                       => 'input-hidden',
                'ctl00_ContentPlaceHolder_ucCriteria_dateInput_tooltipCalendar_ClientState'                           => 'input-hidden',
                'ctl00$ContentPlaceHolder$ucCriteria$radComboWhenPosted'                                              => 'input-text',
                'ctl00_ContentPlaceHolder_ucCriteria_radComboWhenPosted_ClientState'                                  => 'input-hidden',
                'ctl00$ContentPlaceHolder$ucCriteria$radComboSize'                                                    => 'input-text',
                'ctl00_ContentPlaceHolder_ucCriteria_radComboSize_ClientState'                                        => 'input-hidden',
                'ctl00$ContentPlaceHolder$ucLiteEquipmentTypes$listboxEquipment'                                      => 'select-multiple',
                'ctl00$ContentPlaceHolder$ucEquipmentOptions$checkTarp'                                               => 'input-checkbox',
                'ctl00$ContentPlaceHolder$ucEquipmentOptions$checkHazmat'                                             => 'input-checkbox',
                'ctl00$ContentPlaceHolder$ucEquipmentOptions$checkPalletExchange'                                     => 'input-checkbox',
                'ctl00$ContentPlaceHolder$ucEquipmentOptions$checkTeam'                                               => 'input-checkbox',
                'ctl00$ContentPlaceHolder$ucEquipmentOptions$checkExpedited'                                          => 'input-checkbox',
                'ctl00$ContentPlaceHolder$buttonSearch'                                                               => 'input-submit',
                'ctl00$ContentPlaceHolder$buttonVisiload'                                                             => 'input-submit',
                'ctl00_ContentPlaceHolder_ucLoadsGrid_radMenuLoadsCallList_ClientState'                               => 'input-hidden',
                'loadsContextCompanyID'                                                                               => 'input-text',
                'ctl00_ContentPlaceHolder_ucLoadsGrid_ErrorsControl_radToolDisplayErrors_ClientState'                 => 'input-hidden',
                'ctl00_ContentPlaceHolder_ucTrucksGrid_radMenuTruckCallList_ClientState'                              => 'input-hidden',
                'trucksContextCompanyID'                                                                              => 'input-text',
                'ctl00_ContentPlaceHolder_ucTrucksGrid_ErrorsControl_radToolDisplayErrors_ClientState'                => 'input-hidden',
                ));
        $client->removeField('ctl00$Navigation$LogoutButton');
        $client->removeField('ctl00$ContentPlaceHolder$originDestinationControl$checkOriginMultiState');
        $client->removeField('ctl00$ContentPlaceHolder$originDestinationControl$checkDestinationMultiState');
        $client->removeField('ctl00$ContentPlaceHolder$ucEquipmentOptions$checkTarp');
        $client->removeField('ctl00$ContentPlaceHolder$ucEquipmentOptions$checkHazmat');
        $client->removeField('ctl00$ContentPlaceHolder$ucEquipmentOptions$checkPalletExchange');
        $client->removeField('ctl00$ContentPlaceHolder$ucEquipmentOptions$checkTeam');
        $client->removeField('ctl00$ContentPlaceHolder$ucEquipmentOptions$checkExpedited');
        $client->removeField('ctl00$ContentPlaceHolder$buttonVisiload');
        $tag['search'] = $client->getData();
        

        $tag['search']['ctl00$ContentPlaceHolder$originDestinationControl$cityStateOrigin$comboCountry'] = 'USA';
        $tag['search']['ctl00$ContentPlaceHolder$originDestinationControl$hiddenOriginMultiStateCountry'] = 'USA';
        $tag['search']['ctl00_ContentPlaceHolder_originDestinationControl_cityStateOrigin_comboCountry_ClientState'] = '{"logEntries":[],"value":"USA","text":"USA","enabled":true,"checkedIndices":[]}';
        $origin = explode(",", $config->origin);
        if (sizeof($origin) <= 2){ // simple state
            $tag['search']['ctl00$ContentPlaceHolder$originDestinationControl$cityStateOrigin$textCity'] = $origin[0];
            $tag['search']['ctl00$ContentPlaceHolder$originDestinationControl$cityStateOrigin$textState'] = $origin[1];
        } else { // multiple state
            
        }
        $tag['search']['ctl00_ContentPlaceHolder_originDestinationControl_textOriginRange_text'] = ($config->origin_radius ?: 100);
        $tag['search']['ctl00$ContentPlaceHolder$originDestinationControl$textOriginRange'] = ($config->origin_radius ?:100);
        
        $tag['search']['ctl00_ContentPlaceHolder_originDestinationControl_textDestinationRange_text'] = ($config->destination_radius ?:100);
        $tag['search']['ctl00$ContentPlaceHolder$originDestinationControl$textDestinationRange'] = ($config->destination_radius ?:100);
        $tag['search']['ctl00_ContentPlaceHolder_originDestinationControl_textOriginRange_ClientState'] =  '{"enabled":true,"emptyMessage":"","minValue":25,"maxValue":1000}';
        $tag['search']['ctl00_ContentPlaceHolder_originDestinationControl_textDestinationRange_ClientState'] = '{"enabled":true,"emptyMessage":"","minValue":25,"maxValue":1000}';
        
        $tag['search']['ctl00_ContentPlaceHolder_originDestinationControl_cityStateDestination_comboCountry_ClientState'] = ':{"logEntries":[],"value":"USA","text":"USA","enabled":true,"checkedIndices":}';
        
        if ($config->destination) {
            $destination = explode(",", $config->destination);
            if (sizeof($destination) <= 2) {
                $tag['search']['ctl00_ContentPlaceHolder_originDestinationControl_cityStateDestination_textCity'] = $destination[0];
                    $tag['search']['ctl00_ContentPlaceHolder_originDestinationControl_cityStateDestination_textState'] = $destination[1];
            } else {
            
            }
        }
        $tag['search']['ctl00_ContentPlaceHolder_originDestinationControl_textDestinationRange_text'] = $config->destination_radius;
        if ($config->max_age) {
            $tag['search']['ctl00$ContentPlaceHolder$ucCriteria$radComboWhenPosted'] = 'Last '.$config->max_age.' hours';
            $tag['search']['ctl00_ContentPlaceHolder_ucCriteria_radComboWhenPosted_ClientState'] = '{"logEntries":[],"value":"'.$config->max_age.'","text":"Last '.$config->max_age.' hours","enabled":true,"checkedIndices":[]}';
        } else {
            $tag['search']['ctl00$ContentPlaceHolder$ucCriteria$radComboWhenPosted'] = "Any Time";
        }
        
        $tag['search']['ctl00_ContentPlaceHolder_ucCriteria_radComboSize_ClientState'] = '{"logEntries":[],"value":"'.($config->loads_type == 0 ? 'A' : ($config->loads_type == 1 ? 'F' : 'P')).'","text":"'.($config->loads_type == 0 ? 'All' : ($config->loads_type == 1 ? 'Full' : 'Part')).'","enabled":true,"checkedIndices":[]}';
            
        $tag['search']['ctl00$ContentPlaceHolder$ucCriteria$radComboSize'] = $tag['search']['ctl00_ContentPlaceHolder_ucCriteria_radComboSize_Input'];  
        $tag['search']['ctl00$ContentPlaceHolder$ucLiteEquipmentTypes$listboxEquipment'] = array(12); // Flatbed by default, it's quiet hard to process mapping now, TODO mapping with our trucks list
        $client->fill($tag['search']);
        $client->post('http://truckstop.com/Lite/Searches/SuperSearch.aspx');
		$this->create_log($jobboard->name.'-loads-'.date(DATE_ISO8601).'.html', $client->getBody());
		die();
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