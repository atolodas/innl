<?php

class Cafepress_CPCore_Block_Catalog_Product_Edit_Tab_Productcreated extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('OK'),
//                    'onclick'   => 'productForm.submit()',
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
                    'class'     => 'save'
                    ))
                );
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Back'),
                    'onclick'   => 'productForm.submit()',
                    'class'     => 'back'
                    ))
                );
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $product = $this->getProduct();
        $form = new Varien_Data_Form();
        
        $fieldset = $form->addFieldset('create_product', array('legend'=>Mage::helper('cpcore')->__('Information for the newly created product')));

        $fieldset->addField('created_product_id', 'text', array(
            'label'     => $this->__('Product ID'),
            'name'      => 'created_product_id',
            'style'     => 'width:50em;',
            'value'     => $product->getCpSaveProductId(),
        ));
        
        $fieldset->addField('created_product_xml', 'textarea', array(
            'label'     => $this->__('Cafepress Return XML'),
            'name'      => 'created_product_xml',
            'style'     => 'height:24em; width:50em;',
            'value'     => $product->getCpSaveProductXml(),
        ));

//        $fieldset->addField('back_button', 'note', array(
//            'text' => $this->getChildHtml('back_button').' '.$this->getChildHtml('continue_button'),
//        ));
//        $fieldset->addField('continue_button', 'note', array(
//            'text' => $this->getChildHtml('continue_button'),
//        ));
        

        $this->setForm($form);
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/continue', array(
            '_current'      => true,
            'id'            => $this->getRequest()->getParam('id'),
//            'token'         => $this->getRequest()->getParam('token'),
            'action'        => 'selectmerchantback'
        ));
    }
    
    public function getProduct()
    {
        $product = Mage::registry('product');
        if (!$product){
            $productId = $this->getRequest()->getParam('id');
            $product = Mage::getModel('catalog/product')
                ->setStoreId($this->getRequest()->getParam('store', 0))->load($productId);
        
            Mage::register('product', $product);
        } 
        
        return $product;
    }
}
