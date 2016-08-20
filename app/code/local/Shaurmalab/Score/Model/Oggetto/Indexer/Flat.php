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
class Shaurmalab_Score_Model_Oggetto_Indexer_Flat extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'score_oggetto_flat_match_result';

    /**
     * Index math Entities array
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Shaurmalab_Score_Model_Oggetto::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
        ),
        Shaurmalab_Score_Model_Resource_Eav_Attribute::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE,
        ),
        Mage_Core_Model_Store::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE
        ),
        Mage_Core_Model_Store_Group::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Shaurmalab_Score_Model_Convert_Adapter_Oggetto::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Shaurmalab_Score_Model_Oggetto_Flat_Indexer::ENTITY => array(
            Shaurmalab_Score_Model_Oggetto_Flat_Indexer::EVENT_TYPE_REBUILD,
        ),
    );

    /**
     * Whether the indexer should be displayed on process/list page
     *
     * @return bool
     */
    public function isVisible()
    {
        /** @var $oggettoFlatHelper Shaurmalab_Score_Helper_Oggetto_Flat */
        $oggettoFlatHelper = Mage::helper('score/oggetto_flat');
        return $oggettoFlatHelper->isEnabled() || !$oggettoFlatHelper->isBuilt();
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('score')->__('Oggetto Flat Data');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('score')->__('Reorganize EAV oggetto structure to flat structure');
    }

    /**
     * Retrieve Score Oggetto Flat Indexer model
     *
     * @return Shaurmalab_Score_Model_Oggetto_Flat_Indexer
     */
    protected function _getIndexer()
    {
        return Mage::getSingleton('score/oggetto_flat_indexer');
    }

    /**
     * Check if event can be matched by process
     * Overwrote for check is flat score oggetto is enabled and specific save
     * attribute, store, store_group
     *
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        /** @var $oggettoFlatHelper Shaurmalab_Score_Helper_Oggetto_Flat */
        $oggettoFlatHelper = Mage::helper('score/oggetto_flat');
        if (!$oggettoFlatHelper->isEnabled() || !$oggettoFlatHelper->isBuilt()) {
            return false;
        }

        $data = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        if ($entity == Shaurmalab_Score_Model_Resource_Eav_Attribute::ENTITY) {
            /* @var $attribute Shaurmalab_Score_Model_Resource_Eav_Attribute */
            $attribute      = $event->getDataObject();
            $addFilterable  = $oggettoFlatHelper->isAddFilterableAttributes();

            $enableBefore   = $attribute && (($attribute->getOrigData('backend_type') == 'static')
                || ($addFilterable && $attribute->getOrigData('is_filterable') > 0)
                || ($attribute->getOrigData('used_in_oggetto_listing') == 1)
                || ($attribute->getOrigData('is_used_for_promo_rules') == 1)
                || ($attribute->getOrigData('used_for_sort_by') == 1));

            $enableAfter    = $attribute && (($attribute->getData('backend_type') == 'static')
                || ($addFilterable && $attribute->getData('is_filterable') > 0)
                || ($attribute->getData('used_in_oggetto_listing') == 1)
                || ($attribute->getData('is_used_for_promo_rules') == 1)
                || ($attribute->getData('used_for_sort_by') == 1));

            if ($attribute && $event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
                $result = $enableBefore;
            } elseif ($attribute && $event->getType() == Mage_Index_Model_Event::TYPE_SAVE) {
                if ($enableAfter || $enableBefore) {
                    $result = true;
                } else {
                    $result = false;
                }
            } else {
                $result = false;
            }
        } else if ($entity == Mage_Core_Model_Store::ENTITY) {
            if ($event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
                $result = true;
            } else {
                /* @var $store Mage_Core_Model_Store */
                $store = $event->getDataObject();
                if ($store && $store->isObjectNew()) {
                    $result = true;
                } else {
                    $result = false;
                }
            }
        } else if ($entity == Mage_Core_Model_Store_Group::ENTITY) {
            /* @var $storeGroup Mage_Core_Model_Store_Group */
            $storeGroup = $event->getDataObject();
            if ($storeGroup && $storeGroup->dataHasChangedFor('website_id')) {
                $result = true;
            } else {
                $result = false;
            }
        } else {
            $result = parent::matchEvent($event);
        }

        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);

        return $result;
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        switch ($event->getEntity()) {
            case Shaurmalab_Score_Model_Oggetto::ENTITY:
                $this->_registerScoreOggettoEvent($event);
                break;
            case Shaurmalab_Score_Model_Convert_Adapter_Oggetto::ENTITY:
                $event->addNewData('score_oggetto_flat_reindex_all', true);
                break;
            case Mage_Core_Model_Store::ENTITY:
                if ($event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
                    $this->_registerCoreStoreEvent($event);
                    break;
                }
            case Shaurmalab_Score_Model_Resource_Eav_Attribute::ENTITY:
            case Mage_Core_Model_Store_Group::ENTITY:
                $event->addNewData('score_oggetto_flat_skip_call_event_handler', true);
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
            case Shaurmalab_Score_Model_Oggetto_Flat_Indexer::ENTITY:
                switch ($event->getType()) {
                    case Shaurmalab_Score_Model_Oggetto_Flat_Indexer::EVENT_TYPE_REBUILD:
                        $event->addNewData('id', $event->getDataObject()->getId());
                }
                break;
        }
    }

    /**
     * Register data required by score oggetto process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_Score_Model_Oggetto_Indexer_Flat
     */
    protected function _registerScoreOggettoEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                /* @var $oggetto Shaurmalab_Score_Model_Oggetto */
                $oggetto = $event->getDataObject();
                $event->addNewData('score_oggetto_flat_oggetto_id', $oggetto->getId());
                break;

            case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                /* @var $actionObject Varien_Object */
                $actionObject = $event->getDataObject();

                $reindexData  = array();
                $reindexFlat  = false;

                // check if status changed
                $attrData = $actionObject->getAttributesData();
                if (isset($attrData['status'])) {
                    $reindexFlat = true;
                    $reindexData['score_oggetto_flat_status'] = $attrData['status'];
                }

                // check changed websites
                if ($actionObject->getWebsiteIds()) {
                    $reindexFlat = true;
                    $reindexData['score_oggetto_flat_website_ids'] = $actionObject->getWebsiteIds();
                    $reindexData['score_oggetto_flat_action_type'] = $actionObject->getActionType();
                }

                $flatAttributes = array();
                if (is_array($attrData)) {
                    $flatAttributes = array_intersect($this->_getFlatAttributes(), array_keys($attrData));
                }

                if (count($flatAttributes) > 0) {
                    $reindexFlat = true;
                    $reindexData['score_oggetto_flat_force_update'] = true;
                }

                // register affected oggettos
                if ($reindexFlat) {
                    $reindexData['score_oggetto_flat_oggetto_ids'] = $actionObject->getOggettoIds();
                    foreach ($reindexData as $k => $v) {
                        $event->addNewData($k, $v);
                    }
                }
                break;
        }

        return $this;
    }

    /**
     * Register core store delete process
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_Score_Model_Oggetto_Indexer_Flat
     */
    protected function _registerCoreStoreEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
            /* @var $store Mage_Core_Model_Store */
            $store = $event->getDataObject();
            $event->addNewData('score_oggetto_flat_delete_store_id', $store->getId());
        }
        return $this;
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if ($event->getType() == Shaurmalab_Score_Model_Oggetto_Flat_Indexer::EVENT_TYPE_REBUILD) {
            $this->_getIndexer()->getResource()->rebuild($data['id']);
            return;
        }


        if (!empty($data['score_oggetto_flat_reindex_all'])) {
            $this->reindexAll();
        } else if (!empty($data['score_oggetto_flat_oggetto_id'])) {
            // score_oggetto save
            $oggettoId = $data['score_oggetto_flat_oggetto_id'];
            $this->_getIndexer()->saveOggetto($oggettoId);
        } else if (!empty($data['score_oggetto_flat_oggetto_ids'])) {
            // score_oggetto mass_action
            $oggettoIds = $data['score_oggetto_flat_oggetto_ids'];

            if (!empty($data['score_oggetto_flat_website_ids'])) {
                $websiteIds = $data['score_oggetto_flat_website_ids'];
                foreach ($websiteIds as $websiteId) {
                    $website = Mage::app()->getWebsite($websiteId);
                    foreach ($website->getStores() as $store) {
                        if ($data['score_oggetto_flat_action_type'] == 'remove') {
                            $this->_getIndexer()->removeOggetto($oggettoIds, $store->getId());
                        } else {
                            $this->_getIndexer()->updateOggetto($oggettoIds, $store->getId());
                        }
                    }
                }
            }

            if (isset($data['score_oggetto_flat_status'])) {
                $status = $data['score_oggetto_flat_status'];
                $this->_getIndexer()->updateOggettoStatus($oggettoIds, $status);
            }

            if (isset($data['score_oggetto_flat_force_update'])) {
                $this->_getIndexer()->updateOggetto($oggettoIds);
            }
        } else if (!empty($data['score_oggetto_flat_delete_store_id'])) {
            $this->_getIndexer()->deleteStore($data['score_oggetto_flat_delete_store_id']);
        }
    }

    /**
     * Rebuild all index data
     *
     */
    public function reindexAll()
    {
        $this->_getIndexer()->reindexAll();
    }

    /**
     * Retrieve list of attribute codes, that are used in flat
     *
     * @return array
     */
    protected function _getFlatAttributes()
    {
        return Mage::getModel('score/oggetto_flat_indexer')->getAttributeCodes();
    }
}
