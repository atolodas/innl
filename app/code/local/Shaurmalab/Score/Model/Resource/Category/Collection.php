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
 * Category resource collection
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Category_Collection extends Shaurmalab_Score_Model_Resource_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix              = 'score_category_collection';

    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject              = 'category_collection';

    /**
     * Name of oggetto table
     *
     * @var string
     */
    protected $_oggettoTable;

    /**
     * Store id, that we should count oggettos on
     *
     * @var int
     */
    protected $_oggettoStoreId;

    /**
     * Name of oggetto website table
     *
     * @var string
     */
    protected $_oggettoWebsiteTable;

    /**
     * Load with oggetto count flag
     *
     * @var boolean
     */
    protected $_loadWithOggettoCount     = false;

    /**
     * Score factory instance
     *
     * @var Shaurmalab_Score_Model_Factory
     */
    protected $_factory;

    /**
     * Disable flat flag
     *
     * @var bool
     */
    protected $_disableFlat = false;

    /**
     * Initialize factory
     *
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param array $args
     */
    public function __construct($resource = null, array $args = array())
    {
        parent::__construct($resource);
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('score/factory');
    }

    /**
     * Init collection and determine table names
     *
     */
    protected function _construct()
    {
        $this->_init('score/category');

        $this->_oggettoWebsiteTable = $this->getTable('score/oggetto_website');
        $this->_oggettoTable        = $this->getTable('score/category_oggetto');
    }

    /**
     * Add Id filter
     *
     * @param array $categoryIds
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function addIdFilter($categoryIds)
    {
        if (is_array($categoryIds)) {
            if (empty($categoryIds)) {
                $condition = '';
            } else {
                $condition = array('in' => $categoryIds);
            }
        } elseif (is_numeric($categoryIds)) {
            $condition = $categoryIds;
        } elseif (is_string($categoryIds)) {
            $ids = explode(',', $categoryIds);
            if (empty($ids)) {
                $condition = $categoryIds;
            } else {
                $condition = array('in' => $ids);
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }

    /**
     * Set flag for loading oggetto count
     *
     * @param boolean $flag
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function setLoadOggettoCount($flag)
    {
        $this->_loadWithOggettoCount = $flag;
        return $this;
    }

    /**
     * Before collection load
     *
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    protected function _beforeLoad()
    {
        Mage::dispatchEvent($this->_eventPrefix . '_load_before',
                            array($this->_eventObject => $this));
        return parent::_beforeLoad();
    }

    /**
     * After collection load
     *
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    protected function _afterLoad()
    {
        Mage::dispatchEvent($this->_eventPrefix . '_load_after',
                            array($this->_eventObject => $this));

        return parent::_afterLoad();
    }

    /**
     * Set id of the store that we should count oggettos on
     *
     * @param int $storeId
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function setOggettoStoreId($storeId)
    {
        $this->_oggettoStoreId = $storeId;
        return $this;
    }

    /**
     * Get id of the store that we should count oggettos on
     *
     * @return int
     */
    public function getOggettoStoreId()
    {
        if (is_null($this->_oggettoStoreId)) {
            $this->_oggettoStoreId = Shaurmalab_Score_Model_Abstract::DEFAULT_STORE_ID;
        }
        return $this->_oggettoStoreId;
    }

    /**
     * Load collection
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }

        if ($this->_loadWithOggettoCount) {
            $this->addAttributeToSelect('all_children');
            $this->addAttributeToSelect('is_anchor');
        }

        parent::load($printQuery, $logQuery);

        if ($this->_loadWithOggettoCount) {
            $this->_loadOggettoCount();
        }

        return $this;
    }

    /**
     * Load categories oggetto count
     *
     */
    protected function _loadOggettoCount()
    {
        $this->loadOggettoCount($this->_items, true, true);
    }

    /**
     * Load oggetto count for specified items
     *
     * @param array $items
     * @param boolean $countRegular get oggetto count for regular (non-anchor) categories
     * @param boolean $countAnchor get oggetto count for anchor categories
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function loadOggettoCount($items, $countRegular = true, $countAnchor = true)
    {
        $anchor     = array();
        $regular    = array();
        $websiteId  = Mage::app()->getStore($this->getOggettoStoreId())->getWebsiteId();

        foreach ($items as $item) {
            if ($item->getIsAnchor()) {
                $anchor[$item->getId()] = $item;
            } else {
                $regular[$item->getId()] = $item;
            }
        }

        if ($countRegular) {
            // Retrieve regular categories oggetto counts
            $regularIds = array_keys($regular);
            if (!empty($regularIds)) {
                $select = $this->_conn->select();
                $select->from(
                        array('main_table' => $this->_oggettoTable),
                        array('category_id', new Zend_Db_Expr('COUNT(main_table.oggetto_id)'))
                    )
                    ->where($this->_conn->quoteInto('main_table.category_id IN(?)', $regularIds))
                    ->group('main_table.category_id');
                if ($websiteId) {
                    $select->join(
                        array('w' => $this->_oggettoWebsiteTable),
                        'main_table.oggetto_id = w.oggetto_id', array()
                    )
                    ->where('w.website_id = ?', $websiteId);
                }
                $counts = $this->_conn->fetchPairs($select);
                foreach ($regular as $item) {
                    if (isset($counts[$item->getId()])) {
                        $item->setOggettoCount($counts[$item->getId()]);
                    } else {
                        $item->setOggettoCount(0);
                    }
                }
            }
        }

        if ($countAnchor) {
            // Retrieve Anchor categories oggetto counts
            foreach ($anchor as $item) {
                if ($allChildren = $item->getAllChildren()) {
                    $bind = array(
                        'entity_id' => $item->getId(),
                        'c_path'    => $item->getPath() . '/%'
                    );
                    $select = $this->_conn->select();
                    $select->from(
                            array('main_table' => $this->_oggettoTable),
                            new Zend_Db_Expr('COUNT(DISTINCT main_table.oggetto_id)')
                        )
                        ->joinInner(
                            array('e' => $this->getTable('score/category')),
                            'main_table.category_id=e.entity_id',
                            array()
                        )
                        ->where('e.entity_id = :entity_id')
                        ->orWhere('e.path LIKE :c_path');
                    if ($websiteId) {
                        $select->join(
                            array('w' => $this->_oggettoWebsiteTable),
                            'main_table.oggetto_id = w.oggetto_id', array()
                        )
                        ->where('w.website_id = ?', $websiteId);
                    }
                    $item->setOggettoCount((int) $this->_conn->fetchOne($select, $bind));
                } else {
                    $item->setOggettoCount(0);
                }
            }
        }
        return $this;
    }

    /**
     * Add category path filter
     *
     * @param string $regexp
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function addPathFilter($regexp)
    {
        $this->addFieldToFilter('path', array('regexp' => $regexp));
        return $this;
    }

    /**
     * Joins url rewrite rules to collection
     *
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function joinUrlRewrite()
    {
        $this->_factory->getCategoryUrlRewriteHelper()
            ->joinTableToEavCollection($this, $this->_getCurrentStoreId());

        return $this;
    }

    /**
     * Retrieves store_id from current store
     *
     * @return int
     */
    protected function _getCurrentStoreId()
    {
        return (int)Mage::app()->getStore()->getId();
    }

    /**
     * Add active category filter
     *
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function addIsActiveFilter()
    {
        $this->addAttributeToFilter('is_active', 1);
        Mage::dispatchEvent($this->_eventPrefix . '_add_is_active_filter',
                            array($this->_eventObject => $this));
        return $this;
    }

    /**
     * Add name attribute to result
     *
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function addNameToResult()
    {
        $this->addAttributeToSelect('name');
        return $this;
    }

    /**
     * Add url rewrite rules to collection
     *
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function addUrlRewriteToResult()
    {
        $this->joinUrlRewrite();
        return $this;
    }

    /**
     * Add category path filter
     *
     * @param array|string $paths
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function addPathsFilter($paths)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        $write  = $this->getResource()->getWriteConnection();
        $cond   = array();
        foreach ($paths as $path) {
            $cond[] = $write->quoteInto('e.path LIKE ?', "$path%");
        }
        if ($cond) {
            $this->getSelect()->where(join(' OR ', $cond));
        }
        return $this;
    }

    /**
     * Add category level filter
     *
     * @param int|string $level
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function addLevelFilter($level)
    {
        $this->addFieldToFilter('level', array('lteq' => $level));
        return $this;
    }

    /**
     * Add root category filter
     *
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function addRootLevelFilter()
    {
        $this->addFieldToFilter('path', array('neq' => '1'));
        $this->addLevelFilter(1);
        return $this;
    }

    /**
     * Add order field
     *
     * @param string $field
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function addOrderField($field)
    {
        $this->setOrder($field, self::SORT_ORDER_ASC);
        return $this;
    }

    /**
     * Set disable flat flag
     *
     * @param bool $flag
     * @return Shaurmalab_Score_Model_Resource_Category_Collection
     */
    public function setDisableFlat($flag)
    {
        $this->_disableFlat = (bool) $flag;
        return $this;
    }

    /**
     * Retrieve disable flat flag value
     *
     * @return bool
     */
    public function getDisableFlat()
    {
        return $this->_disableFlat;
    }

    /**
     * Retrieve collection empty item
     *
     * @return Shaurmalab_Score_Model_Category
     */
    public function getNewEmptyItem()
    {
        return new $this->_itemObjectClass(array('disable_flat' => $this->getDisableFlat()));
    }
}
