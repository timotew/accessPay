<?php

class Savvy_AccessEPay_Block_Adminhtml_System_Config_Form_VerifyAccountButton extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _construct()
    {
        parent::_construct();
        $template = $this->setTemplate('savvy/accessepay/system/config/verify_account_button.phtml');
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    public function getAjaxVerifyAccountUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/savvy/verifyaccount');
    }

    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(
            array(
                'id'      => 'accessepay_account_checker',
                'label'   => $this->helper('adminhtml')->__('Verify Account'),
                'onclick' => 'javascript:savvy.accessEPay.verifyAccountButton.verifyAccount(); return false;'
            )
        );

        return $button->toHtml();
    }
}
