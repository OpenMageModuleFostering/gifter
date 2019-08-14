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

class Bricks_Gifter_Helper_Data extends Mage_SalesRule_Helper_Coupon
{
    const XML_PATH_GIFTER_ENABLED = 'bricks/gifter/enabled';
    const XML_PATH_GIFTER_COUPON_RULE = 'bricks/gifter/coupon_rule';
    const XML_PATH_GIFTER_COUPON_BATCH = 'bricks/gifter/coupon_batch';
    const XML_PATH_GIFTER_COUPON_LENGTH = 'bricks/gifter/coupon_code_length';
    const XML_PATH_GIFTER_COUPON_FORMAT = 'bricks/gifter/coupon_code_format';
    const XML_PATH_GIFTER_COUPON_PREFIX = 'bricks/gifter/coupon_code_prefix';
    const XML_PATH_GIFTER_COUPON_SUFFIX = 'bricks/gifter/coupon_code_suffix';
    const XML_PATH_GIFTER_COUPON_DASH_INTERVAL = 'bricks/gifter/coupon_code_dash_interval';
    const XML_PATH_GIFTER_EMAIL_BATCH = 'bricks/gifter/email_batch';
    const XML_PATH_GIFTER_EMAIL_SENDER = 'bricks/gifter/email_sender';
    const XML_PATH_GIFTER_EMAIL_TEMPLATE = 'bricks/gifter/email_template';

    public function getEnabledStores()
    {
        $stores = Mage::app()->getStores();

        foreach ($stores as $id => $store) {
            if (!$store->getIsActive() || !$this->isStoreEnabled($store)) {
                unset($stores[$id]);
            }
        }

        return $stores;
    }

    public function isStoreEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_GIFTER_ENABLED, $store);
    }

    public function getCouponBatch($store = null)
    {
        return max((int)Mage::getStoreConfig(self::XML_PATH_GIFTER_COUPON_BATCH, $store), 1);
    }

    /**
     * @param null $store
     *
     * @return Mage_SalesRule_Model_Rule|null
     */
    public function getCouponRule($store = null)
    {
        $ruleId = Mage::getStoreConfig(self::XML_PATH_GIFTER_COUPON_RULE, $store);
        $rule = Mage::getModel('salesrule/rule')->load($ruleId);
        if ($rule->getId()) {
            if($rule->getCouponType() == Mage_SalesRule_Model_Rule::COUPON_TYPE_AUTO) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Get coupon code length
     *
     * @return int
     */
    public function getCouponCodeLength($store = null)
    {
        $value = Mage::getStoreConfig(self::XML_PATH_GIFTER_COUPON_LENGTH, $store);
        if (!$value) {
            $value = $this->getDefaultLength();
        }
        return (int)$value;
    }

    /**
     * Get coupon code format
     *
     * @return string
     */
    public function getCouponCodeFormat($store = null)
    {
        $value = Mage::getStoreConfig(self::XML_PATH_GIFTER_COUPON_FORMAT, $store);
        if (!$value) {
            $value = $this->getDefaultFormat();
        }
        return $value;
    }

    /**
     * Get coupon code prefix
     *
     * @return string
     */
    public function getCouponCodePrefix($store = null)
    {
        $value = Mage::getStoreConfig(self::XML_PATH_GIFTER_COUPON_PREFIX, $store);
        if ($value === null) {
            $value = $this->getDefaultPrefix();
        }
        return $value;
    }

    /**
     * Get coupon code suffix
     *
     * @return string
     */
    public function getCouponCodeSuffix($store = null)
    {
        $value = Mage::getStoreConfig(self::XML_PATH_GIFTER_COUPON_SUFFIX, $store);
        if ($value === null) {
            $value = $this->getDefaultSuffix();
        }
        return $value;
    }

    /**
     * Get dashes occurrences frequency in coupon code
     *
     * @return int
     */
    public function getCouponCodeDashInterval($store = null)
    {
        $value = Mage::getStoreConfig(self::XML_PATH_GIFTER_COUPON_DASH_INTERVAL, $store);
        if (!$value) {
            $value = $this->getDefaultDashInterval();
        }
        return (int)$value;
    }

    public function getEmailBatch($store = null)
    {
        return max((int)Mage::getStoreConfig(self::XML_PATH_GIFTER_EMAIL_BATCH, $store), 1);
    }

    public function getEmailSender($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_GIFTER_EMAIL_SENDER, $store);
    }

    public function getEmailTemplate($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_GIFTER_EMAIL_TEMPLATE, $store);
    }
}
