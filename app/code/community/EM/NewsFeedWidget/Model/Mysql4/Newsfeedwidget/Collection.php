<?php

class EM_NewsFeedWidget_Model_Mysql4_Newsfeedwidget_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('newsfeedwidget/newsfeedwidget');
    }
}
