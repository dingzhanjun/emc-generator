<?php

/**
 * Loads
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    emc
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Loads extends BaseLoads
{
	public function getCurrentLoadsAge() {
		$adding_time = strtotime("now") - strtotime($this->created_at);
		$age = date('Y-m-d').' '.$this->age;
		$age = $adding_time + strtotime($age);
		return date('H:i:s', $age);
	}
}
