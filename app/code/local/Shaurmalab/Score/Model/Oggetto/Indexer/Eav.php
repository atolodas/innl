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
 * Score Oggetto Eav Indexer Model
 *
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Eav _getResource()
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Eav getResource()
 * @method Shaurmalab_Score_Model_Oggetto_Indexer_Eav setEntityId(int $value)
 * @method int getAttributeId()
 * @method Shaurmalab_Score_Model_Oggetto_Indexer_Eav setAttributeId(int $value)
 * @method int getStoreId()
 * @method Shaurmalab_Score_Model_Oggetto_Indexer_Eav setStoreId(int $value)
 * @method int getValue()
 * @method Shaurmalab_Score_Model_Oggetto_Indexer_Eav setValue(int $value)
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Indexer_Eav extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * @var array
     */
    protected $_matchedEntities = array(
        Shaurmalab_Score_Model_Oggetto::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
        ),
        Shaurmalab_Score_Model_Resource_Eav_Attribute::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
        ),
        Shaurmalab_Score_Model_Convert_Adapter_Oggetto::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        )
    );

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('score')->__('Oggetto Attributes');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('score')->__('Index oggetto attributes for layered navigation building');
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_indexer_eav');
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $entity = $event->getEntity();

        if ($entity == Shaurmalab_Score_Model_Oggetto::ENTITY) {
            switch ($event->getType()) {
                case Mage_Index_Model_Event::TYPE_DELETE:
                    $this->_registerScoreOggettoDeleteEvent($event);
                    break;

                case Mage_Index_Model_Event::TYPE_SAVE:
                    $this->_registerScoreOggettoSaveEvent($event);
                    break;

                case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                    $this->_registerScoreOggettoMassActionEvent($event);
                    break;
            }
        } else if ($entity == Shaurmalab_Score_Model_Resource_Eav_Attribute::ENTITY) {
            switch ($event->getType()) {
                case Mage_Index_Model_Event::TYPE_SAVE:
                    $this->_registerScoreAttributeSaveEvent($event);
                    break;
            }
        } else if ($entity == Shaurmalab_Score_Model_Convert_Adapter_Oggetto::ENTITY) {
            $event->addNewData('score_oggetto_eav_reindex_all', true);
        }
    }

    /**
     * Check is attribute indexable in EAV
     *
     * @param Shaurmalab_Score_Model_Resource_Eav_Attribute|string $attribute
     * @return bool
     */
    protected function _attributeIsIndexable($attribute)
    {
        if (!$attribute instanceof Shaurmalab_Score_Model_Resource_Eav_Attribute) {
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Shaurmalab_Score_Model_Oggetto::ENTITY, $attribute);
        }

        return $attribute->isIndexable();
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_Score_Model_Oggetto_Indexer_Eav
     */
    protected function _registerScoreOggettoSaveEvent(Mage_Index_Model_Event $event)
    {
        /* @var $oggetto Shaurmalab_Score_Model_Oggetto */
        $oggetto    = $event->getDataObject();
        $attributes = $oggetto->getAttributes();
        $reindexEav = $oggetto->getForceReindexRequired();
        foreach ($attributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            if ($this->_attributeIsIndexable($attribute) && $oggetto->dataHasChangedFor($attributeCode)) {
                $reindexEav = true;
                break;
            }
        }

        if ($reindexEav) {
            $event->addNewData('reindex_eav', $reindexEav);
        }

        return $this;
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_Score_Model_Oggetto_Indexer_Eav
     */
    protected function _registerScoreOggettoDeleteEvent(Mage_Index_Model_Event $event)
    {
        /* @var $oggetto Shaurmalab_Score_Model_Oggetto */
        $oggetto    = $event->getDataObject();

        $parentIds  = $this->_getResource()->getRelationsByChild($oggetto->getId());
        if ($parentIds) {
            $event->addNewData('reindex_eav_parent_ids', $parentIds);
        }

        return $this;
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_Score_Model_Oggetto_Indexer_Eav
     */
    protected function _registerScoreOggettoMassActionEvent(Mage_Index_Model_Event $event)
    {
        $reindexEav = false;

        /* @var $actionObject Varien_Object */
        $actionObject = $event->getDataObject();
        // check if attributes changed
        $attrData = $actionObject->getAttributesData();
        if (is_array($attrData)) {
            foreach (array_keys($attrData) as $attributeCode) {
                if ($this->_attributeIsIndexable($attributeCode)) {
                    $reindexEav = true;
                    break;
                }
            }
        }

        // check changed websites
        if ($actionObject->getWebsiteIds()) {
            $reindexEav = true;
        }

        // register affected oggettos
        if ($reindexEav) {
            $event->addNewData('reindex_eav_oggetto_ids', $actionObject->getOggettoIds());
        }

        return $this;
    }

    /**
     * Register data required by process attribute save in event object
     *
     * @param Mage_Index_Model_Event $event
     * @return Shaurmalab_Score_Model_Oggetto_Indexer_Eav
     */
    protected function _registerScoreAttributeSaveEvent(Mage_Index_Model_Event $event)
    {
        /* @var $attribute Shaurmalab_Score_Model_Resource_Eav_Attribute */
        $attribute = $event->getDataObject();
        if ($attribute->isIndexable()) {
            $before = $attribute->getOrigData('is_filterable')
                || $attribute->getOrigData('is_filterable_in_search')
                || $attribute->getOrigData('is_visible_in_advanced_search');
            $after  = $attribute->getData('is_filterable')
                || $attribute->getData('is_filterable_in_search')
                || $attribute->getData('is_visible_in_advanced_search');

            if (!$before && $after || $before && !$after) {
                $event->addNewData('reindex_attribute', 1);
                $event->addNewData('attribute_index_type', $attribute->getIndexType());
                $event->addNewData('is_indexable', $after);
            }
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
        if (!empty($data['score_oggetto_eav_reindex_all'])) {
            $this->reindexAll();
        }
        if (empty($data['score_oggetto_eav_skip_call_event_handler'])) {
            $this->callEventHandler($event);
        }
    }
}
