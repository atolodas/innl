<?php

class Cafepress_CPCore_Block_Adminhtml_Logging_Request_Tabs_Form_General_Orderstatus extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('general', array('legend'=>Mage::helper('cpcore')->__('WMS Log Orderstatus')));

        $fieldset->addField('log_id', 'hidden', array('name' => 'log_id', 'value' => $this->getLog()->getId()));

        $fieldset->addField('url', 'text', array(
            'name'      => 'resend[url]',
            'label'     => Mage::helper('cpcore')->__('Url Resend'),
            'title'     => Mage::helper('cpcore')->__('Url Resend'),
            'wysiwyg'   => false,
            'value'     => $this->getLog()->getUrlOfRequest()

        ));
        $fieldset->addField('request', 'textarea', array(
            'name'      => 'resend[request]',
            'label'     => Mage::helper('cpcore')->__('Request'),
            'title'     => Mage::helper('cpcore')->__('Request'),
            'style'     => 'width:200%; height:300pt;',
            'wysiwyg'   => false,
            'required'  => true,
            'value'     => $this->getLog()->getRequest()
        ));

        $fieldset->addField('fuction', 'text', array(
            'name'      => 'resend[function]',
            'label'     => Mage::helper('cpcore')->__('Function'),
            'title'     => Mage::helper('cpcore')->__('Function'),
            'wysiwyg'   => false,
            'required'  => true,
            'value'     => $this->getLog()->getFunction(),
            'note'      => 'FTP / HTTP / other - SOAP functions'
        ));

        $fieldset->addField('response_format', 'textarea', array(
            'name'      => 'resend[response]',
            'label'     => Mage::helper('cpcore')->__('Response Format'),
            'title'     => Mage::helper('cpcore')->__('Response Format'),
            'style'     => 'width:200%; height:300pt;',
            'wysiwyg'   => false,
            'value'     => $this->getLog()->getResponseFormat()
        ));

        $fieldset->addField('response', 'textarea', array(
            'name'      => 'resend[response]',
            'label'     => Mage::helper('cpcore')->__('Response'),
            'title'     => Mage::helper('cpcore')->__('Response'),
            'style'     => 'width:200%; height:200pt;',
            'wysiwyg'   => false,
            'readonly'  => true,
            'value'     => $this->getLog()->getResponse()

        ));

        $this->setForm($form);
    }
    
    public function getLog()
    {
        return Mage::registry('current_cp_log');
    }
    

}