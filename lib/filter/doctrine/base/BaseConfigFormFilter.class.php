<?php

/**
 * Config filter form base class.
 *
 * @package    emc
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseConfigFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'max_age'            => new sfWidgetFormFilterInput(),
      'origin'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'origin_radius'      => new sfWidgetFormFilterInput(),
      'destination'        => new sfWidgetFormFilterInput(),
      'destination_radius' => new sfWidgetFormFilterInput(),
      'frequence'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'max_age'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'origin'             => new sfValidatorPass(array('required' => false)),
      'origin_radius'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'destination'        => new sfValidatorPass(array('required' => false)),
      'destination_radius' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'frequence'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('config_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Config';
  }

  public function getFields()
  {
    return array(
      'id'                 => 'Number',
      'max_age'            => 'Number',
      'origin'             => 'Text',
      'origin_radius'      => 'Number',
      'destination'        => 'Text',
      'destination_radius' => 'Number',
      'frequence'          => 'Number',
      'created_at'         => 'Date',
      'updated_at'         => 'Date',
    );
  }
}