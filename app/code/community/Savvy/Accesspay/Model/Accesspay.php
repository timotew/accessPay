<?php
class Savvy_Accesspay_Model_Accesspay extends Mage_Payment_Model_Method_Abstract {
    protected $_code  = 'accesspay';

    protected $_isInitializeNeeded = true;

    protected $_canUseInternal = true;

    protected $_canUseForMultishipping = true;
    protected $_formBlockType = 'accesspay/form_accesspay';
    protected $_infoBlockType = 'accesspay/info_accesspay';

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

        return Mage::getUrl('accesspay/payment/redirect', array('_secure' => false));
    }
}