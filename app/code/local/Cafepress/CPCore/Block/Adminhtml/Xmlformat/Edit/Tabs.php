<?php

class Cafepress_CPCore_Block_Adminhtml_Xmlformat_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('xmlformat_info_tabs');
        $this->setDestElementId('xmlformat_edit_form');
        $this->setTitle(Mage::helper('cpcore')->__('Xml Format Information'));
    }

    protected function _prepareLayout()
    {
        $format = $this->getFormat();
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
            $formatTypeCode =  Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getNameTypeById($typeFormat);
            if (!$formatTypeCode){
                die('Error in method: '.__METHOD__);
            }

            $help_html = '<iframe style="width: 100%" height="659px" src="'.'http://innativelife.com/wms/'.$formatTypeCode.'"></iframe>';//.Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getNameTypeById(Mage::registry('current_xmlformat')->getType());

            if ($formatTypeCode == 'transformer'){

                $this->addTab('general', array(
                    'label'     => Mage::helper('cpcore')->__('General'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/adminhtml_xmlformat_edit_tabs_form_'.$formatTypeCode.'_general')->toHtml()),
                ));
                $this->addTab('request', array(
                    'label'     => Mage::helper('cpcore')->__('Request'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/adminhtml_xmlformat_edit_tabs_form_'.$formatTypeCode.'_request')->toHtml()),
                ));

                $this->addTab('response', array(
                    'label'     => Mage::helper('cpcore')->__('Response'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/adminhtml_xmlformat_edit_tabs_form_'.$formatTypeCode.'_response')->toHtml()),
                ));

                $this->addTab('help', array(
                    'label'     => Mage::helper('cpcore')->__('Help'),
                    'content'   => $this->_translateHtml($help_html),
                ));
            } else {
                $this->addTab('general', array(
                    'label'     => Mage::helper('cpcore')->__('General'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/adminhtml_xmlformat_edit_tabs_form_'.$formatTypeCode.'_general')->toHtml()),
                ));
                $this->addTab('request', array(
                    'label'     => Mage::helper('cpcore')->__('Request'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/adminhtml_xmlformat_edit_tabs_form_'.$formatTypeCode.'_request')->toHtml())
                ));
                $this->addTab('response', array(
                    'label'     => Mage::helper('cpcore')->__('Response'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/adminhtml_xmlformat_edit_tabs_form_'.$formatTypeCode.'_response')->toHtml())
                ));
                $this->addTab('schedule', array(
                    'label'     => Mage::helper('cpcore')->__('Schedule'),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/adminhtml_xmlformat_edit_tabs_form_'.$formatTypeCode.'_schedule')->toHtml())
                ));
                if($formatTypeCode == 'order'){
                    $this->addTab('condition_check', array(
                        'label'     => Mage::helper('cpcore')->__('Condition Check'),
                        'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/adminhtml_xmlformat_edit_tabs_form_'.$formatTypeCode.'_condition_check')->toHtml())
                    ));
                }
                $this->addTab('help', array(
                    'label'     => Mage::helper('cpcore')->__('Help'),
                    'content'   => $this->_translateHtml($help_html),
                ));
            }

        } else {
            $this->addTab('settings', array(
                'label'     => Mage::helper('cpcore')->__('Settings'),
                'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpcore/adminhtml_xmlformat_edit_tabs_form_settings')->toHtml()),
            ));

        }
        $this->_updateActiveTab();
        return parent::_prepareLayout();
    }

    public function getFormat()
    {
        if (!($this->getData('format') instanceof Cafepress_CPCore_Model_Xmlformat)) {
            $this->setData('format', Mage::registry('current_xmlformat'));
        }
        return $this->getData('format');
    }

    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }

    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if( $tabId ) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if($tabId) {
                $this->setActiveTab($tabId);
            }
        }
    }
}
