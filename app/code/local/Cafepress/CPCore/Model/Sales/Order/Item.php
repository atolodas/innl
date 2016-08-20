<?php

class Cafepress_CPCore_Model_Sales_Order_Item extends Mage_Sales_Model_Order_Item
{
   public function getContinuitySkuAndHasContinuity(){
       return (Mage::getModel('cpcore/catalog_product')->load($this->getProductId())->getContinuitySku() && $this->getHasContinuity());
   }
   
   public function hasParent(){
       return (boolean)$this->getParentItem();
   }
   
   public function hasNotParent(){
       return !(boolean)$this->getParentItem();
   }
}
