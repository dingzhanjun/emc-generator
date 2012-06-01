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
    $tag['login']['ctl00$cphMain$txtUserName'] = 'southernrt';
    $tag['login']['ctl00$cphMain$txtPassword'] = 'S4lt4you';
    $tag['login']['ctl00$cphMain$hfUserTimezoneOffset'] = -420;
    $client->fill($tag['login']);
    $client->post('https://www.truckersedge.net/a/secure/login.aspx?app=truckersedge&');
    $client->get('http://www.truckersedge.net/a/app/default.aspx');
    
    if (!preg_match('#TruckersEdge.net - My Overview#', $client->getBody(), $match)) {
        $this->logSection('info', "Login fail");
        exit;
    }
    $this->logSection('info', 'login success !!! Redirecting to seaching page');
    $client->get('http://www.truckersedge.net/a/app/Search.aspx');
    $client->info();
  }
}
?>