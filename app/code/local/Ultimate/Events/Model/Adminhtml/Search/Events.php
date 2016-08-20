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
 * Admin search model
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Model_Adminhtml_Search_Events
    extends Varien_Object {
    /**
     * Load search results
     * @access public
     * @return Ultimate_Events_Model_Adminhtml_Search_Events
     * @author Ultimate Module Creator
     */
    public function load(){
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('ultimate_events/events_collection')
            ->addFieldToFilter('title', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $events) {
            $arr[] = array(
                'id'=> 'events/1/'.$events->getId(),
                'type'  => Mage::helper('ultimate_events')->__('Events'),
                'name'  => $events->getTitle(),
                'description'   => $events->getTitle(),
                'url' => Mage::helper('adminhtml')->getUrl('*/events_events/edit', array('id'=>$events->getId())),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
