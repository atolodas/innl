<?php

class Cafepress_CPWms_Model_Xmlformat extends Cafepress_CPWms_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY                = 'wms_xmlformat';
    
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix     = 'wms_xmlformat';
    
    protected function _construct()
    {
        $this->_init('cpwms/xmlformat');
        parent::_construct();
    }
    
    public function validate()
    {
//        $this->getAttributes();
//        Mage::dispatchEvent($this->_eventPrefix.'_validate_before', array($this->_eventObject=>$this));
//        $result = $this->_getResource()->validate($this);
//        Mage::dispatchEvent($this->_eventPrefix.'_validate_after', array($this->_eventObject=>$this));
//        return $result;
        Mage::dispatchEvent($this->_eventPrefix.'_validate_before', array($this->_eventObject=>$this));
        $this->_getResource()->validate($this);
        Mage::dispatchEvent($this->_eventPrefix.'_validate_after', array($this->_eventObject=>$this));
        return $this;
    }
    
    public function getAllFormatName()
    {
        $result = array();
        foreach($this->getCollection()->addAttributeToSelect('*') as $format){
            $result[$format->getId()] = $format->getName();
        }
        return $result;
    }
}
