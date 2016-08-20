<?php

/**
 * ProductFieldsPermission Adminhtml tier pricing item renderer
 *
 * @category   BusinessKing
 * @package    BusinessKing_ProductFieldsPermission
 */
class BusinessKing_ProductFieldsPermission_Block_Adminhtml_Catalog_Product_Edit_Tab_Price_Tier extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
{
    public function __construct()
    {
        $this->setTemplate('productfieldspermission/catalog/product/edit/price/tier.phtml');
    }
    
	protected function _prepareLayout()
    {
    	parent::_prepareLayout();
    	$isReadOnly = $this->isReadOnly();
    	if ($isReadOnly) {
    		$onclick = '';
    	}
    	else {
    		$onclick = 'tierPriceControl.addItem()';    		    		
    	}
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Add Tier'),
                    'onclick'   => $onclick,
                    'class' => 'add'
                )));
        return $this;
    }
    
    public function getPermission()
    {
    	$isReadOnly = $this->isReadOnly();                
    	if($isReadOnly) {
    		$permission = false;
    	}	    		
    	else {
    		$permission = true;
    	}    	
    	return $permission;
    }
    
    public function isReadOnly()
    {
    	$currentUser = Mage::getSingleton('admin/session')->getUser();
        $currentRole = $currentUser->getRole();
        $roleId = $currentRole->getId();
        
        $attributeId = Mage::getResourceModel('eav/entity_attribute')
    							->getIdByCode('catalog_product', 'tier_price');
                				
        $isReadOnly = Mage::getModel('productfieldspermission/product_fields')->checkReadOnlyField($roleId, $attributeId);
        
        return $isReadOnly;
    }
}