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
 * Score Category Flat Indexer Model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Category_Indexer_Flat extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'score_category_flat_match_result';

    /**
     * Matched entity events
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Shaurmalab_Score_Model_Category::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Core_Model_Store::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE
        ),
        Mage_Core_Model_Store_Group::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
    );

    /**
     * Whether the indexer should be displayed on process/list page
     *
     * @return bool
     */
    public function isVisible()
    {
        /** @var $categoryFlatHelper Shaurmalab_Score_Helper_Category_Flat */
        $categoryFlatHelper = Mage::helper('score/category_flat');
        return $categoryFlatHelper->isEnabled() || !$categoryFlatHelper->isBuilt();
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('score')->__('Category Flat Data');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('score')->__('Reorganize EAV category structure to flat structure');
    }

    /**
     * Retrieve Score Category Flat Indexer model
     *
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Category_Flat
     */
    protected function _getIndexer()
    {
        return Mage::getResourceSingleton('score/category_flat');
    }

    /**
     * Check if event can be matched by process
     * Overwrote for check is flat score category is enabled and specific save
     * category, store, store_group
     *
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        /** @var $categoryFlatHelper Shaurmalab_Score_Helper_Category_Flat */
        $categoryFlatHelper = Mage::helper('score/category_flat');
        if (!$categoryFlatHelper->isAccessible() || !$categoryFlatHelper->isBuilt()) {
            return false;
        }

        $data = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        if ($entity == Mage_Core_Model_Store::ENTITY) {
            if ($event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
                $result = true;
            } elseif ($event->getType() == Mage_Index_Model_Event::TYPE_SAVE) {
                /** @var $store Mage_Core_Model_Store */
                $store = $event->getDataObject();
                if ($store && ($store->isObjectNew()
                    || $store->dataHasChangedFor('group_id')
                    || $store->dataHasChangedFor('root_category_id')
                )) {
                    $result = true;
                } else {
                    $result = false;
                }
            } else {
                $result = false;
            }
        } elseif ($entity == Mage_Core_Model_Store_Group::ENTITY) {
            /** @var $storeGroup Mage_Core_Model_Store_Group */
            $storeGroup = $event->getDataObject();
            if ($storeGroup
                && ($storeGroup->dataHasChangedFor('website_id') || $storeGroup->dataHasChangedFor('root_category_id'))
            ) {
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
            case Shaurmalab_Score_Model_Category::ENTITY:
                $this->_registerScoreCategoryEvent($event);
                break;

            case Mage_Core_Model_Store::ENTITY:
                if ($event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
                    $this->_registerCoreStoreEvent($event);
                    break;
                }
            case Mage_Core_Model_Store_Group::ENTITY:
                $event->addNewData('score_category_flat_skip_call_event_handler', true);
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
        }
    }

    /**
     * Register data required by score category process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_Score_Model_Category_Indexer_Flat
     */
    protected function _registerScoreCategoryEvent(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                /* @var $category Shaurmalab_Score_Model_Category */
                $category = $event->getDataObject();

                /**
                 * Check if category has another affected category ids (category move result)
                 */
                $affectedCategoryIds = $category->getAffectedCategoryIds();
                if ($affectedCategoryIds) {
                    $event->addNewData('score_category_flat_affected_category_ids', $affectedCategoryIds);
                } else {
                    $event->addNewData('score_category_flat_category_id', $category->getId());
                }

                break;
        }
        return $this;
    }

    /**
     * Register core store delete process
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_Score_Model_Category_Indexer_Flat
     */
    protected function _registerCoreStoreEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
            /* @var $store Mage_Core_Model_Store */
            $store = $event->getDataObject();
            $event->addNewData('score_category_flat_delete_store_id', $store->getId());
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

        if (!empty($data['score_category_flat_reindex_all'])) {
            $this->reindexAll();
        } else if (!empty($data['score_category_flat_category_id'])) {
            // score_oggetto save
            $categoryId = $data['score_category_flat_category_id'];
            $this->_getIndexer()->synchronize($categoryId);
        } else if (!empty($data['score_category_flat_affected_category_ids'])) {
            $categoryIds = $data['score_category_flat_affected_category_ids'];
            $this->_getIndexer()->move($categoryIds);
        } else if (!empty($data['score_category_flat_delete_store_id'])) {
            $storeId = $data['score_category_flat_delete_store_id'];
            $this->_getIndexer()->deleteStores($storeId);
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
}
