<?php

class EM_NewsFeedWidget_Model_Observer extends Mage_Core_Model_Abstract
{   
	public function application_clean_cache($observer)
    {
    	$collection = Mage::getModel('newsfeedwidget/newsfeedwidget')->getCollection();
        $feeds = $collection->load();
        foreach($feeds as $feed){
        	$feed->delete();
        }
    }    
}
