<?php

class Cafepress_CPWms_Model_Resource_Eav_Mysql4_Xmlformat_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Collection_Abstract
{
    
    protected $_defaultItems;
    
    /**
     * Init resource model
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('cpwms/xmlformat');
    }

    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        parent::load($printQuery, $logQuery);

        return $this;
    }

    protected function _initSelect()
    {
        $this->getSelect()->from(array('e'=>$this->getEntity()->getEntityTable()));
        return $this;
    }

    public function addIdFilter($id, $exclude = false)
    {
        if (empty($id)) {
            $this->_setIsLoaded(true);
            return $this;
        }
        if (is_array($id)) {
            if (!empty($id)) {
                if ($exclude) {
                    $condition = array('nin'=>$id);
                } else {
                    $condition = array('in'=>$id);
                }
            }
            else {
                $condition = '';
            }
        }
        else {
            if ($exclude) {
                $condition = array('neq'=>$id);
            } else {
                $condition = $id;
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }

    public function addStoreFilter($store=null)
    {
        if (is_null($store)) {
            $store = $this->getStoreId();
        }
        $store = Mage::app()->getStore($store);

        if (!$store->isAdmin()) {
            $this->setStoreId($store);
        }

        return $this;
    }

    /**
     * Retrieve max value by attribute
     *
     * @param   string $attribute
     * @return  mixed
     */
    public function getMaxAttributeValue($attribute)
    {
        $select     = clone $this->getSelect();
        $attribute  = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias = $attributeCode.'_max_value';

        $condition  = 'e.entity_id='.$tableAlias.'.entity_id
            AND '.$this->_getConditionSql($tableAlias.'.attribute_id', $attribute->getId())
            ;

        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array('max_'.$attributeCode=>new Zend_Db_Expr('MAX('.$tableAlias.'.value)'))
            )
            ->group('e.entity_type_id');

