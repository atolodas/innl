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
class Ultimate_Events_IndexController
    extends Mage_Core_Controller_Front_Action {
    /**
      * default action
      * @access public
      * @return void
      * @author Ultimate Module Creator
      */
    public function indexAction(){
         $this->loadLayout();
         $this->_initLayoutMessages('catalog/session');
         $this->_initLayoutMessages('customer/session');
         $this->_initLayoutMessages('checkout/session');
         if (Mage::helper('ultimate_events/events')->getUseBreadcrumbs()){
             if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')){
                 $breadcrumbBlock->addCrumb('home', array(
                            'label'    => Mage::helper('ultimate_events')->__('Home'),
                            'link'     => Mage::getUrl(),
                        )
                 );
                 $breadcrumbBlock->addCrumb('eventss', array(
                            'label'    => Mage::helper('ultimate_events')->__('Events'),
                            'link'    => '',
                    )
                 );
             }
         }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('ultimate_events/events/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('ultimate_events/events/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('ultimate_events/events/meta_description'));
        }
        $this->renderLayout();
    }
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
    
}
