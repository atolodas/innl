<?php

class Cafepress_CPCore_Model_Xmlformat_Format_Creditmemo extends Cafepress_CPCore_Model_Xmlformat_Format_Abstract
{
    protected   $order = false;
    protected   $creditmemo = false;
    protected   $format = false;


    public function _construct() {
        $this->_init('cpcore/xmlformat');
        parent::_construct();
    }
    
    public function generateRequest()
    {
        $template = $this->getTemplate();
        $this->setOutXml($this->getRequest());
        $template->setXmlformat($this);
        $vars = $this->getVariables();
        
        $xml = $this->substitutionVarsToXml($xml, $template, $vars);

        return $xml;
    }   
    
    protected function getTemplate()
    {
        return Mage::getModel('cpcore/template')->setStoreId($this->getStoreId());
    }
    
    
    public function getOrder(){
        return $this->order;
    }
    public function getCreditmemo(){
        return $this->creditmemo;
    }

    public function setOrder($order){
        $this->order = $order;
        return $this;
    }
    public function setCreditmemo($creditmemo){
        $this->creditmemo = $creditmemo;
        return $this;
    }
    
    public function setOrderById($orderId){
        $this->order = Mage::getModel('cpcore/sales_order')->load($orderId);
        return $this;
    }
    public function setCreditmemoById($crId){
        $this->creditmemo = Mage::getModel('cpcore/sales_order')->load($crId);
        return $this;
    }

    
    public function getSaveFilename()
    {
        $this->addVariable('order', $this->getOrder());
        $this->addVariable('creditmemo', $this->getCreditmemo());
        
        $template = $this->getTemplate();
        $this->format->setOutXml($this->format->getFilenameOut());
        $template->setXmlformat($this->format);
        $vars = $this->getVariables();
        
        $name = $this->substitutionVarsToXml('', $template, $vars);
        
        $name = preg_replace("/[\s]*/", '', $name);

        return $name;
    }
    
    public function setFormat(){
        $type = Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getType('creditmemo');
        $this->format = Mage::getModel('cpcore/xmlformat')
                ->setStoreId($this->getStoreId())
                ->loadByAttributes(array(
                    'type' => $type,
                    'status'=> '1'//1-enabled, 2- disabled
                ));
        return $this;
    }
    
}
