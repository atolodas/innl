<?php

class Cafepress_CPCore_Model_Product extends Mage_Core_Model_Abstract {

    protected $_currentProduct      = false;
    
    
    public function getDefaultProductAttributeSetId(){
        if (!$this->productAttributeId){
            $this->productAttributeId = Mage::getModel('catalog/product')->getResource()->getEntityType()->getDefaultAttributeSetId();
        }
        return $this->productAttributeId;
    }
    
    public function getSkuForNewProduct($name,$prefix='cafepress-empty-',$increment=true){
        $newName = $prefix.strtolower(preg_replace('/[^a-zA-Z0-9-]+/iu', '', str_replace(" ", "-", $name)));
        if ($increment){
            $newName = $this->incrementName($newName);
        }
        return $newName;
    }
    
    protected function incrementName($nameStart,$selecter='-',$changeName=false,$inc=1){
        if (!$changeName){
            $changeName = $nameStart;
        }
        if(Mage::getModel('catalog/product')->loadByAttribute('sku',$changeName)){
            $changeName = $this->incrementName($nameStart,$selecter,$nameStart.$selecter.$inc,++$inc);
        } 
        return $changeName;
    }
    
    public function getEmptyProductCollection(){
        $productCollection = Mage::getResourceModel('catalog/product_collection');
        $productCollection->addAttributeToFilter('created_at', array('to' => '1000-09-10'));;
        return $productCollection;
    }
    
    
    public function getModifiedPrduct($productId,$data){
        $produt = Mage::getModel('catalog/product')->load($productId);
        foreach ($data as $key => $value) {
            $produt->setData($key,$value);
        }
        $this->_currentProduct = $produt;
        return $produt;
    }
    
}