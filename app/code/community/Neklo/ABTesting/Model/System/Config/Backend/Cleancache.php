<?php
class Neklo_ABTesting_Model_System_Config_Backend_Cleancache extends Mage_Core_Model_Config_Data
{
    protected function _afterSave() {        
        if (class_exists('Enterprise_PageCache_Model_Cache')) {
            $cacheInstance = Enterprise_PageCache_Model_Cache::getCacheInstance();
            $cacheInstance->flush();
        }
    }
}
