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
 * API2 for score_oggetto (Admin)
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Api2_Oggetto_Rest_Admin_V1 extends Shaurmalab_Score_Model_Api2_Oggetto_Rest
{
    /**
     * The greatest decimal value which could be stored. Corresponds to DECIMAL (12,4) SQL type
     */
    const MAX_DECIMAL_VALUE = 99999999.9999;

    /**
     * Add special fields to oggetto get response
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     */
    protected function _prepareOggettoForResponse(Shaurmalab_Score_Model_Oggetto $oggetto)
    {
        $pricesFilterKeys = array('price_id', 'all_groups', 'website_price');
        $groupPrice = $oggetto->getData('group_price');
        $oggetto->setData('group_price', $this->_filterOutArrayKeys($groupPrice, $pricesFilterKeys, true));
        $tierPrice = $oggetto->getData('tier_price');
        $oggetto->setData('tier_price', $this->_filterOutArrayKeys($tierPrice, $pricesFilterKeys, true));

        $stockData = $oggetto->getStockItem()->getData();
        $stockDataFilterKeys = array('item_id', 'oggetto_id', 'stock_id', 'low_stock_date', 'type_id',
            'stock_status_changed_auto', 'stock_status_changed_automatically', 'oggetto_name', 'store_id',
            'oggetto_type_id', 'oggetto_status_changed', 'oggetto_changed_websites',
            'use_config_enable_qty_increments');
        $oggetto->setData('stock_data', $this->_filterOutArrayKeys($stockData, $stockDataFilterKeys));
        $oggetto->setData('oggetto_type_name', $oggetto->getTypeId());
    }

    /**
     * Remove specified keys from associative or indexed array
     *
     * @param array $array
     * @param array $keys
     * @param bool $dropOrigKeys if true - return array as indexed array
     * @return array
     */
    protected function _filterOutArrayKeys(array $array, array $keys, $dropOrigKeys = false)
    {
        $isIndexedArray = is_array(reset($array));
        if ($isIndexedArray) {
            foreach ($array as &$value) {
                if (is_array($value)) {
                    $value = array_diff_key($value, array_flip($keys));
                }
            }
            if ($dropOrigKeys) {
                $array = array_values($array);
            }
            unset($value);
        } else {
            $array = array_diff_key($array, array_flip($keys));
        }

        return $array;
    }

    /**
     * Retrieve list of oggettos
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        /** @var $collection Shaurmalab_Score_Model_Resource_Oggetto_Collection */
        $collection = Mage::getResourceModel('score/oggetto_collection');
        $store = $this->_getStore();
        $collection->setStoreId($store->getId());
        $collection->addAttributeToSelect(array_keys(
            $this->getAvailableAttributes($this->getUserType(), Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
        ));
        $this->_applyCategoryFilter($collection);
        $this->_applyCollectionModifiers($collection);
        $oggettos = $collection->load()->toArray();
        return $oggettos;
    }

    /**
     * Delete oggetto by its ID
     *
     * @throws Mage_Api2_Exception
     */
    protected function _delete()
    {
        $oggetto = $this->_getOggetto();
        try {
            $oggetto->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Create oggetto
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        /* @var $validator Shaurmalab_Score_Model_Api2_Oggetto_Validator_Oggetto */
        $validator = Mage::getModel('score/api2_oggetto_validator_oggetto', array(
            'operation' => self::OPERATION_CREATE
        ));

        if (!$validator->isValidData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        $type = $data['type_id'];
        if ($type !== 'simple') {
            $this->_critical("Creation of oggettos with type '$type' is not implemented",
                Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED);
        }
        $set = $data['attribute_set_id'];
        $sku = $data['sku'];

        /** @var $oggetto Shaurmalab_Score_Model_Oggetto */
        $oggetto = Mage::getModel('score/oggetto')
            ->setStoreId(Shaurmalab_Score_Model_Abstract::DEFAULT_STORE_ID)
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);

        foreach ($oggetto->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $oggetto->setData($mediaAttrCode, 'no_selection');
        }

        $this->_prepareDataForSave($oggetto, $data);
        try {
            $oggetto->validate();
            $oggetto->save();
            $this->_multicall($oggetto->getId());
        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $this->_critical(sprintf('Invalid attribute "%s": %s', $e->getAttributeCode(), $e->getMessage()),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }

        return $this->_getLocation($oggetto);
    }

    /**
     * Update oggetto by its ID
     *
     * @param array $data
     */
    protected function _update(array $data)
    {
        /** @var $oggetto Shaurmalab_Score_Model_Oggetto */
        $oggetto = $this->_getOggetto();
        /* @var $validator Shaurmalab_Score_Model_Api2_Oggetto_Validator_Oggetto */
        $validator = Mage::getModel('score/api2_oggetto_validator_oggetto', array(
            'operation' => self::OPERATION_UPDATE,
            'oggetto'   => $oggetto
        ));

        if (!$validator->isValidData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }
        if (isset($data['sku'])) {
            $oggetto->setSku($data['sku']);
        }
        // attribute set and oggetto type cannot be updated
        unset($data['attribute_set_id']);
        unset($data['type_id']);
        $this->_prepareDataForSave($oggetto, $data);
        try {
            $oggetto->validate();
            $oggetto->save();
        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $this->_critical(sprintf('Invalid attribute "%s": %s', $e->getAttributeCode(), $e->getMessage()),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }
    }

    /**
     * Determine if stock management is enabled
     *
     * @param array $stockData
     * @return bool
     */
    protected function _isManageStockEnabled($stockData)
    {
        if (!(isset($stockData['use_config_manage_stock']) && $stockData['use_config_manage_stock'])) {
            $manageStock = isset($stockData['manage_stock']) && $stockData['manage_stock'];
        } else {
            $manageStock = Mage::getStoreConfig(
                Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . 'manage_stock');
        }
        return (bool) $manageStock;
    }

    /**
     * Check if value from config is used
     *
     * @param array $data
     * @param string $field
     * @return bool
     */
    protected function _isConfigValueUsed($data, $field)
    {
        return isset($data["use_config_$field"]) && $data["use_config_$field"];
    }

    /**
     * Set additional data before oggetto save
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param array $oggettoData
     */
    protected function _prepareDataForSave($oggetto, $oggettoData)
    {
        if (isset($oggettoData['stock_data'])) {
            if (!$oggetto->isObjectNew() && !isset($oggettoData['stock_data']['manage_stock'])) {
                $oggettoData['stock_data']['manage_stock'] = $oggetto->getStockItem()->getManageStock();
            }
            $this->_filterStockData($oggettoData['stock_data']);
        } else {
            $oggettoData['stock_data'] = array(
                'use_config_manage_stock' => 1,
                'use_config_min_sale_qty' => 1,
                'use_config_max_sale_qty' => 1,
            );
        }
        $oggetto->setStockData($oggettoData['stock_data']);
        // save gift options
        $this->_filterConfigValueUsed($oggettoData, array('gift_message_available', 'gift_wrapping_available'));
        if (isset($oggettoData['use_config_gift_message_available'])) {
            $oggetto->setData('use_config_gift_message_available', $oggettoData['use_config_gift_message_available']);
            if (!$oggettoData['use_config_gift_message_available']
                && ($oggetto->getData('gift_message_available') === null)) {
                $oggetto->setData('gift_message_available', (int) Mage::getStoreConfig(
                    Mage_GiftMessage_Helper_Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS, $oggetto->getStoreId()));
            }
        }
        if (isset($oggettoData['use_config_gift_wrapping_available'])) {
            $oggetto->setData('use_config_gift_wrapping_available', $oggettoData['use_config_gift_wrapping_available']);
            if (!$oggettoData['use_config_gift_wrapping_available']
                && ($oggetto->getData('gift_wrapping_available') === null)
            ) {
                $xmlPathGiftWrappingAvailable = 'sales/gift_options/wrapping_allow_items';
                $oggetto->setData('gift_wrapping_available', (int)Mage::getStoreConfig(
                    $xmlPathGiftWrappingAvailable, $oggetto->getStoreId()));
            }
        }

        if (isset($oggettoData['website_ids']) && is_array($oggettoData['website_ids'])) {
            $oggetto->setWebsiteIds($oggettoData['website_ids']);
        }
        // Create Permanent Redirect for old URL key
        if (!$oggetto->isObjectNew()  && isset($oggettoData['url_key'])
            && isset($oggettoData['url_key_create_redirect'])
        ) {
            $oggetto->setData('save_rewrites_history', (bool)$oggettoData['url_key_create_redirect']);
        }
        /** @var $attribute Shaurmalab_Score_Model_Resource_Eav_Attribute */
        foreach ($oggetto->getTypeInstance(true)->getEditableAttributes($oggetto) as $attribute) {
            //Unset data if object attribute has no value in current store
            if (Shaurmalab_Score_Model_Abstract::DEFAULT_STORE_ID !== (int)$oggetto->getStoreId()
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
                }
            }
        }
    }

    /**
     * Filter stock data values
     *
     * @param array $stockData
     */
    protected function _filterStockData(&$stockData)
    {
        $fieldsWithPossibleDefautlValuesInConfig = array('manage_stock', 'min_sale_qty', 'max_sale_qty', 'backorders',
            'qty_increments', 'notify_stock_qty', 'min_qty', 'enable_qty_increments');
        $this->_filterConfigValueUsed($stockData, $fieldsWithPossibleDefautlValuesInConfig);

        if ($this->_isManageStockEnabled($stockData)) {
            if (isset($stockData['qty']) && (float)$stockData['qty'] > self::MAX_DECIMAL_VALUE) {
                $stockData['qty'] = self::MAX_DECIMAL_VALUE;
            }
            if (isset($stockData['min_qty']) && (int)$stockData['min_qty'] < 0) {
                $stockData['min_qty'] = 0;
            }
            if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
                $stockData['is_decimal_divided'] = 0;
            }
        } else {
            $nonManageStockFields = array('manage_stock', 'use_config_manage_stock', 'min_sale_qty',
                'use_config_min_sale_qty', 'max_sale_qty', 'use_config_max_sale_qty');
            foreach ($stockData as $field => $value) {
                if (!in_array($field, $nonManageStockFields)) {
                    unset($stockData[$field]);
                }
            }
        }
    }

    /**
     * Filter out fields if Use Config Settings option used
     *
     * @param array $data
     * @param string $fields
     */
    protected function _filterConfigValueUsed(&$data, $fields) {
        foreach($fields as $field) {
            if ($this->_isConfigValueUsed($data, $field)) {
                unset($data[$field]);
            }
        }
    }

    /**
     * Check if attribute is allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array $attributes
     * @return boolean
     */
    protected function _isAllowedAttribute($attribute, $attributes = null)
    {
        $isAllowed = true;
        if (is_array($attributes)
            && !(in_array($attribute->getAttributeCode(), $attributes)
            || in_array($attribute->getAttributeId(), $attributes))
        ) {
            $isAllowed = false;
        }
        return $isAllowed;
    }
}
