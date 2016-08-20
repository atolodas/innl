<?php

class Cafepress_CPWms_Block_Adminhtml_Logging_Request_Tabs_Form_General_Downfileparsresp extends Mage_Adminhtml_Block_Widget_Form
{
    
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('general', array('legend'=>Mage::helper('cpwms')->__('WMS Log Download File And Parse Response')));

        $fieldset->addField('log_id', 'hidden', array('name' => 'log_id', 'value' => $this->getLog()->getId()));
        
        $fieldset->addField('url', 'text', array(
            'name'      => 'resend[url]',
            'label'     => Mage::helper('cpwms')->__('Url Download File'),
            'title'     => Mage::helper('cpwms')->__('Url Download File'),
//            'style'     => 'width:150%;',
            'wysiwyg'   => false,
            'value'     => $this->getLog()->getUrlOfRequest()
            
        ));
        
        $fieldset->addField('request', 'textarea', array(
            'name'      => 'resend[request]',
            'label'     => Mage::helper('cpwms')->__('Request'),
            'title'     => Mage::helper('cpwms')->__('Request'),
            'style'     => 'width:200%; height:400pt;',
            'wysiwyg'   => false,
            'required'  => true,
            'value'     => $this->getLog()->getRequest()
            
        ));
        
        $fieldset->addField('response_format', 'textarea', array(
            'name'      => 'resend[response_format]',
            'label'     => Mage::helper('cpwms')->__('Response Format'),
            'title'     => Mage::helper('cpwms')->__('Response Format'),
            'style'     => 'width:200%; height:400pt;',
            'wysiwyg'   => false,
//            'required'  => true,
            'value'     => $this->getLog()->getResponseFormat()
            
        ));
        
        $this->setForm($form);
    }
    
    public function getLog()
    {
        return Mage::registry('current_wms_log');
    }
    

}