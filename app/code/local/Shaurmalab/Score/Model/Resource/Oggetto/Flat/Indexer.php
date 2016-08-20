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
 * @package     Shaurmalab_Score
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Score Oggetto Flat Indexer Resource Model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer extends Mage_Index_Model_Resource_Abstract
{
    const XML_NODE_MAX_INDEX_COUNT  = 'global/score/oggetto/flat/max_index_count';
    const XML_NODE_ATTRIBUTE_NODES  = 'global/score/oggetto/flat/attribute_nodes';

    /**
     * Attribute codes for flat
     *
     * @var array
     */
    protected $_attributeCodes;

    /**
     * Attribute objects for flat cache
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Required system attributes for preload
     *
     * @var array
     */
    protected $_systemAttributes     = array('status', 'required_options', 'tax_class_id', 'weight');

    /**
     * Eav Catalog_Oggetto Entity Type Id
     *
     * @var int
     */
    protected $_entityTypeId;

    /**
     * Flat table columns cache
     *
     * @var array
     */
    protected $_columns;

    /**
     * Flat table indexes cache
     *
     * @var array
     */
    protected $_indexes;

    /**
     * Oggetto Type Instances cache
     *
     * @var array
     */
    protected $_oggettoTypes;

    /**
     * Exists flat tables cache
     *
     * @var array
     */
    protected $_existsFlatTables     = array();

    /**
     * Flat tables which were prepared
     *
     * @var array
     */
    protected $_preparedFlatTables   = array();

    /**
     * Initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto', 'entity_id');
    }

    /**
     * Rebuild Score Oggetto Flat Data
     *
     * @param Mage_Core_Model_Store|int $store
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function rebuild($store = null)
    {
        if ($store === null) {
            foreach (Mage::app()->getStores() as $store) {
                $this->rebuild($store->getId());
            }
            return $this;
        }

        $storeId = (int)Mage::app()->getStore($store)->getId();

        $this->prepareFlatTable($storeId);
        $this->cleanNonWebsiteOggettos($storeId);
        $this->updateStaticAttributes($storeId);
        $this->updateEavAttributes($storeId);
        $this->updateEventAttributes($storeId);
        $this->updateRelationOggettos($storeId);
        $this->cleanRelationOggettos($storeId);

        $flag = $this->getFlatHelper()->getFlag();
        $flag->setIsBuilt(true)->setStoreBuilt($storeId, true)->save();
        return $this;
    }

    /**
     * Retrieve Score Oggetto Flat helper
     *
     * @return Shaurmalab_Score_Helper_Oggetto_Flat
     */
    public function getFlatHelper()
    {
        return Mage::helper('score/oggetto_flat');
    }

    /**
     * Retrieve attribute codes using for flat
     *
     * @return array
     */
    public function getAttributeCodes()
    {
        if ($this->_attributeCodes === null) {
            $adapter               = $this->_getReadAdapter();
            $this->_attributeCodes = array();

            $attributeNodes = Mage::getConfig()
                ->getNode(self::XML_NODE_ATTRIBUTE_NODES)
                ->children();
            foreach ($attributeNodes as $node) {
                $attributes = Mage::getConfig()->getNode((string)$node)->asArray();
                $attributes = array_keys($attributes);
                $this->_systemAttributes = array_unique(array_merge($attributes, $this->_systemAttributes));
            }

            $bind = array(
                'backend_type'      => Mage_Eav_Model_Entity_Attribute_Abstract::TYPE_STATIC,
                'entity_type_id'    => $this->getEntityTypeId()
            );

            $select = $adapter->select()
                ->from(array('main_table' => $this->getTable('eav/attribute')))
                ->join(
                    array('additional_table' => $this->getTable('score/eav_attribute')),
                    'additional_table.attribute_id = main_table.attribute_id'
                )
                ->where('main_table.entity_type_id = :entity_type_id');
            $whereCondition = array(
                'main_table.backend_type = :backend_type',
                $adapter->quoteInto('additional_table.is_used_for_promo_rules = ?', 1),
                $adapter->quoteInto('additional_table.used_in_oggetto_listing = ?', 1),
                $adapter->quoteInto('additional_table.used_for_sort_by = ?', 1),
                $adapter->quoteInto('main_table.attribute_code IN(?)', $this->_systemAttributes)
            );
            if ($this->getFlatHelper()->isAddFilterableAttributes()) {
               $whereCondition[] = $adapter->quoteInto('additional_table.is_filterable > ?', 0);
            }

            $select->where(implode(' OR ', $whereCondition));
            $attributesData = $adapter->fetchAll($select, $bind);
            Mage::getSingleton('eav/config')
                ->importAttributesData($this->getEntityType(), $attributesData);

            foreach ($attributesData as $data) {
                $this->_attributeCodes[$data['attribute_id']] = $data['attribute_code'];
            }
            unset($attributesData);
        }

        return $this->_attributeCodes;
    }

    /**
     * Retrieve entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return Shaurmalab_Score_Model_Oggetto::ENTITY;
    }

    /**
     * Retrieve Score Entity Type Id
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        if ($this->_entityTypeId === null) {
            $this->_entityTypeId = Mage::getResourceModel('score/config')
                ->getEntityTypeId();
        }
        return $this->_entityTypeId;
    }

    /**
     * Retrieve attribute objects for flat
     *
     * @return array
     */
    public function getAttributes()
    {
        if ($this->_attributes === null) {
            $this->_attributes = array();
            $attributeCodes    = $this->getAttributeCodes();
            $entity = Mage::getSingleton('eav/config')
                ->getEntityType($this->getEntityType())
                ->getEntity();

            foreach ($attributeCodes as $attributeCode) {
                $attribute = Mage::getSingleton('eav/config')
                    ->getAttribute($this->getEntityType(), $attributeCode)
                    ->setEntity($entity);
                try {
                    // check if exists source and backend model.
                    // To prevent exception when some module was disabled
                    $attribute->usesSource() && $attribute->getSource();
                    $attribute->getBackend();
                    $this->_attributes[$attributeCode] = $attribute;
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }

        return $this->_attributes;
    }

    /**
     * Retrieve loaded attribute by code
     *
     * @param string $attributeCode
     * @throws Mage_Core_Exception
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute($attributeCode)
    {
        $attributes = $this->getAttributes();
        if (!isset($attributes[$attributeCode])) {
            $attribute = Mage::getModel('score/resource_eav_attribute')
                ->loadByCode($this->getEntityTypeId(), $attributeCode);
            if (!$attribute->getId()) {
                Mage::throwException(Mage::helper('score')->__('Invalid attribute %s', $attributeCode));
            }
            $entity = Mage::getSingleton('eav/config')
                ->getEntityType($this->getEntityType())
                ->getEntity();
            $attribute->setEntity($entity);

            return $attribute;
        }

        return $attributes[$attributeCode];
    }

    /**
     * Retrieve Score Oggetto Flat Table name
     *
     * @param int $storeId
     * @return string
     */
    public function getFlatTableName($storeId)
    {
        return sprintf('%s_%s', $this->getTable('score/oggetto_flat'), $storeId);
    }

    /**
     * Retrieve score oggetto flat columns array in old format (used before MMDB support)
     *
     * @return array
     */
    protected function _getFlatColumnsOldDefinition()
    {
        $columns = array();
        $columns['entity_id'] = array(
            'type'      => 'int(10)',
            'unsigned'  => true,
            'is_null'   => false,
            'default'   => null,
            'extra'     => null
        );
        if ($this->getFlatHelper()->isAddChildData()) {
            $columns['child_id'] = array(
                'type'      => 'int(10)',
                'unsigned'  => true,
                'is_null'   => true,
                'default'   => null,
                'extra'     => null
            );
            $columns['is_child'] = array(
                'type'      => 'tinyint(1)',
                'unsigned'  => true,
                'is_null'   => false,
                'default'   => 0,
                'extra'     => null
            );
        }
        $columns['attribute_set_id'] = array(
            'type'      => 'smallint(5)',
            'unsigned'  => true,
            'is_null'   => false,
            'default'   => 0,
            'extra'     => null
        );
        $columns['type_id'] = array(
            'type'      => 'varchar(32)',
            'unsigned'  => false,
            'is_null'   => false,
            'default'   => Shaurmalab_Score_Model_Oggetto_Type::TYPE_SIMPLE,
            'extra'     => null
        );

        return $columns;
    }

    /**
     * Retrieve score oggetto flat columns array in DDL format
     *
     * @return array
     */
    protected function _getFlatColumnsDdlDefinition()
    {
        $columns = array();
        $columns['entity_id'] = array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length'    => null,
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => false,
            'primary'   => true,
            'comment'   => 'Entity Id'
        );
        if ($this->getFlatHelper()->isAddChildData()) {
            $columns['child_id'] = array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'length'    => null,
                'unsigned'  => true,
                'nullable'  => true,
                'default'   => null,
                'primary'   => true,
                'comment'   => 'Child Id'
            );
            $columns['is_child'] = array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'length'    => 1,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Checks If Entity Is Child'
            );
        }
        $columns['attribute_set_id'] = array(
            'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'length'    => 5,
            'unsigned'  => true,
            'nullable'  => false,
            'default'   => '0',
            'comment'   => 'Attribute Set Id'
        );
        $columns['type_id'] = array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length'    => 32,
            'unsigned'  => false,
            'nullable'  => false,
            'default'   => Shaurmalab_Score_Model_Oggetto_Type::TYPE_SIMPLE,
            'comment'   => 'Type Id'
        );

        return $columns;
    }

    /**
     * Retrieve score oggetto flat table columns array
     *
     * @return array
     */
    public function getFlatColumns()
    {
        if ($this->_columns === null) {
            if (Mage::helper('core')->useDbCompatibleMode()) {
                $this->_columns = $this->_getFlatColumnsOldDefinition();
            } else {
                $this->_columns = $this->_getFlatColumnsDdlDefinition();
            }

            foreach ($this->getAttributes() as $attribute) {
                /** @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */
                $columns = $attribute
                    ->setFlatAddFilterableAttributes($this->getFlatHelper()->isAddFilterableAttributes())
                    ->setFlatAddChildData($this->getFlatHelper()->isAddChildData())
                    ->getFlatColumns();
                if ($columns !== null) {
                    $this->_columns = array_merge($this->_columns, $columns);
                }
            }

            $columnsObject = new Varien_Object();
            $columnsObject->setColumns($this->_columns);
            Mage::dispatchEvent('score_oggetto_flat_prepare_columns',
                array('columns' => $columnsObject)
            );
            $this->_columns = $columnsObject->getColumns();
        }

        return $this->_columns;
    }

    /**
     * Retrieve score oggetto flat table indexes array
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        if ($this->_indexes === null) {
            $this->_indexes = array();

            if ($this->getFlatHelper()->isAddChildData()) {
                $this->_indexes['PRIMARY'] = array(
                    'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY,
                    'fields' => array('entity_id', 'child_id')
                );
                $this->_indexes['IDX_CHILD'] = array(
                    'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
                    'fields' => array('child_id')
                );
                $this->_indexes['IDX_IS_CHILD'] = array(
                    'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
                    'fields' => array('entity_id', 'is_child')
                );
            } else {
                $this->_indexes['PRIMARY'] = array(
                    'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY,
                    'fields' => array('entity_id')
                );
            }
            $this->_indexes['IDX_TYPE_ID'] = array(
                'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
                'fields' => array('type_id')
            );
            $this->_indexes['IDX_ATTRIBUTE_SET'] = array(
                'type'   => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX,
                'fields' => array('attribute_set_id')
            );

            foreach ($this->getAttributes() as $attribute) {
                /** @var $attribute Mage_Eav_Model_Entity_Attribute */
                $indexes = $attribute
                    ->setFlatAddFilterableAttributes($this->getFlatHelper()->isAddFilterableAttributes())
                    ->setFlatAddChildData($this->getFlatHelper()->isAddChildData())
                    ->getFlatIndexes();
                if ($indexes !== null) {
                    $this->_indexes = array_merge($this->_indexes, $indexes);
                }
            }

            $indexesObject = new Varien_Object();
            $indexesObject->setIndexes($this->_indexes);
            Mage::dispatchEvent('score_oggetto_flat_prepare_indexes', array(
                'indexes'   => $indexesObject
            ));
            $this->_indexes = $indexesObject->getIndexes();
        }

        return $this->_indexes;
    }

    /**
     * Compare Flat style with Describe style columns
     * If column a different - return false
     *
     * @param array $column
     * @param array $describe
     * @return bool
     */
    protected function _compareColumnProperties($column, $describe)
    {
        return Mage::getResourceHelper('score')->compareIndexColumnProperties($column, $describe);
    }

    /**
     * Retrieve column definition fragment
     * @deprecated since 1.5.0.0
     *
     * Example: `field_name` smallint(5) unsigned NOT NULL default '0'
     *
     * @param string $fieldName
     * @param array $fieldProp
     * @return string
     */
    protected function _sqlColunmDefinition($fieldName, $fieldProp)
    {
        $fieldNameQuote = $this->_getWriteAdapter()->quoteIdentifier($fieldName);

        /**
         * Process the case when 'is_null' prohibits null value, and 'default' proposed to be null
         * It just means that default value not specified
         */
        if ($fieldProp['is_null'] === false && $fieldProp['default'] === null) {
            $defaultValue = '';
        } else {
            $defaultValue = $fieldProp['default'] === null ? ' DEFAULT NULL' : $this->_getReadAdapter()
                ->quoteInto(' DEFAULT ?', $fieldProp['default']);
        }

        return "{$fieldNameQuote} {$fieldProp['type']}"
            . ($fieldProp['unsigned'] ? ' UNSIGNED' : '')
            . ($fieldProp['extra'] ? ' ' . $fieldProp['extra'] : '')
            . ($fieldProp['is_null'] === false ? ' NOT NULL' : '')
            . $defaultValue;
    }

    /**
     * Retrieve index definition fragment
     * @deprecated since 1.5.0.0
     *
     * Example: INDEX `IDX_NAME` (`field_id`)
     *
     * @param string $indexName
     * @param array $indexProp
     * @return string
     */
    protected function _sqlIndexDefinition($indexName, $indexProp)
    {
        $fields = $indexProp['fields'];
        if (is_array($fields)) {
            $fieldSql = array();
            foreach ($fields as $field) {
                $fieldSql[] = $this->_getReadAdapter()->quoteIdentifier($field);
            }
            $fieldSql = join(',', $fieldSql);
        }
        else {
            $fieldSql = $this->_getReadAdapter()->quoteIdentifier($fields);
        }

        $indexNameQuote = $this->_getReadAdapter()->quoteIdentifier($indexName);
        switch (strtolower($indexProp['type'])) {
            case 'primary':
                $condition = 'PRIMARY KEY';
                break;
            case 'unique':
                $condition = 'UNIQUE ' . $indexNameQuote;
                break;
            case 'fulltext':
                $condition = 'FULLTEXT ' . $indexNameQuote;
                break;
            default:
                $condition = 'INDEX ' . $indexNameQuote;
                break;
        }

        return sprintf('%s (%s)', $condition, $fieldSql);
    }

    /**
     * Retrieve UNIQUE HASH for a Table foreign key
     *
     * @param string $priTableName  the target table name
     * @param string $priColumnName the target table column name
     * @param string $refTableName  the reference table name
     * @param string $refColumnName the reference table column name
     * @return string
     */
    public function getFkName($priTableName, $priColumnName, $refTableName, $refColumnName)
    {
        return Mage::getSingleton('core/resource')
            ->getFkName($priTableName, $priColumnName, $refTableName, $refColumnName);
    }

    /**
     * Prepare flat table for store
     *
     * @param int $storeId
     * @throws Mage_Core_Exception
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function prepareFlatTable($storeId)
    {
        if (isset($this->_preparedFlatTables[$storeId])) {
            return $this;
        }
        $adapter   = $this->_getWriteAdapter();
        $tableName = $this->getFlatTableName($storeId);

        // Extract columns we need to have in flat table
        $columns = $this->getFlatColumns();
        if (Mage::helper('core')->useDbCompatibleMode()) {
             /* Convert old format of flat columns to new MMDB format that uses DDL types and definitions */
            foreach ($columns as $key => $column) {
                $columns[$key] = Mage::getResourceHelper('core')->convertOldColumnDefinition($column);
            }
        }

        // Extract indexes we need to have in flat table
        $indexesNeed  = $this->getFlatIndexes();

        $maxIndex = Mage::getConfig()->getNode(self::XML_NODE_MAX_INDEX_COUNT);
        if (count($indexesNeed) > $maxIndex) {
            Mage::throwException(Mage::helper('score')->__("The Flat Score module has a limit of %2\$d filterable and/or sortable attributes. Currently there are %1\$d of them. Please reduce the number of filterable/sortable attributes in order to use this module", count($indexesNeed), $maxIndex));
        }

        // Process indexes to create names for them in MMDB-style and reformat to common index definition
        $indexKeys = array();
        $indexProps = array_values($indexesNeed);
        $upperPrimaryKey = strtoupper(Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY);
        foreach ($indexProps as $i => $indexProp) {
            $indexName = $adapter->getIndexName($tableName, $indexProp['fields'], $indexProp['type']);
            $indexProp['type'] = strtoupper($indexProp['type']);
            if ($indexProp['type'] == $upperPrimaryKey) {
                $indexKey = $upperPrimaryKey;
            } else {
                $indexKey = $indexName;
            }

            $indexProps[$i] = array(
                'KEY_NAME' => $indexName,
                'COLUMNS_LIST' => $indexProp['fields'],
                'INDEX_TYPE' => strtolower($indexProp['type'])
            );
            $indexKeys[$i] = $indexKey;
        }
        $indexesNeed = array_combine($indexKeys, $indexProps); // Array with index names as keys, except for primary

        // Foreign keys
        $foreignEntityKey = $this->getFkName($tableName, 'entity_id', 'score/oggetto', 'entity_id');
        $foreignChildKey  = $this->getFkName($tableName, 'child_id', 'score/oggetto', 'entity_id');

        // Create table or modify existing one
        if (!$this->_isFlatTableExists($storeId)) {
            /** @var $table Varien_Db_Ddl_Table */
            $table = $adapter->newTable($tableName);
            foreach ($columns as $fieldName => $fieldProp) {
                $table->addColumn(
                    $fieldName,
                    $fieldProp['type'],
                    isset($fieldProp['length']) ? $fieldProp['length'] : null,
                    array(
                        'nullable' => isset($fieldProp['nullable']) ? (bool)$fieldProp['nullable'] : false,
                        'unsigned' => isset($fieldProp['unsigned']) ? (bool)$fieldProp['unsigned'] : false,
                        'default'  => isset($fieldProp['default']) ? $fieldProp['default'] : false,
                        'primary'  => false,
                    ),
                    isset($fieldProp['comment']) ? $fieldProp['comment'] : $fieldName
                );
            }

            foreach ($indexesNeed as $indexProp) {
                $table->addIndex($indexProp['KEY_NAME'], $indexProp['COLUMNS_LIST'],
                    array('type' => $indexProp['INDEX_TYPE']));
            }

            $table->addForeignKey($foreignEntityKey,
                'entity_id', $this->getTable('score/oggetto'), 'entity_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);

            if ($this->getFlatHelper()->isAddChildData()) {
                $table->addForeignKey($foreignChildKey,
                    'child_id', $this->getTable('score/oggetto'), 'entity_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
            }
            $table->setComment("Score Oggetto Flat (Store {$storeId})");

            $adapter->createTable($table);

            $this->_existsFlatTables[$storeId] = true;
        } else {
            $adapter->resetDdlCache($tableName);

            // Sort columns into added/altered/dropped lists
            $describe   = $adapter->describeTable($tableName);
            $addColumns     = array_diff_key($columns, $describe);
            $dropColumns    = array_diff_key($describe, $columns);
            $modifyColumns  = array();
            foreach ($columns as $field => $fieldProp) {
                if (isset($describe[$field]) && !$this->_compareColumnProperties($fieldProp, $describe[$field])) {
                    $modifyColumns[$field] = $fieldProp;
                }
            }

            // Sort indexes into added/dropped lists. Altered indexes are put into both lists.
            $addIndexes = array();
            $dropIndexes = array();
            $indexesNow  = $adapter->getIndexList($tableName); // Note: primary is always stored under 'PRIMARY' key
            $newIndexes = $indexesNeed;
            foreach ($indexesNow as $key => $indexNow) {
                if (isset($indexesNeed[$key])) {
                    $indexNeed = $indexesNeed[$key];
                    if (($indexNeed['INDEX_TYPE'] != $indexNow['INDEX_TYPE'])
                        || ($indexNeed['COLUMNS_LIST'] != $indexNow['COLUMNS_LIST'])) {
                        $dropIndexes[$key] = $indexNow;
                        $addIndexes[$key] = $indexNeed;
                    }
                    unset($newIndexes[$key]);
                } else {
                    $dropIndexes[$key] = $indexNow;
                }
            }
            $addIndexes = $addIndexes + $newIndexes;

            // Compose contstraints
            $addConstraints = array();
            $addConstraints[$foreignEntityKey] = array(
                'table_index'   => 'entity_id',
                'ref_table'     => $this->getTable('score/oggetto'),
                'ref_index'     => 'entity_id',
                'on_update'     => Varien_Db_Ddl_Table::ACTION_CASCADE,
                'on_delete'     => Varien_Db_Ddl_Table::ACTION_CASCADE
            );

            // Additional data from childs
            $isAddChildData = $this->getFlatHelper()->isAddChildData();
            if (!$isAddChildData && isset($describe['is_child'])) {
                $adapter->delete($tableName, array('is_child = ?' => 1));
                $adapter->dropForeignKey($tableName, $foreignChildKey);
            }
            if ($isAddChildData && !isset($describe['is_child'])) {
                $adapter->delete($tableName);
                $dropIndexes['PRIMARY'] = $indexesNow['PRIMARY'];
                $addIndexes['PRIMARY']  = $indexesNeed['PRIMARY'];

                $addConstraints[$foreignChildKey] = array(
                    'table_index'   => 'child_id',
                    'ref_table'     => $this->getTable('score/oggetto'),
                    'ref_index'     => 'entity_id',
                    'on_update'     => Varien_Db_Ddl_Table::ACTION_CASCADE,
                    'on_delete'     => Varien_Db_Ddl_Table::ACTION_CASCADE
                );
            }

            // Drop constraints
            foreach (array_keys($adapter->getForeignKeys($tableName)) as $constraintName) {
                $adapter->dropForeignKey($tableName, $constraintName);
            }

            // Drop indexes
            foreach ($dropIndexes as $indexProp) {
                $adapter->dropIndex($tableName, $indexProp['KEY_NAME']);
            }

            // Drop columns
            foreach (array_keys($dropColumns) as $columnName) {
                $adapter->dropColumn($tableName, $columnName);
            }

            // Modify columns
            foreach ($modifyColumns as $columnName => $columnProp) {
                $columnProp = array_change_key_case($columnProp, CASE_UPPER);
                if (!isset($columnProp['COMMENT'])) {
                    $columnProp['COMMENT'] = ucwords(str_replace('_', ' ', $columnName));
                }
                $adapter->changeColumn($tableName, $columnName, $columnName, $columnProp);
            }

            // Add columns
            foreach ($addColumns as $columnName => $columnProp) {
                $columnProp = array_change_key_case($columnProp, CASE_UPPER);
                if (!isset($columnProp['COMMENT'])) {
                    $columnProp['COMMENT'] = ucwords(str_replace('_', ' ', $columnName));
                }
                $adapter->addColumn($tableName, $columnName, $columnProp);
            }

            // Add indexes
            foreach ($addIndexes as $indexProp) {
                $adapter->addIndex($tableName, $indexProp['KEY_NAME'], $indexProp['COLUMNS_LIST'],
                    $indexProp['INDEX_TYPE']);
            }

            // Add constraints
            foreach ($addConstraints as $constraintName => $constraintProp) {
                $adapter->addForeignKey($constraintName, $tableName,
                    $constraintProp['table_index'],
                    $constraintProp['ref_table'],
                    $constraintProp['ref_index'],
                    $constraintProp['on_delete'],
                    $constraintProp['on_update']
                );
            }
        }

        $this->_preparedFlatTables[$storeId] = true;

        return $this;
    }

    /**
     * Add or Update static attributes
     *
     * @param int $storeId
     * @param int|array $oggettoIds update only oggetto(s)
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function updateStaticAttributes($storeId, $oggettoIds = null)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }
        $adapter   = $this->_getWriteAdapter();
        $websiteId = (int)Mage::app()->getStore($storeId)->getWebsite()->getId();
        /* @var $status Mage_Eav_Model_Entity_Attribute */
        $status    = $this->getAttribute('status');

        $fieldList  = array('entity_id', 'type_id', 'attribute_set_id');
        $colsList   = array('entity_id', 'type_id', 'attribute_set_id');
        if ($this->getFlatHelper()->isAddChildData()) {
            $fieldList = array_merge($fieldList, array('child_id', 'is_child'));
            $isChild   = new Zend_Db_Expr('0');
            $colsList  = array_merge($colsList, array('entity_id', $isChild));
        }

        $columns    = $this->getFlatColumns();
        $bind       = array(
            'website_id'     => $websiteId,
            'store_id'       => $storeId,
            'entity_type_id' => (int)$status->getEntityTypeId(),
            'attribute_id'   => (int)$status->getId()
        );

        $fieldExpr = $adapter->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');
        $select     = $this->_getWriteAdapter()->select()
            ->from(array('e' => $this->getTable('score/oggetto')), $colsList)
            ->join(
                array('wp' => $this->getTable('score/oggetto_website')),
                'e.entity_id = wp.oggetto_id AND wp.website_id = :website_id',
                array())
            ->joinLeft(
                array('t1' => $status->getBackend()->getTable()),
                'e.entity_id = t1.entity_id',
                array())
            ->joinLeft(
                array('t2' => $status->getBackend()->getTable()),
                't2.entity_id = t1.entity_id'
                    . ' AND t1.entity_type_id = t2.entity_type_id'
                    . ' AND t1.attribute_id = t2.attribute_id'
                    . ' AND t2.store_id = :store_id',
                array())
            ->where('t1.entity_type_id = :entity_type_id')
            ->where('t1.attribute_id = :attribute_id')
            ->where('t1.store_id = ?', Mage_Core_Model_App::ADMIN_STORE_ID)
            ->where("{$fieldExpr} = ?", Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED);
        foreach ($this->getAttributes() as $attributeCode => $attribute) {
            /** @var $attribute Mage_Eav_Model_Entity_Attribute */
            if ($attribute->getBackend()->getType() == 'static') {
                if (!isset($columns[$attributeCode])) {
                    continue;
                }
                $fieldList[] = $attributeCode;
                $select->columns($attributeCode, 'e');
            }
        }

        if ($oggettoIds !== null) {
            $select->where('e.entity_id IN(?)', $oggettoIds);
        }

        $sql = $select->insertFromSelect($this->getFlatTableName($storeId), $fieldList);
        $adapter->query($sql, $bind);

        return $this;
    }

    /**
     * Remove non website oggettos
     *
     * @param int $storeId
     * @param int|array $oggettoIds
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function cleanNonWebsiteOggettos($storeId, $oggettoIds = null)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        $websiteId = (int)Mage::app()->getStore($storeId)->getWebsite()->getId();
        $adapter   = $this->_getWriteAdapter();

        $joinCondition = array(
            'e.entity_id = wp.oggetto_id',
            'wp.website_id = :website_id'
        );
        if ($this->getFlatHelper()->isAddChildData()) {
            $joinCondition[] = 'e.child_id = wp.oggetto_id';
        }
        $bind   = array('website_id'    => $websiteId);
        $select = $adapter->select()
            ->from(array('e' => $this->getFlatTableName($storeId)), null)
            ->joinLeft(
                array('wp' => $this->getTable('score/oggetto_website')),
                implode(' AND ', $joinCondition),
                array());
        if ($oggettoIds !== null) {
            $condition = array(
                $adapter->quoteInto('e.entity_id IN(?)', $oggettoIds)
            );
            if ($this->getFlatHelper()->isAddChildData()) {
                $condition[] = $adapter->quoteInto('e.child_id IN(?)', $oggettoIds);
            }
            $select->where(implode(' OR ', $condition));
        }

        $sql = $select->deleteFromSelect('e');
        $adapter->query($sql, $bind);

        return $this;
    }

    /**
     * Update attribute flat data
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param int $storeId
     * @param int|array $oggettoIds update only oggetto(s)
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function updateAttribute($attribute, $storeId, $oggettoIds = null)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }
        $adapter       = $this->_getWriteAdapter();
        $flatTableName = $this->getFlatTableName($storeId);
        $describe      = $adapter->describeTable($flatTableName);

        if ($attribute->getBackend()->getType() == 'static') {
            if (!isset($describe[$attribute->getAttributeCode()])) {
                return $this;
            }

            $select = $adapter->select()
                ->join(
                    array('main_table' => $this->getTable('score/oggetto')),
                    'main_table.entity_id = e.entity_id',
                    array($attribute->getAttributeCode() => 'main_table.' . $attribute->getAttributeCode())
                );
            if ($this->getFlatHelper()->isAddChildData()) {
                $select->where('e.is_child = ?', 0);
            }
            if ($oggettoIds !== null) {
                $select->where('main_table.entity_id IN(?)', $oggettoIds);
            }

            $sql = $select->crossUpdateFromSelect(array('e' => $flatTableName));
            $adapter->query($sql);
        } else {
            $columns = $attribute->getFlatColumns();
            if (!$columns) {
                return $this;
            }
            foreach (array_keys($columns) as $columnName) {
                if (!isset($describe[$columnName])) {
                    return $this;
                }
            }

            $select = $attribute->getFlatUpdateSelect($storeId);
            if ($select instanceof Varien_Db_Select) {
                if ($oggettoIds !== null) {
                    $select->where('e.entity_id IN(?)', $oggettoIds);
                }

                $sql = $select->crossUpdateFromSelect(array('e' => $flatTableName));
                $adapter->query($sql);
            }
        }

        return $this;
    }

    /**
     * Update non static EAV attributes flat data
     *
     * @param int $storeId
     * @param int|array $oggettoIds update only oggetto(s)
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function updateEavAttributes($storeId, $oggettoIds = null)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        foreach ($this->getAttributes() as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if ($attribute->getBackend()->getType() != 'static') {
                $this->updateAttribute($attribute, $storeId, $oggettoIds);
            }
        }
        return $this;
    }

    /**
     * Update events observer attributes
     *
     * @param int $storeId
     */
    public function updateEventAttributes($storeId = null)
    {
        Mage::dispatchEvent('score_oggetto_flat_rebuild', array(
            'store_id' => $storeId,
            'table'    => $this->getFlatTableName($storeId)
        ));
    }

    /**
     * Retrieve Oggetto Type Instances
     * as key - type code, value - instance model
     *
     * @return array
     */
    public function getOggettoTypeInstances()
    {
        if ($this->_oggettoTypes === null) {
            $this->_oggettoTypes = array();
            $oggettoEmulator     = new Varien_Object();

            foreach (array_keys(Shaurmalab_Score_Model_Oggetto_Type::getTypes()) as $typeId) {
                $oggettoEmulator->setTypeId($typeId);
                $this->_oggettoTypes[$typeId] = Mage::getSingleton('score/oggetto_type')
                    ->factory($oggettoEmulator);
            }
        }
        return $this->_oggettoTypes;
    }

    /**
     * Update relation oggettos
     *
     * @param int $storeId
     * @param int|array $oggettoIds Update child oggetto(s) only
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function updateRelationOggettos($storeId, $oggettoIds = null)
    {
        if (!$this->getFlatHelper()->isAddChildData() || !$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        $adapter = $this->_getWriteAdapter();

        foreach ($this->getOggettoTypeInstances() as $typeInstance) {
            if (!$typeInstance->isComposite()) {
                continue;
            }
            $relation = $typeInstance->getRelationInfo();
            if ($relation
                && $relation->getTable()
                && $relation->getParentFieldName()
                && $relation->getChildFieldName()
            ) {
                $columns    = $this->getFlatColumns();
                $fieldList  = array_keys($columns);
                unset($columns['entity_id']);
                unset($columns['child_id']);
                unset($columns['is_child']);

                $select = $adapter->select()
                    ->from(
                        array('t' => $this->getTable($relation->getTable())),
                        array($relation->getParentFieldName(), $relation->getChildFieldName(), new Zend_Db_Expr('1')))
                    ->join(
                        array('e' => $this->getFlatTableName($storeId)),
                        "e.entity_id = t.{$relation->getChildFieldName()}",
                        array_keys($columns)
                    );
                if ($relation->getWhere() !== null) {
                    $select->where($relation->getWhere());
                }
                if ($oggettoIds !== null) {
                    $cond = array(
                        $adapter->quoteInto("{$relation->getChildFieldName()} IN(?)", $oggettoIds),
                        $adapter->quoteInto("{$relation->getParentFieldName()} IN(?)", $oggettoIds)
                    );

                    $select->where(implode(' OR ', $cond));
                }
                $sql = $select->insertFromSelect($this->getFlatTableName($storeId), $fieldList);
                $adapter->query($sql);
            }
        }

        return $this;
    }

    /**
     * Update children data from parent
     *
     * @param int $storeId
     * @param int|array $oggettoIds
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function updateChildrenDataFromParent($storeId, $oggettoIds = null)
    {
        if (!$this->getFlatHelper()->isAddChildData() || !$this->_isFlatTableExists($storeId)) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();

        $select = $adapter->select();
        foreach (array_keys($this->getFlatColumns()) as $columnName) {
            if ($columnName == 'entity_id' || $columnName == 'child_id' || $columnName == 'is_child') {
                continue;
            }
            $select->columns(array($columnName => new Zend_Db_Expr('t1.' . $columnName)));
        }
        $select
            ->joinLeft(
                array('t1' => $this->getFlatTableName($storeId)),
                $adapter->quoteInto('t2.child_id = t1.entity_id AND t1.is_child = ?', 0),
                array())
            ->where('t2.is_child = ?', 1);

        if ($oggettoIds !== null) {
            $select->where('t2.child_id IN(?)', $oggettoIds);
        }

        $sql = $select->crossUpdateFromSelect(array('t2' => $this->getFlatTableName($storeId)));
        $adapter->query($sql);

        return $this;
    }

    /**
     * Clean unused relation oggettos
     *
     * @param int $storeId
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function cleanRelationOggettos($storeId)
    {
        if (!$this->getFlatHelper()->isAddChildData()) {
            return $this;
        }

        foreach ($this->getOggettoTypeInstances() as $typeInstance) {
            if (!$typeInstance->isComposite()) {
                continue;
            }
            $adapter  = $this->_getWriteAdapter();
            $relation = $typeInstance->getRelationInfo();
            if ($relation
                && $relation->getTable()
                && $relation->getParentFieldName()
                && $relation->getChildFieldName()
            ) {
                $select = $this->_getWriteAdapter()->select()
                    ->distinct(true)
                    ->from(
                        $this->getTable($relation->getTable()),
                        "{$relation->getParentFieldName()}"
                    );
                $joinLeftCond = array(
                    "e.entity_id = t.{$relation->getParentFieldName()}",
                    "e.child_id = t.{$relation->getChildFieldName()}"
                );
                if ($relation->getWhere() !== null) {
                    $select->where($relation->getWhere());
                    $joinLeftCond[] = $relation->getWhere();
                }

                $entitySelect = new Zend_Db_Expr($select->__toString());

                $select = $adapter->select()
                    ->from(array('e' => $this->getFlatTableName($storeId)), null)
                    ->joinLeft(
                        array('t' => $this->getTable($relation->getTable())),
                        implode(' AND ', $joinLeftCond),
                        array())
                    ->where('e.is_child = ?', 1)
                    ->where('e.entity_id IN(?)', $entitySelect)
                    ->where("t.{$relation->getChildFieldName()} IS NULL");

                $sql = $select->deleteFromSelect('e');
                $adapter->query($sql);
            }
        }

        return $this;
    }

    /**
     * Remove oggetto data from flat
     *
     * @param int|array $oggettoIds
     * @param int $storeId
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function removeOggetto($oggettoIds, $storeId)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }
        $adapter = $this->_getWriteAdapter();
        $cond = array(
            $adapter->quoteInto('entity_id IN(?)', $oggettoIds)
        );
        if ($this->getFlatHelper()->isAddChildData()) {
            $cond[] = $adapter->quoteInto('child_id IN(?)', $oggettoIds);
        }
        $cond = implode(' OR ', $cond);
        $adapter->delete($this->getFlatTableName($storeId), $cond);

        return $this;
    }

    /**
     * Remove children from parent oggetto
     *
     * @param int|array $oggettoIds
     * @param int $storeId
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function removeOggettoChildren($oggettoIds, $storeId)
    {
        if (!$this->getFlatHelper()->isAddChildData()) {
            return $this;
        }
        $whereExpr = array(
            'entity_id IN(?)' => $oggettoIds,
            'is_child = ?'    => 1
        );
        $this->_getWriteAdapter()->delete($this->getFlatTableName($storeId), $whereExpr);

        return $this;
    }

    /**
     * Update flat data for oggetto
     *
     * @param int|array $oggettoIds
     * @param int $storeId
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function updateOggetto($oggettoIds, $storeId)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        $this->saveOggetto($oggettoIds, $storeId);

        Mage::dispatchEvent('score_oggetto_flat_update_oggetto', array(
            'store_id'      => $storeId,
            'table'         => $this->getFlatTableName($storeId),
            'oggetto_ids'   => $oggettoIds
        ));

        return $this;
    }

    /**
     * Save oggetto(s) data for store
     *
     * @param int|array $oggettoIds
     * @param int $storeId
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function saveOggetto($oggettoIds, $storeId)
    {
        if (!$this->_isFlatTableExists($storeId)) {
            return $this;
        }

        $this->updateStaticAttributes($storeId, $oggettoIds);
        $this->updateEavAttributes($storeId, $oggettoIds);

        return $this;
    }

    /**
     * Delete flat table process
     *
     * @param int $storeId
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function deleteFlatTable($storeId)
    {
        if ($this->_isFlatTableExists($storeId)) {
            $this->_getWriteAdapter()->dropTable($this->getFlatTableName($storeId));
        }

        return $this;
    }

    /**
     * Check is flat table for store exists
     *
     * @param int $storeId
     * @return bool
     */
    protected function _isFlatTableExists($storeId)
    {
        if (!isset($this->_existsFlatTables[$storeId])) {
            $tableName     = $this->getFlatTableName($storeId);
            $isTableExists = $this->_getWriteAdapter()->isTableExists($tableName);

            $this->_existsFlatTables[$storeId] = $isTableExists ? true : false;
        }

        return $this->_existsFlatTables[$storeId];
    }

    /**
     * Retrieve previous key from array by key
     *
     * @param array $array
     * @param mixed $key
     * @return mixed
     */
    protected function _arrayPrevKey(array $array, $key)
    {
        $prev = false;
        foreach (array_keys($array) as $k) {
            if ($k == $key) {
                return $prev;
            }
            $prev = $k;
        }
        return false;
    }

    /**
     * Retrieve next key from array by key
     *
     * @param array $array
     * @param mixed $key
     * @return mixed
     */
    protected function _arrayNextKey(array $array, $key)
    {
        $next = false;
        foreach (array_keys($array) as $k) {
            if ($next === true) {
                return $k;
            }
            if ($k == $key) {
                $next = true;
            }
        }
        return false;
    }

    /**
     * Transactional rebuild Score Oggetto Flat Data
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer
     */
    public function reindexAll()
    {
        foreach (Mage::app()->getStores() as $storeId => $store) {
            $this->prepareFlatTable($storeId);
            $this->beginTransaction();
            try {
                $this->rebuild($store);
                $this->commit();
           } catch (Exception $e) {
                $this->rollBack();
                throw $e;
           }
        }

        return $this;
    }
}
