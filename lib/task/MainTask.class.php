<?php
class MainTask extends sfBaseTask
{
	public function configure()
	{
		$this->namespace = 'generators';
	    $this->name      = 'main-task';
	    $this->addOptions(array(
	            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
	            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
	            new sfCommandOption('exp', null, sfCommandOption::PARAMETER_OPTIONAL, 'The new expiration date', ''),
	            ));
	}
	public function execute($arguments = array(), $options = array())
	{
		// initialize database connection  
	    $databaseManager = new sfDatabaseManager($this->configuration);
	    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

	    date_default_timezone_set('Asia/Bangkok');
	    $client = new WebFormClient();
		$configs = Doctrine_Query::create()
			->from('Config c')
			->addWhere('c.type = ?', 0)
			->execute();
			
		foreach ($configs as $config)
			if (strtotime("now") - strtotime($config->last_executed_at) >= ($config->frequence*60)) {
				$config->last_executed_at = date(DATE_ISO8601);
				$config->save();
				$this->logSection('info', 'Executing config with id:'.$config->id);
				$jobboard_configs = $config->JobboardConfigs;
				foreach ($jobboard_configs as $jobboard_config) {
					$jobboard = $jobboard_config->Jobboard;
					$this->logSection('info', 'Executing generator for '.$jobboard->name);

					$generator = new $jobboard->generator_name($this->dispatcher, $this->formatter);
					if ($generator) {
						//$arguments = array("Los Angeles, CA", 200, 2);
						$options = array();
						$generator->run($arguments = array($config->id), $options = array());
				}
			}
		}
	}
}
?>