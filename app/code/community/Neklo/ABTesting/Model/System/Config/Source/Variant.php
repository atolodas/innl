<?php
class Neklo_ABTesting_Model_System_Config_Source_Variant
{
    public function toOptionArray() {
        
        $presentations = Mage::getModel('neklo_abtesting/abpresentation')->getCollection()
                        ->addFieldToFilter('status', 1);

        $options = array(array('label' => 'Please select', 'value' => ''));
        foreach ($presentations as $presentation) {
            $options[] = array('label' => $presentation->getName(), 'value' => $presentation->getId());
        }
        return $options;
    }
}