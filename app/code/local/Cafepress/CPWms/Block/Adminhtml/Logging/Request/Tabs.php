<?php

class Cafepress_CPWms_Block_Adminhtml_Logging_Request_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
//    protected $_attributeTabBlock = 'adminhtml/catalog_product_edit_tab_attributes';

    public function __construct()
    {
        parent::__construct();
        $this->setTitle(Mage::helper('cpwms')->__('WMS Logging View Request'));
        
        $this->setId('logging_info_tabs');
//        $this->setDestElementId('wmslog_info_tabs_general_content');
        $this->setDestElementId('wms_logging_request_form');
//        $this->setDestElementId('xmlformat_edit_form');
    }

    protected function _prepareLayout()
    {
        $log = $this->getLog();
        if (!$log->getId()){
            die('No Log:'.__METHOD__);
        }
//        Zend_Debug::dump($log->getData());
        
        $storeId = 0;
        if (!$storeId) {
            $this->getRequest()->setParam('store', 0);
        }

        $xmlformat = Mage::getModel('cpwms/xmlformat')
                ->setStoreId($storeId)
                ->load($log->getFormatId());
        
        $typeFormat = $xmlformat->getType();
        $formatTypeCode =  Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_type')->getNameTypeById($typeFormat);
        
        $this->addTab('general', array(
            'label'     => Mage::helper('cpwms')->__('General'),
            'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpwms/adminhtml_logging_request_tabs_form_general_'.$formatTypeCode)->toHtml()),
            'active'    => true
        ));
        
        return parent::_prepareLayout();
        
        if (!($typeFormat = $format->getAttributeType())) {
            $typeFormat = $this->getRequest()->getParam('type', null);
        }
        if (!($idFormat = $format->getAttributeIdFormat())) {
            $idFormat = $this->getRequest()->getParam('id', null);
        }
        
        if ($typeFormat || $idFormat) {
            if (!$typeFormat){
                $typeFormat = Mage::registry('current_xmlformat')->getType();
            }
            $formatTypeCode =  Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_type')->getNameTypeById($typeFormat);
            if (!$formatTypeCode){
                die('Error in method: '.__METHOD__);
            }
            
            if ($formatTypeCode == 'transformer'){
               
                $this->addTab('general', array(
                    'label'     => Mage::helper('cpwms')->__('General'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpwms/adminhtml_xmlformat_edit_tabs_form_'.$formatTypeCode.'_general')->toHtml()),
                    'active'    => true
                ));
                $this->addTab('request', array(
                    'label'     => Mage::helper('cpwms')->__('Request'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpwms/adminhtml_xmlformat_edit_tabs_form_'.$formatTypeCode.'_request')->toHtml()),
//                    'active'    => true
                ));
                
                $this->addTab('response', array(
                    'label'     => Mage::helper('cpwms')->__('Response'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpwms/adminhtml_xmlformat_edit_tabs_form_'.$formatTypeCode.'_response')->toHtml()),
//                    'active'    => true
                ));
                
                $this->addTab('example', array(
                    'label'     => Mage::helper('cpwms')->__('Example'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpwms/adminhtml_xmlformat_edit_tabs_form_example')->toHtml()),
                ));
            } else {
                $this->addTab('general', array(
                    'label'     => Mage::helper('cpwms')->__('General'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpwms/adminhtml_xmlformat_edit_tabs_form_general_'.$formatTypeCode)->toHtml()),
                    'active'    => true
                ));
                $this->addTab('example', array(
                    'label'     => Mage::helper('cpwms')->__('Example'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpwms/adminhtml_xmlformat_edit_tabs_form_example')->toHtml()),
                ));
            }
                        
        } else {
            $this->addTab('settings', array(
                'label'     => Mage::helper('cpwms')->__('Settings'),
                'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpwms/adminhtml_xmlformat_edit_tabs_form_settings')->toHtml()),
                'active'    => true
            ));
            
        }

        return parent::_prepareLayout();
    }

    public function getLog()
    {
        return Mage::registry('current_wms_log');
    }
    /**
     * Translate html content
     * 
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}
