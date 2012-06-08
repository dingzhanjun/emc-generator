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
      'max_age'            => new sfWidgetFormInputText(),
      'origin'             => new sfWidgetFormInputText(),
      'origin_radius'      => new sfWidgetFormInputText(),
      'destination'        => new sfWidgetFormInputText(),
      'destination_radius' => new sfWidgetFormInputText(),
      'frequence'          => new sfWidgetFormInputText(),
      'created_at'         => new sfWidgetFormDateTime(),
      'updated_at'         => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'max_age'            => new sfValidatorInteger(array('required' => false)),
      'origin'             => new sfValidatorString(array('max_length' => 255)),
      'origin_radius'      => new sfValidatorInteger(array('required' => false)),
      'destination'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'destination_radius' => new sfValidatorInteger(array('required' => false)),
      'frequence'          => new sfValidatorInteger(),
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
