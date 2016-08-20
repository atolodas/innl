<?php

class Cafepress_CPWms_Model_Wms extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cpwms/wms');
    }
}