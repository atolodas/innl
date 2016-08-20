<?php

class BusinessKing_ProductFieldsPermission_Model_Product_Fields extends Mage_Core_Model_Abstract
{
	protected function _construct()
    {
        $this->_init('productfieldspermission/product_fields');
    }
    
    public function setReadOnlyFields($data)
    {
    	Mage::getResourceModel('productfieldspermission/product_fields')->setReadOnlyFields($data);
    }
    
    public function removeReadOnlyFields($roleId)
    {
    	Mage::getResourceModel('productfieldspermission/product_fields')->removeReadOnlyFields($roleId);
    }
    
    public function getAttributes($roleId)
    {
    	return Mage::getResourceModel('productfieldspermission/product_fields')->getAttributes($roleId);
    }
    
    public function checkReadOnlyField($roleId, $attributeId, $tabName = '')
    {
    	return Mage::getResourceModel('productfieldspermission/product_fields')->checkReadOnlyField($roleId, $attributeId, $tabName);
    }
}    