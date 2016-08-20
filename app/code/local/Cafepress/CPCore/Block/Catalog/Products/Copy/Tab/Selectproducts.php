<?php

class Cafepress_CPCore_Block_Catalog_Products_Copy_Tab_Selectproducts extends Mage_Adminhtml_Block_Widget_Form
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

        $fieldset = $form->addFieldset('select_products', array('legend'=>Mage::helper('cpcore')->__('Select Products')));

        $fieldset->addField('attribute_set', 'select', array(
            'label'     => $this->__('Attribute Set'),
            'name'      => 'attribute_set',
            'options'    => Mage::getModel('cpcore/cafepress_product')->getProductAttributeSets(),
        ));

        $fieldset->addField('website', 'select', array(
            'label'     => $this->__('Website'),
            'name'      => 'website',
            'options'    => Mage::getModel('cpcore/cafepress_product')->getWebsites(),
        ));

        $fieldset->addField('simple_to_configurable', 'checkbox', array(
            'label'     => $this->__('Combine Simple Products To Configurables'),
            'name'      => 'simple_to_configurable',
        ));
        $fieldset->addField('just_configurable_to_category', 'checkbox', array(
            'label'     => $this->__('Set Category just for Configurable'),
            'name'      => 'just_configurable_to_category',
            'checked'   => 'checked',
            'note'      => $this->__('If select Configurable & Category, then Category will be set just for configurable product.'),
        ));

        $page = $this->getRequest()->getParam('page');
        if($page){
            $_SESSION['cp_copy_products_page'] = $page;
        } else{
            $_SESSION['cp_copy_products_page'] = 1;
        }

        $fieldset->addType('store_products', 'Cafepress_CPCore_Lib_Varien_Data_Form_Element_StoreProducts');
        $fieldset->addField('store_products', 'store_products', array(
            'label' => $this->__('Store Products'),
            'name' => 'store_products',
            'required' => false,
            'style' => 'width:100%;'
        ));

        $this->setForm($form);
    }
}