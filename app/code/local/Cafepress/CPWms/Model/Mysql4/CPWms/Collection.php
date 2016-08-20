<?php

class Cafepress_CPWms_Model_Mysql4_CPWms_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cpwms/cpwms');
    }
}