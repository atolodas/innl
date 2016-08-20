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
 * Score oggetto api
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Api extends Shaurmalab_Score_Model_Api_Resource
{
    protected $_filtersMap = array(
        'oggetto_id' => 'entity_id',
        'set'        => 'attribute_set_id',
        'type'       => 'type_id'
    );

    protected $_defaultOggettoAttributeList = array(
        'type_id',
        'category_ids',
        'website_ids',
        'name',
        'description',
        'short_description',
        'sku',
        'weight',
        'status',
        'url_key',
        'url_path',
        'visibility',
        'has_options',
        'gift_message_available',
        'price',
        'special_price',
        'special_from_date',
        'special_to_date',
        'tax_class_id',
        'tier_price',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'custom_design',
        'custom_layout_update',
        'options_container',
        'image_label',
        'small_image_label',
        'thumbnail_label',
        'created_at',
        'updated_at'
    );

    public function __construct()
    {
        $this->_storeIdSessionField = 'oggetto_store_id';
        $this->_ignoredAttributeTypes[] = 'gallery';
        $this->_ignoredAttributeTypes[] = 'media_image';
    }

    /**
     * Retrieve list of oggettos with basic info (id, sku, type, set, name)
     *
     * @param null|object|array $filters
     * @param string|int $store
     * @return array
     */
    public function items($filters = null, $store = null)
    {
        $collection = Mage::getModel('score/oggetto')->getCollection()
            ->addStoreFilter($this->_getStoreId($store))
            ->addAttributeToSelect('name');

        /** @var $apiHelper Mage_Api_Helper_Data */
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters, $this->_filtersMap);
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        $result = array();
        foreach ($collection as $oggetto) {
            $result[] = array(
                'oggetto_id' => $oggetto->getId(),
                'sku'        => $oggetto->getSku(),
                'name'       => $oggetto->getName(),
                'set'        => $oggetto->getAttributeSetId(),
                'type'       => $oggetto->getTypeId(),
                'category_ids' => $oggetto->getCategoryIds(),
                'website_ids'  => $oggetto->getWebsiteIds()
            );
        }
        return $result;
    }

    /**
     * Retrieve oggetto info
     *
     * @param int|string $oggettoId
     * @param string|int $store
     * @param array      $attributes
     * @param string     $identifierType
     * @return array
     */
    public function info($oggettoId, $store = null, $attributes = null, $identifierType = null)
    {
        // make sku flag case-insensitive
        if (!empty($identifierType)) {
            $identifierType = strtolower($identifierType);
        }

        $oggetto = $this->_getOggetto($oggettoId, $store, $identifierType);

        $result = array( // Basic oggetto data
            'oggetto_id' => $oggetto->getId(),
            'sku'        => $oggetto->getSku(),
            'set'        => $oggetto->getAttributeSetId(),
            'type'       => $oggetto->getTypeId(),
            'categories' => $oggetto->getCategoryIds(),
            'websites'   => $oggetto->getWebsiteIds()
        );

        foreach ($oggetto->getTypeInstance(true)->getEditableAttributes($oggetto) as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $attributes)) {
                $result[$attribute->getAttributeCode()] = $oggetto->getData(
                                                                $attribute->getAttributeCode());
            }
        }

        return $result;
    }

    /**
     * Create new oggetto.
     *
     * @param string $type
     * @param int $set
     * @param string $sku
     * @param array $oggettoData
     * @param string $store
     * @return int
     */
    public function create($type, $set, $sku, $oggettoData, $store = null)
    {
        if (!$type || !$set || !$sku) {
            $this->_fault('data_invalid');
        }

        $this->_checkOggettoTypeExists($type);
        $this->_checkOggettoAttributeSet($set);

        /** @var $oggetto Shaurmalab_Score_Model_Oggetto */
        $oggetto = Mage::getModel('score/oggetto');
        $oggetto->setStoreId($this->_getStoreId($store))
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);

        if (!isset($oggettoData['stock_data']) || !is_array($oggettoData['stock_data'])) {
            //Set default stock_data if not exist in oggetto data
            $oggetto->setStockData(array('use_config_manage_stock' => 0));
        }

        foreach ($oggetto->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $oggetto->setData($mediaAttrCode, 'no_selection');
        }

        $this->_prepareDataForSave($oggetto, $oggettoData);

        try {
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             * @todo see Shaurmalab_Score_Model_Oggetto::validate()
             */
            if (is_array($errors = $oggetto->validate())) {
                $strErrors = array();
                foreach($errors as $code => $error) {
                    if ($error === true) {
                        $error = Mage::helper('score')->__('Attribute "%s" is invalid.', $code);
                    }
                    $strErrors[] = $error;
                }
                $this->_fault('data_invalid', implode("\n", $strErrors));
            }

            $oggetto->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $oggetto->getId();
    }

    /**
     * Update oggetto data
     *
     * @param int|string $oggettoId
     * @param array $oggettoData
     * @param string|int $store
     * @return boolean
     */
    public function update($oggettoId, $oggettoData, $store = null, $identifierType = null)
    {
        $oggetto = $this->_getOggetto($oggettoId, $store, $identifierType);

        $this->_prepareDataForSave($oggetto, $oggettoData);

        try {
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             * @todo see Shaurmalab_Score_Model_Oggetto::validate()
             */
            if (is_array($errors = $oggetto->validate())) {
                $strErrors = array();
                foreach($errors as $code => $error) {
                    if ($error === true) {
                        $error = Mage::helper('score')->__('Value for "%s" is invalid.', $code);
                    } else {
                        $error = Mage::helper('score')->__('Value for "%s" is invalid: %s', $code, $error);
                    }
                    $strErrors[] = $error;
                }
                $this->_fault('data_invalid', implode("\n", $strErrors));
            }

            $oggetto->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     *  Set additional data before oggetto saved
     *
     *  @param    Shaurmalab_Score_Model_Oggetto $oggetto
     *  @param    array $oggettoData
     *  @return   object
     */
    protected function _prepareDataForSave($oggetto, $oggettoData)
    {
        if (isset($oggettoData['website_ids']) && is_array($oggettoData['website_ids'])) {
            $oggetto->setWebsiteIds($oggettoData['website_ids']);
        }

        foreach ($oggetto->getTypeInstance(true)->getEditableAttributes($oggetto) as $attribute) {
            //Unset data if object attribute has no value in current store
            if (Shaurmalab_Score_Model_Abstract::DEFAULT_STORE_ID !== (int) $oggetto->getStoreId()
                && !$oggetto->getExistsStoreValueFlag($attribute->getAttributeCode())
                && !$attribute->isScopeGlobal()
            ) {
                $oggetto->setData($attribute->getAttributeCode(), false);
            }

            if ($this->_isAllowedAttribute($attribute)) {
                if (isset($oggettoData[$attribute->getAttributeCode()])) {
                    $oggetto->setData(
                        $attribute->getAttributeCode(),
                        $oggettoData[$attribute->getAttributeCode()]
                    );
                } elseif (isset($oggettoData['additional_attributes']['single_data'][$attribute->getAttributeCode()])) {
                    $oggetto->setData(
                        $attribute->getAttributeCode(),
                        $oggettoData['additional_attributes']['single_data'][$attribute->getAttributeCode()]
                    );
                } elseif (isset($oggettoData['additional_attributes']['multi_data'][$attribute->getAttributeCode()])) {
                    $oggetto->setData(
                        $attribute->getAttributeCode(),
                        $oggettoData['additional_attributes']['multi_data'][$attribute->getAttributeCode()]
                    );
                }
            }
        }

        if (isset($oggettoData['categories']) && is_array($oggettoData['categories'])) {
            $oggetto->setCategoryIds($oggettoData['categories']);
        }

        if (isset($oggettoData['websites']) && is_array($oggettoData['websites'])) {
            foreach ($oggettoData['websites'] as &$website) {
                if (is_string($website)) {
                    try {
                        $website = Mage::app()->getWebsite($website)->getId();
                    } catch (Exception $e) { }
                }
            }
            $oggetto->setWebsiteIds($oggettoData['websites']);
        }

        if (Mage::app()->isSingleStoreMode()) {
            $oggetto->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
        }

        if (isset($oggettoData['stock_data']) && is_array($oggettoData['stock_data'])) {
            $oggetto->setStockData($oggettoData['stock_data']);
        }

        if (isset($oggettoData['tier_price']) && is_array($oggettoData['tier_price'])) {
             $tierPrices = Mage::getModel('score/oggetto_attribute_tierprice_api')
                 ->prepareTierPrices($oggetto, $oggettoData['tier_price']);
             $oggetto->setData(Shaurmalab_Score_Model_Oggetto_Attribute_Tierprice_Api::ATTRIBUTE_CODE, $tierPrices);
        }
    }

    /**
     * Update oggetto special price
     *
     * @param int|string $oggettoId
     * @param float $specialPrice
     * @param string $fromDate
     * @param string $toDate
     * @param string|int $store
     * @return boolean
     */
    public function setSpecialPrice($oggettoId, $specialPrice = null, $fromDate = null, $toDate = null, $store = null)
    {
        return $this->update($oggettoId, array(
            'special_price'     => $specialPrice,
            'special_from_date' => $fromDate,
            'special_to_date'   => $toDate
        ), $store);
    }

    /**
     * Retrieve oggetto special price
     *
     * @param int|string $oggettoId
     * @param string|int $store
     * @return array
     */
    public function getSpecialPrice($oggettoId, $store = null)
    {
        $oggetto = $this->_getOggetto($oggettoId, $store);

        $result = array(
            'special_price'     => $oggetto->getSpecialPrice(),
            'special_from_date' => $oggetto->getSpecialFromDate(),
            'special_to_date'   => $oggetto->getSpecialToDate()
        );

        return $result;
    }

    /**
     * Delete oggetto
     *
     * @param int|string $oggettoId
     * @return boolean
     */
    public function delete($oggettoId, $identifierType = null)
    {
        $oggetto = $this->_getOggetto($oggettoId, null, $identifierType);

        try {
            $oggetto->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
    }

   /**
    * Get list of additional attributes which are not in default create/update list
    *
    * @param  $oggettoType
    * @param  $attributeSetId
    * @return array
    */
    public function getAdditionalAttributes($oggettoType, $attributeSetId)
    {
        $this->_checkOggettoTypeExists($oggettoType);
        $this->_checkOggettoAttributeSet($attributeSetId);

        /** @var $oggetto Shaurmalab_Score_Model_Oggetto */
        $oggettoAttributes = Mage::getModel('score/oggetto')
            ->setAttributeSetId($attributeSetId)
            ->setTypeId($oggettoType)
            ->getTypeInstance(false)
            ->getEditableAttributes();

        $result = array();
        foreach ($oggettoAttributes as $attribute) {
            /* @var $attribute Shaurmalab_Score_Model_Resource_Eav_Attribute */
            if ($attribute->isInSet($attributeSetId) && $this->_isAllowedAttribute($attribute)
                && !in_array($attribute->getAttributeCode(), $this->_defaultOggettoAttributeList)) {

                if ($attribute->isScopeGlobal()) {
                    $scope = 'global';
                } elseif ($attribute->isScopeWebsite()) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $result[] = array(
                    'attribute_id' => $attribute->getId(),
                    'code' => $attribute->getAttributeCode(),
                    'type' => $attribute->getFrontendInput(),
                    'required' => $attribute->getIsRequired(),
                    'scope' => $scope
                );
            }
        }

        return $result;
    }

    /**
     * Check if oggetto type exists
     *
     * @param  $oggettoType
     * @throw Mage_Api_Exception
     * @return void
     */
    protected function _checkOggettoTypeExists($oggettoType)
    {
        if (!in_array($oggettoType, array_keys(Mage::getModel('score/oggetto_type')->getOptionArray()))) {
            $this->_fault('oggetto_type_not_exists');
        }
    }

    /**
     * Check if attributeSet is exits and in score_oggetto entity group type
     *
     * @param  $attributeSetId
     * @throw Mage_Api_Exception
     * @return void
     */
    protected function _checkOggettoAttributeSet($attributeSetId)
    {
        $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($attributeSetId);
        if (is_null($attributeSet->getId())) {
            $this->_fault('oggetto_attribute_set_not_exists');
        }
        if (Mage::getModel('score/oggetto')->getResource()->getTypeId() != $attributeSet->getEntityTypeId()) {
            $this->_fault('oggetto_attribute_set_not_valid');
        }
    }
} // Class Shaurmalab_Score_Model_Oggetto_Api End
