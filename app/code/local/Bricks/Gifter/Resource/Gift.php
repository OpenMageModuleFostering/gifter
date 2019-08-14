<?php
/**
 * NOTICE OF LICENSE
 *
 *
 * @category   Mage
 * @package    Bricks_Gifter
 * @license    GPL
 * @author     Adam <adam@bricksandmortarweb.com>
 */

class Bricks_Gifter_Resource_Gift extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Set table and primary key
     */
    protected function _construct()
    {
        $this->_init('Bricks_Gifter/Gift', 'id');
    }
}
