<?php
class Neklo_ABTesting_Helper_Fpc extends Neklo_ABTesting_Helper_Data
{ 
    const FPC_ENABLED = 'FPC_AB_ENABLED';
    const FPC_SESSION_COOKIE_LIFETIME = 'FPC_AB_FPC_SESSION_COOKIE_LIFETIME';
    const FPC_AB_TESTS = 'FPC_AB_TESTS';
    
    protected $_cookieFpc = null;
    
    public function __construct() {
        $this->_cookieFpc = new Neklo_ABTesting_Model_Fpc();
    }
    
    public function init() {
        if (is_null($this->_abtests)) {
            $this->_abtests = $this->getAbtests();
            foreach ($this->_abtests as $abtest) {
                $abtest = Mage::getModel('neklo_abtesting/abtest')->load($abtest->getId());
                if (!$abtest->isActive()) {
                    $this->_cookieFpc->clear($abtest);
                }
            }
        }
    }
    
    public function getAbtests() {
        $cacheInstance = Enterprise_PageCache_Model_Cache::getCacheInstance();
        $abTests = Mage::getModel('neklo_abtesting/abtest')->getCollection();
        $activeIds = array();
        foreach ($abTests as $abTest) {
            $abTest = Mage::getModel('neklo_abtesting/abtest')->load($abTest->getId());
            if ($abTest->isActive()) {
                $activeIds[] = $abTest->getId();
            }
        }

        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
        
        if ($connection->isTableExists($tablePrefix . 'neklo_abtesting_abtest')) {
            $select = $connection->select()->from($tablePrefix . 'neklo_abtesting_abtest as abt');
            $select->join($tablePrefix . 'neklo_abtesting_abtest_abpresentation as abtp1', 
                'abt.abtest_id = abtp1.abtest_id and abtp1.id = (select min(id) from ' . $tablePrefix . 'neklo_abtesting_abtest_abpresentation as tmp1 where tmp1.abtest_id = abt.abtest_id)',
                array('variant_a' => 'abpresentation_id')
                )
                ->join($tablePrefix . 'neklo_abtesting_abtest_abpresentation as abtp2', 
                'abt.abtest_id = abtp2.abtest_id and abtp2.id = (select max(id) from ' . $tablePrefix . 'neklo_abtesting_abtest_abpresentation as tmp2 where tmp2.abtest_id = abt.abtest_id)',
                array('variant_b' => 'abpresentation_id')
                );
            if(!empty($activeIds)) { 
                $select->where('abt.abtest_id IN (' . (implode(',', $activeIds)) . ')'); 
            } else { 
                $select->where('abt.abtest_id IN (0)'); 
            }
            
            $data = $connection->fetchAll($select);
            $cacheInstance->save(serialize($data), self::FPC_AB_TESTS);
        }
        
        $abtests = array();
        if ($data && is_array($data)) {
            foreach($data as $item) {
                $abtests[] = new Varien_Object($item);
            }
        }
        return $abtests;
    }
    
    
    public function setGaEvents() {
        return $this->_cookieFpc->setAbtestGaEventsToCookie();
    }
    
    public function isEnabled() {
        return $this->getStoreConfigFromFpcCache('neklo_abtesting/general/enabled', self::FPC_ENABLED);
    }
    
    public function getSessionCookieLifetime() {
        return intval($this->getStoreConfigFromFpcCache('neklo_abtesting/general/session_cookie_lifetime', self::FPC_SESSION_COOKIE_LIFETIME));
    }
    
    public function getCustomerGroup($abtest) {
        return $this->_cookieFpc->getCustomerGroup($abtest, $this->getSessionCookieLifetime());
    }
    
    public function getStoreConfigFromFpcCache($path, $cacheKey) {
        $cacheInstance = Enterprise_PageCache_Model_Cache::getCacheInstance();
        $value = $cacheInstance->load($cacheKey);
        if ($value===false) {
            $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
            $select = $connection->select()
                    ->from($tablePrefix . 'core_config_data', array('value'))
                    ->where('path = ?', $path)
                    ->where('scope_id = ?', 0);
            $value = $connection->fetchOne($select);
            $cacheInstance->save($value, $cacheKey);
        }
        return $value;
    }
    
    public function applyCookies() {
        return $this->_cookieFpc->applyCookies();
    }

}