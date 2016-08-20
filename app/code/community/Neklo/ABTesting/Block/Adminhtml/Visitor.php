<?php

class Neklo_ABTesting_Block_Adminhtml_Visitor extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'adminhtml_visitor';
        $this->_blockGroup = 'neklo_abtesting';
        $this->_headerText = Mage::helper('reports')->__('Visitors');
        parent::__construct();
        $this->_removeButton('add');
    }

}
