<?php

class Neklo_Monitor_Model_Cron
{
    const CRON_LOCK_ID = 'neklo_monitor_cron_lock_id';

    public function run()
    {
        if ($this->_isLocked()) {
            return null;
        }
        $this->_lock();
        $this->_passData();
    }

    protected function _passData()
    {
        $serverData = $this->_collectServerData();
        $storeData = $this->_collectStoreData();
        try {
            $gatewayConfig = Mage::getModel('neklo_monitor/gateway_connector')->sendInfo($serverData, $storeData);
            $this->_getConfig()->updateGatewayConfig($gatewayConfig);
            $this->_getConfig()->updateGatewayLastUpdate();
        } catch (Exception $e) {
            // TODO: add log
        }
    }

    protected function _collectServerData()
    {
        $info = null;
        try {
            Neklo_Monitor_Autoload::register();
            $linfo = Mage::getModel('neklo_monitor/linfo');
            $linfo->scan();
            $info = $linfo->getInfo();
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $info;
    }

    protected function _collectStoreData()
    {
        $info = null;
        try {
            /* @var $minfo Neklo_Monitor_Model_Minfo */
            $minfo = Mage::getModel('neklo_monitor/minfo');
            $minfo->scan();
            $info = $minfo->getInfo();
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $info;
    }

    protected function _isLocked()
    {
        $lockedAt = Mage::app()->loadCache(self::CRON_LOCK_ID);
        if ($lockedAt && (time() - $lockedAt < $this->_getConfig()->getGatewayFrequency() * 60)) {
            return true;
        }
        return false;
    }

    protected function _lock()
    {
        Mage::app()->saveCache(time(), self::CRON_LOCK_ID, array(), $this->_getConfig()->getGatewayFrequency() * 60);
    }

    /**
     * @return Neklo_Monitor_Helper_Config
     */
    protected function _getConfig()
    {
        return Mage::helper('neklo_monitor/config');
    }
}
