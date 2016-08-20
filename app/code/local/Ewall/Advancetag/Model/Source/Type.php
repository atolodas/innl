<?php
class Ewall_Advancetag_Model_Source_Type
{
    public function toOptionArray()
    {
            return array(
                array('value' => 'popular', 'label' => Mage::helper('ewall_advancetag')->__('Popular Tags')),
                array('value' => 'all', 'label' =>  Mage::helper('ewall_advancetag')->__('All Tags')),
            );
    }
}
