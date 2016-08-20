<?php

class Cafepress_CPWms_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
   
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        
        $this->addColumn('wms_file', array(
            'header' => Mage::helper('sales')->__('Wms File'),
            'index' => 'wms_file',
			'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_Wmsfile()
        ));
        
        $this->addColumn('wms_status', array(
            'header' => Mage::helper('sales')->__('Wms Requests Statuses'),
            'index' => 'wms_file_status',
         //   'type'  => 'options',
         //   'width' => '50px',
         //   'options' => Mage::helper('cpwms')->getStatuses(),
			'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_Wmsfilestatus()
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
                                'base'=>'cpwms/adminhtml_wms/regenerate',
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
