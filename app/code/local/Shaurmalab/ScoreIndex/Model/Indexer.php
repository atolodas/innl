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
 * ScoreIndex Index operation model
 *
 * @method Shaurmalab_ScoreIndex_Model_Resource_Indexer _getResource()
 * @method Shaurmalab_ScoreIndex_Model_Resource_Indexer getResource()
 * @method int getEntityTypeId()
 * @method Shaurmalab_ScoreIndex_Model_Indexer setEntityTypeId(int $value)
 * @method int getAttributeSetId()
 * @method Shaurmalab_ScoreIndex_Model_Indexer setAttributeSetId(int $value)
 * @method string getTypeId()
 * @method Shaurmalab_ScoreIndex_Model_Indexer setTypeId(string $value)
 * @method string getSku()
 * @method Shaurmalab_ScoreIndex_Model_Indexer setSku(string $value)
 * @method int getHasOptions()
 * @method Shaurmalab_ScoreIndex_Model_Indexer setHasOptions(int $value)
 * @method int getRequiredOptions()
 * @method Shaurmalab_ScoreIndex_Model_Indexer setRequiredOptions(int $value)
 * @method string getCreatedAt()
 * @method Shaurmalab_ScoreIndex_Model_Indexer setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Shaurmalab_ScoreIndex_Model_Indexer setUpdatedAt(string $value)
 *
 * @category    Mage
 * @package     Shaurmalab_ScoreIndex
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_ScoreIndex_Model_Indexer extends Mage_Core_Model_Abstract
{
    const REINDEX_TYPE_ALL = 0;
    const REINDEX_TYPE_PRICE = 1;
    const REINDEX_TYPE_ATTRIBUTE = 2;

    const STEP_SIZE = 1000;

    /**
     * Set of available indexers
     * Each indexer type is responsable for index data storage
     *
     * @var array
     */
    protected $_indexers = array();

    /**
     * Predefined set of indexer types which are related with oggetto price
     *
     * @var array
     */
    protected $_priceIndexers = array('price', 'tier_price', 'minimal_price');

    /**
     * Predefined sets of indexer types which are related
     * with oggetto filterable attributes
     *
     * @var array
     */
    protected $_attributeIndexers = array('eav');

    /**
     * Toggetto types sorted by index priority
     *
     * @var array
     */
    protected $_oggettoTypePriority = null;

    /**
     * Initialize all indexers and resource model
     *
     */
    protected function _construct()
    {
        $this->_loadIndexers();
        $this->_init('scoreindex/indexer');
    }

    /**
     * Create instances of all index types
     *
     * @return Shaurmalab_ScoreIndex_Model_Indexer
     */
    protected function _loadIndexers()
    {
        foreach ($this->_getRegisteredIndexers() as $name=>$class) {
            $this->_indexers[$name] = Mage::getSingleton($class);
        }
        return $this;
    }

    /**
     * Get all registered in configuration indexers
     *
     * @return array
     */
    protected function _getRegisteredIndexers()
    {
        $result = array();
        $indexerRegistry = Mage::getConfig()->getNode('global/scoreindex/indexer');

        foreach ($indexerRegistry->children() as $node) {
            $result[$node->getName()] = (string) $node->class;
        }
        return $result;
    }

    /**
     * Get array of attribute codes required for indexing
     * Each indexer type provide his own set of attributes
     *
     * @return array
     */
    protected function _getIndexableAttributeCodes()
    {
        $result = array();
        foreach ($this->_indexers as $indexer) {
            $codes = $indexer->getIndexableAttributeCodes();

            if (is_array($codes))
                $result = array_merge($result, $codes);
        }
        return $result;
    }

    /**
     * Retreive store collection
     *
     * @return array
     */
    protected function _getStores()
    {
        $stores = $this->getData('_stores');
        if (is_null($stores)) {
            $stores = Mage::app()->getStores();
            $this->setData('_stores', $stores);
        }
        return $stores;
    }

    /**
     * Retreive store collection
     *
     * @return Mage_Core_Model_Mysql4_Store_Collection
     */
    protected function _getWebsites()
    {
        $websites = $this->getData('_websites');
        if (is_null($websites)) {
            $websites = Mage::getModel('core/website')->getCollection()->load();
            /* @var $stores Mage_Core_Model_Mysql4_Website_Collection */

            $this->setData('_websites', $websites);
        }
        return $websites;
    }

    /**
     * Remove index data for specifuc oggetto
     *
     * @param   mixed $oggetto
     * @return  Shaurmalab_ScoreIndex_Model_Indexer
     */
    public function cleanup($oggetto)
    {
        $store = $oggetto->getNeedStoreForReindex() === true ? $this->_getStores() : null;
        $this->_getResource()->clear(true, true, true, true, true, $oggetto, $store);

        return $this;
    }

    /**
     * Reindex score oggetto data which used in layered navigation and in oggetto list
     *
     * @param   mixed $oggettos
     * @param   mixed $attributes
     * @param   mixed $stores
     * @return  Shaurmalab_ScoreIndex_Model_Indexer
     */
    public function plainReindex($oggettos = null, $attributes = null, $stores = null)
    {
        /**
         * Check indexer flag
         */
        $flag = Mage::getModel('scoreindex/score_index_flag')->loadSelf();
        if ($flag->getState() == Shaurmalab_ScoreIndex_Model_Score_Index_Flag::STATE_RUNNING) {
            return $this;
        }
        /*if ($flag->getState() == Shaurmalab_ScoreIndex_Model_Catalog_Index_Flag::STATE_QUEUED)*/
        else {
            $flag->setState(Shaurmalab_ScoreIndex_Model_Score_Index_Flag::STATE_RUNNING)->save();
        }

        try {
            /**
             * Collect initialization data
             */
            $websites = array();
            $attributeCodes = $priceAttributeCodes = array();
//            $status = Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED;
//            $visibility = array(
//                Shaurmalab_Score_Model_Oggetto_Visibility::VISIBILITY_BOTH,
//                Shaurmalab_Score_Model_Oggetto_Visibility::VISIBILITY_IN_CATALOG,
//                Shaurmalab_Score_Model_Oggetto_Visibility::VISIBILITY_IN_SEARCH,
//            );

            /**
             * Prepare stores and websites information
             */
            if (is_null($stores)) {
                $stores     = $this->_getStores();
                $websites   = $this->_getWebsites();
            }
            elseif ($stores instanceof Mage_Core_Model_Store) {
                $websites[] = $stores->getWebsiteId();
                $stores     = array($stores);
            }
            elseif (is_array($stores)) {
                foreach ($stores as $one) {
                    $websites[] = Mage::app()->getStore($one)->getWebsiteId();
                }
            }
            elseif (!is_array($stores)) {
                Mage::throwException('Invalid stores supplied for indexing');
            }

            /**
             * Prepare attributes data
             */
            if (is_null($attributes)) {
                $priceAttributeCodes = $this->_indexers['price']->getIndexableAttributeCodes();
                $attributeCodes = $this->_indexers['eav']->getIndexableAttributeCodes();
            }
            elseif ($attributes instanceof Mage_Eav_Model_Entity_Attribute_Abstract) {
                if ($this->_indexers['eav']->isAttributeIndexable($attributes)) {
                    $attributeCodes[] = $attributes->getAttributeId();
                }
                if ($this->_indexers['price']->isAttributeIndexable($attributes)) {
                    $priceAttributeCodes[] = $attributes->getAttributeId();
                }
            }
            elseif ($attributes == self::REINDEX_TYPE_PRICE) {
                $priceAttributeCodes = $this->_indexers['price']->getIndexableAttributeCodes();
            }
            elseif ($attributes == self::REINDEX_TYPE_ATTRIBUTE) {
                $attributeCodes = $this->_indexers['eav']->getIndexableAttributeCodes();
            }
            else {
                Mage::throwException('Invalid attributes supplied for indexing');
            }

            /**
             * Delete index data
             */
            $this->_getResource()->clear(
                $attributeCodes,
                $priceAttributeCodes,
                count($priceAttributeCodes)>0,
                count($priceAttributeCodes)>0,
                count($priceAttributeCodes)>0,
                $oggettos,
                $stores
            );

            /**
             * Process index price data per each website
             * (prices depends from website level)
             */
            foreach ($websites as $website) {
                $ws = Mage::app()->getWebsite($website);
                if (!$ws) {
                    continue;
                }

                $group = $ws->getDefaultGroup();
                if (!$group) {
                    continue;
                }

                $store = $group->getDefaultStore();

                /**
                 * It can happens when website with store was created but store view not yet
                 */
                if (!$store) {
                    continue;
                }

                foreach ($this->_getPriorifiedOggettoTypes() as $type) {
                    $collection = $this->_getOggettoCollection($store, $oggettos);
                    $collection->addAttributeToFilter(
                        'status',
                        array('in'=>Mage::getModel('score/oggetto_status')->getSaleableStatusIds())
                    );
                    $collection->addFieldToFilter('type_id', $type);
                    $this->_walkCollection($collection, $store, array(), $priceAttributeCodes);
                    if (!is_null($oggettos) && !$this->getRetreiver($type)->getTypeInstance()->isComposite()) {
                        $this->_walkCollectionRelation($collection, $ws, array(), $priceAttributeCodes);
                    }
                }
            }

            /**
             * Process EAV attributes per each store view
             */
            foreach ($stores as $store) {
                foreach ($this->_getPriorifiedOggettoTypes() as $type) {
                    $collection = $this->_getOggettoCollection($store, $oggettos);
                    Mage::getSingleton('score/oggetto_visibility')->addVisibleInSiteFilterToCollection($collection);
                    $collection->addFieldToFilter('type_id', $type);

                    $this->_walkCollection($collection, $store, $attributeCodes);
                    if (!is_null($oggettos) && !$this->getRetreiver($type)->getTypeInstance()->isComposite()) {
                        $this->_walkCollectionRelation($collection, $store, $attributeCodes);
                    }
                }
            }

            $this->_afterPlainReindex($stores, $oggettos);

            /**
             * Score Oggetto Flat price update
             */
            /** @var $oggettoFlatHelper Shaurmalab_Score_Helper_Oggetto_Flat */
            $oggettoFlatHelper = Mage::helper('score/oggetto_flat');
            if ($oggettoFlatHelper->isAvailable() && $oggettoFlatHelper->isBuilt()) {
                foreach ($stores as $store) {
                    $this->updateScoreOggettoFlat($store, $oggettos);
                }
            }

        } catch (Exception $e) {
            $flag->delete();
            throw $e;
        }

        if ($flag->getState() == Shaurmalab_ScoreIndex_Model_Score_Index_Flag::STATE_RUNNING) {
            $flag->delete();
        }

        return $this;
    }

    /**
     * After plain reindex process
     *
     * @param Mage_Core_Model_Store|array|int|Mage_Core_Model_Website $store
     * @param int|array|Shaurmalab_Score_Model_Oggetto_Condition_Interface|Shaurmalab_Score_Model_Oggetto $oggettos
     * @return Shaurmalab_ScoreIndex_Model_Indexer
     */
    protected function _afterPlainReindex($store, $oggettos = null)
    {
        Mage::dispatchEvent('scoreindex_plain_reindex_after', array(
            'oggettos' => $oggettos
        ));

        /**
         * Score Oggetto Flat price update
         */
        /** @var $oggettoFlatHelper Shaurmalab_Score_Helper_Oggetto_Flat */
        $oggettoFlatHelper = Mage::helper('score/oggetto_flat');
        if ($oggettoFlatHelper->isAvailable() && $oggettoFlatHelper->isBuilt()) {
            if ($store instanceof Mage_Core_Model_Website) {
                foreach ($store->getStores() as $storeObject) {
                    $this->_afterPlainReindex($storeObject->getId(), $oggettos);
                }
                return $this;
            }
            elseif ($store instanceof Mage_Core_Model_Store) {
                $store = $store->getId();
            }
            // array of stores
            elseif (is_array($store)) {
                foreach ($store as $storeObject) {
                    $this->_afterPlainReindex($storeObject->getId(), $oggettos);
                }
                return $this;
            }

            $this->updateScoreOggettoFlat($store, $oggettos);
        }

        return $this;
    }

    /**
     * Return collection with oggetto and store filters
     *
     * @param Mage_Core_Model_Store $store
     * @param mixed $oggettos
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Collection
     */
    protected function _getOggettoCollection($store, $oggettos)
    {
        $collection = Mage::getModel('score/oggetto')
            ->getCollection()
            ->setStoreId($store)
            ->addStoreFilter($store);
        if ($oggettos instanceof Shaurmalab_Score_Model_Oggetto) {
            $collection->addIdFilter($oggettos->getId());
        } else if (is_array($oggettos) || is_numeric($oggettos)) {
            $collection->addIdFilter($oggettos);
        } elseif ($oggettos instanceof Shaurmalab_Score_Model_Oggetto_Condition_Interface) {
            $oggettos->applyToCollection($collection);
        }

        return $collection;
    }

    /**
     * Walk Oggetto Collection for Relation Parent oggettos
     *
     * @param Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Collection $collection
     * @param Mage_Core_Model_Store|Mage_Core_Model_Website $store
     * @param array $attributes
     * @param array $prices
     * @return Shaurmalab_ScoreIndex_Model_Indexer
     */
    public function _walkCollectionRelation($collection, $store, $attributes = array(), $prices = array())
    {
        if ($store instanceof Mage_Core_Model_Website) {
            $storeObject = $store->getDefaultStore();
        }
        elseif ($store instanceof Mage_Core_Model_Store) {
            $storeObject = $store;
        }

        $statusCond = array(
            'in' => Mage::getSingleton('score/oggetto_status')->getSaleableStatusIds()
        );

        $oggettoCount = $collection->getSize();
        $iterateCount = ($oggettoCount / self::STEP_SIZE);
        for ($i = 0; $i < $iterateCount; $i++) {
            $stepData = $collection
                ->getAllIds(self::STEP_SIZE, $i * self::STEP_SIZE);
            foreach ($this->_getPriorifiedOggettoTypes() as $type) {
                $retriever = $this->getRetreiver($type);
                if (!$retriever->getTypeInstance()->isComposite()) {
                    continue;
                }

                $parentIds = $retriever->getTypeInstance()
                    ->getParentIdsByChild($stepData);
                if ($parentIds) {
                    $parentCollection = $this->_getOggettoCollection($storeObject, $parentIds);
                    $parentCollection->addAttributeToFilter('status', $statusCond);
                    $parentCollection->addFieldToFilter('type_id', $type);
                    $this->_walkCollection($parentCollection, $storeObject, $attributes, $prices);

                    $this->_afterPlainReindex($store, $parentIds);
                }
            }
        }

        return $this;
    }

    /**
     * Run indexing process for oggetto collection
     *
     * @param   Shaurmalab_Score_Resource_Eav_Mysql4_Oggetto_Collection $collection
     * @param   mixed $store
     * @param   array $attributes
     * @param   array $prices
     * @return  Shaurmalab_ScoreIndex_Model_Indexer
     */
    protected function _walkCollection($collection, $store, $attributes = array(), $prices = array())
    {
        $oggettoCount = $collection->getSize();
        if (!$oggettoCount) {
            return $this;
        }

        for ($i=0;$i<$oggettoCount/self::STEP_SIZE;$i++) {
            $this->_getResource()->beginTransaction();

            $stepData = $collection->getAllIds(self::STEP_SIZE, $i*self::STEP_SIZE);

            /**
             * Reindex EAV attributes if required
             */
            if (count($attributes)) {
                $this->_getResource()->reindexAttributes($stepData, $attributes, $store);
            }

            /**
             * Reindex prices if required
             */
            if (count($prices)) {
                $this->_getResource()->reindexPrices($stepData, $prices, $store);
                $this->_getResource()->reindexTiers($stepData, $store);
                $this->_getResource()->reindexMinimalPrices($stepData, $store);
                $this->_getResource()->reindexFinalPrices($stepData, $store);
            }

            Mage::getResourceSingleton('score/oggetto')->refreshEnabledIndex($store, $stepData);

            $kill = Mage::getModel('scoreindex/score_index_kill_flag')->loadSelf();
            if ($kill->checkIsThisProcess()) {
                $this->_getResource()->rollBack();
                $kill->delete();
            } else {
                $this->_getResource()->commit();
            }
        }
        return $this;
    }

    /**
     * Retrieve Data retreiver
     *
     * @param string $type
     * @return Shaurmalab_ScoreIndex_Model_Data_Abstract
     */
    public function getRetreiver($type)
    {
        return Mage::getSingleton('scoreindex/retreiver')->getRetreiver($type);
    }

    /**
     * Set ScoreIndex Flag as queue Indexing
     *
     * @return Shaurmalab_ScoreIndex_Model_Indexer
     */
    public function queueIndexing()
    {
        Mage::getModel('scoreindex/score_index_flag')
            ->loadSelf()
            ->setState(Shaurmalab_ScoreIndex_Model_Score_Index_Flag::STATE_QUEUED)
            ->save();

        return $this;
    }

    /**
     * Get oggetto types list by type priority
     * type priority is important in index process
     * example: before indexing complex (configurable, grouped etc.) oggettos
     * we have to index all simple oggettos
     *
     * @return array
     */
    protected function _getPriorifiedOggettoTypes()
    {
        if (is_null($this->_oggettoTypePriority)) {
            $this->_oggettoTypePriority = array();
            $config = Mage::getConfig()->getNode('global/score/oggetto/type');

            foreach ($config->children() as $type) {
                $typeName = $type->getName();
                $typePriority = (string) $type->index_priority;
                $this->_oggettoTypePriority[$typePriority] = $typeName;
            }
            ksort($this->_oggettoTypePriority);
        }
        return $this->_oggettoTypePriority;
    }

    /**
     * Retrieve Base to Specified Currency Rate
     *
     * @param string $code
     * @return double
     */
    protected function _getBaseToSpecifiedCurrencyRate($code)
    {
        return Mage::app()->getStore()->getBaseCurrency()->getRate($code);
    }

    /**
     * Build Entity price filter
     *
     * @param array $attributes
     * @param array $values
     * @param array $filteredAttributes
     * @param Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Collection $oggettoCollection
     * @return array
     */
    public function buildEntityPriceFilter($attributes, $values, &$filteredAttributes, $oggettoCollection)
    {
        $additionalCalculations = array();
        $filter = array();
        $store = Mage::app()->getStore()->getId();
        $website = Mage::app()->getStore()->getWebsiteId();

        $currentStoreCurrency = Mage::app()->getStore()->getCurrentCurrencyCode();

        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if (isset($values[$code])) {
                foreach ($this->_priceIndexers as $indexerName) {
                    $indexer = $this->_indexers[$indexerName];
                    /* @var $indexer Shaurmalab_ScoreIndex_Model_Indexer_Abstract */
                    if ($indexer->isAttributeIndexable($attribute)) {
                        if ($values[$code]) {
                            if (isset($values[$code]['from']) && isset($values[$code]['to'])
                                && (strlen($values[$code]['from']) == 0 && strlen($values[$code]['to']) == 0)) {
                                continue;
                            }
                            $table = $indexer->getResource()->getMainTable();
                            if (!isset($filter[$code])) {
                                $filter[$code] = $this->_getSelect();
                                $filter[$code]->from($table, array('entity_id'));
                                $filter[$code]->distinct(true);

                                $response = new Varien_Object();
                                $response->setAdditionalCalculations(array());
                                $args = array(
                                    'select'=>$filter[$code],
                                    'table'=>$table,
                                    'store_id'=>$store,
                                    'response_object'=>$response,
                                );
                                Mage::dispatchEvent('scoreindex_prepare_price_select', $args);
                                $additionalCalculations[$code] = $response->getAdditionalCalculations();

                                if ($indexer->isAttributeIdUsed()) {
                                    //$filter[$code]->where("$table.attribute_id = ?", $attribute->getId());
                                }
                            }
                            if (is_array($values[$code])) {
                                $rateConversion = 1;
                                $filter[$code]->distinct(true);

                                if (isset($values[$code]['from']) && isset($values[$code]['to'])) {
                                    if (isset($values[$code]['currency'])) {
                                        $rateConversion = $this->_getBaseToSpecifiedCurrencyRate(
                                            $values[$code]['currency']
                                        );
                                    } else {
                                        $rateConversion = $this->_getBaseToSpecifiedCurrencyRate($currentStoreCurrency);
                                    }

                                    if (strlen($values[$code]['from']) > 0) {
                                        $filter[$code]->where(
                                            "($table.min_price"
                                            . implode('', $additionalCalculations[$code]).")*{$rateConversion} >= ?",
                                            $values[$code]['from']
                                        );
                                    }

                                    if (strlen($values[$code]['to']) > 0) {
                                        $filter[$code]->where(
                                            "($table.min_price"
                                            . implode('', $additionalCalculations[$code]).")*{$rateConversion} <= ?",
                                            $values[$code]['to']
                                        );
                                    }
                                }
                            }
                            $filter[$code]->where("$table.website_id = ?", $website);

                            if ($code == 'price') {
                                $filter[$code]->where(
                                    $table . '.customer_group_id = ?',
                                    Mage::getSingleton('customer/session')->getCustomerGroupId()
                                );
                            }

                            $filteredAttributes[]=$code;
                        }
                    }
                }
            }
        }
        return $filter;
    }

    /**
     * Build Entity filter
     *
     * @param array $attributes
     * @param array $values
     * @param array $filteredAttributes
     * @param Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Collection $oggettoCollection
     * @return array
     */
    public function buildEntityFilter($attributes, $values, &$filteredAttributes, $oggettoCollection)
    {
        $filter = array();
        $store = Mage::app()->getStore()->getId();

        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if (isset($values[$code])) {
                foreach ($this->_attributeIndexers as $indexerName) {
                    $indexer = $this->_indexers[$indexerName];
                    /* @var $indexer Shaurmalab_ScoreIndex_Model_Indexer_Abstract */
                    if ($indexer->isAttributeIndexable($attribute)) {
                        if ($values[$code]) {
                            if (isset($values[$code]['from']) && isset($values[$code]['to'])
                                && (!$values[$code]['from'] && !$values[$code]['to'])) {
                                continue;
                            }

                            $table = $indexer->getResource()->getMainTable();
                            if (!isset($filter[$code])) {
                                $filter[$code] = $this->_getSelect();
                                $filter[$code]->from($table, array('entity_id'));
                            }
                            if ($indexer->isAttributeIdUsed()) {
                                $filter[$code]->where('attribute_id = ?', $attribute->getId());
                            }
                            if (is_array($values[$code])) {
                                if (isset($values[$code]['from']) && isset($values[$code]['to'])) {

                                    if ($values[$code]['from']) {
                                        if (!is_numeric($values[$code]['from'])) {
                                            $_date = date("Y-m-d H:i:s", strtotime($values[$code]['from']));
                                            $values[$code]['from'] = $_date;
                                        }

                                        $filter[$code]->where("value >= ?", $values[$code]['from']);
                                    }


                                    if ($values[$code]['to']) {
                                        if (!is_numeric($values[$code]['to'])) {
                                            $values[$code]['to'] = date("Y-m-d H:i:s", strtotime($values[$code]['to']));
                                        }
                                        $filter[$code]->where("value <= ?", $values[$code]['to']);
                                    }
                                } else {
                                    $filter[$code]->where('value in (?)', $values[$code]);
                                }
                            } else {
                                $filter[$code]->where('value = ?', $values[$code]);
                            }
                            $filter[$code]->where('store_id = ?', $store);
                            $filteredAttributes[]=$code;
                        }
                    }
                }
            }
        }
        return $filter;
    }

    /**
     * Retrieve SELECT object
     *
     * @return Varien_Db_Select
     */
    protected function _getSelect()
    {
        return $this->_getResource()->getReadConnection()->select();
    }

    /**
     * Add indexable attributes to oggetto collection select
     *
     * @deprecated
     * @param   $collection
     * @return  Shaurmalab_ScoreIndex_Model_Indexer
     */
    protected function _addFilterableAttributesToCollection($collection)
    {
        $attributeCodes = $this->_getIndexableAttributeCodes();
        foreach ($attributeCodes as $code) {
            $collection->addAttributeToSelect($code);
        }

        return $this;
    }

