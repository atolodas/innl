<?php
class Neklo_ABTesting_Model_System_Config_Source_Mode
{
    public function toOptionArray() {
        return array(
            array('value' => Neklo_ABTesting_Helper_Data::AB_TEST_MODE_DISABLED, 'label'=>Mage::helper('adminhtml')->__('Disabled')),
            array('value' => Neklo_ABTesting_Helper_Data::AB_TEST_MODE_A, 'label'=>Mage::helper('adminhtml')->__('Only A')),
            array('value' => Neklo_ABTesting_Helper_Data::AB_TEST_MODE_B, 'label'=>Mage::helper('adminhtml')->__('Only B')),
            array('value' => Neklo_ABTesting_Helper_Data::AB_TEST_MODE_AB, 'label'=>Mage::helper('adminhtml')->__('A/B'))
        );
    }
    
    
     public function toArray() {
        return array(
            Neklo_ABTesting_Helper_Data::AB_TEST_MODE_DISABLED => Mage::helper('adminhtml')->__('Disabled'),
            Neklo_ABTesting_Helper_Data::AB_TEST_MODE_A => Mage::helper('adminhtml')->__('Only A'),
            Neklo_ABTesting_Helper_Data::AB_TEST_MODE_B => Mage::helper('adminhtml')->__('Only B'),
            Neklo_ABTesting_Helper_Data::AB_TEST_MODE_AB => Mage::helper('adminhtml')->__('A/B')
        );
    }
}