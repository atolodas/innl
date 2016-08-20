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
 * Oggetto Scoretag API
 *
 * @category   Mage
 * @package    Mage_Scoretag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Scoretag_Model_Api extends Mage_Score_Model_Api_Resource
{
    /**
     * Retrieve list of scoretags for specified oggetto
     *
     * @param int $oggettoId
     * @param string|int $store
     * @return array
     */
    public function items($oggettoId, $store = null)
    {
        $result = array();
        // fields list to return
        $fieldsForResult = array('scoretag_id', 'name');

        /** @var $oggetto Mage_Score_Model_Oggetto */
        $oggetto = Mage::getModel('score/oggetto')->load($oggettoId);
        if (!$oggetto->getId()) {
            $this->_fault('oggetto_not_exists');
        }

        /** @var $scoretags Mage_Scoretag_Model_Resource_Scoretag_Collection */
        $scoretags = Mage::getModel('scoretag/scoretag')->getCollection()->joinRel()->addOggettoFilter($oggettoId);
        if ($store) {
            $scoretags->addStoreFilter($this->_getStoreId($store));
        }

        /** @var $scoretag Mage_Scoretag_Model_Scoretag */
        foreach ($scoretags as $scoretag) {
            $result[$scoretag->getId()] = $scoretag->toArray($fieldsForResult);
        }

        return $result;
    }

    /**
     * Retrieve scoretag info as array('name'-> .., 'status' => ..,
     * 'base_popularity' => .., 'oggettos' => array($oggettoId => $popularity, ...))
     *
     * @param int $scoretagId
     * @param string|int $store
     * @return array
     */
    public function info($scoretagId, $store)
    {
        $result = array();
        $storeId = $this->_getStoreId($store);
        /** @var $scoretag Mage_Scoretag_Model_Scoretag */
        $scoretag = Mage::getModel('scoretag/scoretag')->setStoreId($storeId)->setAddBasePopularity()->load($scoretagId);
        if (!$scoretag->getId()) {
            $this->_fault('scoretag_not_exists');
        }
        $result['status'] = $scoretag->getStatus();
        $result['name'] = $scoretag->getName();
        $result['base_popularity'] = (is_numeric($scoretag->getBasePopularity())) ? $scoretag->getBasePopularity() : 0;
        // retrieve array($oggettoId => $popularity, ...)
        $result['oggettos'] = array();
        $relatedOggettosCollection = $scoretag->getEntityCollection()->addScoretagFilter($scoretagId)
            ->addStoreFilter($storeId)->addPopularity($scoretagId);
        foreach ($relatedOggettosCollection as $oggetto) {
            $result['oggettos'][$oggetto->getId()] = $oggetto->getPopularity();
        }

        return $result;
    }

    /**
     * Add scoretag(s) to oggetto.
     * Return array of added/updated scoretags as array($scoretagName => $scoretagId, ...)
     *
     * @param array $data
     * @return array
     */
    public function add($data)
    {
        $data = $this->_prepareDataForAdd($data);
        /** @var $oggetto Mage_Score_Model_Oggetto */
        $oggetto = Mage::getModel('score/oggetto')->load($data['oggetto_id']);
        if (!$oggetto->getId()) {
            $this->_fault('oggetto_not_exists');
        }
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer')->load($data['customer_id']);
        if (!$customer->getId()) {
            $this->_fault('customer_not_exists');
        }
        $storeId = $this->_getStoreId($data['store']);

        try {
            /** @var $scoretag Mage_Scoretag_Model_Scoretag */
            $scoretag = Mage::getModel('scoretag/scoretag');
            $scoretagNamesArr = Mage::helper('scoretag')->cleanScoretags(Mage::helper('scoretag')->extractScoretags($data['scoretag']));
            foreach ($scoretagNamesArr as $scoretagName) {
                // unset previously added scoretag data
                $scoretag->unsetData();
                $scoretag->loadByName($scoretagName);
                if (!$scoretag->getId()) {
                    $scoretag->setName($scoretagName)
                        ->setFirstCustomerId($customer->getId())
                        ->setFirstStoreId($storeId)
                        ->setStatus($scoretag->getPendingStatus())
                        ->save();
                }
                $scoretag->saveRelation($oggetto->getId(), $customer->getId(), $storeId);
                $result[$scoretagName] = $scoretag->getId();
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return $result;
    }

    /**
     * Change existing scoretag information
     *
     * @param int $scoretagId
     * @param array $data
     * @param string|int $store
     * @return bool
     */
    public function update($scoretagId, $data, $store)
    {
        $data = $this->_prepareDataForUpdate($data);
        $storeId = $this->_getStoreId($store);
        /** @var $scoretag Mage_Scoretag_Model_Scoretag */
        $scoretag = Mage::getModel('scoretag/scoretag')->setStoreId($storeId)->setAddBasePopularity()->load($scoretagId);
        if (!$scoretag->getId()) {
            $this->_fault('scoretag_not_exists');
        }

        // store should be set for 'base_popularity' to be saved in Mage_Scoretag_Model_Resource_Scoretag::_afterSave()
        $scoretag->setStore($storeId);
        if (isset($data['base_popularity'])) {
            $scoretag->setBasePopularity($data['base_popularity']);
        }
        if (isset($data['name'])) {
            $scoretag->setName(trim($data['name']));
        }
        if (isset($data['status'])) {
            // validate scoretag status
            if (!in_array($data['status'], array(
                $scoretag->getApprovedStatus(), $scoretag->getPendingStatus(), $scoretag->getDisabledStatus()))) {
                $this->_fault('invalid_data');
            }
            $scoretag->setStatus($data['status']);
        }

        try {
            $scoretag->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }

    /**
     * Remove existing scoretag
     *
     * @param int $scoretagId
     * @return bool
     */
    public function remove($scoretagId)
    {
        /** @var $scoretag Mage_Scoretag_Model_Scoretag */
        $scoretag = Mage::getModel('scoretag/scoretag')->load($scoretagId);
        if (!$scoretag->getId()) {
            $this->_fault('scoretag_not_exists');
        }
        try {
            $scoretag->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('remove_error', $e->getMessage());
        }

        return true;
    }

    /**
     * Check data before add
     *
     * @param array $data
     * @return array
     */
    protected function _prepareDataForAdd($data)
    {
        if (!isset($data['oggetto_id']) or !isset($data['scoretag'])
            or !isset($data['customer_id']) or !isset($data['store'])) {
            $this->_fault('invalid_data');
        }

        return $data;
    }

    /**
     * Check data before update
     *
     * @param $data
     * @return
     */
    protected function _prepareDataForUpdate($data)
    {
        // $data should contain at least one field to change
        if ( !(isset($data['name']) or isset($data['status']) or isset($data['base_popularity']))) {
            $this->_fault('invalid_data');
        }

        return $data;
    }
}
