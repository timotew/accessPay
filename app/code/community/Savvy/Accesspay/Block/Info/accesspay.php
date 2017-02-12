<?php

class Savvy_Accesspay_Block_Info_Accesspay extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('accesspay/info/accesspay.phtml');
    }
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation)
        {
            return $this->_paymentSpecificInformation;
        }

        $data = array();
        if ($this->getInfo()->getCardType())
        {
            $data[Mage::helper('payment')->__('Card Type')] = $this->getInfo()->getCardType();
        }

        $transport = parent::_prepareSpecificInformation($transport);

        return $transport->setData(array_merge($data, $transport->getData()));
    }
}