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
      'reference'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'jobboard_id'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'age'                => new sfWidgetFormFilterInput(),
      'date'               => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'truck_id'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'loads_type'         => new sfWidgetFormChoice(array('choices' => array('' => '', 'FULL' => 'FULL', 'PARTIAL' => 'PARTIAL', 'BOTH' => 'BOTH'))),
      'origin'             => new sfWidgetFormFilterInput(),
      'origin_radius'      => new sfWidgetFormFilterInput(),
      'destination'        => new sfWidgetFormFilterInput(),
      'destination_radius' => new sfWidgetFormFilterInput(),
      'contact'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'distance'           => new sfWidgetFormFilterInput(),
      'created_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'reference'          => new sfValidatorPass(array('required' => false)),
      'jobboard_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'age'                => new sfValidatorPass(array('required' => false)),
      'date'               => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'truck_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'loads_type'         => new sfValidatorChoice(array('required' => false, 'choices' => array('FULL' => 'FULL', 'PARTIAL' => 'PARTIAL', 'BOTH' => 'BOTH'))),
      'origin'             => new sfValidatorPass(array('required' => false)),
      'origin_radius'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'destination'        => new sfValidatorPass(array('required' => false)),
      'destination_radius' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'contact'            => new sfValidatorPass(array('required' => false)),
      'distance'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
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
      'id'                 => 'Number',
      'reference'          => 'Text',
      'jobboard_id'        => 'Number',
      'age'                => 'Text',
      'date'               => 'Date',
      'truck_id'           => 'Number',
      'loads_type'         => 'Enum',
      'origin'             => 'Text',
      'origin_radius'      => 'Number',
      'destination'        => 'Text',
      'destination_radius' => 'Number',
      'contact'            => 'Text',
      'distance'           => 'Number',
      'created_at'         => 'Date',
      'updated_at'         => 'Date',
    );
  }
}
