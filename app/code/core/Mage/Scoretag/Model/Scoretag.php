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
 * Scoretag model
 *
 * @method Mage_Scoretag_Model_Resource_Scoretag _getResource()
 * @method Mage_Scoretag_Model_Resource_Scoretag getResource()
 * @method Mage_Scoretag_Model_Scoretag setName(string $value)
 * @method int getStatus()
 * @method Mage_Scoretag_Model_Scoretag setStatus(int $value)
 * @method int getFirstCustomerId()
 * @method Mage_Scoretag_Model_Scoretag setFirstCustomerId(int $value)
 * @method int getFirstStoreId()
 * @method Mage_Scoretag_Model_Scoretag setFirstStoreId(int $value)
 *
 * @category    Mage
 * @package     Mage_Scoretag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Scoretag_Model_Scoretag extends Mage_Core_Model_Abstract
{
    const STATUS_DISABLED = -1;
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;

    // statuses for scoretag relation add
    const ADD_STATUS_SUCCESS = 'success';
    const ADD_STATUS_NEW = 'new';
    const ADD_STATUS_EXIST = 'exist';
    const ADD_STATUS_REJECTED = 'rejected';

    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY = 'scoretag';

    /**
     * Event prefix for observer
     *
     * @var string
     */
    protected $_eventPrefix = 'scoretag';

    /**
     * This flag means should we or not add base popularity on scoretag load
     *
     * @var bool
     */
    protected $_addBasePopularity = false;

    protected function _construct()
    {
        $this->_init('scoretag/scoretag');
    }

    /**
     * Init indexing process after scoretag data commit
     *
     * @return Mage_Scoretag_Model_Scoretag
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
     * Setter for addBasePopularity flag
     *
     * @param bool $flag
     * @return Mage_Scoretag_Model_Scoretag
     */
    public function setAddBasePopularity($flag = true)
    {
        $this->_addBasePopularity = $flag;
        return $this;
    }

    /**
     * Getter for addBasePopularity flag
     *
     * @return bool
     */
    public function getAddBasePopularity()
    {
        return $this->_addBasePopularity;
    }

    /**
     * Oggetto event scoretags collection getter
     *
     * @param  Varien_Event_Observer $observer
     * @return Mage_Scoretag_Model_Mysql4_Scoretag_Collection
     */
    protected function _getOggettoEventScoretagsCollection(Varien_Event_Observer $observer)
    {
        return $this->getResourceCollection()
                        ->joinRel()
                        ->addOggettoFilter($observer->getEvent()->getOggetto()->getId())
                        ->addScoretagGroup()
                        ->load();
    }

    public function getPopularity()
    {
        return $this->_getData('popularity');
    }

    public function getName()
    {
        return $this->_getData('name');
    }

    public function getScoretagId()
    {
        return $this->_getData('scoretag_id');
    }

    public function getRatio()
    {
        return $this->_getData('ratio');
    }

    public function setRatio($ratio)
    {
        $this->setData('ratio', $ratio);
        return $this;
    }

    public function loadByName($name)
    {
        $this->_getResource()->loadByName($this, $name);
        return $this;
    }

    public function aggregate()
    {
        $this->_getResource()->aggregate($this);
        return $this;
    }

    public function oggettoEventAggregate($observer)
    {
        $this->_getOggettoEventScoretagsCollection($observer)->walk('aggregate');
        return $this;
    }

    /**
     * Oggetto delete event action
     *
     * @param  Varien_Event_Observer $observer
     * @return Mage_Scoretag_Model_Scoretag
     */
    public function oggettoDeleteEventAction($observer)
    {
        $this->_getResource()->decrementOggettos($this->_getOggettoEventScoretagsCollection($observer)->getAllIds());
        return $this;
    }

    /**
     * Add summary data to current object
     *
     * @deprecated after 1.4.0.0
     * @param int $storeId
     * @return Mage_Scoretag_Model_Scoretag
     */
    public function addSummary($storeId)
    {
        $this->setStoreId($storeId);
        $this->_getResource()->addSummary($this);
        return $this;
    }

    /**
     * getter for self::STATUS_APPROVED
     */
    public function getApprovedStatus()
    {
        return self::STATUS_APPROVED;
    }

    /**
     * getter for self::STATUS_PENDING
     */
    public function getPendingStatus()
    {
        return self::STATUS_PENDING;
    }

    /**
     * getter for self::STATUS_DISABLED
     */
    public function getDisabledStatus()
    {
        return self::STATUS_DISABLED;
    }

    public function getEntityCollection()
    {
        return Mage::getResourceModel('scoretag/oggetto_collection');
    }

    public function getCustomerCollection()
    {
        return Mage::getResourceModel('scoretag/customer_collection');
    }

    public function getScoretaggedOggettosUrl()
    {
        return Mage::getUrl('scoretag/oggetto/list', array('scoretagId' => $this->getScoretagId()));
    }

    public function getViewScoretagUrl()
    {
        return Mage::getUrl('scoretag/customer/view', array('scoretagId' => $this->getScoretagId()));
    }

    public function getEditScoretagUrl()
    {
        return Mage::getUrl('scoretag/customer/edit', array('scoretagId' => $this->getScoretagId()));
    }

    public function getRemoveScoretagUrl()
    {
        return Mage::getUrl('scoretag/customer/remove', array('scoretagId' => $this->getScoretagId()));
    }

    public function getPopularCollection()
    {
        return Mage::getResourceModel('scoretag/popular_collection');
    }

    /**
     * Retrieves array of related oggetto IDs
     *
     * @return array
     */
    public function getRelatedOggettoIds()
    {
        return Mage::getModel('scoretag/scoretag_relation')
            ->setScoretagId($this->getScoretagId())
            ->setStoreId($this->getStoreId())
            ->setStatusFilter($this->getStatusFilter())
            ->setCustomerId(null)
            ->getOggettoIds();
    }

    /**
     * Checks is available current scoretag in specified store
     *
     * @param int $storeId
     * @return bool
     */
    public function isAvailableInStore($storeId = null)
    {
        $storeId = (is_null($storeId)) ? Mage::app()->getStore()->getId() : $storeId;
        return in_array($storeId, $this->getVisibleInStoreIds());
    }

    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * Save scoretag relation with oggetto, customer and store
     *
     * @param int $oggettoId
     * @param int $customerId
     * @param int $storeId
     * @return string - relation add status
     */
    public function saveRelation($oggettoId, $customerId, $storeId)
    {
        /** @var $relationModel Mage_Scoretag_Model_Scoretag_Relation */
        $relationModel = Mage::getModel('scoretag/scoretag_relation');
        $relationModel->setScoretagId($this->getId())
            ->setStoreId($storeId)
            ->setOggettoId($oggettoId)
            ->setCustomerId($customerId)
            ->setActive(Mage_Scoretag_Model_Scoretag_Relation::STATUS_ACTIVE)
            ->setCreatedAt($relationModel->getResource()->formatDate(time()));

        $relationModelSaveNeed = false;
        switch($this->getStatus()) {
            case $this->getApprovedStatus():
                if($this->_checkLinkBetweenScoretagOggetto($relationModel)) {
                    $relation = $this->_getLinkBetweenScoretagCustomerOggetto($relationModel);
                    if ($relation->getId()) {
                        if (!$relation->getActive()) {
                            // activate relation if it was inactive
                            $relationModel->setId($relation->getId());
                            $relationModelSaveNeed = true;
                        }
                    } else {
                        $relationModelSaveNeed = true;
                    }
                    $result = self::ADD_STATUS_EXIST;
                } else {
                    $relationModelSaveNeed = true;
                    $result = self::ADD_STATUS_SUCCESS;
                }
                break;
            case $this->getPendingStatus():
                $relation = $this->_getLinkBetweenScoretagCustomerOggetto($relationModel);
                if ($relation->getId()) {
                    if (!$relation->getActive()) {
                        $relationModel->setId($relation->getId());
                        $relationModelSaveNeed = true;
                    }
                } else {
                    $relationModelSaveNeed = true;
                }
                $result = self::ADD_STATUS_NEW;
                break;
            case $this->getDisabledStatus():
                if($this->_checkLinkBetweenScoretagCustomerOggetto($relationModel)) {
                    $result = self::ADD_STATUS_REJECTED;
                } else {
                    $this->setStatus($this->getPendingStatus())->save();
                    $relationModelSaveNeed = true;
                    $result = self::ADD_STATUS_NEW;
                }
                break;
        }
        if ($relationModelSaveNeed) {
            $relationModel->save();
        }

        return $result;
    }

    /**
     * Check whether oggetto is already marked in store with scoretag
     *
     * @param Mage_Scoretag_Model_Scoretag_Relation $relationModel
     * @return boolean
     */
    protected function _checkLinkBetweenScoretagOggetto($relationModel)
    {
        $customerId = $relationModel->getCustomerId();
        $relationModel->setCustomerId(null);
        $result = in_array($relationModel->getOggettoId(), $relationModel->getOggettoIds());
        $relationModel->setCustomerId($customerId);
        return $result;
    }

    /**
     * Check whether oggetto is already marked in store with scoretag by customer
     *
     * @param Mage_Scoretag_Model_Scoretag_Relation $relationModel
     * @return bool
     */
    protected function _checkLinkBetweenScoretagCustomerOggetto($relationModel)
    {
        return (count($this->_getLinkBetweenScoretagCustomerOggetto($relationModel)->getOggettoIds()) > 0);
    }

    /**
     * Get relation model for oggetto marked in store with scoretag by customer
     *
     * @param Mage_Scoretag_Model_Scoretag_Relation $relationModel
     * @return Mage_Scoretag_Model_Scoretag_Relation
     */
    protected function _getLinkBetweenScoretagCustomerOggetto($relationModel)
    {
        return Mage::getModel('scoretag/scoretag_relation')->loadByScoretagCustomer(
            $relationModel->getOggettoId(),
            $this->getId(),
            $relationModel->getCustomerId(),
            $relationModel->getStoreId()
        );
    }

    /**
     * Processing object after save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        if ($this->hasData('scoretag_assigned_oggettos')) {
            $scoretagRelationModel = Mage::getModel('scoretag/scoretag_relation');
            $scoretagRelationModel->addRelations($this, $this->getData('scoretag_assigned_oggettos'));
        }

        return parent::_afterSave();
    }

}
