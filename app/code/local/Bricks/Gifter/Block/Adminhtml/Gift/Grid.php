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

class Bricks_Gifter_Block_Adminhtml_Gift_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid
     * Set sort settings
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('bricks_gifter_gift_grid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Add websites to sales rules collection
     * Set collection
     *
     * @return Mage_Adminhtml_Block_Promo_Quote_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection bricks_Gifter_Resource_Gift_Collection */
        $collection = Mage::getModel('Bricks_Gifter/Gift')->getCollection();

        $collection->join(
            array('order' => 'sales/order'),
            'main_table.order_id = order.entity_id',
            array(
                'increment_id',
                'customer_firstname',
                'customer_lastname'
            )
        );

        $collection->addExpressionFieldToSelect('sent', 'IF(ISNULL(sent_at), "no", "yes")', array());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Add grid columns
     *
     * @return Mage_Adminhtml_Block_Promo_Quote_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'increment_id',
            array(
                'header' => $this->__('Order #'),
                'align' => 'left',
                'index' => 'increment_id',
                'filter_index' => 'order.increment_id'
            )
        );

        $this->addColumn(
            'customer_firstname',
            array(
                'header' => $this->__('Customer Firstname'),
                'align' => 'left',
                'index' => 'customer_firstname',
                'filter_index' => 'order.customer_firstname'
            )
        );

        $this->addColumn(
            'customer_lastname',
            array(
                'header' => $this->__('Customer Lastname'),
                'align' => 'left',
                'index' => 'customer_lastname',
                'filter_index' => 'order.customer_lastname'
            )
        );

        $this->addColumn(
            'recipient_name',
            array(
                'header' => $this->__('Recipient Name'),
                'align' => 'left',
                'index' => 'recipient_name',
            )
        );

        $this->addColumn(
            'recipient_email',
            array(
                'header' => $this->__('Recipient Email'),
                'align' => 'left',
                'index' => 'recipient_email',
            )
        );

        $this->addColumn(
            'coupon',
            array(
                'header' => $this->__('Coupon Code'),
                'align' => 'left',
                'width' => '150px',
                'index' => 'coupon',
            )
        );

        $this->addColumn(
            'sent',
            array(
                'header' => $this->__('Sent'),
                'align' => 'left',
                'width' => '30px',
                'type' => 'options',
                'index' => 'sent',
                'filter_condition_callback' => array($this, 'filterSent'),
                'options' => array(
                    'yes' => $this->__('Yes'),
                    'no' => $this->__('No'),
                ),
            )
        );

        $this->addColumn(
            'sent_at',
            array(
                'header' => $this->__('Date Sent'),
                'align' => 'left',
                'width' => '120px',
                'type' => 'date',
                'index' => 'sent_at',
            )
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid');
    }

    public function getAbsoluteGridUrl($params = array())
    {
        return $this->getUrl('*/*/grid', $params);
    }

    public function filterSent(Bricks_Gifter_Resource_Gift_Collection $collection, Mage_Adminhtml_Block_Widget_Grid_Column $column)
    {
        $filter = $column->getFilter()->getValue();
        if ($filter === 'yes') {
            $collection->addFieldToFilter('sent_at', array('notnull' => true));
        } elseif ($filter === 'no') {
            $collection->addFieldToFilter('sent_at', array('null' => true));
        }
    }
}
