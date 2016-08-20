<?php
class Neklo_ABTesting_Block_Adminhtml_System_Abpresentation extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Set template
     */
    public function __construct()
    {
       parent::__construct();
       $this->setTemplate('neklo_abtesting/manage/abtest.phtml');
    }

    protected function _prepareLayout() {

        $this->_addButton('add_new', array(
            'label'   => Mage::helper('catalog')->__('Add A/B Presentation'),
            'onclick' => "setLocation('{$this->getUrl('*/*/new')}')",
            'class'   => 'add'
        ));
        
        $this->setChild('grid', $this->getLayout()->createBlock('neklo_abtesting/adminhtml_system_abpresentation_grid', 'abpresentation.grid'));
        return parent::_prepareLayout();
    }

    public function getTitle() {
        return Mage::helper('catalog')->__('Manage A/B Presentations');
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

}
