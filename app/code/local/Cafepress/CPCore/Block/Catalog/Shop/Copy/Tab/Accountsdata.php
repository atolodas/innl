<?php

class Cafepress_CPCore_Block_Catalog_Shop_Copy_Tab_Accountsdata extends Mage_Adminhtml_Block_Widget_Form
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

        $fieldset = $form->addFieldset('select_shops', array('legend'=>Mage::helper('cpcore')->__('Select Shops')));

        $fieldset->addField('src_shop_login', 'text', array(
            'label'     => $this->__('Source Shop Login'),
            'name'      => 'src_shop_login',
            'required'  => true,
            'value'     => Mage::getStoreConfig('cafepress_common/partner/email'),
        ));

        $fieldset->addField('src_shop_password', 'text', array(
            'label'     => $this->__('Source Shop Password'),
            'name'      => 'src_shop_password',
            'required'  => true,
            'value'     => Mage::getStoreConfig('cafepress_common/partner/password'),
        ));

        $fieldset->addField('src_shop_apikey', 'text', array(
            'label'     => $this->__('Source Shop ApiKey'),
            'name'      => 'src_shop_apikey',
            'required'  => true,
            'value'     => Mage::getStoreConfig('cafepress_common/partner/apikey'),
        ));

        $fieldset->addField('dst_shop_login', 'text', array(
            'label'     => $this->__('Destination Shop Login'),
            'name'      => 'dst_shop_login',
            'required'  => true,
        ));

        $fieldset->addField('dst_shop_password', 'text', array(
            'label'     => $this->__('Destination Shop Password'),
            'name'      => 'dst_shop_password',
            'required'  => true,
        ));

        $fieldset->addField('dst_shop_apikey', 'text', array(
            'label'     => $this->__('Destination Shop ApiKey'),
            'name'      => 'dst_shop_apikey',
            'required'  => true,
        ));

        $fieldset->addField('dst_shop_partnerid', 'text', array(
            'label'     => $this->__('Destination Shop Partner Id'),
            'name'      => 'dst_shop_partnerid',
            'required'  => true,
        ));

        $this->setForm($form);
    }
}