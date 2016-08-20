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
 * Score Oggetto Flat Indexer Model
 *
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer _getResource()
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Flat_Indexer getResource()
 * @method int getEntityTypeId()
 * @method Shaurmalab_Score_Model_Oggetto_Flat_Indexer setEntityTypeId(int $value)
 * @method int getAttributeSetId()
 * @method Shaurmalab_Score_Model_Oggetto_Flat_Indexer setAttributeSetId(int $value)
 * @method string getTypeId()
 * @method Shaurmalab_Score_Model_Oggetto_Flat_Indexer setTypeId(string $value)
 * @method string getSku()
 * @method Shaurmalab_Score_Model_Oggetto_Flat_Indexer setSku(string $value)
 * @method int getHasOptions()
 * @method Shaurmalab_Score_Model_Oggetto_Flat_Indexer setHasOptions(int $value)
 * @method int getRequiredOptions()
 * @method Shaurmalab_Score_Model_Oggetto_Flat_Indexer setRequiredOptions(int $value)
 * @method string getCreatedAt()
 * @method Shaurmalab_Score_Model_Oggetto_Flat_Indexer setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Shaurmalab_Score_Model_Oggetto_Flat_Indexer setUpdatedAt(string $value)
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Flat_Indexer extends Mage_Core_Model_Abstract
{
    /**
     * Score oggetto flat entity for indexers
     */
    const ENTITY = 'score_oggetto_flat';

    /**
     * Indexers rebuild event type
     */
    const EVENT_TYPE_REBUILD = 'score_oggetto_flat_rebuild';

    /**
     * Standart model resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_flat_indexer');
    }

    /**
     * Rebuild Score Oggetto Flat Data
     *
     * @param mixed $store
     * @return Shaurmalab_Score_Model_Oggetto_Flat_Indexer
     */
    public function rebuild($store = null)
    {
        if (is_null($store)) {
            $this->_getResource()->prepareFlatTables();
        } else {
            $this->_getResource()->prepareFlatTable($store);
        }
        Mage::getSingleton('index/indexer')->processEntityAction(
            new Varien_Object(array('id' => $store)),
            self::ENTITY,
            self::EVENT_TYPE_REBUILD
        );
        return $this;
    }

    /**
     * Update attribute data for flat table
     *
     * @param string $attributeCode
     * @param int $store
     * @param int|array $oggettoIds
     * @return Shaurmalab_Score_Model_Oggetto_Flat_Indexer
     */
    public function updateAttribute($attributeCode, $store = null, $oggettoIds = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->updateAttribute($attributeCode, $store->getId(), $oggettoIds);
            }

            return $this;
        }

        $this->_getResource()->prepareFlatTable($store);
        $attribute = $this->_getResource()->getAttribute($attributeCode);
        $this->_getResource()->updateAttribute($attribute, $store, $oggettoIds);
        $this->_getResource()->updateChildrenDataFromParent($store, $oggettoIds);

        return $this;
    }

    /**
     * Prepare datastorage for score oggetto flat
     *
     * @param int $store
     * @return Shaurmalab_Score_Model_Oggetto_Flat_Indexer
     */
    public function prepareDataStorage($store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->prepareDataStorage($store->getId());
            }

            return $this;
        }

        $this->_getResource()->prepareFlatTable($store);

        return $this;
    }

    /**
     * Update events observer attributes
     *
     * @param int $store
     * @return Shaurmalab_Score_Model_Oggetto_Flat_Indexer
     */
    public function updateEventAttributes($store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->updateEventAttributes($store->getId());
            }

            return $this;
        }

        $this->_getResource()->prepareFlatTable($store);
        $this->_getResource()->updateEventAttributes($store);
        $this->_getResource()->updateRelationOggettos($store);

        return $this;
    }

    /**
     * Update oggetto status
     *
     * @param int $oggettoId
     * @param int $status
     * @param int $store
     * @return Shaurmalab_Score_Model_Oggetto_Flat_Indexer
     */
    public function updateOggettoStatus($oggettoId, $status, $store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->updateOggettoStatus($oggettoId, $status, $store->getId());
            }
            return $this;
        }

        if ($status == Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED) {
            $this->_getResource()->updateOggetto($oggettoId, $store);
            $this->_getResource()->updateChildrenDataFromParent($store, $oggettoId);
        }
        else {
            $this->_getResource()->removeOggetto($oggettoId, $store);
        }

        return $this;
    }

    /**
     * Update Score Oggetto Flat data
     *
     * @param int|array $oggettoIds
     * @param int $store
     * @return Shaurmalab_Score_Model_Oggetto_Flat_Indexer
     */
    public function updateOggetto($oggettoIds, $store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->updateOggetto($oggettoIds, $store->getId());
            }
            return $this;
        }

        $resource = $this->_getResource();
        $resource->beginTransaction();
        try {
            $resource->removeOggetto($oggettoIds, $store);
            $resource->updateOggetto($oggettoIds, $store);
            $resource->updateRelationOggettos($store, $oggettoIds);
            $resource->commit();
        } catch (Exception $e){
            $resource->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Save Score Oggetto(s) Flat data
     *
     * @param int|array $oggettoIds
     * @param int $store
     * @return Shaurmalab_Score_Model_Oggetto_Flat_Indexer
     */
    public function saveOggetto($oggettoIds, $store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->saveOggetto($oggettoIds, $store->getId());
            }
            return $this;
        }

        $resource = $this->_getResource();
        $resource->beginTransaction();
        try {
            $resource->removeOggetto($oggettoIds, $store);
            $resource->saveOggetto($oggettoIds, $store);
            $resource->updateRelationOggettos($store, $oggettoIds);
            $resource->commit();
        } catch (Exception $e){
            $resource->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Remove oggetto from flat
     *
     * @param int|array $oggettoIds
     * @param int $store
     * @return Shaurmalab_Score_Model_Oggetto_Flat_Indexer
     */
    public function removeOggetto($oggettoIds, $store = null)
    {
        if (is_null($store)) {
            foreach (Mage::app()->getStores() as $store) {
                $this->removeOggetto($oggettoIds, $store->getId());
            }
            return $this;
        }

        $this->_getResource()->removeOggetto($oggettoIds, $store);

        return $this;
    }

    /**
     * Delete store process
     *
     * @param int $store
     * @return Shaurmalab_Score_Model_Oggetto_Flat_Indexer
     */
    public function deleteStore($store)
    {
        $this->_getResource()->deleteFlatTable($store);
        return $this;
    }

    /**
     * Rebuild Score Oggetto Flat Data for all stores
     *
     * @return Shaurmalab_Score_Model_Oggetto_Flat_Indexer
     */
    public function reindexAll()
    {
        $this->_getResource()->reindexAll();
        return $this;
    }

    /**
     * Retrieve list of attribute codes for flat
     *
     * @return array
     */
    public function getAttributeCodes()
    {
        return $this->_getResource()->getAttributeCodes();
    }
}
