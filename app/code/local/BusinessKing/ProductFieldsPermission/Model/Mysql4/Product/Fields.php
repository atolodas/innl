<?php

class BusinessKing_ProductFieldsPermission_Model_Mysql4_Product_fields extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
    {
        $this->_init('eav/attribute', 'attribute_id');        
    }
    
    public function setReadOnlyFields($data)
    {
    	$write = $this->_getWriteAdapter();    	    	
    	$write->insert($this->getTable('productfieldspermission/role_attributes'), $data);
    }
    
    public function removeReadOnlyFields($roleId)
    {
    	$write = $this->_getWriteAdapter(); 
    	$write->delete($this->getTable('productfieldspermission/role_attributes'), 'role_id = '.$roleId);
    }
    
    public function getAttributes($roleId)
    {
    	$read = $this->_getReadAdapter();
    	$select = $read->select()
            ->from($this->getTable('productfieldspermission/role_attributes'))
            ->where('role_id = ?', $roleId);

    	return $read->fetchAll($select);
    }
    
    public function checkReadOnlyField($roleId, $attributeId, $tabName = '')
    {
    	$read = $this->_getReadAdapter();
    	$condition = "role_id = ".$roleId." AND attribute_id = ".$attributeId;
    	if (!empty($tabName)) {
    		$condition .= " AND tab_name = '".$tabName."'";
    	}
    	$select = $read->select()
    		->from($this->getTable('productfieldspermission/role_attributes'), 'role_id')
    		->where($condition);
    		
    	return $read->fetchOne($select);	
    }
}    