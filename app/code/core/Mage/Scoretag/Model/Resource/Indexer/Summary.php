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
 * @package     Mage_Scoretag
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Scoretag Indexer Model
 *
 * @category    Mage
 * @package     Mage_Scoretag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Scoretag_Model_Resource_Indexer_Summary extends Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('scoretag/summary', 'scoretag_id');
    }

    /**
     * Process scoretag save
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Scoretag_Model_Resource_Indexer_Summary
     */
    public function scoretagSave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['scoretag_reindex_scoretag_id'])) {
            return $this;
        }
        return $this->aggregate($data['scoretag_reindex_scoretag_id']);
    }

    /**
     * Process scoretag relation save
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Scoretag_Model_Resource_Indexer_Summary
     */
    public function scoretagRelationSave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['scoretag_reindex_scoretag_id'])) {
            return $this;
        }
        return $this->aggregate($data['scoretag_reindex_scoretag_id']);
    }

    /**
     * Process oggetto save.
     * Method is responsible for index support when oggetto was saved.
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Scoretag_Model_Resource_Indexer_Summary
     */
    public function scoreOggettoSave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['scoretag_reindex_required'])) {
            return $this;
        }

        $scoretagIds = Mage::getModel('scoretag/scoretag_relation')
            ->setOggettoId($event->getEntityPk())
            ->getRelatedScoretagIds();

        return $this->aggregate($scoretagIds);
    }

    /**
     * Process oggetto delete.
     * Method is responsible for index support when oggetto was deleted
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Scoretag_Model_Resource_Indexer_Summary
     */
    public function scoreOggettoDelete(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['scoretag_reindex_scoretag_ids'])) {
            return $this;
        }
        return $this->aggregate($data['scoretag_reindex_scoretag_ids']);
    }

    /**
     * Process oggetto massaction
     *
     * @param Mage_Index_Model_Event $event
     * @return Mage_Scoretag_Model_Resource_Indexer_Summary
     */
    public function scoreOggettoMassAction(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['scoretag_reindex_scoretag_ids'])) {
            return $this;
        }
        return $this->aggregate($data['scoretag_reindex_scoretag_ids']);
    }

    /**
     * Reindex all scoretags
     *
     * @return Mage_Scoretag_Model_Resource_Indexer_Summary
     */
    public function reindexAll()
    {
        return $this->aggregate();
    }

    /**
     * Aggregate scoretags by specified ids
     *
     * @param null|int|array $scoretagIds
     * @return Mage_Scoretag_Model_Resource_Indexer_Summary
     */
    public function aggregate($scoretagIds = null)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $this->beginTransaction();

        try {
            if (!empty($scoretagIds)) {
                $writeAdapter->delete(
                    $this->getTable('scoretag/summary'), array('scoretag_id IN(?)' => $scoretagIds)
                );
            } else {
                $writeAdapter->delete($this->getTable('scoretag/summary'));
            }

            $select = $writeAdapter->select()
                ->from(
                    array('tr' => $this->getTable('scoretag/relation')),
                    array(
                        'tr.scoretag_id',
                        'tr.store_id',
                        'customers'         => 'COUNT(DISTINCT tr.customer_id)',
                        'oggettos'          => 'COUNT(DISTINCT tr.oggetto_id)',
                        'popularity'        => 'COUNT(tr.customer_id) + MIN('
                            . $writeAdapter->getCheckSql(
                                'tp.base_popularity IS NOT NULL',
                                'tp.base_popularity',
                                '0'
                                )
                            . ')',
                        'uses'              => new Zend_Db_Expr(0), // deprecated since 1.4.0.1
                        'historical_uses'   => new Zend_Db_Expr(0), // deprecated since 1.4.0.1
                        'base_popularity'   => new Zend_Db_Expr(0)  // deprecated since 1.4.0.1
                    )
                )
                ->joinInner(
                    array('cs' => $this->getTable('core/store')),
                    'cs.store_id = tr.store_id',
                    array()
                )
                ->joinInner(
                    array('pw' => $this->getTable('score/oggetto_website')),
                    'cs.website_id = pw.website_id AND tr.oggetto_id = pw.oggetto_id',
                    array()
                )
                ->joinInner(
                    array('e' => $this->getTable('score/oggetto')),
                    'tr.oggetto_id = e.entity_id',
                    array()
                )
                ->joinLeft(
                    array('tp' => $this->getTable('scoretag/properties')),
                    'tp.scoretag_id = tr.scoretag_id AND tp.store_id = tr.store_id',
                    array()
                )
                ->group(array(
                    'tr.scoretag_id',
                    'tr.store_id'
                ))
                ->where('tr.active = 1');

            $statusCond = $writeAdapter->quoteInto('=?', Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED);
            $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id', $statusCond);

            $visibilityCond = $writeAdapter
                ->quoteInto('!=?', Shaurmalab_Score_Model_Oggetto_Visibility::VISIBILITY_NOT_VISIBLE);
            $this->_addAttributeToSelect($select, 'visibility', 'e.entity_id', 'cs.store_id', $visibilityCond);

            if (!empty($scoretagIds)) {
                $select->where('tr.scoretag_id IN(?)', $scoretagIds);
            }

            Mage::dispatchEvent('prepare_score_oggetto_index_select', array(
                'select'        => $select,
                'entity_field'  => new Zend_Db_Expr('e.entity_id'),
                'website_field' => new Zend_Db_Expr('cs.website_id'),
                'store_field'   => new Zend_Db_Expr('cs.store_id')
            ));

            $writeAdapter->query(
                $select->insertFromSelect($this->getTable('scoretag/summary'), array(
                    'scoretag_id',
                    'store_id',
                    'customers',
                    'oggettos',
                    'popularity',
                    'uses',            // deprecated since 1.4.0.1
                    'historical_uses', // deprecated since 1.4.0.1
                    'base_popularity'  // deprecated since 1.4.0.1
                ))
            );


            $selectedFields = array(
                'scoretag_id'            => 'scoretag_id',
                'store_id'          => new Zend_Db_Expr(0),
                'customers'         => 'COUNT(DISTINCT customer_id)',
                'oggettos'          => 'COUNT(DISTINCT oggetto_id)',
                'popularity'        => 'COUNT(customer_id)',
                'uses'              => new Zend_Db_Expr(0), // deprecated since 1.4.0.1
                'historical_uses'   => new Zend_Db_Expr(0), // deprecated since 1.4.0.1
                'base_popularity'   => new Zend_Db_Expr(0)  // deprecated since 1.4.0.1
            );

            $agregateSelect = $writeAdapter->select();
            $agregateSelect->from($this->getTable('scoretag/relation'), $selectedFields)
                ->group('scoretag_id')
                ->where('active = 1');

            if (!empty($scoretagIds)) {
                $agregateSelect->where('scoretag_id IN(?)', $scoretagIds);
            }

            $writeAdapter->query(
                $agregateSelect->insertFromSelect($this->getTable('scoretag/summary'), array_keys($selectedFields))
            );
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }
}
