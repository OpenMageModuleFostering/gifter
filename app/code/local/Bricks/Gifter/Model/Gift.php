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

class Bricks_Gifter_Model_Gift extends Mage_Core_Model_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'bricks_gifter_gift';

    /**
     * @var string
     */
    protected $_eventObject = 'gift';

    /**
     * Initialize Model
     */
    protected function _construct()
    {
        $this->_init('Bricks_Gifter/Gift');
    }
}
