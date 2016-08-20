<?php

class DP_Popup_Model_Popup extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('popup/popup');
    }
}