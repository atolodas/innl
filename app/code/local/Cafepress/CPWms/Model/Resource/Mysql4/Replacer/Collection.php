<?php

class Cafepress_CPWms_Model_Resource_Mysql4_Replacer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Init resource model
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('wmsreplacer/replacer');
    }
}
