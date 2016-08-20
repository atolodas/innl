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
 * ScoreSearch Fulltext Observer
 *
 * @category    Mage
 * @package     Shaurmalab_ScoreSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_ScoreSearch_Model_Fulltext_Observer
{
    /**
     * Retrieve fulltext (indexer) model
     *
     * @return Shaurmalab_ScoreSearch_Model_Fulltext
     */
    protected function _getFulltextModel()
    {
        return Mage::getSingleton('scoresearch/fulltext');
    }

    /**
     * Update oggetto index when oggetto data updated
     *
     * @param Varien_Event_Observer $observer
     * @return Shaurmalab_ScoreSearch_Model_Fulltext_Observer
     */
    public function refreshOggettoIndex(Varien_Event_Observer $observer)
    {
        $oggetto = $observer->getEvent()->getOggetto();

        $this->_getFulltextModel()
            ->rebuildIndex(null, $oggetto->getId())
            ->resetSearchResults();

        return $this;
    }

    /**
     * Clean oggetto index when oggetto deleted or marked as unsearchable/invisible
     *
     * @param Varien_Event_Observer $observer
     * @return Shaurmalab_ScoreSearch_Model_Fulltext_Observer
     */
    public function cleanOggettoIndex(Varien_Event_Observer $observer)
    {
        $oggetto = $observer->getEvent()->getOggetto();

        $this->_getFulltextModel()
            ->cleanIndex(null, $oggetto->getId())
            ->resetSearchResults();

        return $this;
    }

    /**
     * Update all attribute-dependant index
     *
     * @param Varien_Event_Observer $observer
     * @return Shaurmalab_ScoreSearch_Model_Fulltext_Observer
     */
    public function eavAttributeChange(Varien_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        /* @var $attribute Mage_Eav_Model_Entity_Attribute */
        $entityType = Mage::getSingleton('eav/config')->getEntityType(Shaurmalab_Score_Model_Oggetto::ENTITY);
        /* @var $entityType Mage_Eav_Model_Entity_Type */

        if ($attribute->getEntityTypeId() != $entityType->getId()) {
            return $this;
        }
        $delete = $observer->getEventName() == 'eav_entity_attribute_delete_after';

        if (!$delete && !$attribute->dataHasChangedFor('is_searchable')) {
            return $this;
        }

        $showNotice = false;
        if ($delete) {
            if ($attribute->getIsSearchable()) {
                $showNotice = true;
            }
        }
        elseif ($attribute->dataHasChangedFor('is_searchable')) {
            $showNotice = true;
        }

        if ($showNotice) {
            $url = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_cache');
            Mage::getSingleton('adminhtml/session')->addNotice(
                Mage::helper('scoresearch')->__('Attribute setting change related with Search Index. Please run <a href="%s">Rebuild Search Index</a> process.', $url)
            );
        }

        return $this;
    }

    /**
     * Rebuild index after import
     *
     * @return Shaurmalab_ScoreSearch_Model_Fulltext_Observer
     */
    public function refreshIndexAfterImport()
    {
        $this->_getFulltextModel()
            ->rebuildIndex();
        return $this;
    }

    /**
     * Refresh fulltext index when we add new store
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_ScoreSearch_Model_Fulltext_Observer
     */
    public function refreshStoreIndex(Varien_Event_Observer $observer)
    {
        $storeId = $observer->getEvent()->getStore()->getId();
        $this->_getFulltextModel()->rebuildIndex($storeId);
        return $this;
    }

    /**
     * Score Oggetto mass website update
     *
     * @param Varien_Event_Observer $observer
     * @return Shaurmalab_ScoreSearch_Model_Fulltext_Observer
     */
    public function scoreOggettoWebsiteUpdate(Varien_Event_Observer $observer)
    {
        $websiteIds = $observer->getEvent()->getWebsiteIds();
        $oggettoIds = $observer->getEvent()->getOggettoIds();
        $actionType = $observer->getEvent()->getAction();

        foreach ($websiteIds as $websiteId) {
            foreach (Mage::app()->getWebsite($websiteId)->getStoreIds() as $storeId) {
                if ($actionType == 'remove') {
                    $this->_getFulltextModel()
                        ->cleanIndex($storeId, $oggettoIds)
                        ->resetSearchResults();
                }
                elseif ($actionType == 'add') {
                    $this->_getFulltextModel()
                        ->rebuildIndex($storeId, $oggettoIds)
                        ->resetSearchResults();
                }
            }
        }

        return $this;
    }

    /**
     * Store delete processing
     *
     * @param Varien_Event_Observer $observer
     * @return Shaurmalab_ScoreSearch_Model_Fulltext_Observer
     */
    public function cleanStoreIndex(Varien_Event_Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        /* @var $store Mage_Core_Model_Store */

        $this->_getFulltextModel()
            ->cleanIndex($store->getId());

        return $this;
    }
}
