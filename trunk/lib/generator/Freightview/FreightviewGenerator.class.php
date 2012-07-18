<?php
// class generator to send a searching request to a given website then take back the loads
class FreightviewGenerator
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
        //$file = dirname(dirname(dirname(dirname(__FILE__)))).'/log/'.$filename;
        //file_put_contents($file, $content);
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
			$notify_error = new NotifyError("Freightview - Config not found\n");
			$notify_error->execute();
			return;
        }
        
        $jobboard = Doctrine_Core::getTable('Jobboard')->findOneByName($this->jobboard_name);
        if (!$jobboard) {
            $notify_error = new NotifyError("Freightview - Jobboard not found\n");
			$notify_error->execute();
			return;
        }
        
		try {
			// ok everything is ready, lets go
			$tag = array();
			$base_url = $jobboard->address;
			$client->get($base_url);
			$client->load(array('id' => 'aspnetForm', 'name' => 'aspnetForm'));
			$client->validate(array(
							'__EVENTTARGET'                           => 'input-hidden',
							'__EVENTARGUMENT'                         => 'input-hidden',
							'__VIEWSTATE'                             => 'input-hidden',
							'__EVENTVALIDATION'                       => 'input-hidden',
							'ctl00$UserSignIn1$lgnSignIn$UserName'    => 'input-text',
							'ctl00$UserSignIn1$lgnSignIn$Password'    => 'input-password',
							'ctl00$UserSignIn1$lgnSignIn$RememberMe'  => 'input-checkbox',
							'ctl00$UserSignIn1$lgnSignIn$LoginButton' => 'input-submit',
							'sprop2'                                  => 'input-hidden',
							'pagename'                                => 'input-hidden',
			));
			$client->removeField('ctl00$UserSignIn1$lgnSignIn$RememberMe');
			$tag['login'] = $client->getData();
			// login step
			$tag['login']['ctl00$UserSignIn1$lgnSignIn$UserName'] = $jobboard->username;
			$tag['login']['ctl00$UserSignIn1$lgnSignIn$Password'] = $jobboard->password;
			$tag['login']['ctl00$UserSignIn1$lgnSignIn$LoginButton'] = 'Sign in';
			$client->fill($tag['login']);
			$client->post($base_url);
			$this->create_log($jobboard->name.'-login-'.date(DATE_ISO8601).'.html', $client->getBody());
			if (!preg_match('#Welcome to Freightview#', $client->getBody(), $match)) {
				$notify_error = new NotifyError("Freightview - Login fail\n");
				$notify_error->execute();
				return;
			}
	
			// second step, searching loads
			$client->get('http://freightview.com/ProviderContent/AvailableLoads.aspx?rfm=1');
			$this->create_log($jobboard->name.'-searching-'.date(DATE_ISO8601).'.html', $client->getBody());
			
			$client->load(array('id' => 'aspnetForm', 'name' => 'aspnetForm'));
			$client->validate(array(
							'__EVENTTARGET'                                                          => 'input-hidden',
							'__EVENTARGUMENT'                                                        => 'input-hidden',
							'__LASTFOCUS'                                                            => 'input-hidden',
							'__VIEWSTATE'                                                            => 'input-hidden',
							'__EVENTVALIDATION'                                                      => 'input-hidden',
							'ctl00$ProviderContentPlaceHolder$rbnAllLanes'                           => 'input-radio',
							'ctl00$ProviderContentPlaceHolder$rbnPrefLanes'                          => 'input-radio',
							'ctl00$ProviderContentPlaceHolder$rbnAllTypes'                           => 'input-radio',
							'ctl00$ProviderContentPlaceHolder$rbnDirectType'                         => 'input-radio',
							'ctl00$ProviderContentPlaceHolder$txbSearchCityOrigin'                   => 'input-text',
							'ctl00$ProviderContentPlaceHolder$OriginWatermark_ClientState'           => 'input-hidden',
							'ctl00$ProviderContentPlaceHolder$ddlSearchStateOrigin'                  => 'select',
							'ctl00$ProviderContentPlaceHolder$txbSearchCityOriginRadius'             => 'input-text',
							'ctl00$ProviderContentPlaceHolder$OriginRadiusWatermark_ClientState'     => 'input-hidden',
							'ctl00$ProviderContentPlaceHolder$txbSearchCityDest'                     => 'input-text',
							'ctl00$ProviderContentPlaceHolder$DestWatermark_ClientState'             => 'input-hidden',
							'ctl00$ProviderContentPlaceHolder$ddlSearchStateDest'                    => 'select',
							'ctl00$ProviderContentPlaceHolder$txbSearchCityDestRadius'               => 'input-text',
							'ctl00$ProviderContentPlaceHolder$DestRadiusWatermark_ClientState'       => 'input-hidden',
							'ctl00$ProviderContentPlaceHolder$txtSearch'                             => 'input-text',
							'ctl00$ProviderContentPlaceHolder$TextBoxWatermarkExtender1_ClientState' => 'input-hidden',
							'ctl00$ProviderContentPlaceHolder$ddlSearchEquipmentType'                => 'select',
							'ctl00$ProviderContentPlaceHolder$lnbSearch'                             => 'input-submit',
							'ctl00$ProviderContentPlaceHolder$hdnPaymentAssurance'                   => 'input-hidden',
							));
	
			$client->removeField('ctl00$ProviderContentPlaceHolder$rbnDirectType');
			$client->removeField('ctl00$ProviderContentPlaceHolder$rbnPrefLanes');
			$tag['search'] = $client->getData();
			$tag['search']['__EVENTTARGET'] = 'ctl00$ProviderContentPlaceHolder$rbnAllLanes';
			$full_origin = str_replace(' ', '', $config->origin);
			$full_origin = preg_split('#,#', $full_origin);
			$tag['search']['ctl00$ProviderContentPlaceHolder$txbSearchCityOrigin'] = strtoupper($full_origin[0]);
			$tag['search']['ctl00$ProviderContentPlaceHolder$ddlSearchStateOrigin'] = strtoupper($full_origin[1]);
			$tag['search']['ctl00$ProviderContentPlaceHolder$txbSearchCityOriginRadius'] = $config->origin_radius;
			if (!empty($config->destination)) {
				$full_destination = str_replace(' ', '', $config->destination);
				$full_destination = preg_split('#,#', $full_destination);
				$tag['search']['ctl00$ProviderContentPlaceHolder$txbSearchCityDest'] = strtoupper($full_destination[0]);
				$tag['search']['ctl00$ProviderContentPlaceHolder$ddlSearchStateDest'] = strtoupper($full_destination[1]);
				$tag['search']['ctl00$ProviderContentPlaceHolder$txbSearchCityDestRadius'] = $config->destination_radius;
			}
			$tag['search']['ctl00$ProviderContentPlaceHolder$rbnAllLanes'] = 'rbnAllLanes';
	
			// only 1 truck type is accepted for this website, we take the first one
			$config_trucks = Doctrine_Query::create()->from('ConfigTruck cf')->addWhere('cf.config_id = ?', $config->id)->execute();
			foreach ($config_trucks as $config_truck) {
				$truck_id = $config_truck->Truck->id;
				// will be remapping later
				$tag['search']['ctl00$ProviderContentPlaceHolder$ddlSearchEquipmentType'] = $this->mapping($truck_id, array(
									'0' =>  '0', // all
									'1' =>  '19', // Dry Bulk
									'2' =>  '0', // containers missing
									'3' =>  '0', // Deck Standard
									'4' =>  '20', // Flatbed
									'5' =>  '0', // Decks, Specialized
									'6' =>  'O', // other Equipment
									'7' =>  '0',
									'8' =>  '0',
									'9' =>  '0',
									'10' => '0',
									'11' => '0',
				));
				break;
			}
			$client->fill($tag['search']);
			$client->post('http://freightview.com/ProviderContent/AvailableLoads.aspx?rfm=1');
			$this->create_log($jobboard->name.'-loads-'.date(DATE_ISO8601).'.html', $client->getBody());
	
			// parsing reponse
			$doc = new DOMDocument();
			@$doc->loadHTML($client->getBody());
			$xpath = new DOMXpath($doc);
	
			$nodes = $xpath->query('//table[@id="ctl00_ProviderContentPlaceHolder_AvailableLoadGridView"]/tr');
			foreach ($nodes as $node) {
				$tds = $xpath->query('td/span', $node);
				$items = array();
				foreach ($tds as $td) {
					$value = $td->nodeValue;
					$value = trim($value);
					//$value = str_replace(array("  "), array(""), $value);
					//$value = str_replace(array("\n"), array(""), $value);
					$items[] = strip_tags($value);
				}
				
				$ships = $xpath->query('td/h5/span', $node);
				foreach ($ships as $ship) {
					$items[] = trim($ship->nodeValue);
				}
				
				$distances = $xpath->query('td/div/span/span', $node);
				foreach ($distances as $distance)
					$items[] = trim($distance->nodeValue);
				$equipments = $xpath->query('td[@class="Equipment"]', $node);
				foreach ($equipments as $equipment)
					$items[] = trim($equipment->nodeValue);
	
				$this->addLoads($items);
			}
		} catch (Exception $ex) {
			$notify_error = new NotifyError("Freightview - Jobboard have been changed. Please contact to VTNS\n");
			$notify_error->execute();
		}
        
    }
    
    
    private function addLoads($items)
    {
        if (sizeof($items) == 10) {
            $this->loads[] = $items;
        }
    }
    
    
    public function getLoads()
    {
        return $this->loads;
    }
}
?>
