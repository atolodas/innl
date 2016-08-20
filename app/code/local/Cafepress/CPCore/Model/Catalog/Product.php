<?php

class Cafepress_CPCore_Model_Catalog_Product extends Mage_Catalog_Model_Product {

    public function getCustomId($type) {
        $result = Mage::getModel('eav/entity_attribute_option')
                        ->getCollection()
                        ->addFieldToFilter('option_id', $this->getData($type))->getData();
        if (isset($result[0])){
            return $result[0]['custom_id'];
        }
        return false;
    }

    public function attributeIs($attr, $val) {
        if ($this->getData($attr) == $val || $this->getAttributeText($attr) == $val) {
            return true;
        }
        return false;
    }

    public function attributeIsNot($attr, $val) {
        if ($this->getData($attr) != $val && $this->getAttributeText($attr) != $val) {
            return true;
        }
        return false;
    }

}

?>
