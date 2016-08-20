<?php

class Cafepress_CPCore_Model_Xmlformat_Format_Entity_Helper extends Cafepress_CPCore_Model_Abstract
{
    protected $helperMethods = array(
        'dataToDatetime'        => array('cpcore/data','dataToDatetime'),
        'toShipCod'             => array('cpcore/data','getCodShippingMethod'),
        'toStatusCod'           => array('cpcore/data','getCodOrderStatus'),
        'toDateShip'            => array('cpcore/data','formatDateShip'),
        'getNumberFromCustom'   => array('cpcore/data','getNumberFromCustomMetod'),
        'unCdata'               => array('cpcore/data','unCdata'),
        'getCountryIsoCode'     => array('cpcore/data','getCountryIsoCode'),
        'cleanHtml'             => array('cpcore/data','cleanHtml'),
    );

    protected function _getVariablesModel()
    {
        return Mage::getSingleton('cpcore/xmlformat_format_entity_variable');
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
        if (isset($this->helperMethods[$method])) {
            return true;
        }
        return false;
    }
}

