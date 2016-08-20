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
 * Events comment list block
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author Ultimate Module Creator
 */
class Ultimate_Events_Block_Events_Comment_List
    extends Mage_Core_Block_Template {
    /**
     * initialize
     * @access public
     * @author Ultimate Module Creator
     */
     public function __construct(){
         parent::__construct();
         $events = $this->getEvents();
         $comments = Mage::getResourceModel('ultimate_events/events_comment_collection')
             ->addFieldToFilter('events_id', $events->getId())
                         ->addStoreFilter(Mage::app()->getStore())
             ->addFieldToFilter('status', 1);
        $comments->setOrder('created_at', 'asc');
        $this->setComments($comments);
    }
    /**
     * prepare the layout
     * @access protected
     * @return Ultimate_Events_Block_Events_Comment_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout(){
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'ultimate_events.events.html.pager')
            ->setCollection($this->getComments());
        $this->setChild('pager', $pager);
        $this->getComments()->load();
        return $this;
    }
    /**
     * get the pager html
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPagerHtml(){
        return $this->getChildHtml('pager');
    }
    public function getEvents() {
        return Mage::registry('current_events');
    }
}