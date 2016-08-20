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
 * Events helper
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Helper_Events
    extends Mage_Core_Helper_Abstract {
    /**
     * get the url to the events list page
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getEventssUrl(){
        return Mage::getUrl('ultimate_events/events/index');
    }
    /**
     * check if breadcrumbs can be used
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function getUseBreadcrumbs(){
        return Mage::getStoreConfigFlag('ultimate_events/events/breadcrumbs');
    }
    /**
     * check if the rss for events is enabled
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function isRssEnabled(){
        return  Mage::getStoreConfigFlag('rss/config/active') && Mage::getStoreConfigFlag('ultimate_events/events/rss');
    }
    /**
     * get the link to the events rss list
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRssUrl(){
        return Mage::getUrl('ultimate_events/events/rss');
    }
}
