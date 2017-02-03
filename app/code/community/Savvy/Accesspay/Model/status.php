<?php

class Savvy_Accesspay_Model_Paymentmethod extends Mage_Core_Model_Abstract
{





    public function verifyAccount($params)
    {
        $helper = Mage::helper('accesspay');

        $data = array(
            'merchantID' => $params['merchant_id'],
            'currencyCode' => "NGN",
            'amount' => "4000",
            'orderID' => "3242563"
        );

        $result = array(
            'status' => 0,
            'ResponseDescription' => $helper->__('An error has occurred. Please, contact the store administrator.')
        );

        $soapResult = $helper->soapClient('get_status', $data);

        $debugLog = array(
            array('message' => $helper->hideLogPrivacies($data)),
            array('message' => $soapResult)
        );

        $helper->debug($debugLog);

        if ($soapResult != '[soapfault]') {
            $response = $helper->parseXmlResponse($soapResult, 'get_status');

            if (strpos($response['StatusCode'], '00')>-1) {
                $result = array(
                    'status' => 1,
                    'ResponseDescription' => $response['ResponseDescription'].$helper->__('Valid Account'),
                    'list' => null
                );
            } else {
                $result['ResponseDescription'] = $helper->__('Invalid Account. If the issue persists, please contact the store administrator.');
            }
        }

        return $result;
    }


}
