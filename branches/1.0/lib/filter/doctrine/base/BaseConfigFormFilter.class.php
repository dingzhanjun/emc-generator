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
      'name'                       => new sfWidgetFormFilterInput(),
      'max_age'                    => new sfWidgetFormFilterInput(),
      'origin'                     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'origin_radius'              => new sfWidgetFormFilterInput(),
      'origin_is_multistates'      => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'destination'                => new sfWidgetFormFilterInput(),
      'destination_radius'         => new sfWidgetFormFilterInput(),
      'destination_is_multistates' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'loads_type'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'length'                     => new sfWidgetFormFilterInput(),
      'weight'                     => new sfWidgetFormFilterInput(),
      'from_date'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'to_date'                    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'frequence'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'type'                       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'last_executed_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'created_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'name'                       => new sfValidatorPass(array('required' => false)),
      'max_age'                    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'origin'                     => new sfValidatorPass(array('required' => false)),
      'origin_radius'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'origin_is_multistates'      => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'destination'                => new sfValidatorPass(array('required' => false)),
      'destination_radius'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'destination_is_multistates' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'loads_type'                 => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'length'                     => new sfValidatorPass(array('required' => false)),
      'weight'                     => new sfValidatorPass(array('required' => false)),
      'from_date'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'to_date'                    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'frequence'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'type'                       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'last_executed_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'created_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
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
      'id'                         => 'Number',
      'name'                       => 'Text',
      'max_age'                    => 'Number',
      'origin'                     => 'Text',
      'origin_radius'              => 'Number',
      'origin_is_multistates'      => 'Boolean',
      'destination'                => 'Text',
      'destination_radius'         => 'Number',
      'destination_is_multistates' => 'Boolean',
      'loads_type'                 => 'Number',
      'length'                     => 'Text',
      'weight'                     => 'Text',
      'from_date'                  => 'Date',
      'to_date'                    => 'Date',
      'frequence'                  => 'Number',
      'type'                       => 'Number',
      'last_executed_at'           => 'Date',
      'created_at'                 => 'Date',
      'updated_at'                 => 'Date',
    );
  }
}
