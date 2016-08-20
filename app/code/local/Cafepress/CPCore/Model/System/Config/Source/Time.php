<?php

class Cafepress_CPCore_Model_System_Config_Source_Time
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    		$locale = Mage::getModel('core/locale');
			$timezone = $locale->getTimezone();
			
		$dateObj = Mage::app()->getLocale()->date();
		$timezone = 'GMT'.$dateObj->get('ZZZZ'); 
		
     
    		for($i=1; $i<=12; $i++) { 
		        $hours[] = array('value' => $i, 'label'=>$i.' AM '.$timezone);
		 	}
    		for($i=1; $i<=12; $i++) { 
		        $hours[] = array('value' => $i+12, 'label'=>$i.' PM '.$timezone);
		 	}
        	return $hours;
    }

}
