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
      'hash'               => new sfWidgetFormInputHidden(),
      'web_reference'      => new sfWidgetFormInputText(),
      'jobboard_id'        => new sfWidgetFormInputText(),
      'date'               => new sfWidgetFormDate(),
      'truck_type'         => new sfWidgetFormInputText(),
      'loads_type'         => new sfWidgetFormInputText(),
      'origin'             => new sfWidgetFormInputText(),
      'origin_radius'      => new sfWidgetFormInputText(),
      'destination'        => new sfWidgetFormInputText(),
      'destination_radius' => new sfWidgetFormInputText(),
      'contact'            => new sfWidgetFormInputText(),
      'distance'           => new sfWidgetFormInputText(),
      'company'            => new sfWidgetFormInputText(),
      'deadline'           => new sfWidgetFormInputText(),
      'created_at'         => new sfWidgetFormDateTime(),
      'updated_at'         => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'hash'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('hash')), 'empty_value' => $this->getObject()->get('hash'), 'required' => false)),
      'web_reference'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'jobboard_id'        => new sfValidatorInteger(),
      'date'               => new sfValidatorDate(),
      'truck_type'         => new sfValidatorString(array('max_length' => 20)),
      'loads_type'         => new sfValidatorString(array('max_length' => 20, 'required' => false)),
      'origin'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'origin_radius'      => new sfValidatorInteger(array('required' => false)),
      'destination'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'destination_radius' => new sfValidatorInteger(array('required' => false)),
      'contact'            => new sfValidatorString(array('max_length' => 255)),
      'distance'           => new sfValidatorInteger(array('required' => false)),
      'company'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'deadline'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
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
