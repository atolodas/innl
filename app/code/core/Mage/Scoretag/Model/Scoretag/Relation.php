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
 * Scoretag relation model
 *
 * @method Mage_Scoretag_Model_Resource_Scoretag_Relation _getResource()
 * @method Mage_Scoretag_Model_Resource_Scoretag_Relation getResource()
 * @method int getScoretagId()
 * @method Mage_Scoretag_Model_Scoretag_Relation setScoretagId(int $value)
 * @method int getCustomerId()
 * @method Mage_Scoretag_Model_Scoretag_Relation setCustomerId(int $value)
 * @method int getOggettoId()
 * @method Mage_Scoretag_Model_Scoretag_Relation setOggettoId(int $value)
 * @method int getStoreId()
 * @method Mage_Scoretag_Model_Scoretag_Relation setStoreId(int $value)
 * @method int getActive()
 * @method Mage_Scoretag_Model_Scoretag_Relation setActive(int $value)
 * @method string getCreatedAt()
 * @method Mage_Scoretag_Model_Scoretag_Relation setCreatedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_Scoretag
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Scoretag_Model_Scoretag_Relation extends Mage_Core_Model_Abstract
{
    /**
     * Relation statuses
     */
    const STATUS_ACTIVE     = 1;
    const STATUS_NOT_ACTIVE = 0;

    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY = 'scoretag_relation';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('scoretag/scoretag_relation');
    }

    /**
     * Retrieve Resource Instance wrapper
     *
     * @return Mage_Scoretag_Model_Mysql4_Scoretag_Relation
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Init indexing process after scoretag data commit
     *
     * @return Mage_Scoretag_Model_Scoretag_Relation
     */
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();
        Mage::getSingleton('index/indexer')->processEntityAction(
            $this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
        );
        return $this;
    }

    /**
     * Load relation by Oggetto (optional), scoretag, customer and store
     *
     * @param int $oggettoId
     * @param int $scoretagId
     * @param int $customerId
     * @param int $storeId
     * @return Mage_Scoretag_Model_Scoretag_Relation
     */
    public function loadByScoretagCustomer($oggettoId=null, $scoretagId, $customerId, $storeId=null)
    {
        $this->setOggettoId($oggettoId);
        $this->setScoretagId($scoretagId);
        $this->setCustomerId($customerId);
        if(!is_null($storeId)) {
            $this->setStoreId($storeId);
        }
        $this->_getResource()->loadByScoretagCustomer($this);
        return $this;
    }

    /**
     * Retrieve Relation Oggetto Ids
     *
     * @return array
     */
    public function getOggettoIds()
    {
        $ids = $this->getData('oggetto_ids');
        if (is_null($ids)) {
            $ids = $this->_getResource()->getOggettoIds($this);
            $this->setOggettoIds($ids);
        }
        return $ids;
    }

    /**
     * Retrieve list of related scoretag ids for oggettos specified in current object
     *
     * @return array
     */
    public function getRelatedScoretagIds()
    {
        if (is_null($this->getData('related_scoretag_ids'))) {
            $this->setRelatedScoretagIds($this->_getResource()->getRelatedScoretagIds($this));
        }
        return $this->getData('related_scoretag_ids');
    }

    /**
     * Deactivate scoretag relations (using current settings)
     *
     * @return Mage_Scoretag_Model_Scoretag_Relation
     */
    public function deactivate()
    {
        $this->_getResource()->deactivate($this->getScoretagId(),  $this->getCustomerId());
        return $this;
    }

    /**
     * Add TAG to PRODUCT relations
     *
     * @param Mage_Scoretag_Model_Scoretag $model
     * @param array $oggettoIds
     * @return Mage_Scoretag_Model_Scoretag_Relation
     */
    public function addRelations(Mage_Scoretag_Model_Scoretag $model, $oggettoIds = array())
    {
        $this->setAddedOggettoIds($oggettoIds);
        $this->setScoretagId($model->getScoretagId());
        $this->setCustomerId(null);
        $this->setStoreId($model->getStore());
        $this->_getResource()->addRelations($this);
        return $this;
    }
}
