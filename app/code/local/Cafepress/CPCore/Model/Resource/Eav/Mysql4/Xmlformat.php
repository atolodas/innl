<?php

class Cafepress_CPCore_Model_Resource_Eav_Mysql4_Xmlformat extends Cafepress_CPCore_Model_Resource_Eav_Mysql4_Abstract//Mage_Core_Model_Abstract*/Mage_Eav_Model_Entity_Abstract
{   
    
    protected $_storeId = 0;
    
    
    public function __construct()
    {
        parent::_construct();
        $resource = Mage::getSingleton('core/resource');
        $this->setType('cpcore_xmlformat');
        $this->setConnection(
            $resource->getConnection('cpcore_read'),
            $resource->getConnection('cpcore_write')
        );
    }
    
    /**
     * Default file attributes
     *
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        return array('entity_id', 'entity_type_id', 'attribute_set_id', 'created_at', 'updated_at');
    }
    
 	/**
    * Get default attribute source model
    *
    * @return string
    */
    public function getDefaultAttributeSourceModel()
    {
        return 'eav/entity_attribute_source_table';
    }

    protected function _afterSave(Varien_Object $file)
    {
        parent::_afterSave($file);
        return $this;
    }

    public function setStoreId($storeId)
    {
    	$this->_storeId = $storeId;
        return $this;
    }

    /**
     * Return store id
     *
     * @return integer
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
        	return Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }
    
    public function loadAttributes() { 
        return $this->_attributesByCode;
    }

}
