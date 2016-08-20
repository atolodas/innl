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
 * Scoretag resourse model
 *
 * @category    Mage
 * @package     Mage_Scoretag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Scoretag_Model_Resource_Scoretag extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table and primary index
     *
     */
    protected function _construct()
    {
        $this->_init('scoretag/scoretag', 'scoretag_id');
    }

    /**
     * Initialize unique fields
     *
     * @return Mage_Scoretag_Model_Resource_Scoretag
     */
    protected function _initUniqueFields()
    {
        $this->_uniqueFields = array(array(
            'field' => 'name',
            'title' => Mage::helper('scoretag')->__('Scoretag')
        ));
        return $this;
    }

    /**
     * Loading scoretag by name
     *
     * @param Mage_Scoretag_Model_Scoretag $model
     * @param string $name
     * @return array|false
     */
    public function loadByName($model, $name)
    {
        if ( $name ) {
            $read = $this->_getReadAdapter();
            $select = $read->select();
            if (Mage::helper('core/string')->strlen($name) > 255) {
                $name = Mage::helper('core/string')->substr($name, 0, 255);
            }

            $select->from($this->getMainTable())
                ->where('name = :name');
            $data = $read->fetchRow($select, array('name' => $name));

            $model->setData(( is_array($data) ) ? $data : array());
        } else {
            return false;
        }
    }

    /**
     * Before saving actions
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Scoretag_Model_Resource_Scoretag
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId() && $object->getStatus() == $object->getApprovedStatus()) {
            $searchScoretag = new Varien_Object();
            $this->loadByName($searchScoretag, $object->getName());
            if ($searchScoretag->getData($this->getIdFieldName())
                    && $searchScoretag->getStatus() == $object->getPendingStatus()) {
                $object->setId($searchScoretag->getData($this->getIdFieldName()));
            }
        }

        if (Mage::helper('core/string')->strlen($object->getName()) > 255) {
            $object->setName(Mage::helper('core/string')->substr($object->getName(), 0, 255));
        }

        return parent::_beforeSave($object);
    }

    /**
     * Saving scoretag's base popularity
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getStore() || !Mage::app()->getStore()->isAdmin()) {
            return parent::_afterSave($object);
        }

        $scoretagId = ($object->isObjectNew()) ? $object->getScoretagId() : $object->getId();

        $writeAdapter = $this->_getWriteAdapter();
        $writeAdapter->insertOnDuplicate($this->getTable('scoretag/properties'), array(
            'scoretag_id'            => $scoretagId,
            'store_id'          => $object->getStore(),
            'base_popularity'   => (!$object->getBasePopularity()) ? 0 : $object->getBasePopularity()
        ));

        return parent::_afterSave($object);
    }

    /**
     * Getting base popularity per store view for specified scoretag
     *
     * @deprecated after 1.4.0.0
     *
     * @param int $scoretagId
     * @return array
     */
    protected function _getExistingBasePopularity($scoretagId)
    {
        $read = $this->_getReadAdapter();
        $selectSummary = $read->select()
            ->from(
                array('main' => $this->getTable('scoretag/summary')),
                array('store_id', 'base_popularity')
            )
            ->where('main.scoretag_id = :scoretag_id')
            ->where('main.store_id != 0');

        return $read->fetchAssoc($selectSummary, array('scoretag_id' => $scoretagId));
    }

    /**
     * Get aggregation data per store view
     *
     * @deprecated after 1.4.0.0
     *
     * @param int $scoretagId
     * @return array
     */
    protected function _getAggregationPerStoreView($scoretagId)
    {
        $readAdapter = $this->_getReadAdapter();
        $selectLocal = $readAdapter->select()
            ->from(
                array('main'  => $this->getTable('scoretag/relation')),
                array(
                    'customers' => 'COUNT(DISTINCT main.customer_id)',
                    'oggettos'  => 'COUNT(DISTINCT main.oggetto_id)',
                    'store_id',
                    'uses'      => 'COUNT(main.scoretag_relation_id)'
                )
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id = main.store_id AND store.store_id > 0',
                array()
            )
            ->join(array('oggetto_website' => $this->getTable('score/oggetto_website')),
                'oggetto_website.website_id = store.website_id AND oggetto_website.oggetto_id = main.oggetto_id',
                array()
            )
            ->where('main.scoretag_id = :scoretag_id')
            ->where('main.active = 1')
            ->group('main.store_id');

        $selectLocalResult = $readAdapter->fetchAll($selectLocal, array('scoretag_id' => $scoretagId));

        $selectHistorical = $readAdapter->select()
            ->from(
                array('main' => $this->getTable('scoretag/relation')),
                array('historical_uses' => 'COUNT(main.scoretag_relation_id)',
                'store_id')
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id = main.store_id AND store.store_id > 0',
                array()
            )
            ->join(array('oggetto_website' => $this->getTable('score/oggetto_website')),
                'oggetto_website.website_id = store.website_id AND oggetto_website.oggetto_id = main.oggetto_id',
                array()
            )
            ->group('main.store_id')
            ->where('main.scoretag_id = :scoretag_id')
            ->where('main.active = 1');

        $selectHistoricalResult = $readAdapter->fetchAll($selectHistorical, array('scoretag_id' => $scoretagId));

        foreach ($selectHistoricalResult as $historical) {
            foreach ($selectLocalResult as $key => $local) {
                if ($local['store_id'] == $historical['store_id']) {
                    $selectLocalResult[$key]['historical_uses'] = $historical['historical_uses'];
                    break;
                }
            }
        }

        return $selectLocalResult;
    }

    /**
     * Get global aggregation data for row with store_id = 0
     *
     * @deprecated after 1.4.0.0
     *
     * @param int $scoretagId
     * @return array
     */
    protected function _getGlobalAggregation($scoretagId)
    {
        $readAdapter = $this->_getReadAdapter();
        // customers and oggettos stats
        $selectGlobal = $readAdapter->select()
            ->from(
                array('main' => $this->getTable('scoretag/relation')),
                array(
                    'customers' => 'COUNT(DISTINCT main.customer_id)',
                    'oggettos'  => 'COUNT(DISTINCT main.oggetto_id)',
                    'store_id'  => new Zend_Db_Expr(0),
                    'uses'      => 'COUNT(main.scoretag_relation_id)'
                )
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id=main.store_id AND store.store_id>0',
                array()
            )
            ->join(array('oggetto_website' => $this->getTable('score/oggetto_website')),
                'oggetto_website.website_id = store.website_id AND oggetto_website.oggetto_id = main.oggetto_id',
                array()
            )
            ->where('main.scoretag_id = :scoretag_id')
            ->where('main.active = 1');
        $result = $readAdapter->fetchRow($selectGlobal, array('scoretag_id' => $scoretagId));
        if (!$result) {
            return array();
        }

        // historical uses stats
        $selectHistoricalGlobal = $readAdapter->select()
            ->from(
                array('main' => $this->getTable('scoretag/relation')),
                array('historical_uses' => 'COUNT(main.scoretag_relation_id)')
            )
            ->join(array('store' => $this->getTable('core/store')),
                'store.store_id = main.store_id AND store.store_id > 0',
                array()
            )
            ->join(array('oggetto_website' => $this->getTable('score/oggetto_website')),
                'oggetto_website.website_id = store.website_id AND oggetto_website.oggetto_id = main.oggetto_id',
                array()
            )
            ->where('main.scoretag_id = :scoretag_id')
            ->where('main.active = 1');
        $result['historical_uses'] = (int) $readAdapter->fetchOne($selectHistoricalGlobal, array('scoretag_id' => $scoretagId));

        return $result;
    }

    /**
     * Getting statistics data into buffer.
     * Replacing our buffer array with new statistics and incoming data.
     *
     * @deprecated after 1.4.0.0
     *
     * @param Mage_Scoretag_Model_Scoretag $object
     * @return Mage_Scoretag_Model_Scoretag
     */
    public function aggregate($object)
    {
        $scoretagId   = (int)$object->getId();
        $storeId = (int)$object->getStore();

        // create final summary from existing data and add specified base popularity
        $finalSummary = $this->_getExistingBasePopularity($scoretagId);
        if ($object->hasBasePopularity() && $storeId) {
            $finalSummary[$storeId]['store_id'] = $storeId;
            $finalSummary[$storeId]['base_popularity'] = $object->getBasePopularity();
        }

        // calculate aggregation data
        $summaries = $this->_getAggregationPerStoreView($scoretagId);
        $summariesGlobal = $this->_getGlobalAggregation($scoretagId);
        if ($summariesGlobal) {
            $summaries[] = $summariesGlobal;
        }

        // override final summary with aggregated data
        foreach ($summaries as $row) {
            $storeId = (int)$row['store_id'];
            foreach ($row as $key => $value) {
                $finalSummary[$storeId][$key] = $value;
            }
        }

        // prepare static parameters to final summary for insertion
        foreach ($finalSummary as $key => $row) {
            $finalSummary[$key]['scoretag_id'] = $scoretagId;
            foreach (array('base_popularity', 'popularity', 'historical_uses', 'uses', 'oggettos', 'customers') as $k) {
                if (!isset($row[$k])) {
                    $finalSummary[$key][$k] = 0;
                }
            }
            $finalSummary[$key]['popularity'] = $finalSummary[$key]['historical_uses'];
        }

        // remove old and insert new data
        $write = $this->_getWriteAdapter();
        $write->delete(
            $this->getTable('scoretag/summary'), array('scoretag_id = ?' => $scoretagId)
        );
        $write->insertMultiple($this->getTable('scoretag/summary'), $finalSummary);

        return $object;
    }

    /**
     * Decrementing scoretag oggettos quantity as action for oggetto delete
     *
     * @param array $scoretagsId
     * @return int The number of affected rows
     */
    public function decrementOggettos(array $scoretagsId)
    {
        $writeAdapter = $this->_getWriteAdapter();
        if (empty($scoretagsId)) {
            return 0;
        }

        return $writeAdapter->update(
            $this->getTable('scoretag/summary'),
            array('oggettos' => new Zend_Db_Expr('oggettos - 1')),
            array('scoretag_id IN (?)' => $scoretagsId)
        );
    }

    /**
     * Add summary data to specified object
     *
     * @deprecated after 1.4.0.0
     *
     * @param Mage_Scoretag_Model_Scoretag $object
     * @return Mage_Scoretag_Model_Scoretag
     */
    public function addSummary($object)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('relation' => $this->getTable('scoretag/relation')), array())
            ->joinLeft(
                array('summary' => $this->getTable('scoretag/summary')),
                'relation.scoretag_id = summary.scoretag_id AND relation.store_id = summary.store_id',
                array(
                    'customers',
                    'oggettos',
                    'popularity'
                )
            )
            ->where('relation.scoretag_id = :scoretag_id')
            ->where('relation.store_id = :store_id')
            ->limit(1);
        $bind = array(
            'scoretag_id' => (int)$object->getId(),
            'store_id' => (int)$object->getStoreId()
        );
        $row = $read->fetchRow($select, $bind);
        if ($row) {
            $object->addData($row);
        }
        return $object;
    }

    /**
     * Retrieve select object for load object data
     * Redeclare parent method just for adding scoretag's base popularity if flag exists
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Core_Model_Abstract $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getAddBasePopularity() && $object->hasStoreId()) {
            $select->joinLeft(
                array('properties' => $this->getTable('scoretag/properties')),
                "properties.scoretag_id = {$this->getMainTable()}.scoretag_id AND properties.store_id = {$object->getStoreId()}",
                'base_popularity'
            );
        }
        return $select;
    }

    /**
     * Fetch store ids in which scoretag visible
     *
     * @param Mage_Scoretag_Model_Resource_Scoretag $object
     * @return Mage_Scoretag_Model_Resource_Scoretag
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from($this->getTable('scoretag/summary'), array('store_id'))
            ->where('scoretag_id = :scoretag_id');
        $storeIds = $read->fetchCol($select, array('scoretag_id' => $object->getId()));

        $object->setVisibleInStoreIds($storeIds);

        return $this;
    }
}
