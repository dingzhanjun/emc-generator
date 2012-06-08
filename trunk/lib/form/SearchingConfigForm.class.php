<?php
class SearchingConfigForm extends LoadsForm
{
	public function configure()
	{
		$this->setWidgets(array(
			'jobboard_id'				=>		new sfWidgetFormChoice(array('choices' => $this->getJobboards(), 'multiple' => true)),
			'max_age'					=>		new sfWidgetFormInput(),
			'origin'					=>		new sfWidgetFormInput(),
			'origin_radius'				=>		new sfWidgetFormInput(),
			'destination'				=>		new sfWidgetFormInput(),
			'destination_radius'		=>		new sfWidgetFormInput(),
			'frequence'					=>		new sfWidgetFormInput(),	
		));
		
		$this->setValidators(array(
			'jobboard_id'				=>		new sfValidatorChoice(array('choices' => array_keys($this->getJobboards()), 'required' => false)),
			'max_age'					=>		new sfValidatorString(array('required' => false)),
			'origin'					=> 		new sfValidatorString(array('required' => false)),
			'origin_radius'				=>		new sfValidatorNumber(array('required' => false)),
			'destination'				=>		new sfValidatorString(array('required' => false)),
			'destination_radius'		=>		new sfValidatorNumber(array('required' => false)),
			'frequence'					=>		new sfValidatorNumber(array('required' => false)),
		));
		
		$this->widgetSchema->setNameFormat('search_config[%s]');
		$this->widgetSchema->setLabels(array(
			'jobboard_id'				=>		'Website',
			'max_age'					=>		'Max age',
			'origin'					=>		'Origin',
			'origin_radius'				=>		'Origin Radius',
			'destination'				=>		'Destination',
			'destination_radius'		=>		'Destination Radius',
			'frequence'					=>		'Frequence',
		));
	}
	
	
	private function getJobboards()
	{
		$jobboards = array();
		$q = Doctrine_Query::create()
			->from('Jobboard j');
		$res = $q->execute();
		foreach ($res as $j)
			$jobboards[$j->id] = $j->name;
		return $jobboards;
	}
}
?>