<?php

/**
 * Loads filter form base class.
 *
 * @package    emc
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLoadsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'web_reference'      => new sfWidgetFormFilterInput(),
      'jobboard_id'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'date'               => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'truck_type'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'loads_type'         => new sfWidgetFormFilterInput(),
      'origin'             => new sfWidgetFormFilterInput(),
      'origin_radius'      => new sfWidgetFormFilterInput(),
      'destination'        => new sfWidgetFormFilterInput(),
      'destination_radius' => new sfWidgetFormFilterInput(),
      'contact'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'distance'           => new sfWidgetFormFilterInput(),
      'company'            => new sfWidgetFormFilterInput(),
      'created_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'web_reference'      => new sfValidatorPass(array('required' => false)),
      'jobboard_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'date'               => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'truck_type'         => new sfValidatorPass(array('required' => false)),
      'loads_type'         => new sfValidatorPass(array('required' => false)),
      'origin'             => new sfValidatorPass(array('required' => false)),
      'origin_radius'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'destination'        => new sfValidatorPass(array('required' => false)),
      'destination_radius' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'contact'            => new sfValidatorPass(array('required' => false)),
      'distance'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'company'            => new sfValidatorPass(array('required' => false)),
      'created_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('loads_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Loads';
  }

  public function getFields()
  {
    return array(
      'hash'               => 'Text',
      'web_reference'      => 'Text',
      'jobboard_id'        => 'Number',
      'date'               => 'Date',
      'truck_type'         => 'Text',
      'loads_type'         => 'Text',
      'origin'             => 'Text',
      'origin_radius'      => 'Number',
      'destination'        => 'Text',
      'destination_radius' => 'Number',
      'contact'            => 'Text',
      'distance'           => 'Number',
      'company'            => 'Text',
      'created_at'         => 'Date',
      'updated_at'         => 'Date',
    );
  }
}
