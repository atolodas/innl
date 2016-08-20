<?php

class Cafepress_CPCore_Block_Catalog_Product_Edit_Tab_Createproduct extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Create And Save Product'),
                    'onclick'   => 'productForm.submit()',
                    'class'     => 'save'
                    ))
                );
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $product = $this->getProduct();
        $form = new Varien_Data_Form(array(
            'id' => 'create_product_form',
            'action' => $this->getUrl('*/*/continue', array(
                'id'    => $this->getRequest()->getParam('id'),
//                'token' => $this->getRequest()->getParam('token'),
                'action'=> 'createproduct'
                )),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        
        $fieldset = $form->addFieldset('create_product', array('legend'=>Mage::helper('cpcore')->__('Create and Save Cafepress Product')));

        $createProduct = array();
        $createProductFields = Mage::helper('cpcore')->getProductCreateAttributes();
        $createProductMediaFields = Mage::helper('cpcore')->getCreateProductMediaConfigurationAttributes();
        $productData = Mage::helper('cpcore')->getProductDataFromXml($product->getCpCreateProductXml());
        
        foreach($createProductFields as $key=>$field){
            if(!isset($field['label'])){
                $createProduct['product_'.$key]['label']    = $key;
            } else{
                $createProduct['product_'.$key]['label']    = $field['label'];
            }
            if(!isset($field['name'])){
                $createProduct['product_'.$key]['name']     = 'create_product_xml[product]['.$key.']';
            }else {
                $createProduct['product_'.$key]['name']     = 'create_product_xml[product]['.$field['name'].']';;
            }
            
            $createProduct['product_'.$key]['style']    = $field['style'];
            $createProduct['product_'.$key]['editable']  = $field['editable'];
            if($field['editable']){
                $createProduct['product_'.$key]['value']    = $productData['product'][$key];
                $createProduct['product_'.$key]['type']     = $field['type'];
            } else {
                $createProduct['product_'.$key]['value']    = $field['default'];
                $createProduct['product_'.$key]['type']     = 'hidden';
            }
            unset($productData['product'][$key]);
        }
        foreach($createProductMediaFields as $key=>$field){
            if(!isset($field['label'])){
                $createProduct['product_media_'.$key]['label']    = 'MediaConfiguration: '.$key;
            } else{
                $createProduct['product_media_'.$key]['label']    = $field['label'];
            }
            if(!isset($field['name'])){
                $createProduct['product_media_'.$key]['name']     = 'create_product_xml[product_media]['.$key.']';
            }else {
                $createProduct['product_media_'.$key]['name']     = 'create_product_xml[product_media]['.$field['name'].']';;
            }
            
            $createProduct['product_media_'.$key]['style']  = $field['style'];
            $createProduct['product_media_'.$key]['editable']  = $field['editable'];
            if($field['editable']){
                $createProduct['product_media_'.$key]['value']    = $productData['mediaConfiguration'][$key];
                $createProduct['product_media_'.$key]['type']     = $field['type'];
            } else {
                $createProduct['product_media_'.$key]['value']    = $field['default'];
                $createProduct['product_media_'.$key]['type']     = 'hidden';
            }
            unset($productData['mediaConfiguration'][$key]);
        }
//        Zend_Debug::dump($productData['mediaConfiguration']);
//        Zend_Debug::dump($createProduct);die();

//        $fieldset->addField('create_product_xml1', 'textarea', array(
//            'label'     => $this->__('Create Product XML'),
//            'name'      => 'create_product_xml1',
//            'style'     => 'height:24em; width:50em;',
//            'value'     => $product->getCpCreateProductXml(),
//        ));
        
        foreach ($createProduct as $key => $value) {
            $fieldset->addField('create_product_data'.$key, $value['type'], array(
                'label'     => $this->__($value['label']),
                'name'      => $value['name'],
                'style'     => $value['style'],
                'value'     => $value['value'],
            ));
        }
        foreach ($productData['product'] as $key => $value) {
            $fieldset->addField('create_product_data[product]['.$key.']', 'hidden', array(
                'name'      => 'create_product_data[product_media]['.$key.']',
                'value'     => $value['value'],
            ));
        }
        foreach ($productData['mediaConfiguration'] as $key => $value) {
            $fieldset->addField('create_product_data[product_media]['.$key.']', 'hidden', array(
                'name'      => 'create_product_data[product_media]['.$key.']',
                'value'     => $value['value'],
            ));
        }

//        $fieldset->addField('continue_button', 'note', array(
//            'text' => $this->getChildHtml('continue_button'),
//        ));
        
        $productInfo = $form->addFieldset('product_info', array('legend'=>Mage::helper('cpcore')->__('Product Info')));

        $productInfo->addField('name', 'text', array(
            'label'     => $this->__('Name'),
            'name'      => 'product_name',
            'value'     => $product->getName(),
        ));
        
        $productInfo->addField('price', 'text', array(
            'label'     => $this->__('Price'),
            'name'      => 'product_pice',
            'value'     => $product->getPrice(),
        ));
        $productInfo->addField('description', 'textarea', array(
            'label'     => $this->__('Description'),
            'name'      => 'product_description',
            'value'     => $product->getDescription(),
            'style'     => 'height:10em; width:50em;',
        ));
        
        $productInfo->addField('media_configuration', 'label', array(
            'label'     => $this->__('Media Configuration'),
            'name'      => 'product_media_configuration',
            'value'     => '<mediaConfiguration height="'.$product->getCpMediaHeight().'" name="FrontCenter" designId="'.$product->getCpDesignId().'"/>',
            'style'     => 'width:50em;',
        ));
        
        $productInfo->addField('cp_design_id', 'text', array(
            'label'     => $this->__('CP: Design Id'),
            'value'     => $product->getCpDesignId(),
        ));
        $productInfo->addField('cp_sell_prise', 'text', array(
            'label'     => $this->__('CP: Sell Price'),
            'value'     => $product->getCpSellprice(),
        ));
        $productInfo->addField('cp_ptn', 'text', array(
            'label'     => $this->__('CP: Merchandise Id (PTN)'),
            'value'     => $product->getCpPtn(),
        ));
        $productInfo->addField('cp_media_height', 'text', array(
            'label'     => $this->__('CP: Media Height'),
            'value'     => $product->getCpMediaHeight(),
        ));
        $productInfo->addField('cp_label', 'text', array(
            'label'     => $this->__('CP: Label'),
            'value'     => $product->getCpLabel(),
        ));
        
        $this->setForm($form);
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/continue', array(
            '_current'      => true,
            'id'            => $this->getRequest()->getParam('id'),
//            'token'         => $this->getRequest()->getParam('token'),
            'action'        => 'createproduct'
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
