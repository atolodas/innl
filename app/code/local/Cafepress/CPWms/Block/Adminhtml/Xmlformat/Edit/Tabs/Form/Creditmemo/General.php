<?php

class Cafepress_CPWms_Block_Adminhtml_Xmlformat_Edit_Tabs_Form_Creditmemo_General extends Cafepress_CPWms_Block_Adminhtml_Xmlformat_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_xmlformat'));
        $fieldset = $form->addFieldset('general', array('legend'=>Mage::helper('cpwms')->__('General')));

        $statusSource = Mage::getModel('catalog/product_status')->getAllOptions();

        $additionalData = array(
            'name'=>array(),
            'filename_out'=>array(),
            'status'=>array('source'=>$statusSource),
            'condition'=>array()
        );
        $fieldset->addField('type', 'hidden', array(
            'name'  => 'xmlformat[type]',
            'value' => Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_type')->getIdTypeByName('creditmemo')
        ));

        $this->_setFieldset(Mage::getModel('cpwms/xmlformat')->getResource()->loadAllAttributes()->getSortedAttributes(), $fieldset, $additionalData);

        $fieldsetPrecondition = $form->addFieldset('precondition_fieldset', array('legend'=>Mage::helper('cpwms')->__('Precondition')));

        $statusSource = Mage::getModel('catalog/product_status')->getAllOptions();

        $additionalData = array(
            'precondition'=>array(),
        );
        $this->_setFieldset(Mage::getModel('cpwms/xmlformat')->getResource()->loadAllAttributes()->getSortedAttributes(), $fieldsetPrecondition, $additionalData);

        $formatId = Mage::registry('current_xmlformat')->getId();
        $fieldsetPrecondition->addType('orders_grid', 'Cafepress_CPWms_Lib_Varien_Data_Form_Element_OrdersGrid');
        $fieldsetPrecondition->addField('orders_grid', 'orders_grid', array(
            'label' => 'Orders Collection',
            'xmlformat_id' => $formatId,
            'name' => 'orders_grid',
            'required' => false,
            'style' => 'width:100%;'
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
