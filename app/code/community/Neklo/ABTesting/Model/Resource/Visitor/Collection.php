<?php
class Neklo_ABTesting_Model_Resource_Visitor_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected function _construct() {        
        $this->_init('neklo_abtesting/visitor');
    }
    
}