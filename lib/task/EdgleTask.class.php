<?php
class webFormClientTask extends sfBaseTask
{
  public function configure()
  {
    $this->namespace = 'backend';
    $this->name      = 'web-form';
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
	//$client->setLogpPrefix('vtns');
	$client->get('https://www.truckersedge.net/a/secure/login.aspx?app=truckersedge');
	$client->info();
	
  }
}
?>