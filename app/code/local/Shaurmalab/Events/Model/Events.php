<?php

class Shaurmalab_Events_Model_Events extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('events/events');
    }
}