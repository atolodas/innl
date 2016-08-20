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
class Shaurmalab_Score_Model_Oggetto_Type_Configurable_Price extends Shaurmalab_Score_Model_Oggetto_Type_Price
{
    /**
     * Get oggetto final price
     *
     * @param   double $qty
     * @param   Shaurmalab_Score_Model_Oggetto $oggetto
     * @return  double
     */
    public function getFinalPrice($qty=null, $oggetto)
    {
        if (is_null($qty) && !is_null($oggetto->getCalculatedFinalPrice())) {
            return $oggetto->getCalculatedFinalPrice();
        }

        $basePrice = $this->getBasePrice($oggetto, $qty);
        $finalPrice = $basePrice;
        $oggetto->setFinalPrice($finalPrice);
        Mage::dispatchEvent('score_oggetto_get_final_price', array('oggetto' => $oggetto, 'qty' => $qty));
        $finalPrice = $oggetto->getData('final_price');

        $finalPrice += $this->getTotalConfigurableItemsPrice($oggetto, $finalPrice);
        $finalPrice += $this->_applyOptionsPrice($oggetto, $qty, $basePrice) - $basePrice;
        $finalPrice = max(0, $finalPrice);

        $oggetto->setFinalPrice($finalPrice);
        return $finalPrice;
    }

    /**
     * Get Total price for configurable items
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param float $finalPrice
     * @return float
     */
    public function getTotalConfigurableItemsPrice($oggetto, $finalPrice)
    {
        $price = 0.0;

        $oggetto->getTypeInstance(true)
                ->setStoreFilter($oggetto->getStore(), $oggetto);
        $attributes = $oggetto->getTypeInstance(true)
                ->getConfigurableAttributes($oggetto);

        $selectedAttributes = array();
        if ($oggetto->getCustomOption('attributes')) {
            $selectedAttributes = unserialize($oggetto->getCustomOption('attributes')->getValue());
        }

        foreach ($attributes as $attribute) {
            $attributeId = $attribute->getOggettoAttribute()->getId();
            $value = $this->_getValueByIndex(
                $attribute->getPrices() ? $attribute->getPrices() : array(),
                isset($selectedAttributes[$attributeId]) ? $selectedAttributes[$attributeId] : null
            );
            $oggetto->setParentId(true);
            if ($value) {
                if ($value['pricing_value'] != 0) {
                    $oggetto->setConfigurablePrice($this->_calcSelectionPrice($value, $finalPrice));
                    Mage::dispatchEvent(
                        'score_oggetto_type_configurable_price',
                        array('oggetto' => $oggetto)
                    );
                    $price += $oggetto->getConfigurablePrice();
                }
            }
        }
        return $price;
    }

    /**
     * Calculate configurable oggetto selection price
     *
     * @param   array $priceInfo
     * @param   decimal $oggettoPrice
     * @return  decimal
     */
    protected function _calcSelectionPrice($priceInfo, $oggettoPrice)
    {
        if($priceInfo['is_percent']) {
            $ratio = $priceInfo['pricing_value']/100;
            $price = $oggettoPrice * $ratio;
        } else {
            $price = $priceInfo['pricing_value'];
        }
        return $price;
    }

    protected function _getValueByIndex($values, $index) {
        foreach ($values as $value) {
            if($value['value_index'] == $index) {
                return $value;
            }
        }
        return false;
    }
}
