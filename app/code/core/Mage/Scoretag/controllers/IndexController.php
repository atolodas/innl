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
 * Scoretag Frontend controller
 *
 * @category   Mage
 * @package    Mage_Scoretag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Scoretag_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Saving scoretag and relation between scoretag, customer, oggetto and store
     */
    public function saveAction()
    {
  //  print_r($this->getRequest()->getParams()); die;
        $customerSession = Mage::getSingleton('customer/session');
        if(!$customerSession->authenticate($this)) {
            return;
        }
        $scoretagName    = (string) $this->getRequest()->getQuery('oggettoScoretagName');
        $oggettoId  = (int)$this->getRequest()->getParam('oggetto');

        if(strlen($scoretagName) && $oggettoId) {
            $session = Mage::getSingleton('catalog/session');
            $oggetto = Mage::getModel('score/oggetto')
                ->load($oggettoId);
            if(!$oggetto->getId()){
                $session->addError($this->__('Unable to save scoretag(s).'));
            } else {
                try {
                    $customerId = $customerSession->getCustomerId();
                    $storeId = Mage::app()->getStore()->getId();

                    $scoretagModel = Mage::getModel('scoretag/scoretag');

                    // added scoretag relation statuses
                    $counter = array(
                        Mage_Scoretag_Model_Scoretag::ADD_STATUS_NEW => array(),
                        Mage_Scoretag_Model_Scoretag::ADD_STATUS_EXIST => array(),
                        Mage_Scoretag_Model_Scoretag::ADD_STATUS_SUCCESS => array(),
                        Mage_Scoretag_Model_Scoretag::ADD_STATUS_REJECTED => array()
                    );

                    $scoretagNamesArr = $this->_cleanScoretags($this->_extractScoretags($scoretagName));
                    foreach ($scoretagNamesArr as $scoretagName) {
                        // unset previously added scoretag data
                        $scoretagModel->unsetData()
                            ->loadByName($scoretagName);

                        if (!$scoretagModel->getId()) {
                            $scoretagModel->setName($scoretagName)
                                ->setFirstCustomerId($customerId)
                                ->setFirstStoreId($storeId)
                                ->setStatus($scoretagModel->getApprovedStatus())
                                ->save();
                        }
                        $relationStatus = $scoretagModel->saveRelation($oggettoId, $customerId, $storeId);
                        $counter[$relationStatus][] = $scoretagName;
                    }
                    $this->_fillMessageBox($counter);
             //   $session->addSuccess($this->__('Saved'));
                } catch (Exception $e) {
                    Mage::logException($e);

                    $session->addError($this->__('Unable to save scoretag(s).'));
                }
            }
        }
        $this->_redirectReferer();
    }

    /**
     * Saving scoretag and relation between scoretag, customer, oggetto and store
     */
    public function saveAjaxAction()
    {
        $customerSession = Mage::getSingleton('customer/session');
        if(!$customerSession->authenticate($this)) {
            return;
        }
        $scoretagName    = (string) $this->getRequest()->getParam('oggettoScoretagName');
        $oggettoId  = (int)$this->getRequest()->getParam('oggetto');

        $response = array();
          $response['messages'] = array();
        if(strlen($scoretagName) && $oggettoId) {
            $session = Mage::getSingleton('catalog/session');
            $oggetto = Mage::getModel('score/oggetto')
                ->load($oggettoId);
            if(!$oggetto->getId()){
               $response['messages']['error'] = $this->__('Unable to save scoretag(s).');
            } else {
                try {
                    $customerId = $customerSession->getCustomerId();
                    $storeId = Mage::app()->getStore()->getId();

                    $scoretagModel = Mage::getModel('scoretag/scoretag');

                    // added scoretag relation statuses
                    $counter = array(
                        Mage_Scoretag_Model_Scoretag::ADD_STATUS_NEW => array(),
                        Mage_Scoretag_Model_Scoretag::ADD_STATUS_EXIST => array(),
                        Mage_Scoretag_Model_Scoretag::ADD_STATUS_SUCCESS => array(),
                        Mage_Scoretag_Model_Scoretag::ADD_STATUS_REJECTED => array()
                    );

                    $scoretagNamesArr = $this->_cleanScoretags($this->_extractScoretags($scoretagName));
                    foreach ($scoretagNamesArr as $scoretagName) {
                        // unset previously added scoretag data
                        $scoretagModel->unsetData()
                            ->loadByName($scoretagName);

                        if (!$scoretagModel->getId()) {
                            $scoretagModel->setName($scoretagName)
                                ->setFirstCustomerId($customerId)
                                ->setFirstStoreId($storeId)
                                ->setStatus($scoretagModel->getApprovedStatus())
                                ->save();
                        }
                        $relationStatus = $scoretagModel->saveRelation($oggettoId, $customerId, $storeId);
                         $response['messages']['success'] = $scoretagModel->getPopularity();
                        $counter[$relationStatus][] = $scoretagName;
                    }



                } catch (Exception $e) {
                    Mage::logException($e);

                      $response['messages']['error'] = $this->__('Unable to save scoretag(s).');
                }
            }
        }
        $response = json_encode($response);
        echo $response;
    }

    /**
     * Checks inputed scoretags on the correctness of symbols and split string to array of scoretags
     *
     * @param string $scoretagNamesInString
     * @return array
     */
    protected function _extractScoretags($scoretagNamesInString)
    {
        return explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $scoretagNamesInString));
    }

    /**
     * Clears the scoretag from the separating characters.
     *
     * @param array $scoretagNamesArr
     * @return array
     */
    protected function _cleanScoretags(array $scoretagNamesArr)
    {
        foreach( $scoretagNamesArr as $key => $scoretagName ) {
            $scoretagNamesArr[$key] = trim($scoretagNamesArr[$key], '\'');
            $scoretagNamesArr[$key] = trim($scoretagNamesArr[$key]);
            if( $scoretagNamesArr[$key] == '' ) {
                unset($scoretagNamesArr[$key]);
            }
        }
        return $scoretagNamesArr;
    }

    /**
     * Fill Message Box by success and notice messages about results of user actions.
     *
     * @param array $counter
     * @return void
     */
    protected function _fillMessageBox($counter)
    {
        $session = Mage::getSingleton('score/session');
        $helper = Mage::helper('core');

        if (count($counter[Mage_Scoretag_Model_Scoretag::ADD_STATUS_NEW])) {
            $session->addSuccess(
                $this->__('%s scoretag(s) have been accepted for moderation.', count($counter[Mage_Scoretag_Model_Scoretag::ADD_STATUS_NEW]))
            );
        }

        if (count($counter[Mage_Scoretag_Model_Scoretag::ADD_STATUS_EXIST])) {
            foreach ($counter[Mage_Scoretag_Model_Scoretag::ADD_STATUS_EXIST] as $scoretagName) {
                $session->addNotice(
                    $this->__('Scoretag "%s" has already been added to the oggetto.' , $helper->escapeHtml($scoretagName))
                );
            }
        }

        if (count($counter[Mage_Scoretag_Model_Scoretag::ADD_STATUS_SUCCESS])) {
            foreach ($counter[Mage_Scoretag_Model_Scoretag::ADD_STATUS_SUCCESS] as $scoretagName) {
                $session->addSuccess(
                    $this->__('Scoretag "%s" has been added to the oggetto.' ,$helper->escapeHtml($scoretagName))
                );
            }
        }

        if (count($counter[Mage_Scoretag_Model_Scoretag::ADD_STATUS_REJECTED])) {
            foreach ($counter[Mage_Scoretag_Model_Scoretag::ADD_STATUS_REJECTED] as $scoretagName) {
                $session->addNotice(
                    $this->__('Scoretag "%s" has been rejected by administrator.' ,$helper->escapeHtml($scoretagName))
                );
            }
        }
    }

}
