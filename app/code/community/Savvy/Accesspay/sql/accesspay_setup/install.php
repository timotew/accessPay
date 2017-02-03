<?php

$installer = $this;

$installer->startSetup();

/**
 * Create table 'svv_accesspay_shipping_service'
 * merchant_ID
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('accesspay/transaction'))
    ->addColumn('transaction_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Transaction.php ID')
    ->addColumn('merchant_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Merchant ID')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Order ID')
    ->addColumn('transaction_ref', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Transaction Reference')
    ->addColumn('payment_gate', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Payment Gateway')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Transaction Status')
    ->addColumn('status_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Status Code')
    ->addColumn('response_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Response Code')
    ->addColumn('response_description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Response Description')
    ->addColumn('date_time', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Date Time')
    ->addColumn('amount', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Amount')
    ->addColumn('payment_ref', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Payment Ref')
    ->addColumn('currency_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Currency Code')
    ->addColumn('amount_discrepancy_code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false
    ), 'Amount Discrepancy Codee')
    ->setComment('Transaction');

$installer->getConnection()->createTable($table);

$installer->endSetup();