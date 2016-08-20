<?php
class Neklo_ABTesting_ActionController extends Mage_Core_Controller_Front_Action {
 	
 	public function logCustomEventAction() { 
 		$params = $this->getRequest()->getParams();
 		echo $params['linkId'];
 		if(isset($params['linkId']) && $params['linkId']) { 
	 		$visitorId = Mage::getModel('neklo_abtesting/observer')->getVisitorIdCookie();
	        
	 		// Logging custom event
		    Mage::getModel('neklo_abtesting/log')->logSuccessEvent($visitorId, 2, $params['linkId']);
        } else { 
        	echo "NOLOGG";
        }
 	}
}