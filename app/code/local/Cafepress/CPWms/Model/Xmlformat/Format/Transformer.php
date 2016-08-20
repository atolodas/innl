<?php

class Cafepress_CPWms_Model_Xmlformat_Format_Transformer extends Cafepress_CPWms_Model_Xmlformat_Format_Abstract
{
    protected $_serverResponse = false;
    protected $_format = false;

    public function processResponse($cap=false)
    {
        $result = NULL;
        
        $methodResponse = Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_Responsemethods')->getNameTypeById($this->getResponseMethod());
        switch ($methodResponse){
            case 'return_result':{
                if (!Mage::helper('cpwms')->isXML($this->_serverResponse)){
                    return false;
                }
                $this->responseDOM = new SimpleXMLElement($this->_serverResponse);
                $this->responseFormatDOM = new SimpleXMLElement($this->_getResponse());
                
                $this->runXmlElementActions($this->responseFormatDOM, $this->responseDOM);
                $result = Mage::getSingleton('cpwms/xmlformat_format_entity_result')->getResult();
            } break;
            case 'return_result_simple':{
                $result = $this->_serverResponse;
            } break;
            case 'edit_result_return_all':{
                if (!Mage::helper('cpwms')->isXML($this->_serverResponse)){
                    return false;
                }
                $this->responseDOM = new SimpleXMLElement($this->_serverResponse);
                $this->responseFormatDOM = new SimpleXMLElement($this->getResponse());
                
                Mage::getSingleton('cpwms/xmlformat_format_entity_edit')->setIncomingXmlObj($this->responseDOM);
                $changes = $this->runXmlElementActions($this->responseFormatDOM, $this->responseDOM);
                $result = Mage::getSingleton('cpwms/xmlformat_format_entity_edit')->getResult();
                return $result;
            } break;
            case 'return_data':{
                if (!Mage::helper('cpwms')->isXML($this->getRequest())){
                    return false;
                }
                $storeId = $this->getStoreId();
                $template = Mage::getModel('cpwms/template')->setStoreId($storeId);
                $this->setOutXml($this->getRequest());
                $template->setXmlformat($this);
                $vars = $this->getVariables();

                if(Mage::registry('order_id')){
                    $order = Mage::getModel('cpwms/sales_order')->load(Mage::registry('order_id'));
                    $vars['order'] = $order;
                }

                $result = $this->substitutionVarsToXml('', $template, $vars);
                $result = str_replace('<![CDATA[', '', $result);
                $result = str_replace(']]>', '', $result);
                Mage::unregister('order_id');
                return $result;
            } break;
        
        }
        return $result;
    }
    
    public function getServerResponse()
    {
        return $this->_serverResponse;
    }
    public function setServerResponse($xml)
    {
        $this->_serverResponse = $xml;
        return $this;
    }
    
    public function getModelformatByName($name,$storeId = false)
    {
        if (!$storeId){
            $storeId = $this->getStoreId();
        }
        $xmlformatModel = $this
                ->setStoreId($storeId)
                ->loadByAttribute('name',trim($name));
        
        $xmlformatModel = $this
                    ->setStoreId($storeId)
                    ->load($xmlformatModel->getId());
        
        if (!$xmlformatModel->getName()){
            die('No format With name "'.$name.'"!');
        }
        
        return $xmlformatModel;
    }

    public function substitutionVarsToXml($xml, $template, $vars = array()) {
        Varien_Profiler::start("xmlformat_template_proccessing");
        $templateProcessed = $template->getProcessedTemplate($vars, true);
        Varien_Profiler::stop("xmlformat_template_proccessing");
        $xml .= $templateProcessed;
        return $xml;
    }

    public function repleasePatternString($string)
    {
        $string = Mage::helper('cpwms/xml')->removeSpaceFromXML($string);
        $template = $this->getTemplate();
        $this->setOutXml($string);
        $template->setXmlformat($this);
        $vars = $this->getVariables();

        $name = $this->substitutionVarsToXml('', $template, $vars);
        return $name;
    }

    public function processRequest()
    {
        $methodRequest = Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_Requestmethods')->getNameTypeById($this->getRequestMethod());
        switch ($methodRequest){
            case 'simple_url':{
            echo $url = $this->repleasePatternString($this->getUrlRequest());
            $this->_serverResponse = Mage::getModel('cpwms/xmlformat_outbound')->getResponseByUrl($url);
            } break;
            case 'soap':{
            $xml = $this->repleasePatternString($this->getRequest());
            $this->setRequest($xml);
            $url = $this->repleasePatternString($this->getUrlRequest());
            $this->_serverResponse = Mage::getModel('cpwms/xmlformat_outbound')->getResponseOverSoap($url, $this->getPatternRequest(), $xml);
            } break;
            case 'http':{
            $xml = $this->repleasePatternString($this->getRequest());
            $this->setRequest($xml);
            $url = $this->repleasePatternString($this->getUrlRequest());
            $this->_serverResponse = Mage::getModel('cpwms/xmlformat_outbound')->sendXmlOverPost($xml,$this->getStoreId(),array('url'=>$url));
            } break;
        }
        return $this;
    }

}


