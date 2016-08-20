<?php
class Neklo_ABTesting_Model_Abevent extends Mage_Core_Model_Abstract {
    
    protected function _construct() {
        parent::_construct();
        $this->_init('neklo_abtesting/abevent');
    }

}