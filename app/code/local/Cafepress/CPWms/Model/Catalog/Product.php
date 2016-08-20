<?php

class Cafepress_CPWms_Model_Catalog_Product extends Mage_Catalog_Model_Product
{
    public function getCustomId($type)
    {
        $result = Mage::getModel('eav/entity_attribute_option')
                ->getCollection()
                ->addFieldToFilter('option_id', $this->getData($type))->getData();
    //    Zend_Debug::dump($result[0]['custom_id']);
        return $result[0]['custom_id'];
    }
    
   public function attributeIs($attr,$val) { 
       if($this->getData($attr) == $val || $this->getAttributeText($attr)==$val) { 
           return true; 
           } 
           return false;
       }
       
       
   public function attributeIsNot($attr,$val) { 
       if($this->getData($attr) != $val && $this->getAttributeText($attr)!=$val) { 
           return true; 
           } 
           return false;
       }
}

?>
