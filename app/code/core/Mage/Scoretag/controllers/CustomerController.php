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
 * Customer scoretags controller
 *
 * @category   Mage
 * @package    Mage_Scoretag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Scoretag_CustomerController extends Mage_Core_Controller_Front_Action
{
    protected function _getScoretagId()
    {
        $scoretagId = (int) $this->getRequest()->getParam('scoretagId');
        if ($scoretagId) {
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $model = Mage::getModel('scoretag/scoretag_relation');
            $model->loadByScoretagCustomer(null, $scoretagId, $customerId);
            Mage::register('scoretagModel', $model);
            return $model->getScoretagId();
        }
        return false;
    }

    public function indexAction()
    {
        if( !Mage::getSingleton('customer/session')->isLoggedIn() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('scoretag/session');
        $this->_initLayoutMessages('score/session');

        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('scoretag/customer');
        }

        $block = $this->getLayout()->getBlock('customer_scoretags');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('scoretag')->__('My Scoretags'));
        $this->renderLayout();
    }

    public function viewAction()
    {
        if( !Mage::getSingleton('customer/session')->isLoggedIn() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $scoretagId = $this->_getScoretagId();
        if ($scoretagId) {
            Mage::register('scoretagId', $scoretagId);
            $this->loadLayout();
            $this->_initLayoutMessages('scoretag/session');

            $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
            if ($navigationBlock) {
                $navigationBlock->setActive('scoretag/customer');
            }

            $this->_initLayoutMessages('checkout/session');
            $this->getLayout()->getBlock('head')->setTitle(Mage::helper('scoretag')->__('My Scoretags'));
            $this->renderLayout();
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * @deprecated after 1.3.2.3
     * This functionality was removed
     *
     */
    public function editAction()
    {
        $this->_forward('noRoute');
    }

    public function removeAction()
    {
        if( !Mage::getSingleton('customer/session')->isLoggedIn() ) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $scoretagId = $this->_getScoretagId();
        if ($scoretagId) {
            try {
                $model = Mage::registry('scoretagModel');
                $model->deactivate();
                $scoretag = Mage::getModel('scoretag/scoretag')->load($scoretagId)->aggregate();
                Mage::getSingleton('scoretag/session')->addSuccess(Mage::helper('scoretag')->__('The scoretag has been deleted.'));
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/', array(
                    self::PARAM_NAME_URL_ENCODED => Mage::helper('core')->urlEncode(Mage::getUrl('customer/account/'))
                )));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('scoretag/session')->addError(Mage::helper('scoretag')->__('Unable to remove scoretag. Please, try again later.'));
            }
        }
        else {
            $this->_forward('noRoute');
        }
    }

    /**
     * @deprecated after 1.3.2.3
     * This functionality was removed
     *
     */
    public function saveAction()
    {
        $this->_forward('noRoute');
    }
}
