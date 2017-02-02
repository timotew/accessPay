<?php

$installer = $this;

$installer->startSetup();

/**
 * Create table 'sg_accessepay_shipping_service'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('accessepay/transaction'))
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Transaction ID')
    ->addColumn('reference', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Transaction Reference')
    ->setComment('Transactions');

$installer->getConnection()->createTable($table);

$installer->endSetup();