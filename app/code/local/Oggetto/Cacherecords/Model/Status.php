<?php

class Oggetto_Cacherecords_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('cacherecords')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('cacherecords')->__('Disabled')
        );
    }
}