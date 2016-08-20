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
 * Resource model for category oggetto indexer
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Category_Indexer_Oggetto extends Mage_Index_Model_Resource_Abstract
{
    /**
     * Category table
     *
     * @var string
     */
    protected $_categoryTable;

    /**
     * Category oggetto table
     *
     * @var string
     */
    protected $_categoryOggettoTable;

    /**
     * Oggetto website table
     *
     * @var string
     */
    protected $_oggettoWebsiteTable;

    /**
     * Store table
     *
     * @var string
     */
    protected $_storeTable;

    /**
     * Group table
     *
     * @var string
     */
    protected $_groupTable;

    /**
     * Array of info about stores
     *
     * @var array
     */
    protected $_storesInfo;

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('score/category_oggetto_index', 'category_id');
        $this->_categoryTable        = $this->getTable('score/category');
        $this->_categoryOggettoTable = $this->getTable('score/category_oggetto');
        $this->_oggettoWebsiteTable  = $this->getTable('score/oggetto_website');
        $this->_storeTable           = $this->getTable('core/store');
        $this->_groupTable           = $this->getTable('core/store_group');
    }

    /**
     * Process oggetto save.
     * Method is responsible for index support
     * when oggetto was saved and assigned categories was changed.
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_Score_Model_Resource_Category_Indexer_Oggetto
     */
    public function scoreOggettoSave(Mage_Index_Model_Event $event)
    {
        $oggettoId = $event->getEntityPk();
        $data      = $event->getNewData();

        /**
         * Check if category ids were updated
         */
        if (!isset($data['category_ids'])) {
            return $this;
        }

        /**
         * Select relations to categories
         */
        $select = $this->_getWriteAdapter()->select()
            ->from(array('cp' => $this->_categoryOggettoTable), 'category_id')
            ->joinInner(array('ce' => $this->_categoryTable), 'ce.entity_id=cp.category_id', 'path')
            ->where('cp.oggetto_id=:oggetto_id');

        /**
         * Get information about oggetto categories
         */
        $categories = $this->_getWriteAdapter()->fetchPairs($select, array('oggetto_id' => $oggettoId));
        $categoryIds = array();
        $allCategoryIds = array();

        foreach ($categories as $id=>$path) {
            $categoryIds[]  = $id;
            $allCategoryIds = array_merge($allCategoryIds, explode('/', $path));
        }
        $allCategoryIds = array_unique($allCategoryIds);
        $allCategoryIds = array_diff($allCategoryIds, $categoryIds);

        /**
         * Delete previous index data
         */
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            array('oggetto_id = ?' => $oggettoId)
        );

        $this->_refreshAnchorRelations($allCategoryIds, $oggettoId);
        $this->_refreshDirectRelations($categoryIds, $oggettoId);
        $this->_refreshRootRelations($oggettoId);
        return $this;
    }

    /**
     * Process Score Oggetto mass action
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_Score_Model_Resource_Category_Indexer_Oggetto
     */
    public function scoreOggettoMassAction(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        /**
         * check is oggetto ids were updated
         */
        if (!isset($data['oggetto_ids'])) {
            return $this;
        }
        $oggettoIds     = $data['oggetto_ids'];
        $categoryIds    = array();
        $allCategoryIds = array();

        /**
         * Select relations to categories
         */
        $adapter = $this->_getWriteAdapter();
        $select  = $adapter->select()
            ->distinct(true)
            ->from(array('cp' => $this->_categoryOggettoTable), array('category_id'))
            ->join(
                array('ce' => $this->_categoryTable),
                'ce.entity_id=cp.category_id',
                array('path'))
            ->where('cp.oggetto_id IN(?)', $oggettoIds);
        $pairs   = $adapter->fetchPairs($select);
        foreach ($pairs as $categoryId => $categoryPath) {
            $categoryIds[] = $categoryId;
            $allCategoryIds = array_merge($allCategoryIds, explode('/', $categoryPath));
        }

        $allCategoryIds = array_unique($allCategoryIds);
        $allCategoryIds = array_diff($allCategoryIds, $categoryIds);

        /**
         * Delete previous index data
         */
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(), array('oggetto_id IN(?)' => $oggettoIds)
        );

        $this->_refreshAnchorRelations($allCategoryIds, $oggettoIds);
        $this->_refreshDirectRelations($categoryIds, $oggettoIds);
        $this->_refreshRootRelations($oggettoIds);
        return $this;
    }

    /**
     * Return array of used root category id - path pairs
     *
     * @return array
     */
    protected function _getRootCategories()
    {
        $rootCategories = array();
        $stores = $this->_getStoresInfo();
        foreach ($stores as $storeInfo) {
            if ($storeInfo['root_id']) {
                $rootCategories[$storeInfo['root_id']] = $storeInfo['root_path'];
            }
        }

        return $rootCategories;
    }

    /**
     * Process category index after category save
     *
     * @param Mage_Index_Model_Event $event
     */
    public function scoreCategorySave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        $checkRootCategories        = false;
        $processRootCategories      = false;
        $affectedRootCategoryIds    = array();
        $rootCategories             = $this->_getRootCategories();

        /**
         * Check if we have reindex category move results
         */
        if (isset($data['affected_category_ids'])) {
            $categoryIds = $data['affected_category_ids'];
            $checkRootCategories = true;
        } else if (isset($data['oggettos_was_changed'])) {
            $categoryIds = array($event->getEntityPk());

            if (isset($rootCategories[$event->getEntityPk()])) {
                $processRootCategories = true;
                $affectedRootCategoryIds[] = $event->getEntityPk();
            }
        } else {
            return;
        }

        $select = $this->_getWriteAdapter()->select()
            ->from($this->_categoryTable, 'path')
            ->where('entity_id IN (?)', $categoryIds);
        $paths = $this->_getWriteAdapter()->fetchCol($select);
        $allCategoryIds = array();
        foreach ($paths as $path) {
            if ($checkRootCategories) {
                foreach ($rootCategories as $rootCategoryId => $rootCategoryPath) {
                    if (strpos($path, sprintf('%d/', $rootCategoryPath)) === 0 || $path == $rootCategoryPath) {
                        $affectedRootCategoryIds[$rootCategoryId] = $rootCategoryId;
                    }
                }
            }
            $allCategoryIds = array_merge($allCategoryIds, explode('/', $path));
        }
        $allCategoryIds = array_unique($allCategoryIds);

        if ($checkRootCategories && count($affectedRootCategoryIds) > 1) {
            $processRootCategories = true;
        }

        /**
         * retrieve anchor category id
         */
        $anchorInfo = $this->_getAnchorAttributeInfo();
        $bind = array(
            'attribute_id' => $anchorInfo['id'],
            'store_id'     => Shaurmalab_Score_Model_Abstract::DEFAULT_STORE_ID,
            'e_value'      => 1
        );
        $select = $this->_getReadAdapter()->select()
            ->distinct(true)
            ->from(array('ce' => $this->_categoryTable), array('entity_id'))
            ->joinInner(
                array('dca'=>$anchorInfo['table']),
                "dca.entity_id=ce.entity_id AND dca.attribute_id=:attribute_id AND dca.store_id=:store_id",
                array())
             ->where('dca.value=:e_value')
             ->where('ce.entity_id IN (?)', $allCategoryIds);
        $anchorIds = $this->_getWriteAdapter()->fetchCol($select, $bind);
        /**
         * delete only anchor id and category ids
         */
        $deleteCategoryIds = array_merge($anchorIds,$categoryIds);

        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('category_id IN(?)', $deleteCategoryIds)
        );

        $directIds = array_diff($categoryIds, $anchorIds);
        if ($anchorIds) {
            $this->_refreshAnchorRelations($anchorIds);
        }
        if ($directIds) {
            $this->_refreshDirectRelations($directIds);
        }

        /**
         * Need to re-index affected root category ids when its are not anchor
         */
        if ($processRootCategories) {
            $reindexRootCategoryIds = array_diff($affectedRootCategoryIds, $anchorIds);
            if ($reindexRootCategoryIds) {
                $this->_refreshNotAnchorRootCategories($reindexRootCategoryIds);
            }
        }

    }

    /**
     * Reindex not anchor root categories
     *
     * @param array $categoryIds
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Category_Indexer_Oggetto
     */
    protected function _refreshNotAnchorRootCategories(array $categoryIds = null)
    {
        if (empty($categoryIds)) {
            return $this;
        }

        $adapter = $this->_getWriteAdapter();

        // remove anchor relations
        $where = array(
            'category_id IN(?)' => $categoryIds,
            'is_parent=?'       => 0
        );
        $adapter->delete($this->getMainTable(), $where);

        $stores = $this->_getStoresInfo();
        /**
         * Build index for each store
         */
        foreach ($stores as $storeData) {
            $storeId    = $storeData['store_id'];
            $websiteId  = $storeData['website_id'];
            $rootPath   = $storeData['root_path'];
            $rootId     = $storeData['root_id'];
            if (!in_array($rootId, $categoryIds)) {
                continue;
            }

            $select = $adapter->select()
                ->distinct(true)
                ->from(array('cc' => $this->getTable('score/category')), null)
                ->join(
                    array('i' => $this->getMainTable()),
                    'i.category_id = cc.entity_id and i.store_id = 1',
                    array())
                ->joinLeft(
                    array('ie' => $this->getMainTable()),
                    'ie.category_id = ' . (int)$rootId
                        . ' AND ie.oggetto_id=i.oggetto_id AND ie.store_id = ' . (int)$storeId,
                    array())
                ->where('cc.path LIKE ?', $rootPath . '/%')
                ->where('ie.category_id IS NULL')
                ->columns(array(
                    'category_id'   => new Zend_Db_Expr($rootId),
                    'oggetto_id'    => 'i.oggetto_id',
                    'position'      => new Zend_Db_Expr('0'),
                    'is_parent'     => new Zend_Db_Expr('0'),
                    'store_id'      => new Zend_Db_Expr($storeId),
                    'visibility'    => 'i.visibility'
                ));
            $query = $select->insertFromSelect($this->getMainTable());
            $adapter->query($query);

            $visibilityInfo = $this->_getVisibilityAttributeInfo();
            $statusInfo     = $this->_getStatusAttributeInfo();

            $select = $this->_getReadAdapter()->select()
                ->from(array('pw' => $this->_oggettoWebsiteTable), array())
                ->joinLeft(
                    array('i' => $this->getMainTable()),
                    'i.oggetto_id = pw.oggetto_id AND i.category_id = ' . (int)$rootId
                        . ' AND i.store_id = ' . (int) $storeId,
                    array())
                ->join(
                    array('dv' => $visibilityInfo['table']),
                    "dv.entity_id = pw.oggetto_id AND dv.attribute_id = {$visibilityInfo['id']} AND dv.store_id = 0",
                    array())
                ->joinLeft(
                    array('sv' => $visibilityInfo['table']),
                    "sv.entity_id = pw.oggetto_id AND sv.attribute_id = {$visibilityInfo['id']}"
                        . " AND sv.store_id = " . (int)$storeId,
                    array())
                ->join(
                    array('ds' => $statusInfo['table']),
                    "ds.entity_id = pw.oggetto_id AND ds.attribute_id = {$statusInfo['id']} AND ds.store_id = 0",
                    array())
                ->joinLeft(
                    array('ss' => $statusInfo['table']),
                    "ss.entity_id = pw.oggetto_id AND ss.attribute_id = {$statusInfo['id']}"
                        . " AND ss.store_id = " . (int)$storeId,
                    array())
                ->where('i.oggetto_id IS NULL')
                ->where('pw.website_id=?', $websiteId)
                ->where(
                    $this->_getWriteAdapter()->getCheckSql('ss.value_id IS NOT NULL', 'ss.value', 'ds.value') . ' = ?',
                    Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED)
                ->columns(array(
                    'category_id'   => new Zend_Db_Expr($rootId),
                    'oggetto_id'    => 'pw.oggetto_id',
                    'position'      => new Zend_Db_Expr('0'),
                    'is_parent'     => new Zend_Db_Expr('1'),
                    'store_id'      => new Zend_Db_Expr($storeId),
                    'visibility'    => $adapter->getCheckSql('sv.value_id IS NOT NULL', 'sv.value', 'dv.value')
                ));

            $query = $select->insertFromSelect($this->getMainTable());
            $this->_getWriteAdapter()->query($query);
        }

        return $this;
    }


    /**
     * Rebuild index for direct associations categories and oggettos
     *
     * @param null|array $categoryIds
     * @param null|array $oggettoIds
     * @return Shaurmalab_Score_Model_Resource_Category_Indexer_Oggetto
     */
    protected function _refreshDirectRelations($categoryIds = null, $oggettoIds = null)
    {
        if (!$categoryIds && !$oggettoIds) {
            return $this;
        }

        $visibilityInfo = $this->_getVisibilityAttributeInfo();
        $statusInfo     = $this->_getStatusAttributeInfo();
        $adapter = $this->_getWriteAdapter();
        /**
         * Insert direct relations
         * oggetto_ids (enabled filter) X category_ids X store_ids
         * Validate store root category
         */
        $isParent = new Zend_Db_Expr('1');
        $select = $adapter->select()
            ->from(array('cp' => $this->_categoryOggettoTable),
                array('category_id', 'oggetto_id', 'position', $isParent))
            ->joinInner(array('pw'  => $this->_oggettoWebsiteTable), 'pw.oggetto_id=cp.oggetto_id', array())
            ->joinInner(array('g'   => $this->_groupTable), 'g.website_id=pw.website_id', array())
            ->joinInner(array('s'   => $this->_storeTable), 's.group_id=g.group_id', array('store_id'))
            ->joinInner(array('rc'  => $this->_categoryTable), 'rc.entity_id=g.root_category_id', array())
            ->joinInner(
                array('ce'=>$this->_categoryTable),
                'ce.entity_id=cp.category_id AND ('.
                $adapter->quoteIdentifier('ce.path') . ' LIKE ' .
                $adapter->getConcatSql(array($adapter->quoteIdentifier('rc.path') , $adapter->quote('/%'))) .
                ' OR ce.entity_id=rc.entity_id)',
                array())
            ->joinLeft(
                array('dv'=>$visibilityInfo['table']),
                $adapter->quoteInto(
                    "dv.entity_id=cp.oggetto_id AND dv.attribute_id=? AND dv.store_id=0",
                    $visibilityInfo['id']),
                array()
            )
            ->joinLeft(
                array('sv'=>$visibilityInfo['table']),
                $adapter->quoteInto(
                    "sv.entity_id=cp.oggetto_id AND sv.attribute_id=? AND sv.store_id=s.store_id",
                    $visibilityInfo['id']),
                array('visibility' => $adapter->getCheckSql('sv.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('sv.value'),
                    $adapter->quoteIdentifier('dv.value')
                ))
            )
            ->joinLeft(
                array('ds'=>$statusInfo['table']),
                "ds.entity_id=cp.oggetto_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                array())
            ->joinLeft(
                array('ss'=>$statusInfo['table']),
                "ss.entity_id=cp.oggetto_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                array())
            ->where(
                $adapter->getCheckSql('ss.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('ss.value'),
                    $adapter->quoteIdentifier('ds.value')
                ) . ' = ?',
                Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED
            );
        if ($categoryIds) {
            $select->where('cp.category_id IN (?)', $categoryIds);
        }
        if ($oggettoIds) {
            $select->where('cp.oggetto_id IN(?)', $oggettoIds);
        }
        $sql = $select->insertFromSelect(
            $this->getMainTable(),
            array('category_id', 'oggetto_id', 'position', 'is_parent', 'store_id', 'visibility'),
            true
        );
        $adapter->query($sql);
        return $this;
    }

    /**
     * Rebuild index for anchor categories and associated to child categories oggettos
     *
     * @param null | array $categoryIds
     * @param null | array $oggettoIds
     * @return Shaurmalab_Score_Model_Resource_Category_Indexer_Oggetto
     */
    protected function _refreshAnchorRelations($categoryIds = null, $oggettoIds = null)
    {
        if (!$categoryIds && !$oggettoIds) {
            return $this;
        }

        $anchorInfo     = $this->_getAnchorAttributeInfo();
        $visibilityInfo = $this->_getVisibilityAttributeInfo();
        $statusInfo     = $this->_getStatusAttributeInfo();

        /**
         * Insert anchor categories relations
         */
        $adapter = $this->_getReadAdapter();
        $isParent = $adapter->getCheckSql('MIN(cp.category_id)=ce.entity_id', 1, 0);
        $position = 'MIN('.
            $adapter->getCheckSql(
                'cp.category_id = ce.entity_id',
                'cp.position',
                '(cc.position + 1) * ('.$adapter->quoteIdentifier('cc.level').' + 1) * 10000 + cp.position'
            )
        .')';

        $select = $adapter->select()
            ->distinct(true)
            ->from(array('ce' => $this->_categoryTable), array('entity_id'))
            ->joinInner(
                array('cc' => $this->_categoryTable),
                $adapter->quoteIdentifier('cc.path') .
                ' LIKE ('.$adapter->getConcatSql(array($adapter->quoteIdentifier('ce.path'),$adapter->quote('/%'))).')'
                . ' OR cc.entity_id=ce.entity_id'
                , array()
            )
            ->joinInner(
                array('cp' => $this->_categoryOggettoTable),
                'cp.category_id=cc.entity_id',
                array('cp.oggetto_id', 'position' => $position, 'is_parent' => $isParent)
            )
            ->joinInner(array('pw' => $this->_oggettoWebsiteTable), 'pw.oggetto_id=cp.oggetto_id', array())
            ->joinInner(array('g'  => $this->_groupTable), 'g.website_id=pw.website_id', array())
            ->joinInner(array('s'  => $this->_storeTable), 's.group_id=g.group_id', array('store_id'))
            ->joinInner(array('rc' => $this->_categoryTable), 'rc.entity_id=g.root_category_id', array())
            ->joinLeft(
                array('dca'=>$anchorInfo['table']),
                "dca.entity_id=ce.entity_id AND dca.attribute_id={$anchorInfo['id']} AND dca.store_id=0",
                array())
            ->joinLeft(
                array('sca'=>$anchorInfo['table']),
                "sca.entity_id=ce.entity_id AND sca.attribute_id={$anchorInfo['id']} AND sca.store_id=s.store_id",
                array())
            ->joinLeft(
                array('dv'=>$visibilityInfo['table']),
                "dv.entity_id=pw.oggetto_id AND dv.attribute_id={$visibilityInfo['id']} AND dv.store_id=0",
                array())
            ->joinLeft(
                array('sv'=>$visibilityInfo['table']),
                "sv.entity_id=pw.oggetto_id AND sv.attribute_id={$visibilityInfo['id']} AND sv.store_id=s.store_id",
                array('visibility' => $adapter->getCheckSql(
                    'MIN(sv.value_id) IS NOT NULL',
                    'MIN(sv.value)', 'MIN(dv.value)'
                ))
            )
            ->joinLeft(
                array('ds'=>$statusInfo['table']),
                "ds.entity_id=pw.oggetto_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                array())
            ->joinLeft(
                array('ss'=>$statusInfo['table']),
                "ss.entity_id=pw.oggetto_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                array())
            /**
             * Condition for anchor or root category (all oggettos should be assigned to root)
             */
            ->where('('.
                $adapter->quoteIdentifier('ce.path') . ' LIKE ' .
                $adapter->getConcatSql(array($adapter->quoteIdentifier('rc.path'), $adapter->quote('/%'))) . ' AND ' .
                $adapter->getCheckSql('sca.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('sca.value'),
                    $adapter->quoteIdentifier('dca.value')) . '=1) OR ce.entity_id=rc.entity_id'
            )
            ->where(
                $adapter->getCheckSql('ss.value_id IS NOT NULL', 'ss.value', 'ds.value') . '=?',
                Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED
            )
            ->group(array('ce.entity_id', 'cp.oggetto_id', 's.store_id'));
        if ($categoryIds) {
            $select->where('ce.entity_id IN (?)', $categoryIds);
        }
        if ($oggettoIds) {
            $select->where('pw.oggetto_id IN(?)', $oggettoIds);
        }

        $sql = $select->insertFromSelect($this->getMainTable());
        $this->_getWriteAdapter()->query($sql);
        return $this;
    }

    /**
     * Add oggetto association with root store category for oggettos which are not assigned to any another category
     *
     * @param int | array $oggettoIds
     * @return Shaurmalab_Score_Model_Resource_Category_Indexer_Oggetto
     */
    protected function _refreshRootRelations($oggettoIds)
    {
        $visibilityInfo = $this->_getVisibilityAttributeInfo();
        $statusInfo     = $this->_getStatusAttributeInfo();
        $adapter = $this->_getWriteAdapter();
        /**
         * Insert anchor categories relations
         */
        $isParent = new Zend_Db_Expr('0');
        $position = new Zend_Db_Expr('0');
        $select = $this->_getReadAdapter()->select()
            ->distinct(true)
            ->from(array('pw'  => $this->_oggettoWebsiteTable), array())
            ->joinInner(array('g'   => $this->_groupTable), 'g.website_id=pw.website_id', array())
            ->joinInner(array('s'   => $this->_storeTable), 's.group_id=g.group_id', array())
            ->joinInner(array('rc'  => $this->_categoryTable), 'rc.entity_id=g.root_category_id',
                array('entity_id'))
            ->joinLeft(array('cp'   => $this->_categoryOggettoTable), 'cp.oggetto_id=pw.oggetto_id',
                array('pw.oggetto_id', $position, $isParent, 's.store_id'))
            ->joinLeft(
                array('dv'=>$visibilityInfo['table']),
                "dv.entity_id=pw.oggetto_id AND dv.attribute_id={$visibilityInfo['id']} AND dv.store_id=0",
                array())
            ->joinLeft(
                array('sv'=>$visibilityInfo['table']),
                "sv.entity_id=pw.oggetto_id AND sv.attribute_id={$visibilityInfo['id']} AND sv.store_id=s.store_id",
                array('visibility' => $adapter->getCheckSql('sv.value_id IS NOT NULL',
                    $adapter->quoteIdentifier('sv.value'),
                    $adapter->quoteIdentifier('dv.value')
                ))
            )
            ->joinLeft(
                array('ds'=>$statusInfo['table']),
                "ds.entity_id=pw.oggetto_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                array())
            ->joinLeft(
                array('ss'=>$statusInfo['table']),
                "ss.entity_id=pw.oggetto_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                array())
            /**
             * Condition for anchor or root category (all oggettos should be assigned to root)
             */
            ->where('cp.oggetto_id IS NULL')
            ->where(
                    $adapter->getCheckSql('ss.value_id IS NOT NULL',
                        $adapter->quoteIdentifier('ss.value'),
                        $adapter->quoteIdentifier('ds.value')
                    ) . ' = ?', Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED)
            ->where('pw.oggetto_id IN(?)', $oggettoIds);

        $sql = $select->insertFromSelect($this->getMainTable());
        $this->_getWriteAdapter()->query($sql);

        $select = $this->_getReadAdapter()->select()
            ->from(array('pw' => $this->_oggettoWebsiteTable), array())
            ->joinInner(array('g' => $this->_groupTable), 'g.website_id = pw.website_id', array())
            ->joinInner(array('s' => $this->_storeTable), 's.group_id = g.group_id', array())
            ->joinLeft(
                array('i'  => $this->getMainTable()),
                'i.oggetto_id = pw.oggetto_id AND i.category_id = g.root_category_id', array())
            ->joinLeft(
                array('dv' => $visibilityInfo['table']),
                "dv.entity_id = pw.oggetto_id AND dv.attribute_id = {$visibilityInfo['id']} AND dv.store_id = 0",
                array())
            ->joinLeft(
                array('sv' => $visibilityInfo['table']),
                "sv.entity_id = pw.oggetto_id AND sv.attribute_id = {$visibilityInfo['id']}"
                    . " AND sv.store_id = s.store_id",
                array())
            ->join(
                array('ds' => $statusInfo['table']),
                "ds.entity_id = pw.oggetto_id AND ds.attribute_id = {$statusInfo['id']} AND ds.store_id = 0",
                array())
            ->joinLeft(
                array('ss' => $statusInfo['table']),
                "ss.entity_id = pw.oggetto_id AND ss.attribute_id = {$statusInfo['id']} AND ss.store_id = s.store_id",
                array())
            ->where('i.oggetto_id IS NULL')
            ->where(
                $adapter->getCheckSql('ss.value_id IS NOT NULL', 'ss.value', 'ds.value') . '=?',
                Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED)
            ->where('pw.oggetto_id IN(?)', $oggettoIds)
            ->columns(array(
                'category_id'   => 'g.root_category_id',
                'oggetto_id'    => 'pw.oggetto_id',
                'position'      => $position,
                'is_parent'     => new Zend_Db_Expr('1'),
                'store_id'      => 's.store_id',
                'visibility'    => $adapter->getCheckSql('sv.value_id IS NOT NULL', 'sv.value', 'dv.value'),
            ));

        $sql = $select->insertFromSelect($this->getMainTable());
        $this->_getWriteAdapter()->query($sql);

        return $this;
    }

    /**
     * Get is_anchor category attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getAnchorAttributeInfo()
    {
        $isAnchorAttribute = Mage::getSingleton('eav/config')
            ->getAttribute(Shaurmalab_Score_Model_Category::ENTITY, 'is_anchor');
        $info = array(
            'id'    => $isAnchorAttribute->getId() ,
            'table' => $isAnchorAttribute->getBackend()->getTable()
        );
        return $info;
    }

    /**
     * Get visibility oggetto attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getVisibilityAttributeInfo()
    {
        $visibilityAttribute = Mage::getSingleton('eav/config')
            ->getAttribute(Shaurmalab_Score_Model_Oggetto::ENTITY, 'visibility');
        $info = array(
            'id'    => $visibilityAttribute->getId() ,
            'table' => $visibilityAttribute->getBackend()->getTable()
        );
        return $info;
    }

    /**
     * Get status oggetto attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getStatusAttributeInfo()
    {
        $statusAttribute = Mage::getSingleton('eav/config')->getAttribute(Shaurmalab_Score_Model_Oggetto::ENTITY, 'status');
        $info = array(
            'id'    => $statusAttribute->getId() ,
            'table' => $statusAttribute->getBackend()->getTable()
        );
        return $info;
    }

    /**
     * Rebuild all index data
     *
     * @return Shaurmalab_Score_Model_Resource_Category_Indexer_Oggetto
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->beginTransaction();
        try {
            $this->clearTemporaryIndexTable();
            $idxTable = $this->getIdxTable();
            $idxAdapter = $this->_getIndexAdapter();
            $stores = $this->_getStoresInfo();
            /**
             * Build index for each store
             */
            foreach ($stores as $storeData) {
                $storeId    = $storeData['store_id'];
                $websiteId  = $storeData['website_id'];
                $rootPath   = $storeData['root_path'];
                $rootId     = $storeData['root_id'];
                /**
                 * Prepare visibility for all enabled store oggettos
                 */
                $enabledTable = $this->_prepareEnabledOggettosVisibility($websiteId, $storeId);
                /**
                 * Select information about anchor categories
                 */
                $anchorTable = $this->_prepareAnchorCategories($storeId, $rootPath);
                /**
                 * Add relations between not anchor categories and oggettos
                 */
                $select = $idxAdapter->select();
                /** @var $select Varien_Db_Select */
                $select->from(
                    array('cp' => $this->_categoryOggettoTable),
                    array('category_id', 'oggetto_id', 'position', 'is_parent' => new Zend_Db_Expr('1'),
                        'store_id' => new Zend_Db_Expr($storeId))
                )
                ->joinInner(array('pv' => $enabledTable), 'pv.oggetto_id=cp.oggetto_id', array('visibility'))
                ->joinLeft(array('ac' => $anchorTable), 'ac.category_id=cp.category_id', array())
                ->where('ac.category_id IS NULL');

                $query = $select->insertFromSelect(
                    $idxTable,
                    array('category_id', 'oggetto_id', 'position', 'is_parent', 'store_id', 'visibility'),
                    false
                );
                $idxAdapter->query($query);

                /**
                 * Assign oggettos not associated to any category to root category in index
                 */

                $select = $idxAdapter->select();
                $select->from(
                    array('pv' => $enabledTable),
                    array(new Zend_Db_Expr($rootId), 'oggetto_id', new Zend_Db_Expr('0'), new Zend_Db_Expr('1'),
                        new Zend_Db_Expr($storeId), 'visibility')
                )
                ->joinLeft(array('cp' => $this->_categoryOggettoTable), 'pv.oggetto_id=cp.oggetto_id', array())
                ->where('cp.oggetto_id IS NULL');

                $query = $select->insertFromSelect(
                    $idxTable,
                    array('category_id', 'oggetto_id', 'position', 'is_parent', 'store_id', 'visibility'),
                    false
                );
                $idxAdapter->query($query);

                /**
                 * Prepare anchor categories oggettos
                 */
                $anchorOggettosTable = $this->_getAnchorCategoriesOggettosTemporaryTable();
                $idxAdapter->delete($anchorOggettosTable);

                $position = 'MIN('.
                    $idxAdapter->getCheckSql(
                        'ca.category_id = ce.entity_id',
                        $idxAdapter->quoteIdentifier('cp.position'),
                        '('.$idxAdapter->quoteIdentifier('ce.position').' + 1) * '
                        .'('.$idxAdapter->quoteIdentifier('ce.level').' + 1 * 10000)'
                        .' + '.$idxAdapter->quoteIdentifier('cp.position')
                    )
                .')';


                $select = $idxAdapter->select()
                ->useStraightJoin(true)
                ->distinct(true)
                ->from(array('ca' => $anchorTable), array('category_id'))
                ->joinInner(
                    array('ce' => $this->_categoryTable),
                    $idxAdapter->quoteIdentifier('ce.path') . ' LIKE ' .
                    $idxAdapter->quoteIdentifier('ca.path') . ' OR ce.entity_id = ca.category_id',
                    array()
                )
                ->joinInner(
                    array('cp' => $this->_categoryOggettoTable),
                    'cp.category_id = ce.entity_id',
                    array('oggetto_id')
                )
                ->joinInner(
                    array('pv' => $enabledTable),
                    'pv.oggetto_id = cp.oggetto_id',
                    array('position' => $position)
                )
                ->group(array('ca.category_id', 'cp.oggetto_id'));
                $query = $select->insertFromSelect($anchorOggettosTable,
                    array('category_id', 'oggetto_id', 'position'), false);
                $idxAdapter->query($query);

                /**
                 * Add anchor categories oggettos to index
                 */
                $select = $idxAdapter->select()
                ->from(
                    array('ap' => $anchorOggettosTable),
                    array('category_id', 'oggetto_id',
                        'position', // => new Zend_Db_Expr('MIN('. $idxAdapter->quoteIdentifier('ap.position').')'),
                        'is_parent' => $idxAdapter->getCheckSql('cp.oggetto_id > 0', 1, 0),
                        'store_id' => new Zend_Db_Expr($storeId))
                )
                ->joinLeft(
                    array('cp' => $this->_categoryOggettoTable),
                    'cp.category_id=ap.category_id AND cp.oggetto_id=ap.oggetto_id',
                    array()
                )
                ->joinInner(array('pv' => $enabledTable), 'pv.oggetto_id = ap.oggetto_id', array('visibility'));

                $query = $select->insertFromSelect(
                    $idxTable,
                    array('category_id', 'oggetto_id', 'position', 'is_parent', 'store_id', 'visibility'),
                    false
                );
                $idxAdapter->query($query);

                $select = $idxAdapter->select()
                    ->from(array('e' => $this->getTable('score/oggetto')), null)
                    ->join(
                        array('ei' => $enabledTable),
                        'ei.oggetto_id = e.entity_id',
                        array())
                    ->joinLeft(
                        array('i' => $idxTable),
                        'i.oggetto_id = e.entity_id AND i.category_id = :category_id AND i.store_id = :store_id',
                        array())
                    ->where('i.oggetto_id IS NULL')
                    ->columns(array(
                        'category_id'   => new Zend_Db_Expr($rootId),
                        'oggetto_id'    => 'e.entity_id',
                        'position'      => new Zend_Db_Expr('0'),
                        'is_parent'     => new Zend_Db_Expr('1'),
                        'store_id'      => new Zend_Db_Expr($storeId),
                        'visibility'    => 'ei.visibility'
                    ));

                $query = $select->insertFromSelect(
                    $idxTable,
                    array('category_id', 'oggetto_id', 'position', 'is_parent', 'store_id', 'visibility'),
                    false
                );

                $idxAdapter->query($query, array('store_id' => $storeId, 'category_id' => $rootId));
            }

            $this->syncData();

            /**
             * Clean up temporary tables
             */
            $this->clearTemporaryIndexTable();
            $idxAdapter->delete($enabledTable);
            $idxAdapter->delete($anchorTable);
            $idxAdapter->delete($anchorOggettosTable);
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }


    /**
     * Create temporary table with enabled oggettos visibility info
     *
     * @param int $websiteId
     * @param int $storeId
     * @return string temporary table name
     */
    protected function _prepareEnabledOggettosVisibility($websiteId, $storeId)
    {
        $statusAttribute = Mage::getSingleton('eav/config')->getAttribute(Shaurmalab_Score_Model_Oggetto::ENTITY, 'status');
        $visibilityAttribute = Mage::getSingleton('eav/config')
            ->getAttribute(Shaurmalab_Score_Model_Oggetto::ENTITY, 'visibility');
			
		$statusAttributeId = $statusAttribute->getId();
        $visibilityAttributeId = $visibilityAttribute->getId();
        $statusTable = $statusAttribute->getBackend()->getTable();
        $visibilityTable = $visibilityAttribute->getBackend()->getTable();

        /**
         * Prepare temporary table
         */
        $tmpTable = $this->_getEnabledOggettosTemporaryTable();
        $this->_getIndexAdapter()->delete($tmpTable);

        $adapter        = $this->_getIndexAdapter();
        $visibilityExpr = $adapter->getCheckSql('pvs.value_id>0', $adapter->quoteIdentifier('pvs.value'),
            $adapter->quoteIdentifier('pvd.value'));
        $select         = $adapter->select()
            ->from(array('pw' => $this->_oggettoWebsiteTable), array('oggetto_id', 'visibility' => $visibilityExpr))
            ->joinLeft(
                array('pvd' => $visibilityTable),
                $adapter->quoteInto('pvd.entity_id=pw.oggetto_id AND pvd.attribute_id=? AND pvd.store_id=0',
                    $visibilityAttributeId),
                array())
            ->joinLeft(
                array('pvs' => $visibilityTable),
                $adapter->quoteInto('pvs.entity_id=pw.oggetto_id AND pvs.attribute_id=? AND ', $visibilityAttributeId)
                    . $adapter->quoteInto('pvs.store_id=?', $storeId),
                array())
            ->joinLeft(
                array('psd' => $statusTable),
                $adapter->quoteInto('psd.entity_id=pw.oggetto_id AND psd.attribute_id=? AND psd.store_id=0',
                    $statusAttributeId),
                array())
            ->joinLeft(
                array('pss' => $statusTable),
                    $adapter->quoteInto('pss.entity_id=pw.oggetto_id AND pss.attribute_id=? AND ', $statusAttributeId)
                        . $adapter->quoteInto('pss.store_id=?', $storeId),
                array())
            ->where('pw.website_id=?',$websiteId)
            ->where($adapter->getCheckSql('pss.value_id > 0',
                $adapter->quoteIdentifier('pss.value'),
                $adapter->quoteIdentifier('psd.value')) . ' = ?', Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED);

        $query = $select->insertFromSelect($tmpTable, array('oggetto_id' , 'visibility'), false);
        $adapter->query($query);
        return $tmpTable;
    }

    /**
     * Retrieve temporary table of category enabled oggettos
     *
     * @return string
     */
    protected function _getEnabledOggettosTemporaryTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('score/category_oggetto_enabled_indexer_idx');
        }
        return $this->getTable('score/category_oggetto_enabled_indexer_tmp');
    }

    /**
     * Get array with store|website|root_categry path information
     *
     * @return array
     */
    protected function _getStoresInfo()
    {
        if (is_null($this->_storesInfo)) {
            $adapter = $this->_getReadAdapter();
            $select = $adapter->select()
                ->from(array('s' => $this->getTable('core/store')), array('store_id', 'website_id'))
                ->join(
                    array('sg' => $this->getTable('core/store_group')),
                    'sg.group_id = s.group_id',
                    array())
                ->join(
                    array('c' => $this->getTable('score/category')),
                    'c.entity_id = sg.root_category_id',
                    array(
                        'root_path' => 'path',
                        'root_id'   => 'entity_id'
                    )
                );
            $this->_storesInfo = $adapter->fetchAll($select);
        }

        return $this->_storesInfo;
    }


    /**
     * @param int $storeId
     * @param string $rootPath
     * @return string temporary table name
     */
    protected function _prepareAnchorCategories($storeId, $rootPath)
    {
        $isAnchorAttribute = Mage::getSingleton('eav/config')
            ->getAttribute(Shaurmalab_Score_Model_Category::ENTITY, 'is_anchor');
        $anchorAttributeId = $isAnchorAttribute->getId();
        $anchorTable = $isAnchorAttribute->getBackend()->getTable();
        $adapter = $this->_getIndexAdapter();
        $tmpTable = $this->_getAnchorCategoriesTemporaryTable();
        $adapter->delete($tmpTable);

        $anchorExpr = $adapter->getCheckSql('cas.value_id>0', $adapter->quoteIdentifier('cas.value'),
            $adapter->quoteIdentifier('cad.value'));
        $pathConcat = $adapter->getConcatSql(array($adapter->quoteIdentifier('ce.path'), $adapter->quote('/%')));
        $select = $adapter->select()
            ->from(
                array('ce' => $this->_categoryTable),
                array('category_id' => 'ce.entity_id', 'path' => $pathConcat))
            ->joinLeft(
                array('cad' => $anchorTable),
                $adapter->quoteInto("cad.entity_id=ce.entity_id AND cad.attribute_id=? AND cad.store_id=0",
                    $anchorAttributeId),
                array())
            ->joinLeft(
                array('cas' => $anchorTable),
                $adapter->quoteInto("cas.entity_id=ce.entity_id AND cas.attribute_id=? AND ", $anchorAttributeId)
                    . $adapter->quoteInto('cas.store_id=?', $storeId),
                array())
            ->where("{$anchorExpr} = 1 AND {$adapter->quoteIdentifier('ce.path')} LIKE ?", $rootPath . '%')
            ->orWhere('ce.path = ?', $rootPath);

        $query = $select->insertFromSelect($tmpTable, array('category_id' , 'path'), false);
        $adapter->query($query);
        return $tmpTable;
    }

    /**
     * Retrieve temporary table of anchor categories
     *
     * @return string
     */
    protected function _getAnchorCategoriesTemporaryTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('score/category_anchor_indexer_idx');
        }
        return $this->getTable('score/category_anchor_indexer_tmp');
    }

    /**
     * Retrieve temporary table of anchor categories oggettos
     *
     * @return string
     */
    protected function _getAnchorCategoriesOggettosTemporaryTable()
    {
        if ($this->useIdxTable()) {
            return $this->getTable('score/category_anchor_oggettos_indexer_idx');
        }
        return $this->getTable('score/category_anchor_oggettos_indexer_tmp');
    }

    /**
     * Retrieve temporary decimal index table name
     *
     * @param string $table
     * @return string
     */
    public function getIdxTable($table = null)
    {
        if ($this->useIdxTable()) {
            return $this->getTable('score/category_oggetto_indexer_idx');
        }
        return $this->getTable('score/category_oggetto_indexer_tmp');
    }
}
