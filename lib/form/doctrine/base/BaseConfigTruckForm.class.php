<?php

/**
 * ConfigTruck form base class.
 *
 * @method ConfigTruck getObject() Returns the current form's model object
 *
 * @package    emc
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseConfigTruckForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'config_id'  => new sfWidgetFormInputHidden(),
      'truck_id'   => new sfWidgetFormInputHidden(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'config_id'  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('config_id')), 'empty_value' => $this->getObject()->get('config_id'), 'required' => false)),
      'truck_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('truck_id')), 'empty_value' => $this->getObject()->get('truck_id'), 'required' => false)),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('config_truck[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ConfigTruck';
  }

}