/**
     * Prepare Score Oggetto Flat Columns
     *
     * @param Varien_Object $object
     * @return Shaurmalab_ScoreIndex_Model_Indexer
     */
    public function prepareScoreOggettoFlatColumns(Varien_Object $object)
    {
        $this->_getResource()->prepareScoreOggettoFlatColumns($object);

        return $this;
    }

    /**
     * Prepare Score Oggetto Flat Indexes
     *
     * @param Varien_Object $object
     * @return Shaurmalab_ScoreIndex_Model_Indexer
     */
    public function prepareScoreOggettoFlatIndexes(Varien_Object $object)
    {
        $this->_getResource()->prepareScoreOggettoFlatIndexes($object);

        return $this;
    }

    /**
     * Update price process for score oggetto flat
     *
     * @param mixed $storeId
     * @param string $resourceTable
     * @param mixed $oggettos
     * @return Shaurmalab_ScoreIndex_Model_Indexer
     */
    public function updateScoreOggettoFlat($store, $oggettos = null, $resourceTable = null)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = $store->getId();
        }
        if ($oggettos instanceof Shaurmalab_Score_Model_Oggetto) {
            $oggettos = $oggettos->getId();
        }
        $this->_getResource()->updateScoreOggettoFlat($store, $oggettos, $resourceTable);

        return $this;
    }
}
