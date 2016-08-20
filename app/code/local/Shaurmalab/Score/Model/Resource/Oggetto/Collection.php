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
 * Oggetto collection
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Oggetto_Collection extends Shaurmalab_Score_Model_Resource_Collection_Abstract
{
    /**
     * Alias for index table
     */
    const INDEX_TABLE_ALIAS = 'price_index';

    /**
     * Alias for main table
     */
    const MAIN_TABLE_ALIAS = 'e';

    /**
     * Score Oggetto Flat is enabled cache per store
     *
     * @var array
     */
    protected $_flatEnabled                  = array();

    /**
     * Oggetto websites table name
     *
     * @var string
     */
    protected $_oggettoWebsiteTable;

    /**
     * Oggetto categories table name
     *
     * @var string
     */
    protected $_oggettoCategoryTable;

    /**
     * Is add URL rewrites to collection flag
     *
     * @var bool
     */
    protected $_addUrlRewrite                = false;

    /**
     * Add URL rewrite for category
     *
     * @var int
     */
    protected $_urlRewriteCategory           = '';

    /**
     * Is add minimal price to oggetto collection flag
     *
     * @var bool
     */
    protected $_addMinimalPrice              = false;

    /**
     * Is add final price to oggetto collection flag
     *
     * @var unknown_type
     */
    protected $_addFinalPrice                = false;

    /**
     * Cache for all ids
     *
     * @var array
     */
    protected $_allIdsCache                  = null;

    /**
     * Is add tax percents to oggetto collection flag
     *
     * @var bool
     */
    protected $_addTaxPercents               = false;

    /**
     * Oggetto limitation filters
     * Allowed filters
     *  store_id                int;
     *  category_id             int;
     *  category_is_anchor      int;
     *  visibility              array|int;
     *  website_ids             array|int;
     *  store_table             string;
     *  use_price_index         bool;   join price index table flag
     *  customer_group_id       int;    required for price; customer group limitation for price
     *  website_id              int;    required for price; website limitation for price
     *
     * @var array
     */
    protected $_oggettoLimitationFilters     = array();

    /**
     * Category oggetto count select
     *
     * @var Zend_Db_Select
     */
    protected $_oggettoCountSelect           = null;

    /**
     * Enter description here ...
     *
     * @var bool
     */
    protected $_isWebsiteFilter              = false;

    /**
     * Additional field filters, applied in _oggettoLimitationJoinPrice()
     *
     * @var array
     */
    protected $_priceDataFieldFilters = array();

    /**
     * Map of price fields
     *
     * @var array
     */
    protected $_map = array('fields' => array(
        'price'         => 'price_index.price',
        'final_price'   => 'price_index.final_price',
        'min_price'     => 'price_index.min_price',
        'max_price'     => 'price_index.max_price',
        'tier_price'    => 'price_index.tier_price',
        'special_price' => 'price_index.special_price',
    ));

    /**
     * Price expression sql
     *
     * @var string|null
     */
    protected $_priceExpression;

    /**
     * Additional price expression sql part
     *
     * @var string|null
     */
    protected $_additionalPriceExpression;

    /**
     * Max prise (statistics data)
     *
     * @var float
     */
    protected $_maxPrice;

    /**
     * Min prise (statistics data)
     *
     * @var float
     */
    protected $_minPrice;

    /**
     * Prise standard deviation (statistics data)
     *
     * @var float
     */
    protected $_priceStandardDeviation;

    /**
     * Prises count (statistics data)
     *
     * @var int
     */
    protected $_pricesCount = null;

    /**
     * Cloned Select after dispatching 'catalog_prepare_price_select' event
     *
     * @var Varien_Db_Select
     */
    protected $_scorePreparePriceSelectcatalog = null;

    /**
     * Score factory instance
     *
     * @var Shaurmalab_Score_Model_Factory
     */
    protected $_factory;

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
     * Get cloned Select after dispatching 'catalog_prepare_price_select' event
     *
     * @return Varien_Db_Select
     */
    public function getScorePreparedSelect()
    {
        return $this->_scorePreparePriceSelect;
    }

    /**
     * Prepare additional price expression sql part
     *
     * @param Varien_Db_Select $select
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _preparePriceExpressionParameters($select)
    {
        // prepare response object for event
        $response = new Varien_Object();
        $response->setAdditionalCalculations(array());
        $tableAliases = array_keys($select->getPart(Zend_Db_Select::FROM));
        if (in_array(self::INDEX_TABLE_ALIAS, $tableAliases)) {
            $table = self::INDEX_TABLE_ALIAS;
        } else {
            $table = reset($tableAliases);
        }

        // prepare event arguments
        $eventArgs = array(
            'select'          => $select,
            'table'           => $table,
            'store_id'        => $this->getStoreId(),
            'response_object' => $response
        );

        Mage::dispatchEvent('score_prepare_price_select', $eventArgs);

        $additional   = join('', $response->getAdditionalCalculations());
        $this->_priceExpression = $table . '.min_price';
        $this->_additionalPriceExpression = $additional;
        $this->_scorePreparePriceSelect = clone $select;

        return $this;
    }

    /**
     * Get price expression sql part
     *
     * @param Varien_Db_Select $select
     * @return string
     */
    public function getPriceExpression($select)
    {
        if (is_null($this->_priceExpression)) {
            $this->_preparePriceExpressionParameters($select);
        }
        return $this->_priceExpression;
    }

    /**
     * Get additional price expression sql part
     *
     * @param Varien_Db_Select $select
     * @return string
     */
    public function getAdditionalPriceExpression($select)
    {
        if (is_null($this->_additionalPriceExpression)) {
            $this->_preparePriceExpressionParameters($select);
        }
        return $this->_additionalPriceExpression;
    }

    /**
     * Get currency rate
     *
     * @return float
     */
    public function getCurrencyRate()
    {
        return Mage::app()->getStore($this->getStoreId())->getCurrentCurrencyRate();
    }

    /**
     * Retrieve Score Oggetto Flat Helper object
     *
     * @return Shaurmalab_Score_Helper_Oggetto_Flat
     */
    public function getFlatHelper()
    {
        return Mage::helper('score/oggetto_flat');
    }

    /**
     * Retrieve is flat enabled flag
     * Return always false if magento run admin
     *
     * @return bool
     */
    public function isEnabledFlat()
    {
        // Flat Data can be used only on frontend
        if (Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        $storeId = $this->getStoreId();
        if (!isset($this->_flatEnabled[$storeId])) {
            $flatHelper = $this->getFlatHelper();
            $this->_flatEnabled[$storeId] = $flatHelper->isAvailable() && $flatHelper->isBuilt($storeId);
        }
        return $this->_flatEnabled[$storeId];
    }

    /**
     * Initialize resources
     *
     */
    protected function _construct()
    {
        if ($this->isEnabledFlat()) {
            $this->_init('score/oggetto', 'score/oggetto_flat');
        }
        else {
            $this->_init('score/oggetto');
        }
        $this->_initTables();
    }

    /**
     * Define oggetto website and category oggetto tables
     *
     */
    protected function _initTables()
    {
        $this->_oggettoWebsiteTable = $this->getResource()->getTable('score/oggetto_website');
        $this->_oggettoCategoryTable= $this->getResource()->getTable('score/category_oggetto');
    }

    /**
     * Standard resource collection initalization
     *
     * @param string $model
     * @param unknown_type $entityModel
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _init($model, $entityModel = null)
    {
        if ($this->isEnabledFlat()) {
            $entityModel = 'score/oggetto_flat';
        }

        return parent::_init($model, $entityModel);
    }

    /**
     * Prepare static entity fields
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _prepareStaticFields()
    {
        if ($this->isEnabledFlat()) {
            return $this;
        }
        return parent::_prepareStaticFields();
    }

    /**
     * Retrieve collection empty item
     * Redeclared for specifying id field name without getting resource model inside model
     *
     * @return Varien_Object
     */
    public function getNewEmptyItem()
    {
        $object = parent::getNewEmptyItem();
        if ($this->isEnabledFlat()) {
            $object->setIdFieldName($this->getEntity()->getIdFieldName());
        }
        return $object;
    }

    /**
     * Set entity to use for attributes
     *
     * @param Mage_Eav_Model_Entity_Abstract $entity
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function setEntity($entity)
    {
        if ($this->isEnabledFlat() && ($entity instanceof Mage_Core_Model_Resource_Db_Abstract)) {
            $this->_entity = $entity;
            return $this;
        }
        return parent::setEntity($entity);
    }

    /**
     * Set Store scope for collection
     *
     * @param mixed $store
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function setStore($store)
    {
        parent::setStore($store);
        if ($this->isEnabledFlat()) {
            $this->getEntity()->setStoreId($this->getStoreId());
        }
        return $this;
    }

    /**
     * Initialize collection select
     * Redeclared for remove entity_type_id condition
     * in score_oggetto_entity we store just oggettos
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _initSelect()
    {
        if ($this->isEnabledFlat()) {
            $this->getSelect()
                ->from(array(self::MAIN_TABLE_ALIAS => $this->getEntity()->getFlatTableName()), null)
                ->columns(array('status' => new Zend_Db_Expr(Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED)));
            $this->addAttributeToSelect(array('entity_id', 'type_id', 'attribute_set_id'));
            if ($this->getFlatHelper()->isAddChildData()) {
                $this->getSelect()
                    ->where('e.is_child=?', 0);
                $this->addAttributeToSelect(array('child_id', 'is_child'));
            }
        } else {
            $this->getSelect()->from(array(self::MAIN_TABLE_ALIAS => $this->getEntity()->getEntityTable()));
        }
        return $this;
    }

    /**
     * Load attributes into loaded entities
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function _loadAttributes($printQuery = false, $logQuery = false)
    {
        if ($this->isEnabledFlat()) {
            return $this;
        }
        return parent::_loadAttributes($printQuery, $logQuery);
    }

    /**
     * Add attribute to entities in collection
     * If $attribute=='*' select all attributes
     *
     * @param array|string|integer|Mage_Core_Model_Config_Element $attribute
     * @param false|string $joinType
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addAttributeToSelect($attribute, $joinType = false)
    {


        if ($this->isEnabledFlat()) {
            if (!is_array($attribute)) {
                $attribute = array($attribute);
            }
            foreach ($attribute as $attributeCode) {
                if ($attributeCode == '*') {
                    foreach ($this->getEntity()->getAllTableColumns() as $column) {
                        $this->getSelect()->columns('e.' . $column);
                        $this->_selectAttributes[$column] = $column;
                        $this->_staticFields[$column]     = $column;
                    }
                } else {
                    $columns = $this->getEntity()->getAttributeForSelect($attributeCode);
                    if ($columns) {
                        foreach ($columns as $alias => $column) {
                            $this->getSelect()->columns(array($alias => 'e.' . $column));
                            $this->_selectAttributes[$column] = $column;
                            $this->_staticFields[$column]     = $column;
                        }
                    }
                }
            }
            return $this;
        }
        return parent::addAttributeToSelect($attribute, $joinType);
    }

    /**
     * Add tax class id attribute to select and join price rules data if needed
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _beforeLoad()
    {
        Mage::dispatchEvent('score_oggetto_collection_load_before', array('collection' => $this));

        return parent::_beforeLoad();
    }

    /**
     * Processing collection items after loading
     * Adding url rewrites, minimal prices, final prices, tax percents
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _afterLoad()
    {
        if ($this->_addUrlRewrite) {
           $this->_addUrlRewrite($this->_urlRewriteCategory);
        }

        if (count($this) > 0) {
            Mage::dispatchEvent('score_oggetto_collection_load_after', array('collection' => $this));
        }

        foreach ($this as $oggetto) {
            if ($oggetto->isRecurring() && $profile = $oggetto->getRecurringProfile()) {
                $oggetto->setRecurringProfile(unserialize($profile));
            }
        }

        return $this;
    }

    /**
     * Prepare Url Data object
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     * @deprecated after 1.7.0.2
     */
    protected function _prepareUrlDataObject()
    {
        $objects = array();
        /** @var $item Shaurmalab_Score_Model_Oggetto */
        foreach ($this->_items as $item) {
            if ($this->getFlag('do_not_use_category_id')) {
                $item->setDoNotUseCategoryId(true);
            }
            if (!$item->isVisibleInSiteVisibility() && $item->getItemStoreId()) {
                $objects[$item->getEntityId()] = $item->getItemStoreId();
            }
        }

        if ($objects && $this->hasFlag('url_data_object')) {
            $objects = Mage::getResourceSingleton('score/url')
                ->getRewriteByOggettoStore($objects);
            foreach ($this->_items as $item) {
                if (isset($objects[$item->getEntityId()])) {
                    $object = new Varien_Object($objects[$item->getEntityId()]);
                    $item->setUrlDataObject($object);
                }
            }
        }

        return $this;
    }

    /**
     * Add collection filters by identifiers
     *
     * @param mixed $oggettoId
     * @param boolean $exclude
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addIdFilter($oggettoId, $exclude = false)
    {
        if (empty($oggettoId)) {
            $this->_setIsLoaded(true);
            return $this;
        }
        if (is_array($oggettoId)) {
            if (!empty($oggettoId)) {
                if ($exclude) {
                    $condition = array('nin' => $oggettoId);
                } else {
                    $condition = array('in' => $oggettoId);
                }
            } else {
                $condition = '';
            }
        } else {
            if ($exclude) {
                $condition = array('neq' => $oggettoId);
            } else {
                $condition = $oggettoId;
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }

    /**
     * Adding oggetto website names to result collection
     * Add for each oggetto websites information
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addWebsiteNamesToResult()
    {
        $oggettoWebsites = array();
        foreach ($this as $oggetto) {
            $oggettoWebsites[$oggetto->getId()] = array();
        }

        if (!empty($oggettoWebsites)) {
            $select = $this->getConnection()->select()
                ->from(array('oggetto_website' => $this->_oggettoWebsiteTable))
                ->join(
                    array('website' => $this->getResource()->getTable('core/website')),
                    'website.website_id = oggetto_website.website_id',
                    array('name'))
                ->where('oggetto_website.oggetto_id IN (?)', array_keys($oggettoWebsites))
                ->where('website.website_id > ?', 0);

            $data = $this->getConnection()->fetchAll($select);
            foreach ($data as $row) {
                $oggettoWebsites[$row['oggetto_id']][] = $row['website_id'];
            }
        }

        foreach ($this as $oggetto) {
            if (isset($oggettoWebsites[$oggetto->getId()])) {
                $oggetto->setData('websites', $oggettoWebsites[$oggetto->getId()]);
            }
        }
        return $this;
    }

    /**
     * Add store availability filter. Include availability oggetto
     * for store website
     *
     * @param mixed $store
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addStoreFilter($store = null)
    {
        if ($store === null) {
            $store = $this->getStoreId();
        }
        $store = Mage::app()->getStore($store);

        if (!$store->isAdmin()) {
            $this->_oggettoLimitationFilters['store_id'] = $store->getId();
            $this->_applyOggettoLimitations();
        }

        return $this;
    }

    /**
     * Add website filter to collection
     *
     * @param unknown_type $websites
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addWebsiteFilter($websites = null)
    {
        if (!is_array($websites)) {
            $websites = array(Mage::app()->getWebsite($websites)->getId());
        }

        $this->_oggettoLimitationFilters['website_ids'] = $websites;
        $this->_applyOggettoLimitations();

        return $this;
    }

    /**
     * Get filters applied to collection
     *
     * @return array
     */
    public function getLimitationFilters()
    {
        return $this->_oggettoLimitationFilters;
    }

    /**
     * Specify category filter for oggetto collection
     *
     * @param Shaurmalab_Score_Model_Category $category
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addCategoryFilter(Shaurmalab_Score_Model_Category $category)
    {
        $this->_oggettoLimitationFilters['category_id'] = $category->getId();
        if ($category->getIsAnchor()) {
            unset($this->_oggettoLimitationFilters['category_is_anchor']);
        } else {
            $this->_oggettoLimitationFilters['category_is_anchor'] = 1;
        }

        if ($this->getStoreId() == Shaurmalab_Score_Model_Abstract::DEFAULT_STORE_ID) {
            $this->_applyZeroStoreOggettoLimitations();
        } else {
            $this->_applyOggettoLimitations();
        }

        return $this;
    }

    /**
     * Join minimal price attribute to result
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function joinMinimalPrice()
    {
        $this->addAttributeToSelect('price')
             ->addAttributeToSelect('minimal_price');
        return $this;
    }

    /**
     * Retrieve max value by attribute
     *
     * @param string $attribute
     * @return mixed
     */
    public function getMaxAttributeValue($attribute)
    {
        $select        = clone $this->getSelect();
        $attribute     = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias    = $attributeCode . '_max_value';
        $fieldAlias    = 'max_' . $attributeCode;
        $condition  = 'e.entity_id = ' . $tableAlias . '.entity_id
            AND '.$this->_getConditionSql($tableAlias . '.attribute_id', $attribute->getId());

        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array($fieldAlias => new Zend_Db_Expr('MAX('.$tableAlias.'.value)'))
            )
            ->group('e.entity_type_id');

        $data = $this->getConnection()->fetchRow($select);
        if (isset($data[$fieldAlias])) {
            return $data[$fieldAlias];
        }

        return null;
    }

    /**
     * Retrieve ranging oggetto count for arrtibute range
     *
     * @param string $attribute
     * @param int $range
     * @return array
     */
    public function getAttributeValueCountByRange($attribute, $range)
    {
        $select        = clone $this->getSelect();
        $attribute     = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias    = $attributeCode . '_range_count_value';

        $condition  = 'e.entity_id = ' . $tableAlias . '.entity_id
            AND ' . $this->_getConditionSql($tableAlias . '.attribute_id', $attribute->getId());

        $select->reset(Zend_Db_Select::GROUP);
        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array(
                    'count_' . $attributeCode => new Zend_Db_Expr('COUNT(DISTINCT e.entity_id)'),
                    'range_' . $attributeCode => new Zend_Db_Expr(
                        'CEIL((' . $tableAlias . '.value+0.01)/' . $range . ')')
                 )
            )
            ->group('range_' . $attributeCode);

        $data   = $this->getConnection()->fetchAll($select);
        $res    = array();

        foreach ($data as $row) {
            $res[$row['range_' . $attributeCode]] = $row['count_' . $attributeCode];
        }
        return $res;
    }

    /**
     * Retrieve oggetto count by some value of attribute
     *
     * @param string $attribute
     * @return array($value=>$count)
     */
    public function getAttributeValueCount($attribute)
    {
        $select        = clone $this->getSelect();
        $attribute     = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias    = $attributeCode . '_value_count';

        $select->reset(Zend_Db_Select::GROUP);
        $condition  = 'e.entity_id=' . $tableAlias . '.entity_id
            AND '.$this->_getConditionSql($tableAlias . '.attribute_id', $attribute->getId());

        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array(
                    'count_' . $attributeCode => new Zend_Db_Expr('COUNT(DISTINCT e.entity_id)'),
                    'value_' . $attributeCode => new Zend_Db_Expr($tableAlias . '.value')
                 )
            )
            ->group('value_' . $attributeCode);

        $data   = $this->getConnection()->fetchAll($select);
        $res    = array();

        foreach ($data as $row) {
            $res[$row['value_' . $attributeCode]] = $row['count_' . $attributeCode];
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
        /** @var $select Varien_Db_Select */
        $select    = clone $this->getSelect();
        $attribute = $this->getEntity()->getAttribute($attribute);

        $select->reset()
            ->from($attribute->getBackend()->getTable(), array('entity_id', 'store_id', 'value'))
            ->where('attribute_id = ?', (int)$attribute->getId());

        $data = $this->getConnection()->fetchAll($select);
        $res  = array();

        foreach ($data as $row) {
            $res[$row['entity_id']][$row['store_id']] = $row['value'];
        }

        return $res;
    }

    /**
     * Get SQL for get record count without left JOINs
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        return $this->_getSelectCountSql();
    }

    /**
     * Get SQL for get record count
     *
     * @param bool $resetLeftJoins
     * @return Varien_Db_Select
     */
    protected function _getSelectCountSql($select = null, $resetLeftJoins = true)
    {
        $this->_renderFilters();
        $countSelect = (is_null($select)) ?
            $this->_getClearSelect() :
            $this->_buildClearSelect($select);
        $countSelect->columns('COUNT(DISTINCT e.entity_id)');
        if ($resetLeftJoins) {
            $countSelect->resetJoinLeft();
        }
        return $countSelect;
    }

    /**
     * Prepare statistics data
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _prepareStatisticsData()
    {
        $select = clone $this->getSelect();
        $priceExpression = $this->getPriceExpression($select) . ' ' . $this->getAdditionalPriceExpression($select);
        $sqlEndPart = ') * ' . $this->getCurrencyRate() . ', 2)';
        $select = $this->_getSelectCountSql($select, false);
        $select->columns(array(
            'max' => 'ROUND(MAX(' . $priceExpression . $sqlEndPart,
            'min' => 'ROUND(MIN(' . $priceExpression . $sqlEndPart,
            'std' => $this->getConnection()->getStandardDeviationSql('ROUND((' . $priceExpression . $sqlEndPart)
        ));
        $select->where($this->getPriceExpression($select) . ' IS NOT NULL');
        $row = $this->getConnection()->fetchRow($select, $this->_bindParams, Zend_Db::FETCH_NUM);
        $this->_pricesCount = (int)$row[0];
        $this->_maxPrice = (float)$row[1];
        $this->_minPrice = (float)$row[2];
        $this->_priceStandardDeviation = (float)$row[3];

        return $this;
    }

    /**
     * Retreive clear select
     *
     * @return Varien_Db_Select
     */
    protected function _getClearSelect()
    {
        return $this->_buildClearSelect();
    }

    /**
     * Build clear select
     *
     * @param Varien_Db_Select $select
     * @return Varien_Db_Select
     */
    protected function _buildClearSelect($select = null)
    {
        if (is_null($select)) {
            $select = clone $this->getSelect();
        }
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::COLUMNS);

        return $select;
    }

    /**
     * Retrive all ids for collection
     *
     * @param unknown_type $limit
     * @param unknown_type $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('e.' . $this->getEntity()->getIdFieldName());
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

    /**
     * Retreive oggetto count select for categories
     *
     * @return Varien_Db_Select
     */
    public function getOggettoCountSelect()
    {
        if ($this->_oggettoCountSelect === null) {
            $this->_oggettoCountSelect = clone $this->getSelect();
            $this->_oggettoCountSelect->reset(Zend_Db_Select::COLUMNS)
                ->reset(Zend_Db_Select::GROUP)
                ->reset(Zend_Db_Select::ORDER)
                ->distinct(false)
                ->join(array('count_table' => $this->getTable('score/category_oggetto_index')),
                    'count_table.oggetto_id = e.entity_id',
                    array(
                        'count_table.category_id',
                        'oggetto_count' => new Zend_Db_Expr('COUNT(DISTINCT count_table.oggetto_id)')
                    )
                )
                ->where('count_table.store_id = ?', $this->getStoreId())
                ->group('count_table.category_id');
        }

        return $this->_oggettoCountSelect;
    }

    /**
     * Destruct oggetto count select
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function unsOggettoCountSelect()
    {
        $this->_oggettoCountSelect = null;
        return $this;
    }

    /**
     * Adding oggetto count to categories collection
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $categoryCollection
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addCountToCategories($categoryCollection)
    {
        $isAnchor    = array();
        $isNotAnchor = array();
        foreach ($categoryCollection as $category) {
            if ($category->getIsAnchor()) {
                $isAnchor[]    = $category->getId();
            } else {
                $isNotAnchor[] = $category->getId();
            }
        }
        $oggettoCounts = array();
        if ($isAnchor || $isNotAnchor) {
            $select = $this->getOggettoCountSelect();

            Mage::dispatchEvent(
                'score_oggetto_collection_before_add_count_to_categories',
                array('collection' => $this)
            );

            if ($isAnchor) {
                $anchorStmt = clone $select;
                $anchorStmt->limit(); //reset limits
                $anchorStmt->where('count_table.category_id IN (?)', $isAnchor);
                $oggettoCounts += $this->getConnection()->fetchPairs($anchorStmt);
                $anchorStmt = null;
            }
            if ($isNotAnchor) {
                $notAnchorStmt = clone $select;
                $notAnchorStmt->limit(); //reset limits
                $notAnchorStmt->where('count_table.category_id IN (?)', $isNotAnchor);
                $notAnchorStmt->where('count_table.is_parent = 1');
                $oggettoCounts += $this->getConnection()->fetchPairs($notAnchorStmt);
                $notAnchorStmt = null;
            }
            $select = null;
            $this->unsOggettoCountSelect();
        }

        foreach ($categoryCollection as $category) {
            $_count = 0;
            if (isset($oggettoCounts[$category->getId()])) {
                $_count = $oggettoCounts[$category->getId()];
            }
            $category->setOggettoCount($_count);
        }

        return $this;
    }

    /**
     * Retrieve unique attribute set ids in collection
     *
     * @return array
     */
    public function getSetIds()
    {
        $select = clone $this->getSelect();
        /** @var $select Varien_Db_Select */
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->distinct(true);
        $select->columns('attribute_set_id');
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Return array of unique oggetto type ids in collection
     *
     * @return array
     */
    public function getOggettoTypeIds()
    {
        $select = clone $this->getSelect();
        /** @var $select Varien_Db_Select */
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->distinct(true);
        $select->columns('type_id');
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Joins url rewrite rules to collection
     *
     * @deprecated after 1.7.0.2. Method is not used anywhere in the code.
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function joinUrlRewrite()
    {
        $this->joinTable(
            'core/url_rewrite',
            'entity_id=entity_id',
            array('request_path'),
            '{{table}}.type = ' . Mage_Core_Model_Url_Rewrite::TYPE_OGGETTO,
            'left'
        );

        return $this;
    }

    /**
     * Add URL rewrites data to oggetto
     * If collection loadded - run processing else set flag
     *
     * @param int|string $categoryId
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addUrlRewrite($categoryId = '')
    {
        $this->_addUrlRewrite = true;
        if (Mage::getStoreConfig(Shaurmalab_Score_Helper_Oggetto::XML_PATH_OGGETTO_URL_USE_CATEGORY, $this->getStoreId())) {
            $this->_urlRewriteCategory = $categoryId;
        } else {
            $this->_urlRewriteCategory = 0;
        }

        if ($this->isLoaded()) {
            $this->_addUrlRewrite();
        }

        return $this;
    }

    /**
     * Add URL rewrites to collection
     *
     */
    protected function _addUrlRewrite()
    {
        $urlRewrites = null;
        if ($this->_cacheConf) {
            if (!($urlRewrites = Mage::app()->loadCache($this->_cacheConf['prefix'] . 'urlrewrite'))) {
                $urlRewrites = null;
            } else {
                $urlRewrites = unserialize($urlRewrites);
            }
        }

        if (!$urlRewrites) {
            $oggettoIds = array();
            foreach($this->getItems() as $item) {
                $oggettoIds[] = $item->getEntityId();
            }
            if (!count($oggettoIds)) {
                return;
            }

            $select = $this->_factory->getOggettoUrlRewriteHelper()
                ->getTableSelect($oggettoIds, $this->_urlRewriteCategory, Mage::app()->getStore()->getId());

            $urlRewrites = array();
            foreach ($this->getConnection()->fetchAll($select) as $row) {
                if (!isset($urlRewrites[$row['oggetto_id']])) {
                    $urlRewrites[$row['oggetto_id']] = $row['request_path'];
                }
            }

            if ($this->_cacheConf) {
                Mage::app()->saveCache(
                    serialize($urlRewrites),
                    $this->_cacheConf['prefix'] . 'urlrewrite',
                    array_merge($this->_cacheConf['tags'], array(Shaurmalab_Score_Model_Oggetto_Url::CACHE_TAG)),
                    $this->_cacheLifetime
                );
            }
        }

        foreach($this->getItems() as $item) {
            if (empty($this->_urlRewriteCategory)) {
                $item->setDoNotUseCategoryId(true);
            }
            if (isset($urlRewrites[$item->getEntityId()])) {
                $item->setData('request_path', $urlRewrites[$item->getEntityId()]);
            } else {
                $item->setData('request_path', false);
            }
        }
    }

    /**
     * Add minimal price data to result
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addMinimalPrice()
    {
        return $this->addPriceData();
    }

    /**
     * Add minimal price to oggetto collection
     *
     * @deprecated sinse 1.3.2.2
     * @see Shaurmalab_Score_Model_Resource_Oggetto_Collection::addPriceData
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _addMinimalPrice()
    {
        return $this;
    }

    /**
     * Add price data for calculate final price
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addFinalPrice()
    {
        return $this->addPriceData();
    }

    /**
     * Join prices from price rules to oggettos collection
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _joinPriceRules()
    {
        if ($this->isEnabledFlat()) {
            $customerGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
            $priceColumn   = 'e.display_price_group_' . $customerGroup;
            $this->getSelect()->columns(array('_rule_price' => $priceColumn));

            return $this;
        }
        if (!Mage::helper('score')->isModuleEnabled('Shaurmalab_ScoreRule')) {
            return $this;
        }
        $wId = Mage::app()->getWebsite()->getId();
        $gId = Mage::getSingleton('customer/session')->getCustomerGroupId();

        $storeDate = Mage::app()->getLocale()->storeTimeStamp($this->getStoreId());
        $conditions  = 'price_rule.oggetto_id = e.entity_id AND ';
        $conditions .= "price_rule.rule_date = '".$this->getResource()->formatDate($storeDate, false)."' AND ";
        $conditions .= $this->getConnection()->quoteInto('price_rule.website_id = ? AND', $wId);
        $conditions .= $this->getConnection()->quoteInto('price_rule.customer_group_id = ?', $gId);

        $this->getSelect()->joinLeft(
            array('price_rule' => $this->getTable('catalogrule/rule_product_price')),
            $conditions,
            array('rule_price' => 'rule_price')
        );
        return $this;
    }

    /**
     * Add final price to the oggetto
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _addFinalPrice()
    {
        foreach ($this->_items as $oggetto) {
            $basePrice = $oggetto->getPrice();
            $specialPrice = $oggetto->getSpecialPrice();
            $specialPriceFrom = $oggetto->getSpecialFromDate();
            $specialPriceTo = $oggetto->getSpecialToDate();
            if ($this->isEnabledFlat()) {
                $rulePrice = null;
                if ($oggetto->getData('_rule_price') != $basePrice) {
                    $rulePrice = $oggetto->getData('_rule_price');
                }
            } else {
                $rulePrice = $oggetto->getData('_rule_price');
            }

            $finalPrice = $oggetto->getPriceModel()->calculatePrice(
                $basePrice,
                $specialPrice,
                $specialPriceFrom,
                $specialPriceTo,
                $rulePrice,
                null,
                null,
                $oggetto->getId()
            );

            $oggetto->setCalculatedFinalPrice($finalPrice);
        }

        return $this;
    }

    /**
     * Retreive all ids
     *
     * @param boolean $resetCache
     * @return array
     */
    public function getAllIdsCache($resetCache = false)
    {
        $ids = null;
        if (!$resetCache) {
            $ids = $this->_allIdsCache;
        }

        if (is_null($ids)) {
            $ids = $this->getAllIds();
            $this->setAllIdsCache($ids);
        }

        return $ids;
    }

    /**
     * Set all ids
     *
     * @param array $value
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function setAllIdsCache($value)
    {
        $this->_allIdsCache = $value;
        return $this;
    }

    /**
     * Add Price Data to result
     *
     * @param int $customerGroupId
     * @param int $websiteId
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addPriceData($customerGroupId = null, $websiteId = null)
    {
        $this->_oggettoLimitationFilters['use_price_index'] = true;

        if (!isset($this->_oggettoLimitationFilters['customer_group_id']) && is_null($customerGroupId)) {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
        if (!isset($this->_oggettoLimitationFilters['website_id']) && is_null($websiteId)) {
            $websiteId       = Mage::app()->getStore($this->getStoreId())->getWebsiteId();
        }

        if (!is_null($customerGroupId)) {
            $this->_oggettoLimitationFilters['customer_group_id'] = $customerGroupId;
        }
        if (!is_null($websiteId)) {
            $this->_oggettoLimitationFilters['website_id'] = $websiteId;
        }

        $this->_applyOggettoLimitations();

        return $this;
    }

    /**
     * Add attribute to filter
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|string $attribute
     * @param array $condition
     * @param string $joinType
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        if ($this->isEnabledFlat()) {
            if ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract) {
                $attribute = $attribute->getAttributeCode();
            }

            if (is_array($attribute)) {
                $sqlArr = array();
                foreach ($attribute as $condition) {
                    $sqlArr[] = $this->_getAttributeConditionSql($condition['attribute'], $condition, $joinType);
                }
                $conditionSql = '('.join(') OR (', $sqlArr).')';
                $this->getSelect()->where($conditionSql);
                return $this;
            }

            if (!isset($this->_selectAttributes[$attribute])) {
                $this->addAttributeToSelect($attribute);
            }

            if (isset($this->_selectAttributes[$attribute])) {
                $this->getSelect()->where($this->_getConditionSql('e.' . $attribute, $condition));
            }

            return $this;
        }

        $this->_allIdsCache = null;

        if (is_string($attribute) && $attribute == 'is_saleable') {
            $columns = $this->getSelect()->getPart(Zend_Db_Select::COLUMNS);
            foreach ($columns as $columnEntry) {
                list($correlationName, $column, $alias) = $columnEntry;
                if ($alias == 'is_saleable') {
                    if ($column instanceof Zend_Db_Expr) {
                        $field = $column;
                    } else {
                        $adapter = $this->getSelect()->getAdapter();
                        if (empty($correlationName)) {
                            $field = $adapter->quoteColumnAs($column, $alias, true);
                        } else {
                            $field = $adapter->quoteColumnAs(array($correlationName, $column), $alias, true);
                        }
                    }
                    $this->getSelect()->where("{$field} = ?", $condition);
                    break;
                }
            }

            return $this;
        } else {
            return parent::addAttributeToFilter($attribute, $condition, $joinType);
        }
    }

    /**
     * Add requere tax percent flag for oggetto collection
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addTaxPercents()
    {
        $this->_addTaxPercents = true;
        return $this;
    }

    /**
     * Get require tax percent flag value
     *
     * @return bool
     */
    public function requireTaxPercent()
    {
        return $this->_addTaxPercents;
    }

    /**
     * Enter description here ...
     *
     * @deprecated from 1.3.0
     *
     */
    protected function _addTaxPercents()
    {
        $classToRate = array();
        $request = Mage::getSingleton('tax/calculation')->getRateRequest();
        foreach ($this as &$item) {
            if (null === $item->getTaxClassId()) {
                $item->setTaxClassId($item->getMinimalTaxClassId());
            }
            if (!isset($classToRate[$item->getTaxClassId()])) {
                $request->setOggettoClassId($item->getTaxClassId());
                $classToRate[$item->getTaxClassId()] = Mage::getSingleton('tax/calculation')->getRate($request);
            }
            $item->setTaxPercent($classToRate[$item->getTaxClassId()]);
        }
    }

    /**
     * Adding oggetto custom options to result collection
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addOptionsToResult()
    {
        $oggettoIds = array();
        foreach ($this as $oggetto) {
            $oggettoIds[] = $oggetto->getId();
        }
        if (!empty($oggettoIds)) {
            $options = Mage::getModel('score/oggetto_option')
                ->getCollection()
                ->addTitleToResult(Mage::app()->getStore()->getId())
                ->addPriceToResult(Mage::app()->getStore()->getId())
                ->addOggettoToFilter($oggettoIds)
                ->addValuesToResult();

            foreach ($options as $option) {
                if($this->getItemById($option->getOggettoId())) {
                    $this->getItemById($option->getOggettoId())->addOption($option);
                }
            }
        }

        return $this;
    }

    /**
     * Filter oggettos with required options
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addFilterByRequiredOptions()
    {
        $this->addAttributeToFilter('required_options', array(array('neq' => '1'), array('null' => true)), 'left');
        return $this;
    }

    /**
     * Set oggetto visibility filter for enabled oggettos
     *
     * @param array $visibility
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function setVisibility($visibility)
    {
        $this->_oggettoLimitationFilters['visibility'] = $visibility;
        $this->_applyOggettoLimitations();

        return $this;
    }

    /**
     * Add attribute to sort order
     *
     * @param string $attribute
     * @param string $dir
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if ($attribute == 'position') {
            if (isset($this->_joinFields[$attribute])) {
                $this->getSelect()->order($this->_getAttributeFieldName($attribute) . ' ' . $dir);
                return $this;
            }
            if ($this->isEnabledFlat()) {
         //       $this->getSelect()->order("cat_index_position {$dir}");
            }
            // optimize if using cat index
            $filters = $this->_oggettoLimitationFilters;
            if (isset($filters['category_id']) || isset($filters['visibility'])) {
                $this->getSelect()->order('cat_index.position ' . $dir);
            } else {
                $this->getSelect()->order('e.entity_id ' . $dir);
            }

            return $this;
        } elseif($attribute == 'is_saleable'){
            $this->getSelect()->order("is_saleable " . $dir);
            return $this;
        }

        $storeId = $this->getStoreId();
        if ($attribute == 'price' && $storeId != 0) {
            $this->addPriceData();
            $this->getSelect()->order("price_index.min_price {$dir}");

            return $this;
        }

        if ($this->isEnabledFlat()) {
            $column = $this->getEntity()->getAttributeSortColumn($attribute);

            if ($column) {
                $this->getSelect()->order("e.{$column} {$dir}");
            }
            else if (isset($this->_joinFields[$attribute])) {
                $this->getSelect()->order($this->_getAttributeFieldName($attribute) . ' ' . $dir);
            }

            return $this;
        } else {
            $attrInstance = $this->getEntity()->getAttribute($attribute);
            if ($attrInstance && $attrInstance->usesSource()) {
                $attrInstance->getSource()
                    ->addValueSortToCollection($this, $dir);
                return $this;
            }
        }

        return parent::addAttributeToSort($attribute, $dir);
    }

    /**
     * Prepare limitation filters
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _prepareOggettoLimitationFilters()
    {
        if (isset($this->_oggettoLimitationFilters['visibility'])
            && !isset($this->_oggettoLimitationFilters['store_id'])
        ) {
            $this->_oggettoLimitationFilters['store_id'] = $this->getStoreId();
        }
        if (isset($this->_oggettoLimitationFilters['category_id'])
            && !isset($this->_oggettoLimitationFilters['store_id'])
        ) {
            $this->_oggettoLimitationFilters['store_id'] = $this->getStoreId();
        }
        if (isset($this->_oggettoLimitationFilters['store_id'])
            && isset($this->_oggettoLimitationFilters['visibility'])
            && !isset($this->_oggettoLimitationFilters['category_id'])
        ) {
            $this->_oggettoLimitationFilters['category_id'] = Mage::app()
                ->getStore($this->_oggettoLimitationFilters['store_id'])
                ->getRootCategoryId();
        }

        return $this;
    }

    /**
     * Join website oggetto limitation
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _oggettoLimitationJoinWebsite()
    {
        $joinWebsite = false;
        $filters     = $this->_oggettoLimitationFilters;
        $conditions  = array('oggetto_website.oggetto_id = e.entity_id');

        if (isset($filters['website_ids'])) {
            $joinWebsite = true;
            if (count($filters['website_ids']) > 1) {
                $this->getSelect()->distinct(true);
            }
            $conditions[] = $this->getConnection()
                ->quoteInto('oggetto_website.website_id IN(?)', $filters['website_ids']);
        } elseif (isset($filters['store_id'])
            && (!isset($filters['visibility']) && !isset($filters['category_id']))
            && !$this->isEnabledFlat()
        ) {
            $joinWebsite = true;
            $websiteId = Mage::app()->getStore($filters['store_id'])->getWebsiteId();
            $conditions[] = $this->getConnection()
                ->quoteInto('oggetto_website.website_id = ?', $websiteId);
        }

        $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
        if (isset($fromPart['oggetto_website'])) {
            if (!$joinWebsite) {
                unset($fromPart['oggetto_website']);
            } else {
                $fromPart['oggetto_website']['joinCondition'] = join(' AND ', $conditions);
            }
            $this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
        } elseif ($joinWebsite) {
            $this->getSelect()->join(
                array('oggetto_website' => $this->getTable('score/oggetto_website')),
                join(' AND ', $conditions),
                array()
            );
        }

        return $this;
    }

    /**
     * Join additional (alternative) store visibility filter
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _oggettoLimitationJoinStore()
    {
        $filters = $this->_oggettoLimitationFilters;
        if (!isset($filters['store_table'])) {
            return $this;
        }

        $hasColumn = false;
        foreach ($this->getSelect()->getPart(Zend_Db_Select::COLUMNS) as $columnEntry) {
            list(,,$alias) = $columnEntry;
            if ($alias == 'visibility') {
                $hasColumn = true;
            }
        }
        if (!$hasColumn) {
            $this->getSelect()->columns('visibility', 'cat_index');
        }

        $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
        if (!isset($fromPart['store_index'])) {
            $this->getSelect()->joinLeft(
                array('store_index' => $this->getTable('core/store')),
                'store_index.store_id = ' . $filters['store_table'] . '.store_id',
                array()
            );
        }
        if (!isset($fromPart['store_group_index'])) {
            $this->getSelect()->joinLeft(
                array('store_group_index' => $this->getTable('core/store_group')),
                'store_index.group_id = store_group_index.group_id',
                array()
            );
        }
        if (!isset($fromPart['store_cat_index'])) {
            $this->getSelect()->joinLeft(
                array('store_cat_index' => $this->getTable('score/category_oggetto_index')),
                join(' AND ', array(
                    'store_cat_index.oggetto_id = e.entity_id',
                    'store_cat_index.store_id = ' . $filters['store_table'] . '.store_id',
                    'store_cat_index.category_id=store_group_index.root_category_id'
                )),
                array('store_visibility' => 'visibility')
            );
        }
        // Avoid column duplication problems
        Mage::getResourceHelper('core')->prepareColumnsList($this->getSelect());

        $whereCond = join(' OR ', array(
            $this->getConnection()->quoteInto('cat_index.visibility IN(?)', $filters['visibility']),
            $this->getConnection()->quoteInto('store_cat_index.visibility IN(?)', $filters['visibility'])
        ));

        $wherePart = $this->getSelect()->getPart(Zend_Db_Select::WHERE);
        $hasCond   = false;
        foreach ($wherePart as $cond) {
            if ($cond == '(' . $whereCond . ')') {
                $hasCond = true;
            }
        }

        if (!$hasCond) {
            $this->getSelect()->where($whereCond);
        }

        return $this;
    }

    /**
     * Join Oggetto Price Table
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _oggettoLimitationJoinPrice()
    {
        return $this->_oggettoLimitationPrice();
    }

    /**
     * Join Oggetto Price Table with left-join possibility
     *
     * @see Shaurmalab_Score_Model_Resource_Oggetto_Collection::_oggettoLimitationJoinPrice()
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _oggettoLimitationPrice($joinLeft = false)
    {
        $filters = $this->_oggettoLimitationFilters;
        if (empty($filters['use_price_index'])) {
            return $this;
        }

        $helper     = Mage::getResourceHelper('core');
        $connection = $this->getConnection();
        $select     = $this->getSelect();
        $joinCond   = join(' AND ', array(
            'price_index.entity_id = e.entity_id',
            $connection->quoteInto('price_index.website_id = ?', $filters['website_id']),
            $connection->quoteInto('price_index.customer_group_id = ?', $filters['customer_group_id'])
        ));

        $fromPart = $select->getPart(Zend_Db_Select::FROM);
        if (!isset($fromPart['price_index'])) {
            $least       = $connection->getLeastSql(array('price_index.min_price', 'price_index.tier_price'));
            $minimalExpr = $connection->getCheckSql('price_index.tier_price IS NOT NULL',
                $least, 'price_index.min_price');
            $colls       = array('price', 'tax_class_id', 'final_price',
                'minimal_price' => $minimalExpr , 'min_price', 'max_price', 'tier_price');
            $tableName = array('price_index' => $this->getTable('score/oggetto_index_price'));
            if ($joinLeft) {
                $select->joinLeft($tableName, $joinCond, $colls);
            } else {
                $select->join($tableName, $joinCond, $colls);
            }
            // Set additional field filters
            foreach ($this->_priceDataFieldFilters as $filterData) {
                $select->where(call_user_func_array('sprintf', $filterData));
            }
        } else {
            $fromPart['price_index']['joinCondition'] = $joinCond;
            $select->setPart(Zend_Db_Select::FROM, $fromPart);
        }
        //Clean duplicated fields
        $helper->prepareColumnsList($select);


        return $this;
    }

    /**
     * Apply front-end price limitation filters to the collection
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function applyFrontendPriceLimitations()
    {
        $this->_oggettoLimitationFilters['use_price_index'] = true;
        if (!isset($this->_oggettoLimitationFilters['customer_group_id'])) {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
            $this->_oggettoLimitationFilters['customer_group_id'] = $customerGroupId;
        }
        if (!isset($this->_oggettoLimitationFilters['website_id'])) {
            $websiteId = Mage::app()->getStore($this->getStoreId())->getWebsiteId();
            $this->_oggettoLimitationFilters['website_id'] = $websiteId;
        }
        $this->_applyOggettoLimitations();
        return $this;
    }

    /**
     * Apply limitation filters to collection
     * Method allows using one time category oggetto index table (or oggetto website table)
     * for different combinations of store_id/category_id/visibility filter states
     * Method supports multiple changes in one collection object for this parameters
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _applyOggettoLimitations()
    {
        Mage::dispatchEvent('score_oggetto_collection_apply_limitations_before', array(
            'collection'  => $this,
            'category_id' => isset($this->_oggettoLimitationFilters['category_id'])
                ? $this->_oggettoLimitationFilters['category_id']
                : null,
        ));
        $this->_prepareOggettoLimitationFilters();
        $this->_oggettoLimitationJoinWebsite();
        $this->_oggettoLimitationJoinPrice();
        $filters = $this->_oggettoLimitationFilters;

        if (!isset($filters['category_id']) && !isset($filters['visibility'])) {
            return $this;
        }

        $conditions = array(
            'cat_index.oggetto_id=e.entity_id',
            $this->getConnection()->quoteInto('cat_index.store_id=?', $filters['store_id'])
        );
        if (isset($filters['visibility']) && !isset($filters['store_table'])) {
            $conditions[] = $this->getConnection()
                ->quoteInto('cat_index.visibility IN(?)', $filters['visibility']);
        }

        if (!$this->getFlag('disable_root_category_filter')) {
            $conditions[] = $this->getConnection()->quoteInto('cat_index.category_id = ?', $filters['category_id']);
        }

        if (isset($filters['category_is_anchor'])) {
            $conditions[] = $this->getConnection()
                ->quoteInto('cat_index.is_parent=?', $filters['category_is_anchor']);
        }

        $joinCond = join(' AND ', $conditions);
        $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
        if (isset($fromPart['cat_index'])) {
            $fromPart['cat_index']['joinCondition'] = $joinCond;
            $this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
        }
        else {
//            $this->getSelect()->join(
//                array('cat_index' => $this->getTable('score/category_oggetto_index')),
//                $joinCond,
//                array('cat_index_position' => 'position')
//            );
        }

        $this->_oggettoLimitationJoinStore();

        Mage::dispatchEvent('score_oggetto_collection_apply_limitations_after', array(
            'collection' => $this
        ));

        return $this;
    }

    /**
     * Apply limitation filters to collection base on API
     * Method allows using one time category oggetto table
     * for combinations of category_id filter states
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    protected function _applyZeroStoreOggettoLimitations()
    {
        $filters = $this->_oggettoLimitationFilters;

        $conditions = array(
            'cat_pro.oggetto_id=e.entity_id',
            $this->getConnection()->quoteInto('cat_pro.category_id=?', $filters['category_id'])
        );
        $joinCond = join(' AND ', $conditions);

        $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
        if (isset($fromPart['cat_pro'])) {
            $fromPart['cat_pro']['joinCondition'] = $joinCond;
            $this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
        }
        else {
//            $this->getSelect()->join(
//                array('cat_pro' => $this->getTable('score/category_oggetto')),
//                $joinCond,
//                array('cat_index_position' => 'position')
//            );
        }
        $this->_joinFields['position'] = array(
            'table' => 'cat_pro',
            'field' => 'position',
        );

        return $this;
    }

    /**
     * Add category ids to loaded items
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addCategoryIds()
    {
        if ($this->getFlag('category_ids_added')) {
            return $this;
        }
        $ids = array_keys($this->_items);
        if (empty($ids)) {
            return $this;
        }

        $select = $this->getConnection()->select();

        $select->from($this->_oggettoCategoryTable, array('oggetto_id', 'category_id'));
        $select->where('oggetto_id IN (?)', $ids);

        $data = $this->getConnection()->fetchAll($select);

        $categoryIds = array();
        foreach ($data as $info) {
            if (isset($categoryIds[$info['oggetto_id']])) {
                $categoryIds[$info['oggetto_id']][] = $info['category_id'];
            } else {
                $categoryIds[$info['oggetto_id']] = array($info['category_id']);
            }
        }


        foreach ($this->getItems() as $item) {
            $oggettoId = $item->getId();
            if (isset($categoryIds[$oggettoId])) {
                $item->setCategoryIds($categoryIds[$oggettoId]);
            } else {
                $item->setCategoryIds(array());
            }
        }

        $this->setFlag('category_ids_added', true);
        return $this;
    }

    /**
     * Add tier price data to loaded items
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function addTierPriceData()
    {
        if ($this->getFlag('tier_price_added')) {
            return $this;
        }

        $tierPrices = array();
        $oggettoIds = array();
        foreach ($this->getItems() as $item) {
            $oggettoIds[] = $item->getId();
            $tierPrices[$item->getId()] = array();
        }
        if (!$oggettoIds) {
            return $this;
        }

        /** @var $attribute Shaurmalab_Score_Model_Resource_Eav_Attribute */
        $attribute = $this->getAttribute('tier_price');
        if ($attribute->isScopeGlobal()) {
            $websiteId = 0;
        } else if ($this->getStoreId()) {
            $websiteId = Mage::app()->getStore($this->getStoreId())->getWebsiteId();
        }

        $adapter   = $this->getConnection();
        $columns   = array(
            'price_id'      => 'value_id',
            'website_id'    => 'website_id',
            'all_groups'    => 'all_groups',
            'cust_group'    => 'customer_group_id',
            'price_qty'     => 'qty',
            'price'         => 'value',
            'oggetto_id'    => 'entity_id'
        );
        $select  = $adapter->select()
            ->from($this->getTable('score/oggetto_attribute_tier_price'), $columns)
            ->where('entity_id IN(?)', $oggettoIds)
            ->order(array('entity_id','qty'));

        if ($websiteId == '0') {
            $select->where('website_id = ?', $websiteId);
        } else {
            $select->where('website_id IN(?)', array('0', $websiteId));
        }

        foreach ($adapter->fetchAll($select) as $row) {
            $tierPrices[$row['oggetto_id']][] = array(
                'website_id'    => $row['website_id'],
                'cust_group'    => $row['all_groups'] ? Mage_Customer_Model_Group::CUST_GROUP_ALL : $row['cust_group'],
                'price_qty'     => $row['price_qty'],
                'price'         => $row['price'],
                'website_price' => $row['price'],

            );
        }

        /* @var $backend Shaurmalab_Score_Model_Oggetto_Attribute_Backend_Tierprice */
        $backend = $attribute->getBackend();

        foreach ($this->getItems() as $item) {
            $data = $tierPrices[$item->getId()];
            if (!empty($data) && $websiteId) {
                $data = $backend->preparePriceData($data, $item->getTypeId(), $websiteId);
            }
            $item->setData('tier_price', $data);
        }

        $this->setFlag('tier_price_added', true);
        return $this;
    }

    /**
     * Add field comparison expression
     *
     * @param string $comparisonFormat - expression for sprintf()
     * @param array $fields - list of fields
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     * @throws Exception
     */
    public function addPriceDataFieldFilter($comparisonFormat, $fields)
    {
        if (!preg_match('/^%s( (<|>|=|<=|>=|<>) %s)*$/', $comparisonFormat)) {
            throw new Exception('Invalid comparison format.');
        }

        if (!is_array($fields)) {
            $fields = array($fields);
        }
        foreach ($fields as $key => $field) {
            $fields[$key] = $this->_getMappedField($field);
        }

        $this->_priceDataFieldFilters[] = array_merge(array($comparisonFormat), $fields);
        return $this;
    }

    /**
     * Clear collection
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function clear()
    {
        foreach ($this->_items as $i => $item) {
            if ($item->hasStockItem()) {
                $item->unsStockItem();
            }
            $item = $this->_items[$i] = null;
        }

        foreach ($this->_itemsById as $i => $item) {
            $item = $this->_itemsById[$i] = null;
        }

        unset($this->_items, $this->_data, $this->_itemsById);
        $this->_data = array();
        $this->_itemsById = array();
        return parent::clear();
    }

    /**
     * Set Order field
     *
     * @param string $attribute
     * @param string $dir
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection
     */
    public function setOrder($attribute, $dir = 'desc')
    {
        if ($attribute == 'price') {
            $this->addAttributeToSort($attribute, $dir);
        } else {
            parent::setOrder($attribute, $dir);
        }
        return $this;
    }

    /**
     * Get oggettos max price
     *
     * @return float
     */
    public function getMaxPrice()
    {
        if (is_null($this->_maxPrice)) {
            $this->_prepareStatisticsData();
        }

        return $this->_maxPrice;
    }

    /**
     * Get oggettos min price
     *
     * @return float
     */
    public function getMinPrice()
    {
        if (is_null($this->_minPrice)) {
            $this->_prepareStatisticsData();
        }

        return $this->_minPrice;
    }

    /**
     * Get standard deviation of oggettos price
     *
     * @return float
     */
    public function getPriceStandardDeviation()
    {
        if (is_null($this->_priceStandardDeviation)) {
            $this->_prepareStatisticsData();
        }

        return $this->_priceStandardDeviation;
    }


    /**
     * Get count of oggetto prices
     *
     * @return int
     */
    public function getPricesCount()
    {
        if (is_null($this->_pricesCount)) {
            $this->_prepareStatisticsData();
        }

        return $this->_pricesCount;
    }
}
