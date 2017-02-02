<?php

class Savvy_AccessEPay_Model_Transaction extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('accessepay/transaction');
    }

    public function getList($params)
    {
        $helper = Mage::helper('accessepay');

        $data = array(
            'merchantID' => $params['merchant_id'],
            'orderID' => $params['order_id'],
            'currencyCode' => $params['currency_code'],
            'amount' => $params['amount']
        );

        $result = array(
            'status' => '01',
            'ResponseDescription' => $helper->__('An error has occurred. Please, contact the store administrator.')
        );

        $soapResult = $helper->soapClient('get_status', $data);

        $debugLog = array(
            array('message' => $helper->hideLogPrivacies($data)),
            array('message' => $soapResult)
        );

        $helper->debug($debugLog, 'accessepay_transaction_status');

        if ($soapResult != '[soapfault]') {
            $response = $helper->parseXmlResponse($soapResult, 'get_status');

            if (strpos($response['StatusCode'], '00')>-1) {
                $this->_saveTransactionData($response['get_status']);

                $list = Mage::getModel('accessepay/system_config_source_transaction')
                    ->toOptionArray();

                $result = array(
                    'status' => '00',
                    'ResponseDescription' => $helper->__('Service list has been retrieved successfully!'),
                    'list' => $list
                );
            } else {
                $result['ResponseDescription'] = $response['ResponseDescription'];
            }
        }

        return $result;
    }

    public function getResponseText($value)
    {
        $text       = '';
        $collection = Mage::getModel('accessepay/transaction')->getCollection();

        foreach ($collection as $item) {
            if ($value == $item->getTransactionId()) {

                $text = $item->getResponseCode();
                break;
            }
        }

        return $text;
    }

    private function _saveTransactionData($data)
    {
        Mage::getResourceModel('accessepay/transaction')
            ->truncate();

        foreach ($data as $i) {
            Mage::getModel('accessepay/transaction')
                ->setService($i)
                ->save();
        }
    }
}
