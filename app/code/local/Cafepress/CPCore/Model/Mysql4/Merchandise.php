<?php

class Cafepress_CPCore_Model_Mysql4_Merchandise extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('merchandise/merchandise', 'id');
    }
}