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

class Bricks_Gifter_Model_Config_Source_CouponFormats
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        foreach ($this->toOptionHash() as $value => $label) {
            $coupons[] = array(
                'value' => $value,
                'label' => $label
            );
        }

        return $coupons;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toOptionHash()
    {
        /** @var $helper Bricks_Gifter_Helper_Data */
        $helper = Mage::helper('Bricks_Gifter/Data');

        return $helper->getFormatsList();
    }
}
