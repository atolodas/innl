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
 * Oggetto type price model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Type_Price
{
    const CACHE_TAG = 'OGGETTO_PRICE';

    static $attributeCache = array();

    /**
     * Default action to get price of oggetto
     *
     * @return decimal
     */
    public function getPrice($oggetto)
    {
        return $oggetto->getData('price');
    }

    /**
     * Get base price with apply Group, Tier, Special prises
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param float|null $qty
     *
     * @return float
     */
    public function getBasePrice($oggetto, $qty = null)
    {
        $price = (float)$oggetto->getPrice();
        return min($this->_applyGroupPrice($oggetto, $price), $this->_applyTierPrice($oggetto, $qty, $price),
            $this->_applySpecialPrice($oggetto, $price)
        );
    }


    /**
     * Retrieve oggetto final price
     *
     * @param float|null $qty
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return float
     */
    public function getFinalPrice($qty = null, $oggetto)
    {
        if (is_null($qty) && !is_null($oggetto->getCalculatedFinalPrice())) {
            return $oggetto->getCalculatedFinalPrice();
        }

        $finalPrice = $this->getBasePrice($oggetto, $qty);
        $oggetto->setFinalPrice($finalPrice);

        Mage::dispatchEvent('score_oggetto_get_final_price', array('oggetto' => $oggetto, 'qty' => $qty));

        $finalPrice = $oggetto->getData('final_price');
        $finalPrice = $this->_applyOptionsPrice($oggetto, $qty, $finalPrice);
        $finalPrice = max(0, $finalPrice);
        $oggetto->setFinalPrice($finalPrice);

        return $finalPrice;
    }

    public function getChildFinalPrice($oggetto, $oggettoQty, $childOggetto, $childOggettoQty)
    {
        return $this->getFinalPrice($childOggettoQty, $childOggetto);
    }

    /**
     * Apply group price for oggetto
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param float $finalPrice
     * @return float
     */
    protected function _applyGroupPrice($oggetto, $finalPrice)
    {
        $groupPrice = $oggetto->getGroupPrice();
        if (is_numeric($groupPrice)) {
            $finalPrice = min($finalPrice, $groupPrice);
        }
        return $finalPrice;
    }

    /**
     * Get oggetto group price
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return float
     */
    public function getGroupPrice($oggetto)
    {

        $groupPrices = $oggetto->getData('group_price');

        if (is_null($groupPrices)) {
            $attribute = $oggetto->getResource()->getAttribute('group_price');
            if ($attribute) {
                $attribute->getBackend()->afterLoad($oggetto);
                $groupPrices = $oggetto->getData('group_price');
            }
        }

        if (is_null($groupPrices) || !is_array($groupPrices)) {
            return $oggetto->getPrice();
        }

        $customerGroup = $this->_getCustomerGroupId($oggetto);

        $matchedPrice = $oggetto->getPrice();
        foreach ($groupPrices as $groupPrice) {
            if ($groupPrice['cust_group'] == $customerGroup && $groupPrice['website_price'] < $matchedPrice) {
                $matchedPrice = $groupPrice['website_price'];
                break;
            }
        }

        return $matchedPrice;
    }

    /**
     * Apply tier price for oggetto if not return price that was before
     *
     * @param   Shaurmalab_Score_Model_Oggetto $oggetto
     * @param   float $qty
     * @param   float $finalPrice
     * @return  float
     */
    protected function _applyTierPrice($oggetto, $qty, $finalPrice)
    {
        if (is_null($qty)) {
            return $finalPrice;
        }

        $tierPrice  = $oggetto->getTierPrice($qty);
        if (is_numeric($tierPrice)) {
            $finalPrice = min($finalPrice, $tierPrice);
        }
        return $finalPrice;
    }

    /**
     * Get oggetto tier price by qty
     *
     * @param   float $qty
     * @param   Shaurmalab_Score_Model_Oggetto $oggetto
     * @return  float
     */
    public function getTierPrice($qty = null, $oggetto)
    {
        $allGroups = Mage_Customer_Model_Group::CUST_GROUP_ALL;
        $prices = $oggetto->getData('tier_price');

        if (is_null($prices)) {
            $attribute = $oggetto->getResource()->getAttribute('tier_price');
            if ($attribute) {
                $attribute->getBackend()->afterLoad($oggetto);
                $prices = $oggetto->getData('tier_price');
            }
        }

        if (is_null($prices) || !is_array($prices)) {
            if (!is_null($qty)) {
                return $oggetto->getPrice();
            }
            return array(array(
                'price'         => $oggetto->getPrice(),
                'website_price' => $oggetto->getPrice(),
                'price_qty'     => 1,
                'cust_group'    => $allGroups,
            ));
        }

        $custGroup = $this->_getCustomerGroupId($oggetto);
        if ($qty) {
            $prevQty = 1;
            $prevPrice = $oggetto->getPrice();
            $prevGroup = $allGroups;

            foreach ($prices as $price) {
                if ($price['cust_group']!=$custGroup && $price['cust_group']!=$allGroups) {
                    // tier not for current customer group nor is for all groups
                    continue;
                }
                if ($qty < $price['price_qty']) {
                    // tier is higher than oggetto qty
                    continue;
                }
                if ($price['price_qty'] < $prevQty) {
                    // higher tier qty already found
                    continue;
                }
                if ($price['price_qty'] == $prevQty && $prevGroup != $allGroups && $price['cust_group'] == $allGroups) {
                    // found tier qty is same as current tier qty but current tier group is ALL_GROUPS
                    continue;
                }
                if ($price['website_price'] < $prevPrice) {
                    $prevPrice  = $price['website_price'];
                    $prevQty    = $price['price_qty'];
                    $prevGroup  = $price['cust_group'];
                }
            }
            return $prevPrice;
        } else {
            $qtyCache = array();
            foreach ($prices as $i => $price) {
                if ($price['cust_group'] != $custGroup && $price['cust_group'] != $allGroups) {
                    unset($prices[$i]);
                } else if (isset($qtyCache[$price['price_qty']])) {
                    $j = $qtyCache[$price['price_qty']];
                    if ($prices[$j]['website_price'] > $price['website_price']) {
                        unset($prices[$j]);
                        $qtyCache[$price['price_qty']] = $i;
                    } else {
                        unset($prices[$i]);
                    }
                } else {
                    $qtyCache[$price['price_qty']] = $i;
                }
            }
        }

        return ($prices) ? $prices : array();
    }

    protected function _getCustomerGroupId($oggetto)
    {
        if ($oggetto->getCustomerGroupId()) {
            return $oggetto->getCustomerGroupId();
        }
        return Mage::getSingleton('customer/session')->getCustomerGroupId();
    }

    /**
     * Apply special price for oggetto if not return price that was before
     *
     * @param   Shaurmalab_Score_Model_Oggetto $oggetto
     * @param   float $finalPrice
     * @return  float
     */
    protected function _applySpecialPrice($oggetto, $finalPrice)
    {
        return $this->calculateSpecialPrice($finalPrice, $oggetto->getSpecialPrice(), $oggetto->getSpecialFromDate(),
                        $oggetto->getSpecialToDate(), $oggetto->getStore()
        );
    }

    /**
     * Count how many tier prices we have for the oggetto
     *
     * @param   Shaurmalab_Score_Model_Oggetto $oggetto
     * @return  int
     */
    public function getTierPriceCount($oggetto)
    {
        $price = $oggetto->getTierPrice();
        return count($price);
    }

    /**
     * Get formatted by currency tier price
     *
     * @param   float $qty
     * @param   Shaurmalab_Score_Model_Oggetto $oggetto
     * @return  array || float
     */
    public function getFormatedTierPrice($qty=null, $oggetto)
    {
        $price = $oggetto->getTierPrice($qty);
        if (is_array($price)) {
            foreach ($price as $index => $value) {
                $price[$index]['formated_price'] = Mage::app()->getStore()->convertPrice(
                        $price[$index]['website_price'], true
                );
            }
        }
        else {
            $price = Mage::app()->getStore()->formatPrice($price);
        }

        return $price;
    }

    /**
     * Get formatted by currency oggetto price
     *
     * @param   Shaurmalab_Score_Model_Oggetto $oggetto
     * @return  array || float
     */
    public function getFormatedPrice($oggetto)
    {
        return Mage::app()->getStore()->formatPrice($oggetto->getFinalPrice());
    }

    /**
     * Apply options price
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param int $qty
     * @param float $finalPrice
     * @return float
     */
    protected function _applyOptionsPrice($oggetto, $qty, $finalPrice)
    {
        if ($optionIds = $oggetto->getCustomOption('option_ids')) {
            $basePrice = $finalPrice;
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $oggetto->getOptionById($optionId)) {
                    $confItemOption = $oggetto->getCustomOption('option_'.$option->getId());

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItemOption($confItemOption);
                    $finalPrice += $group->getOptionPrice($confItemOption->getValue(), $basePrice);
                }
            }
        }

        return $finalPrice;
    }

    /**
     * Calculate oggetto price based on special price data and price rules
     *
     * @param   float $basePrice
     * @param   float $specialPrice
     * @param   string $specialPriceFrom
     * @param   string $specialPriceTo
     * @param   float|null|false $rulePrice
     * @param   mixed $wId
     * @param   mixed $gId
     * @param   null|int $oggettoId
     * @return  float
     */
    public static function calculatePrice($basePrice, $specialPrice, $specialPriceFrom, $specialPriceTo,
            $rulePrice = false, $wId = null, $gId = null, $oggettoId = null)
    {
        Varien_Profiler::start('__OGGETTO_CALCULATE_PRICE__');
        if ($wId instanceof Mage_Core_Model_Store) {
            $sId = $wId->getId();
            $wId = $wId->getWebsiteId();
        } else {
            $sId = Mage::app()->getWebsite($wId)->getDefaultGroup()->getDefaultStoreId();
        }

        $finalPrice = $basePrice;
        if ($gId instanceof Mage_Customer_Model_Group) {
            $gId = $gId->getId();
        }

        $finalPrice = self::calculateSpecialPrice($finalPrice, $specialPrice, $specialPriceFrom, $specialPriceTo, $sId);

        if ($rulePrice === false) {
            $storeTimestamp = Mage::app()->getLocale()->storeTimeStamp($sId);
            $rulePrice = Mage::getResourceModel('catalogrule/rule')
                ->getRulePrice($storeTimestamp, $wId, $gId, $oggettoId);
        }

        if ($rulePrice !== null && $rulePrice !== false) {
            $finalPrice = min($finalPrice, $rulePrice);
        }

        $finalPrice = max($finalPrice, 0);
        Varien_Profiler::stop('__OGGETTO_CALCULATE_PRICE__');
        return $finalPrice;
    }

    /**
     * Calculate and apply special price
     *
     * @param float $finalPrice
     * @param float $specialPrice
     * @param string $specialPriceFrom
     * @param string $specialPriceTo
     * @param mixed $store
     * @return float
     */
    public static function calculateSpecialPrice($finalPrice, $specialPrice, $specialPriceFrom, $specialPriceTo,
            $store = null)
    {
        if (!is_null($specialPrice) && $specialPrice != false) {
            if (Mage::app()->getLocale()->isStoreDateInInterval($store, $specialPriceFrom, $specialPriceTo)) {
                $finalPrice     = min($finalPrice, $specialPrice);
            }
        }
        return $finalPrice;
    }

    /**
     * Check is tier price value fixed or percent of original price
     *
     * @return bool
     */
    public function isTierPriceFixed()
    {
        return $this->isGroupPriceFixed();
    }

    /**
     * Check is group price value fixed or percent of original price
     *
     * @return bool
     */
    public function isGroupPriceFixed()
    {
        return true;
    }
}
