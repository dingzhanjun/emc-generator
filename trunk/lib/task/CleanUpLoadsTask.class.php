<?php
class CleanUpLoadsTask extends sfBaseTask
{
	public function configure()
	{
		$this->namespace = 'generators';
	    $this->name      = 'clean-up';
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
	    $q = Doctrine_Query::create()
	        ->from('Loads l')
	        ->addWhere('DATEDIFF(now(), l.created_at) > ?', 1);
	    $loads_list = $q->execute();
	    $count = 0;
	    foreach ($loads_list as $loads) {
	        $this->logSection('info', 'Deleting loads with hash code: '.$loads->hash.' and created_date: '.$loads->created_at);
	        $loads->delete();
	        $count++;
	    }
	    $this->logSection('info', 'Deleted total: '.$count.' loads');
	}
}
?>