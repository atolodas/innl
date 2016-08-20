<?php

require_once 'Mage/Adminhtml/controllers/Sales/OrderController.php';
class Cafepress_CPCore_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Sales_OrderController {

    public function exportMoreDataCsvAction()
    {
    
        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_order_gridall');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    public function exportOrdersPrototypeReportAction()
    {

        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('adminhtml/sales_order_gridall2');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
}
