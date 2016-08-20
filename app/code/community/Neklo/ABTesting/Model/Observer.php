<?php
class Neklo_ABTesting_Model_Observer {

	public function getVisitorIdCookie($observer = null) {

        if(is_object($observer)) { 
            $customerSession = $observer->getEvent()->getCustomerSession(); 
        } else { 
            $customerSession = Mage::getSingleton('customer/session');
        }

        $cookieModel = $this->getCookieModel();
        $id = 0;
        try { 
             	if(Mage::registry('visitor_id') || $cookieModel->get(Neklo_ABTesting_Model_Cookie::VISITOR_ID)) {
        				if(Mage::registry('visitor_id')) $id = Mage::registry('visitor_id');
        				else $id = $cookieModel->get(Neklo_ABTesting_Model_Cookie::VISITOR_ID);
        		    if(!Mage::registry('logging_in_progress')) { 
                        if(!Mage::registry('first_visit_flag') && !$cookieModel->get(Neklo_ABTesting_Model_Cookie::VISITS_COUNTER_FLAG)) { 
            				Mage::getModel('neklo_abtesting/visitor')->updateVisitor($id);
            				$cookieModel->set(Neklo_ABTesting_Model_Cookie::VISITS_COUNTER_FLAG, 1, 3 * 3600);
            			}
                        if(!Mage::registry('logging_in_progress')) Mage::register('logging_in_progress', true);
                    }
        		} else { 
        		    if(!Mage::registry('logging_in_progress')) { 
                        $maxId = (int)Mage::getModel('neklo_abtesting/visitor')->getMaxId();
                        
                        $id = Mage::getModel('neklo_abtesting/visitor')->logNewVisitor();

            			$cookieModel->set(Neklo_ABTesting_Model_Cookie::VISITOR_ID, $id, 0);
            			$cookieModel->set(Neklo_ABTesting_Model_Cookie::VISITS_COUNTER_FLAG, 1, 3 * 3600);
            			if(!Mage::registry('first_visit_flag')) Mage::register('first_visit_flag', 1);
            			if(!Mage::registry('visitor_id')) Mage::register('visitor_id', $id);
                        if(!Mage::registry('logging_in_progress')) Mage::register('logging_in_progress', true);
                    } else { 
                        $id = $cookieModel->get(Neklo_ABTesting_Model_Cookie::VISITOR_ID);
                    }
                }
        } catch (Exception $e) { 
            Mage::log('Can not set Visitor Id. ' . $e->getMessage(), null, 'ab-error.log');
        }

		return $id;
	}

	public function saveAbTestsLog(Varien_Event_Observer $observer) { 
		if(Mage::getModel('neklo_abtesting/visitor')->validateVisitor()) {
			$name = $observer->getCookieName();
			$cookieValue = $observer->getCookieValue();
			$visitorId = $this->getVisitorIdCookie();
        	Mage::getModel('neklo_abtesting/log')->logNewVisitor($visitorId, $name, $cookieValue);	
		}
	}

	public function onCustomerLogin(Varien_Event_Observer $observer)
    {
    	$cookieModel = $this->getCookieModel();
        $customer = $observer->getEvent()->getCustomer();
        $visitorId = $cookieModel->get(Neklo_ABTesting_Model_Cookie::VISITOR_ID);
        Mage::getModel('neklo_abtesting/visitor')->updateCustomerIdInLog($customer->getId(), $visitorId);
    }

	public function onCustomerCreate(Varien_Event_Observer $observer)
    {
    	$cookieModel = $this->getCookieModel();
        $customer = $observer->getEvent()->getCustomer();

        if ($customer->isObjectNew()) {
        	$visitorId = $cookieModel->get(Neklo_ABTesting_Model_Cookie::VISITOR_ID);
        	Mage::getModel('neklo_abtesting/visitor')->updateCustomerIdInLog($customer->getId(), $visitorId);
    	}
    }

    public function getCookieModel() { 
    	return Mage::getSingleton('core/cookie');
    }

