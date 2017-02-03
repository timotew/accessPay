<?php

class Savvy_Accesspay_Model_Resource_Transaction extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('accesspay/transaction', 'transaction_id');
    }

    public function truncate()
    {
        $this->_getWriteAdapter()->query('TRUNCATE TABLE ' . $this->getMainTable());

        Mage::getConfig()->deleteConfig(
            Savvy_Accesspay_Helper_Data::PAYMENT_ACCESSPAY_SYSTEM_PATH . 'transaction'
        );

        return $this;
    }
}
