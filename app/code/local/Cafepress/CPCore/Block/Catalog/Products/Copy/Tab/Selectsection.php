<?php

class Cafepress_CPCore_Block_Catalog_Products_Copy_Tab_Selectsection extends Mage_Adminhtml_Block_Widget_Form
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

        $fieldset = $form->addFieldset('select_section', array('legend'=>Mage::helper('cpcore')->__('Select Section')));

        $fieldset->addField('section', 'select', array(
            'label'     => $this->__('Section'),
            'name'      => 'section',
            'options'    => Mage::getModel('cpcore/cafepress_sections')->getSectionsList(),
        ));

        $this->setForm($form);
    }
}