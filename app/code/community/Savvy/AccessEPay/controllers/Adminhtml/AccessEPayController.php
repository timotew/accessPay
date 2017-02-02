<?php

class Savvy_AccessEPay_Adminhtml_AccessEPayController extends Mage_Adminhtml_Controller_Action
{
   public function verifyAccountAction()
    {
        $params =  $this->getRequest()->getPost();
        $helper =  Mage::helper('accessepay');
        $response = Mage::app()->getResponse()
            ->setHeader('content-type', 'application/json; charset=utf-8');

        $result = array(
            'status' => 0,
            'ResponseDescription' => $this->__('An error has occurred. Please, contact the store administrator.')
        );
        $merchantInfo = $helper->getgetMerchantDetails();



        foreach ($merchantInfo as $key => $val) {
            if (empty($params[$key]) && $params[$key] !== 0) {
                $result['ResponseDescription'] = $this->__(
                    'Merchant ID, Merchant Email and Currency Code are required in order to verify your account'
                );
                $response->setBody(json_encode($result));

                return;
            }
        }

        if (!$params['merchant_id_changed'] && $params['merchant_id'] == '******') {
            $params['merchant_id'] = $helper->getConfigData('merchant_id', 'payment_accessepay');
            $params['merchant_id'] = Mage::helper('core')->decrypt($params['merchant_id']);
        }

        if (!$params['merchant_email'] && $params['password'] == '******') {
            $params['merchant_email'] = $helper->getConfigData('merchant_email', 'payment_accessepay');
            $params['merchant_email'] = Mage::helper('core')->decrypt($params['merchant_email']);
        }

        $result = Mage::getModel('accessepay/status')->verifyAccount($params);

        $response->setBody(json_encode($result));
    }
}
