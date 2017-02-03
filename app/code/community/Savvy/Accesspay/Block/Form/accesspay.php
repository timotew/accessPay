<?php
class Savvy_Accesspay_Block_Form_Accesspay extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('accesspay/form/accesspay.phtml');
    }
}