<?php
class CleanUpLogsTask extends sfBaseTask
{
	public function configure()
	{
		$this->namespace = 'generators';
	    $this->name      = 'clean-up-log';
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

	    date_default_timezone_set('America/Phoenix');
        
		$dir = dirname(dirname(dirname(__FILE__))).'/log/';
		
    	$handle = opendir($dir);

		while (($file = readdir($handle))!==false) {
			@unlink($dir.'/'.$file);
		}
		
		closedir($handle);
	}
}
?>