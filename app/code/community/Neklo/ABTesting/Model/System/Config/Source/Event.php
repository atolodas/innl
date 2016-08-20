<?php
class Neklo_ABTesting_Model_System_Config_Source_Event
{
    public function toOptionArray() {
        
        $presentations = Mage::getModel('neklo_abtesting/abevent')->getCollection()
        				->addFieldToFilter('event_id', array('gt' => 1));

        $options = array();
        foreach ($presentations as $presentation) {
            $options[] = array('label' => $presentation->getName(), 'value' => $presentation->getId());
        }
        return $options;
    }
}