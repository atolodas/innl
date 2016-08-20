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
class Ultimate_Events_Block_Events_Customer_Comment_View
    extends Mage_Customer_Block_Account_Dashboard {
    /**
     * get current comment
     * @access public
     * @return Ultimate_Events_Model_Events_Comment
     * @author Ultimate Module Creator
     */
    public function getComment() {
        return Mage::registry('current_comment');
    }
    /**
     * get current events
     * @access public
     * @return Ultimate_Events_Model_Events
     * @author Ultimate Module Creator
     */
    public function getEvents() {
        return Mage::registry('current_events');
    }
}
