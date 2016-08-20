<?php 

/**
 * Product fields content block
 *
 * @category   BusinessKing
 * @package    BusinessKing_ProductFieldsPermission
 */
class BusinessKing_ProductFieldsPermission_Block_Adminhtml_Product_Fields extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
    	$this->setTemplate('productfieldspermission/product/fields.phtml');        
    }
    
	/**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }
    
    public function getRoles()
    {
    	return Mage::getModel("admin/roles")->getCollection();
    }

    public function getGroups()
    {
    	$groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                				->load();               				
        
        return $groupCollection;  
    }
    
    public function getProductsAttributes()
    {
    	$allAttributes = array();
    	$i = 0;
    	/*$groups = $this->getGroups(); 

    	$productSimple = Mage::getModel('catalog/product')
        					->setTypeId('simple')
        					->setAttributeSetId(4);
        $productGrouped = Mage::getModel('catalog/product')
        					->setTypeId('grouped')
        					->setAttributeSetId(4);
        $productConfigurable = Mage::getModel('catalog/product')
        					->setTypeId('configurable')
        					->setAttributeSetId(4);
        $productVirtual = Mage::getModel('catalog/product')
        					->setTypeId('virtual')
        					->setAttributeSetId(4);
        $productBundle = Mage::getModel('catalog/product')
        					->setTypeId('bundle')
        					->setAttributeSetId(4);
        $productDownloadable = Mage::getModel('catalog/product')
        					->setTypeId('downloadable')
        					->setAttributeSetId(4);																									
        			
    	if (count($groups) > 0) {
    		foreach ($groups as $group) {
    			$attributes = $productSimple->getAttributes($group->getId(), true);
        		foreach ($attributes as $attribute) {
        			if (!$attribute || !$attribute->getIsVisible()) {
                		continue;
            		}
            		$allAttributes[$i++] = $attribute;
        		}
    			$attributes = $productGrouped->getAttributes($group->getId(), true);
        		foreach ($attributes as $attribute) {
        			if (!$attribute || !$attribute->getIsVisible()) {
                		continue;
            		}
            		if (!in_array($attribute, $allAttributes)) {
            			$allAttributes[$i++] = $attribute;
            		}	
        		}
    			$attributes = $productConfigurable->getAttributes($group->getId(), true);
        		foreach ($attributes as $attribute) {
        			if (!$attribute || !$attribute->getIsVisible()) {
                		continue;
            		}
            		if (!in_array($attribute, $allAttributes)) {
            			$allAttributes[$i++] = $attribute;
            		}	
        		}
    			$attributes = $productVirtual->getAttributes($group->getId(), true);
        		foreach ($attributes as $attribute) {
        			if (!$attribute || !$attribute->getIsVisible()) {
                		continue;
            		}
            		if (!in_array($attribute, $allAttributes)) {
            			$allAttributes[$i++] = $attribute;
            		}	
        		}
    			$attributes = $productBundle->getAttributes($group->getId(), true);
        		foreach ($attributes as $attribute) {
        			if (!$attribute || !$attribute->getIsVisible()) {
                		continue;
            		}
            		if (!in_array($attribute, $allAttributes)) {
            			$allAttributes[$i++] = $attribute;
            		}	
        		}
    			$attributes = $productDownloadable->getAttributes($group->getId(), true);
        		foreach ($attributes as $attribute) {
        			if (!$attribute || !$attribute->getIsVisible()) {
                		continue;
            		}
            		if (!in_array($attribute, $allAttributes)) {
            			$allAttributes[$i++] = $attribute;
            		}	
        		}
    		}
    	}
    	return $allAttributes;*/
    	
    	$entityType = Mage::getModel('eav/entity_type')->loadByCode('catalog_product');
    	$entityTypeId = $entityType->getId();
    	
    	$attributes = Mage::getResourceModel('eav/entity_attribute_collection')
    							->setAttributeSetFilter($entityTypeId)
    							->addFieldToFilter('attribute_code', array('neq'=>'thumbnail'))
    							->addFieldToFilter('attribute_code', array('neq'=>'image'))
    							->addFieldToFilter('attribute_code', array('neq'=>'small_image'))
    							->addFieldToFilter('attribute_code', array('neq'=>'image_label'))
    							->addFieldToFilter('attribute_code', array('neq'=>'small_image_label'))
    							->addFieldToFilter('attribute_code', array('neq'=>'gallery'))
    							->load();        					      				 
    	foreach ($attributes as $attribute) {
           if (!$attribute) { //|| !$attribute->getIsVisible()
               continue;
           }
           $allAttributes[$i++] = $attribute;
        }   
        return $allAttributes;		      				
    }
}    