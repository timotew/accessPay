<?php
class Savvy_AccessEPay_Block_Form_AccessEPay extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('accessepay/form/accessepay.phtml');
    }
}