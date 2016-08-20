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
 * Score Oggetto Eav Indexer Resource Model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Eav extends Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Abstract
{
    /**
     * EAV Indexers by type
     *
     * @var array
     */
    protected $_types;

    /**
     * Define main index table
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_index_eav', 'entity_id');
    }

    /**
     * Retrieve array of EAV type indexers
     *
     * @return array
     */
    public function getIndexers()
    {
        if (is_null($this->_types)) {
            $this->_types   = array(
                'source'    => Mage::getResourceModel('score/oggetto_indexer_eav_source'),
                'decimal'   => Mage::getResourceModel('score/oggetto_indexer_eav_decimal'),
            );
        }

        return $this->_types;
    }

    /**
     * Retrieve indexer instance by type
     *
     * @param string $type
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Eav_Abstract
     */
    public function getIndexer($type)
    {
        $indexers = $this->getIndexers();
        if (!isset($indexers[$type])) {
            Mage::throwException(Mage::helper('score')->__('Unknown EAV indexer type "%s".', $type));
        }
        return $indexers[$type];
    }

    /**
     * Process oggetto save.
     * Method is responsible for index support
     * when oggetto was saved and assigned categories was changed.
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Eav
     */
    public function scoreOggettoSave(Mage_Index_Model_Event $event)
    {
        $oggettoId = $event->getEntityPk();
        $data = $event->getNewData();

        /**
         * Check if filterable attribute values were updated
         */
        if (!isset($data['reindex_eav'])) {
            return $this;
        }

        foreach ($this->getIndexers() as $indexer) {
            /** @var $indexer Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Eav_Abstract */
            $indexer->reindexEntities($oggettoId);
        }

        return $this;
    }

    /**
     * Process Oggetto Delete
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Eav
     */
    public function scoreOggettoDelete(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_eav_parent_ids'])) {
            return $this;
        }

        foreach ($this->getIndexers() as $indexer) {
            /** @var $indexer Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Eav_Abstract */
            $indexer->reindexEntities($data['reindex_eav_parent_ids']);
        }

        return $this;
    }

    /**
     * Process Oggetto Mass Update
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Eav
     */
    public function scoreOggettoMassAction(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_eav_oggetto_ids'])) {
            return $this;
        }

        foreach ($this->getIndexers() as $indexer) {
            /** @var $indexer Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Eav_Abstract */
            $indexer->reindexEntities($data['reindex_eav_oggetto_ids']);
        }

        return $this;
    }

    /**
     * Process Score Eav Attribute Save
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Eav
     */
    public function scoreEavAttributeSave(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_attribute'])) {
            return $this;
        }

        $indexer = $this->getIndexer($data['attribute_index_type']);

        $indexer->reindexAttribute($event->getEntityPk(), !empty($data['is_indexable']));

        return $this;
    }

    /**
     * Rebuild all index data
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Eav
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        foreach ($this->getIndexers() as $indexer) {
            /** @var $indexer Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Eav_Abstract */
            $indexer->reindexAll();
        }

        return $this;
    }

    /**
     * Retrieve temporary source index table name
     *
     * @param string $table
     * @return string
     */
    public function getIdxTable($table = null)
    {
        if ($this->useIdxTable()) {
           return $this->getTable('score/oggetto_eav_indexer_idx');
        }
        return $this->getTable('score/oggetto_eav_indexer_tmp');
    }
}
