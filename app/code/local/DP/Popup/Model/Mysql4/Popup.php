<?php

class DP_Popup_Model_Mysql4_Popup extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the popup_id refers to the key field in your database table.
        $this->_init('popup/popup', 'popup_id');
    }
}