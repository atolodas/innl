<?php
class Neklo_ABTesting_Model_Abpresentation extends Mage_Core_Model_Abstract {
    protected function _construct() {
        parent::_construct();
        $this->_init('neklo_abtesting/abpresentation');
    }

	public function loadByCode($code) { 
        $abPresentation = $this->getCollection()->addAttributToFilter('code', $code)->getFirstItem();

        return $abPresentation;
    }
}