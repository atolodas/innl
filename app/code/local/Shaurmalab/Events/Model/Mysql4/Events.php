<?php

class Shaurmalab_Events_Model_Mysql4_Events extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the events_id refers to the key field in your database table.
        $this->_init('events/events', 'events_id');
    }
}