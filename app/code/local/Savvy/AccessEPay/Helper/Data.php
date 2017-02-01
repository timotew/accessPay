<?php
class Savvy_AccessEPay_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Log file name
     *
     * @var string
     */
    protected $_logFile = 'savvy_accessepay.log';

    /**
     * Email fail message
     *
     * @var string
     */
    protected $_emailFailMessage = 'Could not send email';

    protected $_getwayURL = "https://cipg.accessbankplc.com/MerchantServices/MakePayment.aspx";

    const ACCESSEPAY_SETTINGS_SYSTEM_PATH        = 'payment/accessepay/';

    function getPaymentGatewayUrl()
    {
        return $this->_getwayURL;
    }
    public function getMerchantDetails()
    {



        $accessAccount = array(
            'merchant_id'          => trim(Mage::helper('core')->decrypt($this->getConfigData('merchant_id', 'payment_accessepay'))),
            'currency_code'          => strtoupper(trim($this->getConfigData('currency_code', 'payment_accessepay'))),
            'merchant_email'          => trim(Mage::helper('core')->decrypt($this->getConfigData('merchant_email', 'payment_accessepay')))
        );


        return $accessAccount;
    }

    public function soapClient($method, $callParams, $scOptions = array())
    {
        $wsdl = $this->_getWsdl();
        $result = null;

        if (!isset($scOptions['soap_version'])) {
            $scOptions['soap_version'] = SOAP_1_2;
        }

        if (!isset($scOptions['trace'])) {
            $scOptions['trace'] = 1;
        }

        if (!isset($scOptions['exceptions'])) {
            $scOptions['exceptions'] = 0;
        }

        try {
            $soapClient = new SoapClient($wsdl, $scOptions);

            if ($actionHeader = $this->_getSoapHeader($method)) {
                $soapClient->__setSoapHeaders($actionHeader);
            }

            $result = $this->_soapClientCall($method, $soapClient, $callParams);

            if ($result instanceof SoapFault) {
                $this->log($result->faultstring);
                $result = '[soapfault]';
            }
        } catch (SoapFault $sf) {
            $this->log($sf->faultstring);
            $result = '[soapfault]';
        }

        return $result;
    }
    private function _soapClientCall($method, $soapClient, $callParams)
    {
        $result = null;

        switch ($method) {
            case 'get_status':
                $result = $soapClient->GetTransactionStatus($callParams)->GetTransactionStatusResult;
                break;
            case 'get_details':
                $result = $soapClient->GetTransactionDetails($callParams)->GetTransactionDetailsResult;
                break;
            case 'settlement_detail':
                $result = $soapClient->GetTransactionSettlementDetails($callParams)->GetTransactionSettlementDetailsResult;
                break;
            case 'confirmation_status':
                $result = $soapClient->DoWebServiceConfirmationStatus($callParams)->DoWebServiceConfirmationStatusResult;
                break;
            case 'get_status_secure':
                $result = $soapClient->GetStatusSecure($callParams)->GetStatusSecureResult;
                break;

        }

        return $result;
    }
    private function _getSoapHeader($method)
    {
        $action = '';
        $header = null;

        switch ($method) {
            case 'get_status':
                $action = 'http://tempuri.org/ITransactionStatusCheck/GetTransactionStatus';
                break;

            case 'get_details':
                $action = 'http://tempuri.org/ITransactionStatusCheck/GetTransactionDetails';
                break;
            case 'settlement_detail':
                $action = 'http://tempuri.org/ITransactionStatusCheck/GetTransactionSettlementDetails';
                break;
            case 'confirmation_status':
                $action = 'http://tempuri.org/ITransactionStatusCheck/DoWebServiceConfirmationStatus';
                break;
            case 'get_status_secure':
                $action = 'http://tempuri.org/ITransactionStatusCheck/GetStatusSecure';
                break;

            default:
                return $header;
        }
        $header = new SoapHeader(
            'http://www.w3.org/2005/08/addressing',
            'Action',
            $action,
            true
        );

        return $header;
    }
    private function _getWsdl()
    {
        $wsdl = 'https://cipg.accessbankplc.com/WebService/TransactionStatusCheck.svc?wsdl';

        return $wsdl;
    }


    public function parseXmlResponse($xml, $method)
    {
        $result = array();

        $xmlObj = new Varien_Simplexml_Config($xml);

        if (empty($xmlObj)) {
            return $result;
        }

        $result['status'] = $xmlObj->getNode('StatusCode')->asArray();

        if (strpos($result['status_code'], '00')>-1) {
            switch ($method) {
                case 'get_status':
                    $result['get_status'] = $xmlObj->getNode()->asArray();
                    break;

            }
        }

        return $result;
    }

    public function getConfigData($var, $type, $store = null)
    {
        $path = '';

        switch ($type) {

            case 'payment_acccessepay':
                $path = self::ACCESSEPAY_SETTINGS_SYSTEM_PATH;
                break;
        }

        return Mage::getStoreConfig($path . $var, $store);
    }
    public function debug($params, $file = '')
    {
        if ($this->getConfigData('debug', 'payment_accessepay')) {
            $this->log($params, '', $file);
        }
    }
    /**
     * Send email
     *
     * @param string $to
     * @param int|string $templateId
     * @param array $params
     * @param string|array $sender
     * @param string $name
     * @param int|null $storeId
     * @return bool
     */
    public function sendEmail($to, $templateId, $params = array(), $sender = 'general', $name = null, $storeId = null)
    {
        $mailTemplate = Mage::getModel('core/email_template');
        $translate = Mage::getSingleton('core/translate');
        $result = true;

        if (empty($storeView)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        if (isset($params['subject'])) {
            $mailTemplate->setTemplateSubject($params['subject']);
        }

        $mailTemplate->sendTransactional(
            $templateId,
            $sender,
            $to,
            $name,
            $params,
            $storeId
        );

        if (!$mailTemplate->getSentSuccess()) {
            $this->log($this->_emailFailMessage);
            $result = false;
        }

        $translate->setTranslateInline(true);

        return $result;
    }
    /**
     * Set user messages
     *
     * @param string|array $message
     * @param string $type
     * @param string $sessionPath
     * @return bool
     */
    public function userMessage($message, $type, $sessionPath = 'core/session')
    {
        try {
            $session = Mage::getSingleton($sessionPath);

            if (is_array($message)) {
                if (!isset($message['text'])) {
                    return false;
                }

                if (isset($message['translate'])) {
                    $message = $this->__($message['text']);
                }
            }

            switch ($type) {
                case 'error':
                    $session->addError($message);
                    break;
                case 'success':
                    $session->addSuccess($message);
                    break;
                case 'notice':
                    $session->addNotice($message);
                    break;
            }
        } catch (Exception $e) {
            $this->log($e, 'exception');
            return false;
        }

        return true;
    }

    /**
     * Generate log
     *
     * @param string|array $logs
     * @param string $type
     * @param string $file
     * @param bool $source
     * @return bool
     */
    public function log($logs, $type = 'system', $file = '', $source = false)
    {
        if (!Mage::getStoreConfig('dev/log/active')
            || empty($logs)) {
            return;
        }

        if (empty($file)) {
            $file = $this->_logFile;
        }

        switch ($type) {
            case 'exception':
                if (!is_array($logs)) {
                    $logs = array($logs);
                }

                foreach ($logs as $log) {
                    if (!$log instanceof Exception) {
                        continue;
                    }

                    Mage::logException($log);
                }
                break;
            default:
                $this->_systemLog($logs, $file, $source);
        }
    }

    public function hideLogPrivacies($data)
    {
        $mask = '******';

        $data['merchand_id'] = $mask;
        $data['merchant_email'] = $mask;

        return $data;
    }

    /**
     * Generate system log
     *
     * @param string|array $logs
     * @param string $file
     * @param bool $source
     * @return null
     */
    private function _systemLog($logs, $file, $source = false)
    {
        if (is_string($logs)) {
            $logs = array(array('message' => $logs));
        }

        if (is_array($logs) && isset($logs['message'])) {
            $logs = array($logs);
        }

        foreach ($logs as $log) {
            if (!isset($log['message'])) {
                continue;
            }

            $message = $log['message'];

            if ($source) {
                if (isset($log['source']) && !$log['source']) {
                    // Do nothing
                } else {
                    $message .= sprintf(
                        ' [File: %s] -> [Line: %s]',
                        __FILE__, __LINE__
                    );
                }
            }

            $level = isset($log['level'])
                ? $log['level'] : null;

            if (!empty($log['file'])) {
                $file = $log['file'];
            }

            if (false === strpos($file, '.log')) {
                $file .= '.log';
            }

            $forceLog = isset($log['forceLog'])
                ? $log['forceLog'] : false;

            Mage::log($message, $level, $file, $forceLog);
        }
    }

    /**
     * Check whether Advanced Ifconfig module is enabled or not
     *
     * @return bool
     */
    public function isAdvIfconfigEnabled()
    {
        return Mage::helper('core')->isModuleEnabled('Savvy_AdvIfconfig');
    }


}