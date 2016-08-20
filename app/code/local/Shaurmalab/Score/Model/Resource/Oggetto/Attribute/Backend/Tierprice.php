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
 * Score oggetto tier price backend attribute model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Oggetto_Attribute_Backend_Tierprice
    extends Shaurmalab_Score_Model_Resource_Oggetto_Attribute_Backend_Groupprice_Abstract
{
    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_attribute_tier_price', 'value_id');
    }

    /**
     * Add qty column
     *
     * @param array $columns
     * @return array
     */
    protected function _loadPriceDataColumns($columns)
    {
        $columns = parent::_loadPriceDataColumns($columns);
        $columns['price_qty'] = 'qty';
        return $columns;
    }

    /**
     * Order by qty
     *
     * @param Varien_Db_Select $select
     * @return Varien_Db_Select
     */
    protected function _loadPriceDataSelect($select)
    {
        $select->order('qty');
        return $select;
    }

    /**
     * Load oggetto tier prices
     *
     * @deprecated since 1.3.2.3
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param Shaurmalab_Score_Model_Resource_Eav_Attribute $attribute
     * @return array
     */
    public function loadOggettoPrices($oggetto, $attribute)
    {
        $websiteId = null;
        if ($attribute->isScopeGlobal()) {
            $websiteId = 0;
        } else if ($oggetto->getStoreId()) {
            $websiteId = Mage::app()->getStore($oggetto->getStoreId())->getWebsiteId();
        }

        return $this->loadPriceData($oggetto->getId(), $websiteId);
    }

    /**
     * Delete oggetto tier price data from storage
     *
     * @deprecated since 1.3.2.3
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param Shaurmalab_Score_Model_Resource_Eav_Attribute $attribute
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Attribute_Backend_Tierprice
     */
    public function deleteOggettoPrices($oggetto, $attribute)
    {
        $websiteId = null;
        if (!$attribute->isScopeGlobal()) {
            $storeId = $oggetto->getOggettoId();
            if ($storeId) {
                $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
            }
        }

        $this->deletePriceData($oggetto->getId(), $websiteId);

        return $this;
    }

    /**
     * Insert oggetto Tier Price to storage
     *
     * @deprecated since 1.3.2.3
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param array $data
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Attribute_Backend_Tierprice
     */
    public function insertOggettoPrice($oggetto, $data)
    {
        $priceObject = new Varien_Object($data);
        $priceObject->setEntityId($oggetto->getId());

        return $this->savePriceData($priceObject);
    }
}
