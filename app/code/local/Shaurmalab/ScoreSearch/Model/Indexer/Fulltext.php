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
 * @package     Shaurmalab_ScoreSearch
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * ScoreSearch fulltext indexer model
 *
 * @category    Mage
 * @package     Shaurmalab_ScoreSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_ScoreSearch_Model_Indexer_Fulltext extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'scoresearch_fulltext_match_result';

    /**
     * List of searchable attributes
     *
     * @var null|array
     */
    protected $_searchableAttributes = null;

    /**
     * Retrieve resource instance
     *
     * @return Shaurmalab_ScoreSearch_Model_Resource_Indexer_Fulltext
     */
    protected function _getResource()
    {
        return Mage::getResourceSingleton('scoresearch/indexer_fulltext');
    }

    /**
     * Indexer must be match entities
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Shaurmalab_Score_Model_Oggetto::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
            Mage_Index_Model_Event::TYPE_DELETE
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
        Mage_Core_Model_Config_Data::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Shaurmalab_Score_Model_Convert_Adapter_Oggetto::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Shaurmalab_Score_Model_Category::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        )
    );

    /**
     * Related Configuration Settings for match
     *
     * @var array
     */
    protected $_relatedConfigSettings = array(
        Shaurmalab_ScoreSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_TYPE
    );

    /**
     * Retrieve Fulltext Search instance
     *
     * @return Shaurmalab_ScoreSearch_Model_Fulltext
     */
    protected function _getIndexer()
    {
        return Mage::getSingleton('scoresearch/fulltext');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('scoresearch')->__('Objects Search Index');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('scoresearch')->__('Rebuild Objects fulltext search index');
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
        $data       = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        if ($entity == Shaurmalab_Score_Model_Resource_Eav_Attribute::ENTITY) {
            /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
            $attribute      = $event->getDataObject();

            if (!$attribute) {
                $result = false;
            } elseif ($event->getType() == Mage_Index_Model_Event::TYPE_SAVE) {
                $result = $attribute->dataHasChangedFor('is_searchable');
            } elseif ($event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
                $result = $attribute->getIsSearchable();
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
        } else if ($entity == Mage_Core_Model_Config_Data::ENTITY) {
            $data = $event->getDataObject();
            if ($data && in_array($data->getPath(), $this->_relatedConfigSettings)) {
                $result = $data->isValueChanged();
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
                $event->addNewData('scoresearch_fulltext_reindex_all', true);
                break;

            case Mage_Core_Model_Config_Data::ENTITY:
            case Mage_Core_Model_Store::ENTITY:
            case Shaurmalab_Score_Model_Resource_Eav_Attribute::ENTITY:
            case Mage_Core_Model_Store_Group::ENTITY:
                $event->addNewData('scoresearch_fulltext_skip_call_event_handler', true);
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
            case Shaurmalab_Score_Model_Category::ENTITY:
                $this->_registerScoreCategoryEvent($event);
                break;
        }
    }

    /**
     * Get data required for category'es products reindex
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_ScoreSearch_Model_Indexer_Search
     */
    protected function _registerScoreCategoryEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                /* @var $category Mage_Catalog_Model_Category */
                $category   = $event->getDataObject();
                $oggettoIds = $category->getAffectedOggettoIds();
                if ($oggettoIds) {
                    $event->addNewData('scoresearch_category_update_oggetto_ids', $oggettoIds);
                    $event->addNewData('scoresearch_category_update_category_ids', array($category->getId()));
                } else {
                    $movedCategoryId = $category->getMovedCategoryId();
                    if ($movedCategoryId) {
                        $event->addNewData('scoresearch_category_update_oggetto_ids', array());
                        $event->addNewData('scoresearch_category_update_category_ids', array($movedCategoryId));
                    }
                }
                break;
        }

        return $this;
    }

    /**
     * Register data required by catatalog oggetto process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_ScoreSearch_Model_Indexer_Search
     */
    protected function _registerScoreOggettoEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                /* @var $oggetto Mage_Catalog_Model_Oggetto */
                $oggetto = $event->getDataObject();

                $event->addNewData('scoresearch_update_oggetto_id', $oggetto->getId());
                break;
            case Mage_Index_Model_Event::TYPE_DELETE:
                /* @var $oggetto Mage_Catalog_Model_Oggetto */
                $oggetto = $event->getDataObject();

                $event->addNewData('scoresearch_delete_oggetto_id', $oggetto->getId());
                break;
            case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                /* @var $actionObject Varien_Object */
                $actionObject = $event->getDataObject();

                $reindexData  = array();
                $rebuildIndex = false;

                // check if status changed
                $attrData = $actionObject->getAttributesData();
                if (isset($attrData['status'])) {
                    $rebuildIndex = true;
                    $reindexData['scoresearch_status'] = $attrData['status'];
                }

                // check changed websites
                if ($actionObject->getWebsiteIds()) {
                    $rebuildIndex = true;
                    $reindexData['scoresearch_website_ids'] = $actionObject->getWebsiteIds();
                    $reindexData['scoresearch_action_type'] = $actionObject->getActionType();
                }

                $searchableAttributes = array();
                if (is_array($attrData)) {
                    $searchableAttributes = array_intersect($this->_getSearchableAttributes(), array_keys($attrData));
                }

                if (count($searchableAttributes) > 0) {
                    $rebuildIndex = true;
                    $reindexData['scoresearch_force_reindex'] = true;
                }

                // register affected products
                if ($rebuildIndex) {
                    $reindexData['scoresearch_oggetto_ids'] = $actionObject->getOggettoIds();
                    foreach ($reindexData as $k => $v) {
                        $event->addNewData($k, $v);
                    }
                }
                break;
        }

        return $this;
    }

    /**
     * Retrieve searchable attributes list
     *
     * @return array
     */
    protected function _getSearchableAttributes()
    {
        if (is_null($this->_searchableAttributes)) {
            /** @var $attributeCollection Mage_Catalog_Model_Resource_Product_Attribute_Collection */
            $attributeCollection = Mage::getResourceModel('score/oggetto_attribute_collection');
            $attributeCollection->addIsSearchableFilter();

            foreach ($attributeCollection as $attribute) {
                $this->_searchableAttributes[] = $attribute->getAttributeCode();
            }
        }

        return $this->_searchableAttributes;
    }

    /**
     * Check if oggetto is composite
     *
     * @param int $oggettoId
     * @return bool
     */
    protected function _isOggettoComposite($oggettoId)
    {
        $oggetto = Mage::getModel('score/oggetto')->load($oggettoId);
        return $oggetto->isComposite();
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        if (!empty($data['scoresearch_fulltext_reindex_all'])) {
            $this->reindexAll();
        } else if (!empty($data['scoresearch_delete_oggetto_id'])) {
            $oggettoId = $data['scoresearch_delete_oggetto_id'];

            if (!$this->_isOggettoComposite($oggettoId)) {
                $parentIds = $this->_getResource()->getRelationsByChild($oggettoId);
                if (!empty($parentIds)) {
                    $this->_getIndexer()->rebuildIndex(null, $parentIds);
                }
            }

            $this->_getIndexer()->cleanIndex(null, $oggettoId)
                ->resetSearchResults();
        } else if (!empty($data['scoresearch_update_oggetto_id'])) {
            $oggettoId = $data['scoresearch_update_oggetto_id'];
            $oggettoIds = array($oggettoId);

            if (!$this->_isOggettoComposite($oggettoId)) {
                $parentIds = $this->_getResource()->getRelationsByChild($oggettoId);
                if (!empty($parentIds)) {
                    $oggettoIds = array_merge($oggettoIds, $parentIds);
                }
            }

            $this->_getIndexer()->rebuildIndex(null, $oggettoIds)
                ->resetSearchResults();
        } else if (!empty($data['scoresearch_oggetto_ids'])) {
            // mass action
            $oggettoIds = $data['scoresearch_oggetto_ids'];

            if (!empty($data['scoresearch_website_ids'])) {
                $websiteIds = $data['scoresearch_website_ids'];
                $actionType = $data['scoresearch_action_type'];

                foreach ($websiteIds as $websiteId) {
                    foreach (Mage::app()->getWebsite($websiteId)->getStoreIds() as $storeId) {
                        if ($actionType == 'remove') {
                            $this->_getIndexer()
                                ->cleanIndex($storeId, $oggettoIds)
                                ->resetSearchResults();
                        } else if ($actionType == 'add') {
                            $this->_getIndexer()
                                ->rebuildIndex($storeId, $oggettoIds)
                                ->resetSearchResults();
                        }
                    }
                }
            }
            if (isset($data['scoresearch_status'])) {
                $status = $data['scoresearch_status'];
                if ($status == Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED) {
                    $this->_getIndexer()
                        ->rebuildIndex(null, $oggettoIds)
                        ->resetSearchResults();
                } else {
                    $this->_getIndexer()
                        ->cleanIndex(null, $oggettoIds)
                        ->resetSearchResults();
                }
            }
            if (isset($data['scoresearch_force_reindex'])) {
                $this->_getIndexer()
                    ->rebuildIndex(null, $oggettoIds)
                    ->resetSearchResults();
            }
        } else if (isset($data['scoresearch_category_update_oggetto_ids'])) {
            $oggettoIds = $data['scoresearch_category_update_oggetto_ids'];
            $categoryIds = $data['scoresearch_category_update_category_ids'];

            $this->_getIndexer()
                ->updateCategoryIndex($oggettoIds, $categoryIds);
        }
    }

    /**
     * Rebuild all index data
     *
     */
    public function reindexAll()
    {
        $resourceModel = $this->_getIndexer()->getResource();
        $resourceModel->beginTransaction();
        try {
            $this->_getIndexer()->rebuildIndex();
            $resourceModel->commit();
        } catch (Exception $e) {
            $resourceModel->rollBack();
            throw $e;
        }
    }
}
