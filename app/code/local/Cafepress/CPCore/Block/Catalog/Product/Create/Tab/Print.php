<?php

class Cafepress_CPCore_Block_Catalog_Product_Create_Tab_Print extends Mage_Adminhtml_Block_Widget_Form
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

        
        $fieldset = $form->addFieldset('setprint', array('legend'=>Mage::helper('cpcore')->__('Select CafePress Print')));

        $fieldset->addField('set_new_print', 'checkbox', array(
            'label'     => $this->__('Set New Print'),
            'name'      => 'set_new_print',
            'onclick'   => 'setNewPrint(this)',
            'checked'   => false
        ));

        $fieldset->addField('new_print', 'file', array(
            'label'     => $this->__('New CafePress Print'),
            'name'      => 'new_print',
            'disabled'  => true
        ));

        $fieldset->addField('selected_print', 'hidden', array(
            'label'     => $this->__('Selected Print'),
            'name'      => 'selected_print',
        ));

        $fieldset->addType('existing_prints', 'Cafepress_CPCore_Lib_Varien_Data_Form_Element_CPPrints');
        $fieldset->addField('existing_prints', 'existing_prints', array(
            'label' => $this->__('Existing CafePress Prints'),
            'name' => 'existing_prints',
            'required' => false,
            'style' => 'width:100%;'
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
