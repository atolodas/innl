<?php

class Cafepress_CPWms_Model_Resource_Mysql4_Replacer extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('wmsreplacer/replacer', 'id');
    }
}
