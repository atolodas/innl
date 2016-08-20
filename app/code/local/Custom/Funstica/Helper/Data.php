<?php
class Custom_Funstica_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function translate($string, $number) { 
        $lastChar = substr($number.'', -1);
        $translate['идей'] = array('идея','идеи');
        $translate['мест'] = array('место','места');
        $translate['событий'] = array('событие','события');
        $translate['путешествий'] = array('путешествие','путешествия');
        $translate['вещей'] = array('вещь','вещи');
        $translate['скидок'] = array('скидка','скидки');
        
        foreach ($translate as $key => $value) {
        	if(in_array($number, array(11,12,13,14))) {}
            elseif($lastChar == '1') { $string = str_replace($key, $value[0], $string); }
        	elseif(in_array($lastChar,array('2','3','4'))) { $string = str_replace($key, $value[1], $string); }
        }
        return $string;
	}

        public function getAccountUrl()
    {
        return Mage::getUrl('ideas');
    }

}