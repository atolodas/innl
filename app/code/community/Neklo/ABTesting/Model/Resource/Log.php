<?php
class Neklo_ABTesting_Model_Resource_Log extends Mage_Core_Model_Mysql4_Abstract {
    protected function _construct() {        
        $this->_init('neklo_abtesting/log', 'log_id');
    }        
}