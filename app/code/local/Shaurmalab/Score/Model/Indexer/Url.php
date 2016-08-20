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
 * Score url rewrites index model.
 * Responsibility for system actions:
 *  - Oggetto save (changed assigned categories list, assigned websites or url key)
 *  - Category save (changed assigned oggettos list, category move, changed url key)
 *  - Store save (new store creation, changed store group) - require reindex all data
 *  - Store group save (changed root category or group website) - require reindex all data
 *  - Seo config settings change - require reindex all data
 */
class Shaurmalab_Score_Model_Indexer_Url extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'score_url_match_result';

    /**
     * Index math: oggetto save, category save, store save
     * store group save, config save
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Shaurmalab_Score_Model_Oggetto::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
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
        Mage_Core_Model_Config_Data::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Shaurmalab_Score_Model_Convert_Adapter_Oggetto::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        )
    );

    protected $_relatedConfigSettings = array(
        Shaurmalab_Score_Helper_Category::XML_PATH_CATEGORY_URL_SUFFIX,
        Shaurmalab_Score_Helper_Oggetto::XML_PATH_OGGETTO_URL_SUFFIX,
        Shaurmalab_Score_Helper_Oggetto::XML_PATH_OGGETTO_URL_USE_CATEGORY,
    );

    /**
     * Get Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('score')->__('Objects URL Rewrites');
    }

    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('score')->__('Index Objects URL rewrites');
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
        $data       = $event->getNewData();
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
        } else if ($entity == Mage_Core_Model_Store_Group::ENTITY) {
            $storeGroup = $event->getDataObject();
            $hasDataChanges = $storeGroup && ($storeGroup->dataHasChangedFor('root_category_id')
                || $storeGroup->dataHasChangedFor('website_id'));
            if ($storeGroup && !$storeGroup->isObjectNew() && $hasDataChanges) {
                $result = true;
            } else {
                $result = false;
            }
        } else if ($entity == Mage_Core_Model_Config_Data::ENTITY) {
            $configData = $event->getDataObject();
            if ($configData && in_array($configData->getPath(), $this->_relatedConfigSettings)) {
                $result = $configData->isValueChanged();
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
        $entity = $event->getEntity();
        switch ($entity) {
            case Shaurmalab_Score_Model_Oggetto::ENTITY:
               $this->_registerOggettoEvent($event);
                break;

            case Shaurmalab_Score_Model_Category::ENTITY:
                $this->_registerCategoryEvent($event);
                break;

            case Shaurmalab_Score_Model_Convert_Adapter_Oggetto::ENTITY:
                $event->addNewData('score_url_reindex_all', true);
                break;

            case Mage_Core_Model_Store::ENTITY:
            case Mage_Core_Model_Store_Group::ENTITY:
            case Mage_Core_Model_Config_Data::ENTITY:
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
        $oggetto = $event->getDataObject();
        $dataChange = $oggetto->dataHasChangedFor('url_key')
            || $oggetto->getIsChangedCategories()
            || $oggetto->getIsChangedWebsites();

        if (!$oggetto->getExcludeUrlRewrite() && $dataChange) {
            $event->addNewData('rewrite_oggetto_ids', array($oggetto->getId()));
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
        if (!$category->getInitialSetupFlag() && $category->getLevel() > 1) {
            if ($category->dataHasChangedFor('url_key') || $category->getIsChangedOggettoList()) {
                $event->addNewData('rewrite_category_ids', array($category->getId()));
            }
            /**
             * Check if category has another affected category ids (category move result)
             */
            if ($category->getAffectedCategoryIds()) {
                $event->addNewData('rewrite_category_ids', $category->getAffectedCategoryIds());
            }
        }
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!empty($data['score_url_reindex_all'])) {
            $this->reindexAll();
        }

        /* @var $urlModel Shaurmalab_Score_Model_Url */
        $urlModel = Mage::getSingleton('score/url');

        // Force rewrites history saving
        $dataObject = $event->getDataObject();
        if ($dataObject instanceof Varien_Object && $dataObject->hasData('save_rewrites_history')) {
            $urlModel->setShouldSaveRewritesHistory($dataObject->getData('save_rewrites_history'));
        }
        if(isset($data['rewrite_oggetto_ids'])) {
            $urlModel->clearStoreInvalidRewrites(); // Maybe some oggettos were moved or removed from website
            foreach ($data['rewrite_oggetto_ids'] as $oggettoId) {
                 $urlModel->refreshOggettoRewrite($oggettoId);
            }
        }
        if (isset($data['rewrite_category_ids'])) {
            $urlModel->clearStoreInvalidRewrites(); // Maybe some categories were moved
            foreach ($data['rewrite_category_ids'] as $categoryId) {
                $urlModel->refreshCategoryRewrite($categoryId);
            }
        }
    }

    /**
     * Rebuild all index data
     */
    public function reindexAll()
    {
        /** @var $resourceModel Shaurmalab_Score_Model_Resource_Url */
        $resourceModel = Mage::getResourceSingleton('score/url');
        $resourceModel->beginTransaction();
        try {
            Mage::getSingleton('score/url')->refreshRewrites();
            $resourceModel->commit();
        } catch (Exception $e) {
            $resourceModel->rollBack();
            throw $e;
        }
    }
}
