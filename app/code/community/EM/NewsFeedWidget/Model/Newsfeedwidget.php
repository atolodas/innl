<?php

class EM_NewsFeedWidget_Model_Newsfeedwidget extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('newsfeedwidget/newsfeedwidget');
    }  
}
