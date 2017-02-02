<?php

class Savvy_AccessEPay_Model_Resource_Transaction extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('accessepay/transaction', 'transaction_id');
    }

    public function truncate()
    {
        $this->_getWriteAdapter()->query('TRUNCATE TABLE ' . $this->getMainTable());

        Mage::getConfig()->deleteConfig(
            Savvy_AccessEPay_Helper_Data::PAYMENT_ACCESSEPAY_SYSTEM_PATH . 'transaction'
        );

        return $this;
    }
}
