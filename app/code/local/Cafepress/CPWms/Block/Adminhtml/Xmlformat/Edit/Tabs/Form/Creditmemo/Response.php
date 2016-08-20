<?php

class Cafepress_CPWms_Block_Adminhtml_Xmlformat_Edit_Tabs_Form_Creditmemo_Response extends Cafepress_CPWms_Block_Adminhtml_Xmlformat_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_xmlformat'));
        $fieldset = $form->addFieldset('general', array('legend'=>Mage::helper('cpwms')->__('Response')));

        $additionalData = array(
			'response'=>array('style'=>'width:500px; height:300px;')
        );
        $fieldset->addField('type', 'hidden', array(
            'name'  => 'xmlformat[type]',
            'value' => Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_type')->getIdTypeByName('creditmemo')
        ));

        $this->_setFieldset(Mage::getModel('cpwms/xmlformat')->getResource()->loadAllAttributes()->getSortedAttributes(), $fieldset, $additionalData);

        if ( Mage::getSingleton('adminhtml/session')->getXmlformatData() )
        {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getXmlformatData());
            Mage::getSingleton('adminhtml/session')->setXmlformatData(null);
        } elseif (Mage::registry('current_xmlformat')) {
            $form->addValues(Mage::registry('current_xmlformat')->getData());
        }

        $this->setForm($form);
    }
}
