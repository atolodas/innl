<?php 

/**
 * Limit Pricing ajax controller
 *
 * @category   BusinessKing
 * @package    BusinessKing_ProductFieldsPermission
 */
class BusinessKing_ProductFieldsPermission_Adminhtml_AjaxController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$roleId = $this->getRequest()->getPost('role');
		$allAttributes = array('inventory_tab' => false, 'websites_tab' => false);
		if (empty($roleId)) {
			$roleAttributes = array();
		}
		else {
			$attributes = Mage::getModel('productfieldspermission/product_fields')->getAttributes($roleId);
			$roleAttributes = array();
			if (count($attributes) > 0) {
				foreach ($attributes as $attribute) {
					if ($attribute['tab_name']=="inventory_tab") {
						$allAttributes['inventory_tab'] = true;
					}
					elseif ($attribute['tab_name']=="websites_tab") {
						$allAttributes['websites_tab'] = true;
					}
					else {
						$roleAttributes[] = $attribute['attribute_id'];
					}	
				}
			}
		}		
		$i = 0;				
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
           $attributeId = $attribute->getId();
           if (in_array($attributeId, $roleAttributes)) {
           	   $allAttributes[$i++] = true;
           }	
           else {
           	   $allAttributes[$i++] = false;	
           }
        }
        $this->getResponse()->setBody(Zend_Json::encode($allAttributes));
	}	
}