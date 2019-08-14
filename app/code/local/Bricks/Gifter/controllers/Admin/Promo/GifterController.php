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

class Bricks_Gifter_Admin_Promo_GifterController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }

    public function gridAction()
    {
        if ($this->getRequest()->isAjax()) {
            $this->loadLayout()->renderLayout();
        } else {
            $this->_forward('noroute');
        }
    }

    public function assignCouponsAction()
    {
        Mage::getModel('Bricks_Gifter/Cron')->assignCoupons();
        $this->_getSession()->addSuccess('Manually ran the Assign Coupons cron job.');
        $this->_redirect('*/*');
    }

    public function sendEmailsAction()
    {
        Mage::getModel('Bricks_Gifter/Cron')->sendEmails();
        $this->_getSession()->addSuccess('Manually ran the Send Emails cron job.');
        $this->_redirect('*/*');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/gifter');
    }
}
