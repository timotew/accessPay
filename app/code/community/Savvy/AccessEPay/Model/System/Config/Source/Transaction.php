<?php

class Savvy_AccessEPay_Model_System_Config_Source_Transaction
{
    public function toOptionArray()
    {
        $options    = array();
        $collection = Mage::getModel('accessepay/transaction')->getCollection();

        foreach ($collection as $item) {
            $options[] = array(
                'label' => $item['status'],
                'value' => $item['transaction_id']
            );
        }

        if (empty($options)) {
            $options[] = array(
                'label' => Mage::helper('accessepay')->__('- No Transaction Available -'),
                'value' => ''
            );
        } else {
            array_unshift(
                $options,
                array(
                    'label' => Mage::helper('accessepay')->__('--Please Select--'),
                    'value' => ''
                )
            );
        }

        return $options;
    }
}
