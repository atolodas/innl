<?php

class Cafepress_CPCore_Model_Xmlformat_Variable_Date extends Cafepress_CPCore_Model_Xmlformat_Variable_Abstract
{

    public function getNow()
    {
        //Zend_Debug::dump(date('Y-m-d H:i:s'));
        return date('Y-m-d H:i:s');
    }
    
    /**
     * Get Now date +/- modificator
     * @param type $modificator :- 1 day - 3 weeks + 3 years
     * @return type 
     */
    public function getNowDate($modificator=false)
    {
        $result = date('Y-m-d');
        if ($modificator){
            $result = date('Y-m-d', strtotime($modificator));
        }
        return $result;
    }
    
}