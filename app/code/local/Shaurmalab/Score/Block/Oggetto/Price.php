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
 * Oggetto price block
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 */
class Shaurmalab_Score_Block_Oggetto_Price extends Shaurmalab_Score_Block_Oggetto_Abstract
{
    protected $_priceDisplayType = null;
    protected $_idSuffix = '';

    /**
     * Retrieve oggetto
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggetto()
    {
        $oggetto = $this->_getData('oggetto');
        if (!$oggetto) {
            $oggetto = Mage::registry('oggetto');
        }
        return $oggetto;
    }

    public function getDisplayMinimalPrice()
    {
        return $this->_getData('display_minimal_price');
    }

    public function setIdSuffix($idSuffix)
    {
        $this->_idSuffix = $idSuffix;
        return $this;
    }

    public function getIdSuffix()
    {
        return $this->_idSuffix;
    }

    /**
     * Get tier prices (formatted)
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getTierPrices($oggetto = null)
    {
        if (is_null($oggetto)) {
            $oggetto = $this->getOggetto();
        }
        $prices = $oggetto->getFormatedTierPrice();

        $res = array();
        if (is_array($prices)) {
            foreach ($prices as $price) {
                $price['price_qty'] = $price['price_qty'] * 1;

                $oggettoPrice = $oggetto->getPrice();
                if ($oggetto->getPrice() != $oggetto->getFinalPrice()) {
                    $oggettoPrice = $oggetto->getFinalPrice();
                }

                // Group price must be used for percent calculation if it is lower
                $groupPrice = $oggetto->getGroupPrice();
                if ($oggettoPrice > $groupPrice) {
                    $oggettoPrice = $groupPrice;
                }

                if ($price['price'] < $oggettoPrice) {
                    $price['savePercent'] = ceil(100 - ((100 / $oggettoPrice) * $price['price']));

                    $tierPrice = Mage::app()->getStore()->convertPrice(
                        Mage::helper('tax')->getPrice($oggetto, $price['website_price'])
                    );
                    $price['formated_price'] = Mage::app()->getStore()->formatPrice($tierPrice);
                    $price['formated_price_incl_tax'] = Mage::app()->getStore()->formatPrice(
                        Mage::app()->getStore()->convertPrice(
                            Mage::helper('tax')->getPrice($oggetto, $price['website_price'], true)
                        )
                    );

                    if (Mage::helper('score')->canApplyMsrp($oggetto)) {
                        $oldPrice = $oggetto->getFinalPrice();
                        $oggetto->setPriceCalculation(false);
                        $oggetto->setPrice($tierPrice);
                        $oggetto->setFinalPrice($tierPrice);

                        $this->getLayout()->getBlock('oggetto.info')->getPriceHtml($oggetto);
                        $oggetto->setPriceCalculation(true);

                        $price['real_price_html'] = $oggetto->getRealPriceHtml();
                        $oggetto->setFinalPrice($oldPrice);
                    }

                    $res[] = $price;
                }
            }
        }

        return $res;
    }

    /**
     * Retrieve url for direct adding oggetto to cart
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($oggetto, $additional = array())
    {
        return $this->helper('checkout/cart')->getAddUrl($oggetto, $additional);
    }

    /**
     * Prevent displaying if the price is not available
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->getOggetto() || $this->getOggetto()->getCanShowPrice() === false) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Get Oggetto Price valid JS string
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return string
     */
    public function getRealPriceJs($oggetto)
    {
        $html = $this->hasRealPriceHtml() ? $this->getRealPriceHtml() : $oggetto->getRealPriceHtml();
        return Mage::helper('core')->jsonEncode($html);
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(parent::getCacheTags(), $this->getOggetto()->getCacheIdTags());
    }

    /**
     * Retrieve attribute instance by name, id or config node
     *
     * If attribute is not found false is returned
     *
     * @param string|integer|Mage_Core_Model_Config_Element $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Abstract || false
     */
    public function getOggettoAttribute($attribute)
    {
        return $this->getOggetto()->getResource()->getAttribute($attribute);
    }
}
