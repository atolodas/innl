<?php

class Cafepress_CPWms_Block_Adminhtml_Replacer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('cpwmsGridReplacer');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
//        $this->setVarNameFilter('xmlformat_filter');
    }
    

    /**
     * Init ordered product collection
     * @return void
     */
    protected function _prepareCollection()
    {
//        $collection = Mage::getModel('cpwms/resource_mysql4_replacer')->getCollection();
        $collection = Mage::getModel('wmsreplacer/replacer')->getCollection();
        
        $this->setCollection($collection);
        
        parent::_prepareCollection();
        return $this;
    }
    
//    protected function _addColumnFilterToCollection($column)
//    {
//        return parent::_addColumnFilterToCollection($column);
//    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'=> Mage::helper('cpwms')->__('ID'),
            'width' => '30px',
            'type'  => 'number',
            'index' => 'id',
        ));
        
//        $this->addColumn('pattern', array(
//            'header' => Mage::helper('cpwms')->__('Construction'),
//            'width' => '150px',
//            'align' => 'right',
//            'index' => 'pattern',
//            'type'  => 'text',
//        ));
        $this->addColumn('helper', array(
            'header' => Mage::helper('cpwms')->__('Helper'),
            'width' => '150px',
            'align' => 'right',
            'index' => 'helper',
            'type'  => 'text',
        ));
        
        $this->addColumn('value', array(
            'header' => Mage::helper('cpwms')->__('Default Values'),
            'width' => '300px',
//            'align' => 'right',
            'index' => 'id',
            'type'  => 'text',
            'filter'    => false,
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Replacer_Grid_Renderer_Value()
        ));
        
        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

//    public function getRowUrl($row)
//    {
//        return $this->getUrl('*/*/#', array('id'=>$row->getId()));
//    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }

}
