<?php

class Cafepress_CPCore_Block_Adminhtml_Xmlformat_Edit_Tabs_Form_Creditmemo_Schedule extends Cafepress_CPCore_Block_Adminhtml_Xmlformat_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_xmlformat'));
        $fieldset = $form->addFieldset('general', array('legend'=>Mage::helper('cpcore')->__('Schedule')));

        $fieldset->addField('type', 'hidden', array(
            'name'  => 'xmlformat[type]',
            'value' => Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getIdTypeByName('creditmemo')
        ));

        $fieldset->addField('schedulepro', 'hidden', array(
            'required' => false
        ));

        $fieldset->addType('schedule', 'Cafepress_CPCore_Lib_Varien_Data_Form_Element_Schedule');
        $fieldset->addField('schedule', 'schedule', array(
            'label' => 'Schedule',
            'name' => 'schedule',
            'required' => false,
            'style' => 'width:100%;'
        ));

        $this->_setFieldset(Mage::getModel('cpcore/xmlformat')->getResource()->loadAllAttributes()->getSortedAttributes(), $fieldset, array());

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
