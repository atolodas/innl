<?php

class Cafepress_CPCore_Model_Resource_Report_Royalty_Collection extends Mage_Reports_Model_Resource_Product_Collection
{
    /**
     * Initialize resources
     *
     */
    protected function _construct()
    {

        parent::_construct();
        $this->_useAnalyticFunction = true;
    }
    /**
     * Set Date range to collection
     *
     * @param int $from
     * @param int $to
     * @return Mage_Reports_Model_Resource_Product_Sold_Collection
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->addAttributeToSelect('*')
           // ->addOrderedQty($from, $to)
            ->addRefundedQty($from, $to)
           // ->setOrder('ordered_qty', self::SORT_ORDER_DESC)
        ;

        return $this;
    }

    /**
     * Add ordered qty's
     *
     * @param string $from
     * @param string $to
     * @return Mage_Reports_Model_Resource_Product_Collection
     */
    public function addRefundedQty($from = '', $to = '')
    {
        $adapter              = $this->getConnection();
        $compositeTypeIds     = Mage::getSingleton('catalog/product_type')->getCompositeTypes();
        $orderTableAliasName  = $adapter->quoteIdentifier('order');

        $orderJoinCondition   = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state IN ('".Mage_Sales_Model_Order::STATE_COMPLETE."','".Mage_Sales_Model_Order::STATE_CLOSED."')"),

        );

        $productJoinCondition = array(
            $adapter->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
            'e.entity_id = order_items.product_id',
            $adapter->quoteInto('e.entity_type_id = ?', $this->getProductEntityTypeId())
        );

        if ($from != '' && $to != '') {
            $fieldName            = $orderTableAliasName . '.created_at';
            $orderJoinCondition[] = $this->_prepareBetweenSql($fieldName, $from, $to);
        }

        $this->getSelect()
            ->reset()
            ->from(
            array('order_items' => $this->getTable('sales/order_item')),
            array(
                'qty_ordered' => 'SUM(order_items.qty_ordered)',
                'qty_refunded' => 'SUM(order_items.qty_refunded)',
                'amount_refunded' => 'SUM(order_items.qty_refunded * order_items.price / order_items.qty_ordered)',
                'product_price' =>'SUM(order_items.row_total)/SUM(order_items.qty_ordered)',
                'row_total' => 'SUM(order_items.row_total)',
                'net_sales' => 'SUM(order_items.row_total)-SUM(order_items.qty_refunded * order_items.row_total / order_items.qty_ordered)'
            ))
            ->joinInner(
            array('order' => $this->getTable('sales/order')),
            implode(' AND ', $orderJoinCondition),
            array())
            ->joinLeft(
            array('e' => $this->getProductEntityTableName()),
            implode(' AND ', $productJoinCondition),
            array(
                'entity_id' => 'order_items.product_id',
                'entity_type_id' => 'e.entity_type_id',
                'attribute_set_id' => 'e.attribute_set_id',
                'type_id' => 'e.type_id',
                'sku' => 'order_items.sku',
                'name' => 'order_items.name',
                'has_options' => 'e.has_options',
                'required_options' => 'e.required_options',
                'created_at' => 'e.created_at',
                'updated_at' => 'e.updated_at'
            ))
            ->where('parent_item_id IS NULL')
            ->group('order_items.product_id')
    //        ->having('SUM(order_items.qty_refunded) > ?', 0)
    ;
        return $this;
    }


    /**
     * Set store filter to collection
     *
     * @param array $storeIds
     * @return Mage_Reports_Model_Resource_Product_Sold_Collection
     */
    public function setStoreIds($storeIds)
    {
        if ($storeIds) {
            $this->getSelect()->where('order_items.store_id IN (?)', (array)$storeIds);
        }
        return $this;
    }

    /**
     * Add website product limitation
     *
     * @return Mage_Reports_Model_Resource_Product_Sold_Collection
     */
    protected function _productLimitationJoinWebsite()
    {
        $filters     = $this->_productLimitationFilters;
        $conditions  = array('product_website.product_id=e.entity_id');
        if (isset($filters['website_ids'])) {
            $conditions[] = $this->getConnection()
                ->quoteInto('product_website.website_id IN(?)', $filters['website_ids']);

            $subQuery = $this->getConnection()->select()
                ->from(array('product_website' => $this->getTable('catalog/product_website')),
                array('product_website.product_id')
            )
                ->where(join(' AND ', $conditions));
            $this->getSelect()->where('e.entity_id IN( '.$subQuery.' )');
        }

        return $this;
    }
}
