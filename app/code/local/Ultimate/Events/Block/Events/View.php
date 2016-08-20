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
 * Events view block
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Block_Events_View
    extends Mage_Core_Block_Template {
    /**
     * get the current events
     * @access public
     * @return mixed (Ultimate_Events_Model_Events|null)
     * @author Ultimate Module Creator
     */
    public function getCurrentEvents(){
        return Mage::registry('current_events');
    }
}
