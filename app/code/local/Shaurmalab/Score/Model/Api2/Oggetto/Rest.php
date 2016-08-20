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
 * Abstract API2 class for oggetto instance
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Shaurmalab_Score_Model_Api2_Oggetto_Rest extends Shaurmalab_Score_Model_Api2_Oggetto
{
    /**
     * Current loaded oggetto
     *
     * @var Shaurmalab_Score_Model_Oggetto
     */
    protected $_oggetto;

    /**
     * Retrieve oggetto data
     *
     * @return array
     */
    protected function _retrieve()
    {
        $oggetto = $this->_getOggetto();

        $this->_prepareOggettoForResponse($oggetto);
        return $oggetto->getData();
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
        $entityOnlyAttributes = $this->getEntityOnlyAttributes($this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ);
        $availableAttributes = array_keys($this->getAvailableAttributes($this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ));
        // available attributes not contain image attribute, but it needed for get image_url
        $availableAttributes[] = 'image';
        $collection->addStoreFilter($store->getId())
            ->addPriceData($this->_getCustomerGroupId(), $store->getWebsiteId())
            ->addAttributeToSelect(array_diff($availableAttributes, $entityOnlyAttributes))
            ->addAttributeToFilter('visibility', array(
                'neq' => Shaurmalab_Score_Model_Oggetto_Visibility::VISIBILITY_NOT_VISIBLE))
            ->addAttributeToFilter('status', array('eq' => Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED));
        $this->_applyCategoryFilter($collection);
        $this->_applyCollectionModifiers($collection);
        $oggettos = $collection->load();

        /** @var Shaurmalab_Score_Model_Oggetto $oggetto */
        foreach ($oggettos as $oggetto) {
            $this->_setOggetto($oggetto);
            $this->_prepareOggettoForResponse($oggetto);
        }
        return $oggettos->toArray();
    }

    /**
     * Apply filter by category id
     *
     * @param Shaurmalab_Score_Model_Resource_Oggetto_Collection $collection
     */
    protected function _applyCategoryFilter(Shaurmalab_Score_Model_Resource_Oggetto_Collection $collection)
    {
        $categoryId = $this->getRequest()->getParam('category_id');
        if ($categoryId) {
            $category = $this->_getCategoryById($categoryId);
            if (!$category->getId()) {
                $this->_critical('Category not found.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $collection->addCategoryFilter($category);
        }
    }

    /**
     * Add special fields to oggetto get response
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     */
    protected function _prepareOggettoForResponse(Shaurmalab_Score_Model_Oggetto $oggetto)
    {
        /** @var $oggettoHelper Shaurmalab_Score_Helper_Oggetto */
        $oggettoHelper = Mage::helper('score/oggetto');
        $oggettoData = $oggetto->getData();
        $oggetto->setWebsiteId($this->_getStore()->getWebsiteId());
        // customer group is required in oggetto for correct prices calculation
        $oggetto->setCustomerGroupId($this->_getCustomerGroupId());
        // calculate prices
        $finalPrice = $oggetto->getFinalPrice();
        $oggettoData['regular_price_with_tax'] = $this->_applyTaxToPrice($oggetto->getPrice(), true);
        $oggettoData['regular_price_without_tax'] = $this->_applyTaxToPrice($oggetto->getPrice(), false);
        $oggettoData['final_price_with_tax'] = $this->_applyTaxToPrice($finalPrice, true);
        $oggettoData['final_price_without_tax'] = $this->_applyTaxToPrice($finalPrice, false);

        $oggettoData['is_saleable'] = $oggetto->getIsSalable();
        $oggettoData['image_url'] = (string) Mage::helper('score/image')->init($oggetto, 'image');

        if ($this->getActionType() == self::ACTION_TYPE_ENTITY) {
            // define URLs
            $oggettoData['url'] = $oggettoHelper->getOggettoUrl($oggetto->getId());
            /** @var $cartHelper Mage_Checkout_Helper_Cart */
            $cartHelper = Mage::helper('checkout/cart');
            $oggettoData['buy_now_url'] = $cartHelper->getAddUrl($oggetto);

            /** @var $stockItem Shaurmalab_ScoreInventory_Model_Stock_Item */
            $stockItem = $oggetto->getStockItem();
            if (!$stockItem) {
                $stockItem = Mage::getModel('cataloginventory/stock_item');
                $stockItem->loadByOggetto($oggetto);
            }
            $oggettoData['is_in_stock'] = $stockItem->getIsInStock();

            /** @var $reviewModel Mage_Review_Model_Review */
            $reviewModel = Mage::getModel('review/review');
            $oggettoData['total_reviews_count'] = $reviewModel->getTotalReviews($oggetto->getId(), true,
                $this->_getStore()->getId());

            $oggettoData['tier_price'] = $this->_getTierPrices();
            $oggettoData['has_custom_options'] = count($oggetto->getOptions()) > 0;
        } else {
            // remove tier price from response
            $oggetto->unsetData('tier_price');
            unset($oggettoData['tier_price']);
        }
        $oggetto->addData($oggettoData);
    }

    /**
     * Oggetto create only available for admin
     *
     * @param array $data
     */
    protected function _create(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Oggetto update only available for admin
     *
     * @param array $data
     */
    protected function _update(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Oggetto delete only available for admin
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Load oggetto by its SKU or ID provided in request
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    protected function _getOggetto()
    {
        if (is_null($this->_oggetto)) {
            $oggettoId = $this->getRequest()->getParam('id');
            /** @var $oggettoHelper Shaurmalab_Score_Helper_Oggetto */
            $oggettoHelper = Mage::helper('score/oggetto');
            $oggetto = $oggettoHelper->getOggetto($oggettoId, $this->_getStore()->getId());
            if (!($oggetto->getId())) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            // check if oggetto belongs to website current
            if ($this->_getStore()->getId()) {
                $isValidWebsite = in_array($this->_getStore()->getWebsiteId(), $oggetto->getWebsiteIds());
                if (!$isValidWebsite) {
                    $this->_critical(self::RESOURCE_NOT_FOUND);
                }
            }
            // Check display settings for customers & guests
            if ($this->getApiUser()->getType() != Mage_Api2_Model_Auth_User_Admin::USER_TYPE) {
                // check if oggetto assigned to any website and can be shown
                if ((!Mage::app()->isSingleStoreMode() && !count($oggetto->getWebsiteIds()))
                    || !$oggettoHelper->canShow($oggetto)
                ) {
                    $this->_critical(self::RESOURCE_NOT_FOUND);
                }
            }
            $this->_oggetto = $oggetto;
        }
        return $this->_oggetto;
    }

    /**
     * Set oggetto
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     */
    protected function _setOggetto(Shaurmalab_Score_Model_Oggetto $oggetto)
    {
        $this->_oggetto = $oggetto;
    }

    /**
     * Load category by id
     *
     * @param int $categoryId
     * @return Shaurmalab_Score_Model_Category
     */
    protected function _getCategoryById($categoryId)
    {
        return Mage::getModel('score/category')->load($categoryId);
    }

    /**
     * Get oggetto price with all tax settings processing
     *
     * @param float $price inputed oggetto price
     * @param bool $includingTax return price include tax flag
     * @param null|Mage_Customer_Model_Address $shippingAddress
     * @param null|Mage_Customer_Model_Address $billingAddress
     * @param null|int $ctc customer tax class
     * @param bool $priceIncludesTax flag that price parameter contain tax
     * @return float
     * @see Mage_Tax_Helper_Data::getPrice()
     */
    protected function _getPrice($price, $includingTax = null, $shippingAddress = null,
        $billingAddress = null, $ctc = null, $priceIncludesTax = null
    ) {
        $oggetto = $this->_getOggetto();
        $store = $this->_getStore();

        if (is_null($priceIncludesTax)) {
            /** @var $config Mage_Tax_Model_Config */
            $config = Mage::getSingleton('tax/config');
            $priceIncludesTax = $config->priceIncludesTax($store) || $config->getNeedUseShippingExcludeTax();
        }

        $percent = $oggetto->getTaxPercent();
        $includingPercent = null;

        $taxClassId = $oggetto->getTaxClassId();
        if (is_null($percent)) {
            if ($taxClassId) {
                $request = Mage::getSingleton('tax/calculation')
                    ->getRateRequest($shippingAddress, $billingAddress, $ctc, $store);
                $percent = Mage::getSingleton('tax/calculation')->getRate($request->setOggettoClassId($taxClassId));
            }
        }
        if ($taxClassId && $priceIncludesTax) {
            $request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false, $store);
            $includingPercent = Mage::getSingleton('tax/calculation')
                ->getRate($request->setOggettoClassId($taxClassId));
        }

        if ($percent === false || is_null($percent)) {
            if ($priceIncludesTax && !$includingPercent) {
                return $price;
            }
        }
        $oggetto->setTaxPercent($percent);

        if (!is_null($includingTax)) {
            if ($priceIncludesTax) {
                if ($includingTax) {
                    /**
                     * Recalculate price include tax in case of different rates
                     */
                    if ($includingPercent != $percent) {
                        $price = $this->_calculatePrice($price, $includingPercent, false);
                        /**
                         * Using regular rounding. Ex:
                         * price incl tax   = 52.76
                         * store tax rate   = 19.6%
                         * customer tax rate= 19%
                         *
                         * price excl tax = 52.76 / 1.196 = 44.11371237 ~ 44.11
                         * tax = 44.11371237 * 0.19 = 8.381605351 ~ 8.38
                         * price incl tax = 52.49531773 ~ 52.50 != 52.49
                         *
                         * that why we need round prices excluding tax before applying tax
                         * this calculation is used for showing prices on score pages
                         */
                        if ($percent != 0) {
                            $price = Mage::getSingleton('tax/calculation')->round($price);
                            $price = $this->_calculatePrice($price, $percent, true);
                        }
                    }
                } else {
                    $price = $this->_calculatePrice($price, $includingPercent, false);
                }
            } else {
                if ($includingTax) {
                    $price = $this->_calculatePrice($price, $percent, true);
                }
            }
        } else {
            if ($priceIncludesTax) {
                if ($includingTax) {
                    $price = $this->_calculatePrice($price, $includingPercent, false);
                    $price = $this->_calculatePrice($price, $percent, true);
                } else {
                    $price = $this->_calculatePrice($price, $includingPercent, false);
                }
            } else {
                if ($includingTax) {
                    $price = $this->_calculatePrice($price, $percent, true);
                }
            }
        }

        return $store->roundPrice($price);
    }

    /**
     * Calculate price imcluding/excluding tax base on tax rate percent
     *
     * @param float $price
     * @param float $percent
     * @param bool $includeTax true - for calculate price including tax and false if price excluding tax
     * @return float
     */
    protected function _calculatePrice($price, $percent, $includeTax)
    {
        /** @var $calculator Mage_Tax_Model_Calculation */
        $calculator = Mage::getSingleton('tax/calculation');
        $taxAmount = $calculator->calcTaxAmount($price, $percent, !$includeTax, false);

        return $includeTax ? $price + $taxAmount : $price - $taxAmount;
    }

    /**
     * Retrive tier prices in special format
     *
     * @return array
     */
    protected function _getTierPrices()
    {
        $tierPrices = array();
        foreach ($this->_getOggetto()->getTierPrice() as $tierPrice) {
            $tierPrices[] = array(
                'qty' => $tierPrice['price_qty'],
                'price_with_tax' => $this->_applyTaxToPrice($tierPrice['price']),
                'price_without_tax' => $this->_applyTaxToPrice($tierPrice['price'], false)
            );
        }
        return $tierPrices;
    }

    /**
     * Default implementation. May be different for customer/guest/admin role.
     *
     * @return null
     */
    protected function _getCustomerGroupId()
    {
        return null;
    }

    /**
     * Default implementation. May be different for customer/guest/admin role.
     *
     * @param float $price
     * @param bool $withTax
     * @return float
     */
    protected function _applyTaxToPrice($price, $withTax = true)
    {
        return $price;
    }
}
