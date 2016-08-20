<?php

class Cafepress_CPWms_Block_Adminhtml_Logging_Request_Tabs_Form_General_Transformer extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('general', array('legend'=>Mage::helper('cpwms')->__('WMS Log Request And Response')));

        $fieldset->addField('log_id', 'hidden', array('name' => 'log_id', 'value' => $this->getLog()->getId()));

        $fieldset->addField('url', 'text', array(
            'name'      => 'resend[url]',
            'label'     => Mage::helper('cpwms')->__('Url Resend'),
            'title'     => Mage::helper('cpwms')->__('Url Resend'),
//            'style'     => 'width:150%;',
            'wysiwyg'   => false,
//            'required'  => true,
            'value'     => $this->getLog()->getUrlOfRequest()
        ));

        $fieldset->addField('fuction', 'text', array(
            'name'      => 'resend[function]',
            'label'     => Mage::helper('cpwms')->__('Function'),
            'title'     => Mage::helper('cpwms')->__('Function'),
//            'style'     => 'width:150%;',
            'wysiwyg'   => false,
            'required'  => true,
            'value'     => $this->getLog()->getFunction(),
            'note'      => 'FTP / HTTP / other - SOAP functions'
        ));

        $fieldset->addField('request', 'textarea', array(
            'name'      => 'resend[request]',
            'label'     => Mage::helper('cpwms')->__('Request'),
            'title'     => Mage::helper('cpwms')->__('Request'),
            'style'     => 'width:200%; height:300pt;',
            'wysiwyg'   => false,
            'required'  => true,
            'value'     => Mage::helper('cpwms/xml')->xmlstringToXmldom($this->getLog()->getRequest())
        ));

        $fieldset->addField('response_format', 'textarea', array(
            'name'      => 'resend[response]',
            'label'     => Mage::helper('cpwms')->__('Response Format'),
            'title'     => Mage::helper('cpwms')->__('Response Format'),
            'style'     => 'width:200%; height:300pt;',
            'wysiwyg'   => false,
//            'required'  => true,
            'value'     => Mage::helper('cpwms/xml')->xmlstringToXmldom($this->getLog()->getResponseFormat())
        ));

        $fieldset->addField('response', 'textarea', array(
            'name'      => 'resend[response]',
            'label'     => Mage::helper('cpwms')->__('Response'),
            'title'     => Mage::helper('cpwms')->__('Response'),
            'style'     => 'width:200%; height:200pt;',
            'wysiwyg'   => false,
            'readonly'  => true,
            'value'     => Mage::helper('cpwms/xml')->xmlstringToXmldom($this->getLog()->getResponse())

        ));

        $this->setForm($form);
    }
    
    public function getLog()
    {
        return Mage::registry('current_wms_log');
    }

}