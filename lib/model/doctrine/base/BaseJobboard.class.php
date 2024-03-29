<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Jobboard', 'doctrine');

/**
 * BaseJobboard
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $alias
 * @property string $name
 * @property string $generator_name
 * @property string $address
 * @property string $username
 * @property string $password
 * @property Doctrine_Collection $Configs
 * 
 * @method integer             getId()             Returns the current record's "id" value
 * @method string              getAlias()          Returns the current record's "alias" value
 * @method string              getName()           Returns the current record's "name" value
 * @method string              getGeneratorName()  Returns the current record's "generator_name" value
 * @method string              getAddress()        Returns the current record's "address" value
 * @method string              getUsername()       Returns the current record's "username" value
 * @method string              getPassword()       Returns the current record's "password" value
 * @method Doctrine_Collection getConfigs()        Returns the current record's "Configs" collection
 * @method Jobboard            setId()             Sets the current record's "id" value
 * @method Jobboard            setAlias()          Sets the current record's "alias" value
 * @method Jobboard            setName()           Sets the current record's "name" value
 * @method Jobboard            setGeneratorName()  Sets the current record's "generator_name" value
 * @method Jobboard            setAddress()        Sets the current record's "address" value
 * @method Jobboard            setUsername()       Sets the current record's "username" value
 * @method Jobboard            setPassword()       Sets the current record's "password" value
 * @method Jobboard            setConfigs()        Sets the current record's "Configs" collection
 * 
 * @package    emc
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseJobboard extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('jobboard');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('alias', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('generator_name', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('address', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('username', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
        $this->hasColumn('password', 'string', 255, array(
             'type' => 'string',
             'notnull' => false,
             'length' => 255,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('JobboardConfig as Configs', array(
             'local' => 'id',
             'foreign' => 'jobboard_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}