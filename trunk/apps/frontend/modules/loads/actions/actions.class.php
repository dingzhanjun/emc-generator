<?php

/**
 * loads actions.
 *
 * @package    emc
 * @subpackage loads
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class loadsActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
	$this->loads_form = new SearchingLoadsForm();
	$q = Doctrine_Query::create()
		->from('Loads l');
		
	if ($request->hasParameter('loads')) {
		$form_data = $request->getParameter('loads');
		$this->loads_form->bind($form_data);
		$q = $this->executeFilters($form_data, $q);
	}
	
	$this->sort_by = $request->getParameter('sort_by', 'created_at');
    if ($request->getParameter('sort_order', 1))
        $this->sort_order = 'desc';
    else
        $this->sort_order = 'asc';
    if (Doctrine_Core::getTable('Loads')->hasField($this->sort_by))
        $q->orderBy('l.'.$this->sort_by.' '.strtoupper($this->sort_order));

	//echo $q->getSQLQuery();
	$this->pager = new sfDoctrinePager('Loads', 20);
	$this->pager->setQuery($q);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();

	if ($request->isXmlHttpRequest()) {
        return 'Ajax';
    }
    return sfView::SUCCESS;
  }

 
  private function executeFilters($form_data, $q)
  {
	if (!empty($form_data['jobboard_id']) && $form_data['jobboard_id'] != 'all')
		$q->addWhere('l.jobboard_id = ?', $form_data['jobboard_id']);
	if (!empty($form_data['origin']))
		$q->addWhere('l.origin LIKE "%'.$form_data['origin'].'%"');
	if (!empty($form_data['origin_radius']))
		$q->addWhere('l.origin_radius <= ?', $form_data['origin_radius']);
	if (!empty($form_data['destination']))
		$q->addWhere('l.destination LIKE "%'.$form_data['destination'].'%"');
	if (!empty($form_data['destination_radius']))
		$q->addWhere('l.destination_radius <= ?', $form_data['destination_radius']);
		
	if (!empty($form_data['loads_age']))
		$q->addWhere('(unix_timestamp(now()) - unix_timestamp(l.created_at))/60 <= '.$form_data['loads_age']);
		
	return $q;
  }
}
