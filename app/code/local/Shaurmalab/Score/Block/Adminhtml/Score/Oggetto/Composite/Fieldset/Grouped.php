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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml block for fieldset of grouped entity
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Composite_Fieldset_Grouped extends Shaurmalab_Score_Block_Oggetto_View_Type_Grouped
{
    /**
     * Redefine default price block
     * Set current customer to tax calculation
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_block = 'score/adminhtml_score_oggetto_price';
        $this->_useLinkForAsLowAs = false;

        $taxCalculation = Mage::getSingleton('tax/calculation');
        if (!$taxCalculation->getCustomer() && Mage::registry('current_customer')) {
            $taxCalculation->setCustomer(Mage::registry('current_customer'));
        }
    }

    /**
     * Retrieve entity
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggetto()
    {
        if (!$this->hasData('entity')) {
            $this->setData('entity', Mage::registry('entity'));
        }
        $entity = $this->getData('entity');
        if (is_null($entity->getTypeInstance(true)->getStoreFilter($entity))) {
            $entity->getTypeInstance(true)->setStoreFilter(Mage::app()->getStore($entity->getStoreId()), $entity);
        }

        return $entity;
    }

    /**
     * Retrieve array of associated entitys
     *
     * @return array
     */
    public function getAssociatedOggettos()
    {
        $entity = $this->getOggetto();
        $result = $entity->getTypeInstance(true)
            ->getAssociatedOggettos($entity);

        $storeId = $entity->getStoreId();
        foreach ($result as $item) {
            $item->setStoreId($storeId);
        }

        return $result;
    }


    /**
     * Set preconfigured values to grouped associated entitys
     *
     * @return Shaurmalab_Score_Block_Oggetto_View_Type_Grouped
     */
    public function setPreconfiguredValue() {
        $configValues = $this->getOggetto()->getPreconfiguredValues()->getSuperGroup();
        if (is_array($configValues)) {
            $associatedOggettos = $this->getAssociatedOggettos();
            foreach ($associatedOggettos as $item) {
                if (isset($configValues[$item->getId()])) {
                    $item->setQty($configValues[$item->getId()]);
                }
            }
        }
        return $this;
    }

    /**
     * Check whether the price can be shown for the specified entity
     *
     * @param Shaurmalab_Score_Model_Oggetto $entity
     * @return bool
     */
    public function getCanShowOggettoPrice($entity)
    {
        return true;
    }

    /**
     * Checks whether block is last fieldset in popup
     *
     * @return bool
     */
    public function getIsLastFieldset()
    {
        $isLast = $this->getData('is_last_fieldset');
        if (!$isLast) {
            $options = $this->getOggetto()->getOptions();
            return !$options || !count($options);
        }
        return $isLast;
    }

    /**
     * Returns price converted to current currency rate
     *
     * @param float $price
     * @return float
     */
    public function getCurrencyPrice($price)
    {
        $store = $this->getOggetto()->getStore();
        return $this->helper('core')->currencyByStore($price, $store, false);
    }
}
