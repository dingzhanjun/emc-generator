<?php
class FreightviewGeneratorTask extends sfBaseTask
{
  public function configure()
  {
    $this->namespace = 'generators';
    $this->name      = 'freightview';
    $this->addOptions(array(
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            new sfCommandOption('exp', null, sfCommandOption::PARAMETER_OPTIONAL, 'The new expiration date', ''),
            ));

    $this->addArgument('config', sfCommandArgument::REQUIRED, 'The config id');
  }
 
  
  public function execute($arguments = array(), $options = array())
  {
    // initialize database connection  
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    date_default_timezone_set('America/Phoenix');
    $client = new WebFormClient();

    // getting config
    $config_id = $arguments['config'];
    $generator = new FreightviewGenerator($config_id, 'Freightview');
    $generator->execute();
    $loads = $generator->getLoads();
    foreach ($loads as $item)
        $this->addLoads($item);
  }


  private function addLoads($items)
  {
      $loads_values = $items;
      unset($loads_values[6]);
      $hash = md5(json_encode($loads_values));
      $loads = Doctrine_Core::getTable('Loads')->findOneByHash($hash);
      if ($loads) {
          $this->logSection('info', 'Loads found but exists !');
      } else {
          $loads = new Loads();
          $loads->jobboard_id = Doctrine_Core::getTable('Jobboard')->findOneByName('Freightview')->id;
          $loads->date = date('Y-m-d', strtotime($items[1]));
          $loads->truck_type = $items[9];
          $loads->origin = $items[2].' '.$items[3];
          $loads->distance = $items[8];
          $loads->destination = $items[4].' '.$items[5];
          $loads->company = $items[7].' Ref: '.$items[0];
          $loads->deadline = $items[6];
          $loads->hash = $hash;
          $this->logSection('info', 'Found new loads with hash code '.$hash);
          $loads->save();
    }
  }
}
?>