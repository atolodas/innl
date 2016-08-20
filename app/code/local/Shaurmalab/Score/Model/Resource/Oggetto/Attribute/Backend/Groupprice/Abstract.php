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
 * Score oggetto abstract price backend attribute model with customer group specific
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Shaurmalab_Score_Model_Resource_Oggetto_Attribute_Backend_Groupprice_Abstract
    extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Load Tier Prices for oggetto
     *
     * @param int $oggettoId
     * @param int $websiteId
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Attribute_Backend_Tierprice
     */
    public function loadPriceData($oggettoId, $websiteId = null)
    {
        $adapter = $this->_getReadAdapter();

        $columns = array(
            'price_id'      => $this->getIdFieldName(),
            'website_id'    => 'website_id',
            'all_groups'    => 'all_groups',
            'cust_group'    => 'customer_group_id',
            'price'         => 'value',
        );

        $columns = $this->_loadPriceDataColumns($columns);

        $select  = $adapter->select()
            ->from($this->getMainTable(), $columns)
            ->where('entity_id=?', $oggettoId);

        $this->_loadPriceDataSelect($select);

        if (!is_null($websiteId)) {
            if ($websiteId == '0') {
                $select->where('website_id = ?', $websiteId);
            } else {
                $select->where('website_id IN(?)', array(0, $websiteId));
            }
        }

        return $adapter->fetchAll($select);
    }

    /**
     * Load specific sql columns
     *
     * @param array $columns
     * @return array
     */
    protected function _loadPriceDataColumns($columns)
    {
        return $columns;
    }

    /**
     * Load specific db-select data
     *
     * @param Varien_Db_Select $select
     * @return Varien_Db_Select
     */
    protected function _loadPriceDataSelect($select)
    {
        return $select;
    }

    /**
     * Delete Tier Prices for oggetto
     *
     * @param int $oggettoId
     * @param int $websiteId
     * @param int $priceId
     * @return int The number of affected rows
     */
    public function deletePriceData($oggettoId, $websiteId = null, $priceId = null)
    {
        $adapter = $this->_getWriteAdapter();

        $conds   = array(
            $adapter->quoteInto('entity_id = ?', $oggettoId)
        );

        if (!is_null($websiteId)) {
            $conds[] = $adapter->quoteInto('website_id = ?', $websiteId);
        }

        if (!is_null($priceId)) {
            $conds[] = $adapter->quoteInto($this->getIdFieldName() . ' = ?', $priceId);
        }

        $where = implode(' AND ', $conds);

        return $adapter->delete($this->getMainTable(), $where);
    }

    /**
     * Save tier price object
     *
     * @param Varien_Object $priceObject
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Attribute_Backend_Tierprice
     */
    public function savePriceData(Varien_Object $priceObject)
    {
        $adapter = $this->_getWriteAdapter();
        $data    = $this->_prepareDataForTable($priceObject, $this->getMainTable());

        if (!empty($data[$this->getIdFieldName()])) {
            $where = $adapter->quoteInto($this->getIdFieldName() . ' = ?', $data[$this->getIdFieldName()]);
            unset($data[$this->getIdFieldName()]);
            $adapter->update($this->getMainTable(), $data, $where);
        } else {
            $adapter->insert($this->getMainTable(), $data);
        }
        return $this;
    }
}
