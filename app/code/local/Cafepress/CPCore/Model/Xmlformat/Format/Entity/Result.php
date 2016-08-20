<?php

class Cafepress_CPCore_Model_Xmlformat_Format_Entity_Result extends Cafepress_CPCore_Model_Xmlformat_Format_Abstract
{
    
    protected $_result = false;

    public function _getVarModel()
    {
        return Mage::getSingleton('cpcore/xmlformat_format_entity_variable');
    }
    
    public function setResult($data){
        $this->_result = $data;
        return $this;
    }
    
    public function getResult(){
        return $this->_result;
    }
    
    public function add($key,$val)
    {
        $this->_result[$key] = $val;
//        Zend_Debug::dump($key);
//        Zend_Debug::dump($val);
        return $this;
    }

	public function setMulti($key,$val)
    {
		if(!is_array($this->_result)) $this->_result = array();
		if(!is_array($this->_result[$key])) $this->_result[$key] = array();
        $this->_result[$key][] = $val;
//        Zend_Debug::dump($key);
//        Zend_Debug::dump($val);
        return $this;
	}
}