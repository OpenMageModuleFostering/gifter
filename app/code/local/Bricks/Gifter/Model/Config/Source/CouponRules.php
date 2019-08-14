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

class Bricks_Gifter_Model_Config_Source_CouponRules
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $coupons = array();

        foreach ($this->_getData() as $entry) {
            $coupons[] = array(
                'value' => $entry['rule_id'],
                'label' => $entry['name']
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
        $coupons = array();

        foreach ($this->_getData() as $entry) {
            $coupons[$entry['rule_id']] = $entry['name'];
        }

        return $coupons;
    }

    protected function _getData()
    {
        /** @var $collection Mage_SalesRule_Model_Resource_Rule_Collection */
        $collection = Mage::getModel('salesrule/rule')->getCollection()
            ->addFieldToFilter('coupon_type', Mage_SalesRule_Model_Rule::COUPON_TYPE_AUTO);

        $data = $collection->toArray(array('rule_id', 'name'));

        return $data['items'];
    }
}
