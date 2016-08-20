<?php

class Cafepress_CPCore_Block_Adminhtml_Xmlformat_Edit_Tabs_Form_Transformer_General extends Cafepress_CPCore_Block_Adminhtml_Xmlformat_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_xmlformat'));
        $fieldset = $form->addFieldset('general', array('legend'=>Mage::helper('cpcore')->__('General')));

        $scheduleSource = Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_schedule')->getAllOptions();
        $statusSource = Mage::getModel('catalog/product_status')->getAllOptions();

        $additionalData = array(
                'name'=>array(),
//                'custom_url'=>array(),
//                'request'=>array('style'=>'width:500px; height:200px;'),
//                'response'=>array('style'=>'width:500px; height:300px;'),
                'status'=>array('source'=>$statusSource),
                'schedule'=>array('source'=>$scheduleSource),
                'condition' => array()
                );

        $fieldset->addField('type', 'hidden', array(
            'name'  => 'xmlformat[type]',
            'value' => Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getIdTypeByName('transformer'),
        ));
        $this->_setFieldset(Mage::getModel('cpcore/xmlformat')->getResource()->loadAllAttributes()->getSortedAttributes(), $fieldset, $additionalData);

$fieldsetPrecondition = $form->addFieldset('precondition_fieldset', array('legend'=>Mage::helper('cpwms')->__('Precondition')));

        $statusSource = Mage::getModel('catalog/product_status')->getAllOptions();

        $additionalData = array(
            'precondition'=>array(),
        );
        $this->_setFieldset(Mage::getModel('cpwms/xmlformat')->getResource()->loadAllAttributes()->getSortedAttributes(), $fieldsetPrecondition, $additionalData);
        $fieldsetPrecondition->addField('note', 'note', array(
          'text'     => 'Attribute_name (ex. "name","attribute_set") + "-" + Sufix (ex. "eq","neq","in") + "-" + Values separated by comma (ex. "1,2,3"). separate several conditions with "+"',
        ));

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
