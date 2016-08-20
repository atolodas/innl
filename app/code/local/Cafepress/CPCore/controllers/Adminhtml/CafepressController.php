<?php

class Cafepress_CPCore_Adminhtml_CafepressController extends Mage_Adminhtml_Controller_Action
{
    public function orderCancelAction()
    {
        $orderId = $this->getRequest()->getParam('id');
        $order = Mage::getModel('sales/order')->load($orderId);
        if($order->hasShipments()){
            Mage::getSingleton('adminhtml/session')->addError($this->__('Order: #'.$order->getIncrementId().' has`t been canceled. Order has shipment!'));
        } else{
            if($order->getCpWmsFileStatus()){
                $statuses = unserialize($order->getCpWmsFileStatus());
                if(isset($statuses['GetSalesOrderStatus'])){
                    $statuses['GetSalesOrderStatus'] = 'Canceled';
                    $statuses_string = serialize($statuses);
                    $order->setData('cp_wms_file_status', $statuses_string);
                }
            }
            if($order->getCustomNumber()){
                $result = Mage::getModel('cpcore/cafepress_order')->cancel($order->getStoreId(), $order->getCustomNumber());
            }
            $order->setCustomNumber(null);
            if($order->hasInvoices()){
                $order->getPayment()->registerRefundNotification($order->getPayment()->getAmountPaid());
                $order->setState(Mage_Sales_Model_Order::STATE_CANCELED);
                $order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED);
                $order->save();
            } else{
                $order->setState(Mage_Sales_Model_Order::STATE_CANCELED);
                $order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED);
                $order->save();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cpcore')->__('Order: #'.$order->getIncrementId().' has been refunded.'));
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cpcore')->__('Order: #'.$order->getIncrementId().' has been canceled.'));
        }
        $this->_redirectReferer();
    }

    public function testAction(){
//        $order = Mage::getModel('sales/order')->load(188);
//        if($order->getCpWmsFileStatus()){
//            $statuses = unserialize($order->getCpWmsFileStatus());
//            if(isset($statuses['GetSalesOrderStatus'])){
//                $statuses['GetSalesOrderStatus'] = 'Canceled';
//                $statuses_string = serialize($statuses);
//                $order->setData('cp_wms_file_status', $statuses_string);
//            }
//        }
//        if($order->hasInvoices()){
//            $order->getPayment()->registerRefundNotification($order->getPayment()->getAmountPaid());
//            $order->setState(Mage_Sales_Model_Order::STATE_CANCELED);
//            $order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED);
//            $order->save();
//        } else{
//            $order->setState(Mage_Sales_Model_Order::STATE_CANCELED);
//            $order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED);
//            $order->save();
//        }
//        Zend_Debug::dump($order->getData());
    }
}