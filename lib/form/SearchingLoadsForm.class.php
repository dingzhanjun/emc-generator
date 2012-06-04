<?php
class SearchingLoadsForm extends LoadsForm
{
	public function configure()
	{
		$this->setWidgets(array(
			'jobboard_id'				=>		new sfWidgetFormChoice(array('choices' => $this->getJobboards())),
			'origin'					=>		new sfWidgetFormInput(),
			'origin_radius'				=>		new sfWidgetFormInput(),
			'destination'				=>		new sfWidgetFormInput(),
			'destination_radius'		=>		new sfWidgetFormInput(),		
		));
		
		$this->setValidators(array(
			'jobboard_id'				=>		new sfValidatorChoice(array('choices' => array_keys($this->getJobboards()), 'required' => false)),
			'origin'					=> 		new sfValidatorString(array('required' => false)),
			'origin_radius'				=>		new sfValidatorNumber(array('required' => false)),
			'destination'				=>		new sfValidatorString(array('required' => false)),
			'destination_radius'		=>		new sfValidatorNumber(array('required' => false)),
		));
		
		$this->widgetSchema->setNameFormat('loads[%s]');
		$this->widgetSchema->setLabels(array(
			'jobboard_id'				=>		'Website',
			'origin'					=>		'Origin',
			'origin_radius'				=>		'Origin Radius',
			'destination'				=>		'Destination',
			'destination_radius'		=>		'Destination Radius',
		));
	}
	
	
	private function getJobboards()
	{
		return array();
	}
}
?>