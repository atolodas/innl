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
 * Score attribute model
 *
 * @method Shaurmalab_Score_Model_Resource_Attribute _getResource()
 * @method Shaurmalab_Score_Model_Resource_Attribute getResource()
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getFrontendInputRenderer()
 * @method string setFrontendInputRenderer(string $value)
 * @method int setIsGlobal(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getIsVisible()
 * @method int setIsVisible(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getIsSearchable()
 * @method int setIsSearchable(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getSearchWeight()
 * @method int setSearchWeight(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getIsFilterable()
 * @method int setIsFilterable(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getIsComparable()
 * @method int setIsComparable(int $value)
 * @method int setIsVisibleOnFront(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getIsHtmlAllowedOnFront()
 * @method int setIsHtmlAllowedOnFront(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getIsUsedForPriceRules()
 * @method int setIsUsedForPriceRules(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getIsFilterableInSearch()
 * @method int setIsFilterableInSearch(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getUsedInOggettoListing()
 * @method int setUsedInOggettoListing(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getUsedForSortBy()
 * @method int setUsedForSortBy(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getIsConfigurable()
 * @method int setIsConfigurable(int $value)
 * @method string setApplyTo(string $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getIsVisibleInAdvancedSearch()
 * @method int setIsVisibleInAdvancedSearch(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getPosition()
 * @method int setPosition(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getIsWysiwygEnabled()
 * @method int setIsWysiwygEnabled(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getIsUsedForPromoRules()
 * @method int setIsUsedForPromoRules(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getIsUsedForCustomerSegment()
 * @method int setIsUsedForCustomerSegment(int $value)
 * @method Shaurmalab_Score_Model_Resource_Eav_Attribute getIsUsedForTargetRules()
 * @method int setIsUsedForTargetRules(int $value)
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Eav_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    const SCOPE_STORE                           = 0;
    const SCOPE_GLOBAL                          = 1;
    const SCOPE_WEBSITE                         = 2;

    const MODULE_NAME                           = 'Shaurmalab_Score';
    const ENTITY                                = 'score_eav_attribute';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix                     = 'score_oggetto_attribute';
    /**
     * Event object name
     *
     * @var string
     */
    protected $_eventObject                     = 'attribute';

    /**
     * Array with labels
     *
     * @var array
     */
    static protected $_labels                   = null;

    protected function _construct()
    {
        $this->_init('score/attribute');
    }

    /**
     * Processing object before save data
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $this->setData('modulePrefix', self::MODULE_NAME);
        if (isset($this->_origData['is_global'])) {
            if (!isset($this->_data['is_global'])) {
                $this->_data['is_global'] = self::SCOPE_GLOBAL;
            }
            if (($this->_data['is_global'] != $this->_origData['is_global'])
                && $this->_getResource()->isUsedBySuperOggettos($this)) {
                Mage::throwException(Mage::helper('score')->__('Scope must not be changed, because the attribute is used in configurable oggettos.'));
            }
        }
        if ($this->getFrontendInput() == 'price') {
            if (!$this->getBackendModel()) {
                $this->setBackendModel('score/oggetto_attribute_backend_price');
            }
        }
        if ($this->getFrontendInput() == 'textarea') {
            if ($this->getIsWysiwygEnabled()) {
                $this->setIsHtmlAllowedOnFront(1);
            }
        }
        return parent::_beforeSave();
    }

    /**
     * Processing object after save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        /**
         * Fix saving attribute in admin
         */
        Mage::getSingleton('eav/config')->clear();

        Mage::getSingleton('index/indexer')->processEntityAction(
            $this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
        );
        return parent::_afterSave();
    }

    /**
     * Register indexing event before delete score eav attribute
     *
     * @return Shaurmalab_Score_Model_Resource_Eav_Attribute
     */
    protected function _beforeDelete()
    {
        if ($this->_getResource()->isUsedBySuperOggettos($this)) {
            Mage::throwException(Mage::helper('score')->__('This attribute is used in configurable oggettos.'));
        }
        Mage::getSingleton('index/indexer')->logEvent(
            $this, self::ENTITY, Mage_Index_Model_Event::TYPE_DELETE
        );
        return parent::_beforeDelete();
    }

    /**
     * Init indexing process after score eav attribute delete commit
     *
     * @return Shaurmalab_Score_Model_Resource_Eav_Attribute
     */
    protected function _afterDeleteCommit()
    {
        parent::_afterDeleteCommit();
        Mage::getSingleton('index/indexer')->indexEvents(
            self::ENTITY, Mage_Index_Model_Event::TYPE_DELETE
        );
        return $this;
    }

    /**
     * Return is attribute global
     *
     * @return integer
     */
    public function getIsGlobal()
    {
        return $this->_getData('is_global');
    }

    /**
     * Retrieve attribute is global scope flag
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        return $this->getIsGlobal() == self::SCOPE_GLOBAL;
    }

    /**
     * Retrieve attribute is website scope website
     *
     * @return bool
     */
    public function isScopeWebsite()
    {
        return $this->getIsGlobal() == self::SCOPE_WEBSITE;
    }

    /**
     * Retrieve attribute is store scope flag
     *
     * @return bool
     */
    public function isScopeStore()
    {
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }

    /**
     * Retrieve store id
     *
     * @return int
     */
    public function getStoreId()
    {
        $dataObject = $this->getDataObject();
        if ($dataObject) {
            return $dataObject->getStoreId();
        }
        return $this->getData('store_id');
    }

    /**
     * Retrieve apply to oggettos array
     * Return empty array if applied to all oggettos
     *
     * @return array
     */
    public function getApplyTo()
    {
        if ($this->getData('apply_to')) {
            if (is_array($this->getData('apply_to'))) {
                return $this->getData('apply_to');
            }
            return explode(',', $this->getData('apply_to'));
        } else {
            return array();
        }
    }

    /**
     * Retrieve source model
     *
     * @return Mage_Eav_Model_Entity_Attribute_Source_Abstract
     */
    public function getSourceModel()
    {
        $model = $this->getData('source_model');
        if (empty($model)) {
            if ($this->getBackendType() == 'int' && $this->getFrontendInput() == 'select') {
                return $this->_getDefaultSourceModel();
            }
        }
        return $model;
    }

    /**
     * Check is allow for rule condition
     *
     * @return bool
     */
    public function isAllowedForRuleCondition()
    {
        $allowedInputTypes = array('text', 'multiselect', 'textarea', 'date', 'datetime', 'select', 'boolean', 'price');
        return $this->getIsVisible() && in_array($this->getFrontendInput(), $allowedInputTypes);
    }

    /**
     * Retrieve don't translated frontend label
     *
     * @return string
     */
    public function getFrontendLabel()
    {
        return $this->_getData('frontend_label');
    }

    /**
     * Get Attribute translated label for store
     *
     * @deprecated
     * @return string
     */
    protected function _getLabelForStore()
    {
        return $this->getFrontendLabel();
    }

    /**
     * Initialize store Labels for attributes
     *
     * @deprecated
     * @param int $storeId
     */
    public static function initLabels($storeId = null)
    {
        if (is_null(self::$_labels)) {
            if (is_null($storeId)) {
                $storeId = Mage::app()->getStore()->getId();
            }
            $attributeLabels = array();
            $attributes = Mage::getResourceSingleton('score/oggetto')->getAttributesByCode();
            foreach ($attributes as $attribute) {
                if (strlen($attribute->getData('frontend_label')) > 0) {
                    $attributeLabels[] = $attribute->getData('frontend_label');
                }
            }

            self::$_labels = Mage::app()->getTranslator()->getResource()
                ->getTranslationArrayByStrings($attributeLabels, $storeId);
        }
    }

    /**
     * Get default attribute source model
     *
     * @return string
     */
    public function _getDefaultSourceModel()
    {
        return 'eav/entity_attribute_source_table';
    }

    /**
     * Check is an attribute used in EAV index
     *
     * @return bool
     */
    public function isIndexable()
    {
        // exclude price attribute
        if ($this->getAttributeCode() == 'price') {
            return false;
        }

        if (!$this->getIsFilterableInSearch() && !$this->getIsVisibleInAdvancedSearch() && !$this->getIsFilterable()) {
            return false;
        }

        $backendType    = $this->getBackendType();
        $frontendInput  = $this->getFrontendInput();

        if ($backendType == 'int' && $frontendInput == 'select') {
            return true;
        } else if ($backendType == 'varchar' && $frontendInput == 'multiselect') {
            return true;
        } else if ($backendType == 'decimal') {
            return true;
        }

        return false;
    }

    /**
     * Retrieve index type for indexable attribute
     *
     * @return string|false
     */
    public function getIndexType()
    {
        if (!$this->isIndexable()) {
            return false;
        }
        if ($this->getBackendType() == 'decimal') {
            return 'decimal';
        }

        return 'source';
    }
}
