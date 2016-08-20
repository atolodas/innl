<?php

class Cafepress_CPWms_Model_Sales_Order_Item extends Mage_Sales_Model_Order_Item
{
   public function getContinuitySkuAndHasContinuity(){
       return (Mage::getModel('cpwms/catalog_product')->load($this->getProductId())->getContinuitySku() && $this->getHasContinuity());
   }
}
