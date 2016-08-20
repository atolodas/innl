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
 * Events customer comments list
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Block_Events_Customer_Comment_List
    extends Mage_Customer_Block_Account_Dashboard {
    /**
     * Events comments collection
     * @var Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     */
    protected $_collection;

    /**
     * Initializes collection
     * @access public
     * @author Ultimate Module Creator
     */
    protected function _construct() {
        $this->_collection = Mage::getResourceModel('ultimate_events/events_comment_events_collection');
        $this->_collection
            ->setStoreFilter(Mage::app()->getStore()->getId(), true)
            ->addFieldToFilter('main_table.status', 1) //only active entities

            ->addStatusFilter(Ultimate_Events_Model_Events_Comment::STATUS_APPROVED) //only approved comments
            ->addCustomerFilter(Mage::getSingleton('customer/session')->getCustomerId()) //only my comments
            ->setDateOrder();
    }

    /**
     * Gets collection items count
     * @access public
     * @return int
     * @author Ultimate Module Creator
     */
    public function count() {
        return $this->_collection->getSize();
    }

    /**
     * Get html code for toolbar
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getToolbarHtml() {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Initializes toolbar
     * @access protected
     * @return Mage_Core_Block_Abstract
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'customer_events_comments.toolbar')
            ->setCollection($this->getCollection());

        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    /**
     * Get collection
     * @access protected
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    protected function _getCollection() {
        return $this->_collection;
    }

    /**
     * Get collection
     * @access public
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    public function getCollection() {
        return $this->_getCollection();
    }

    /**
     * Get review link
     * @access public
     * @param mixed $comment
     * @return string
     * @author Ultimate Module Creator
     */
    public function getCommentLink($comment) {
        if ($comment instanceof Varien_Object){
            $comment = $comment->getCtCommentId();
        }
        return Mage::getUrl('ultimate_events/events_customer_comment/view/', array('id'=>$comment));
    }

    /**
     * Get product link
     * @access public
     * @param mixed $comment
     * @return string
     * @author Ultimate Module Creator
     */
    public function getEventsLink($comment) {
        return $comment->getEventsUrl();
    }

    /**
     * Format date in short format
     * @access public
     * @param $date
     * @return string
     * @author Ultimate Module Creator
     */
    public function dateFormat($date) {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }
}
