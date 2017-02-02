<?php

class Savvy_AccessEPay_Model_Resource_Transaction_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('accessepay/transaction');
    }
}
