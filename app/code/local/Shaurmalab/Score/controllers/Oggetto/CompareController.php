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
 * Score comapare controller
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Oggetto_CompareController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action list where need check enabled cookie
     *
     * @var array
     */
    protected $_cookieCheckActions = array('add');

    /**
     * Customer id
     *
     * @var null|int
     */
    protected $_customerId = null;

    public function indexAction()
    {
        $items = $this->getRequest()->getParam('items');

        if ($beforeUrl = $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED)) {
            Mage::getSingleton('score/session')
                ->setBeforeCompareUrl(Mage::helper('core')->urlDecode($beforeUrl));
        }

        if ($items) {
            $items = explode(',', $items);
            $list = Mage::getSingleton('score/oggetto_compare_list');
            $list->addOggettos($items);
            $this->_redirect('*/*/*');
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Add item to compare list
     */
    public function addAction()
    {
        $oggettoId = (int) $this->getRequest()->getParam('oggetto');
        if ($oggettoId
            && (Mage::getSingleton('log/visitor')->getId() || Mage::getSingleton('customer/session')->isLoggedIn())
        ) {
            $oggetto = Mage::getModel('score/oggetto')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($oggettoId);

            if ($oggetto->getId()/* && !$oggetto->isSuper()*/) {
                Mage::getSingleton('score/oggetto_compare_list')->addOggetto($oggetto);
                Mage::getSingleton('score/session')->addSuccess(
                    $this->__('The oggetto %s has been added to comparison list.', Mage::helper('core')->escapeHtml($oggetto->getName()))
                );
                Mage::dispatchEvent('score_oggetto_compare_add_oggetto', array('oggetto'=>$oggetto));
            }

            Mage::helper('score/oggetto_compare')->calculate();
        }

        $this->_redirectReferer();
    }

    /**
     * Remove item from compare list
     */
    public function removeAction()
    {
        if ($oggettoId = (int) $this->getRequest()->getParam('oggetto')) {
            $oggetto = Mage::getModel('score/oggetto')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($oggettoId);

            if($oggetto->getId()) {
                /** @var $item Shaurmalab_Score_Model_Oggetto_Compare_Item */
                $item = Mage::getModel('score/oggetto_compare_item');
                if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
                } elseif ($this->_customerId) {
                    $item->addCustomerData(
                        Mage::getModel('customer/customer')->load($this->_customerId)
                    );
                } else {
                    $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
                }

                $item->loadByOggetto($oggetto);

                if($item->getId()) {
                    $item->delete();
                    Mage::getSingleton('score/session')->addSuccess(
                        $this->__('The oggetto %s has been removed from comparison list.', $oggetto->getName())
                    );
                    Mage::dispatchEvent('score_oggetto_compare_remove_oggetto', array('oggetto'=>$item));
                    Mage::helper('score/oggetto_compare')->calculate();
                }
            }
        }

        if (!$this->getRequest()->getParam('isAjax', false)) {
            $this->_redirectReferer();
        }
    }

    /**
     * Remove all items from comparison list
     */
    public function clearAction()
    {
        $items = Mage::getResourceModel('score/oggetto_compare_item_collection');

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
        }

        /** @var $session Shaurmalab_Score_Model_Session */
        $session = Mage::getSingleton('score/session');

        try {
            $items->clear();
            $session->addSuccess($this->__('The comparison list was cleared.'));
            Mage::helper('score/oggetto_compare')->calculate();
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred while clearing comparison list.'));
        }

        $this->_redirectReferer();
    }

    /**
     * Setter for customer id
     *
     * @param int $id
     * @return Shaurmalab_Score_Oggetto_CompareController
     */
    public function setCustomerId($id)
    {
        $this->_customerId = $id;
        return $this;
    }
}
