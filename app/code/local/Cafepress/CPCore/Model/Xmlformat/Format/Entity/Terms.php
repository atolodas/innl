<?php

class Cafepress_CPCore_Model_Xmlformat_Format_Entity_Terms extends Cafepress_CPCore_Model_Xmlformat_Format_Abstract
{
    
    public function _getVarModel()
    {
        return Mage::getSingleton('cpcore/xmlformat_format_entity_variable');
    }
    
    public function issetVar($name)
    {
        $variables = $this->_getVarModel()->getAll();
        if ((isset($variables[$name])) && ($variables[$name]!='')){
            return true;
        }
        return false;
    }

    public function issetValue()
    {
        $variables = $this->_getVarModel()->getAll();
        if ((isset($variables['val'])) && ($variables['val']!='')){
            return true;
        }
        return false;
    }
    
    public function positiveValue()
    {
        $variables = $this->_getVarModel()->getAll();
        if ((isset($variables['val'])) && ($variables['val']!='0.0000')&& ($variables['val']!=0)){
            return true;
        }
        return false;
    }
    
    public function compare($val1, $val2)
    {
        $val1 = trim(strtolower($val1));
        $val2 = trim(strtolower($val2));
        
        if ($val1==$val2){
            return true;
        } 
        return false;
    }
    
    public function pregmatch($pattern, $subject)
    {
        return preg_match($pattern, $subject);
    }
    
}