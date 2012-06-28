<?php

/**
 * quickSearch actions.
 *
 * @package    emc
 * @subpackage quickSearch
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class quickSearchActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->search_form = new ConfigForm();
	$this->configs = Doctrine_Query::create()->from('Config')->execute();
    if ($request->hasParameter('config')) {
        $form_data = $request->getParameter('config');
        $this->search_form->bind($form_data);
        if ($this->search_form->isValid()) {
			$config_id = $request->getParameter('config_id');
			$config_save = $request->getParameter('config_id');
			if ($config_id != 0)
				$config = Doctrine_Core::getTable('Config')->find($config_id);
			else 
            	$config = new Config();
			
            $config->type = 1; // TODO put a constant here
			$config->name = $request->getParameter('config_name');
            $config->save();
            $this->updateConfig($form_data, $config);
            
            // process
            $this->loads = array();
            $jobboard_configs = Doctrine_Query::create()->from('JobboardConfig jc')->addWhere('jc.config_id = ?', $config->id)->execute();
            foreach ($jobboard_configs as $jobboard_config) {
                $jobboard = $jobboard_config->Jobboard;
                $generator_name = $jobboard->name.'Generator';
                $generator = new $generator_name($config->id, $jobboard->name);
                if ($generator) {
                    $generator->execute();
                    $this->loads[$jobboard->name] = $generator->getLoads();
                }
            }			
			
			if ($config_save != 'on')
			{
				$deleted = Doctrine_Query::create()->delete()->from('JobboardConfig c')->andWhere('c.config_id = ?', $config->id)->execute();
				$deleted = Doctrine_Query::create()->delete()->from('ConfigTruck c')->andWhere('c.config_id = ?', $config->id)->execute();
				$deleted = Doctrine_Query::create()->delete()->from('Config c')->andWhere('c.id = ?', $config->id)->execute();
			}
            //$this->setlayout(false);
            //$this->getResponse()->setHttpHeader('Content-Type', 'text/csv');
            //$this->getResponse()->setHttpHeader('Content-Disposition', 'attachment; filename=quick_search ' . date("Y-m-d_Hi") . '.csv');
            //return 'CSV';
			return sfView::SUCCESS;
        }   
    }
    return sfView::SUCCESS;
  }

  public function executeReload(sfWebRequest $request) {
	  
	  $config_id = $request->getParameter("config_id");
	  $config = Doctrine_Core::getTable('Config')->find($config_id);
 	  $jobboard_configs = $config->JobboardConfigs;
	  $config_trucks = $config->ConfigTrucks;
	  $config_form = array();
	  
	  $jobboard_ids = ""; 
	  foreach ($jobboard_configs as $jobboard_config) {
		  $jobboard_ids .= $jobboard_config->jobboard_id.";";
	  }
	 
	  $truck_ids = "";
	  foreach ($config_trucks as $config_truck) {
		  $truck_ids .= $config_truck->truck_id.";";
	  }
	   
	  $config_form["config[jobboard_id][]"] = $jobboard_ids;
	  $config_form["config[truck_type][]"] = $truck_ids;
	  $config_form["config[max_age]"] = $config->max_age;
	  $config_form["config[origin]"] = $config->origin;
	  $config_form["config[origin_radius]"] = $config->origin_radius;
	  $config_form["config[destination]"] = $config->destination;
	  $config_form["config[destination_radius]"] = $config->destination_radius;
	  $config_form["config[loads_type]"] = $config->loads_type;
	  $config_form["config[length]"] = $config->length;
	  $config_form["config[weight]"] = $config->weight;
	  $config_form["config[from_date]"] = $config->from_date;
	  $config_form["config[to_date]"] = $config->to_date;
	  $this->config_form_new = json_encode($config_form);
	  return SfView::SUCCESS;
  }


  private function updateConfig($form, $config)
  {
    $config->max_age = $form['max_age'];
    $config->origin = $form['origin'];
    $config->origin_radius = $form['origin_radius'];
    $config->destination = $form['destination'];
    $config->destination_radius = $form['destination_radius'];
    $config->loads_type = $form['loads_type'];
    $config->length = $form['length'];
    $config->weight = $form['weight'];
    $config->from_date = date('d/m/y', strtotime($form['from_date']));
    $config->to_date = date('d/m/y', strtotime($form['to_date']));
    $config->save();

    // jobboard config
    $jobboard_ids = $form['jobboard_id'];
    $jobboard_configs = $config->JobboardConfigs;
    if (!empty($jobboard_configs))
        foreach ($jobboard_configs as $jobboard_config)
            $jobboard_config->delete();

    if ($jobboard_ids[0] == 'all') {
        $jobboards = Doctrine_Query::create()->from('Jobboard j')->execute();
        $jobboard_ids = array();
        foreach ($jobboards as $jobboard)
            $jobboard_ids[] = $jobboard->id;
    }

    foreach ($jobboard_ids as $jobboard_id) {
        $jobboard_config = new JobboardConfig();
        $jobboard_config->jobboard_id = $jobboard_id;
        $jobboard_config->config_id = $config->id;
        $jobboard_config->save();
    }

    // truck types
    $truck_types = $form['truck_type'];
    $config_trucks = $config->ConfigTrucks;
    if (!empty($config_trucks))
        foreach ($config_trucks as $config_truck)
            $config_truck->delete();
    foreach ($truck_types as $truck_id) {
        $config_truck = new ConfigTruck();
        $config_truck->truck_id = $truck_id;
        $config_truck->config_id = $config->id;
        $config_truck->save();
    }
  }
}