    public function controllerActionLayoutLoadBefore(Varien_Event_Observer $observer) { 
    	/** @var $layout Mage_Core_Model_Layout */
        $layout = $observer->getEvent()->getLayout();
        $action = $observer->getEvent()->getAction();
 		$variants = Mage::helper('neklo_abtesting')->getVariants();
        $update = $layout->getUpdate();
        $handles = $update->getHandles();

        if(!Mage::registry('custom_layout_loaded')) { 
            foreach ($variants as $abTestId => $abPresentationId) {
            	$presentation = Mage::getModel('neklo_abtesting/abpresentation')->load($abPresentationId);
            	$xmlLayoutUpdate = $presentation->getLayoutUpdate();

                $abTestPresentationLinkId = Mage::getModel('neklo_abtesting/abtestpresentation')
                                            ->loadByAbTestPresentationId($abTestId, $abPresentationId)
                                            ->getId();


            	$htmlContent = $presentation->getHtmlContent();
            	
                $layoutElement = Mage::getConfig()->getModelClassName('core/layout_element');
                $xml = new SimpleXMLElement("<layout>" . $xmlLayoutUpdate . "</layout>");
               
                if($xmlLayoutUpdate) { 
                    foreach ($xml as $key => $xmlUpdate) {
                        if(in_array($key, $handles)) {
                            $updateString = '';
                            foreach ($xmlUpdate as $block) {
                                $updateString .= $block->asXML();
                            }

                            if($htmlContent) {
                                // If Custom event is Valid
                                if(Mage::getModel('neklo_abtesting/abtestevent')->loadByAbTestEventId($abTestId, 2)->getId()) { 
                                    $htmlContent = str_replace('{{link_id}}', $abTestPresentationLinkId, $htmlContent); 
                                } else { 
                                    $htmlContent = str_replace('{{link_id}}', 0, $htmlContent); 
                                }
                    			$updateString = str_replace('{{html}}', '<![CDATA[' . $htmlContent . ']]>', $updateString);
            				}  
                            $updateString = str_replace('{{html}}', '', $updateString);
                            
                    		$layout->getUpdate()->addUpdate($updateString);
                	    }
                    }
                    $layout->generateXml();
                }
            }
            Mage::register('custom_layout_loaded', 1);   
        }
    }
    
    public function checkValidEvents(Varien_Event_Observer $observer) { 
    	$allEventCodes = $this->catchEvents();
    	$abTests = Mage::helper('neklo_abtesting')->getAbtests();
        $visitorId = $this->getVisitorIdCookie();
       
        foreach ($abTests as $abTest) {
            $abTest = Mage::getModel('neklo_abtesting/abtest')->load($abTest->getAbtestId());
            if(!$abTest->isActive()) continue;
            $validCodes = array();
            $events = $abTest->getValidEvents();
            
            foreach ($events as $event) {
                $validCodes[$event->getId()] = $event->getCode();
            }
            $loggedEvents = array();

            $successEvents = array_intersect($allEventCodes, $validCodes);
            if(count($successEvents)) { 
                
                $activePresentations = Mage::helper('neklo_abtesting')->getVariants();
                $activePresentation = $activePresentations[$abTest->getId()];

                $abTestPresentationLinkId = Mage::getModel('neklo_abtesting/abtestpresentation')
                                            ->loadByAbTestPresentationId($abTest->getId(), $activePresentation)
                                            ->getId();
                
                if($abTestPresentationLinkId) { 
                    foreach ($successEvents as $code) {

                        $eventId = array_search($code, $validCodes);
                        $requestUri = Mage::app()->getRequest()->getRequestUri();

                        if(!in_array($requestUri . '-' . $code . '-' . $abTest->getId(), $loggedEvents)) { 
                            //Mage::log(Mage::app()->getRequest()->getRequestUri() . ' ' . $abTest->getName() . ' ' . $code, null, 'abSuccessEvents.log');
                            Mage::getModel('neklo_abtesting/log')->logSuccessEvent($visitorId, $eventId, $abTestPresentationLinkId, $code);   
                            $loggedEvents[] = $requestUri . '-' . $code . '-' . $abTest->getId();
                        }
                    }
                }
            }
        }
    }

    public function catchEvents() { 
		$app = Mage::app();
        
        $r = new ReflectionObject($app);
        $rp = $r->getProperty('_events');
        if(method_exists($rp, 'setAccessible'))
        {
            $rp->setAccessible(true);
            $events = $rp->getValue($app);        
        }
        else
        {
            $events = $this->_phpFivePointTwoReflection($app);
        }
        
        $eventCodes = array();
        foreach($events as $area=>$event)
        {
            foreach($event as $event_name=>$configuration)
            {
	             $eventCodes[] = $event_name;
            }        
        }

        return $eventCodes; 
	}

	protected function _phpFivePointTwoReflection($app)
    {
        $serialized = serialize($app);
        
        //grab the events portion
        $parts = preg_split('%.\*._events";%',$serialized);
        
        //remove pre first bracket and stow way for later
        $ser_events = $parts[1];
        $ser_events = explode('{', $ser_events);
        $start      = array_shift($ser_events);
        $ser_events = implode('{', $ser_events);
        
        //parse through until brackets balance — makes assumptions about 
        //_Events not containing a { or a }
        $length         = strlen($ser_events);
        $bracket_count  = 0;
        for($i=0;$i<$length;$i++)
        {            
            $chr = $ser_events[$i];
            
            if($chr == '{')
            {
                $bracket_count++;
            }

            if($chr == '}')
            {
                $bracket_count--;
            }
            
            if($bracket_count == -1)
            {
                break;
            }
        }
        //put the string back together
        $ser_events = substr($ser_events, 0, $i);
        $ser_events = $start . '{' . $ser_events . '}';
        $events = $this->_unserializeEvents($ser_events);
        $events = is_array($events) ? $events : array();
        return $events;        
    }

    /**
    * Wrapping in method in case the @ causes a problem (rewrite)
    */    
    protected function _unserializeEvents($ser_events)
    {
        return @unserialize($ser_events);
    }
}
