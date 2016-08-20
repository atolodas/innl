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
 * Configurable Oggettos Price Indexer Resource model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Price_Grouped
    extends Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Price_Default
{
    /**
     * Reindex temporary (price result data) for all oggettos
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Price_Grouped
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->beginTransaction();
        try {
            $this->_prepareGroupedOggettoPriceData();
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Reindex temporary (price result data) for defined oggetto(s)
     *
     * @param int|array $entityIds
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Price_Grouped
     */
    public function reindexEntity($entityIds)
    {
        $this->_prepareGroupedOggettoPriceData($entityIds);

        return $this;
    }

    /**
     * Calculate minimal and maximal prices for Grouped oggettos
     * Use calculated price for relation oggettos
     *
     * @param int|array $entityIds  the parent entity ids limitation
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Price_Grouped
     */
    protected function _prepareGroupedOggettoPriceData($entityIds = null)
    {
        $write = $this->_getWriteAdapter();
        $table = $this->getIdxTable();

        $select = $write->select()
            ->from(array('e' => $this->getTable('score/oggetto')), 'entity_id')
            ->joinLeft(
                array('l' => $this->getTable('score/oggetto_link')),
                'e.entity_id = l.oggetto_id AND l.link_type_id=' . Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_GROUPED,
                array())
            ->join(
                array('cg' => $this->getTable('customer/customer_group')),
                '',
                array('customer_group_id'));
        $this->_addWebsiteJoinToSelect($select, true);
        $this->_addOggettoWebsiteJoinToSelect($select, 'cw.website_id', 'e.entity_id');
        $minCheckSql = $write->getCheckSql('le.required_options = 0', 'i.min_price', 0);
        $maxCheckSql = $write->getCheckSql('le.required_options = 0', 'i.max_price', 0);
        $select->columns('website_id', 'cw')
            ->joinLeft(
                array('le' => $this->getTable('score/oggetto')),
                'le.entity_id = l.linked_oggetto_id',
                array())
            ->joinLeft(
                array('i' => $table),
                'i.entity_id = l.linked_oggetto_id AND i.website_id = cw.website_id'
                    . ' AND i.customer_group_id = cg.customer_group_id',
                array(
                    'tax_class_id' => $this->_getReadAdapter()
                        ->getCheckSql('MIN(i.tax_class_id) IS NULL', '0', 'MIN(i.tax_class_id)'),
                    'price'        => new Zend_Db_Expr('NULL'),
                    'final_price'  => new Zend_Db_Expr('NULL'),
                    'min_price'    => new Zend_Db_Expr('MIN(' . $minCheckSql . ')'),
                    'max_price'    => new Zend_Db_Expr('MAX(' . $maxCheckSql . ')'),
                    'tier_price'   => new Zend_Db_Expr('NULL'),
                    'group_price'  => new Zend_Db_Expr('NULL'),
                ))
            ->group(array('e.entity_id', 'cg.customer_group_id', 'cw.website_id'))
            ->where('e.type_id=?', $this->getTypeId());

        if (!is_null($entityIds)) {
            $select->where('l.oggetto_id IN(?)', $entityIds);
        }

        /**
         * Add additional external limitation
         */
        Mage::dispatchEvent('score_oggetto_prepare_index_select', array(
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('e.entity_id'),
            'website_field' => new Zend_Db_Expr('cw.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id')
        ));

        $query = $select->insertFromSelect($table);
        $write->query($query);

        return $this;
    }
}
