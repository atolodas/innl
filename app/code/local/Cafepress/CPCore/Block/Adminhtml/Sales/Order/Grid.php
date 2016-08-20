<?php

class Cafepress_CPCore_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        
        $this->addColumn('cp_wms_file', array(
            'header' => Mage::helper('sales')->__('Wms File'),
            'index' => 'cp_wms_file',
			'renderer' => new Cafepress_CPCore_Block_Adminhtml_Sales_Order_Renderer_Wmsfile()
        ));
        
        $this->addColumn('cp_wms_status', array(
            'header' => Mage::helper('sales')->__('Wms Requests Statuses'),
            'index' => 'cp_wms_file_status',
         //   'type'  => 'options',
         //   'width' => '50px',
         //   'options' => Mage::helper('cpcore')->getStatuses(),
			'renderer' => new Cafepress_CPCore_Block_Adminhtml_Sales_Order_Renderer_Wmsfilestatus()
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn('action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id'
                        ),
                        array(
                            'caption' => Mage::helper('catalog')->__('WMS Regenerate'),
                            'url'     => array(
                                'base'=>'cpcore/adminhtml_wms/regenerate',
                                'params'=>array('store'=>$this->getRequest()->getParam('store'))
                            ),
                            'field'   => 'id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ));
        }
        
        //$this->addExportType('*/*/exportMoreDataCsv', Mage::helper('sales')->__('Full Data to CSV'));
        $this->addExportType('*/*/exportOrdersPrototypeReport', Mage::helper('sales')->__('Order Detail'));

    	return $this;
    }
}
