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

class Bricks_Gifter_Block_Adminhtml_Gift extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'promo_gifter';
        $this->_headerText = $this->__('Gifts');

        parent::__construct();

        $this->removeButton('add');
    }

    protected function _prepareLayout()
    {
        return Mage_Adminhtml_Block_Widget_Container::_prepareLayout();
    }
}
