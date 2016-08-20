<?php

class Cafepress_CPWms_Block_Adminhtml_Xmlformat_Edit_Tabs_Form_Order_Condition_Check extends Cafepress_CPWms_Block_Adminhtml_Xmlformat_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_xmlformat'));
        $fieldset = $form->addFieldset('condition_check', array('legend'=>Mage::helper('cpwms')->__('Condition Check')));

        $fieldset->addType('conditions_table', 'Cafepress_CPWms_Lib_Varien_Data_Form_Element_ConditionsTable');
        $fieldset->addField('conditions_table', 'conditions_table', array(
            'label' => 'Conditions Table',
            'name' => 'conditions_table',
            'required' => false,
            'style' => 'width:100%;'
        ));

        $this->setForm($form);
    }
    
    public function test1(){
        return 'asd';
    }
}
