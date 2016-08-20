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
 * @package     Mage_Scoretag
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Scoretag Indexer Model
 *
 * @method Mage_Scoretag_Model_Resource_Indexer_Summary _getResource()
 * @method Mage_Scoretag_Model_Resource_Indexer_Summary getResource()
 * @method int getScoretagId()
 * @method Mage_Scoretag_Model_Indexer_Summary setScoretagId(int $value)
 * @method int getStoreId()
 * @method Mage_Scoretag_Model_Indexer_Summary setStoreId(int $value)
 * @method int getCustomers()
 * @method Mage_Scoretag_Model_Indexer_Summary setCustomers(int $value)
 * @method int getOggettos()
 * @method Mage_Scoretag_Model_Indexer_Summary setOggettos(int $value)
 * @method int getUses()
 * @method Mage_Scoretag_Model_Indexer_Summary setUses(int $value)
 * @method int getHistoricalUses()
 * @method Mage_Scoretag_Model_Indexer_Summary setHistoricalUses(int $value)
 * @method int getPopularity()
 * @method Mage_Scoretag_Model_Indexer_Summary setPopularity(int $value)
 * @method int getBasePopularity()
 * @method Mage_Scoretag_Model_Indexer_Summary setBasePopularity(int $value)
 *
 * @category    Mage
 * @package     Mage_Scoretag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Scoretag_Model_Indexer_Summary extends Mage_Index_Model_Indexer_Abstract
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
        Mage_Scoretag_Model_Scoretag::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        ),
        Mage_Scoretag_Model_Scoretag_Relation::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE
        )
    );

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('scoretag/indexer_summary');
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('scoretag')->__('Scoretag Aggregation Data');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return Mage::helper('scoretag')->__('Rebuild Scoretag aggregation data');
    }

    /**
     * Retrieve attribute list that has an effect on scoretags
     *
     * @return array
     */
    protected function _getOggettoAttributesDependOn()
    {
        return array(
            'visibility',
            'status',
            'website_ids'
        );
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getEntity() == Shaurmalab_Score_Model_Oggetto::ENTITY) {
            $this->_registerScoreOggetto($event);
        } elseif ($event->getEntity() == Mage_Scoretag_Model_Scoretag::ENTITY) {
            $this->_registerScoretag($event);
        } elseif ($event->getEntity() == Mage_Scoretag_Model_Scoretag_Relation::ENTITY) {
            $this->_registerScoretagRelation($event);
        }
    }

    /**
     * Register data required by score oggetto save process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerScoreOggettoSaveEvent(Mage_Index_Model_Event $event)
    {
        /* @var $oggetto Mage_Score_Model_Oggetto */
        $oggetto = $event->getDataObject();
        $reindexScoretag = $oggetto->getForceReindexRequired();

        foreach ($this->_getOggettoAttributesDependOn() as $attributeCode) {
            $reindexScoretag = $reindexScoretag || $oggetto->dataHasChangedFor($attributeCode);
        }

        if (!$oggetto->isObjectNew() && $reindexScoretag) {
            $event->addNewData('scoretag_reindex_required', true);
        }
    }

    /**
     * Register data required by score oggetto delete process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerScoreOggettoDeleteEvent(Mage_Index_Model_Event $event)
    {
        $scoretagIds = Mage::getModel('scoretag/scoretag_relation')
            ->setOggettoId($event->getEntityPk())
            ->getRelatedScoretagIds();
        if ($scoretagIds) {
            $event->addNewData('scoretag_reindex_scoretag_ids', $scoretagIds);
        }
    }

    /**
     * Register data required by score oggetto massaction process
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerScoreOggettoMassActionEvent(Mage_Index_Model_Event $event)
    {
        /* @var $actionObject Varien_Object */
        $actionObject = $event->getDataObject();
        $attributes   = $this->_getOggettoAttributesDependOn();
        $reindexScoretags  = false;

        // check if attributes changed
        $attrData = $actionObject->getAttributesData();
        if (is_array($attrData)) {
            foreach ($attributes as $attributeCode) {
                if (array_key_exists($attributeCode, $attrData)) {
                    $reindexScoretags = true;
                    break;
                }
            }
        }

        // check changed websites
        if ($actionObject->getWebsiteIds()) {
            $reindexScoretags = true;
        }

        // register affected scoretags
        if ($reindexScoretags) {
            $scoretagIds = Mage::getModel('scoretag/scoretag_relation')
                ->setOggettoId($actionObject->getOggettoIds())
                ->getRelatedScoretagIds();
            if ($scoretagIds) {
                $event->addNewData('scoretag_reindex_scoretag_ids', $scoretagIds);
            }
        }
    }

    protected function _registerScoreOggetto(Mage_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                $this->_registerScoreOggettoSaveEvent($event);
                break;

            case Mage_Index_Model_Event::TYPE_DELETE:
                $this->_registerScoreOggettoDeleteEvent($event);
                break;

            case Mage_Index_Model_Event::TYPE_MASS_ACTION:
                $this->_registerScoreOggettoMassActionEvent($event);
                break;
        }
    }

    protected function _registerScoretag(Mage_Index_Model_Event $event)
    {
        if ($event->getType() == Mage_Index_Model_Event::TYPE_SAVE) {
            $event->addNewData('scoretag_reindex_scoretag_id', $event->getEntityPk());
        }
    }

    protected function _registerScoretagRelation(Mage_Index_Model_Event $event)
    {
        if ($event->getType() == Mage_Index_Model_Event::TYPE_SAVE) {
            $event->addNewData('scoretag_reindex_scoretag_id', $event->getDataObject()->getScoretagId());
        }
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $this->callEventHandler($event);
    }
}
