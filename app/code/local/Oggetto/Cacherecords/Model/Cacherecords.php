<?php

class Oggetto_Cacherecords_Model_Cacherecords extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('cacherecords/cacherecords');
    }
    
    public function sendMailAboutReferers($email) { 
    	$orders=Mage::getModel('sales/order')->getCollection()
    	->addAttributeToFilter('created_at',array('like'=>date('Y-m-d').'%'));
    	
    	$str = '';
    	
    	foreach($orders as $o) { 
    		$order=Mage::getModel('sales/order')->load($o->getId());
    		
	    	if($order->getFirstReferer().$order->getFirstLanding().$order->getReferer().$order->getReferer()!='') {
	    		$str.= "Order Id: ".$order->getIncrementId()."<br/>";
	    		$str.="<br/>";
	    		$str.= "Order First Referrer Url: ";
	    		if($order->getFirstReferer()!='') { $str.=$order->getFirstReferer(); } else { $str.="None"; } 
	    		$str.="<br/>";
	    		$str.= "Order First Landing Page: ";
	    		if($order->getFirstLanding()!='') { $str.=$order->getFirstLanding(); } else { $str.="None"; }
	    		$str.="<br/>"; 
	    		$str.= "Order Last Referrer Url: ";
	    		if($order->getReferer()!='') { $str.=$order->getReferer(); } else { $str.="None"; }
	    		$str.="<br/>"; 
	    		$str.= "Order Last Landing Page: ";
	    		if($order->getReferer()!='') { $str.=$order->getReferer(); } else { $str.="None"; }
	    		$str.="<br/><br/>"; 
	    	}
    		
	    	//Order Number;Order Date;Total Order Amount;Items;First Referer Url;First Landing Page;Last Referer Url;Last Landing Page;
			//Item #: moo-2-010 - Kaleen Moods Ban-0 Size: 3'6" x 5'3" - Color: Red
    	}
    	
    	if($str!='') { 
    		$str = date('m.d.Y')."Orders with referrers: <br/><br/>".$str;
    		$headers= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			
			$message = '<html><head></head><body>'.$str.'</body></html>';
			mail('ivanovp.daemon@gmail.com','Orders with referrers',$message,$headers);
			mail($email,'Orders with referrers',$message,$headers); 
    	}
    }
}