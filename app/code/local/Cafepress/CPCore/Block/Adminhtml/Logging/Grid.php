<?php

class Cafepress_CPCore_Block_Adminhtml_Logging_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('wmsGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('xmlformat_filter');
    }
    
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('cplog/log')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _addColumnFilterToCollection($column)
    {
        return parent::_addColumnFilterToCollection($column);
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id',
            array(
                'header'=> Mage::helper('cpcore')->__('ID #'),
                'width' => '10px',
                'align' => 'right',
                'type'  => 'text',
                'index' => 'id',
        ));
        $this->addColumn('format_id',
            array(
                'header'=> Mage::helper('cpcore')->__('Format Name'),
                'width' => '30px',
                'align' => 'right',
                'type'  => 'options',
                'index' => 'format_id',
                'renderer' => new Cafepress_CPCore_Block_Adminhtml_Logging_Grid_Renderer_Formatname(),
                'options' => Mage::getModel('cpcore/xmlformat')->getAllFormatName(),
        ));
        
        $this->addColumn('execution_date',
            array(
                'header'=> Mage::helper('cpcore')->__('Execution Date'),
                'width' => '10px',
                'type'  => 'datetime',
                'index' => 'execution_date',
        ));
        
        $this->addColumn('status', array(
            'header' => Mage::helper('cpcore')->__('Status'),
            'width' => '50px',
            'index' => 'status',
            'type'  => 'text'
        ));
        $this->addColumn('file', array(
            'header' => Mage::helper('cpcore')->__('File'),
            'width' => '250px',
            'index' => 'link_to_file',
//            'type'  => 'text'
            'renderer' => new Cafepress_CPCore_Block_Adminhtml_Logging_Grid_Renderer_File()
        ));
        
        $this->addColumn('order_id',
            array(
                'header'=> Mage::helper('cpcore')->__('Order ID'),
                'width' => '50px',
                'type'  => 'text',
                'align' => 'right',
                'index' => 'order_id',
        ));
        
        $this->addColumn('order_data', array(
            'header' => Mage::helper('cpcore')->__('Order/CM WMS Status'),
            'width' => '250px',
            'index' => 'order_id',
            'filter'    => false,
//            'type'  => 'text'
            'renderer' => new Cafepress_CPCore_Block_Adminhtml_Logging_Grid_Renderer_Orderdata()
        ));

        $this->addColumn('parent_id',
            array(
                'header'=> Mage::helper('cpcore')->__('Parent ID'),
                'width' => '10px',
//                'type'  => 'number',
                'align' => 'right',
                'index' => 'parent_id',
        ));
        
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('cpcore')->__('Actions'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('cpcore')->__('View'),
                        'url'     => array(
                            'base'=>'*/adminhtml_logging/request',
                            'params'=>array(
                                'store' => $this->getRequest()->getParam('store'),
                                )
                        ),
                        'field'   => 'log_id'
                    ),
                    array(
                        'caption' => Mage::helper('cpcore')->__('Resend'),
                        'url'     => array(
                            'base'=>'*/adminhtml_logging/request',
                            'params'=>array(
                                'store' => $this->getRequest()->getParam('store'),
                                )
                        ),
                        'field'   => 'log_id'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

//    public function getRowUrl($row)
//    {
//        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
//    }
        public function getRowUrl($row)
    {
        return $this->getUrl(
                '*/adminhtml_logging/request', 
                array(
                    'log_id' => $row->getId()
                    ));
    }

}
