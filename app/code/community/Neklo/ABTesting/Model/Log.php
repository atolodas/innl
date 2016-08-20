<?php

class Neklo_ABTesting_Model_Log extends Mage_Core_Model_Abstract {

    protected function _construct() {
        parent::_construct();
        $this->_init('neklo_abtesting/log');
    }

	public function getMaxId() { 
        
        $tableName = $this->getLogTableName();
        
        $maxId = Mage::getSingleton('core/resource')->getConnection('core/read')
            ->query("SELECT MAX(visitor_id) as max FROM {$tableName}")
            ->fetch();

        return $maxId['max'];
    }

    public function logNewVisitor($visitorId, $cookieName, $cookieValue) {

        if($visitorId && $cookieValue) { 
            $cookieModel = Mage::getSingleton('core/cookie');
            $AbTestCookieModel = Mage::getModel('neklo_abtesting/cookie');
            
            list($abTestPresentationLinkId, $abTestCode, $abPresentationCode) = explode('_', $cookieValue);

            $tableName = $this->getLogTableName();
    		$visitorInfo = Mage::getModel('neklo_abtesting/visitor')->getNewVisitorInfo();
            Mage::log('Initing abtest for '.$visitorId.' '.$visitorInfo, null, 'visitors.log');
            
            // Logging Abtest "Init" Event
    		Mage::getSingleton('core/resource')->getConnection('core/write')
                ->query("INSERT INTO {$tableName} VALUES (log_id, {$visitorId}, {$abTestPresentationLinkId}, 1, NOW(), '{$visitorInfo}')"); 
        }
    }

    public function logSuccessEvent($visitorId, $eventId, $abTestPresentationLinkId, $eventCode = null) { 
        $tableName = $this->getLogTableName();
        
        if($visitorId && $abTestPresentationLinkId) { 
            $visitorInfo = Mage::getModel('neklo_abtesting/visitor')->getNewVisitorInfo();
            // Logging Abtest Success event Event
            Mage::getSingleton('core/resource')->getConnection('core/write')
                ->query("INSERT INTO {$tableName} VALUES (log_id, {$visitorId}, {$abTestPresentationLinkId}, $eventId, NOW(), '{$visitorInfo}')"); 
        }
    }

   

    public function getLogTableName() { 
    	return Mage::getSingleton('core/resource')->getTableName('neklo_abtesting/log');
    }

}