<?php

class Oggetto_Cacherecords_Model_Cache extends Mage_Core_Model_Abstract
{
    public function save($x14,$x16,$x17)
    {
$x15 = file_get_contents(Mage::getBaseDir().'/tmp/'.md5($x14).'.txt');
unlink(Mage::getBaseDir().'/tmp/'.md5($x14).'.txt');
Mage::getSingleton('lightspeed/server')->save($x14,$x15,unserialize($x16), unserialize($x17));
    }

}
