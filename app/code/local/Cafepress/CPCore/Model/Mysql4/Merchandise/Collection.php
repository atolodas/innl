<?php

class Cafepress_CPCore_Model_Mysql4_Merchandise_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('merchandise/merchandise');
    }
}