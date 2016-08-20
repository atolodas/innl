<?php
/**
 * Ultimate_Events extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Ultimate
 * @package        Ultimate_Events
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Events comments controller
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Events_Customer_CommentController
    extends Mage_Core_Controller_Front_Action {
    /**
     * Action predispatch
     * Check customer authentication for some actions
     * @access public
     * @author Ultimate Module Creator
     */
    public function preDispatch() {
        parent::preDispatch();
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }
    /**
     * List comments
     * @access public
     * @author Ultimate Module Creator
     */
    public function indexAction() {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('ultimate_events/events_customer_comment/');
        }
        if ($block = $this->getLayout()->getBlock('events_customer_comment_list')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->getLayout()->getBlock('head')->setTitle($this->__('My Events Comments'));

        $this->renderLayout();
    }
    /**
     * View comment
     * @access public
     * @author Ultimate Module Creator
     */
    public function viewAction() {
        $commentId = $this->getRequest()->getParam('id');
        $comment = Mage::getModel('ultimate_events/events_comment')->load($commentId);
        if (!$comment->getId() || $comment->getCustomerId() != Mage::getSingleton('customer/session')->getCustomerId() || $comment->getStatus() != Ultimate_Events_Model_Events_Comment::STATUS_APPROVED) {
            $this->_forward('no-route');
            return;
        }
        $events = Mage::getModel('ultimate_events/events')
                ->load($comment->getEventsId());
        if (!$events->getId() || $events->getStatus() != 1){
            $this->_forward('no-route');
            return;
        }
        $stores = array(Mage::app()->getStore()->getId(), 0);
        if (count(array_intersect($stores, $comment->getStoreId())) == 0) {
            $this->_forward('no-route');
            return;
        }
        if (count(array_intersect($stores, $events->getStoreId())) == 0) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_comment', $comment);
        Mage::register('current_events', $events);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('ultimate_events/events_customer_comment/');
        }
        if ($block = $this->getLayout()->getBlock('customer_events_comment')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Events Comments'));
        $this->renderLayout();
    }
}
