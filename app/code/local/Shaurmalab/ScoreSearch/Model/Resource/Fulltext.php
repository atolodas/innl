<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Shaurmalab_ScoreSearch
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * ScoreSearch Fulltext Index resource model
 *
 * @category    Mage
 * @package     Shaurmalab_ScoreSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_ScoreSearch_Model_Resource_Fulltext extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Searchable attributes cache
     *
     * @var array
     */
    protected $_searchableAttributes     = null;

    /**
     * Index values separator
     *
     * @var string
     */
    protected $_separator                = '|';

    /**
     * Array of Zend_Date objects per store
     *
     * @var array
     */
    protected $_dates                    = array();

    /**
     * Oggetto Type Instances cache
     *
     * @var array
     */
    protected $_productTypes             = array();

    /**
     * Store search engine instance
     *
     * @var object
     */
    protected $_engine                   = null;

    /**
     * Whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     * @var bool
     */
    protected $_allowTableChanges       = true;





    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('scoresearch/fulltext', 'oggetto_id');
        $this->_engine = Mage::helper('scoresearch')->getEngine();
    }

    /**
     * Return options separator
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

    /**
     * Regenerate search index for store(s)
     *
     * @param  int|null $storeId
     * @param  int|array|null $oggettoIds
     * @return Shaurmalab_ScoreSearch_Model_Resource_Fulltext
     */
    public function rebuildIndex($storeId = null, $oggettoIds = null)
    {
        if (is_null($storeId)) {
            $storeIds = array_keys(Mage::app()->getStores());
            foreach ($storeIds as $storeId) {
                $this->_rebuildStoreIndex($storeId, $oggettoIds);
            }
        } else {
            $this->_rebuildStoreIndex($storeId, $oggettoIds);
        }

        return $this;
    }

    /**
     * Regenerate search index for specific store
     *
     * @param int $storeId Store View Id
     * @param int|array $oggettoIds Oggetto Entity Id
     * @return Shaurmalab_ScoreSearch_Model_Resource_Fulltext
     */
    protected function _rebuildStoreIndex($storeId, $oggettoIds = null)
    {
        $this->cleanIndex($storeId, $oggettoIds);

        // prepare searchable attributes
        $staticFields = array();
        foreach ($this->_getSearchableAttributes('static') as $attribute) {
            $staticFields[] = $attribute->getAttributeCode();
        }
        $dynamicFields = array(
            'int'       => array_keys($this->_getSearchableAttributes('int')),
            'varchar'   => array_keys($this->_getSearchableAttributes('varchar')),
            'text'      => array_keys($this->_getSearchableAttributes('text')),
            'decimal'   => array_keys($this->_getSearchableAttributes('decimal')),
            'datetime'  => array_keys($this->_getSearchableAttributes('datetime')),
        );

        // status and visibility filter
        $visibility     = $this->_getSearchableAttribute('visibility');
        $status         = $this->_getSearchableAttribute('status');
        $statusVals     = Mage::getSingleton('score/oggetto_status')->getVisibleStatusIds();
        $allowedVisibilityValues = $this->_engine->getAllowedVisibility();

        $lastOggettoId = 0;
        while (true) {
            $oggettos = $this->_getSearchableOggettos($storeId, $staticFields, $oggettoIds, $lastOggettoId);
            if (!$oggettos) {
                break;
            }

            $oggettoAttributes = array();
            $oggettoRelations  = array();
            foreach ($oggettos as $oggettoData) {
                $lastOggettoId = $oggettoData['entity_id'];
                $oggettoAttributes[$oggettoData['entity_id']] = $oggettoData['entity_id'];
                $oggettoChildren = $this->_getOggettoChildIds($oggettoData['entity_id'], $oggettoData['type_id']);
                $oggettoRelations[$oggettoData['entity_id']] = $oggettoChildren;
                if ($oggettoChildren) {
                    foreach ($oggettoChildren as $oggettoChildId) {
                        $oggettoAttributes[$oggettoChildId] = $oggettoChildId;
                    }
                }
            }

            $oggettoIndexes    = array();
            $oggettoAttributes = $this->_getOggettoAttributes($storeId, $oggettoAttributes, $dynamicFields);
            foreach ($oggettos as $oggettoData) {
                if (!isset($oggettoAttributes[$oggettoData['entity_id']])) {
                    continue;
                }

                $oggettoAttr = $oggettoAttributes[$oggettoData['entity_id']];
                if (!isset($oggettoAttr[$visibility->getId()])
                    || !in_array($oggettoAttr[$visibility->getId()], $allowedVisibilityValues)
                ) {
                    continue;
                }
                if (!isset($oggettoAttr[$status->getId()]) || !in_array($oggettoAttr[$status->getId()], $statusVals)) {
                    continue;
                }

                $oggettoIndex = array(
                    $oggettoData['entity_id'] => $oggettoAttr
                );

                if ($oggettoChildren = $oggettoRelations[$oggettoData['entity_id']]) {
                    foreach ($oggettoChildren as $oggettoChildId) {
                        if (isset($oggettoAttributes[$oggettoChildId])) {
                            $oggettoIndex[$oggettoChildId] = $oggettoAttributes[$oggettoChildId];
                        }
                    }
                }

                $index = $this->_prepareOggettoIndex($oggettoIndex, $oggettoData, $storeId);

                $oggettoIndexes[$oggettoData['entity_id']] = $index;
            }

            $this->_saveOggettoIndexes($storeId, $oggettoIndexes);
        }

        $this->resetSearchResults();

        return $this;
    }

    /**
     * Retrieve searchable products per store
     *
     * @param int $storeId
     * @param array $staticFields
     * @param array|int $oggettoIds
     * @param int $lastProductId
     * @param int $limit
     * @return array
     */
    protected function _getSearchableOggettos($storeId, array $staticFields, $oggettoIds = null, $lastOggettoId = 0,
        $limit = 100)
    {
        $websiteId      = 0;//Mage::app()->getStore($storeId)->getWebsiteId();
        $writeAdapter   = $this->_getWriteAdapter();
        // TODO: make only public Oggettos searchable
        $select = $writeAdapter->select()
            ->useStraightJoin(true)
            ->from(
                array('e' => $this->getTable('score/oggetto')),
                array_merge(array('entity_id', 'type_id'), $staticFields)
            )
            /*->join(
                array('website' => $this->getTable('score/oggetto_website')),
                $writeAdapter->quoteInto(
                    'website.oggetto_id=e.entity_id AND website.website_id=?',
                    $websiteId
                ),
                array()
            ) // TODO: mage seach through all websites ?
           /* ->join(
                array('stock_status' => $this->getTable('cataloginventory/stock_status')),
                $writeAdapter->quoteInto(
                    'stock_status.oggetto_id=e.entity_id AND stock_status.website_id=?',
                    $websiteId
                ),
                array('in_stock' => 'stock_status')
            ) */
           ;

        if (!is_null($oggettoIds)) {
            $select->where('e.entity_id IN(?)', $oggettoIds);
        }

        $select->where('e.entity_id>?', $lastOggettoId)
            ->limit($limit)
            ->order('e.entity_id');
        $result = $writeAdapter->fetchAll($select);

        return $result;
    }

    /**
     * Reset search results
     *
     * @return Shaurmalab_ScoreSearch_Model_Resource_Fulltext
     */
    public function resetSearchResults()
    {
        $adapter = $this->_getWriteAdapter();
        //$adapter->update($this->getTable('scoresearch/search_query'), array('is_processed' => 0)); // TODO: fix that query. speed issue
        $adapter->delete($this->getTable('scoresearch/result'));

        Mage::dispatchEvent('scoresearch_reset_search_result');

        return $this;
    }

    /**
     * Delete search index data for store
     *
     * @param int $storeId Store View Id
     * @param int $oggettoId Oggetto Entity Id
     * @return Shaurmalab_ScoreSearch_Model_Resource_Fulltext
     */
    public function cleanIndex($storeId = null, $oggettoId = null)
    {
        if ($this->_engine) {
            $this->_engine->cleanIndex($storeId, $oggettoId);
        }

        return $this;
    }

    /**
     * Prepare results for query
     *
     * @param Shaurmalab_ScoreSearch_Model_Fulltext $object
     * @param string $queryText
     * @param Shaurmalab_ScoreSearch_Model_Query $query
     * @return Shaurmalab_ScoreSearch_Model_Resource_Fulltext
     */
    public function prepareResult($object, $queryText, $query)
    {
        $adapter = $this->_getWriteAdapter();
        if (!$query->getIsProcessed()) {
            $searchType = $object->getSearchType($query->getStoreId());

            $preparedTerms = Mage::getResourceHelper('scoresearch')
                ->prepareTerms($queryText, $query->getMaxQueryWords());

            $bind = array();
            $like = array();
            $likeCond  = '';
            if ($searchType == Shaurmalab_ScoreSearch_Model_Fulltext::SEARCH_TYPE_LIKE
                || $searchType == Shaurmalab_ScoreSearch_Model_Fulltext::SEARCH_TYPE_COMBINE
            ) {
                $helper = Mage::getResourceHelper('core');
                $words = Mage::helper('core/string')->splitWords($queryText, true, $query->getMaxQueryWords());
                foreach ($words as $word) {
                    $like[] = $helper->getCILike('s.data_index', $word, array('position' => 'any'));
                }
                if ($like) {
                    $likeCond = '(' . join(' OR ', $like) . ')';
                }
            }
            $mainTableAlias = 's';
            $fields = array(
                'query_id' => new Zend_Db_Expr($query->getId()),
                'oggetto_id',
            );
            $select = $adapter->select()
                ->from(array($mainTableAlias => $this->getMainTable()), $fields)
                ->joinInner(array('e' => $this->getTable('score/oggetto')),
                    'e.entity_id = s.oggetto_id',
                    array())
                ->where($mainTableAlias.'.store_id = ?', (int)$query->getStoreId());

            if ($searchType == Shaurmalab_ScoreSearch_Model_Fulltext::SEARCH_TYPE_FULLTEXT
                || $searchType == Shaurmalab_ScoreSearch_Model_Fulltext::SEARCH_TYPE_COMBINE
            ) {
                $bind[':query'] = implode(' ', $preparedTerms[0]);
                $where = Mage::getResourceHelper('scoresearch')
                    ->chooseFulltext($this->getMainTable(), $mainTableAlias, $select);
            }

            if ($likeCond != '' && $searchType == Shaurmalab_ScoreSearch_Model_Fulltext::SEARCH_TYPE_COMBINE) {
                    $where .= ($where ? ' OR ' : '') . $likeCond;
            } elseif ($likeCond != '' && $searchType == Shaurmalab_ScoreSearch_Model_Fulltext::SEARCH_TYPE_LIKE) {
                $select->columns(array('relevance'  => new Zend_Db_Expr(0)));
                $where = $likeCond;
            }

            if ($where != '') {
                $select->where($where);
            }

            $sql = $adapter->insertFromSelect($select,
                $this->getTable('scoresearch/result'),
                array(),
                Varien_Db_Adapter_Interface::INSERT_ON_DUPLICATE);
            $adapter->query($sql, $bind);

            $query->setIsProcessed(1);
        }

        return $this;
    }

    /**
     * Retrieve EAV Config Singleton
     *
     * @return Mage_Eav_Model_Config
     */
    public function getEavConfig()
    {
        return Mage::getSingleton('eav/config');
    }

    /**
     * Retrieve searchable attributes
     *
     * @param string $backendType
     * @return array
     */
    protected function _getSearchableAttributes($backendType = null)
    {
        if (is_null($this->_searchableAttributes)) {
            $this->_searchableAttributes = array();

            $oggettoAttributeCollection = Mage::getResourceModel('score/oggetto_attribute_collection');

            if ($this->_engine && $this->_engine->allowAdvancedIndex()) {
                $oggettoAttributeCollection->addToIndexFilter(true);
            } else {
                $oggettoAttributeCollection->addSearchableAttributeFilter();
            }
            $attributes = $oggettoAttributeCollection->getItems();

            Mage::dispatchEvent('catelogsearch_searchable_attributes_load_after', array(
                'engine' => $this->_engine,
                'attributes' => $attributes
            ));

            $entity = $this->getEavConfig()
                ->getEntityType(Shaurmalab_Score_Model_Oggetto::ENTITY)
                ->getEntity();

            foreach ($attributes as $attribute) {
                $attribute->setEntity($entity);
            }

            $this->_searchableAttributes = $attributes;
        }

        if (!is_null($backendType)) {
            $attributes = array();
            foreach ($this->_searchableAttributes as $attributeId => $attribute) {
                if ($attribute->getBackendType() == $backendType) {
                    $attributes[$attributeId] = $attribute;
                }
            }

            return $attributes;
        }

        return $this->_searchableAttributes;
    }

    /**
     * Retrieve searchable attribute by Id or code
     *
     * @param int|string $attribute
     * @return Mage_Eav_Model_Entity_Attribute
     */
    protected function _getSearchableAttribute($attribute)
    {
        $attributes = $this->_getSearchableAttributes();
        if (is_numeric($attribute)) {
            if (isset($attributes[$attribute])) {
                return $attributes[$attribute];
            }
        } elseif (is_string($attribute)) {
            foreach ($attributes as $attributeModel) {
                if ($attributeModel->getAttributeCode() == $attribute) {
                    return $attributeModel;
                }
            }
        }

        return $this->getEavConfig()->getAttribute(Shaurmalab_Score_Model_Oggetto::ENTITY, $attribute);
    }

    /**
     * Returns expresion for field unification
     *
     * @param string $field
     * @param string $backendType
     * @return Zend_Db_Expr
     */
    protected function _unifyField($field, $backendType = 'varchar')
    {
        if ($backendType == 'datetime') {
            $expr = Mage::getResourceHelper('scoresearch')->castField(
                $this->_getReadAdapter()->getDateFormatSql($field, '%Y-%m-%d %H:%i:%s'));
        } else {
            $expr = Mage::getResourceHelper('scoresearch')->castField($field);
        }
        return $expr;
    }

    /**
     * Load product(s) attributes
     *
     * @param int $storeId
     * @param array $oggettoIds
     * @param array $attributeTypes
     * @return array
     */
    protected function _getOggettoAttributes($storeId, array $oggettoIds, array $attributeTypes)
    {
        $result  = array();
        $selects = array();
        $adapter = $this->_getWriteAdapter();
        $ifStoreValue = $adapter->getCheckSql('t_store.value_id > 0', 't_store.value', 't_default.value');
        foreach ($attributeTypes as $backendType => $attributeIds) {
            if ($attributeIds) {
                $tableName = $this->getTable(array('score/oggetto', $backendType));
                $selects[] = $adapter->select()
                    ->from(
                        array('t_default' => $tableName),
                        array('entity_id', 'attribute_id'))
                    ->joinLeft(
                        array('t_store' => $tableName),
                        $adapter->quoteInto(
                            't_default.entity_id=t_store.entity_id' .
                                ' AND t_default.attribute_id=t_store.attribute_id' .
                                ' AND t_store.store_id=?',
                            $storeId),
                        array('value' => $this->_unifyField($ifStoreValue, $backendType)))
                    ->where('t_default.store_id=?', 0)
                    ->where('t_default.attribute_id IN (?)', $attributeIds)
                    ->where('t_default.entity_id IN (?)', $oggettoIds);
            }
        }

        if ($selects) {
            $select = $adapter->select()->union($selects, Zend_Db_Select::SQL_UNION_ALL);
            $query = $adapter->query($select);
            while ($row = $query->fetch()) {
                $result[$row['entity_id']][$row['attribute_id']] = $row['value'];
            }
        }

        return $result;
    }

    /**
     * Retrieve Oggetto Type Instance
     *
     * @param string $typeId
     * @return Mage_Catalog_Model_Product_Type_Abstract
     */
    protected function _getOggettoTypeInstance($typeId)
    {
        if (!isset($this->_oggettoTypes[$typeId])) {
            $oggettoEmulator = $this->_getOggettoEmulator();
            $oggettoEmulator->setTypeId($typeId);

            $this->_oggettoTypes[$typeId] = Mage::getSingleton('score/oggetto_type')
                ->factory($oggettoEmulator);
        }
        return $this->_oggettoTypes[$typeId];
    }

    /**
     * Return all oggetto children ids
     *
     * @param int $oggettoId Oggetto Entity Id
     * @param string $typeId Super Oggetto Link Type
     * @return array
     */
    protected function _getOggettoChildIds($oggettoId, $typeId)
    {
        $typeInstance = $this->_getOggettoTypeInstance($typeId);
        $relation = $typeInstance->isComposite()
            ? $typeInstance->getRelationInfo()
            : false;

        if ($relation && $relation->getTable() && $relation->getParentFieldName() && $relation->getChildFieldName()) {
            $select = $this->_getReadAdapter()->select()
                ->from(
                    array('main' => $this->getTable($relation->getTable())),
                    array($relation->getChildFieldName()))
                ->where("{$relation->getParentFieldName()}=?", $oggettoId);
            if (!is_null($relation->getWhere())) {
                $select->where($relation->getWhere());
            }
            return $this->_getReadAdapter()->fetchCol($select);
        }

        return null;
    }

    /**
     * Retrieve Oggetto Emulator (Varien Object)
     *
     * @return Varien_Object
     */
    protected function _getOggettoEmulator()
    {
        $oggettoEmulator = new Varien_Object();
        $oggettoEmulator->setIdFieldName('entity_id');

        return $oggettoEmulator;
    }

    /**
     * Prepare Fulltext index value for product
     *
     * @param array $indexData
     * @param array $oggettoData
     * @param int $storeId
     * @return string
     */
    protected function _prepareOggettoIndex($indexData, $oggettoData, $storeId)
    {
        $index = array();

        foreach ($this->_getSearchableAttributes('static') as $attribute) {
            $attributeCode = $attribute->getAttributeCode();

            if (isset($oggettoData[$attributeCode])) {
                $value = $this->_getAttributeValue($attribute->getId(), $oggettoData[$attributeCode], $storeId);
                if ($value) {
                    //For grouped products
                    if (isset($index[$attributeCode])) {
                        if (!is_array($index[$attributeCode])) {
                            $index[$attributeCode] = array($index[$attributeCode]);
                        }
                        $index[$attributeCode][] = $value;
                    }
                    //For other types of products
                    else {
                        $index[$attributeCode] = $value;
                    }
                }
            }
        }

        foreach ($indexData as $entityId => $attributeData) {
            foreach ($attributeData as $attributeId => $attributeValue) {
                $value = $this->_getAttributeValue($attributeId, $attributeValue, $storeId);
                if (!is_null($value) && $value !== false) {
                    $attributeCode = $this->_getSearchableAttribute($attributeId)->getAttributeCode();

                    if (isset($index[$attributeCode])) {
                        $index[$attributeCode][$entityId] = $value;
                    } else {
                        $index[$attributeCode] = array($entityId => $value);
                    }
                }
            }
        }

        if (!$this->_engine->allowAdvancedIndex()) {
            $oggetto = $this->_getOggettoEmulator()
                ->setId($oggettoData['entity_id'])
                ->setTypeId($oggettoData['type_id'])
                ->setStoreId($storeId);
            $typeInstance = $this->_getOggettoTypeInstance($oggettoData['type_id']);
            if ($data = $typeInstance->getSearchableData($oggetto)) {
                $index['options'] = $data;
            }
        }

        if (isset($oggettoData['in_stock'])) {
            $index['in_stock'] = $oggettoData['in_stock'];
        }

        if ($this->_engine) {
            return $this->_engine->prepareEntityIndex($index, $this->_separator);
        }

        return Mage::helper('scoresearch')->prepareIndexdata($index, $this->_separator);
    }

    /**
     * Retrieve attribute source value for search
     *
     * @param int $attributeId
     * @param mixed $value
     * @param int $storeId
     * @return mixed
     */
    protected function _getAttributeValue($attributeId, $value, $storeId)
    {
        $attribute = $this->_getSearchableAttribute($attributeId);
        if (!$attribute->getIsSearchable()) {
            if ($this->_engine->allowAdvancedIndex()) {
                if ($attribute->getAttributeCode() == 'visibility') {
                    return $value;
                } elseif (!($attribute->getIsVisibleInAdvancedSearch()
                    || $attribute->getIsFilterable()
                    || $attribute->getIsFilterableInSearch()
                    || $attribute->getUsedForSortBy())
                ) {
                    return null;
                }
            } else {
                return null;
            }
        }

        if ($attribute->usesSource()) {
            if ($this->_engine->allowAdvancedIndex()) {
                return $value;
            }

            $attribute->setStoreId($storeId);
            $value = $attribute->getSource()->getIndexOptionText($value);

            if (is_array($value)) {
                $value = implode($this->_separator, $value);
            } elseif (empty($value)) {
                $inputType = $attribute->getFrontend()->getInputType();
                if ($inputType == 'select' || $inputType == 'multiselect') {
                    return null;
                }
            }
        } elseif ($attribute->getBackendType() == 'datetime') {
            $value = $this->_getStoreDate($storeId, $value);
        } else {
            $inputType = $attribute->getFrontend()->getInputType();
            if ($inputType == 'price') {
                $value = Mage::app()->getStore($storeId)->roundPrice($value);
            }
        }

        $value = preg_replace("#\s+#siu", ' ', trim(strip_tags($value)));

        return $value;
    }

    /**
     * Save Oggetto index
     *
     * @param int $oggettoId
     * @param int $storeId
     * @param string $index
     * @return Shaurmalab_ScoreSearch_Model_Resource_Fulltext
     */
    protected function _saveOggettoIndex($oggettoId, $storeId, $index)
    {
        if ($this->_engine) {
            $this->_engine->saveEntityIndex($oggettoId, $storeId, $index);
        }

        return $this;
    }

    /**
     * Save Multiply Oggetto indexes
     *
     * @param int $storeId
     * @param array $oggettoIndexes
     * @return Shaurmalab_ScoreSearch_Model_Resource_Fulltext
     */
    protected function _saveOggettoIndexes($storeId, $oggettoIndexes)
    {
        if ($this->_engine) {
            $this->_engine->saveEntityIndexes($storeId, $oggettoIndexes);
        }

        return $this;
    }

    /**
     * Retrieve Date value for store
     *
     * @param int $storeId
     * @param string $date
     * @return string
     */
    protected function _getStoreDate($storeId, $date = null)
    {
        if (!isset($this->_dates[$storeId])) {
            $timezone = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE, $storeId);
            $locale   = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId);
            $locale   = new Zend_Locale($locale);

            $dateObj = new Zend_Date(null, null, $locale);
            $dateObj->setTimezone($timezone);
            $this->_dates[$storeId] = array($dateObj, $locale->getTranslation(null, 'date', $locale));
        }

        if (!is_empty_date($date)) {
            list($dateObj, $format) = $this->_dates[$storeId];
            $dateObj->setDate($date, Varien_Date::DATETIME_INTERNAL_FORMAT);

            return $dateObj->toString($format);
        }

        return null;
    }





    // Deprecated methods

    /**
     * Set whether table changes are allowed
     *
     * @deprecated after 1.6.1.0
     * @param bool $value
     * @return Shaurmalab_ScoreSearch_Model_Resource_Fulltext
     */
    public function setAllowTableChanges($value = true)
    {
        $this->_allowTableChanges = $value;
        return $this;
    }

    /**
     * Update category products indexes
     *
     * deprecated after 1.6.2.0
     *
     * @param array $oggettoIds
     * @param array $categoryIds
     * @return Shaurmalab_ScoreSearch_Model_Resource_Fulltext
     */
    public function updateCategoryIndex($oggettoIds, $categoryIds)
    {
        return $this;
    }
}
