<?php

class EM_NewsFeedWidget_Model_Mysql4_Item extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the interestmodule_id refers to the key field in your database table.
        $this->_init('newsfeedwidget/item', 'item_id');
    }
}
