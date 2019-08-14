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

class Bricks_Gifter_Model_Cron
{
    public function assignCoupons()
    {
        /** @var $helper Bricks_Gifter_Helper_Data */
        $helper = Mage::helper('Bricks_Gifter/Data');

        $stores = $helper->getEnabledStores();
        if (empty($stores)) {
            return;
        }

        // Get the resource object so we can use it for a transaction
        $resource = Mage::getModel('Bricks_Gifter/Gift')->getResource();

        /** @var $generator Bricks_Gifter_Model_CouponGenerator */
        $generator = Mage::getSingleton('Bricks_Gifter/CouponGenerator');

        foreach ($stores as $store) {
            $rule = $helper->getCouponRule($store);
            if (!$rule || !$rule->getIsActive()) {
                continue;
            }

            // Start transaction
            $resource->beginTransaction();

            try {
                /** @var $gifts Bricks_Gifter_Resource_Gift_Collection */
                $gifts = Mage::getModel('Bricks_Gifter/Gift')->getCollection()
                    ->join('sales/order', 'main_table.order_id=`sales/order`.entity_id', 'store_id')
                    ->addFieldToFilter('store_id', $store->getId())
                    ->addFieldToFilter('coupon', array('null' => true))
                    ->addFieldToFilter('sent_at', array('null' => true))
                    ->setPageSize($helper->getCouponBatch($store))
                    ->addOrder('id', 'ASC');

                if ($gifts->getSize() > 0) {
                    // Create and save coupon code batch
                    $generationData = array(
                        'rule_id' => $rule->getId(),
                        'qty' => $gifts->getSize(),
                        'length' => $helper->getCouponCodeLength($store),
                        'prefix' => $helper->getCouponCodePrefix($store),
                        'suffix' => $helper->getCouponCodeSuffix($store),
                        'dash' => $helper->getCouponCodeDashInterval($store),
                    );
                    if (!$generator->validateData($generationData)) {
                        Mage::log('Invalid gifter automatic coupon generation data');
                        continue;
                    }
                    $generator->setData($generationData);
                    $generator->generatePool();
                    $generatedCodes = $generator->getGeneratedCodes();
                    if (count($generatedCodes) < $gifts->getSize()) {
                        Mage::log('Generated too few coupon codes!', Zend_Log::WARN);
                    }

                    // Assign coupons to gifts, oldest first, until there are no more coupons
                    foreach ($gifts as $gift) {
                        if (empty($generatedCodes)) {
                            break;
                        }

                        // Assign coupon and save
                        $gift->setCoupon(array_shift($generatedCodes))->save();
                    }
                }

                // Commit Transaction
                $resource->commit();
            }
            catch (Exception $e) {
                // Log the exception
                Mage::logException($e);

                // Revert Transaction
                $resource->rollBack();
            }
        }
    }

    public function sendEmails()
    {
        /** @var $helper Bricks_Gifter_Helper_Data */
        $helper = Mage::helper('Bricks_Gifter/Data');

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        foreach ($helper->getEnabledStores() as $store) {
            $this->_sendEmailsForStore($store);
        }

        $translate->setTranslateInline(true);
    }

    protected function _sendEmailsForStore(Mage_Core_Model_Store $store)
    {
        /** @var $helper Bricks_Gifter_Helper_Data */
        $helper = Mage::helper('Bricks_Gifter/Data');

        /** @var $gifts Bricks_Gifter_Resource_Gift_Collection */
        $gifts = Mage::getModel('Bricks_Gifter/Gift')->getCollection()
            ->join('sales/order', 'main_table.order_id=`sales/order`.entity_id', 'store_id')
            ->join('salesrule/coupon', 'main_table.coupon=`salesrule/coupon`.code', '')
            ->join('salesrule/rule', '`salesrule/coupon`.rule_id=`salesrule/rule`.rule_id', 'is_active')
            ->addFieldToFilter('store_id', $store->getId())
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('coupon', array('notnull' => true))
            ->addFieldToFilter('sent_at', array('null' => true))
            ->setPageSize($helper->getEmailBatch($store))
            ->addOrder('id', 'ASC');

        if ($gifts->getSize() > 0) {
            foreach ($gifts as $gift) {
                try {
                    $this->_sendEmailForGift($store, $gift);
                }
                catch (Exception $e) {
                    // Log the exception
                    Mage::logException($e);
                }
            }
        }
    }

    protected function _sendEmailForGift(Mage_Core_Model_Store $store, Bricks_Gifter_Model_Gift $gift)
    {
        /** @var $helper Bricks_Gifter_Helper_Data */
        $helper = Mage::helper('Bricks_Gifter/Data');

        $order = Mage::getModel('sales/order')->load($gift->getOrderId());
        if (!$order->getId()) {
            Mage::throwException('Invalid order referenced from gift record');
        }

        /** @var $mailTemplate Mage_Core_Model_Email_Template */
        $mailTemplate = Mage::getModel('core/email_template')
            ->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()));

        try {
            $mailTemplate->emulateDesign($store);

            $mailTemplate->sendTransactional(
                $helper->getEmailTemplate($store),
                $helper->getEmailSender($store),
                $gift->getRecipientEmail(),
                $gift->getRecipientName(),
                array('gift' => $gift, 'order' => $order)
            );
            $mailTemplate->revertDesign();
        }
        catch (Exception $e) {
            $mailTemplate->revertDesign();
        }

        if ($mailTemplate->getSentSuccess()) {
            // Commit Transaction
            $gift->setSentAt(new Zend_Date())->save();
        } else {
            Mage::throwException('Failed to send email for gift: ' . $gift->getId());
        }
    }
}
