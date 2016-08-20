<?php

class Cafepress_CPCore_Block_Catalog_Products_Copy_Tab_Productscopied extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'products_copy_form',
            'action' => $this->getUrl('*/*/continue'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));


        $fieldset = $form->addFieldset('products_copied', array('legend'=>Mage::helper('cpcore')->__('Products Copied')));

        $fieldset->addField('cp_products_copied_result', 'textarea', array(
            'label'     => $this->__('Result Log'),
            'name'      => 'cp_products_copied_result',
            'value'     => $_SESSION['cp_copy_log']
        ));

        $this->setForm($form);
    }
}