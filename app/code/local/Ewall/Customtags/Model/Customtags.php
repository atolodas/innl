<?php

class Ewall_Customtags_Model_Customtags extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('customtags/customtags');
    }
}
