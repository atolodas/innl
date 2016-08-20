<?php

class Cafepress_CPWms_Block_Adminhtml_Catalog_Product_Tab extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    protected $_product = null;

    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('cpwms/catalog/product/tab.phtml');
    }

    public function getTabLabel()
    {
        return $this->__('CP Simples Creation');
    }

    public function getTabTitle()
    {
        return $this->__('CP Simples Creation');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        if($this->_getProduct()->getTypeId() == 'configurable'){
            return false;
        } else{
            return true;
        }
    }

    protected function _getProduct()
    {
        if (!$this->_product) {
            $this->_product = Mage::registry('current_product');
        }
        return $this->_product;
    }

    public function getSimpleXml(){
        $result = false;
        $xml = $this->_getProduct()->getCpSaveProductXml();
        if($xml){
            $result = simplexml_load_string($xml);
        }
        return $result;
    }

    public function getChildCustomSkus(){
        $result = Mage::getModel('cpwms/cafepress_product')->getChildProductCustomSkus($this->_getProduct());
        return $result;
    }

    protected function _prepareLayout(){
        $this->_updateActiveTab();
        return parent::_prepareLayout();
    }

    protected function _updateActiveTab() {

        $tabId = $this->getRequest()->getParam('tab');
        if( $tabId ) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if($tabId) {
                $this->setActiveTab($tabId);
            }
        }

    }
}