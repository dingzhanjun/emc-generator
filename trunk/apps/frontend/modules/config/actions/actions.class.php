<?php

/**
 * config actions.
 *
 * @package    emc
 * @subpackage config
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class configActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
	$this->config_form = new SearchingConfigForm();
	$q = Doctrine_Query::create()
		->from('Config c');
	if ($request->hasParameter('search_config')) {
		$form_data = $request->getParameter('search_config');
		$q = $this->executeFilters($form_data, $q);
	}
	
    $this->sort_by = $request->getParameter('sort_by', 'created_at');
    if ($request->getParameter('sort_order', 1))
        $this->sort_order = 'desc';
    else
        $this->sort_order = 'asc';
    if (Doctrine_Core::getTable('Config')->hasField($this->sort_by))
        $q->orderBy('c.'.$this->sort_by.' '.strtoupper($this->sort_order));

	//echo $q->getSQLQuery();
	$this->pager = new sfDoctrinePager('Config', 20);
	$this->pager->setQuery($q);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();

	if ($request->isXmlHttpRequest()) {
        return 'Ajax';
    }
    return sfView::SUCCESS;
  }


  public function executeEdit(sfWebRequest $request)
  {
	if ($request->hasParameter('config_id')) {
		$config_id = $request->getParameter('config_id');
		$config = Doctrine_Core::getTable('Config')->find($config_id);
	} else {
		$config = new Config();
	}
	$this->config_form = new ConfigForm();
	if ($request->hasParameter('config')) {
		$data = $request->getParameter('config');
		$this->config_form->bind($data);
		if ($this->config_form->isValid()) {
			$config = $this->updateConfig($data, $config);
			$this->forward('config', 'index');
		}
	}
	return sfView::SUCCESS;
  }


  private function executeFilters($form_data, $q) {
	if (!empty($form_data['jobboard_id']))
		$q->andWhereIn('c.jobboard_id', $form_data['jobboard_id']);
	if (!empty($form_data['max_age']))
		$q->addWhere('c.max_age = ?', $form_data['max_age']);
	if (!empty($form_data['origin']))
		$q->addWhere('c.origin LIKE "%'.$form_data['origin'].'%"');
	if (!empty($form_data['origin_radius']))
		$q->addWhere('c.origin_radius = ?', $form_data['origin_radius']);
	if (!empty($form_data['destination']))
		$q->addWhere('c.destination LIKE "%'.$form_data['destination'].'%"');
	if (!empty($form_data['destination_radius']))
		$q->addWhere('c.destination_radius = ?', $form_data['destination_radius']);
	if (!empty($form_data['frequence']))
		$q->addWhere('c.frequence = ?', $form_data['frequence']);
	return $q;
  }


  private function updateConfig($form, $config)
  {
	$config->max_age = $form['max_age'];
	$config->origin = $form['origin'];
	$config->origin_radius = $form['origin_radius'];
	$config->destination = $form['destination'];
	$config->destination_radius = $form['destination_radius'];
	$config->frequence = $form['frequence'];
	$config->save();
	
	// jobboard config
	$jobboard_ids = $form['jobboard_id'];
	$jobboards_configs = $config->JobboardConfigs;
	if (!empty($jobboards_configs))
		foreach ($jobboard_configs as $jobboard_config)
			$jobboard_config->delete();

	foreach ($jobboard_ids as $jobboard_id) {
		$jobboard_config = new JobboardConfig();
		$jobboard_config->jobboard_id = $jobboard_id;
		$jobboard_config->config_id = $config->id;
		$jobboard_config->save();
	}
  }
}
