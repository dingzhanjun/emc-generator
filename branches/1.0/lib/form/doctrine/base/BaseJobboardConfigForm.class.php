<?php

/**
 * JobboardConfig form base class.
 *
 * @method JobboardConfig getObject() Returns the current form's model object
 *
 * @package    emc
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseJobboardConfigForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'jobboard_id' => new sfWidgetFormInputHidden(),
      'config_id'   => new sfWidgetFormInputHidden(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'jobboard_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('jobboard_id')), 'empty_value' => $this->getObject()->get('jobboard_id'), 'required' => false)),
      'config_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('config_id')), 'empty_value' => $this->getObject()->get('config_id'), 'required' => false)),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('jobboard_config[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'JobboardConfig';
  }

}
