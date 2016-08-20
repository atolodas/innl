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
 * Scoretag collection model
 *
 * @category    Mage
 * @package     Mage_Scoretag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Scoretag_Model_Resource_Scoretag_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Use getFlag('store_filter') & setFlag('store_filter', true) instead.
     *
     * @var bool
     */
    protected $_isStoreFilter  = false;

    /**
     * Joined tables
     *
     * @var array
     */
    protected $_joinFlags      = array();

    /**
     * Mapping for fields
     *
     * @var array
     */
    public $_map               = array(
        'fields' => array(
            'scoretag_id' => 'main_table.scoretag_id'
        ),
    );

    /**
     * Define resource model and model
     *
     */
    protected function _construct()
    {
        $this->_init('scoretag/scoretag');
    }

    /**
     * Loads collection
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        parent::load($printQuery, $logQuery);
        if ($this->getFlag('add_stores_after')) {
            $this->_addStoresVisibility();
        }
        return $this;
    }

    /**
     * Set flag about joined table.
     * setFlag method must be used in future.
     *
     * @deprecated after 1.3.2.3
     *
     * @param string $table
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function setJoinFlag($table)
    {
        $this->setFlag($table, true);
        return $this;
    }

    /**
     * Get flag's status about joined table.
     * getFlag method must be used in future.
     *
     * @deprecated after 1.3.2.3
     *
     * @param string $table
     * @return bool
     */
    public function getJoinFlag($table)
    {
        return $this->getFlag($table);
    }

    /**
     * Unset value of join flag.
     * Set false (bool) value to flag instead in future.
     *
     * @deprecated after 1.3.2.3
     *
     * @param string $table
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function unsetJoinFlag($table = null)
    {
        $this->setFlag($table, false);
        return $this;
    }

    /**
     * Sett
     *
     * @param int $limit
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function limit($limit)
    {
        $this->getSelect()->limit($limit);
        return $this;
    }

    /**
     * Replacing popularity by sum of popularity and base_popularity
     *
     * @param int $limit
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function addPopularity($limit = null)
    {
        if (!$this->getFlag('popularity')) {
            $this->getSelect()
            ->joinLeft(
                array('relation' => $this->getTable('scoretag/relation')),
                'main_table.scoretag_id = relation.scoretag_id',
                array()
            )
            ->joinLeft(
                array('summary' => $this->getTable('scoretag/summary')),
                'relation.scoretag_id = summary.scoretag_id AND relation.store_id = summary.store_id',
                array('popularity')
            )
            ->group('main_table.scoretag_id');

            /*
             * Allow analytic function usage
             */
            $this->_useAnalyticFunction = true;

            if (!is_null($limit)) {
                $this->getSelect()->limit($limit);
            }

            $this->setFlag('popularity');
        }
        return $this;
    }

    /**
     * Adds summary
     *
     * @param int $storeId
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function addSummary($storeId)
    {
        if (!$this->getFlag('summary')) {
            $tableAlias = 'summary';
            $joinCondition = $this->getConnection()
                    ->quoteInto(' AND ' . $tableAlias . '.store_id IN(?)', $storeId);

            $this->getSelect()
                ->joinLeft(
                    array($tableAlias => $this->getTable('scoretag/summary')),
                    'main_table.scoretag_id = ' . $tableAlias . '.scoretag_id' . $joinCondition,
                    array('store_id','popularity', 'customers', 'oggettos'
                ));

            $this->addFilterToMap('store_id', $tableAlias . '.store_id');
            $this->addFilterToMap('popularity', $tableAlias . '.popularity');
            $this->addFilterToMap('customers', $tableAlias . '.customers');
            $this->addFilterToMap('oggettos', $tableAlias . '.oggettos');

            $this->setFlag('summary', true);
        }
        return $this;
    }

    /**
     * Adds store visibility
     *
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function addStoresVisibility()
    {
        $this->setFlag('add_stores_after', true);
        return $this;
    }

    /**
     * Adds store visibility
     *
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    protected function _addStoresVisibility()
    {
        $scoretagIds = $this->getColumnValues('scoretag_id');

        $scoretagsStores = array();
        if (sizeof($scoretagIds) > 0) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('scoretag/summary'), array('store_id', 'scoretag_id'))
                ->where('scoretag_id IN(?)', $scoretagIds);
            $scoretagsRaw = $this->getConnection()->fetchAll($select);

            foreach ($scoretagsRaw as $scoretag) {
                if (!isset($scoretagsStores[$scoretag['scoretag_id']])) {
                    $scoretagsStores[$scoretag['scoretag_id']] = array();
                }

                $scoretagsStores[$scoretag['scoretag_id']][] = $scoretag['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($scoretagsStores[$item->getId()])) {
                $item->setStores($scoretagsStores[$item->getId()]);
            } else {
                $item->setStores(array());
            }
        }

        return $this;
    }

    /**
     * Adds field to filter
     *
     * @param string $field
     * @param array $condition
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($this->getFlag('relation') && 'popularity' == $field) {
            // TOFIX
            $this->getSelect()->having(
                $this->_getConditionSql('COUNT(relation.scoretag_relation_id)', $condition)
            );
        } elseif ($this->getFlag('summary') && in_array(
            $field, array('customers', 'oggettos', 'uses', 'historical_uses', 'popularity')
        )) {
            $this->getSelect()->where($this->_getConditionSql('summary.' . $field, $condition));
        } else {
           parent::addFieldToFilter($field, $condition);
        }
        return $this;
    }

    /**
     * Get sql for get record count
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();

        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::GROUP);
        $select->reset(Zend_Db_Select::HAVING);
        $select->columns('COUNT(DISTINCT main_table.scoretag_id)');
        return $select;
    }

    /**
     * Add filter by store
     *
     * @param array | int $storeId
     * @param bool $allFilter
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function addStoreFilter($storeId, $allFilter = true)
    {
        if (!$this->getFlag('store_filter')) {

            $this->getSelect()->joinLeft(
                array('summary_store' => $this->getTable('scoretag/summary')),
                'main_table.scoretag_id = summary_store.scoretag_id'
            );

            $this->getSelect()->where('summary_store.store_id IN (?)', $storeId);

            $this->getSelect()->group('main_table.scoretag_id');

            if ($this->getFlag('relation') && $allFilter) {
                $this->getSelect()->where('relation.store_id IN (?)', $storeId);
            }
            if ($this->getFlag('prelation') && $allFilter) {
                $this->getSelect()->where('prelation.store_id IN (?)', $storeId);
            }

            /*
             * Allow Analytic functions usage
             */

            $this->_useAnalyticFunction = true;

            $this->setFlag('store_filter', true);
        }

        return $this;
    }

    /**
     * Adds filtering by active
     *
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function setActiveFilter()
    {
        $statusActive = Mage_Scoretag_Model_Scoretag_Relation::STATUS_ACTIVE;
        $this->getSelect()->where('relation.active = ?', $statusActive);
        if ($this->getFlag('prelation')) {
            $this->getSelect()->where('prelation.active = ?', $statusActive);
        }
        return $this;
    }

    /**
     * Adds filter by status
     *
     * @param int $status
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function addStatusFilter($status)
    {
        $this->getSelect()->where('main_table.status = ?', $status);
        return $this;
    }

    /**
     * Adds filter by oggetto id
     *
     * @param int $oggettoId
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function addOggettoFilter($oggettoId)
    {
        $this->addFieldToFilter('relation.oggetto_id', $oggettoId);
        if ($this->getFlag('prelation')) {
            $this->addFieldToFilter('prelation.oggetto_id', $oggettoId);
        }
        return $this;
    }

    /**
     * Adds filter by customer id
     *
     * @param int $customerId
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function addCustomerFilter($customerId)
    {
        $this->getSelect()
            ->where('relation.customer_id = ?', $customerId);
        if ($this->getFlag('prelation')) {
            $this->getSelect()
                ->where('prelation.customer_id = ?', $customerId);
        }
        return $this;
    }

    /**
     * Adds grouping by scoretag id
     *
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function addScoretagGroup()
    {
        $this->getSelect()->group('main_table.scoretag_id');
        $this->_useAnalyticFunction = true;
        return $this;
    }

    /**
     * Joins scoretag/relation table
     *
     * @return Mage_Scoretag_Model_Resource_Scoretag_Collection
     */
    public function joinRel()
    {
        $this->setFlag('relation', true);
        $this->getSelect()->joinLeft(
            array('relation' => $this->getTable('scoretag/relation')),
            'main_table.scoretag_id=relation.scoretag_id'
        );
        return $this;
    }
}
