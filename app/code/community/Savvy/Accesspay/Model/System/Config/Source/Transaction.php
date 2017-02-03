<?php

class Savvy_Accesspay_Model_System_Config_Source_Transaction
{
    public function toOptionArray()
    {
        $options    = array();
        $collection = Mage::getModel('accesspay/transaction')->getCollection();

        foreach ($collection as $item) {
            $options[] = array(
                'label' => $item['status'],
                'value' => $item['transaction_id']
            );
        }

        if (empty($options)) {
            $options[] = array(
                'label' => Mage::helper('accesspay')->__('- No Transaction Available -'),
                'value' => ''
            );
        } else {
            array_unshift(
                $options,
                array(
                    'label' => Mage::helper('accesspay')->__('--Please Select--'),
                    'value' => ''
                )
            );
        }

        return $options;
    }
}
