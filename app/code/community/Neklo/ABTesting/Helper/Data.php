<?php
class Neklo_ABTesting_Helper_Data extends Mage_Core_Helper_Data
{
    const AB_TESTING_PACKAGE = 'abtesting';
    const AB_TESTING_MOBILE_PACKAGE = 'abtesting_mobile';
    
    const AB_TEST_MODE_DISABLED = 0;
    const AB_TEST_MODE_A = 1;
    const AB_TEST_MODE_B = 2;
    const AB_TEST_MODE_AB = 3;
    
    protected $_abtests = null;
    protected $_variants = null;
    
    public function init() {
        $this->_abtests = Mage::getModel('neklo_abtesting/abtest')->getCollection();
        foreach ($this->_abtests as $key => $abTest) {
            $abTest =  Mage::getModel('neklo_abtesting/abtest')->load($abTest->getId());
            if (!$abTest->isActive()) {
                Mage::getSingleton('neklo_abtesting/cookie')->clear($abTest);
            }
        }
        
        // fix for FPC Cache
        $abHelper = Mage::registry('neklo_abtesting_helper_fpc');
        if ($abHelper) {
            $abHelper->applyCookies();
            $this->_variants = $abHelper->getVariants();
        }
    }
    
    public function isEnabled() {
        return Mage::getStoreConfig('neklo_abtesting/general/enabled');
    }
    
    public function getSessionCookieLifetime() {
        return intval(Mage::getStoreConfig('neklo_abtesting/general/session_cookie_lifetime'));
    }
    
    public function getCustomerGroup($abtest) {
        return Mage::getSingleton('neklo_abtesting/cookie')->getCustomerGroup($abtest, $this->getSessionCookieLifetime());
    }
    
    public function setGaEvents() {
        return Mage::getSingleton('neklo_abtesting/cookie')->setAbtestGaEventsToCookie();
    }
    
    public function getVariantsHash($length = 6) {
        $variants = $this->getVariants();
        if (!$variants) return false;

        $string = array();
        foreach($variants as $variant) {
            $string[] = (is_array($variant) ? implode(',', $variant) : $variant);
        }
        $string = implode('|', $string); 
        return substr(md5($string), 0, $length);
    }

    public function getVariants() {
        if (!$this->isEnabled()) return array();
        
        $this->init();
        

        if (!is_null($this->_variants)) return $this->_variants;
        $variants = array();

        foreach ($this->_abtests as $abtest) {
            $abtest = Mage::getModel('neklo_abtesting/abtest')->load($abtest->getData('abtest_id'));
            if($abtest->isActive()) { 
                $variant = $abtest->getPresentation($this->getCustomerGroup($abtest))->getId();
                if ($variant) $variants[$abtest->getData('abtest_id')] = $variant;
            }
        }
        // set to cookie Ga Events
        $this->setGaEvents();
        
        $this->_variants = $variants;
        
        return $variants;
    }  

    public function getAbtests() { 

        if (!$this->isEnabled()) return array();
       
        $this->init();
        
        if (!is_null($this->_abtests)) return $this->_abtests;

    }  
}