<?php
class Neklo_ABTesting_Model_Abtest extends Mage_Core_Model_Abstract {
    protected function _construct() {
        parent::_construct();
        $this->_init('neklo_abtesting/abtest');
    }

    public function loadByCode($code) { 
        $abTest = $this->getCollection()->addAttributToFilter('code', $code)->getFirstItem();

        return $abTest;
    }

    public function getSuccessEvents() { 
        $events = Mage::getModel('neklo_abtesting/abtestevent')->loadByAbTestId($this->getId());
        return $events;
    }
    
    // public function getVariantA() {
        
    //     $variants = Mage::getModel('neklo_abtesting/abtestpresentation')->loadByAbTestId($this->getId());

    //     $value = $variants->getFirstItem()->getAbpresentationId();

    //     return $value;
    // }
    
    // public function getVariantB() {

    //     $variants = Mage::getModel('neklo_abtesting/abtestpresentation')->loadByAbTestId($this->getId());

    //     $value = $variants->getLastItem()->getAbpresentationId();

    //     return $value;
    // }

    // public function getVariantAPresentation() {
        
    //     $variants = Mage::getModel('neklo_abtesting/abtestpresentation')->loadByAbTestId($this->getId());

    //     $value = $variants->getFirstItem()->getAbpresentationId();

    //     return Mage::getModel('neklo_abtesting/abpresentation')->load($value);
    // }
    
    // public function getVariantBPresentation() {

    //     $variants = Mage::getModel('neklo_abtesting/abtestpresentation')->loadByAbTestId($this->getId());

    //     $value = $variants->getLastItem()->getAbpresentationId();

    //     return Mage::getModel('neklo_abtesting/abpresentation')->load($value);
    // }

    public function getPresentation($presentationLinkId) { 
        $presentationLink = Mage::getModel('neklo_abtesting/abtestpresentation')->load($presentationLinkId);

        $presentationId = $presentationLink->getAbpresentationId();

        return Mage::getModel('neklo_abtesting/abpresentation')->load($presentationId);
    }

    public function isActive() { 
        if(!$this->getStatus()) return false; // disabled

        if($this->getStartAt() && $this->getStartAt() > Mage::getModel('core/date')->date('Y-m-d H:i:s')) return false;

        if($this->getEndAt() && $this->getEndAt() < Mage::getModel('core/date')->date('Y-m-d H:i:s')) return false;

        return true;
    }

    public function getValidEvents() { 
        $eventsIds  =   Mage::getModel('neklo_abtesting/abtestevent')->loadByAbTestId($this->getId())->getColumnValues('event_id');
        $events     =   Mage::getModel('neklo_abtesting/abevent')->getCollection()
                        ->addFieldToFilter('event_id', array('in' => $eventsIds));
        return $events;
    }
    
}