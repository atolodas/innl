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
 * Oggetto entity resource model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Oggetto extends Shaurmalab_Score_Model_Resource_Abstract
{
    /**
     * Oggetto to website linkage table
     *
     * @var string
     */
    protected $_oggettoWebsiteTable;

    /**
     * Oggetto to category linkage table
     *
     * @var string
     */
    protected $_oggettoCategoryTable;

    /**
     * Initialize resource
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType(Shaurmalab_Score_Model_Oggetto::ENTITY)
             ->setConnection('score_read', 'score_write');
        $this->_oggettoWebsiteTable  = $this->getTable('score/oggetto_website');
        $this->_oggettoCategoryTable = $this->getTable('score/category_oggetto');
    }

    /**
     * Default oggetto attributes
     *
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        return array('entity_id', 'entity_type_id', 'attribute_set_id', 'type_id', 'created_at', 'updated_at');
    }

    /**
     * Retrieve oggetto website identifiers
     *
     * @param Shaurmalab_Score_Model_Oggetto|int $oggetto
     * @return array
     */
    public function getWebsiteIds($oggetto)
    {
        $adapter = $this->_getReadAdapter();

        if ($oggetto instanceof Shaurmalab_Score_Model_Oggetto) {
            $oggettoId = $oggetto->getId();
        } else {
            $oggettoId = $oggetto;
        }

        $select = $adapter->select()
            ->from($this->_oggettoWebsiteTable, 'website_id')
            ->where('oggetto_id = ?', (int)$oggettoId);

        return $adapter->fetchCol($select);
    }

    /**
     * Retrieve oggetto website identifiers by oggetto identifiers
     *
     * @param   array $oggettoIds
     * @return  array
     */
    public function getWebsiteIdsByOggettoIds($oggettoIds)
    {
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_oggettoWebsiteTable, array('oggetto_id', 'website_id'))
            ->where('oggetto_id IN (?)', $oggettoIds);
        $oggettosWebsites = array();
        foreach ($this->_getWriteAdapter()->fetchAll($select) as $oggettoInfo) {
            $oggettoId = $oggettoInfo['oggetto_id'];
            if (!isset($oggettosWebsites[$oggettoId])) {
                $oggettosWebsites[$oggettoId] = array();
            }
            $oggettosWebsites[$oggettoId][] = $oggettoInfo['website_id'];

        }

        return $oggettosWebsites;
    }

    /**
     * Retrieve oggetto category identifiers
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getCategoryIds($oggetto)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->_oggettoCategoryTable, 'category_id')
            ->where('oggetto_id = ?', (int)$oggetto->getId());

        return $adapter->fetchCol($select);
    }

    /**
     * Get oggetto identifier by sku
     *
     * @param string $sku
     * @return int|false
     */
    public function getIdBySku($sku)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getEntityTable(), 'entity_id')
            ->where('sku = :sku');

        $bind = array(':sku' => (string)$sku);

        return $adapter->fetchOne($select, $bind);
    }

    /**
     * Process oggetto data before save
     *
     * @param Varien_Object $object
     * @return Shaurmalab_Score_Model_Resource_Oggetto
     */
    protected function _beforeSave(Varien_Object $object)
    {
        /**
         * Try detect oggetto id by sku if id is not declared
         */
        if (!$object->getId() && $object->getSku()) {
            $object->setId($this->getIdBySku($object->getSku()));
        }

        /**
         * Check if declared category ids in object data.
         */
        if ($object->hasCategoryIds()) {
            $categoryIds = Mage::getResourceSingleton('score/category')->verifyIds(
                $object->getCategoryIds()
            );
            $object->setCategoryIds($categoryIds);
        }

        return parent::_beforeSave($object);
    }

    /**
     * Save data related with oggetto
     *
     * @param Varien_Object $oggetto
     * @return Shaurmalab_Score_Model_Resource_Oggetto
     */
    protected function _afterSave(Varien_Object $oggetto)
    {
        $this->_saveWebsiteIds($oggetto)
            ->_saveCategories($oggetto);

        return parent::_afterSave($oggetto);
    }

    /**
     * Save oggetto website relations
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Resource_Oggetto
     */
    protected function _saveWebsiteIds($oggetto)
    {
        $websiteIds = $oggetto->getWebsiteIds();
        $oldWebsiteIds = array();

        $oggetto->setIsChangedWebsites(false);

        $adapter = $this->_getWriteAdapter();

        $oldWebsiteIds = $this->getWebsiteIds($oggetto);

        $insert = array_diff($websiteIds, $oldWebsiteIds);
        $delete = array_diff($oldWebsiteIds, $websiteIds);

        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $websiteId) {
                $data[] = array(
                    'oggetto_id' => (int)$oggetto->getId(),
                    'website_id' => (int)$websiteId
                );
            }
            $adapter->insertMultiple($this->_oggettoWebsiteTable, $data);
        }

        if (!empty($delete)) {
            foreach ($delete as $websiteId) {
                $condition = array(
                    'oggetto_id = ?' => (int)$oggetto->getId(),
                    'website_id = ?' => (int)$websiteId,
                );

                $adapter->delete($this->_oggettoWebsiteTable, $condition);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $oggetto->setIsChangedWebsites(true);
        }

        return $this;
    }

    /**
     * Save oggetto category relations
     *
     * @param Varien_Object $object
     * @return Shaurmalab_Score_Model_Resource_Oggetto
     */
    protected function _saveCategories(Varien_Object $object)
    {
        /**
         * If category ids data is not declared we haven't do manipulations
         */
        if (!$object->hasCategoryIds()) {
            return $this;
        }
        $categoryIds = $object->getCategoryIds();
        $oldCategoryIds = $this->getCategoryIds($object);

        $object->setIsChangedCategories(false);

        $insert = array_diff($categoryIds, $oldCategoryIds);
        $delete = array_diff($oldCategoryIds, $categoryIds);

        $write = $this->_getWriteAdapter();
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $categoryId) {
                if (empty($categoryId)) {
                    continue;
                }
                $data[] = array(
                    'category_id' => (int)$categoryId,
                    'oggetto_id'  => (int)$object->getId(),
                    'position'    => 1
                );
            }
            if ($data) {
                $write->insertMultiple($this->_oggettoCategoryTable, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $categoryId) {
                $where = array(
                    'oggetto_id = ?'  => (int)$object->getId(),
                    'category_id = ?' => (int)$categoryId,
                );

                $write->delete($this->_oggettoCategoryTable, $where);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $object->setAffectedCategoryIds(array_merge($insert, $delete));
            $object->setIsChangedCategories(true);
        }

        return $this;
    }

    /**
     * Refresh Oggetto Enabled Index
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Resource_Oggetto
     */
    public function refreshIndex($oggetto)
    {
        $writeAdapter = $this->_getWriteAdapter();

        /**
         * Ids of all categories where oggetto is assigned (not related with store)
         */
        $categoryIds = $oggetto->getCategoryIds();

        /**
         * Clear previous index data related with oggetto
         */
        $condition = array('oggetto_id = ?' => (int)$oggetto->getId());
        $writeAdapter->delete($this->getTable('score/category_oggetto_index'), $condition);

        /** @var $categoryObject Shaurmalab_Score_Model_Resource_Category */
        $categoryObject = Mage::getResourceSingleton('score/category');
        if (!empty($categoryIds)) {
            $categoriesSelect = $writeAdapter->select()
                ->from($this->getTable('score/category'))
                ->where('entity_id IN (?)', $categoryIds);

            $categoriesInfo = $writeAdapter->fetchAll($categoriesSelect);

            $indexCategoryIds = array();
            foreach ($categoriesInfo as $categoryInfo) {
                $ids = explode('/', $categoryInfo['path']);
                $ids[] = $categoryInfo['entity_id'];
                $indexCategoryIds = array_merge($indexCategoryIds, $ids);
            }

            $indexCategoryIds   = array_unique($indexCategoryIds);
            $indexOggettoIds    = array($oggetto->getId());

           $categoryObject->refreshOggettoIndex($indexCategoryIds, $indexOggettoIds);
        } else {
            $websites = $oggetto->getWebsiteIds();

            if ($websites) {
                $storeIds = array();

                foreach ($websites as $websiteId) {
                    $website  = Mage::app()->getWebsite($websiteId);
                    $storeIds = array_merge($storeIds, $website->getStoreIds());
                }

                $categoryObject->refreshOggettoIndex(array(), array($oggetto->getId()), $storeIds);
            }
        }

        /**
         * Refresh enabled oggettos index (visibility state)
         */
        $this->refreshEnabledIndex(null, $oggetto);

        return $this;
    }

    /**
     * Refresh index for visibility of enabled oggetto in store
     * if store parameter is null - index will refreshed for all stores
     * if oggetto parameter is null - idex will be refreshed for all oggettos
     *
     * @param Mage_Core_Model_Store $store
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @throws Mage_Core_Exception
     * @return Shaurmalab_Score_Model_Resource_Oggetto
     */
    public function refreshEnabledIndex($store = null, $oggetto = null)
    {
        $statusAttribute        = $this->getAttribute('status');
        $visibilityAttribute    = $this->getAttribute('visibility');
        $statusAttributeId      = $statusAttribute->getId();
        $visibilityAttributeId  = $visibilityAttribute->getId();
        $statusTable            = $statusAttribute->getBackend()->getTable();
        $visibilityTable        = $visibilityAttribute->getBackend()->getTable();

        $adapter = $this->_getWriteAdapter();

        $select = $adapter->select();
        $condition = array();

        $indexTable = $this->getTable('score/oggetto_enabled_index');
        if (is_null($store) && is_null($oggetto)) {
            Mage::throwException(
                Mage::helper('score')->__('To reindex the enabled oggetto(s), the store or oggetto must be specified')
            );
        } elseif (is_null($oggetto) || is_array($oggetto)) {
            $storeId    = $store->getId();
            $websiteId  = $store->getWebsiteId();

            if (is_array($oggetto) && !empty($oggetto)) {
                $condition[] = $adapter->quoteInto('oggetto_id IN (?)', $oggetto);
            }

            $condition[] = $adapter->quoteInto('store_id = ?', $storeId);

            $selectFields = array(
                't_v_default.entity_id',
                new Zend_Db_Expr($storeId),
                $adapter->getCheckSql('t_v.value_id > 0', 't_v.value', 't_v_default.value'),
            );

            $select->joinInner(
                array('w' => $this->getTable('score/oggetto_website')),
                $adapter->quoteInto(
                    'w.oggetto_id = t_v_default.entity_id AND w.website_id = ?', $websiteId
                ),
                array()
            );
        } elseif ($store === null) {
            foreach ($oggetto->getStoreIds() as $storeId) {
                $store = Mage::app()->getStore($storeId);
                $this->refreshEnabledIndex($store, $oggetto);
            }
            return $this;
        } else {
            $oggettoId = is_numeric($oggetto) ? $oggetto : $oggetto->getId();
            $storeId   = is_numeric($store) ? $store : $store->getId();

            $condition = array(
                'oggetto_id = ?' => (int)$oggettoId,
                'store_id   = ?' => (int)$storeId,
            );

            $selectFields = array(
                new Zend_Db_Expr($oggettoId),
                new Zend_Db_Expr($storeId),
                $adapter->getCheckSql('t_v.value_id > 0', 't_v.value', 't_v_default.value')
            );

            $select->where('t_v_default.entity_id = ?', $oggettoId);
        }

        $adapter->delete($indexTable, $condition);

        $select->from(array('t_v_default' => $visibilityTable), $selectFields);

        $visibilityTableJoinCond = array(
            't_v.entity_id = t_v_default.entity_id',
            $adapter->quoteInto('t_v.attribute_id = ?', $visibilityAttributeId),
            $adapter->quoteInto('t_v.store_id     = ?', $storeId),
        );

        $select->joinLeft(
            array('t_v' => $visibilityTable),
            implode(' AND ', $visibilityTableJoinCond),
            array()
        );

        $defaultStatusJoinCond = array(
            't_s_default.entity_id = t_v_default.entity_id',
            't_s_default.store_id = 0',
            $adapter->quoteInto('t_s_default.attribute_id = ?', $statusAttributeId),
        );

        $select->joinInner(
            array('t_s_default' => $statusTable),
            implode(' AND ', $defaultStatusJoinCond),
            array()
        );


        $statusJoinCond = array(
            't_s.entity_id = t_v_default.entity_id',
            $adapter->quoteInto('t_s.store_id     = ?', $storeId),
            $adapter->quoteInto('t_s.attribute_id = ?', $statusAttributeId),
        );

        $select->joinLeft(
            array('t_s' => $statusTable),
            implode(' AND ', $statusJoinCond),
            array()
        );

        $valueCondition = $adapter->getCheckSql('t_s.value_id > 0', 't_s.value', 't_s_default.value');

        $select->where('t_v_default.attribute_id = ?', $visibilityAttributeId)
            ->where('t_v_default.store_id = ?', 0)
            ->where(sprintf('%s = ?', $valueCondition), Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED);

        if (is_array($oggetto) && !empty($oggetto)) {
            $select->where('t_v_default.entity_id IN (?)', $oggetto);
        }

        $adapter->query($adapter->insertFromSelect($select, $indexTable));


        return $this;
    }

    /**
     * Get collection of oggetto categories
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function getCategoryCollection($oggetto)
    {
        $collection = Mage::getResourceModel('score/category_collection')
            ->joinField('oggetto_id',
                'score/category_oggetto',
                'oggetto_id',
                'category_id = entity_id',
                null)
            ->addFieldToFilter('oggetto_id', (int)$oggetto->getId());
        return $collection;
    }

    /**
     * Retrieve category ids where oggetto is available
     *
     * @param Shaurmalab_Score_Model_Oggetto $object
     * @return array
     */
    public function getAvailableInCategories($object)
    {
        // is_parent=1 ensures that we'll get only category IDs those are direct parents of the oggetto, instead of
        // fetching all parent IDs, including those are higher on the tree
        $select = $this->_getReadAdapter()->select()->distinct()
            ->from($this->getTable('score/category_oggetto_index'), array('category_id'))
            ->where('oggetto_id = ? AND is_parent = 1', (int)$object->getEntityId());

        return $this->_getReadAdapter()->fetchCol($select);
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

    /**
     * Check availability display oggetto in category
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param int $categoryId
     * @return string
     */
    public function canBeShowInCategory($oggetto, $categoryId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('score/category_oggetto_index'), 'oggetto_id')
            ->where('oggetto_id = ?', (int)$oggetto->getId())
            ->where('category_id = ?', (int)$categoryId);

        return $this->_getReadAdapter()->fetchOne($select);
    }

    /**
     * Duplicate oggetto store values
     *
     * @param int $oldId
     * @param int $newId
     * @return Shaurmalab_Score_Model_Resource_Oggetto
     */
    public function duplicate($oldId, $newId)
    {
        $adapter = $this->_getWriteAdapter();
        $eavTables = array('datetime', 'decimal', 'int', 'text', 'varchar');

        $adapter = $this->_getWriteAdapter();

        // duplicate EAV store values
        foreach ($eavTables as $suffix) {
            $tableName = $this->getTable(array('score/oggetto', $suffix));

            $select = $adapter->select()
                ->from($tableName, array(
                    'entity_type_id',
                    'attribute_id',
                    'store_id',
                    'entity_id' => new Zend_Db_Expr($adapter->quote($newId)),
                    'value'
                ))
                ->where('entity_id = ?', $oldId)
                ->where('store_id > ?', 0);

            $adapter->query($adapter->insertFromSelect(
                $select,
                $tableName,
                array(
                    'entity_type_id',
                    'attribute_id',
                    'store_id',
                    'entity_id',
                    'value'
                ),
                Varien_Db_Adapter_Interface::INSERT_ON_DUPLICATE
            ));
        }

        // set status as disabled
        $statusAttribute      = $this->getAttribute('status');
        $statusAttributeId    = $statusAttribute->getAttributeId();
        $statusAttributeTable = $statusAttribute->getBackend()->getTable();
        $updateCond[]         = 'store_id > 0';
        $updateCond[]         = $adapter->quoteInto('entity_id = ?', $newId);
        $updateCond[]         = $adapter->quoteInto('attribute_id = ?', $statusAttributeId);
        $adapter->update(
            $statusAttributeTable,
            array('value' => Shaurmalab_Score_Model_Oggetto_Status::STATUS_DISABLED),
            $updateCond
        );

        return $this;
    }

    /**
     * Get SKU through oggetto identifiers
     *
     * @param  array $oggettoIds
     * @return array
     */
    public function getOggettosSku(array $oggettoIds)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('score/oggetto'), array('entity_id', 'sku'))
            ->where('entity_id IN (?)', $oggettoIds);
        return $this->_getReadAdapter()->fetchAll($select);
    }

    /**
     * @deprecated after 1.4.2.0
     * @param  $object Shaurmalab_Score_Model_Oggetto
     * @return array
     */
    public function getParentOggettoIds($object)
    {
        return array();
    }

    /**
     * Retrieve oggetto entities info
     *
     * @param  array|string|null $columns
     * @return array
     */
    public function getOggettoEntitiesInfo($columns = null)
    {
        if (!empty($columns) && is_string($columns)) {
            $columns = array($columns);
        }
        if (empty($columns) || !is_array($columns)) {
            $columns = $this->_getDefaultAttributes();
        }

        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('score/oggetto'), $columns);

        return $adapter->fetchAll($select);
    }

    /**
     * Return assigned images for specific stores
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param int|array $storeIds
     * @return array
     *
     */
    public function getAssignedImages($oggetto, $storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }

        $mainTable = $oggetto->getResource()->getAttribute('image')
            ->getBackend()
            ->getTable();
        $read      = $this->_getReadAdapter();
        $select    = $read->select()
            ->from(
                array('images' => $mainTable),
                array('value as filepath', 'store_id')
            )
            ->joinLeft(
                array('attr' => $this->getTable('eav/attribute')),
                'images.attribute_id = attr.attribute_id',
                array('attribute_code')
            )
            ->where('entity_id = ?', $oggetto->getId())
            ->where('store_id IN (?)', $storeIds)
            ->where('attribute_code IN (?)', array('small_image', 'thumbnail', 'image'));

        $images = $read->fetchAll($select);
        return $images;
    }
}
