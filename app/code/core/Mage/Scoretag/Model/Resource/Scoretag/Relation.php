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
 * Scoretag Relation resource model
 *
 * @category    Mage
 * @package     Mage_Scoretag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Scoretag_Model_Resource_Scoretag_Relation extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource connection and define table resource
     *
     */
    protected function _construct()
    {
        $this->_init('scoretag/relation', 'scoretag_relation_id');
    }

    /**
     * Load by Scoretag and Customer
     *
     * @param Mage_Scoretag_Model_Scoretag_Relation $model
     * @return Mage_Scoretag_Model_Resource_Scoretag_Relation
     */
    public function loadByScoretagCustomer($model)
    {
        if ($model->getScoretagId() && $model->getCustomerId()) {
            $read = $this->_getReadAdapter();
            $bind = array(
                'scoretag_id'      => $model->getScoretagId(),
                'customer_id' => $model->getCustomerId()
            );

            $select = $read->select()
                ->from($this->getMainTable())
                ->join(
                    $this->getTable('scoretag/scoretag'),
                    $this->getTable('scoretag/scoretag') . '.scoretag_id = ' . $this->getMainTable() . '.scoretag_id'
                )
                ->where($this->getMainTable() . '.scoretag_id = :scoretag_id')
                ->where('customer_id = :customer_id');

            if ($model->getOggettoId()) {
                $select->where($this->getMainTable() . '.oggetto_id = :oggetto_id');
                $bind['oggetto_id'] = $model->getOggettoId();
            }

            if ($model->hasStoreId()) {
                $select->where($this->getMainTable() . '.store_id = :sore_id');
                $bind['sore_id'] = $model->getStoreId();
            }
            $data = $read->fetchRow($select, $bind);
            $model->setData(( is_array($data) ) ? $data : array());
        }

        return $this;
    }

    /**
     * Retrieve Scoretagged Oggettos
     *
     * @param Mage_Scoretag_Model_Scoretag_Relation $model
     * @return array
     */
    public function getOggettoIds($model)
    {
        $bind = array(
            'scoretag_id' => $model->getScoretagId()
        );
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'oggetto_id')
            ->where($this->getMainTable() . '.scoretag_id=:scoretag_id');

        if (!is_null($model->getCustomerId())) {
            $select->where($this->getMainTable() . '.customer_id= :customer_id');
            $bind['customer_id'] = $model->getCustomerId();
        }

        if ($model->hasStoreId()) {
            $select->where($this->getMainTable() . '.store_id = :store_id');
            $bind['store_id'] = $model->getStoreId();
        }

        if (!is_null($model->getStatusFilter())) {
            $select->join(
                $this->getTable('scoretag/scoretag'),
                $this->getTable('scoretag/scoretag') . '.scoretag_id = ' . $this->getMainTable() . '.scoretag_id'
            )
            ->where($this->getTable('scoretag/scoretag') . '.status = :t_status');
            $bind['t_status'] = $model->getStatusFilter();
        }

        return $this->_getReadAdapter()->fetchCol($select, $bind);
    }

    /**
     * Retrieve related to oggetto scoretag ids
     *
     * @param Mage_Scoretag_Model_Scoretag_Relation $model
     * @return array
     */
    public function getRelatedScoretagIds($model)
    {
        $oggettoIds = (is_array($model->getOggettoId())) ? $model->getOggettoId() : array($model->getOggettoId());
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), 'scoretag_id')
            ->where("oggetto_id IN(?)", $oggettoIds)
            ->order('scoretag_id');
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Deactivate scoretag relations by scoretag and customer
     *
     * @param int $scoretagId
     * @param int $customerId
     * @return Mage_Scoretag_Model_Resource_Scoretag_Relation
     */
    public function deactivate($scoretagId, $customerId)
    {
        $condition = array(
            'scoretag_id = ?'      => $scoretagId,
            'customer_id = ?' => $customerId
        );

        $data = array('active' => Mage_Scoretag_Model_Scoretag_Relation::STATUS_NOT_ACTIVE);
        $this->_getWriteAdapter()->update($this->getMainTable(), $data, $condition);
        return $this;
    }

    /**
     * Add TAG to PRODUCT relations
     *
     * @param Mage_Scoretag_Model_Scoretag_Relation $model
     * @return Mage_Scoretag_Model_Resource_Scoretag_Relation
     */
    public function addRelations($model)
    {
        $addedIds = $model->getAddedOggettoIds();

        $bind = array(
            'scoretag_id'   => $model->getScoretagId(),
            'store_id' => $model->getStoreId()
        );
        $write = $this->_getWriteAdapter();

        $select = $write->select()
            ->from($this->getMainTable(), 'oggetto_id')
            ->where('scoretag_id = :scoretag_id')
            ->where('store_id = :store_id');
        $oldRelationIds = $write->fetchCol($select, $bind);

        $insert = array_diff($addedIds, $oldRelationIds);
        $delete = array_diff($oldRelationIds, $addedIds);

        if (!empty($insert)) {
            $insertData = array();
            foreach ($insert as $value) {
                $insertData[] = array(
                    'scoretag_id'        => $model->getScoretagId(),
                    'store_id'      => $model->getStoreId(),
                    'oggetto_id'    => $value,
                    'customer_id'   => $model->getCustomerId(),
                    'created_at'    => $this->formatDate(time())
                );
            }
            $write->insertMultiple($this->getMainTable(), $insertData);
        }

        if (!empty($delete)) {
            $write->delete($this->getMainTable(), array(
                'oggetto_id IN (?)' => $delete,
                'store_id = ?'      => $model->getStoreId(),
            ));
        }

        return $this;
    }
}
