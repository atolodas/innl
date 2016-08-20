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
 * Event observer and indexer running application
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_ScoreIndex_Model_Observer extends Mage_Core_Model_Abstract
{
    protected $_parentOggettoIds = array();
    protected $_oggettoIdsMassupdate = array();

    protected function _construct() {}

    /**
     * Get indexer object
     *
     * @return Shaurmalab_ScoreIndex_Model_Indexer
     */
    protected function _getIndexer()
    {
        return Mage::getSingleton('scoreindex/indexer');
    }

    /**
     * Get aggregation object
     *
     * @return Shaurmalab_ScoreIndex_Model_Aggregation
     */
    protected function _getAggregator()
    {
        return Mage::getSingleton('scoreindex/aggregation');
    }

    /**
     * Reindex all score data
     *
     * @return Shaurmalab_ScoreIndex_Model_Observer
     */
    public function reindexAll()
    {
        $this->_getIndexer()->plainReindex();
        $this->_getAggregator()->clearCacheData();
        return $this;
    }

    /**
     * Reindex daily related data (prices)
     *
     * @return Shaurmalab_ScoreIndex_Model_Observer
     */
    public function reindexDaily()
    {
        $this->_getIndexer()->plainReindex(
            null,
            Shaurmalab_ScoreIndex_Model_Indexer::REINDEX_TYPE_PRICE
        );
        $this->clearPriceAggregation();
        return $this;
    }

    /**
     * Process oggetto after save
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_ScoreIndex_Model_Observer
     */
    public function processAfterSaveEvent(Varien_Event_Observer $observer)
    {
        $oggettoIds = array();
        $eventOggetto = $observer->getEvent()->getOggetto();
        $oggettoIds[] = $eventOggetto->getId();

        if (!$eventOggetto->getIsMassupdate()) {
            $this->_getIndexer()->plainReindex($eventOggetto);
        } else {
            $this->_oggettoIdsMassupdate[] = $eventOggetto->getId();
        }

        $eventOggetto->loadParentOggettoIds();
        $parentOggettoIds = $eventOggetto->getParentOggettoIds();
        if ($parentOggettoIds && !$eventOggetto->getIsMassupdate()) {
            $this->_getIndexer()->plainReindex($parentOggettoIds);
        } elseif ($parentOggettoIds) {
            $this->_oggettoIdsMassupdate = array_merge($this->_oggettoIdsMassupdate, $parentOggettoIds);
            $oggettoIds = array_merge($oggettoIds, $parentOggettoIds);
        }
        $this->_getAggregator()->clearOggettoData($oggettoIds);
        return $this;
    }

    /**
     * Reindex price data after attribute scope change
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_ScoreIndex_Model_Observer
     */
    public function processPriceScopeChange(Varien_Event_Observer $observer)
    {
        $configOption   = $observer->getEvent()->getOption();
        if ($configOption->isValueChanged()) {
            $this->_getIndexer()->plainReindex(
                null,
                Shaurmalab_ScoreIndex_Model_Indexer::REINDEX_TYPE_PRICE
            );
            $this->clearPriceAggregation();
        }
        return $this;
    }

    /**
     * Process score index after price rules were applied
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_ScoreIndex_Model_Observer
     */
    public function processPriceRuleApplication(Varien_Event_Observer $observer)
    {
        $eventOggetto = $observer->getEvent()->getOggetto();
        $oggettoCondition = $observer->getEvent()->getOggettoCondition();
        if ($oggettoCondition) {
            $eventOggetto = $oggettoCondition;
        }
        $this->_getIndexer()->plainReindex(
            $eventOggetto,
            Shaurmalab_ScoreIndex_Model_Indexer::REINDEX_TYPE_PRICE
        );

        $this->clearPriceAggregation();
        return $this;
    }

    /**
     * Cleanup oggetto index after oggetto delete
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_ScoreIndex_Model_Observer
     */
    public function processAfterDeleteEvent(Varien_Event_Observer $observer)
    {
        $eventOggetto = $observer->getEvent()->getOggetto();
        $eventOggetto->setNeedStoreForReindex(true);
        $this->_getIndexer()->cleanup($eventOggetto);
        $parentOggettoIds = $eventOggetto->getParentOggettoIds();

        if ($parentOggettoIds) {
            $this->_getIndexer()->plainReindex($parentOggettoIds);
        }
        return $this;
    }

    /**
     * Process index data after attribute information was changed
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_ScoreIndex_Model_Observer
     */
    public function processAttributeChangeEvent(Varien_Event_Observer $observer)
    {
        /**
         * @todo add flag to attribute model which will notify what options was changed
         */
        $attribute = $observer->getEvent()->getAttribute();
        $tags = array(
            Mage_Eav_Model_Entity_Attribute::CACHE_TAG.':'.$attribute->getId()
        );

        if ($attribute->getOrigData('is_filterable') != $attribute->getIsFilterable()) {
            if ($attribute->getIsFilterable() != 0) {
                $this->_getIndexer()->plainReindex(null, $attribute);
            } else {
                $this->_getAggregator()->clearCacheData($tags);
            }
        } elseif ($attribute->getIsFilterable()) {
            $this->_getAggregator()->clearCacheData($tags);
        }

        return $this;
    }

    /**
     * Create index for new store
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_ScoreIndex_Model_Observer
     */
    public function processStoreAdd(Varien_Event_Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        $this->_getIndexer()->plainReindex(null, null, $store);
        return $this;
    }

    /**
     * Rebuild index after score import
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_ScoreIndex_Model_Observer
     */
    public function scoreOggettoImportAfter(Varien_Event_Observer $observer)
    {
        $this->_getIndexer()->plainReindex();
        $this->_getAggregator()->clearCacheData();
        return $this;
    }

    /**
     * Run planed reindex
     *
     * @return Shaurmalab_ScoreIndex_Model_Observer
     */
    public function runQueuedIndexing()
    {
        $flag = Mage::getModel('scoreindex/score_index_flag')->loadSelf();
        if ($flag->getState() == Shaurmalab_ScoreIndex_Model_Score_Index_Flag::STATE_QUEUED) {
            $this->_getIndexer()->plainReindex();
            $this->_getAggregator()->clearCacheData();
        }
        return $this;
    }

    /**
     * Clear aggregated layered navigation data
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_ScoreIndex_Model_Observer
     */
    public function cleanCache(Varien_Event_Observer $observer)
    {
        $tagsArray = $observer->getEvent()->getTags();
        $tagName = Shaurmalab_ScoreIndex_Model_Aggregation::CACHE_FLAG_NAME;

        if (empty($tagsArray) || in_array($tagName, $tagsArray)) {
            $this->_getAggregator()->clearCacheData();
        }
        return $this;
    }

    /**
     * Process index data after category save
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_ScoreIndex_Model_Observer
     */
    public function scoreCategorySaveAfter(Varien_Event_Observer $observer)
    {
        // $category = $observer->getEvent()->getCategory();
        // if ($category->getInitialSetupFlag()) {
        //     return $this;
        // }
        // $tags = array(
        //     Shaurmalab_Score_Model_Category::CACHE_TAG.':'.$category->getPath()
        // );
        // $this->_getAggregator()->clearCacheData($tags);
        return $this;
    }

    /**
     * Delete price aggreagation data
     *
     * @return Shaurmalab_ScoreIndex_Model_Observer
     */
    public function clearPriceAggregation()
    {
        $this->_getAggregator()->clearCacheData(array(
            Shaurmalab_Score_Model_Oggetto_Type_Price::CACHE_TAG
        ));
        return $this;
    }

    /**
     * Clear layer navigation cache for search results
     *
     * @return Shaurmalab_ScoreIndex_Model_Observer
     */
    public function clearSearchLayerCache()
    {
        $this->_getAggregator()->clearCacheData(array(
            Shaurmalab_ScoreSearch_Model_Query::CACHE_TAG
        ));
        return $this;
    }

    /**
     * Load parent ids for oggettos before deleting
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_ScoreIndex_Model_Observer
     */
    public function registerParentIds(Varien_Event_Observer $observer)
    {
        $oggetto = $observer->getEvent()->getOggetto();
        $oggetto->loadParentOggettoIds();
        $oggettoIds = array($oggetto->getId());
        $oggettoIds = array_merge($oggettoIds, $oggetto->getParentOggettoIds());
        $this->_getAggregator()->clearOggettoData($oggettoIds);
        return $this;
    }

    /**
     * Reindex producs after change websites associations
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_ScoreIndex_Model_Observer
     */
    public function processOggettosWebsitesChange(Varien_Event_Observer $observer)
    {
        $oggettoIds = $observer->getEvent()->getOggettos();
        $this->_getIndexer()->plainReindex($oggettoIds);
        $this->_getAggregator()->clearOggettoData($oggettoIds);
        return $this;
    }

    /**
     * Prepare columns for score oggetto flat
     *
     * @param Varien_Event_Observer $observer
     * @return Shaurmalab_ScoreIndex_Model_Observer
     */
    public function scoreOggettoFlatPrepareColumns(Varien_Event_Observer $observer)
    {
        $columns = $observer->getEvent()->getColumns();

        $this->_getIndexer()->prepareScoreOggettoFlatColumns($columns);

        return $this;
    }

    /**
     * Prepare indexes for score oggetto flat
     *
     * @param Varien_Event_Observer $observer
     * @return Shaurmalab_ScoreIndex_Model_Observer
     */
    public function scoreOggettoFlatPrepareIndexes(Varien_Event_Observer $observer)
    {
        $indexes = $observer->getEvent()->getIndexes();

        $this->_getIndexer()->prepareScoreOggettoFlatIndexes($indexes);

        return $this;
    }

    /**
     * Rebuild score oggetto flat
     *
     * @param Varien_Event_Observer $observer
     * @return Shaurmalab_ScoreIndex_Model_Observer
     */
    public function scoreOggettoFlatRebuild(Varien_Event_Observer $observer)
    {
        $storeId    = $observer->getEvent()->getStoreId();
        $tableName  = $observer->getEvent()->getTable();

        $this->_getIndexer()->updateScoreOggettoFlat($storeId, null, $tableName);

        return $this;
    }

    /**
     * Score Oggetto Flat update oggetto(s)
     *
     * @param Varien_Event_Observer $observer
     * @return Shaurmalab_ScoreIndex_Model_Observer
     */
    public function scoreOggettoFlatUpdateOggetto(Varien_Event_Observer $observer)
    {
        $storeId    = $observer->getEvent()->getStoreId();
        $tableName  = $observer->getEvent()->getTable();
        $oggettoIds = $observer->getEvent()->getOggettoIds();

        $this->_getIndexer()->updateScoreOggettoFlat($storeId, $oggettoIds, $tableName);

        return $this;
    }
}
