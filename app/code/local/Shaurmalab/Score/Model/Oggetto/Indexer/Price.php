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
 * Enter description here ...
 *
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Price _getResource()
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Indexer_Price getResource()
 * @method Shaurmalab_Score_Model_Oggetto_Indexer_Price setEntityId(int $value)
 * @method int getCustomerGroupId()
 * @method Shaurmalab_Score_Model_Oggetto_Indexer_Price setCustomerGroupId(int $value)
 * @method int getWebsiteId()
 * @method Shaurmalab_Score_Model_Oggetto_Indexer_Price setWebsiteId(int $value)
 * @method int getTaxClassId()
 * @method Shaurmalab_Score_Model_Oggetto_Indexer_Price setTaxClassId(int $value)
 * @method float getPrice()
 * @method Shaurmalab_Score_Model_Oggetto_Indexer_Price setPrice(float $value)
 * @method float getFinalPrice()
 * @method Shaurmalab_Score_Model_Oggetto_Indexer_Price setFinalPrice(float $value)
 * @method float getMinPrice()
 * @method Shaurmalab_Score_Model_Oggetto_Indexer_Price setMinPrice(float $value)
 * @method float getMaxPrice()
 * @method Shaurmalab_Score_Model_Oggetto_Indexer_Price setMaxPrice(float $value)
 * @method float getTierPrice()
 * @method Shaurmalab_Score_Model_Oggetto_Indexer_Price setTierPrice(float $value)
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Indexer_Price extends Mage_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'score_oggetto_price_match_result';

    /**
     * Reindex price event type
     */
    const EVENT_TYPE_REINDEX_PRICE = 'score_reindex_price';

    /**
     * Matched Entities instruction array
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Shaurmalab_Score_Model_Oggetto::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE,
            Mage_Index_Model_Event::TYPE_MASS_ACTION,
            self::EVENT_TYPE_REINDEX_PRICE,
        ),
        Mage_Core_Model_Config_Data::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Shaurmalab_Score_Model_Convert_Adapter_Oggetto::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Customer_Model_Group::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        )
    );

    protected $_relatedConfigSettings = array(
        Shaurmalab_Score_Helper_Data::XML_PATH_PRICE_SCOPE,
        Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK
    );

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_indexer_price');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('score')->__('Oggetto Prices');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('score')->__('Index oggetto prices');
    }

    /**
     * Retrieve attribute list has an effect on oggetto price
     *
     * @return array
     */
    protected function _getDependentAttributes()
    {
        return array(
            'price',
            'special_price',
            'special_from_date',
            'special_to_date',
            'tax_class_id',
            'status',
            'required_options',
            'force_reindex_required'
        );
    }

    /**
     * Check if event can be matched by process.
     * Rewrited for checking configuration settings save (like price scope).
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

        if ($event->getEntity() == Mage_Core_Model_Config_Data::ENTITY) {
            $data = $event->getDataObject();
            if ($data && in_array($data->getPath(), $this->_relatedConfigSettings)) {
                $result = $data->isValueChanged();
            } else {
                $result = false;
            }
        } elseif ($event->getEntity() == Mage_Customer_Model_Group::ENTITY) {
            $result = $event->getDataObject() && $event->getDataObject()->isObjectNew();
        } else {
            $result = parent::matchEvent($event);
        }

        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);

        return $result;
    }

    /**
     * Register data required by score oggetto delete process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerScoreOggettoDeleteEvent(Mage_Index_Model_Event $event)
    {
        /* @var $oggetto Shaurmalab_Score_Model_Oggetto */
        $oggetto = $event->getDataObject();

        $parentIds = $this->_getResource()->getOggettoParentsByChild($oggetto->getId());
        if ($parentIds) {
            $event->addNewData('reindex_price_parent_ids', $parentIds);
        }
    }

    /**
     * Register data required by score oggetto save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerScoreOggettoSaveEvent(Mage_Index_Model_Event $event)
    {
        /* @var $oggetto Shaurmalab_Score_Model_Oggetto */
        $oggetto      = $event->getDataObject();
        $attributes   = $this->_getDependentAttributes();
        $reindexPrice = $oggetto->getIsRelationsChanged() || $oggetto->getIsCustomOptionChanged()
            || $oggetto->dataHasChangedFor('tier_price_changed')
            || $oggetto->getIsChangedWebsites()
            || $oggetto->getForceReindexRequired();

        foreach ($attributes as $attributeCode) {
            $reindexPrice = $reindexPrice || $oggetto->dataHasChangedFor($attributeCode);
        }

        if ($reindexPrice) {
            $event->addNewData('oggetto_type_id', $oggetto->getTypeId());
            $event->addNewData('reindex_price', 1);
        }
    }

    protected function _registerScoreOggettoMassActionEvent(Mage_Index_Model_Event $event)
    {
        /* @var $actionObject Varien_Object */
        $actionObject = $event->getDataObject();
        $attributes   = $this->_getDependentAttributes();
        $reindexPrice = false;

        // check if attributes changed
        $attrData = $actionObject->getAttributesData();
        if (is_array($attrData)) {
            foreach ($attributes as $attributeCode) {
                if (array_key_exists($attributeCode, $attrData)) {
                    $reindexPrice = true;
                    break;
                }
            }
        }

        // check changed websites
        if ($actionObject->getWebsiteIds()) {
            $reindexPrice = true;
        }

        // register affected oggettos
        if ($reindexPrice) {
            $event->addNewData('reindex_price_oggetto_ids', $actionObject->getOggettoIds());
        }
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

        if ($entity == Mage_Core_Model_Config_Data::ENTITY || $entity == Mage_Customer_Model_Group::ENTITY) {
            $process = $event->getProcess();
            $process->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        } else if ($entity == Shaurmalab_Score_Model_Convert_Adapter_Oggetto::ENTITY) {
            $event->addNewData('score_oggetto_price_reindex_all', true);
        } else if ($entity == Shaurmalab_Score_Model_Oggetto::ENTITY) {
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
                case self::EVENT_TYPE_REINDEX_PRICE:
                    $event->addNewData('id', $event->getDataObject()->getId());
                    break;
            }

            // call oggetto type indexers registerEvent
            $indexers = $this->_getResource()->getTypeIndexers();
            foreach ($indexers as $indexer) {
                $indexer->registerEvent($event);
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
        if ($event->getType() == self::EVENT_TYPE_REINDEX_PRICE) {
            $this->_getResource()->reindexOggettoIds($data['id']);
            return;
        }
        if (!empty($data['score_oggetto_price_reindex_all'])) {
            $this->reindexAll();
        }
        if (empty($data['score_oggetto_price_skip_call_event_handler'])) {
            $this->callEventHandler($event);
        }
    }
}
