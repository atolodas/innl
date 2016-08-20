<?php

class Oggetto_Cacherecords_Model_Mysql4_Cacherecords extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the cacherecords_id refers to the key field in your database table.
        $this->_init('cacherecords/cacherecords', 'cacherecords_id');
    }
}