<?php

class Cafepress_CPWms_Model_Xmlformat_Format_Entity_Helper extends Cafepress_CPWms_Model_Abstract
{
    protected $helperMethods = array(
        'dataToDatetime'        => array('cpwms/data','dataToDatetime'),
        'toShipCod'             => array('cpwms/data','getCodShippingMethod'),
        'toStatusCod'           => array('cpwms/data','getCodOrderStatus'),
        'toDateShip'            => array('cpwms/data','formatDateShip'),
        'getNumberFromCustom'   => array('cpwms/data','getNumberFromCustomMetod'),
        'unCdata'               => array('cpwms/data','unCdata'),
        'getCountryIsoCode'     => array('cpwms/data','getCountryIsoCode')
    );
    
    protected function _getVariablesModel()
    {
        return Mage::getSingleton('cpwms/xmlformat_format_entity_variable');
    }
    
    public function setVar($method,$inData,$varName)
    {
	//	Zend_Debug::dump($method);
        if ($this->isMetod($method)){
            $variable = $this->_getVariablesModel();
            $variable->setVar($varName, $this->getHelperMethod($method, $inData));
        }
        return $this;
    }
    
    public function getHelperMethod($method,$data)
    {
        if ($this->isMetod($method)){
            $helper = $this->helperMethods[$method][0];
            $method = $this->helperMethods[$method][1];
            return Mage::helper($helper)->$method($data);
        }
    }
    
    protected function isMetod($method)
    {
        if (isset($this->helperMethods[$method])){
            return true;
        }
        return false;
    }

  
    
    
//    public function getTerm($method,$in)
//    {
//        $variable = $this->_getVariablesModel();
//        $variable->setVar($varName, Mage::helper('cpwms')->$method($in));
//        return $this;
//    }

    

}

