<?php

/**
 * Product Stores(Websites) tab
 *
 * @category   BusinessKing
 * @package    BusinessKing_ProductFieldsPermission
 */
class BusinessKing_ProductFieldsPermission_Block_Adminhtml_Catalog_Product_Edit_Tab_Websites extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Websites
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('productfieldspermission/catalog/product/edit/websites.phtml');
    }
    
    public function getPermission()
    {
    	$currentUser = Mage::getSingleton('admin/session')->getUser();
        $currentRole = $currentUser->getRole();
        $roleId = $currentRole->getId();
    	$isReadOnly = Mage::getModel('productfieldspermission/product_fields')->checkReadOnlyField($roleId, 0, 'websites_tab');
    	if($isReadOnly) {
    		$permission = false;
    	}	    		
    	else {
    		$permission = true;
    	}    	
    	return $permission;
    }
}
