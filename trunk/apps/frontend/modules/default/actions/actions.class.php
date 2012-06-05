<?php

/**
 * default actions.
 *
 * @package    DUYTAN
 * @subpackage default
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class defaultActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
		return sfView::SUCCESS;
  }

  public function executeLogout($request)
  {
		if ($request->hasParameter('is_log_out'))
			$this->getUser()->signOut();
		$this->forward('default', 'index');
  }
}