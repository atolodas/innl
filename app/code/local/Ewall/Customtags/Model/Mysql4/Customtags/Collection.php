<?php

class Ewall_Customtags_Model_Mysql4_Customtags_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('customtags/customtags');
    }
}
