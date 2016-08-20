<?php

class Oggetto_Cacherecords_Model_Mysql4_Cacherecords_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cacherecords/cacherecords');
    }
}