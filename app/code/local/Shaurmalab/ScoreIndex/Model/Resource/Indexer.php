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
 * @package     Shaurmalab_ScoreIndex
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reindexer resource model
 *
 * @category    Mage
 * @package     Shaurmalab_ScoreIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_ScoreIndex_Model_Resource_Indexer extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_insertData       = array();

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_tableFields      = array();

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_attributeCache   = array();

    /**
     * Enter description here ...
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto', 'entity_id');
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $id
     * @return unknown
     */
    protected function _loadAttribute($id)
    {
        if (!isset($this->_attributeCache[$id])) {
            $this->_attributeCache[$id] = Mage::getModel('eav/entity_attribute')->load($id);
        }

        return $this->_attributeCache[$id];
    }

    /**
     * Delete index data by specific conditions
     *
     * @param bool $eav clear eav index data flag
     * @param bool $price clear price index data flag
     * @param bool $minimal clear minimal price index data flag
     * @param bool $finalPrice clear final price index data flag
     * @param bool $tierPrice clear tier price index data flag
     * @param mixed $oggettos applicable oggettos
     * @param mixed $store applicable stores
     */
    public function clear($eav = true, $price = true, $minimal = true, $finalPrice = true, $tierPrice = true,
        $oggettos = null, $store = null)
    {
        $suffix = '';
        $priceSuffix = '';
        $tables = array('eav'=>'scoreindex/eav', 'price'=>'scoreindex/price');
        if (!is_null($oggettos)) {
            if ($oggettos instanceof Shaurmalab_Score_Model_Oggetto) {
                $oggettos = $oggettos->getId();
            } elseif ($oggettos instanceof Shaurmalab_Score_Model_Oggetto_Condition_Interface) {
                $suffix = 'entity_id IN ('.$oggettos->getIdsSelect($this->_getWriteAdapter())->__toString().')';
            }
            else if (!is_numeric($oggettos) && !is_array($oggettos)) {
                Mage::throwException('Invalid oggettos supplied for indexing');
            }
            if (empty($suffix)) {
                $suffix = $this->_getWriteAdapter()->quoteInto('entity_id in (?)', $oggettos);
            }
        }
        if (!is_null($store)) {
            $websiteIds = array();

            if ($store instanceof Mage_Core_Model_Store) {
                $store = $store->getId();
                $websiteIds[] = Mage::app()->getStore($store)->getWebsiteId();
            } else if ($store instanceof Mage_Core_Model_Resource_Store_Collection) {
                $store = $store->getAllIds();
                foreach ($store as $one) {
                    $websiteIds[] = Mage::app()->getStore($one)->getWebsiteId();
                }
            } else if (is_array($store)) {
                $resultStores = array();
                foreach ($store as $s) {
                    if ($s instanceof Mage_Core_Model_Store) {
                        $resultStores[] = $s->getId();
                        $websiteIds[] = $s->getWebsiteId();
                    } elseif (is_numeric($s)) {
                        $websiteIds[] = Mage::app()->getStore($s)->getWebsiteId();
                        $resultStores[] = $s;
                    }
                }
                $store = $resultStores;
            }

            if ($suffix) {
                $suffix .= ' AND ';
            }

            $priceSuffix = $suffix . $this->_getWriteAdapter()->quoteInto('website_id in (?)', $websiteIds);
            $suffix .= $this->_getWriteAdapter()->quoteInto('store_id in (?)', $store);

        }

        if ($tierPrice) {
            $tables['tierPrice'] = 'scoreindex/price';
            $tierPrice = array(Mage::getSingleton('eav/entity_attribute')->getIdByCode(Shaurmalab_Score_Model_Oggetto::ENTITY, 'tier_price'));
        }
        if ($finalPrice) {
            $tables['finalPrice'] = 'scoreindex/price';
            $tierPrice = array(Mage::getSingleton('eav/entity_attribute')->getIdByCode(Shaurmalab_Score_Model_Oggetto::ENTITY, 'price'));
        }
        if ($minimal) {
            $tables['minimal'] = 'scoreindex/minimal_price';
        }


        foreach ($tables as $variable=>$table) {
            $variable = $$variable;
            $suffixToInsert = $suffix;
            if (in_array($table, $this->_getPriceTables())) {
                $suffixToInsert = $priceSuffix;
            }

            if ($variable === true) {
                $query = "DELETE FROM {$this->getTable($table)} ";
                if ($suffixToInsert) {
                    $query .= "WHERE {$suffixToInsert}";
                }

                $this->_getWriteAdapter()->query($query);
            } else if (is_array($variable) && count($variable)) {
                $query  = "DELETE FROM {$this->getTable($table)} WHERE ";
                $query .= $this->_getWriteAdapter()->quoteInto("attribute_id in (?)", $variable);
                if ($suffixToInsert) {
                    $query .= " AND {$suffixToInsert}";
                }

                $this->_getWriteAdapter()->query($query);
            }
        }
    }

    /**
     * Get tables which are used for index related with price
     *
     * @return array
     */
    protected function _getPriceTables()
    {
        return array('scoreindex/price', 'scoreindex/minimal_price');
    }

    /**
     * Reindex data for tier prices
     *
     * @param array $oggettos array of oggetto ids
     * @param Mage_Core_Model_Store $store
     * @param int | null $forcedId identifier of "parent" oggetto
     * @return Shaurmalab_ScoreIndex_Model_Resource_Indexer
     */
    public function reindexTiers($oggettos, $store, $forcedId = null)
    {
        $websiteId = $store->getWebsiteId();
        $attribute = Mage::getSingleton('eav/entity_attribute')->getIdByCode(Shaurmalab_Score_Model_Oggetto::ENTITY, 'tier_price');
        $this->_beginInsert(
            'scoreindex/price',
            array('entity_id', 'attribute_id', 'value', 'website_id', 'customer_group_id', 'qty')
        );

        /**
         * Get information about oggetto types
         * array (
         *      $oggettoType => array()
         * )
         */
        $oggettos = Mage::getSingleton('scoreindex/retreiver')->assignOggettoTypes($oggettos);
        if (is_null($forcedId)) {
            foreach ($oggettos as $type=>$typeIds) {
                $retreiver = Mage::getSingleton('scoreindex/retreiver')->getRetreiver($type);
                if ($retreiver->areChildrenIndexable(Shaurmalab_ScoreIndex_Model_Retreiver::CHILDREN_FOR_TIERS)) {
                    foreach ($typeIds as $id) {
                        $children = $retreiver->getChildOggettoIds($store, $id);
                        if ($children) {
                            $this->reindexTiers($children, $store, $id);
                        }
                    }
                }
            }
        }

        $attributeIndex = $this->getTierData($oggettos, $store);
        foreach ($attributeIndex as $index) {
            $type = $index['type_id'];
            $id = (is_null($forcedId) ? $index['entity_id'] : $forcedId);
            if ($id && $index['value']) {
                if ($index['all_groups'] == 1) {
                    foreach (Mage::getSingleton('scoreindex/retreiver')->getCustomerGroups() as $group) {
                        $this->_insert('scoreindex/price', array(
                            $id,
                            $attribute,
                            $index['value'],
                            $websiteId,
                            (int) $group->getId(),
                            (int) $index['qty']
                        ));
                    }
                } else {
                    $this->_insert('scoreindex/price', array(
                        $id,
                        $attribute,
                        $index['value'],
                        $websiteId,
                        (int) $index['customer_group_id'],
                        (int) $index['qty']
                    ));
                }
            }
        }
        $this->_commitInsert('scoreindex/price');
        return $this;
    }

    /**
     * Reindex oggetto prices
     *
     * @param array | int $oggettos oggetto ids
     * @param array $attributeIds
     * @param Mage_Core_Model_Store $store
     * @return Shaurmalab_ScoreIndex_Model_Resource_Indexer
     */
    public function reindexPrices($oggettos, $attributeIds, $store)
    {
        $this->reindexAttributes($oggettos, $attributeIds, $store, null, 'scoreindex/price', true);
        return $this;
    }

    /**
     * Reindex oggetto final prices
     *
     * @param array $oggettos array of oggetto ids
     * @param Mage_Core_Model_Store $store
     * @param int | null $forcedId identifier of "parent" oggetto
     * @return Shaurmalab_ScoreIndex_Model_Resource_Indexer
     */
    public function reindexFinalPrices($oggettos, $store, $forcedId = null)
    {
        $priceAttribute = Mage::getSingleton('eav/entity_attribute')->getIdByCode(Shaurmalab_Score_Model_Oggetto::ENTITY, 'price');
        $this->_beginInsert('scoreindex/price', array(
            'entity_id',
            'website_id',
            'customer_group_id',
            'value',
            'attribute_id',
            'tax_class_id'
        ));

        $oggettoTypes = Mage::getSingleton('scoreindex/retreiver')->assignOggettoTypes($oggettos);
        foreach ($oggettoTypes as $type=>$oggettos) {
            $retreiver = Mage::getSingleton('scoreindex/retreiver')->getRetreiver($type);
            foreach ($oggettos as $oggetto) {
                if (is_null($forcedId)) {
                    if ($retreiver->areChildrenIndexable(Shaurmalab_ScoreIndex_Model_Retreiver::CHILDREN_FOR_PRICES)) {
                        $children = $retreiver->getChildOggettoIds($store, $oggetto);
                        if ($children) {
                            $this->reindexFinalPrices($children, $store, $oggetto);
                        }
                    }
                }
                foreach (Mage::getSingleton('scoreindex/retreiver')->getCustomerGroups() as $group) {
                    $finalPrice = $retreiver->getFinalPrice($oggetto, $store, $group);
                    $taxClassId = $retreiver->getTaxClassId($oggetto, $store);
                    $id = $oggetto;
                    if (!is_null($forcedId)) {
                        $id = $forcedId;
                    }

                    if (false !== $finalPrice && false !== $id && false !== $priceAttribute) {
                        $this->_insert('scoreindex/price', array(
                            $id,
                            $store->getWebsiteId(),
                            $group->getId(),
                            $finalPrice,
                            $priceAttribute,
                            $taxClassId
                        ));
                    }
                }
            }
        }
        $this->_commitInsert('scoreindex/price');
        return $this;
    }

    /**
     * Reindex oggetto minimal prices
     *
     * @param array $oggettos array of oggetto ids
     * @param Mage_Core_Model_Store $store
     * @return Shaurmalab_ScoreIndex_Model_Resource_Indexer
     */
    public function reindexMinimalPrices($oggettos, $store)
    {
        $this->_beginInsert('scoreindex/minimal_price', array(
            'website_id',
            'entity_id',
            'customer_group_id',
            'value',
            'tax_class_id'
        ));
        $this->clear(false, false, true, false, false, $oggettos, $store);
        $oggettos = Mage::getSingleton('scoreindex/retreiver')->assignOggettoTypes($oggettos);

        foreach ($oggettos as $type=>$typeIds) {
            $retreiver = Mage::getSingleton('scoreindex/retreiver')->getRetreiver($type);

            foreach ($typeIds as $id) {
                $minimal = array();
                if ($retreiver->areChildrenIndexable(Shaurmalab_ScoreIndex_Model_Retreiver::CHILDREN_FOR_PRICES)) {
                    $children = $retreiver->getChildOggettoIds($store, $id);
                    if ($children) {
                        $minimal = $this->getMinimalPrice(array($type=>$children), $store);
                    }
                } else {
                    $minimal = $this->getMinimalPrice(array($type=>array($id)), $store);
                }

                if (is_array($minimal)) {
                    foreach ($minimal as $price) {
                        if (!isset($price['tax_class_id'])) {
                            $price['tax_class_id'] = 0;
                        }
                        $this->_insert('scoreindex/minimal_price', array(
                            $store->getWebsiteId(),
                            $id,
                            $price['customer_group_id'],
                            $price['minimal_value'],
                            $price['tax_class_id']
                        ));
                    }
                }
            }
        }

        $this->_commitInsert('scoreindex/minimal_price');
        return $this;
    }

    /**
     * Reindex attributes data
     *
     * @param array $oggettos
     * @param array $attributeIds
     * @param mixed $store
     * @param int|null $forcedId
     * @param string $table
     * @param bool $storeIsWebsite
     * @return Shaurmalab_ScoreIndex_Model_Resource_Indexer
     */
    public function reindexAttributes($oggettos, $attributeIds, $store, $forcedId = null, $table = 'scoreindex/eav',
        $storeIsWebsite = false)
    {
        $storeField = 'store_id';
        $websiteId = null;
        if ($storeIsWebsite) {
            $storeField = 'website_id';
            if ($store instanceof Mage_Core_Model_Store) {
                $websiteId = $store->getWebsiteId();
            } else {
                $websiteId = Mage::app()->getStore($store)->getWebsiteId();
            }
        }

        $this->_beginInsert($table, array('entity_id', 'attribute_id', 'value', $storeField));

        $oggettos = Mage::getSingleton('scoreindex/retreiver')->assignOggettoTypes($oggettos);

        if (is_null($forcedId)) {
            foreach ($oggettos as $type=>$typeIds) {
                $retreiver = Mage::getSingleton('scoreindex/retreiver')->getRetreiver($type);
                if ($retreiver->areChildrenIndexable(Shaurmalab_ScoreIndex_Model_Retreiver::CHILDREN_FOR_ATTRIBUTES)) {
                    foreach ($typeIds as $id) {
                        $children = $retreiver->getChildOggettoIds($store, $id);
                        if ($children) {
                            $this->reindexAttributes($children, $attributeIds, $store, $id, $table, $storeIsWebsite);
                        }
                    }
                }
            }
        }

        $attributeIndex = $this->getOggettoData($oggettos, $attributeIds, $store);
        foreach ($attributeIndex as $index) {
            $type = $index['type_id'];
            $id = (is_null($forcedId) ? $index['entity_id'] : $forcedId);

            if ($id && $index['attribute_id'] && isset($index['value'])) {
                $attribute = $this->_loadAttribute($index['attribute_id']);
                if ($attribute->getFrontendInput() == 'multiselect') {
                    $index['value'] = explode(',', $index['value']);
                }

                if (is_array($index['value'])) {
                    foreach ($index['value'] as $value) {
                        $this->_insert($table, array(
                            $id,
                            $index['attribute_id'],
                            $value,
                            (is_null($websiteId) ? $store->getId() : $websiteId)
                        ));
                    }
                } else {
                    $this->_insert($table, array(
                        $id,
                        $index['attribute_id'],
                        $index['value'],
                        (is_null($websiteId) ? $store->getId() : $websiteId)
                    ));
                }
            }
        }

        $this->_commitInsert($table);
        return $this;
    }

    /**
     * Get tier prices data by set of oggettos
     *
     * @param array $oggettos
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getTierData($oggettos, $store)
    {
        $result = array();
        foreach ($oggettos as $type=>$typeIds) {
            $retreiver = Mage::getSingleton('scoreindex/retreiver')->getRetreiver($type);
            $byType = $retreiver->getTierPrices($typeIds, $store);
            if ($byType) {
                $result = array_merge($result, $byType);
            }
        }
        return $result;
    }

    /**
     * Get minimal prices by set of the oggettos
     *
     * @param arary $oggettos
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getMinimalPrice($oggettos, $store)
    {
        $result = array();
        foreach ($oggettos as $type=>$typeIds) {
            $retreiver = Mage::getSingleton('scoreindex/retreiver')->getRetreiver($type);
            $byType = $retreiver->getMinimalPrice($typeIds, $store);
            if ($byType) {
                $result = array_merge($result, $byType);
            }
        }
        return $result;
    }

    /**
     * Get data for oggettos
     *
     * @param array $oggettos
     * @param array $attributeIds
     * @param Mage_Core_Model_Store $store
     * @return array
     */
    public function getOggettoData($oggettos, $attributeIds, $store)
    {
        $result = array();
        foreach ($oggettos as $type=>$typeIds) {
            $retreiver = Mage::getSingleton('scoreindex/retreiver')->getRetreiver($type);
            $byType = $retreiver->getAttributeData($typeIds, $attributeIds, $store);
            if ($byType) {
                $result = array_merge($result, $byType);
            }
        }
        return $result;
    }

    /**
     * Prepare base information for data insert
     *
     * @param string $table
     * @param array $fields
     * @return Shaurmalab_ScoreIndex_Model_Resource_Indexer
     */
    protected function _beginInsert($table, $fields)
    {
        $this->_tableFields[$table] = $fields;
        return $this;
    }

    /**
     * Put data into table
     *
     * @param string $table
     * @param bool $forced
     * @return Shaurmalab_ScoreIndex_Model_Resource_Indexer
     */
    protected function _commitInsert($table, $forced = true)
    {
        if (isset($this->_insertData[$table]) && count($this->_insertData[$table]) && ($forced || count($this->_insertData[$table]) >= 100)) {
            $query = 'REPLACE INTO ' . $this->getTable($table) . ' (' . implode(', ', $this->_tableFields[$table]) . ') VALUES ';
            $separator = '';
            foreach ($this->_insertData[$table] as $row) {
                $rowString = $this->_getWriteAdapter()->quoteInto('(?)', $row);
                $query .= $separator . $rowString;
                $separator = ', ';
            }
            $this->_getWriteAdapter()->query($query);
            $this->_insertData[$table] = array();
        }
        return $this;
    }

    /**
     * Insert data to table
     *
     * @param string $table
     * @param array $data
     * @return Shaurmalab_ScoreIndex_Model_Resource_Indexer
     */
    protected function _insert($table, $data)
    {
        $this->_insertData[$table][] = $data;
        $this->_commitInsert($table, false);
        return $this;
    }

    /**
     * Add price columns for score oggetto flat table
     *
     * @param Varien_Object $object
     * @return Shaurmalab_ScoreIndex_Model_Resource_Indexer
     */
    public function prepareScoreOggettoFlatColumns(Varien_Object $object)
    {
        $columns = $object->getColumns();

        foreach (Mage::getSingleton('scoreindex/retreiver')->getCustomerGroups() as $group) {
            $columnName = 'display_price_group_' . $group->getId();
            $columns[$columnName] = array(
                'type'      => 'decimal(12,4)',
                'unsigned'  => false,
                'nullable'   => true,
                'default'   => null,
                'extra'     => null,
                'comment'   => $columnName . ' column'
            );
        }

        $object->setColumns($columns);

        return $this;
    }

    /**
     * Add price indexes for score oggetto flat table
     *
     * @param Varien_Object $object
     * @return Shaurmalab_ScoreIndex_Model_Resource_Indexer
     */
    public function prepareScoreOggettoFlatIndexes(Varien_Object $object)
    {
        $indexes = $object->getIndexes();

        foreach (Mage::getSingleton('scoreindex/retreiver')->getCustomerGroups() as $group) {
            $columnName = 'display_price_group_' . $group->getId();
            $indexName  = 'IDX_DISPLAY_PRICE_GROUP_' . $group->getId();
            $indexes[$indexName] = array(
                'type'   => 'index',
                'fields' => array($columnName)
            );
        }

        $object->setIndexes($indexes);

        return $this;
    }

    /**
     * Update prices for Score Oggetto flat
     *
     * @param int $storeId
     * @param unknown_type $oggettoIds
     * @param string $tableName
     * @return Shaurmalab_ScoreIndex_Model_Resource_Indexer
     */
    public function updateScoreOggettoFlat($storeId, $oggettoIds = null, $tableName = null)
    {
        if (is_null($tableName)) {
            $tableName = $this->getTable('score/oggetto_flat') . '_' . $storeId;
        }
        $addChildData = Mage::helper('score/oggetto_flat')->isAddChildData();

        $priceAttribute = Mage::getSingleton('eav/entity_attribute')
            ->getIdByCode(Shaurmalab_Score_Model_Oggetto::ENTITY, 'price');
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();

        foreach (Mage::getSingleton('scoreindex/retreiver')->getCustomerGroups() as $group) {
            $columnName = 'display_price_group_' . $group->getId();

            /**
             * Update prices of main oggettos in flat table
             */
            $select = $this->_getWriteAdapter()->select()
                ->join(
                    array('p' => $this->getTable('scoreindex/price')),
                    "`e`.`entity_id`=`p`.`entity_id`"
                        . " AND `p`.`attribute_id`={$priceAttribute}"
                        . " AND `p`.`customer_group_id`={$group->getId()}"
                        . " AND `p`.`website_id`={$websiteId}",
                    array($columnName => 'value'));
            if ($addChildData) {
                $select->where('e.is_child=?', 0);
            }

            if ($oggettoIds instanceof Shaurmalab_Score_Model_Oggetto_Condition_Interface) {
                $select->where('e.entity_id IN ('.$oggettoIds->getIdsSelect($this->_getWriteAdapter())->__toString().')');
            } elseif (!is_null($oggettoIds)) {
                $select->where("e.entity_id IN(?)", $oggettoIds);
            }

            $sql = $select->crossUpdateFromSelect(array('e' => $tableName));
            $this->_getWriteAdapter()->query($sql);

            if ($addChildData) {
                /**
                 * Update prices for children oggettos in flat table
                 */
                $select = $this->_getWriteAdapter()->select()
                    ->join(
                        array('p' => $this->getTable('scoreindex/price')),
                        "`e`.`child_id`=`p`.`entity_id`"
                            . " AND `p`.`attribute_id`={$priceAttribute}"
                            . " AND `p`.`customer_group_id`={$group->getId()}"
                            . " AND `p`.`website_id`={$websiteId}",
                        array($columnName => 'value'))
                    ->where('e.is_child=?', 1);

                if ($oggettoIds instanceof Shaurmalab_Score_Model_Oggetto_Condition_Interface) {
                    $select->where('e.child_id IN ('.$oggettoIds->getIdsSelect($this->_getWriteAdapter())->__toString().')');
                } elseif (!is_null($oggettoIds)) {
                    $select->where("e.child_id IN(?)", $oggettoIds);
                }

                $sql = $select->crossUpdateFromSelect(array('e' => $tableName));
                $this->_getWriteAdapter()->query($sql);
            }

        }

        return $this;
    }
}
