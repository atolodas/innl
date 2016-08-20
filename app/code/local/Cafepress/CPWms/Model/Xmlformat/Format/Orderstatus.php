<?php

class Cafepress_CPWms_Model_Xmlformat_Format_Orderstatus extends Cafepress_CPWms_Model_Xmlformat_Format_Abstract
{
    protected   $responseDOM = false;
    protected   $responseFormatDOM = false;
    
    public function _construct() {
        $this->_init('cpwms/xmlformat');
        parent::_construct();
    }
    
    public function generateRequest()
    {
        $template = $this->getTemplate();
        $this->setOutXml($this->getRequest());
        $template->setXmlformat($this);
        $vars = $this->getVariables();
        $xml = $this->substitutionVarsToXml('', $template, $vars);
        
        return $xml;
    }   
    
    protected function getTemplate()
    {
        return Mage::getModel('cpwms/template')->setStoreId($this->getStoreId());
    }
    
    /**
     *Copy $this->addVariable($key, $value)
     * only has a short name :)
     * @param type $key
     * @param type $value
     * @return type 
     */
    public function addVar($key, $value)
    {
        return $this->addVariable($key, $value);
    }
    
    public function processResponse($responseXML)
    {
        try {
            $this->responseDOM = new SimpleXMLElement($responseXML);
            $this->responseFormatDOM = new SimpleXMLElement($this->getResponse());

            $this->runXmlElementActions($this->responseFormatDOM, $this->responseDOM);
        } catch (Exception $e) {
            Mage::log($e->getMessage(),null,'orders.log');
            return false;
        }
        return true;
    }
    
    public function getMethodOfRequest()
    {
        return array('sentFileByHttp');
    }
    
}