<?php

class Cafepress_CPWms_Block_Adminhtml_Royalty extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize container block settings
     *
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_royalty';
        $this->_blockGroup = 'cpwms';

        $this->_headerText = Mage::helper('reports')->__('Products Ordered');
        parent::__construct();
        $this->_removeButton('add');
    }
}
