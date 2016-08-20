<?php
class Neklo_ABTesting_Model_Resource_Abtestevent extends Mage_Core_Model_Mysql4_Abstract {
    protected function _construct() {        
        $this->_init('neklo_abtesting/abtest_event', 'id');
    }        
}