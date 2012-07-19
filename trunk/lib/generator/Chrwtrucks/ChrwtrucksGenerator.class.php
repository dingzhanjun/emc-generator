<?php
// class generator to send a searching request to a given website then take back the loads
class ChrwtrucksGenerator
{
    protected $config_id;
    protected $jobboard_name;
    protected $loads = array();
    public function __construct($config_id, $jobboard_name)
    {
        $this->config_id = $config_id;
        $this->jobboard_name = $jobboard_name;
    }

    public function create_log($filename, $content)
    {
        // no longer create log
        //$file = dirname(dirname(dirname(dirname(__FILE__)))).'/log/'.$filename;
        $file = dirname(dirname(dirname(dirname(__FILE__)))).'/log/log.html';
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
        else
            return null;
    }

    public function execute()
    {
        $this->initialize();
        $client = new WebFormClient();

        $client->setLogPrefix(dirname(dirname(dirname(dirname(__FILE__)))).'/log/'.$this->jobboard_name.' '.date("Y-m-d H-i-s O", time()));
        $config = Doctrine_Core::getTable('Config')->find($this->config_id);
        if (!$config) {
			$notify_error = new NotifyError("Chrwtrucks - Config not found\n");
			$notify_error->execute();
			return;
        }

        $jobboard = Doctrine_Core::getTable('Jobboard')->findOneByName($this->jobboard_name);
        if (!$jobboard) {
            $notify_error = new NotifyError("Chrwtrucks - Jobboard not found\n");
			$notify_error->execute();
			return;
        }

        // ok everything is ready, lets go
		try {
			$tag = array();
			$base_url = $jobboard->address;
			$client->get($base_url);
			// $this->create_log($jobboard->name.'-login-'.date(DATE_ISO8601).'.html', $client->getBody());
			$client->load(array('id' => 'form1', 'name' => 'form1'));
			$client->validate(array(
				'__VIEWSTATE' 					=> 'input-hidden',
				'__EVENTVALIDATION'				=> 'input-hidden',
				'Login_External1:txtLogin' 		=> 'input-text',
				'Login_External1:txtPassword' 	=> 'input-password',
				'Login_External1:chkRetainUser' => 'input-checkbox',
				'Login_External1:ibnLogin' 		=> 'input-image',
				'javascript' 					=> 'input-hidden',
			));
			$client->removeField('Login_External1:chkRetainUser');
			$tag['login'] = $client->getData();
			$tag['login']['Login_External1:txtLogin'] = $jobboard->username;
			$tag['login']['Login_External1:txtPassword'] = $jobboard->password;
			$tag['login']['javascript'] = 'enabled';
			$client->fill($tag['login']);
			$client->post('https://www.chrwtrucks.com/');
			if (preg_match("#CHRWTrucks - Find Loads#", $client->getBody(), $match)) {
				$origin = $config->origin;
				$destination = $config->destination;
				if ($origin != "" && $destination != "") {
					$status_search = "origin and destination";
					if($config->origin_is_multistates = true || $config->destination_is_multistates = true) {
						$status_search = "multi_state";
						$client->get('https://www.chrwtrucks.com/Applications/FindLoad/FindLoadMultiple.aspx');
						$client->load(array('id' => 'aspnetForm', 'name' => 'aspnetForm'));
						$client->validate(array(
							'__EVENTTARGET'                          => 'input-hidden',
							'__EVENTARGUMENT'                        => 'input-hidden',
							'__LASTFOCUS'                            => 'input-hidden',
							'__VIEWSTATE'                            => 'input-hidden',
							'_ctl0:cphMain:DateOptions'              => 'input-radio',
							'Source'                                 => 'input-radio',
							'_ctl0_cphMain_txtStartDate_clientState' => 'input-hidden',
							'_ctl0_cphMain_txtEndDate_clientState'   => 'input-hidden',
							'_ctl0:cphMain:Origin'                   => 'select-multiple',
							'_ctl0:cphMain:Destination'              => 'select-multiple',
							'_ctl0:cphMain:dpdEquipment'             => 'select',
							'_ctl0:cphMain:ddlSpecialized'           => 'select',
							'_ctl0:cphMain:cbSaveSearch'             => 'input-checkbox',
							'_ctl0:cphMain:btnSubmit'                => 'input-submit',
							'_ctl0:cphMain:btnReset'                 => 'input-submit',
							'_ctl0:cphMain:btnRetrieve'              => 'input-submit',
							'_ctl0:cphMain:originNames'              => 'input-hidden',
							'_ctl0:cphMain:destNames'                => 'input-hidden',
							'_ctl0__ig_def_dp_cal_clientState'       => 'input-hidden',
							'_ctl0:_IG_CSS_LINKS_'                   => 'input-hidden',
						));
						$client->removeField('_ctl0:cphMain:cbSaveSearch');
						$client->removeField('_ctl0:cphMain:btnReset');
						$client->removeField('_ctl0:cphMain:btnRetrieve');
						$client->removeField('_ctl0:cphMain:ddlSpecialized');
						$tag['search'][''] = $client->getData();
						
						$start_date = date("Y-m-d", strtotime($config->from_date));
						$tag['search']['_ctl0_cphMain_txtStartDate_clientState'] = "|0|01". $start_date . "-0-0-0-0||[[[[]],[],[]],[{},[]]," . '"01' . $start_date . '-0-0-0-0"' . "]";
						$end_date = date("Y-m-d", strtotime($config->to_date));
						$tag['search']['_ctl0_cphMain_txtEndDate_clientState'] = "|0|01". $end_date . "-0-0-0-0||[[[[]],[],[]],[{},[]]," . '"01' . $end_date . '-0-0-0-0"' . "]";
						$config_trucks = Doctrine_Query::create()->from('ConfigTruck cf')->addWhere('cf.config_id = ?', $config->id)->execute();
						foreach ($config_trucks as $config_truck) {
							$truck_id = $config_truck->Truck->id;
							// will be remapping later
							$tag['search']['_ctl0:cphMain:dpdEquipment'] = $this->mapping($truck_id, array(
								'0' =>  'ALL', // all
								'1' =>  'ALL', // Dry Bulk
								'2' =>  'ALL', // containers missing
								'3' =>  'ALL', // Deck Standard
								'4' =>  'F', // Flatbed
								'5' =>  'ALL', // Decks, Specialized
								'6' =>  'Z', // other Equipment
								'7' =>  'R', // Reefers
								'8' =>  'V', // Vans, Specialized
								'9' =>  'ALL', // Tankers
								'10' => 'V', // Vans, Standard
								'11' => 'ALL', // Hazardous Materials
							));
							break;
						 }
						 $tag['search']['_ctl0:cphMain:originNames'] = trim(strtoupper($config->origin)); 
						 $tag['search']['_ctl0:cphMain:destNames'] = trim(strtoupper($config->destination));
						 $tag['search']['_ctl0:cphMain:btnSubmit'] = "Submit";
						 $client->fill($tag['search']);
						 $client->post('https://www.chrwtrucks.com/Applications/FindLoad/FindLoadMultiple.aspx');
						 $this->create_log('',$client->getBody());
					}
					else {
						$client->get('https://www.chrwtrucks.com/Applications/FindLoad/RadiusSearchOD.aspx');
						$client->load(array('id' => 'aspnetForm', 'name' => 'aspnetForm'));
						$client->validate(array(
							'__EVENTTARGET'                          => 'input-hidden',
							'__EVENTARGUMENT'                        => 'input-hidden',
							'__LASTFOCUS'                            => 'input-hidden',
							'__VIEWSTATEFIELDCOUNT'                  => 'input-hidden',
							'__VIEWSTATE'                            => 'input-hidden',
							'__VIEWSTATE1'                           => 'input-hidden',
							'_ctl0:cphMain:searchParmsChanged'       => 'input-hidden',
							'_ctl0:cphMain:ddlOCountry'              => 'select',
							'_ctl0:cphMain:ddlOState'                => 'select',
							'_ctl0:cphMain:txtOCity'                 => 'input-text',
							'_ctl0:cphMain:ddlDCountry'              => 'select',
							'_ctl0:cphMain:ddlDState'                => 'select',
							'_ctl0:cphMain:txtDCity'                 => 'input-text',
							'_ctl0:cphMain:DateOptions'              => 'input-radio',
							'_ctl0_cphMain_txtStartDate_clientState' => 'input-hidden',
							'_ctl0_cphMain_txtEndDate_clientState'   => 'input-hidden',
							'_ctl0:cphMain:ddlEquipment'             => 'select',
							'_ctl0:cphMain:ddlSpecialized'           => 'select',
							'_ctl0_cphMain_oRadius_clientState'      => 'input-hidden',
							'_ctl0_cphMain_oRadius'                  => 'input-text',
							'_ctl0_cphMain_dradius_clientState'      => 'input-hidden',
							'_ctl0_cphMain_dradius'                  => 'input-text',
							'_ctl0:cphMain:cbSaveSearch'             => 'input-checkbox',
							'_ctl0:cphMain:btnSubmit'                => 'input-submit',
							'_ctl0:cphMain:btnReset'                 => 'input-submit',
							'_ctl0:cphMain:btnRetrieve'              => 'input-submit',
							'_ctl0:cphMain:hdnStartDate'             => 'input-hidden',
							'_ctl0:cphMain:hdnEndDate'               => 'input-hidden',
							'_ctl0__ig_def_dp_cal_clientState'       => 'input-hidden',
							'_ctl0:_IG_CSS_LINKS_'                   => 'input-hidden',
						));
						$client->removeField('_ctl0:cphMain:cbSaveSearch');
						$client->removeField('_ctl0:cphMain:btnReset');
						$client->removeField('_ctl0:cphMain:btnRetrieve');
						$client->removeField('_ctl0:cphMain:ddlSpecialized');
						$tag['search'][''] = $client->getData();
						$tag['search']['_ctl0:cphMain:ddlOState'] = trim(strtoupper(substr($origin, strrpos($origin, ",") + 1, strlen($origin))));  
						$tag['search']['_ctl0:cphMain:txtOCity'] =  trim(substr($origin, 0, strrpos($origin, ",")));
						$tag['search']['_ctl0:cphMain:ddlDState'] = trim(strtoupper(substr($destination, strrpos($destination, ",") + 1, strlen($destination))));  
						$tag['search']['_ctl0:cphMain:txtDCity'] =  trim(substr($destination, 0, strrpos($destination, ",")));
						$config_trucks = Doctrine_Query::create()->from('ConfigTruck cf')->addWhere('cf.config_id = ?', $config->id)->execute();
						foreach ($config_trucks as $config_truck) {
							$truck_id = $config_truck->Truck->id;
							// will be remapping later
							$tag['search']['_ctl0:cphMain:ddlEquipment'] = $this->mapping($truck_id, array(
								'0' =>  'A', // all
								'1' =>  'A', // Dry Bulk
								'2' =>  'A', // containers missing
								'3' =>  'A', // Deck Standard
								'4' =>  'F', // Flatbed
								'5' =>  'A', // Decks, Specialized
								'6' =>  'Z', // other Equipment
								'7' =>  'R', // Reefers
								'8' =>  'V', // Vans, Specialized
								'9' =>  'A', // Tankers
								'10' => 'V', // Vans, Standard
								'11' => 'A', // Hazardous Materials
							));
							break;
						 }
						$tag['search']['_ctl0_cphMain_oRadius'] = $config->origin_radius;
						$tag['search']['_ctl0_cphMain_oRadius_clientState'] = "|0|01" . $config->origin_radius . "||[[[[]],[],[]],[{},[]]," . '"01' . $config->origin_radius . '"' . "]";
						$tag['search']['_ctl0_cphMain_dradius'] = $config->destination_radius;
						$tag['search']['_ctl0_cphMain_dradius_clientState'] = "|0|01" . $config->destination_radius . "||[[[[]],[],[]],[{},[]]," . '"01' . $config->destination_radius . '"' . "]";
						$tag['search']['_ctl0:cphMain:hdnStartDate'] = date("m/d/Y",strtotime($config->from_date));
						$start_date = date("Y-m-d", strtotime($config->from_date));
						$tag['search']['_ctl0_cphMain_txtStartDate_clientState'] = "|0|01". $start_date . "-0-0-0-0||[[[[]],[],[]],[{},[]]," . '"01' . $start_date . '-0-0-0-0"' . "]";
						$tag['search']['_ctl0:cphMain:hdnEndDate'] = date("m/d/Y", strtotime($config->to_date));
						$end_date = date("Y-m-d", strtotime($config->to_date));
						$tag['search']['_ctl0_cphMain_txtEndDate_clientState'] = "|0|01". $end_date . "-0-0-0-0||[[[[]],[],[]],[{},[]]," . '"01' . $end_date . '-0-0-0-0"' . "]";
						$tag['search']['_ctl0:cphMain:btnSubmit'] = "Submit";
						$client->fill($tag['search']);
						$client->post('https://www.chrwtrucks.com/Applications/FindLoad/RadiusSearchOD.aspx');
					}
				}                
				elseif ($origin != "") {
					$status_search = "origin";
					$client->get('https://www.chrwtrucks.com/Applications/FindLoad/RadiusSearch.aspx?InOut=1');
					$client->load(array('id' => 'aspnetForm', 'name' => 'aspnetForm'));
					$client->validate(array(
						'__EVENTTARGET'                          => 'input-hidden',
						'__EVENTARGUMENT'                        => 'input-hidden',
						'__LASTFOCUS'                            => 'input-hidden',
						'__VIEWSTATEFIELDCOUNT'                  => 'input-hidden',
						'__VIEWSTATE'                            => 'input-hidden',
						'__VIEWSTATE1'                           => 'input-hidden',
						'_ctl0:cphMain:searchParmsChanged'       => 'input-hidden',
						'_ctl0:cphMain:DateOptions'              => 'input-radio',
						'_ctl0_cphMain_txtStartDate_clientState' => 'input-hidden',
						'_ctl0_cphMain_txtEndDate_clientState'   => 'input-hidden',
						'_ctl0:cphMain:ddlCountry'               => 'select',
						'_ctl0:cphMain:ddlState'                 => 'select',
						'_ctl0:cphMain:txtCity'                  => 'input-text',
						'_ctl0:cphMain:hidlat'                   => 'input-hidden',
						'_ctl0:cphMain:hidlong'                  => 'input-hidden',
						'_ctl0:cphMain:ddlEquipment'             => 'select',
						'_ctl0:cphMain:ddlSpecialized'           => 'select',
						'_ctl0_cphMain_intMiles_clientState'     => 'input-hidden',
						'_ctl0_cphMain_intMiles'                 => 'input-text',
						'_ctl0:cphMain:cbSaveSearch'             => 'input-checkbox',
						'_ctl0:cphMain:btnSubmit'                => 'input-submit',
						'_ctl0:cphMain:btnReset'                 => 'input-submit',
						'_ctl0:cphMain:btnRetrieve'              => 'input-submit',
						'_ctl0__ig_def_dp_cal_clientState'       => 'input-hidden',
						'_ctl0:_IG_CSS_LINKS_'                   => 'input-hidden',
					));
					$client->removeField('_ctl0:cphMain:cbSaveSearch');
					$client->removeField('_ctl0:cphMain:btnReset');
					$client->removeField('_ctl0:cphMain:btnRetrieve');
					$client->removeField('_ctl0:cphMain:ddlSpecialized');
					
					$tag['search'][''] = $client->getData();
					$tag['search']['_ctl0:cphMain:ddlState'] = trim(strtoupper(substr($origin, strrpos($origin, ",") + 1, strlen($origin))));  
					$tag['search']['_ctl0:cphMain:txtCity'] =  trim(substr($origin, 0, strrpos($origin, ",")));
					$config_trucks = Doctrine_Query::create()->from('ConfigTruck cf')->addWhere('cf.config_id = ?', $config->id)->execute();
					foreach ($config_trucks as $config_truck) {
						$truck_id = $config_truck->Truck->id;
						// will be remapping later
						$tag['search']['_ctl0:cphMain:ddlEquipment'] = $this->mapping($truck_id, array(
							'0' =>  'A', // all
							'1' =>  'A', // Dry Bulk
							'2' =>  'A', // containers missing
							'3' =>  'A', // Deck Standard
							'4' =>  'F', // Flatbed
							'5' =>  'A', // Decks, Specialized
							'6' =>  'Z', // other Equipment
							'7' =>  'R', // Reefers
							'8' =>  'V', // Vans, Specialized
							'9' =>  'A', // Tankers
							'10' => 'V', // Vans, Standard
							'11' => 'A', // Hazardous Materials
						));
						break;
					}
					$tag['search']['_ctl0_cphMain_intMiles'] = $config->origin_radius;
					$tag['search']['_ctl0_cphMain_intMiles_clientState'] = "|0|01" . $config->origin_radius . "||[[[[]],[],[]],[{},[]]," . '"01' . $config->origin_radius.'"' . "]";
					$start_date = date("Y-m-d",strtotime($config->from_date));
					$tag['search']['_ctl0_cphMain_txtStartDate_clientState'] = "|0|01" . $start_date . "-0-0-0-0||[[[[]],[],[]],[{},[]]," . '"01' . $start_date . '-0-0-0-0"' . "]";
					$end_date = date("Y-m-d",strtotime($config->to_date));
					$tag['search']['_ctl0_cphMain_txtEndDate_clientState'] = "|0|01" . $end_date . "-0-0-0-0||[[[[]],[],[]],[{},[]]," . '"01' . $end_date . '-0-0-0-0"' . "]";
					$tag['search']['_ctl0:cphMain:btnSubmit'] = "Submit";
					$tag['search']['_ctl0:cphMain:btnSubmit'] = "Submit";
					$client->fill($tag['search']);
					$client->post('https://www.chrwtrucks.com/Applications/FindLoad/RadiusSearch.aspx?InOut=1');
				}
				elseif ($destination != "") {
					$status_search = "destination";
					$client->get('https://www.chrwtrucks.com/Applications/FindLoad/RadiusSearch.aspx?InOut=0');
					$client->load(array('id' => 'aspnetForm', 'name' => 'aspnetForm'));
					$client->validate(array(
						'__EVENTTARGET'                          => 'input-hidden',
						'__EVENTARGUMENT'                        => 'input-hidden',
						'__LASTFOCUS'                            => 'input-hidden',
						'__VIEWSTATEFIELDCOUNT'                  => 'input-hidden',
						'__VIEWSTATE'                            => 'input-hidden',
						'__VIEWSTATE1'                           => 'input-hidden',
						'_ctl0:cphMain:searchParmsChanged'       => 'input-hidden',
						'_ctl0:cphMain:DateOptions'              => 'input-radio',
						'_ctl0_cphMain_txtStartDate_clientState' => 'input-hidden',
						'_ctl0_cphMain_txtEndDate_clientState'   => 'input-hidden',
						'_ctl0:cphMain:ddlCountry'               => 'select',
						'_ctl0:cphMain:ddlState'                 => 'select',
						'_ctl0:cphMain:txtCity'                  => 'input-text',
						'_ctl0:cphMain:hidlat'                   => 'input-hidden',
						'_ctl0:cphMain:hidlong'                  => 'input-hidden',
						'_ctl0:cphMain:ddlEquipment'             => 'select',
						'_ctl0:cphMain:ddlSpecialized'           => 'select',
						'_ctl0_cphMain_intMiles_clientState'     => 'input-hidden',
						'_ctl0_cphMain_intMiles'                 => 'input-text',
						'_ctl0:cphMain:cbSaveSearch'             => 'input-checkbox',
						'_ctl0:cphMain:btnSubmit'                => 'input-submit',
						'_ctl0:cphMain:btnReset'                 => 'input-submit',
						'_ctl0:cphMain:btnRetrieve'              => 'input-submit',
						'_ctl0__ig_def_dp_cal_clientState'       => 'input-hidden',
						'_ctl0:_IG_CSS_LINKS_'                   => 'input-hidden',
					));
					$client->removeField('_ctl0:cphMain:cbSaveSearch');
					$client->removeField('_ctl0:cphMain:btnReset');
					$client->removeField('_ctl0:cphMain:btnRetrieve');
					$client->removeField('_ctl0:cphMain:ddlSpecialized');
					
					$tag['search'][''] = $client->getData();
					$tag['search']['_ctl0:cphMain:ddlState'] = trim(strtoupper(substr($destination, strrpos($destination,  ",") + 1, strlen($destination))));  
					$tag['search']['_ctl0:cphMain:txtCity'] =  trim(substr($destination, 0, strrpos($destination, ",")));
					$config_trucks = Doctrine_Query::create()->from('ConfigTruck cf')->addWhere('cf.config_id = ?', $config->id)->execute();
					foreach ($config_trucks as $config_truck) {
						$truck_id = $config_truck->Truck->id;
						// will be remapping later
						$tag['search']['_ctl0:cphMain:ddlEquipment'] = $this->mapping($truck_id, array(
							'0' =>  'A', // all
							'1' =>  'A', // Dry Bulk
							'2' =>  'A', // containers missing
							'3' =>  'A', // Deck Standard
							'4' =>  'F', // Flatbed
							'5' =>  'A', // Decks, Specialized
							'6' =>  'Z', // other Equipment
							'7' =>  'R', // Reefers
							'8' =>  'V', // Vans, Specialized
							'9' =>  'A', // Tankers
							'10' => 'V', // Vans, Standard
							'11' => 'A', // Hazardous Materials
						 ));
						break;
					 }
					$tag['search']['_ctl0_cphMain_intMiles'] = $config->destination_radius;
					$tag['search']['_ctl0_cphMain_intMiles_clientState'] = "|0|01" . $config->destination_radius . "||[[[[]],[],[]],[{},[]]," . '"01' . $config->destination_radius . '"' . "]";
					$start_date = date("Y-m-d",strtotime($config->from_date));
					$tag['search']['_ctl0_cphMain_txtStartDate_clientState'] = "|0|01" . $start_date . "-0-0-0-0||[[[[]],[],[]],[{},[]]," . '"01' . $start_date . '-0-0-0-0"' . "]";
					$end_date = date("Y-m-d",strtotime($config->to_date));
					$tag['search']['_ctl0_cphMain_txtEndDate_clientState'] = "|0|01" . $end_date . "-0-0-0-0||[[[[]],[],[]],[{},[]]," . '"01' . $end_date . '-0-0-0-0"' . "]";
					$tag['search']['_ctl0:cphMain:btnSubmit'] = "Submit";
					$client->fill($tag['search']);
					$client->post('https://www.chrwtrucks.com/Applications/FindLoad/RadiusSearch.aspx?InOut=0');
				}          
			} elseif (!preg_match('#Invalid username and/or password. Please try again.#', $client->getBody(), $match)) {
				$notify_error = new NotifyError("Chrwtrucks - Login fail\n");
				$notify_error->execute();
				return;
			}      
			// parsing reponse
			$doc = new DOMDocument();
			@$doc->loadHTML($client->getBody());
			$xpath = new DOMXpath($doc);
			if($status_search = "multi_search")
				$nodes = $xpath->query("//div[@id = '_ctl0_cphMain_pnlLoadlist']//table//tr");
			else
				$nodes = $xpath->query("//div[@id ='_ctl0_cphMain_pnlSearchResult']//table//table//tr");        
			foreach ($nodes as $node) {
				$tds = $xpath->query('td', $node);
				$items = array(); 
				foreach ($tds as $td) {
					$td = $td->nodeValue;
					$td = preg_replace("/[^A-Za-z0-9, \/:-]/i", " ", $td);
					$td = preg_replace('/\s+/', ' ', $td);
					$items[] = trim($td);
				}
				if($items[0] >= 1)
				{
					if($status_search == "origin") {
						$tmp = array();
						for($i = 0; $i <= 5; $i++) {
							$tmp[$i] = $items[$i];
						}
						$tmp[6] = " ";
						$tmp[7] = $items[6];
						$tmp[8] = $items[7];
						$items = $tmp;
					}
					elseif($status_search == "destination") {
						$tmp = array();
						for($i = 0; $i <= 2; $i++) {
							$tmp[$i] = $items[$i];
						}
						$tmp[3] = " ";
						for($i = 4; $i <= 8 ; $i++) {
							$tmp[$i] = $items[$i - 1];
						}                    
						$items = $tmp; 
					}
					elseif ($status_search == "multi_search") {
						$tmp = array();
						for($i = 0; $i <= 2; $i++) {
							$tmp[$i] = $items[$i];
						}
						$tmp[3] = " ";
						$tmp[4] = $items[3];
						$tmp[5] = $items[4]; 
						$tmp[6] = " ";
						$tmp[7] = $items[5];
						$tmp[8] = $items[6];
						$items = $tmp;
					}    
					
					/*
					$client->get('https://www.chrwtrucks.com/Applications/FindLoad/FindLoadDetails.aspx?Caller=Radius&LoadNumber='.$items[0]);
					$html = new DOMDocument();
					@$html->loadHTML($client->getBody());
					$xpath_detail = new DOMXpath($html);
					$nodes_detail = $xpath_detail->query("//a[@id='hypContact']");     
					foreach($nodes_detail as $n ) {
						$items[] = $n->nodeValue;
					}
					$nodes_detail = $xpath_detail->query("//span[@id='lblBranchName']");
					foreach($nodes_detail as $n ) {
						$items[] = $n->nodeValue;
					}
					$nodes_detail = $xpath_detail->query("//span[@id='lblBranchPhone']");
					foreach($nodes_detail as $n ) {
						$items[] = $n->nodeValue;
					}
					$nodes_detail = $xpath_detail->query("//span[@id='lblDistance']");
					foreach($nodes_detail as $n ) {
						$items[] = $n->nodeValue;
					}
					*/
					$items[0] = "<a href=https://www.chrwtrucks.com/Applications/FindLoad/FindLoadDetails.aspx?Caller=Radius&LoadNumber=$items[0] target=_blank >$items[0]</a>";
					$this->addLoads($items);
				}
				
			}   
		} catch (Exception $ex) {
			$notify_error = new NotifyError("Chrwtrucks - Jobboard have been changed. Please contact to VTNS\n");
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