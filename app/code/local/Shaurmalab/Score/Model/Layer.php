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
 * Score view layer model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Layer extends Varien_Object
{
    /**
     * Oggetto collections array
     *
     * @var array
     */
    protected $_oggettoCollections = array();

    /**
     * Key which can be used for load/save aggregation data
     *
     * @var string
     */
    protected $_stateKey = null;

    /**
     * Get data aggregation object
     *
     * @return Shaurmalab_ScoreIndex_Model_Aggregation
     */
    public function getAggregator()
    {
        return Mage::getSingleton('scoreindex/aggregation');
    }

    /**
     * Get layer state key
     *
     * @return string
     */
    public function getStateKey()
    {
        if ($this->_stateKey === null) {
            $this->_stateKey = 'STORE_'.Mage::app()->getStore()->getId()
                . '_CAT_' . $this->getCurrentCategory()->getId()
                . '_CUSTGROUP_' . Mage::getSingleton('customer/session')->getCustomerGroupId();
        }

        return $this->_stateKey;
    }

    /**
     * Get default tags for current layer state
     *
     * @param   array $additionalTags
     * @return  array
     */
    public function getStateTags(array $additionalTags = array())
    {
        $additionalTags = array_merge($additionalTags, array(
            Shaurmalab_Score_Model_Category::CACHE_TAG.$this->getCurrentCategory()->getId()
        ));

        return $additionalTags;
    }

    /**
     * Retrieve current layer oggetto collection
     *
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Collection
     */
    public function getOggettoCollection()
    {
        if (isset($this->_oggettoCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_oggettoCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->getCurrentCategory()->getOggettoCollection();
            $this->prepareOggettoCollection($collection);
            $this->_oggettoCollections[$this->getCurrentCategory()->getId()] = $collection;
        }

        return $collection;
    }

    /**
     * Initialize oggetto collection
     *
     * @param Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Collection $collection
     * @return Shaurmalab_Score_Model_Layer
     */
    public function prepareOggettoCollection($collection)
    {
        $collection
            ->addAttributeToSelect(Mage::getSingleton('score/config')->getOggettoAttributes())
//            ->addMinimalPrice()
//            ->addFinalPrice()
//            ->addTaxPercents()
            ->addUrlRewrite($this->getCurrentCategory()->getId());

        Mage::getSingleton('score/oggetto_status')->addVisibleFilterToCollection($collection);
      //  Mage::getSingleton('score/oggetto_visibility')->addVisibleInScoreFilterToCollection($collection);

        return $this;
    }

    /**
     * Apply layer
     * Method is colling after apply all filters, can be used
     * for prepare some index data before getting information
     * about existing intexes
     *
     * @return Shaurmalab_Score_Model_Layer
     */
    public function apply()
    {
        $stateSuffix = '';
        foreach ($this->getState()->getFilters() as $filterItem) {
            $stateSuffix .= '_' . $filterItem->getFilter()->getRequestVar()
                . '_' . $filterItem->getValueString();
        }
        if (!empty($stateSuffix)) {
            $this->_stateKey = $this->getStateKey().$stateSuffix;
        }

        return $this;
    }

    /**
     * Retrieve current category model
     * If no category found in registry, the root will be taken
     *
     * @return Shaurmalab_Score_Model_Category
     */
    public function getCurrentCategory()
    {
        $category = $this->getData('current_category');
        if (is_null($category)) {
            if ($category = Mage::registry('current_category')) {
                $this->setData('current_category', $category);
            }
            else {
                $category = Mage::getModel('score/category')->load($this->getCurrentStore()->getRootCategoryId());
                $this->setData('current_category', $category);
            }
        }

        return $category;
    }

    /**
     * Change current category object
     *
     * @param mixed $category
     * @return Shaurmalab_Score_Model_Layer
     */
    public function setCurrentCategory($category)
    {
        if (is_numeric($category)) {
            $category = Mage::getModel('score/category')->load($category);
        }
        if (!$category instanceof Shaurmalab_Score_Model_Category) {
            Mage::throwException(Mage::helper('score')->__('Category must be an instance of Shaurmalab_Score_Model_Category.'));
        }
        if (!$category->getId()) {
            Mage::throwException(Mage::helper('score')->__('Invalid category.'));
        }

        if ($category->getId() != $this->getCurrentCategory()->getId()) {
            $this->setData('current_category', $category);
        }

        return $this;
    }

    /**
     * Retrieve current store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getCurrentStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Get collection of all filterable attributes for layer oggettos set
     *
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Attribute_Collection
     */
    public function getFilterableAttributes()
    {
//        $entity = Mage::getSingleton('eav/config')
//            ->getEntityType('score_oggetto');

        $setIds = $this->_getSetIds();
        if (!$setIds) {
            return array();
        }
        /** @var $collection Shaurmalab_Score_Model_Resource_Oggetto_Attribute_Collection */
        $collection = Mage::getResourceModel('score/oggetto_attribute_collection');
        $collection
            ->setItemObjectClass('score/resource_eav_attribute')
            ->setAttributeSetFilter($setIds)
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->setOrder('position', 'ASC');
        $collection = $this->_prepareAttributeCollection($collection);
        $collection->load();

        return $collection;
    }

    /**
     * Prepare attribute for use in layered navigation
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @return  Mage_Eav_Model_Entity_Attribute
     */
    protected function _prepareAttribute($attribute)
    {
        Mage::getResourceSingleton('score/oggetto')->getAttribute($attribute);
        return $attribute;
    }

    /**
     * Add filters to attribute collection
     *
     * @param   Shaurmalab_Score_Model_Resource_Eav_Mysql4_Attribute_Collection $collection
     * @return  Shaurmalab_Score_Model_Resource_Eav_Mysql4_Attribute_Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableFilter();
        return $collection;
    }

    /**
     * Retrieve layer state object
     *
     * @return Shaurmalab_Score_Model_Layer_State
     */
    public function getState()
    {
        $state = $this->getData('state');
        if (is_null($state)) {
            Varien_Profiler::start(__METHOD__);
            $state = Mage::getModel('score/layer_state');
            $this->setData('state', $state);
            Varien_Profiler::stop(__METHOD__);
        }

        return $state;
    }

    /**
     * Get attribute sets identifiers of current oggetto set
     *
     * @return array
     */
    protected function _getSetIds()
    {
        $key = $this->getStateKey().'_SET_IDS';
        $setIds = $this->getAggregator()->getCacheData($key);

        if ($setIds === null) {
            $setIds = $this->getOggettoCollection()->getSetIds();
            $this->getAggregator()->saveCacheData($setIds, $key, $this->getStateTags());
        }

        return $setIds;
    }
}
