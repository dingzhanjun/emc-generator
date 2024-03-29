<?php

/**
 * ConfigTruckTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ConfigTruckTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object ConfigTruckTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('ConfigTruck');
    }
	public function deleteConfigId($config_id)
    {
		$q = $this->createQuery('c')
            ->where('c.config_id = ?',$config_id);
        $result = $q->execute();
        $result->delete();
	}
}