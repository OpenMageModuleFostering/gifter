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

class Bricks_Gifter_Model_Observer
{
    public function activateAutoCoupons(Varien_Event_Observer $observer)
    {
        /** @var $order Varien_Object */
        $transport = $observer->getTransport();
        if (!$transport instanceof Varien_Object) {
            return;
        }

        $transport->setIsCouponTypeAutoVisible(true);
    }

    public function processGiftRecipient(Varien_Event_Observer $observer)
    {
        /** @var $helper Bricks_Gifter_Helper_Data */
        $helper = Mage::helper('Bricks_Gifter/Data');

        if (!$helper->isStoreEnabled()) {
            return;
        }

        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getOrder();
        if (!$order instanceof Mage_Sales_Model_Order) {
            return;
        }

        $data = Mage::app()->getRequest()->getPost('bricks-gifter', array());
        if (!empty($data['recipient_name']) && !empty($data['recipient_email'])) {
            $gift = Mage::getModel('Bricks_Gifter/Gift')->load($order->getId(), 'order_id');
            if ($gift && !$gift->getId()) {
                $gift->setOrderId($order->getId())
                    ->setRecipientName($data['recipient_name'])
                    ->setRecipientEmail($data['recipient_email'])
                    ->save();
            }
        }
    }
}
