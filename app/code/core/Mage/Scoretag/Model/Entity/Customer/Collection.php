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
 * Customers collection
 *
 * @category   Mage
 * @package    Mage_Scoretag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Scoretag_Model_Entity_Customer_Collection extends Mage_Customer_Model_Entity_Customer_Collection
{
    protected $_scoretagTable;
    protected $_scoretagRelTable;

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct();
        $this->_scoretagTable = $resource->getTableName('scoretag/scoretag');
        $this->_scoretagRelTable = $resource->getTableName('scoretag/scoretag_relation');

//        $this->joinField('scoretag_total_used', $this->_scoretagRelTable, 'count(_table_scoretag_total_used.scoretag_relations_id)', 'entity_val_id=entity_id', array('entity_id' => '2'));
//        $this->getSelect()->group('scoretag_scoretag_id');
//        echo $this->getSelect();
//        $this->_oggettoTable = $resource->getTableName('score/oggetto');
//        $this->_select->from(array('p' => $this->_oggettoTable))
//            ->join(array('tr' => $this->_scoretagRelTable), 'tr.entity_val_id=p.oggetto_id and tr.entity_id=1', array('total_used' => 'count(tr.scoretag_relations_id)'))
//            ->group('p.oggetto_id', 'tr.scoretag_id')
//        ;

    }

    public function addScoretagFilter($scoretagId)
    {
        $this->joinField('scoretag_scoretag_id', $this->_scoretagRelTable, 'scoretag_id', 'customer_id=entity_id');
        $this->getSelect()->where($this->_getAttributeTableAlias('scoretag_scoretag_id') . '.scoretag_id=?', $scoretagId);
        return $this;
    }

    public function addOggettoFilter($oggettoId)
    {
        $this->joinField('scoretag_oggetto_id', $this->_scoretagRelTable, 'oggetto_id', 'customer_id=entity_id');
        $this->getSelect()->where($this->_getAttributeTableAlias('scoretag_oggetto_id') . '.oggetto_id=?', $oggettoId);
        return $this;
    }

    public function load($printQuery = false, $logQuery = false)
    {
        parent::load($printQuery, $logQuery);
        $this->_loadScoretags($printQuery, $logQuery);
        return $this;
    }

    protected function _loadScoretags($printQuery = false, $logQuery = false)
    {
        if (empty($this->_items)) {
            return $this;
        }
        $customerIds = array();
        foreach ($this->getItems() as $item) {
            $customerIds[] = $item->getId();
        }
        $this->getSelect()->reset()
            ->from(array('tr' => $this->_scoretagRelTable), array('*','total_used' => 'count(tr.scoretag_relation_id)'))
            ->joinLeft(array('t' => $this->_scoretagTable),'t.scoretag_id=tr.scoretag_id')
            ->group(array('tr.customer_id', 't.scoretag_id'))
            ->where('tr.customer_id in (?)',$customerIds)
        ;
        $this->printLogQuery($printQuery, $logQuery);

        $scoretags = array();
        $data = $this->_read->fetchAll($this->getSelect());
        foreach ($data as $row) {
            if (!isset($scoretags[ $row['customer_id'] ])) {
                $scoretags[ $row['customer_id'] ] = array();
            }
            $scoretags[ $row['customer_id'] ][] = $row;
        }
        foreach ($this->getItems() as $item) {
            if (isset($scoretags[$item->getId()])) {
                $item->setData('scoretags', $scoretags[$item->getId()]);
            }
        }
        return $this;
    }

}
