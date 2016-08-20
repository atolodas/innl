<?php

class Cafepress_CPCore_Block_Catalog_Product_Create_Tab_Setparams extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'token_form',
            'action' => $this->getUrl('*/*/continue', array(
                'id'    => $this->getRequest()->getParam('id'),
                'token' => Mage::getModel('cpcore/cafepress_token')->get(),
                'action'=> 'image'
            )),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));
        

        $fieldset = $form->addFieldset('setprint', array('legend'=>Mage::helper('cpcore')->__('Set Product Params')));

        $fieldset->addField('cp_name', 'text', array(
            'label'     => $this->__('Name'),
            'name'      => 'cp_name'
        ));

        $image_location_options = array();
        foreach(Mage::helper('cpcore')->getAttributeOptions('cp_image_location') as $label){
            $image_location_options[$label] = $label;
        }

        $fieldset->addField('cp_image_location', 'select', array(
            'label'     => $this->__('Image Location'),
            'name'      => 'cp_image_location',
            'options'    => $image_location_options
        ));

        $fieldset->addField('cp_sellprice', 'text', array(
            'label'     => $this->__('Sellprice'),
            'name'      => 'cp_sellprice'
        ));

        $fieldset->addField('cp_height', 'text', array(
            'label'     => $this->__('Media Height'),
            'name'      => 'cp_height'
        ));

        $this->setForm($form);
    }

    public function getContinueUrl()
    {
        return $this->getUrl('*/*/continue', array(
            '_current'      => true,
            'id'            => $this->getRequest()->getParam('id'),
            'token'         => $this->getRequest()->getParam('token'),
            'action'        => 'selectmerchant'
        ));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/continue', array(
            '_current'      => true,
            'id'            => $this->getRequest()->getParam('id'),
            'token'        => $this->getRequest()->getParam('token'),
            'action'        => 'savetoken'
        ));
    }
}
