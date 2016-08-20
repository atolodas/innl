<?php

class Cafepress_CPCore_Block_Catalog_Product_Edit_Tab_Token extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
//        $this->setChild('continue_button',
//            $this->getLayout()->createBlock('adminhtml/widget_button')
//                ->setData(array(
//                    'label'     => Mage::helper('catalog')->__('Continue'),
////                    'onclick'   => "setSettings('".$this->getContinueUrl()."','".Mage::registry('current_product_id')."','user_token')",
////                    'onclick'   => 'setLocation(\'' .$this->getContinueUrl().'\')',
//                    'onclick'   => 'productForm.submit()',
//                    'class'     => 'save'
//                    ))
//                );
//        $this->setChild('get_token_button',
//            $this->getLayout()->createBlock('adminhtml/widget_button')
//                ->setData(array(
//                    'label'     => Mage::helper('cpcore')->__('Create User Token'),
//                    'onclick'   => 'setLocation(\'' . $this->getCreateTokenUrl() .'\')',
//                    'class'     => 'add'
//                    ))
//                );
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

        
        $fieldset = $form->addFieldset('token', array('legend'=>Mage::helper('cpcore')->__('Select Image')));

        $product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id'));
//        $fieldset->addField('user_token', 'text', array(
//            'label'     => $this->__('User Token'),
//            'name'      => 'user_token',
//            'value'     => $product->getDesignId(),
//            'note'      => $this->__('A user token expires in 30 minutes, please complete your product within this time'),
//        ));

//        $token = Mage::getModel('cpcore/cafepress_token')->get();

        $fileDisabled = false;
        if($product->getCpDesignId()){
            $fieldset->addType('cafepress_label', 'Cafepress_CPCore_Lib_Varien_Data_Form_Element_CafepressLabel');
            $fieldset->addField('cafepress_label', 'cafepress_label', array(
                'label' => $this->__('Current CP Image'),
                'name' => 'cafepress_label',
                'required' => false,
                'style' => 'width:100%;'
            ));

            $fieldset->addField('leaveOld', 'checkbox', array(
                'label'     => $this->__('Leave Old Image'),
                'name'      => 'leaveOld',
                'onclick'   => 'leaveOldClick(this)',
                'checked'   => $product->getCpDesignId()
            ));
            $fileDisabled = true;
        }

        $fieldset->addField('newCpFile', 'file', array(
            'label'     => $this->__('New CP Image'),
            'name'      => 'newCpFile',
            'disabled'  => $fileDisabled
        ));

//        $userToken = $this->getRequest()->getParam('token');
//        if ($userToken){
//            Mage::register('cafepress_user_token', $userToken);
//            $fieldset->addField('user_token', 'text', array(
//                'label'     => $this->__('User Token'),
//                'name'      => 'user_token',
//                'value'     => $userToken,
//                'note'      => $this->__('A user token expires in 30 minutes, please complete your product within this time'),
//            ));
//
//            $fieldset->addField('continue_button', 'note', array(
//                'text' => $this->getChildHtml('continue_button'),
//            ));
//        } else {
//            $fieldset->addField('get_token_button', 'note', array(
//                'text' => $this->getChildHtml('get_token_button'),
//                'note' => $this->__('A user token expires in 30 minutes, please complete your product within this time'),
//            ));
//        }

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
    
    public function getCreateTokenUrl()
    {
        return $this->getUrl('*/*/createToken',array(
            'id' => $this->getRequest()->getParam('id')
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
