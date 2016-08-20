<?php

class Cafepress_CPCore_Block_Catalog_Shop_Copy_Tab_Copylog extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'shop_copy_form',
            'action' => $this->getUrl('*/*/continue'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('copy_log', array('legend'=>Mage::helper('cpcore')->__('Copy Log')));

        $fieldset->addField('copy_log_list', 'textarea', array(
            'label'     => $this->__('Copy Log'),
            'name'      => 'copy_log_list',
            'style'     => 'width: 150%',
            'value'     => $_SESSION['cp_shop_copy_log']
        ));

        $this->setForm($form);
    }
}