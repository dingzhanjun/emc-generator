<?php

/**
 * Notify form base class.
 *
 * @method Notify getObject() Returns the current form's model object
 *
 * @package    emc
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseNotifyForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'notify_id'  => new sfWidgetFormInputHidden(),
      'content'    => new sfWidgetFormInputText(),
      'status'     => new sfWidgetFormInputCheckbox(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'notify_id'  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('notify_id')), 'empty_value' => $this->getObject()->get('notify_id'), 'required' => false)),
      'content'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'status'     => new sfValidatorBoolean(array('required' => false)),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('notify[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Notify';
  }

}
