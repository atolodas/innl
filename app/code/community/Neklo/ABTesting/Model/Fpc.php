<?php
class Neklo_ABTesting_Model_Fpc extends Neklo_ABTesting_Model_Cookie
{   
    protected $_cookieChanges  = array();
    
    public function setCookie($cookieGroup, $value, $cookieLifetime = 0) {
        if ($cookieLifetime) $cookieLifetime = time() + $cookieLifetime;
        $this->_cookieChanges[$cookieGroup] = array('set', $cookieGroup, $value, $cookieLifetime);
    }
    
    public function getCookie($cookieGroup) {
        $value = '';
        
        if (isset($this->_cookieChanges[$cookieGroup])) {
            $data = $this->_cookieChanges[$cookieGroup];
            if ($data[0]=='set') return $data[2];
        }
        
        if (isset($_COOKIE[$cookieGroup])) {
            $value = $_COOKIE[$cookieGroup];
        }
        return $value;
    }
    
    public function deleteCookie($cookieGroup) {
        $this->_cookieChanges[$cookieGroup] = array('delete', $cookieGroup);
    }
    
    public function applyCookies() {
        foreach($this->_cookieChanges as $cookie) {
            if ($cookie[0]=='set') {
                setcookie($cookie[1], $cookie[2], $cookie[3], '/');
                if(substr_count($cookie[1], 'GA')==0) { 
                    $visitorId = Mage::getModel('neklo_abtesting/observer')->getVisitorIdCookie();
                    Mage::getModel('neklo_abtesting/log')->logNewVisitor($visitorId, $cookie[1], $cookie[2]);  
                }
            } else {
                setcookie($cookie[1], '', 100, '/');
            }
        }
        
        $this->_cookieChanges = array();
    }
    
}