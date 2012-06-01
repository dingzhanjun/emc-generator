<?php

/**
 * Loads form base class.
 *
 * @method Loads getObject() Returns the current form's model object
 *
 * @package    emc
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLoadsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputHidden(),
      'reference'          => new sfWidgetFormTextarea(),
      'jobboard_id'        => new sfWidgetFormInputText(),
      'age'                => new sfWidgetFormTime(),
      'date'               => new sfWidgetFormDate(),
      'truck_id'           => new sfWidgetFormInputText(),
      'loads_type'         => new sfWidgetFormChoice(array('choices' => array('FULL' => 'FULL', 'PARTIAL' => 'PARTIAL', 'BOTH' => 'BOTH'))),
      'origin'             => new sfWidgetFormTextarea(),
      'origin_radius'      => new sfWidgetFormInputText(),
      'destination'        => new sfWidgetFormTextarea(),
      'destination_radius' => new sfWidgetFormInputText(),
      'contact'            => new sfWidgetFormTextarea(),
      'distance'           => new sfWidgetFormInputText(),
      'created_at'         => new sfWidgetFormDateTime(),
      'updated_at'         => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'reference'          => new sfValidatorString(),
      'jobboard_id'        => new sfValidatorInteger(),
      'age'                => new sfValidatorTime(array('required' => false)),
      'date'               => new sfValidatorDate(),
      'truck_id'           => new sfValidatorInteger(),
      'loads_type'         => new sfValidatorChoice(array('choices' => array(0 => 'FULL', 1 => 'PARTIAL', 2 => 'BOTH'), 'required' => false)),
      'origin'             => new sfValidatorString(array('required' => false)),
      'origin_radius'      => new sfValidatorInteger(array('required' => false)),
      'destination'        => new sfValidatorString(array('required' => false)),
      'destination_radius' => new sfValidatorInteger(array('required' => false)),
      'contact'            => new sfValidatorString(),
      'distance'           => new sfValidatorInteger(array('required' => false)),
      'created_at'         => new sfValidatorDateTime(),
      'updated_at'         => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('loads[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Loads';
  }

}
