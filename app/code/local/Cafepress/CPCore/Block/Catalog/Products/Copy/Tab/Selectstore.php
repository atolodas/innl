<?php

class Cafepress_CPCore_Block_Catalog_Products_Copy_Tab_Selectstore extends Mage_Adminhtml_Block_Widget_Form
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

        $fieldset = $form->addFieldset('select_cp_store', array('legend'=>Mage::helper('cpcore')->__('Select Store')));


        $storeList = array();
        foreach(Mage::app()->getStores() as $store){
            $storeList[$store['store_id']] = $store['name'];
        }

        $fieldset->addField('cp_store', 'select', array(
            'label'     => $this->__('Stores'),
            'name'      => 'cp_store',
            'options'    => $storeList,
        ));

        $this->setForm($form);
    }
}