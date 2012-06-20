<?php

/**
 * Config form base class.
 *
 * @method Config getObject() Returns the current form's model object
 *
 * @package    emc
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseConfigForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputHidden(),
      'name'               => new sfWidgetFormTextarea(),
      'max_age'            => new sfWidgetFormInputText(),
      'origin'             => new sfWidgetFormInputText(),
      'origin_radius'      => new sfWidgetFormInputText(),
      'destination'        => new sfWidgetFormInputText(),
      'destination_radius' => new sfWidgetFormInputText(),
      'loads_type'         => new sfWidgetFormInputText(),
      'length'             => new sfWidgetFormInputText(),
      'weight'             => new sfWidgetFormInputText(),
      'from_date'          => new sfWidgetFormDate(),
      'to_date'            => new sfWidgetFormDate(),
      'frequence'          => new sfWidgetFormInputText(),
      'type'               => new sfWidgetFormInputText(),
      'last_executed_at'   => new sfWidgetFormInputText(),
      'created_at'         => new sfWidgetFormDateTime(),
      'updated_at'         => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'               => new sfValidatorString(array('required' => false)),
      'max_age'            => new sfValidatorInteger(array('required' => false)),
      'origin'             => new sfValidatorString(array('max_length' => 255)),
      'origin_radius'      => new sfValidatorInteger(array('required' => false)),
      'destination'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'destination_radius' => new sfValidatorInteger(array('required' => false)),
      'loads_type'         => new sfValidatorInteger(),
      'length'             => new sfValidatorPass(array('required' => false)),
      'weight'             => new sfValidatorPass(array('required' => false)),
      'from_date'          => new sfValidatorDate(),
      'to_date'            => new sfValidatorDate(),
      'frequence'          => new sfValidatorInteger(),
      'type'               => new sfValidatorInteger(array('required' => false)),
      'last_executed_at'   => new sfValidatorPass(array('required' => false)),
      'created_at'         => new sfValidatorDateTime(),
      'updated_at'         => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('config[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Config';
  }

}
