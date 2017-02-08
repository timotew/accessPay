<?php
class Savvy_Accesspay_PaymentController extends Mage_Core_Controller_Front_Action
{

    public function gatewayAction()
    {
        if ($this->getRequest()->get("orderId"))
        {
            $arr_querystring = array(
                'flag' => 1,
                'orderId' => $this->getRequest()->get("orderId")
            );

            Mage_Core_Controller_Varien_Action::_redirect('accesspay/payment/response', array('_secure' => false, '_query'=> $arr_querystring));
        }

        /*
         * TODO: Remove this the gateway action is performed by access getway URL
         */
    }
    public function verifyAccountAction()
    {
        $params =  $this->getRequest()->getPost();
        $helper =  Mage::helper('accesspay');
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
            $params['merchant_id'] = $helper->getConfigData('merchant_id', 'payment_accesspay');
            $params['merchant_id'] = Mage::helper('core')->decrypt($params['merchant_id']);
        }

        if (!$params['merchant_email'] && $params['password'] == '******') {
            $params['merchant_email'] = $helper->getConfigData('merchant_email', 'payment_accesspay');
            $params['merchant_email'] = Mage::helper('core')->decrypt($params['merchant_email']);
        }

        $result = Mage::getModel('accesspay/status')->verifyAccount($params);

        $response->setBody(json_encode($result));
    }
    public function redirectAction()
    {
        /*
         * $address = $quote->getShippingAddress();
        $address->getBaseShippingAmount();
        $address->getBaseShippingDiscountAmount();
        $address->getBaseShippingHiddenTaxAmount();
        $address->getBaseShippingInclTax();
        $address->getBaseShippingTaxAmount();

        $address->getShippingAmount();
        $address->getShippingDiscountAmount();
        $address->getShippingHiddenTaxAmount();
        $address->getShippingInclTax();
        $address->getShippingTaxAmount();
         *//* echo 'ID: '.$item->getProductId().'<br />';
    echo 'Name: '.$item->getName().'<br />';
    echo 'Sku: '.$item->getSku().'<br />';
    echo 'Quantity: '.$item->getQty().'<br />';
    echo 'Price: '.$item->getPrice().'<br />';
    echo "<br />";*/
        // without currency sign
        //$_actualPrice = number_format($item->getPrice(), 2);
        // with currency sign
        //$_formattedActualPrice = Mage::helper('core')->currency(number_format($item->getPrice(), 2),true,false);
        // without currency sign
        //$_specialPrice = $item->getFinalPrice();
        // with currency sign
        //$_formattedSpecialPrice = Mage::helper('core')->currency(number_format($item->getFinalPrice(), 2),true,false);
        // Mage::getModel('catalog/category')->load($categoryId)->getUrl(); product cat URL
        //    $product = Mage::getModel('catalog/product')->load($productId);
        //$path = Mage::getUrl().$product->getUrlPath();
        /*
 *$order->getSubtotal()
 *$order->getShippingAmount()
 *$order->getDiscountAmount()
 *$order->getTaxAmount()
 *$order->getGrandTotal()
 * Mage::helper('checkout')->formatPrice($priceInclVat);
 */

        $this->loadLayout();
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','savvy',array('template' => 'accesspay/redirect.phtml'));
        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }

    public function responseAction()
    {
        /*
         * http://merchantURL.com/Success?OrderID=988676&TransactionReference=8765678998989779
         */
        if ($this->getRequest()->get("TransactionReference")&& $this->getRequest()->get("OrderID"))
        {
            $helper =  Mage::helper('accesspay');
            $merchantInfo = $helper->getgetMerchantDetails();
            $merchantId = Mage::helper('core')->decrypt($merchantInfo['merchant_id']);
            $orderId = $this->getRequest()->get("OrderID");
            $transactionRef = $this->getRequest()->get("TransactionReference");
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);


            $param = array(
                "orderID"=>$orderId,
                "merchantID"=>$merchantId,
                "currencyCode"=>$this->getStore()->getCurrentCurrencyCode(),
                "amount"=>$order->getGrandTotal());

            $soapResult = $helper->soapClient('get_status', $param);
            $debugLog = array(
                array('message' => $helper->hideLogPrivacies($param)),
                array('message' => $soapResult)
            );
            $helper->debug($debugLog);

            if ($soapResult != '[soapfault]') {

            //$response = $helper->parseXmlResponse($soapResult, 'get_status');
                if (strpos($soapResult, 'E00&[No Transaction Record]')<0)
                {

                    $order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true, 'Payment Success.');
                    $order->save();
                    Mage::getSingleton('checkout/session')->unsQuoteId();
                    Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=> false));
                } else {
                    $result['ResponseDescription'] = $helper->__('Invalid Account. If the issue persists, please contact the store administrator.');
                }

            }else{

                Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/error', array('_secure'=> false));
            }

        }

    }
}