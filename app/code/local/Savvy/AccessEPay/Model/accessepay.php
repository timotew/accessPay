<?php
class Savvy_AccessEPay_Model_Paymentmethod extends Mage_Payment_Model_Method_Abstract {
    protected $_code  = 'accessepay';
    protected $_formBlockType = 'accessepay/form_accessepay';
    protected $_infoBlockType = 'accessepay/info_accessepay';

    public function assignData($data)
    {
        $info = $this->getInfoInstance();


        if ($data->getCardType())
        {
            $info->setCardType($data->getCardType());
        }

        return $this;
    }

    public function validate()
    {
        parent::validate();
        $info = $this->getInfoInstance();

        if (!$info->getCardType())
        {
            $errorCode = 'invalid_data';
            $errorMsg = $this->_getHelper()->__("Please Choose your Card Type.\n");
        }



        if ($errorMsg)
        {
            Mage::throwException($errorMsg);
        }

        return $this;
    }

    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('accessepay/payment/redirect', array('_secure' => false));
    }
}