        $data = $this->getConnection()->fetchRow($select);
        if (isset($data['max_'.$attributeCode])) {
            return $data['max_'.$attributeCode];
        }
        return null;
    }

    /**
     * Retrieve ranging files count for arrtibute range
     *
     * @param   string $attribute
     * @param   int $range
     * @return  array
     */
    public function getAttributeValueCountByRange($attribute, $range)
    {
        $select     = clone $this->getSelect();
        $attribute  = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias = $attributeCode.'_range_count_value';

        $condition  = 'e.entity_id='.$tableAlias.'.entity_id
            AND '.$this->_getConditionSql($tableAlias.'.attribute_id', $attribute->getId())
            ;

        $select->reset(Zend_Db_Select::GROUP);
        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array(
                        'count_'.$attributeCode=>new Zend_Db_Expr('COUNT(DISTINCT e.entity_id)'),
                        'range_'.$attributeCode=>new Zend_Db_Expr('CEIL(('.$tableAlias.'.value+0.01)/'.$range.')')
                     )
            )
            ->group('range_'.$attributeCode);

        $data   = $this->getConnection()->fetchAll($select);
        $res    = array();

        foreach ($data as $row) {
            $res[$row['range_'.$attributeCode]] = $row['count_'.$attributeCode];
        }
        return $res;
    }

    /**
     * Retrieve files count by some value of attribute
     *
     * @param   string $attribute
     * @return  array($value=>$count)
     */
    public function getAttributeValueCount($attribute)
    {
        $select     = clone $this->getSelect();
        $attribute  = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias = $attributeCode.'_value_count';

        $select->reset(Zend_Db_Select::GROUP);
        $condition  = 'e.entity_id='.$tableAlias.'.entity_id
            AND '.$this->_getConditionSql($tableAlias.'.attribute_id', $attribute->getId())
            ;

        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array(
                        'count_'.$attributeCode=>new Zend_Db_Expr('COUNT(DISTINCT e.entity_id)'),
                        'value_'.$attributeCode=>new Zend_Db_Expr($tableAlias.'.value')
                     )
            )
            ->group('value_'.$attributeCode);

        $data   = $this->getConnection()->fetchAll($select);
        $res    = array();

        foreach ($data as $row) {
            $res[$row['value_'.$attributeCode]] = $row['count_'.$attributeCode];
        }
        return $res;
    }

    /**
     * Return all attribute values as array in form:
     * array(
     *   [entity_id_1] => array(
     *          [store_id_1] => store_value_1,
     *          [store_id_2] => store_value_2,
     *          ...
     *          [store_id_n] => store_value_n
     *   ),
     *   ...
     * )
     *
     * @param string $attribute attribute code
     * @return array
     */
    public function getAllAttributeValues($attribute)
    {
        /** @var Zend_Db_Select */
    	$select    = clone $this->getSelect();
        $attribute = $this->getEntity()->getAttribute($attribute);

        $select->reset()
            ->from($attribute->getBackend()->getTable(), array('entity_id', 'store_id', 'value'))
            ->where('attribute_id = ?', $attribute->getId(), Zend_Db::INT_TYPE);

        $data = $this->getConnection()->fetchAll($select);
        $res  = array();

        foreach ($data as $row) {
            $res[$row['entity_id']][$row['store_id']] = $row['value'];
        }

        return $res;
    }

    /**
     * Retrive all ids for collection
     *
     * @return array
     */
    public function getAllIds($limit=null, $offset=null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->columns('e.'.$this->getEntity()->getIdFieldName());
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

    public function addAttributeToSort($attribute, $dir='asc')
    {
        $storeId = Mage::app()->getStore()->getId();

        $attrInstance = $this->getEntity()->getAttribute($attribute);
            if ($attrInstance && $attrInstance->usesSource()) {
                $attrInstance->getSource()
                    ->addValueSortToCollection($this, $dir);
                return $this;
            }
        return parent::addAttributeToSort($attribute, $dir);
    }

    public function mergeCollections($collections = array(), $storeId = 1)
    {
        foreach($collections as $collect2){
            foreach($collect2 as $item)
            {
                $this->addItemm($item, $storeId);
            } 
        }
        return $this;
    }


    /**
     * Rewrite method from parent class
     * @param Varien_Object $object
     * @param type $storeId
     * @return type 
     */
    public function addItemm(Varien_Object $object, $storeId)
    {
        if (get_class($object) !== $this->_itemObjectClass) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Attempt to add an invalid object'));
        }
        return $this->addItemmm($object, $storeId);
    }

    public function addItemmm(Varien_Object $item, $storeId)
    {
        $itemId = $this->_getItemId($item);

        if (!is_null($itemId)) {
            $itemId = $itemId+(100000*$storeId);
            if (isset($this->_items[$itemId])) {
//                throw new Exception('Item ('.get_class($item).') with the same id "'.$item->getId().'" already exist');
            }
            
            if (!$this->compareItems($item)){
                $this->_items[$itemId] = $item
//                    ->setEntityId($itemId)
//                    ->setId($itemId)
                    ->setData('id', $itemId)
                    ->setData('store_id', $storeId)
//                    ->setData('entity_id', $itemId)
//                    ->setData('request', '')
//                    ->setData('response', '')
                    ;
                
            }
            
            
        } else {
            $this->_items[] = $item;
        }
        return $this;
    }

    public function _setDefaultItems()
    {
        foreach ($this->getItems() as $item){
            $this->_defaultItems[$item->getId()] = $item->getData();
        }
        return $this;
    }
    
    /**
     * Compare value from defauly Item and This Item
     * @param type $item
     * @return type 
     */
    protected function compareItems($item){
        if(isset($this->_defaultItems[$item->getId()])){
            $defaultData = $this->_defaultItems[$item->getId()];
            $itemData = $item->getData();
            try {
            foreach($itemData as $key=>$val){
                if ($val!=$defaultData[$key]){
                    return false;
                }
            }
            }catch (Exception $e){
                return false;
            }
        } else {
            return false;
        }
        return true;
    }
    
    public function addFilterIfNotDeveloper()
    {
        if (!Mage::helper('cpwms')->isDeveloper()){
            //Filter By Type Format
            $devTypeIds = Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_type')->getDeveloperTypeIds();
            $this->addAttributeToFilter('type', array('nin' => $devTypeIds));
        }
    }
    
}