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
		else return null;
	}
	
	
	public function execute()
	{
		$this->initialize();
		$client = new WebFormClient();
		
		//$client->setLogPrefix(dirname(dirname(dirname(dirname(__FILE__)))).'/log/'.$this->jobboard_name.' '.date(DATE_ISO8601));
		
		$client->setLogPrefix(dirname(dirname(dirname(dirname(__FILE__)))).'/log/log.html');
		
		$config = Doctrine_Core::getTable('Config')->find($this->config_id);
		if (!$config) {
			$notify_error = new NotifyError("Getloaded - Config not found\n");
			$notify_error->execute();
			return;
		}
		
		$jobboard = Doctrine_Core::getTable('Jobboard')->findOneByName($this->jobboard_name);
		if (!$jobboard) {
			$notify_error = new NotifyError("Getloaded - Jobboard not found\n");
			$notify_error->execute();
			return;
		}
		
		try 
		{
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
			$client->get('http://member.getloaded.com/search/load_search.php');
			$client->load(array('id' => 'load_search_form'));
			$client->validate(array(
							'search_id'                             => 'input-hidden',
							'search_id_type'                        => 'input-hidden',
							'search'                                => 'input-hidden',
							'pickup_start_date'                     => 'input-hidden',
							'sc'                                    => 'input-hidden',
							'ss'                                    => 'input-hidden',
							'dc'                                    => 'input-hidden',
							'ds'                                    => 'input-hidden',
							'smask'                                 => 'input-hidden',
							'dmask'                                 => 'input-hidden',
							'search_type'                           => 'input-hidden',
							'ttype'                                 => 'input-hidden',
							'amask'                                 => 'input-hidden',
							'unknown_start'                         => 'input-hidden',
							'unknown_dest'                          => 'input-hidden',
							'saved_search_count'                    => 'input-hidden',
							'search_status'                         => 'input-hidden',
							'prevTxt'                               => 'input-hidden',
							'starting_point'                        => 'input-text',
							'destination_point'                     => 'input-text',
							'radius_strict'                         => 'input-radio',
							's_radius'                              => 'input-text',
							'd_radius'                              => 'input-text',
							'posted_within'                         => 'select',
							'ttype_select[64]'                      => 'input-checkbox',
							'ttype_select[16384]'                   => 'input-checkbox',
							'ttype_select[2048]'                    => 'input-checkbox',
							'ttype_select[1024]'                    => 'input-checkbox',
							'ttype_select[1]'                       => 'input-checkbox',
							'ttype_select[2]'                       => 'input-checkbox',
							'ttype_select[1048576]'                 => 'input-checkbox',
							'ttype_select[8]'                       => 'input-checkbox',
							'ttype_select[4]'                       => 'input-checkbox',
							'ttype_select[32]'                      => 'input-checkbox',
							'ttype_select[256]'                     => 'input-checkbox',
							'ttype_select[524288]'                  => 'input-checkbox',
							'ttype_select[512]'                     => 'input-checkbox',
							'ttype_select[128]'                     => 'input-checkbox',
							'ttype_select[262144]'                  => 'input-checkbox',
							'ttype_select[16]'                      => 'input-checkbox',
							'ttype_select[32768]'                   => 'input-checkbox',
							'ttype_select[4096]'                    => 'input-checkbox',
							'ttype_select[8192]'                    => 'input-checkbox',
							'ttype_select[131072]'                  => 'input-checkbox',
							'ttype_select[65536]'                   => 'input-checkbox',
							'use_attributes'                        => 'input-radio',
							'selected_equipment_attributes[1024]'   => 'input-checkbox',
							'selected_equipment_attributes[128]'    => 'input-checkbox',
							'selected_equipment_attributes[131072]' => 'input-checkbox',
							'selected_equipment_attributes[16]'     => 'input-checkbox',
							'selected_equipment_attributes[32768]'  => 'input-checkbox',
							'selected_equipment_attributes[512]'    => 'input-checkbox',
							'selected_equipment_attributes[8]'      => 'input-checkbox',
							'selected_equipment_attributes[256]'    => 'input-checkbox',
							'selected_equipment_attributes[2]'      => 'input-checkbox',
							'selected_equipment_attributes[65536]'  => 'input-checkbox',
							'selected_equipment_attributes[262144]' => 'input-checkbox',
							'selected_equipment_attributes[4]'      => 'input-checkbox',
							'selected_equipment_attributes[1]'      => 'input-checkbox',
							'selected_equipment_attributes[64]'     => 'input-checkbox',
							'selected_equipment_attributes[4096]'   => 'input-checkbox',
							'selected_equipment_attributes[8192]'   => 'input-checkbox',
							'selected_equipment_attributes[16384]'  => 'input-checkbox',
							'selected_equipment_attributes[32]'     => 'input-checkbox',
							'selected_equipment_attributes[2048]'   => 'input-checkbox',
							'team'                                  => 'input-checkbox',
							'pay_amt'                               => 'input-checkbox',
							'favorites_only'                        => 'input-checkbox',
							'overweight'                            => 'input-checkbox',
							'fp'                                    => 'select',
							'weight'                                => 'input-text',
							'save_search_new'                       => 'input-checkbox',
							'new_search_name'                       => 'input-text',
							'save'                                  => 'input-button',
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
			$tag['search']['search_type'] = "radius_radius";
			$tag['search']['ttype'] = 64;
			$tag['search']['amask'] = 0;
			$tag['search']['unknown_start'] = '';
			$tag['search']['unknown_dest'] = '';
			$tag['search']['saved_search_count'] = '';
			$tag['search']['search_status'] = 'search';
			$tag['search']['prevTxt'] = 'Prev';
			$tag['search']['nextTxt'] = 'Next';
			$tag['search']['curTxt'] = 'Today';
			$tag['search']['radius_strict'] = 0;
			$tag['search']['s_radius'] = (($config->origin_radius != 0)?($config->origin_radius):"150");
			$tag['search']['d_radius'] = (($config->destination_radius != 0)?($config->destination_radius):"150");;
			$tag['search']['starting_point'] = $config->origin;
			$tag['search']['destination_point'] = $config->destination;
			$tag['search']['pickup_start_date'] = date('m/d/Y');
			$tag['search']['posted_within'] = 0;
			$tag['search']['ttype_select[64]'] = 64;
			$tag['search']['use_attributes'] = 'any';
			
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
				$this->addLoads($items);
			}
		} catch (Exception $ex) {
			$notify_error = new NotifyError("Getloaded - Jobboard has been changed. Please contact to VTNS.	\n");
			$notify_error->execute();
			return;
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