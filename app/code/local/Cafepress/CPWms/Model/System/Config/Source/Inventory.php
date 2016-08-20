<?php

class Cafepress_CPWms_Model_System_Config_Source_Inventory
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
        	array('value' => 0, 'label'=>Mage::helper('cpwms')->__('Hourly')),
            array('value' => 1, 'label'=>Mage::helper('cpwms')->__('Daily')),
        );
    }

}
