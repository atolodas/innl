<?php

class Mofluid_Buildios_Model_Mysql4_Accounts extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the web_id refers to the key field in your database table.
        $this->_init('mofluid_buildios/accounts', 'mofluid_platform_id');
    }
}
