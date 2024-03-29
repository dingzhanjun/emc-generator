<?php

/**
 * Config form.
 *
 * @package    emc
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SearchForm extends BaseConfigForm
{
	static $loads_types = array(
		'0'				=>	'BOTH',
		'1'				=>	'Full',
		'2'				=>	'Partial',
	);
  	public function configure()
	{
		$module = sfContext::getInstance()->getModuleName();	
		$this->setWidgets(array(
			'jobboard_id'				=>		new sfWidgetFormChoice(array('choices' => $this->getJobboards(), 'multiple' => true)),
			'truck_type'				=>		new sfWidgetFormChoice(array('choices' => $this->getTrucks(), 'multiple' => true)),
			'max_age'					=>		new sfWidgetFormInput(),
			'origin'					=>		new sfWidgetFormInput(),
            'origin_is_multistates'     =>      new sfWidgetFormInputCheckbox(),
			'origin_radius'				=>		new sfWidgetFormInput(),
			'destination'				=>		new sfWidgetFormInput(),
            'destination_is_multistates'=>      new sfWidgetFormInputCheckbox(),
			'destination_radius'		=>		new sfWidgetFormInput(),
			'loads_type'				=>		new sfWidgetFormChoice(array('choices' => self::$loads_types)),
			'length'					=> 		new sfWidgetFormInput(),
			'weight'					=>		new sfWidgetFormInput(),
		));
		
		
		$this->setValidators(array(
			'jobboard_id'				=>		new sfValidatorChoice(array('choices' => array_keys($this->getJobboards()), 'required' => true, 'multiple' => true)),
			'truck_type'				=>		new sfValidatorChoice(array('choices' => array_keys($this->getTrucks()), 'required' => true, 'multiple' => true)),
			'max_age'					=>		new sfValidatorString(array('required' => true)),
			'origin'					=> 		new sfValidatorString(array('required' => true)),
			'origin_radius'				=>		new sfValidatorNumber(array('required' => false)),
            'origin_is_multistates'     =>      new sfValidatorBoolean(array('required' => false)),
			'destination'				=>		new sfValidatorString(array('required' => false)),
            'destination_is_multistates'=>      new sfValidatorBoolean(array('required' => false)),
			'destination_radius'		=>		new sfValidatorNumber(array('required' => false)),
			'loads_type'				=>		new sfValidatorChoice(array('choices' => array_keys(self::$loads_types))),
			'length'					=>		new sfValidatorString(array('required' => false)),
			'weight'					=>		new sfValidatorString(array('required' => false)),
		));
		
		if ($module == 'quickSearch') {
			$this->setWidget('from_date', new sfWidgetFormInput());
			$this->setWidget('to_date', new sfWidgetFormInput());
			$this->setValidator('from_date', new sfValidatorString(array('required' => true)));
			$this->setValidator('to_date', new sfValidatorString(array('required' => true)));
		} elseif ($module == 'config') {
			$this->setWidget('frequence', new sfWidgetFormInput());
			$this->setValidator('frequence', new sfValidatorNumber(array('required' => true)));
		}
		
		$this->widgetSchema->setNameFormat('config[%s]');
		$this->widgetSchema->setLabels(array(
			'jobboard_id'				=>		'Website',
			'truck_type'				=>		'Truck Type',
			'max_age'					=>		'Max age (hour)',
			'origin'					=>		'Origin',
            'origin_is_multistates'     =>      'Origin Multistates',
			'origin_radius'				=>		'Origin Radius',
			'destination'				=>		'Destination',
            'destination_is_multistates'=>      'Destination Multistates',
			'destination_radius'		=>		'Destination Radius',
			'loads_type'				=>		'Full/Partial',
			'length'					=>		'Length',
			'weight'					=>		'Weight',
			'from_date'					=>		'From',
			'to_date'					=>		'To',
			'frequence'					=>		'Frequence',
		));
	}
	
	
	private function getJobboards()
	{
		$jobboards = array('all' => 'All');
		$q = Doctrine_Query::create()
			->from('Jobboard j');
		$res = $q->execute();
		foreach ($res as $j)
			$jobboards[$j->id] = $j->alias;
		return $jobboards;
	}
	
	
	private function getTrucks()
	{
		$trucks = array();
		$q = Doctrine_Query::create()
			->from('Truck t');
		$res = $q->execute();
		foreach ($res as $t)
			$trucks[$t->id] = $t->name;
		return $trucks;
	}
}