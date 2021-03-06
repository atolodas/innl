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
 * Score Layer Decimal attribute Filter Resource Model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Layer_Filter_Decimal extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table name
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_index_eav_decimal', 'entity_id');
    }

    /**
     * Apply attribute filter to oggetto collection
     *
     * @param Shaurmalab_Score_Model_Layer_Filter_Decimal $filter
     * @param float $range
     * @param int $index
     * @return Shaurmalab_Score_Model_Resource_Layer_Filter_Decimal
     */
    public function applyFilterToCollection($filter, $range, $index)
    {
        $collection = $filter->getLayer()->getOggettoCollection();
        $attribute  = $filter->getAttributeModel();
        $connection = $this->_getReadAdapter();
        $tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());
        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId())
        );

        $collection->getSelect()->join(
            array($tableAlias => $this->getMainTable()),
            implode(' AND ', $conditions),
            array()
        );

        $collection->getSelect()
            ->where("{$tableAlias}.value >= ?", ($range * ($index - 1)))
            ->where("{$tableAlias}.value < ?", ($range * $index));

        return $this;
    }

    /**
     * Retrieve array of minimal and maximal values
     *
     * @param Shaurmalab_Score_Model_Layer_Filter_Decimal $filter
     * @return array
     */
    public function getMinMax($filter)
    {
        $select     = $this->_getSelect($filter);
        $adapter    = $this->_getReadAdapter();

        $select->columns(array(
            'min_value' => new Zend_Db_Expr('MIN(decimal_index.value)'),
            'max_value' => new Zend_Db_Expr('MAX(decimal_index.value)'),
        ));

        $result     = $adapter->fetchRow($select);

        return array($result['min_value'], $result['max_value']);
    }

    /**
     * Retrieve clean select with joined index table
     * Joined table has index
     *
     * @param Shaurmalab_Score_Model_Layer_Filter_Decimal $filter
     * @return Varien_Db_Select
     */
    protected function _getSelect($filter)
    {
        $collection = $filter->getLayer()->getOggettoCollection();

        // clone select from collection with filters
        $select = clone $collection->getSelect();
        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $attributeId = $filter->getAttributeModel()->getId();
        $storeId     = $collection->getStoreId();

        $select->join(
            array('decimal_index' => $this->getMainTable()),
            'e.entity_id = decimal_index.entity_id'.
            ' AND ' . $this->_getReadAdapter()->quoteInto('decimal_index.attribute_id = ?', $attributeId) .
            ' AND ' . $this->_getReadAdapter()->quoteInto('decimal_index.store_id = ?', $storeId),
            array()
        );

        return $select;
    }

    /**
     * Retrieve array with oggettos counts per range
     *
     * @param Shaurmalab_Score_Model_Layer_Filter_Decimal $filter
     * @param int $range
     * @return array
     */
    public function getCount($filter, $range)
    {
        $select     = $this->_getSelect($filter);
        $adapter    = $this->_getReadAdapter();

        $countExpr  = new Zend_Db_Expr("COUNT(*)");
        $rangeExpr  = new Zend_Db_Expr("FLOOR(decimal_index.value / {$range}) + 1");

        $select->columns(array(
            'decimal_range' => $rangeExpr,
            'count' => $countExpr
        ));
        $select->group($rangeExpr);

        return $adapter->fetchPairs($select);
    }
}
