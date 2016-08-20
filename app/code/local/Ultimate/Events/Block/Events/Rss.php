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
 * Events RSS block
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Block_Events_Rss
    extends Mage_Rss_Block_Abstract {
    /**
     * Cache tag constant for feed reviews
     * @var string
     */
    const CACHE_TAG = 'block_html_events_events_rss';
    /**
     * constructor
     * @access protected
     * @return void
     * @author Ultimate Module Creator
     */
    protected function _construct(){
        $this->setCacheTags(array(self::CACHE_TAG));
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('ultimate_events_events_rss');
        $this->setCacheLifetime(600);
    }
    /**
     * toHtml method
     * @access protected
     * @return string
     * @author Ultimate Module Creator
     */
    protected function _toHtml(){
        $url = Mage::helper('ultimate_events/events')->getEventssUrl();
        $title = Mage::helper('ultimate_events')->__('Events');
        $rssObj = Mage::getModel('rss/rss');
        $data = array(
            'title' => $title,
            'description' => $title,
            'link'=> $url,
            'charset' => 'UTF-8',
        );
        $rssObj->_addHeader($data);
        $collection = Mage::getModel('ultimate_events/events')->getCollection()
            ->addFieldToFilter('status', 1)
            ->addStoreFilter(Mage::app()->getStore())
            ->addFieldToFilter('in_rss', 1)
            ->setOrder('created_at');
        $collection->load();
        foreach ($collection as $item){
            $description = '<p>';
            $description .= '<div>'.Mage::helper('ultimate_events')->__('Event title').': '.$item->getTitle().'</div>';
            $description .= '<div>'.Mage::helper('ultimate_events')->__('Event description').': '.$item->getDescription().'</div>';
            $description .= '<div>'.Mage::helper('ultimate_events')->__('Event date').': '.Mage::helper('core')->formatDate($item->getDate(), 'full').'</div>';
            $description .= '</p>';
            $data = array(
                'title'=>$item->getTitle(),
                'link'=>$item->getEventsUrl(),
                'description' => $description
            );
            $rssObj->_addEntry($data);
        }
        return $rssObj->createRssXml();
    }
}
