<?php

class Cafepress_CPCore_Model_Mysql4_CPCore extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the wms_id refers to the key field in your database table.
        $this->_init('cpcore/cpcore', 'wms_id');
    }
}