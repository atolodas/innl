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
 * Category oggettos indexer model.
 * Responsibility for system actions:
 *  - Oggetto save (changed assigned categories list)
 *  - Category save (changed assigned oggettos list or category move)
 *  - Store save (new store creation, changed store group) - require reindex all data
 *  - Store group save (changed root category or group website) - require reindex all data
 *
 * @method Shaurmalab_Score_Model_Resource_Category_Indexer_Oggetto _getResource()
 * @method Shaurmalab_Score_Model_Resource_Category_Indexer_Oggetto getResource()
 * @method int getCategoryId()
 * @method Shaurmalab_Score_Model_Category_Indexer_Oggetto setCategoryId(int $value)
 * @method int getOggettoId()
 * @method Shaurmalab_Score_Model_Category_Indexer_Oggetto setOggettoId(int $value)
 * @method int getPosition()
 * @method Shaurmalab_Score_Model_Category_Indexer_Oggetto setPosition(int $value)
 * @method int getIsParent()
 * @method Shaurmalab_Score_Model_Category_Indexer_Oggetto setIsParent(int $value)
 * @method int getStoreId()
 * @method Shaurmalab_Score_Model_Category_Indexer_Oggetto setStoreId(int $value)
 * @method int getVisibility()
 * @method Shaurmalab_Score_Model_Category_Indexer_Oggetto setVisibility(int $value)
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Category_Indexer_Oggetto extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'score_category_oggetto_match_result';

    /**
     * @var array
     */
    protected $_matchedEntities = array(
        Shaurmalab_Score_Model_Oggetto::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION
        ),
        Shaurmalab_Score_Model_Category::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Core_Model_Store::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Core_Model_Store_Group::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Shaurmalab_Score_Model_Convert_Adapter_Oggetto::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        )
    );

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('score/category_indexer_oggetto');
    }

    /**
     * Get Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('score')->__('Category Oggettos');
    }

    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('score')->__('Indexed category/oggettos association');
    }

    /**
     * Check if event can be matched by process.
     * Overwrote for specific config save, store and store groups save matching
     *
     * @param Mage_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Mage_Index_Model_Event $event)
    {
        $data      = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        if ($entity == Mage_Core_Model_Store::ENTITY) {
            $store = $event->getDataObject();
            if ($store && ($store->isObjectNew() || $store->dataHasChangedFor('group_id'))) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ($entity == Mage_Core_Model_Store_Group::ENTITY) {
            $storeGroup = $event->getDataObject();
            $hasDataChanges = $storeGroup && ($storeGroup->dataHasChangedFor('root_category_id')
                || $storeGroup->dataHasChangedFor('website_id'));
            if ($storeGroup && !$storeGroup->isObjectNew() && $hasDataChanges) {
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
     * Check if category ids was changed
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        $entity = $event->getEntity();
        switch ($entity) {
            case Shaurmalab_Score_Model_Oggetto::ENTITY:
               $this->_registerOggettoEvent($event);
                break;

            case Shaurmalab_Score_Model_Category::ENTITY:
                $this->_registerCategoryEvent($event);
                break;

            case Shaurmalab_Score_Model_Convert_Adapter_Oggetto::ENTITY:
                $event->addNewData('score_category_oggetto_reindex_all', true);
                break;

            case Mage_Core_Model_Store::ENTITY:
            case Mage_Core_Model_Store_Group::ENTITY:
                $process = $event->getProcess();
                $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
        }
        return $this;
    }

    /**
     * Register event data during oggetto save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerOggettoEvent(Mage_Index_Model_Event $event)
    {
        $eventType = $event->getType();
        if ($eventType == Mage_Index_Model_Event::TYPE_SAVE) {
            $oggetto = $event->getDataObject();
            /**
             * Check if oggetto categories data was changed
             */
            if ($oggetto->getIsChangedCategories() || $oggetto->dataHasChangedFor('status')
                || $oggetto->dataHasChangedFor('visibility') || $oggetto->getIsChangedWebsites()) {
                $event->addNewData('category_ids', $oggetto->getCategoryIds());
            }
        } else if ($eventType == Mage_Index_Model_Event::TYPE_MASS_ACTION) {
            /* @var $actionObject Varien_Object */
            $actionObject = $event->getDataObject();
            $attributes   = array('status', 'visibility');
            $rebuildIndex = false;

            // check if attributes changed
            $attrData = $actionObject->getAttributesData();
            if (is_array($attrData)) {
                foreach ($attributes as $attributeCode) {
                    if (array_key_exists($attributeCode, $attrData)) {
                        $rebuildIndex = true;
                        break;
                    }
                }
            }

            // check changed websites
            if ($actionObject->getWebsiteIds()) {
                $rebuildIndex = true;
            }

            // register affected oggettos
            if ($rebuildIndex) {
                $event->addNewData('oggetto_ids', $actionObject->getOggettoIds());
            }
        }
    }

    /**
     * Register event data during category save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerCategoryEvent(Mage_Index_Model_Event $event)
    {
        $category = $event->getDataObject();
        /**
         * Check if oggetto categories data was changed
         */
        if ($category->getIsChangedOggettoList()) {
            $event->addNewData('oggettos_was_changed', true);
        }
        /**
         * Check if category has another affected category ids (category move result)
         */
        if ($category->getAffectedCategoryIds()) {
            $event->addNewData('affected_category_ids', $category->getAffectedCategoryIds());
        }
    }

    /**
     * Process event data and save to index
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!empty($data['score_category_oggetto_reindex_all'])) {
            $this->reindexAll();
        }
        if (empty($data['score_category_oggetto_skip_call_event_handler'])) {
            $this->callEventHandler($event);
        }
    }
}
