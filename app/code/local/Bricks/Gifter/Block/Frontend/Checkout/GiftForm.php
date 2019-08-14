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
class Bricks_Gifter_Block_Frontend_Checkout_GiftForm extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        if (!Mage::helper('Bricks_Gifter/Data')->isStoreEnabled()) {
            return '';
        } else {
            return parent::_toHtml();
        }
    }
}
