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

class Bricks_Gifter_Model_CouponGenerator extends Mage_SalesRule_Model_Coupon_Codegenerator
{
    const CHARSET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    const MAX_GENERATE_ATTEMPTS = 10;

    protected $_generatedCodes = array();

    /**
     * Count of generated Coupons
     *
     * @var int
     */
    protected $_generatedCount = 0;

    /**
     * Generate coupon code
     *
     * @return string
     */
    public function generateCode()
    {
        $length = max(1, (int)$this->getLength());
        $split = max(0, (int)$this->getDash());
        $suffix = $this->getSuffix();
        $prefix = $this->getPrefix();

        $splitChar = $this->getDelimiter();
        $charset = (string)self::CHARSET;

        $code = '';
        $charsetSize = strlen($charset);
        for ($i = 0; $i < $length; $i++) {
            $char = $charset[mt_rand(0, $charsetSize - 1)];
            if ($split > 0 && ($i % $split) == 0 && $i != 0) {
                $char = $splitChar . $char;
            }
            $code .= $char;
        }

        $code = $prefix . $code . $suffix;
        return $code;
    }

    /**
     * Retrieve delimiter
     *
     * @return string
     */
    public function getDelimiter()
    {
        if ($this->getData('delimiter')) {
            return $this->getData('delimiter');
        } else {
            return '-';
        }
    }

    /**
     * Generate Coupons Pool
     *
     * @return Bricks_Gifter_Model_CouponGenerator
     */
    public function generatePool()
    {
        $this->_generatedCodes = array();
        $this->_generatedCount = 0;
        $size = $this->getQty();

        $maxAttempts = $this->getMaxAttempts() ? $this->getMaxAttempts() : self::MAX_GENERATE_ATTEMPTS;

        /** @var $coupon Mage_SalesRule_Model_Coupon */
        $coupon = Mage::getModel('salesrule/coupon');

        for ($i = 0; $i < $size; $i++) {
            $attempt = 0;
            do {
                if ($attempt >= $maxAttempts) {
                    Mage::throwException('Unable to create requested Coupon Qty. Please check settings and try again.');
                }
                $code = $this->generateCode();
                $attempt++;
            } while ($this->_exists($code));

            $expirationDate = $this->getToDate();
            if ($expirationDate instanceof Zend_Date) {
                $expirationDate = $expirationDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
            }

            $coupon->setId(null)
                ->setRuleId($this->getRuleId())
                ->setUsageLimit($this->getUsesPerCoupon())
                ->setUsagePerCustomer($this->getUsesPerCustomer())
                ->setExpirationDate($expirationDate)
                ->setCode($code)
                ->save();

            $this->_generatedCodes[] = $code;
            $this->_generatedCount++;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getGeneratedCodes()
    {
        return $this->_generatedCodes;
    }

    /**
     * Retrieve count of generated Coupons
     *
     * @return int
     */
    public function getGeneratedCount()
    {
        return $this->_generatedCount;
    }

    /**
     * Validate input
     *
     * @param array $data
     *
     * @return bool
     */
    public function validateData($data)
    {
        return !empty($data) && !empty($data['qty']) && !empty($data['rule_id'])
            && !empty($data['length'])
            && (int)$data['qty'] > 0
            && (int)$data['rule_id'] > 0
            && (int)$data['length'] > 0;
    }

    protected function _exists($code)
    {
        /** @var $resource Mage_SalesRule_Model_Resource_Coupon */
        $resource = Mage::getResourceModel('salesrule/coupon');
        $read = $resource->getReadConnection();
        $select = $read->select();
        $select->from($resource->getMainTable(), 'code');
        $select->where('code = :code');
        if ($read->fetchOne($select, array('code' => $code)) === false) {
            return false;
        }
        return true;
    }
}
