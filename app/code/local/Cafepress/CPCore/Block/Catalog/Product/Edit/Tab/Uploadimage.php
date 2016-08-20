<?php

class Cafepress_CPCore_Block_Catalog_Product_Edit_Tab_Uploadimage extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
    }
}
