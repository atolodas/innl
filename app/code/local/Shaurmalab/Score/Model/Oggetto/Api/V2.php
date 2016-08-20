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
 * Score oggetto api V2
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Api_V2 extends Shaurmalab_Score_Model_Oggetto_Api
{
    /**
     * Retrieve oggetto info
     *
     * @param int|string $oggettoId
     * @param string|int $store
     * @param stdClass   $attributes
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

        $allAttributes = array();
        if (!empty($attributes->attributes)) {
            $allAttributes = array_merge($allAttributes, $attributes->attributes);
        } else {
            foreach ($oggetto->getTypeInstance(true)->getEditableAttributes($oggetto) as $attribute) {
                if ($this->_isAllowedAttribute($attribute, $attributes)) {
                    $allAttributes[] = $attribute->getAttributeCode();
                }
            }
        }

        $_additionalAttributeCodes = array();
        if (!empty($attributes->additional_attributes)) {
            foreach ($attributes->additional_attributes as $k => $_attributeCode) {
                $allAttributes[] = $_attributeCode;
                $_additionalAttributeCodes[] = $_attributeCode;
            }
        }

        $_additionalAttribute = 0;
        foreach ($oggetto->getTypeInstance(true)->getEditableAttributes($oggetto) as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $allAttributes)) {
                if (in_array($attribute->getAttributeCode(), $_additionalAttributeCodes)) {
                    $result['additional_attributes'][$_additionalAttribute]['key'] = $attribute->getAttributeCode();
                    $result['additional_attributes'][$_additionalAttribute]['value'] = $oggetto
                        ->getData($attribute->getAttributeCode());
                    $_additionalAttribute++;
                } else {
                    $result[$attribute->getAttributeCode()] = $oggetto->getData($attribute->getAttributeCode());
                }
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

        if (!property_exists($oggettoData, 'stock_data')) {
            //Set default stock_data if not exist in oggetto data
            $_stockData = array('use_config_manage_stock' => 0);
            $oggetto->setStockData($_stockData);
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
     * Update multiple oggettos information at once
     *
     * @param array      $oggettoIds
     * @param array      $oggettoData
     * @param string|int $store
     * @param string     $identifierType
     * @return boolean
     */
    public function multiUpdate($oggettoIds, $oggettoData, $store = null, $identifierType = null)
    {
        if (count($oggettoIds) != count($oggettoData)) {
            $this->_fault('multi_update_not_match');
        }

        $oggettoData = (array)$oggettoData;
        $failMessages = array();

        foreach ($oggettoIds as $index => $oggettoId) {
            try {
                $this->update($oggettoId, $oggettoData[$index], $store, $identifierType);
            } catch (Mage_Api_Exception $e) {
                $failMessages[] = sprintf("Oggetto ID %d:\n %s", $oggettoId, $e->getMessage());
            }
        }

        if (empty($failMessages)) {
            return true;
        } else {
            $this->_fault('partially_updated', implode("\n", $failMessages));
        }

        return false;
    }

    /**
     *  Set additional data before oggetto saved
     *
     *  @param    Shaurmalab_Score_Model_Oggetto $oggetto
     *  @param    array $oggettoData
     *  @return   object
     */
    protected function _prepareDataForSave ($oggetto, $oggettoData)
    {
        if (property_exists($oggettoData, 'website_ids') && is_array($oggettoData->website_ids)) {
            $oggetto->setWebsiteIds($oggettoData->website_ids);
        }

        if (property_exists($oggettoData, 'additional_attributes')) {
            if (property_exists($oggettoData->additional_attributes, 'single_data')) {
                foreach ($oggettoData->additional_attributes->single_data as $_attribute) {
                    $_attrCode = $_attribute->key;
                    $oggettoData->$_attrCode = $_attribute->value;
                }
            }
            if (property_exists($oggettoData->additional_attributes, 'multi_data')) {
                foreach ($oggettoData->additional_attributes->multi_data as $_attribute) {
                    $_attrCode = $_attribute->key;
                    $oggettoData->$_attrCode = $_attribute->value;
                }
            }
            unset($oggettoData->additional_attributes);
        }

        foreach ($oggetto->getTypeInstance(true)->getEditableAttributes($oggetto) as $attribute) {
            $_attrCode = $attribute->getAttributeCode();

            //Unset data if object attribute has no value in current store
            if (Shaurmalab_Score_Model_Abstract::DEFAULT_STORE_ID !== (int) $oggetto->getStoreId()
                && !$oggetto->getExistsStoreValueFlag($_attrCode)
                && !$attribute->isScopeGlobal()
            ) {
                $oggetto->setData($_attrCode, false);
            }

            if ($this->_isAllowedAttribute($attribute) && (isset($oggettoData->$_attrCode))) {
                $oggetto->setData(
                    $_attrCode,
                    $oggettoData->$_attrCode
                );
            }
        }

        if (property_exists($oggettoData, 'categories') && is_array($oggettoData->categories)) {
            $oggetto->setCategoryIds($oggettoData->categories);
        }

        if (property_exists($oggettoData, 'websites') && is_array($oggettoData->websites)) {
            foreach ($oggettoData->websites as &$website) {
                if (is_string($website)) {
                    try {
                        $website = Mage::app()->getWebsite($website)->getId();
                    } catch (Exception $e) { }
                }
            }
            $oggetto->setWebsiteIds($oggettoData->websites);
        }

        if (Mage::app()->isSingleStoreMode()) {
            $oggetto->setWebsiteIds(array(Mage::app()->getStore(true)->getWebsite()->getId()));
        }

        if (property_exists($oggettoData, 'stock_data')) {
            $_stockData = array();
            foreach ($oggettoData->stock_data as $key => $value) {
                $_stockData[$key] = $value;
            }
            $oggetto->setStockData($_stockData);
        }

        if (property_exists($oggettoData, 'tier_price')) {
             $tierPrices = Mage::getModel('score/oggetto_attribute_tierprice_api_V2')
                 ->prepareTierPrices($oggetto, $oggettoData->tier_price);
             $oggetto->setData(Shaurmalab_Score_Model_Oggetto_Attribute_Tierprice_Api_V2::ATTRIBUTE_CODE, $tierPrices);
        }
    }

    /**
     * Update oggetto special priceim
     *
     * @param int|string $oggettoId
     * @param float $specialPrice
     * @param string $fromDate
     * @param string $toDate
     * @param string|int $store
     * @param string $identifierType OPTIONAL If 'sku' - search oggetto by SKU, if any except for NULL - search by ID,
     *                                        otherwise - try to determine identifier type automatically
     * @return boolean
     */
    public function setSpecialPrice($oggettoId, $specialPrice = null, $fromDate = null, $toDate = null, $store = null,
        $identifierType = null
    ) {
        $obj = new stdClass();
        $obj->special_price = $specialPrice;
        $obj->special_from_date = $fromDate;
        $obj->special_to_date = $toDate;
        return $this->update($oggettoId, $obj, $store, $identifierType);
    }
}
