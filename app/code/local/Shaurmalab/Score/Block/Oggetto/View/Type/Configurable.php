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
 * Score super oggetto configurable part block
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Oggetto_View_Type_Configurable extends Shaurmalab_Score_Block_Oggetto_View_Abstract
{
    /**
     * Prices
     *
     * @var array
     */
    protected $_prices      = array();

    /**
     * Prepared prices
     *
     * @var array
     */
    protected $_resPrices   = array();

    /**
     * Get allowed attributes
     *
     * @return array
     */
    public function getAllowAttributes()
    {
        return $this->getOggetto()->getTypeInstance(true)
            ->getConfigurableAttributes($this->getOggetto());
    }

    /**
     * Check if allowed attributes have options
     *
     * @return bool
     */
    public function hasOptions()
    {
        $attributes = $this->getAllowAttributes();
        if (count($attributes)) {
            foreach ($attributes as $attribute) {
                /** @var Shaurmalab_Score_Model_Oggetto_Type_Configurable_Attribute $attribute */
                if ($attribute->getData('prices')) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get Allowed Oggettos
     *
     * @return array
     */
    public function getAllowOggettos()
    {
        if (!$this->hasAllowOggettos()) {
            $oggettos = array();
            $skipSaleableCheck = Mage::helper('score/oggetto')->getSkipSaleableCheck();
            $allOggettos = $this->getOggetto()->getTypeInstance(true)
                ->getUsedOggettos(null, $this->getOggetto());
            foreach ($allOggettos as $oggetto) {
                if ($oggetto->isSaleable() || $skipSaleableCheck) {
                    $oggettos[] = $oggetto;
                }
            }
            $this->setAllowOggettos($oggettos);
        }
        return $this->getData('allow_oggettos');
    }

    /**
     * retrieve current store
     *
     * @return Mage_Core_Model_Store
     */
    public function getCurrentStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Returns additional values for js config, con be overriden by descedants
     *
     * @return array
     */
    protected function _getAdditionalConfig()
    {
        return array();
    }

    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $attributes = array();
        $options    = array();
        $store      = $this->getCurrentStore();
        $taxHelper  = Mage::helper('tax');
        $currentOggetto = $this->getOggetto();

        $preconfiguredFlag = $currentOggetto->hasPreconfiguredValues();
        if ($preconfiguredFlag) {
            $preconfiguredValues = $currentOggetto->getPreconfiguredValues();
            $defaultValues       = array();
        }

        foreach ($this->getAllowOggettos() as $oggetto) {
            $oggettoId  = $oggetto->getId();

            foreach ($this->getAllowAttributes() as $attribute) {
                $oggettoAttribute   = $attribute->getOggettoAttribute();
                $oggettoAttributeId = $oggettoAttribute->getId();
                $attributeValue     = $oggetto->getData($oggettoAttribute->getAttributeCode());
                if (!isset($options[$oggettoAttributeId])) {
                    $options[$oggettoAttributeId] = array();
                }

                if (!isset($options[$oggettoAttributeId][$attributeValue])) {
                    $options[$oggettoAttributeId][$attributeValue] = array();
                }
                $options[$oggettoAttributeId][$attributeValue][] = $oggettoId;
            }
        }

        $this->_resPrices = array(
            $this->_preparePrice($currentOggetto->getFinalPrice())
        );

        foreach ($this->getAllowAttributes() as $attribute) {
            $oggettoAttribute = $attribute->getOggettoAttribute();
            $attributeId = $oggettoAttribute->getId();
            $info = array(
               'id'        => $oggettoAttribute->getId(),
               'code'      => $oggettoAttribute->getAttributeCode(),
               'label'     => $attribute->getLabel(),
               'options'   => array()
            );

            $optionPrices = array();
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if(!$this->_validateAttributeValue($attributeId, $value, $options)) {
                        continue;
                    }
                    $currentOggetto->setConfigurablePrice(
                        $this->_preparePrice($value['pricing_value'], $value['is_percent'])
                    );
                    $currentOggetto->setParentId(true);
                    Mage::dispatchEvent(
                        'score_oggetto_type_configurable_price',
                        array('oggetto' => $currentOggetto)
                    );
                    $configurablePrice = $currentOggetto->getConfigurablePrice();

                    if (isset($options[$attributeId][$value['value_index']])) {
                        $oggettosIndex = $options[$attributeId][$value['value_index']];
                    } else {
                        $oggettosIndex = array();
                    }

                    $info['options'][] = array(
                        'id'        => $value['value_index'],
                        'label'     => $value['label'],
                        'price'     => $configurablePrice,
                        'oldPrice'  => $this->_prepareOldPrice($value['pricing_value'], $value['is_percent']),
                        'oggettos'  => $oggettosIndex,
                    );
                    $optionPrices[] = $configurablePrice;
                }
            }
            /**
             * Prepare formated values for options choose
             */
            foreach ($optionPrices as $optionPrice) {
                foreach ($optionPrices as $additional) {
                    $this->_preparePrice(abs($additional-$optionPrice));
                }
            }
            if($this->_validateAttributeInfo($info)) {
               $attributes[$attributeId] = $info;
            }

            // Add attribute default value (if set)
            if ($preconfiguredFlag) {
                $configValue = $preconfiguredValues->getData('super_attribute/' . $attributeId);
                if ($configValue) {
                    $defaultValues[$attributeId] = $configValue;
                }
            }
        }

        $taxCalculation = Mage::getSingleton('tax/calculation');
        if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
            $taxCalculation->setCustomer(Mage::registry('current_customer'));
        }

        $_request = $taxCalculation->getRateRequest(false, false, false);
        $_request->setOggettoClassId($currentOggetto->getTaxClassId());
        $defaultTax = $taxCalculation->getRate($_request);

        $_request = $taxCalculation->getRateRequest();
        $_request->setOggettoClassId($currentOggetto->getTaxClassId());
        $currentTax = $taxCalculation->getRate($_request);

        $taxConfig = array(
            'includeTax'        => $taxHelper->priceIncludesTax(),
            'showIncludeTax'    => $taxHelper->displayPriceIncludingTax(),
            'showBothPrices'    => $taxHelper->displayBothPrices(),
            'defaultTax'        => $defaultTax,
            'currentTax'        => $currentTax,
            'inclTaxTitle'      => Mage::helper('score')->__('Incl. Tax')
        );

        $config = array(
            'attributes'        => $attributes,
            'template'          => str_replace('%s', '#{price}', $store->getCurrentCurrency()->getOutputFormat()),
            'basePrice'         => $this->_registerJsPrice($this->_convertPrice($currentOggetto->getFinalPrice())),
            'oldPrice'          => $this->_registerJsPrice($this->_convertPrice($currentOggetto->getPrice())),
            'oggettoId'         => $currentOggetto->getId(),
            'chooseText'        => Mage::helper('score')->__('Choose an Option...'),
            'taxConfig'         => $taxConfig
        );

        if ($preconfiguredFlag && !empty($defaultValues)) {
            $config['defaultValues'] = $defaultValues;
        }

        $config = array_merge($config, $this->_getAdditionalConfig());

        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * Validating of super oggetto option value
     *
     * @param array $attributeId
     * @param array $value
     * @param array $options
     * @return boolean
     */
    protected function _validateAttributeValue($attributeId, &$value, &$options)
    {
        if(isset($options[$attributeId][$value['value_index']])) {
            return true;
        }

        return false;
    }

    /**
     * Validation of super oggetto option
     *
     * @param array $info
     * @return boolean
     */
    protected function _validateAttributeInfo(&$info)
    {
        if(count($info['options']) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Calculation real price
     *
     * @param float $price
     * @param bool $isPercent
     * @return mixed
     */
    protected function _preparePrice($price, $isPercent = false)
    {
        if ($isPercent && !empty($price)) {
            $price = $this->getOggetto()->getFinalPrice() * $price / 100;
        }

        return $this->_registerJsPrice($this->_convertPrice($price, true));
    }

    /**
     * Calculation price before special price
     *
     * @param float $price
     * @param bool $isPercent
     * @return mixed
     */
    protected function _prepareOldPrice($price, $isPercent = false)
    {
        if ($isPercent && !empty($price)) {
            $price = $this->getOggetto()->getPrice() * $price / 100;
        }

        return $this->_registerJsPrice($this->_convertPrice($price, true));
    }

    /**
     * Replace ',' on '.' for js
     *
     * @param float $price
     * @return string
     */
    protected function _registerJsPrice($price)
    {
        return str_replace(',', '.', $price);
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param float $price
     * @param boolean $round
     * @return float
     */
    protected function _convertPrice($price, $round = false)
    {
        if (empty($price)) {
            return 0;
        }

        $price = $this->getCurrentStore()->convertPrice($price);
        if ($round) {
            $price = $this->getCurrentStore()->roundPrice($price);
        }

        return $price;
    }
}
