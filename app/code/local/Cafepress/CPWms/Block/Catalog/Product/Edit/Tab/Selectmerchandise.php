<?php

class Cafepress_CPWms_Block_Catalog_Product_Edit_Tab_Selectmerchandise extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Create Product'),
//                    'onclick'   => 'setLocation(\'' .$this->getContinueUrl().'\')',
                    'onclick'   => 'productForm.submit()',
                    'class'     => 'save'
                    ))
                );
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $_SESSION['cafepress_merchandise_collection'] = serialize(Mage::getModel('merchandise/merchandise')->getFormattedData());

        $form = new Varien_Data_Form(array(
            'id' => 'select_merchant_form',
            'action' => $this->getUrl('*/*/continue', array(
                'id' => $this->getRequest()->getParam('id'),
                'token'         => $this->getRequest()->getParam('token'),
                'action'        => 'selectmerchant'
                )),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));
        
        $fieldset = $form->addFieldset('select_merchant', array('legend'=>Mage::helper('cpwms')->__('Select Product Type')));

        $fieldset->addField('product_type', 'hidden', array(
            'label'     => $this->__('Product Type'),
            'name'      => 'product_type',
            'required'  => true
        ));

        $fieldset->addField('merchant_content', 'hidden', array(
            'label'     => $this->__('Merchandise Content'),
            'name'      => 'merchant_content',
            'required'  => true,
            'style'     => 'height:24em; width:50em;',
        ));

        $fieldset->addField('merchant_content_all', 'hidden', array(
            'name'  => 'merchant_content_all',
            'value' => json_encode(Mage::getModel('merchandise/merchandise')->getContentArray())
        ));

//        $fieldset->addType('product_types', 'Cafepress_CPWms_Lib_Varien_Data_Form_Element_ProductTypes');
//        $fieldset->addField('product_types', 'product_types', array(
//            'label' => $this->__('Product Types'),
//            'name' => 'product_types',
//            'required' => false,
//            'style' => 'width:100%;'
//        ));

        $this->setForm($form);
    }

    public function getContinueUrl()
    {
        return $this->getUrl('*/*/continue', array(
            '_current'      => true,
            'id'            => $this->getRequest()->getParam('id'),
            'token'         => $this->getRequest()->getParam('token'),
            'prodtype'      => '{{product_type}}',
            'action'        => 'selectmerchant'
        ));
    }
    
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/continue', array(
            '_current'      => true,
            'id'            => $this->getRequest()->getParam('id'),
            'token'         => $this->getRequest()->getParam('token'),
            'prodtype'      => '{{product_type}}',
            'action'        => 'selectmerchant'
        ));
    }
    
    
//    public function _getProductTypeSelectOptions()
//    {
////        $merchandiseModel = Mage::getModel('cpwms/cafepress_merchandise');
//        $merchandiseCollection = Mage::registry('cafepress_merchandise');;//$merchandiseModel->getMerchandiseCollection();
//
//        $productTypes = array();
//        foreach ($merchandiseCollection as $key => $value) {
//            $productTypes[$value['id']] = $value['name'].' ('.$value['id'].')';
//        }
//        return $productTypes;
//
//    }
//
//    public function getMerchendiseContent()
//    {
////        $merchandiseModel = Mage::getModel('cpwms/cafepress_merchandise');
//        $merchandiseCollection = Mage::registry('cafepress_merchandise');//$merchandiseModel->getMerchandiseCollection();
//
//        $merchantContent = array();
//        foreach ($merchandiseCollection as $merchant){
//            $merchantContent[$merchant['id']] = $merchant['all_block_content'];
//        }
//        return $merchantContent;
//    }
}
