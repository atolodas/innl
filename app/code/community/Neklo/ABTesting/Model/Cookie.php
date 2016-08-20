<?php
class Neklo_ABTesting_Model_Cookie
{
    const COOKIE_GROUP = 'AB_test';
    const COOKIE_GA_EVENTS = 'AB_test_GA_events';
    const VISITOR_ID = 'visitor_id';
    const VISITS_COUNTER_FLAG = 'visits_counter_flag';

    const COOKIE_GROUP_A = 'a';
    const COOKIE_GROUP_B = 'b';
    
    protected $_currentGroup = array();
    protected $_gaEvents = array();
    
    public function clear($abtest) {
        $this->deleteCookie($this->getAbtestCookieName($abtest));
        $this->deleteCookie($this->getAbtestGaCookieName($abtest));
    }
    
    public function assignCustomerToGroup($abtest) {
        if (!isset($this->_currentGroup[$abtest->getId()])) {
            $presentations = Mage::getModel('neklo_abtesting/abtestpresentation')->loadByAbTestId($abtest->getId());
            $chances = array();
            foreach ($presentations as $presentation) {
                for($i=0; $i<$presentation['chance']; $i++) {
                    $chances[] = $presentation['id'];
                }
            }

            $newGroup = $chances[array_rand($chances)];
            $this->_currentGroup[$abtest->getId()] = $newGroup;
        } else {
            $newGroup = $this->_currentGroup[$abtest->getId()];
        }

        $presentation = $abtest->getPresentation($newGroup);
        $cookieValue = $abtest->getCode() . '_' . $presentation->getCode();

        $abTestPresentationLinkId = Mage::getModel('neklo_abtesting/abtestpresentation')->loadByAbTestPresentationId($abtest->getId(), $presentation->getId())->getId();
        
        $cookieValue = $abTestPresentationLinkId . '_' . $cookieValue;
        
        $this->setCookie($this->getAbtestCookieName($abtest), $cookieValue, intval($abtest->getCookieLifetime()) * 3600);

        Mage::dispatchEvent('AB_test_customer_group_set', array('cookie_name' => $this->getAbtestCookieName($abtest), 'cookie_value' => $cookieValue));
   
        return $newGroup;
    }
    
    public function getAbtestCookieName($abtest) {
        return self::COOKIE_GROUP . '_' . $abtest->getCode();
    }
    
    public function getAbtestGaCookieName($abtest) {
        return $this->getAbtestCookieName($abtest) . '_GA_fired';
    }
    
    public function getCustomerGroup($abtest, $sessionCookieLifetime) {
        $cookieValue = $this->getCookie($this->getAbtestCookieName($abtest));
        $isNew = false;
        $abTestPresentationLinkId = 0;
        if(count(explode('_', $cookieValue)) == 3) {
            list($abTestPresentationLinkId, $abTestCode, $abPresentationCode) = explode('_', $cookieValue);
        } else { 
            $abtestPresentationLink = 0;
            $abTestCode = '';
            $abPresentationCode = '';
        }
        $abtestPresentationLink = $abTestCode . '_' . $abPresentationCode;
        if($abTestPresentationLinkId) {
                $customerGroup = $abTestPresentationLinkId;
        } else { 
                $isNew = true;
                $customerGroup = $this->assignCustomerToGroup($abtest);
        }

        // check and set GA Cookie
        $gaCookieValue = $this->getCookie($this->getAbtestGaCookieName($abtest));
        if (!$gaCookieValue) {
            $this->setCookie($this->getAbtestGaCookieName($abtest), 1, $sessionCookieLifetime);
            
            // add to GA Events
            if ($isNew) {
                $eventName = 'AB_test_distro';
            } else {
                $eventName = 'AB_test_repeat';
            }
            
            $presentation = Mage::getModel('neklo_abtesting/abtest')->getPresentation($customerGroup);
            $eventLabel = $presentation->getCode();
            
            $this->_gaEvents[$abtest->getCode()] = array($eventName, $abtest->getCode(), $eventLabel);
        } elseif ($sessionCookieLifetime>0) {
            $this->setCookie($this->getAbtestGaCookieName($abtest), 1, $sessionCookieLifetime);
        }
        

        return $customerGroup;
    }
    
    public function setAbtestGaEventsToCookie() {
        if ($this->_gaEvents) {
            $gaEvents = array_values($this->_gaEvents);
            $gaEvents = json_encode($gaEvents);
            $this->setCookie(self::COOKIE_GA_EVENTS, $gaEvents, 0);
        } else {
            $this->deleteCookie(self::COOKIE_GA_EVENTS);
        }
    }

    public function setCookie($cookieGroup, $value, $cookieLifetime = 0) {
        Mage::getSingleton('core/cookie')->set($cookieGroup, $value, $cookieLifetime);
    }
    
    public function getCookie($cookieGroup) {
        return Mage::getSingleton('core/cookie')->get($cookieGroup);
    }
    
    public function deleteCookie($cookieGroup) {
        Mage::getSingleton('core/cookie')->delete($cookieGroup);
    }
}