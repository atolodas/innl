<?php

class Cafepress_CPCore_Block_Adminhtml_Xmlformat_Edit_Tabs_Form_Downfileparsresp_Response extends Cafepress_CPCore_Block_Adminhtml_Xmlformat_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_xmlformat'));
        $fieldset = $form->addFieldset('general', array('legend'=>Mage::helper('cpcore')->__('Response')));

        $additionalData = array(
			'response'=>array('style'=>'width:500px; height:300px;')
        );
        $fieldset->addField('type', 'hidden', array(
            'name'  => 'xmlformat[type]',
            'value' => Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getIdTypeByName('downfileparsresp')
        ));

        $this->_setFieldset(Mage::getModel('cpcore/xmlformat')->getResource()->loadAllAttributes()->getSortedAttributes(), $fieldset, $additionalData);

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
