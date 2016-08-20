<?php

class Cafepress_CPWms_Model_Xmlformat_Entity_Invoice extends Cafepress_CPWms_Model_Xmlformat_Entity_Abstract
{
    public function createInvoice(){
        $order = $this->getOrder();
        try {
            if (!$order->canInvoice()) {
                Mage::log("Cannot create an invoice for order:".$order->getIncrementId(),null,'invoice.log');
                return;
            }
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

            if (!$invoice->getTotalQty()) {
                Mage::log("Cannot create an invoice without products. for order:".$order->getIncrementId(),null,'invoice.log');
                return;
            }
            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
            $invoice->register();
            $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
            $transactionSave->save();
        } catch (Mage_Core_Exception $e) {
            Mage::log("Error:".$e->getMessage(),null,'invoice.log');
        }
    }
    
    public function createInvoiceForOrder($orderId){
        $this->setOrderId($orderId);
        return $this->createInvoice();
    }
}
