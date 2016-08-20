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
 * Score url rewrite resource model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Url extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Stores configuration array
     *
     * @var array
     */
    protected $_stores;

    /**
     * Category attribute properties cache
     *
     * @var array
     */
    protected $_categoryAttributes          = array();

    /**
     * Oggetto attribute properties cache
     *
     * @var array
     */
    protected $_oggettoAttributes           = array();

    /**
     * Limit oggettos for select
     *
     * @var int
     */
    protected $_oggettoLimit                = 250;

    /**
     * Cache of root category children ids
     *
     * @var array
     */
    protected $_rootChildrenIds             = array();

    /**
     * Load core Url rewrite model
     *
     */
    protected function _construct()
    {
        $this->_init('core/url_rewrite', 'url_rewrite_id');
    }

    /**
     * Retrieve stores array or store model
     *
     * @param int $storeId
     * @return Mage_Core_Model_Store|array
     */
    public function getStores($storeId = null)
    {
        if ($this->_stores === null) {
            $this->_stores = $this->_prepareStoreRootCategories(Mage::app()->getStores());
        }
        if ($storeId && isset($this->_stores[$storeId])) {
            return $this->_stores[$storeId];
        }
        return $this->_stores;
    }

    /**
     * Retrieve Category model singleton
     *
     * @return Shaurmalab_Score_Model_Category
     */
    public function getCategoryModel()
    {
        return Mage::getSingleton('score/category');
    }

    /**
     * Retrieve oggetto model singleton
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggettoModel()
    {
        return Mage::getSingleton('score/oggetto');
    }

    /**
     * Retrieve rewrite by idPath
     *
     * @param string $idPath
     * @param int $storeId
     * @return Varien_Object|false
     */
    public function getRewriteByIdPath($idPath, $storeId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('store_id = :store_id')
            ->where('id_path = :id_path');
        $bind = array(
            'store_id' => (int)$storeId,
            'id_path'  => $idPath
        );
        $row = $adapter->fetchRow($select, $bind);

        if (!$row) {
            return false;
        }
        $rewrite = new Varien_Object($row);
        $rewrite->setIdFieldName($this->getIdFieldName());

        return $rewrite;
    }

    /**
     * Retrieve rewrite by requestPath
     *
     * @param string $requestPath
     * @param int $storeId
     * @return Varien_Object|false
     */
    public function getRewriteByRequestPath($requestPath, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('store_id = :store_id')
            ->where('request_path = :request_path');
        $bind = array(
            'request_path'  => $requestPath,
            'store_id'      => (int)$storeId
        );
        $row = $adapter->fetchRow($select, $bind);

        if (!$row) {
            return false;
        }
        $rewrite = new Varien_Object($row);
        $rewrite->setIdFieldName($this->getIdFieldName());

        return $rewrite;
    }

    /**
     * Get last used increment part of rewrite request path
     *
     * @param string $prefix
     * @param string $suffix
     * @param int $storeId
     * @return int
     */
    public function getLastUsedRewriteRequestIncrement($prefix, $suffix, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $requestPathField = new Zend_Db_Expr($adapter->quoteIdentifier('request_path'));
        //select increment part of request path and cast expression to integer
        $urlIncrementPartExpression = Mage::getResourceHelper('eav')
            ->getCastToIntExpression($adapter->getSubstringSql(
                $requestPathField,
                strlen($prefix) + 1,
                $adapter->getLengthSql($requestPathField) . ' - ' . strlen($prefix) . ' - ' . strlen($suffix)
            ));
        $select = $adapter->select()
            ->from($this->getMainTable(), new Zend_Db_Expr('MAX(' . $urlIncrementPartExpression . ')'))
            ->where('store_id = :store_id')
            ->where('request_path LIKE :request_path')
            ->where($adapter->prepareSqlCondition('request_path', array(
                'regexp' => '^' . preg_quote($prefix) . '[0-9]*' . preg_quote($suffix) . '$'
            )));
        $bind = array(
            'store_id'            => (int)$storeId,
            'request_path'        => $prefix . '%' . $suffix,
        );

        return (int)$adapter->fetchOne($select, $bind);
    }

    /**
     * Validate array of request paths. Return first not used path in case if validations passed
     *
     * @param array $paths
     * @param int $storeId
     * @return false | string
     */
    public function checkRequestPaths($paths, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'request_path')
            ->where('store_id = :store_id')
            ->where('request_path IN (?)', $paths);
        $data = $adapter->fetchCol($select, array('store_id' => $storeId));
        $paths = array_diff($paths, $data);
        if (empty($paths)) {
            return false;
        }
        reset($paths);

        return current($paths);
    }

    /**
     * Prepare rewrites for condition
     *
     * @param int $storeId
     * @param int|array $categoryIds
     * @param int|array $oggettoIds
     * @return array
     */
    public function prepareRewrites($storeId, $categoryIds = null, $oggettoIds = null)
    {
        $rewrites   = array();
        $adapter    = $this->_getWriteAdapter();
        $select     = $adapter->select()
            ->from($this->getMainTable())
            ->where('store_id = :store_id')
            ->where('is_system = ?', 1);
        $bind = array('store_id' => $storeId);
        if ($categoryIds === null) {
            $select->where('category_id IS NULL');
        } elseif ($categoryIds) {
            $catIds = is_array($categoryIds) ? $categoryIds : array($categoryIds);

            // Check maybe we request oggettos and root category id is within categoryIds,
            // it's a separate case because root category oggettos are stored with NULL categoryId
            if ($oggettoIds) {
                $addNullCategory = in_array($this->getStores($storeId)->getRootCategoryId(), $catIds);
            } else {
                $addNullCategory = false;
            }

            // Compose optimal condition
            if ($addNullCategory) {
                $select->where('category_id IN(?) OR category_id IS NULL', $catIds);
            } else {
                $select->where('category_id IN(?)', $catIds);
            }
        }

        if ($oggettoIds === null) {
            $select->where('oggetto_id IS NULL');
        } elseif ($oggettoIds) {
            $select->where('oggetto_id IN(?)', $oggettoIds);
        }

        $rowSet = $adapter->fetchAll($select, $bind);

        foreach ($rowSet as $row) {
            $rewrite = new Varien_Object($row);
            $rewrite->setIdFieldName($this->getIdFieldName());
            $rewrites[$rewrite->getIdPath()] = $rewrite;
        }

        return $rewrites;
    }

    /**
     * Save rewrite URL
     *
     * @param array $rewriteData
     * @param int|Varien_Object $rewrite
     * @return Shaurmalab_Score_Model_Resource_Url
     */
    public function saveRewrite($rewriteData, $rewrite)
    {
        $adapter = $this->_getWriteAdapter();
        try {
            $adapter->insertOnDuplicate($this->getMainTable(), $rewriteData);
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::throwException(Mage::helper('score')->__('An error occurred while saving the URL rewrite'));
        }

        if ($rewrite && $rewrite->getId()) {
            if ($rewriteData['request_path'] != $rewrite->getRequestPath()) {
                // Update existing rewrites history and avoid chain redirects
                $where = array('target_path = ?' => $rewrite->getRequestPath());
                if ($rewrite->getStoreId()) {
                    $where['store_id = ?'] = (int)$rewrite->getStoreId();
                }
                $adapter->update(
                    $this->getMainTable(),
                    array('target_path' => $rewriteData['request_path']),
                    $where
                );
            }
        }
        unset($rewriteData);

        return $this;
    }

    /**
     * Saves rewrite history
     *
     * @param array $rewriteData
     * @return Shaurmalab_Score_Model_Resource_Url
     */
    public function saveRewriteHistory($rewriteData)
    {
        $rewriteData = new Varien_Object($rewriteData);
        // check if rewrite exists with save request_path
        $rewrite = $this->getRewriteByRequestPath($rewriteData->getRequestPath(), $rewriteData->getStoreId());
        if ($rewrite === false) {
            // create permanent redirect
            $this->_getWriteAdapter()->insert($this->getMainTable(), $rewriteData->getData());
        }

        return $this;
    }

    /**
     * Save category attribute
     *
     * @param Varien_Object $category
     * @param string $attributeCode
     * @return Shaurmalab_Score_Model_Resource_Url
     */
    public function saveCategoryAttribute(Varien_Object $category, $attributeCode)
    {
        $adapter = $this->_getWriteAdapter();
        if (!isset($this->_categoryAttributes[$attributeCode])) {
            $attribute = $this->getCategoryModel()->getResource()->getAttribute($attributeCode);

            $this->_categoryAttributes[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal()
            );
            unset($attribute);
        }

        $attributeTable = $this->_categoryAttributes[$attributeCode]['table'];

        $attributeData = array(
            'entity_type_id'    => $this->_categoryAttributes[$attributeCode]['entity_type_id'],
            'attribute_id'      => $this->_categoryAttributes[$attributeCode]['attribute_id'],
            'store_id'          => $category->getStoreId(),
            'entity_id'         => $category->getId(),
            'value'             => $category->getData($attributeCode)
        );

        if ($this->_categoryAttributes[$attributeCode]['is_global'] || $category->getStoreId() == 0) {
            $attributeData['store_id'] = 0;
        }

        $select = $adapter->select()
            ->from($attributeTable)
            ->where('entity_type_id = ?', (int)$attributeData['entity_type_id'])
            ->where('attribute_id = ?', (int)$attributeData['attribute_id'])
            ->where('store_id = ?', (int)$attributeData['store_id'])
            ->where('entity_id = ?', (int)$attributeData['entity_id']);

        $row = $adapter->fetchRow($select);
        $whereCond = array('value_id = ?' => $row['value_id']);
        if ($row) {
            $adapter->update($attributeTable, $attributeData, $whereCond);
        } else {
            $adapter->insert($attributeTable, $attributeData);
        }

        if ($attributeData['store_id'] != 0) {
            $attributeData['store_id'] = 0;
            $select = $adapter->select()
                ->from($attributeTable)
                ->where('entity_type_id = ?', (int)$attributeData['entity_type_id'])
                ->where('attribute_id = ?', (int)$attributeData['attribute_id'])
                ->where('store_id = ?', (int)$attributeData['store_id'])
                ->where('entity_id = ?', (int)$attributeData['entity_id']);

            $row = $adapter->fetchRow($select);
            if ($row) {
                $whereCond = array('value_id = ?' => $row['value_id']);
                $adapter->update($attributeTable, $attributeData, $whereCond);
            } else {
                $adapter->insert($attributeTable, $attributeData);
            }
        }
        unset($attributeData);

        return $this;
    }

    /**
     * Retrieve category attributes
     *
     * @param string $attributeCode
     * @param int|array $categoryIds
     * @param int $storeId
     * @return array
     */
    protected function _getCategoryAttribute($attributeCode, $categoryIds, $storeId)
    {
        $adapter = $this->_getWriteAdapter();
        if (!isset($this->_categoryAttributes[$attributeCode])) {
            $attribute = $this->getCategoryModel()->getResource()->getAttribute($attributeCode);

            $this->_categoryAttributes[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal(),
                'is_static'      => $attribute->isStatic()
            );
            unset($attribute);
        }

        if (!is_array($categoryIds)) {
            $categoryIds = array($categoryIds);
        }

        $attributeTable = $this->_categoryAttributes[$attributeCode]['table'];
        $select         = $adapter->select();
        $bind           = array();
        if ($this->_categoryAttributes[$attributeCode]['is_static']) {
            $select
                ->from(
                    $this->getTable('score/category'),
                    array('value' => $attributeCode, 'entity_id' => 'entity_id')
                )
                ->where('entity_id IN(?)', $categoryIds);
        } elseif ($this->_categoryAttributes[$attributeCode]['is_global'] || $storeId == 0) {
            $select
                ->from($attributeTable, array('entity_id', 'value'))
                ->where('attribute_id = :attribute_id')
                ->where('store_id = ?', 0)
                ->where('entity_id IN(?)', $categoryIds);
            $bind['attribute_id'] = $this->_categoryAttributes[$attributeCode]['attribute_id'];
        } else {
            $valueExpr = $adapter->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');
            $select
                ->from(
                    array('t1' => $attributeTable),
                    array('entity_id', 'value' => $valueExpr)
                )
                ->joinLeft(
                    array('t2' => $attributeTable),
                    't1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id = :store_id',
                    array()
                )
                ->where('t1.store_id = ?', 0)
                ->where('t1.attribute_id = :attribute_id')
                ->where('t1.entity_id IN(?)', $categoryIds);

            $bind['attribute_id'] = $this->_categoryAttributes[$attributeCode]['attribute_id'];
            $bind['store_id']     = $storeId;
        }

        $rowSet = $adapter->fetchAll($select, $bind);

        $attributes = array();
        foreach ($rowSet as $row) {
            $attributes[$row['entity_id']] = $row['value'];
        }
        unset($rowSet);
        foreach ($categoryIds as $categoryId) {
            if (!isset($attributes[$categoryId])) {
                $attributes[$categoryId] = null;
            }
        }

        return $attributes;
    }

    /**
     * Save oggetto attribute
     *
     * @param Varien_Object $oggetto
     * @param string $attributeCode
     * @return Shaurmalab_Score_Model_Resource_Url
     */
    public function saveOggettoAttribute(Varien_Object $oggetto, $attributeCode)
    {
        $adapter = $this->_getWriteAdapter();
        if (!isset($this->_oggettoAttributes[$attributeCode])) {
            $attribute = $this->getOggettoModel()->getResource()->getAttribute($attributeCode);

            $this->_oggettoAttributes[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal()
            );
            unset($attribute);
        }

        $attributeTable = $this->_oggettoAttributes[$attributeCode]['table'];

        $attributeData = array(
            'entity_type_id'    => $this->_oggettoAttributes[$attributeCode]['entity_type_id'],
            'attribute_id'      => $this->_oggettoAttributes[$attributeCode]['attribute_id'],
            'store_id'          => $oggetto->getStoreId(),
            'entity_id'         => $oggetto->getId(),
            'value'             => $oggetto->getData($attributeCode)
        );

        if ($this->_oggettoAttributes[$attributeCode]['is_global'] || $oggetto->getStoreId() == 0) {
            $attributeData['store_id'] = 0;
        }

        $select = $adapter->select()
            ->from($attributeTable)
            ->where('entity_type_id = ?', (int)$attributeData['entity_type_id'])
            ->where('attribute_id = ?', (int)$attributeData['attribute_id'])
            ->where('store_id = ?', (int)$attributeData['store_id'])
            ->where('entity_id = ?', (int)$attributeData['entity_id']);

        $row = $adapter->fetchRow($select);
        if ($row) {
            $whereCond = array('value_id = ?' => $row['value_id']);
            $adapter->update($attributeTable, $attributeData, $whereCond);
        } else {
            $adapter->insert($attributeTable, $attributeData);
        }

        if ($attributeData['store_id'] != 0) {
            $attributeData['store_id'] = 0;
            $select = $adapter->select()
                ->from($attributeTable)
                ->where('entity_type_id = ?', (int)$attributeData['entity_type_id'])
                ->where('attribute_id = ?', (int)$attributeData['attribute_id'])
                ->where('store_id = ?', (int)$attributeData['store_id'])
                ->where('entity_id = ?', (int)$attributeData['entity_id']);

            $row = $adapter->fetchRow($select);
            if ($row) {
                $whereCond = array('value_id = ?' => $row['value_id']);
                $adapter->update($attributeTable, $attributeData, $whereCond);
            } else {
                $adapter->insert($attributeTable, $attributeData);
            }
        }
        unset($attributeData);

        return $this;
    }

    /**
     * Retrieve oggetto attribute
     *
     * @param string $attributeCode
     * @param int|array $oggettoIds
     * @param string $storeId
     * @return array
     */
    public function _getOggettoAttribute($attributeCode, $oggettoIds, $storeId)
    {
        $adapter = $this->_getReadAdapter();
        if (!isset($this->_oggettoAttributes[$attributeCode])) {
            $attribute = $this->getOggettoModel()->getResource()->getAttribute($attributeCode);

            $this->_oggettoAttributes[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id'   => $attribute->getId(),
                'table'          => $attribute->getBackend()->getTable(),
                'is_global'      => $attribute->getIsGlobal()
            );
            unset($attribute);
        }

        if (!is_array($oggettoIds)) {
            $oggettoIds = array($oggettoIds);
        }
        $bind = array('attribute_id' => $this->_oggettoAttributes[$attributeCode]['attribute_id']);
        $select = $adapter->select();
        $attributeTable = $this->_oggettoAttributes[$attributeCode]['table'];
        if ($this->_oggettoAttributes[$attributeCode]['is_global'] || $storeId == 0) {
            $select
                ->from($attributeTable, array('entity_id', 'value'))
                ->where('attribute_id = :attribute_id')
                ->where('store_id = ?', 0)
                ->where('entity_id IN(?)', $oggettoIds);
        } else {
            $valueExpr = $adapter->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');
            $select
                ->from(
                    array('t1' => $attributeTable),
                    array('entity_id', 'value' => $valueExpr)
                )
                ->joinLeft(
                    array('t2' => $attributeTable),
                    't1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id=:store_id',
                    array()
                )
                ->where('t1.store_id = ?', 0)
                ->where('t1.attribute_id = :attribute_id')
                ->where('t1.entity_id IN(?)', $oggettoIds);
            $bind['store_id'] = $storeId;
        }

        $rowSet = $adapter->fetchAll($select, $bind);

        $attributes = array();
        foreach ($rowSet as $row) {
            $attributes[$row['entity_id']] = $row['value'];
        }
        unset($rowSet);
        foreach ($oggettoIds as $oggettoIds) {
            if (!isset($attributes[$oggettoIds])) {
                $attributes[$oggettoIds] = null;
            }
        }

        return $attributes;
    }

    /**
     * Prepare category parentId
     *
     * @param Varien_Object $category
     * @return Shaurmalab_Score_Model_Resource_Url
     */
    protected function _prepareCategoryParentId(Varien_Object $category)
    {
        if ($category->getPath() != $category->getId()) {
            $split = explode('/', $category->getPath());
            $category->setParentId($split[(count($split) - 2)]);
        } else {
            $category->setParentId(0);
        }
        return $this;
    }

    /**
     * Prepare stores root categories
     *
     * @param array $stores
     * @return array
     */
    protected function _prepareStoreRootCategories($stores)
    {
        $rootCategoryIds = array();
        foreach ($stores as $store) {
            /* @var $store Mage_Core_Model_Store */
            $rootCategoryIds[$store->getRootCategoryId()] = $store->getRootCategoryId();
        }
        if ($rootCategoryIds) {
            $categories = $this->_getCategories($rootCategoryIds);
        }
        foreach ($stores as $store) {
            /* @var $store Mage_Core_Model_Store */
            $rootCategoryId = $store->getRootCategoryId();
            if (isset($categories[$rootCategoryId])) {
                $store->setRootCategoryPath($categories[$rootCategoryId]->getPath());
                $store->setRootCategory($categories[$rootCategoryId]);
            } else {
                unset($stores[$store->getId()]);
            }
        }
        return $stores;
    }

    /**
     * Retrieve categories objects
     * Either $categoryIds or $path (with ending slash) must be specified
     *
     * @param int|array $categoryIds
     * @param int $storeId
     * @param string $path
     * @return array
     */
    protected function _getCategories($categoryIds, $storeId = null, $path = null)
    {
        $isActiveAttribute = Mage::getSingleton('eav/config')
            ->getAttribute(Shaurmalab_Score_Model_Category::ENTITY, 'is_active');
        $categories        = array();
        $adapter           = $this->_getReadAdapter();

        if (!is_array($categoryIds)) {
            $categoryIds = array($categoryIds);
        }
        $isActiveExpr = $adapter->getCheckSql('c.value_id > 0', 'c.value', 'c.value');
        $select = $adapter->select()
            ->from(array('main_table' => $this->getTable('score/category')), array(
                'main_table.entity_id',
                'main_table.parent_id',
                'main_table.level',
                'is_active' => $isActiveExpr,
                'main_table.path'));

        // Prepare variables for checking whether categories belong to store
        if ($path === null) {
            $select->where('main_table.entity_id IN(?)', $categoryIds);
        } else {
            // Ensure that path ends with '/', otherwise we can get wrong results - e.g. $path = '1/2' will get '1/20'
            if (substr($path, -1) != '/') {
                $path .= '/';
            }

            $select
                ->where('main_table.path LIKE ?', $path . '%')
                ->order('main_table.path');
        }
        $table = $this->getTable(array('score/category', 'int'));
        $select->joinLeft(array('d' => $table),
            'd.attribute_id = :attribute_id AND d.store_id = 0 AND d.entity_id = main_table.entity_id',
            array()
        )
        ->joinLeft(array('c' => $table),
            'c.attribute_id = :attribute_id AND c.store_id = :store_id AND c.entity_id = main_table.entity_id',
            array()
        );

        if ($storeId !== null) {
            $rootCategoryPath = '/';
            $rootCategoryPathLength = strlen($rootCategoryPath);
        }
        $bind = array(
            'attribute_id' => (int)$isActiveAttribute->getId(),
            'store_id'     => (int)$storeId
        );

        $rowSet = $adapter->fetchAll($select, $bind);
        foreach ($rowSet as $row) {
            if ($storeId !== null) {
                // Check the category to be either store's root or its descendant
                // First - check that category's start is the same as root category
                if (substr($row['path'], 0, $rootCategoryPathLength) != $rootCategoryPath) {
                    continue;
                }
                // Second - check non-root category - that it's really a descendant, not a simple string match
                if ((strlen($row['path']) > $rootCategoryPathLength)
                    && ($row['path'][$rootCategoryPathLength] != '/')) {
                    continue;
                }
            }

            $category = new Varien_Object($row);
            $category->setIdFieldName('entity_id');
            $category->setStoreId($storeId);
            $this->_prepareCategoryParentId($category);

            $categories[$category->getId()] = $category;
        }
        unset($rowSet);

        if ($storeId !== null && $categories) {
            foreach (array('name', 'url_key', 'url_path') as $attributeCode) {
                $attributes = $this->_getCategoryAttribute($attributeCode, array_keys($categories),
                    $category->getStoreId());
                foreach ($attributes as $categoryId => $attributeValue) {
                    $categories[$categoryId]->setData($attributeCode, $attributeValue);
                }
            }
        }

        return $categories;
    }

    /**
     * Retrieve category data object
     *
     * @param int $categoryId
     * @param int $storeId
     * @return Varien_Object
     */
    public function getCategory($categoryId, $storeId)
    {
        if (!$categoryId || !$storeId) {
            return false;
        }

        $categories = $this->_getCategories($categoryId, $storeId);
        if (isset($categories[$categoryId])) {
            return $categories[$categoryId];
        }
        return false;
    }

    /**
     * Retrieve categories data objects by their ids. Return only categories that belong to specified store.
     *
     * @param int|array $categoryIds
     * @param int $storeId
     * @return array
     */
    public function getCategories($categoryIds, $storeId)
    {
        if (!$categoryIds || !$storeId) {
          //  return false;
        }

        return $this->_getCategories($categoryIds, $storeId);
    }

    /**
     * Retrieve category childs data objects
     *
     * @param Varien_Object $category
     * @return Varien_Object
     */
    public function loadCategoryChilds(Varien_Object $category)
    {
        if ($category->getId() === null || $category->getStoreId() === null) {
            return $category;
        }

        $categories = $this->_getCategories(null, $category->getStoreId(), $category->getPath() . '/');
        $category->setChilds(array());
        foreach ($categories as $child) {
            if (!is_array($child->getChilds())) {
                $child->setChilds(array());
            }
            if ($child->getParentId() == $category->getId()) {
                $category->setChilds($category->getChilds() + array($child->getId() => $child));
            } else {
                if (isset($categories[$child->getParentId()])) {
                    if (!is_array($categories[$child->getParentId()]->getChilds())) {
                        $categories[$child->getParentId()]->setChilds(array());
                    }
                    $categories[$child->getParentId()]->setChilds(
                        $categories[$child->getParentId()]->getChilds() + array($child->getId() => $child)
                    );
                }
            }
        }
        $category->setAllChilds($categories);

        return $category;
    }

    /**
     * Retrieves all children ids of root category tree
     * Actually this routine can be used to get children ids of any category, not only root.
     * But as far as result is cached in memory, it's not recommended to do so.
     *
     * @param Varien_Object $category
     * @return Varien_Object
     */
    public function getRootChildrenIds($categoryId, $categoryPath, $includeStart = true)
    {
        if (!isset($this->_rootChildrenIds[$categoryId])) {
            // Select all descedant category ids
            $adapter = $this->_getReadAdapter();
            $select = $adapter->select()
                ->from(array($this->getTable('score/category')), array('entity_id'))
                ->where('path LIKE ?', $categoryPath . '/%');

            $categoryIds = array();
            $rowSet = $adapter->fetchAll($select);
            foreach ($rowSet as $row) {
                $categoryIds[$row['entity_id']] = $row['entity_id'];
            }
            $this->_rootChildrenIds[$categoryId] = $categoryIds;
        }

        $categoryIds = $this->_rootChildrenIds[$categoryId];
        if ($includeStart) {
            $categoryIds[$categoryId] = $categoryId;
        }
        return $categoryIds;
    }

    /**
     * Retrieve category parent path
     *
     * @param Varien_Object $category
     * @return string
     */
    public function getCategoryParentPath(Varien_Object $category)
    {
        $store = Mage::app()->getStore($category->getStoreId());

        if ($category->getId() == $store->getRootCategoryId()) {
            return '';
        } elseif ($category->getParentId() == 1 || $category->getParentId() == $store->getRootCategoryId()) {
            return '';
        }

        $parentCategory = $this->getCategory($category->getParentId(), $store->getId());
        return $parentCategory->getUrlPath() . '/';
    }

    /**
     * Retrieve oggetto ids by category
     *
     * @param Varien_Object|int $category
     * @return array
     */
    public function getOggettoIdsByCategory($category)
    {
        if ($category instanceof Varien_Object) {
            $categoryId = $category->getId();
        } else {
            $categoryId = $category;
        }
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getTable('score/category_oggetto'), array('oggetto_id'))
            ->where('category_id = :category_id')
            ->order('oggetto_id');
        $bind = array('category_id' => $categoryId);

        return $adapter->fetchCol($select, $bind);
    }

    /**
     * Retrieve Oggetto data objects
     *
     * @param int|array $oggettoIds
     * @param int $storeId
     * @param int $entityId
     * @param int $lastEntityId
     * @return array
     */
    protected function _getOggettos($oggettoIds, $storeId, $entityId, &$lastEntityId)
    {
        $oggettos   = array();
        $websiteId  = Mage::app()->getStore($storeId)->getWebsiteId();
        $adapter    = $this->_getReadAdapter();
        if ($oggettoIds !== null) {
            if (!is_array($oggettoIds)) {
                $oggettoIds = array($oggettoIds);
            }
        }
        $bind = array(
            'website_id' => (int)$websiteId,
            'entity_id'  => (int)$entityId,
        );
        $select = $adapter->select()
            ->useStraightJoin(true)
            ->from(array('e' => $this->getTable('score/oggetto')), array('entity_id'))
            ->join(
                array('w' => $this->getTable('score/oggetto_website')),
                'e.entity_id = w.oggetto_id AND w.website_id = :website_id',
                array()
            )
            ->where('e.entity_id > :entity_id')
            ->order('e.entity_id')
            ->limit($this->_oggettoLimit);
        if ($oggettoIds !== null) {
            $select->where('e.entity_id IN(?)', $oggettoIds);
        }

        $rowSet = $adapter->fetchAll($select, $bind);
        foreach ($rowSet as $row) {
            $oggetto = new Varien_Object($row);
            $oggetto->setIdFieldName('entity_id');
            $oggetto->setCategoryIds(array());
            $oggetto->setStoreId($storeId);
            $oggettos[$oggetto->getId()] = $oggetto;
            $lastEntityId = $oggetto->getId();
        }

        unset($rowSet);

        if ($oggettos) {
            $select = $adapter->select()
                ->from(
                    $this->getTable('score/category_oggetto'),
                    array('oggetto_id', 'category_id')
                )
                ->where('oggetto_id IN(?)', array_keys($oggettos));
            $categories = $adapter->fetchAll($select);
            foreach ($categories as $category) {
                $oggettoId = $category['oggetto_id'];
                $categoryIds = $oggettos[$oggettoId]->getCategoryIds();
                $categoryIds[] = $category['category_id'];
                $oggettos[$oggettoId]->setCategoryIds($categoryIds);
            }

            foreach (array('name', 'url_key', 'url_path') as $attributeCode) {
                $attributes = $this->_getOggettoAttribute($attributeCode, array_keys($oggettos), $storeId);
                foreach ($attributes as $oggettoId => $attributeValue) {
                    $oggettos[$oggettoId]->setData($attributeCode, $attributeValue);
                }
            }
        }

        return $oggettos;
    }

    /**
     * Retrieve Oggetto data object
     *
     * @param int $oggettoId
     * @param int $storeId
     * @return Varien_Object
     */
    public function getOggetto($oggettoId, $storeId)
    {
        $entityId = 0;
        $oggettos = $this->_getOggettos($oggettoId, $storeId, 0, $entityId);
        if (isset($oggettos[$oggettoId])) {
            return $oggettos[$oggettoId];
        }
        return false;
    }

    /**
     * Retrieve Oggetto data obects for store
     *
     * @param int $storeId
     * @param int $lastEntityId
     * @return array
     */
    public function getOggettosByStore($storeId, &$lastEntityId)
    {
        return $this->_getOggettos(null, $storeId, $lastEntityId, $lastEntityId);
    }

    /**
     * Retrieve Oggetto data objects in category
     *
     * @param Varien_Object $category
     * @param int $lastEntityId
     * @return array
     */
    public function getOggettosByCategory(Varien_Object $category, &$lastEntityId)
    {
        $oggettoIds = $this->getOggettoIdsByCategory($category);
        if (!$oggettoIds) {
            return array();
        }
        return $this->_getOggettos($oggettoIds, $category->getStoreId(), $lastEntityId, $lastEntityId);
    }

    /**
     * Find and remove unused oggettos rewrites - a case when oggettos were moved away from the category
     * (either to other category or deleted), so rewrite "category_id-oggetto_id" is invalid
     *
     * @param int $storeId
     * @return Shaurmalab_Score_Model_Resource_Url
     */
    public function clearCategoryOggetto($storeId)
    {
        $adapter = $this->_getWriteAdapter();
        $select = $adapter->select()
            ->from(array('tur' => $this->getMainTable()), $this->getIdFieldName())
            ->joinLeft(
                array('tcp' => $this->getTable('score/category_oggetto')),
                'tur.category_id = tcp.category_id AND tur.oggetto_id = tcp.oggetto_id',
                array()
            )
            ->where('tur.store_id = :store_id')
            ->where('tur.category_id IS NOT NULL')
            ->where('tur.oggetto_id IS NOT NULL')
            ->where('tcp.category_id IS NULL');
        $rewriteIds = $adapter->fetchCol($select, array('store_id' => $storeId));
        if ($rewriteIds) {
            $where = array($this->getIdFieldName() . ' IN(?)' => $rewriteIds);
            $adapter->delete($this->getMainTable(), $where);
        }

        return $this;
    }

    /**
     * Remove unused rewrites for oggetto - called after we created all needed rewrites for oggetto and know the categories
     * where the oggetto is contained ($excludeCategoryIds), so we can remove all invalid oggetto rewrites that have other category ids
     *
     * Notice: this routine is not identical to clearCategoryOggetto(), because after checking all categories this one removes rewrites
     * for oggetto still contained within categories.
     *
     * @param int $oggettoId Oggetto entity Id
     * @param int $storeId Store Id for rewrites
     * @param array $excludeCategoryIds Array of category Ids that should be skipped
     * @return Shaurmalab_Score_Model_Resource_Url
     */
    public function clearOggettoRewrites($oggettoId, $storeId, $excludeCategoryIds = array())
    {
        $where = array(
            'oggetto_id = ?' => $oggettoId,
            'store_id = ?' => $storeId
        );

        if (!empty($excludeCategoryIds)) {
            $where['category_id NOT IN (?)'] = $excludeCategoryIds;
            // If there's at least one category to skip, also skip root category, because oggetto belongs to website
            $where[] = 'category_id IS NOT NULL';
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }

    /**
     * Finds and deletes all old category and category/oggetto rewrites for store
     * left from the times when categories/oggettos belonged to store
     *
     * @param int $storeId
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Url
     */
    public function clearStoreCategoriesInvalidRewrites($storeId)
    {
        // Form a list of all current store categories ids
        $store          = $this->getStores($storeId);
        $rootCategoryId = $store->getRootCategoryId();
        if (!$rootCategoryId) {
            return $this;
        }
        $categoryIds = $this->getRootChildrenIds($rootCategoryId, $store->getRootCategoryPath());

        // Remove all store score rewrites that are for some category or cartegory/oggetto not within store categories
        $where   = array(
            'store_id = ?' => $storeId,
            'category_id IS NOT NULL', // For sure check that it's a score rewrite
            'category_id NOT IN (?)' => $categoryIds
        );

        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);

        return $this;
    }

    /**
     * Finds and deletes oggetto rewrites (that are not assigned to any category) for store
     * left from the times when oggetto was assigned to this store's website and now is not assigned
     *
     * Notice: this routine is different from clearOggettoRewrites() and clearCategoryOggetto() because
     * it handles direct rewrites to oggetto without defined category (category_id IS NULL) whilst that routines
     * handle only oggetto rewrites within categories
     *
     * @param int $storeId
     * @param int|array|null $oggettoId
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Url
     */
    public function clearStoreOggettosInvalidRewrites($storeId, $oggettoId = null)
    {
        $store   = $this->getStores($storeId);
        $adapter = $this->_getReadAdapter();
        $bind    = array(
            'website_id' => (int)$store->getWebsiteId(),
            'store_id'   => (int)$storeId
        );
        $select = $adapter->select()
            ->from(array('rewrite' => $this->getMainTable()), $this->getIdFieldName())
            ->joinLeft(
                array('website' => $this->getTable('score/oggetto_website')),
                'rewrite.oggetto_id = website.oggetto_id AND website.website_id = :website_id',
                array()
            )->where('rewrite.store_id = :store_id')
            //->where('rewrite.category_id IS NULL')
			;
        if ($oggettoId) {
            $select->where('rewrite.oggetto_id IN (?)', $oggettoId);
        } else {
            $select->where('rewrite.oggetto_id IS NOT NULL');
        }
        $select->where('website.website_id IS NULL');

        $rewriteIds = $adapter->fetchCol($select, $bind);
        if ($rewriteIds) {
            $where = array($this->getIdFieldName() . ' IN(?)' => $rewriteIds);
            $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        }

        return $this;
    }

    /**
     * Finds and deletes old rewrites for store
     * a) category rewrites left from the times when store had some other root category
     * b) oggetto rewrites left from oggettos that once belonged to this site, but then deleted or just removed from website
     *
     * @param int $storeId
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Url
     */
    public function clearStoreInvalidRewrites($storeId)
    {
        $this->clearStoreCategoriesInvalidRewrites($storeId);
        $this->clearStoreOggettosInvalidRewrites($storeId);
        return $this;
    }

    /**
     * Delete rewrites for associated to category oggettos
     *
     * @param int $categoryId
     * @param array $oggettoIds
     * @return Shaurmalab_Score_Model_Resource_Url
     */
    public function deleteCategoryOggettoRewrites($categoryId, $oggettoIds)
    {
        $this->deleteCategoryOggettoStoreRewrites($categoryId, $oggettoIds);
        return $this;
    }

    /**
     * Delete URL rewrites for category oggettos of specific store
     *
     * @param int $categoryId
     * @param array|int|null $oggettoIds
     * @param null|int $storeId
     * @return Shaurmalab_Score_Model_Resource_Url
     */
    public function deleteCategoryOggettoStoreRewrites($categoryId, $oggettoIds = null, $storeId = null)
    {
        // Notice that we don't include category_id = NULL in case of root category,
        // because oggetto removed from all categories but assigned to store's website is still
        // assumed to be in root cat. Unassigned oggettos must be removed by other routine.
        $condition = array('category_id = ?' => $categoryId);
        if (empty($oggettoIds)) {
            $condition[] = 'oggetto_id IS NOT NULL';
        } else {
            $condition['oggetto_id IN (?)'] = $oggettoIds;
        }

        if ($storeId !== null) {
            $condition['store_id IN(?)'] = $storeId;
        }

        $this->_getWriteAdapter()->delete($this->getMainTable(), $condition);
        return $this;
    }

    /**
     * Retrieve rewrites and visibility by store
     * Input array format:
     * oggetto_id as key and store_id as value
     * Output array format (oggetto_id as key)
     * store_id     int; store id
     * visibility   int; visibility for store
     * url_rewrite  string; rewrite URL for store
     *
     * @param array $oggettos
     * @return array
     */
    public function getRewriteByOggettoStore(array $oggettos)
    {
        $result = array();

        if (empty($oggettos)) {
            return $result;
        }
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from(
                array('i' => $this->getTable('score/category_oggetto_index')),
                array('oggetto_id', 'store_id', 'visibility')
            )
            ->joinLeft(
                array('r' => $this->getMainTable()),
                'i.oggetto_id = r.oggetto_id AND i.store_id=r.store_id AND r.category_id IS NULL',
                array('request_path')
            );

        $bind = array();
        foreach ($oggettos as $oggettoId => $storeId) {
            $catId = Mage::app()->getStore($storeId)->getRootCategoryId();
            $oggettoBind = 'oggetto_id' . $oggettoId;
            $storeBind   = 'store_id' . $storeId;
            $catBind     = 'category_id' . $catId;
            $cond  = '(' . implode(' AND ', array(
                'i.oggetto_id = :' . $oggettoBind,
                'i.store_id = :' . $storeBind,
                'i.category_id = :' . $catBind,
            )) . ')';
            $bind[$oggettoBind] = $oggettoId;
            $bind[$storeBind]   = $storeId;
            $bind[$catBind]     = $catId;
            $select->orWhere($cond);
        }

        $rowSet = $adapter->fetchAll($select, $bind);
        foreach ($rowSet as $row) {
            $result[$row['oggetto_id']] = array(
                'store_id'      => $row['store_id'],
                'visibility'    => $row['visibility'],
                'url_rewrite'   => $row['request_path'],
            );
        }

        return $result;
    }

    /**
     * Find and return final id path by request path
     * Needed for permanent redirect old URLs.
     *
     * @param string $requestPath
     * @param int $storeId
     * @param array $_checkedPaths internal varible to prevent infinite loops.
     * @return string | bool
     */
    public function findFinalTargetPath($requestPath, $storeId, &$_checkedPaths = array())
    {
        if (in_array($requestPath, $_checkedPaths)) {
            return false;
        }

        $_checkedPaths[] = $requestPath;

        $select = $this->_getWriteAdapter()->select()
            ->from($this->getMainTable(), array('target_path', 'id_path'))
            ->where('store_id = ?', $storeId)
            ->where('request_path = ?', $requestPath);

        if ($row = $this->_getWriteAdapter()->fetchRow($select)) {
            $idPath = $this->findFinalTargetPath($row['target_path'], $storeId, $_checkedPaths);
            if (!$idPath) {
                return $row['id_path'];
            } else {
                return $idPath;
            }
        }

        return false;
    }

    /**
     * Delete rewrite path record from the database.
     *
     * @param string $requestPath
     * @param int $storeId
     * @return void
     */
    public function deleteRewrite($requestPath, $storeId)
    {
        $this->deleteRewriteRecord($requestPath, $storeId);
    }

    /**
     * Delete rewrite path record from the database with RP checking.
     *
     * @param string $requestPath
     * @param int $storeId
     * @param bool $rp whether check rewrite option to be "Redirect = Permanent"
     * @return void
     */
    public function deleteRewriteRecord($requestPath, $storeId, $rp = false)
    {
        $conditions =  array(
            'store_id = ?' => $storeId,
            'request_path = ?' => $requestPath,
        );
        if ($rp) {
            $conditions['options = ?'] = 'RP';
        }
        $this->_getWriteAdapter()->delete($this->getMainTable(), $conditions);
    }
}
