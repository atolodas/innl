<?php

/**
 * Product inventory data
 *
 * @category   BusinessKing
 * @package    BusinessKing_ProductFieldsPermission
 */
class BusinessKing_ProductFieldsPermission_Block_Adminhtml_Catalog_Product_Edit_Tab_Inventory extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('productfieldspermission/catalog/product/edit/tab/inventory.phtml');
    }
    
	public function getPermission()
    {
    	$currentUser = Mage::getSingleton('admin/session')->getUser();
        $currentRole = $currentUser->getRole();
        $roleId = $currentRole->getId();
    	$isReadOnly = Mage::getModel('productfieldspermission/product_fields')->checkReadOnlyField($roleId, 0, 'inventory_tab');
    	if($isReadOnly) {
    		$permission = false;
    	}	    		
    	else {
    		$permission = true;
    	}    	
    	return $permission;
    }
}
