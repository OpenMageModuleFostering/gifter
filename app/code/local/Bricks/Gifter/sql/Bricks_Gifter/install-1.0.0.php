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

/* @var $setup Mage_Core_Model_Resource_Setup */
$setup = $this;

// Begin setup
$setup->startSetup();

// Create new table object
$table = $setup->getConnection()->newTable($setup->getTable('Bricks_Gifter/Gift'));

// Columns
$table->addColumn(
    'id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    null,
    array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    )
);
$table->addColumn(
    'order_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    null,
    array(
        'unsigned' => true,
        'nullable' => false,
    )
);
$table->addColumn(
    'recipient_name',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    255,
    array()
);
$table->addColumn(
    'recipient_email',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    255,
    array()
);
$table->addColumn(
    'coupon',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    255,
    array(
        'nullable' => true,
    )
);
$table->addColumn(
    'sent_at',
    Varien_Db_Ddl_Table::TYPE_DATETIME,
    null,
    array(
        'nullable' => true,
    )
);

// Indexes
$table->addIndex(
    $setup->getIdxName('Bricks_Gifter/Gift', array('order_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
    array('order_id'),
    array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
);
$table->addIndex(
    $setup->getIdxName('Bricks_Gifter/Gift', array('recipient_email')),
    array('recipient_email')
);
$table->addIndex(
    $setup->getIdxName('Bricks_Gifter/Gift', array('coupon'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
    array('coupon'),
    array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
);

// Constraints
$table->addForeignKey(
    $setup->getFkName('Bricks_Gifter/Gift', 'order_id', 'sales/order', 'entity_id'),
    'order_id',
    $setup->getTable('sales/order'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);
$table->addForeignKey(
    $setup->getFkName('Bricks_Gifter/Gift', 'coupon', 'salesrule/coupon', 'code'),
    'coupon',
    $setup->getTable('salesrule/coupon'),
    'code',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);

// Turn the table object into DDL and execute
$setup->getConnection()->createTable($table);

// Finish up setup
$setup->endSetup();
