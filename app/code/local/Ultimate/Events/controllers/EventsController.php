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
 * Events front contrller
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_EventsController
    extends Mage_Core_Controller_Front_Action {
   
    /**
     * init Events
     * @access protected
     * @return Ultimate_Events_Model_Entity
     * @author Ultimate Module Creator
     */
    protected function _initEvents(){
        $eventsId   = $this->getRequest()->getParam('id', 0);
        $events     = Mage::getModel('ultimate_events/events')
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->load($eventsId);
        if (!$events->getId()){
            return false;
        }
        elseif (!$events->getStatus()){
            return false;
        }
        return $events;
    }
    /**
      * view events action
      * @access public
      * @return void
      * @author Ultimate Module Creator
      */
    public function viewAction(){
        $events = $this->_initEvents();
        // if (!$events) {
        //     $this->_forward('no-route');
        //     return;
        // }
        Mage::register('current_events', $events);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('events-events events-events' . $events->getId());
        }
        if (Mage::helper('ultimate_events/events')->getUseBreadcrumbs()){
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')){
                $breadcrumbBlock->addCrumb('home', array(
                            'label'    => Mage::helper('ultimate_events')->__('Home'),
                            'link'     => Mage::getUrl(),
                        )
                );
                $breadcrumbBlock->addCrumb('eventss', array(
                            'label'    => Mage::helper('ultimate_events')->__('Events'),
                            'link'    => Mage::helper('ultimate_events/events')->getEventssUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb('events', array(
                            'label'    => $events->getTitle(),
                            'link'    => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            if ($events->getMetaTitle()){
                $headBlock->setTitle($events->getMetaTitle());
            }
            else{
                $headBlock->setTitle($events->getTitle());
            }
            $headBlock->setKeywords($events->getMetaKeywords());
            $headBlock->setDescription($events->getMetaDescription());
        }
        $this->renderLayout();
    }
    /**
     * events rss list action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function rssAction(){
        if (Mage::helper('ultimate_events/events')->isRssEnabled()) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
            $this->renderLayout();
        }
        else {
            $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
            $this->getResponse()->setHeader('Status','404 File not found');
            $this->_forward('nofeed','index','rss');
        }
    }
    /**
     * Submit new comment action
     *
     */
    public function commentpostAction() {
        $data   = $this->getRequest()->getPost();
        $events = $this->_initEvents();
        $session    = Mage::getSingleton('core/session');
        if ($events) {
            if ($events->getAllowComments()) {
                if ((Mage::getSingleton('customer/session')->isLoggedIn() || Mage::getStoreConfigFlag('ultimate_events/events/allow_guest_comment'))){
                    $comment    = Mage::getModel('ultimate_events/events_comment')->setData($data);
                    $validate = $comment->validate();
                    if ($validate === true) {
                        try {
                            $comment->setEventsId($events->getId())
                                ->setStatus(Ultimate_Events_Model_Events_Comment::STATUS_PENDING)
                                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                                ->setStores(array(Mage::app()->getStore()->getId()))
                                ->save();
                            $session->addSuccess($this->__('Your comment has been accepted for moderation.'));
                        }
                        catch (Exception $e) {
                            $session->setEventsCommentData($data);
                            $session->addError($this->__('Unable to post the comment.'));
                        }
                    }
                    else {
                        $session->setEventsCommentData($data);
                        if (is_array($validate)) {
                            foreach ($validate as $errorMessage) {
                                $session->addError($errorMessage);
                            }
                        }
                        else {
                            $session->addError($this->__('Unable to post the comment.'));
                        }
                    }
                }
                else {
                    $session->addError($this->__('Guest comments are not allowed'));
                }
            }
            else {
                $session->addError($this->__('This events does not allow comments'));
            }
        }
        $this->_redirectReferer();
    }
}
