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
 * API2 Website Validator
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Api2_Oggetto_Website_Validator_Admin_Website extends Mage_Api2_Model_Resource_Validator
{
    /**
     * Validate data for website assignment to oggetto.
     * If fails validation, then this method returns false, and
     * getErrors() will return an array of errors that explain why the
     * validation failed.
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param array $data
     * @return bool
     */
    public function isValidDataForWebsiteAssignmentToOggetto(Shaurmalab_Score_Model_Oggetto $oggetto, array $data)
    {
        // Validate website id
        if (!isset($data['website_id']) || !is_numeric($data['website_id'])) {
            $this->_addError('Invalid value for "website_id" in request.');
            return false;
        }

        // Validate website
        /* @var $website Mage_Core_Model_Website */
        $website = Mage::getModel('core/website')->load($data['website_id']);
        if (!$website->getId()) {
            $this->_addError(sprintf('Website #%d not found.', $data['website_id']));
            return false;
        }

        // Validate oggetto to website association
        if (in_array($website->getId(), $oggetto->getWebsiteIds())) {
            $this->_addError(sprintf('Oggetto #%d is already assigned to website #%d', $oggetto->getId(),
                $website->getId()));
            return false;
        }

        // Validate "Copy To Stores" data and associations
        $this->_addErrorsIfCopyToStoresDataIsNotValid($oggetto, $website, $data);

        return !count($this->getErrors());
    }

    /**
     * Validate "Copy To Stores" data and associations.
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param Mage_Core_Model_Website $website
     * @param array $data
     * @return \Shaurmalab_Score_Model_Api2_Oggetto_Website_Validator_Admin_Website
     */
    protected function _addErrorsIfCopyToStoresDataIsNotValid($oggetto, $website, $data)
    {
        if (isset($data['copy_to_stores'])) {
            foreach ($data['copy_to_stores'] as $storeData) {
                $this->_checkStoreFrom($oggetto, $website, $storeData);
                $this->_checkStoreTo($website, $storeData);
            }
        }
        return $this;
    }

    /**
     * Check if it possible to copy from store "store_from"
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param Mage_Core_Model_Website $website
     * @param array $storeData
     * @return \Shaurmalab_Score_Model_Api2_Oggetto_Website_Validator_Admin_Website
     */
    protected function _checkStoreFrom($oggetto, $website, $storeData)
    {
        if (!isset($storeData['store_from']) || !is_numeric($storeData['store_from'])) {
            $this->_addError(sprintf('Invalid value for "store_from" for the website with ID #%d.',
                $website->getId()));
            return $this;
        }

        // Check if the store with the specified ID (from which we will copy the information) exists
        // and if it belongs to the oggetto being edited
        $storeFrom = Mage::getModel('core/store')->load($storeData['store_from']);
        if (!$storeFrom->getId()) {
            $this->_addError(sprintf('Store not found #%d for website #%d.', $storeData['store_from'],
                $website->getId()));
            return $this;
        }

        if (!in_array($storeFrom->getId(), $oggetto->getStoreIds())) {
            $this->_addError(sprintf('Store #%d from which we will copy the information does not belong'
                . ' to the oggetto #%d being edited.', $storeFrom->getId(), $oggetto->getId()));
        }

        return $this;
    }

    /**
     * Check if it possible to copy into store "store_to"
     *
     * @param Mage_Core_Model_Website $website
     * @param array $storeData
     * @return \Shaurmalab_Score_Model_Api2_Oggetto_Website_Validator_Admin_Website
     */
    protected function _checkStoreTo($website, $storeData)
    {
        if (!isset($storeData['store_to']) || !is_numeric($storeData['store_to'])) {
            $this->_addError(sprintf('Invalid value for "store_to" for the website with ID #%d.',
                $website->getId()));
            return $this;
        }

        // Check if the store with the specified ID (to which we will copy the information) exists
        // and if it belongs to the website being added
        $storeTo = Mage::getModel('core/store')->load($storeData['store_to']);
        if (!$storeTo->getId()) {
            $this->_addError(sprintf('Store not found #%d for website #%d.', $storeData['store_to'],
                $website->getId()));
            return $this;
        }

        if (!in_array($storeTo->getId(), $website->getStoreIds())) {
            $this->_addError(sprintf('Store #%d to which we will copy the information does not belong'
                . ' to the website #%d being added.', $storeTo->getId(), $website->getId()));
        }

        return $this;
    }

    /**
     * Validate is valid association for website unassignment from oggetto.
     * If fails validation, then this method returns false, and
     * getErrors() will return an array of errors that explain why the
     * validation failed.
     *
     * @param Mage_Core_Model_Website $website
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return bool
     */
    public function isWebsiteAssignedToOggetto(Mage_Core_Model_Website $website, Shaurmalab_Score_Model_Oggetto $oggetto)
    {
        if (false === array_search($website->getId(), $oggetto->getWebsiteIds())) {
            $this->_addError(sprintf('Oggetto #%d isn\'t assigned to website #%d', $oggetto->getId(),
                $website->getId()));
        }
        return !count($this->getErrors());
    }
}
