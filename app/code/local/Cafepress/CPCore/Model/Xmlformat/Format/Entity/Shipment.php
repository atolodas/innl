<?php

class Cafepress_CPCore_Model_Xmlformat_Format_Entity_Shipment extends Cafepress_CPCore_Model_Abstract
{
    
    private $order = false;
    private $_shipment = false;
    private $_invoice = false;
    private $_dateShipment = false;
    private $_tracking = array();
    private $_emailSent = false;
    
    private $_importData = array(
        'tracking_number'   => array(),
        'sku'               => '',
        'qty_shipped'       => '',
        'ship_via'          => array()
        
        );
    
    public function reset(){
        $this->order = false;
        $this->_shipment = false;
        $this->_invoice = false;
        $this->_dateShipment = false;
        $this->_tracking = array();
        $this->_emailSent = false;

        $this->_importData = array(
            'tracking_number'   => array(),
            'sku'               => '',
            'qty_shipped'       => '',
            'ship_via'          => array()

            );
//        $this->_getVarModel()->reset();
        $this->_getOrderModel()->reset();

    }

//    public function shipOrder($order,$importData) {
//		try {
//			if($order->hasShipments()) // Load existing shipment
//				{
//					$shipment = $order->getShipmentsCollection()->getFirstItem();
//					$newShipment = false;
//				}
//				else // Create new shipment
//				{
//					// All qtys are set to maximum initially.
//					// Will be updated later for each order item
//					$savedQtys = array();
//					foreach($order->getAllItems() as $_item)
//						$savedQtys[$_item->getId()] = $_item->getQtyOrdered();
//					$shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
//					$shipment->register();
//					$shipment->getOrder()->setIsInProcess(true);
//
//					// Assign tracking number
//					if($importData['tracking number'])
//						$this->addTrack($shipment, $importData);
//					$transactionSave = Mage::getModel('core/resource_transaction')
//						->addObject($shipment)
//						->addObject($shipment->getOrder())
//						->save();
//
//					// Capture the invoice
//					try
//					{
//						$invoice = $shipment->getOrder()->getInvoiceCollection()->getFirstItem();
//						if($invoice->getId() && $invoice->canCapture())
//							$invoice->capture();
//					}
//					catch(Exception $e)
//					{
//						Mage::log("Error during invoice capture: {$e->getMessage()}",null,'shipment.log');
//					}
//					$newShipment = true;
//				}
//				// Update item qty
//				foreach($shipment->getAllItems() as $_item)
//				{
//					if($_item->getSku() == $importData['sku'])
//					{
//						$_item->setData('qty', $importData['qty_shipped'])->save();
//						// Update parent item
//						if($_item->getOrderItem()->getParentItemId())
//							foreach($shipment->getAllItems() as $_parent)
//								if($_parent->getOrderItem()->getItemId() == $_item->getOrderItem()->getParentItemId())
//									$_parent->setData('qty', $importData['qty_shipped'])->save();
//					}
//				}
//				// Assign tracking number
//				if($importData['tracking_number'])
//				{
//					$this->addTrack($shipment, $importData);
//					$shipment->save();
//				}
//                                $shipment->sendEmail(true, '');
//                                $shipment->setEmailSent(true);
//		} catch (Exception $e) {
//			Mage::log("Shipment creation error: {$e->getMessage()}",null,'shipment.log');
//		}
//	}

    
    /**
     * Get Order Model
     * @return type 
     */
    protected function _getOrderModel()
    {
        if (!$this->order){
            $this->order = Mage::getSingleton('sales/order');
        }
        return $this->order;
    } 
    
    
    private function _getShipmentModel()
    {
        if ($this->_shipment){
            return $this->_shipment;
        }
        try {
            $order = $this->_getOrderModel();
            if (!$order->getIncrementId()){
                Mage::log("Shipment creation error: No Order",null,'shipment.log');
                return false;
            }
            if ($order->hasShipments()){
                echo 'Shipment already exists. For Order:'.$order->getIncrementId().'<br>';
                Mage::log('Shipment already exists. For Order:'.$order->getIncrementId(),null,'orderstatus.log');
//                return;
//            if (0){
                $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
                        ->addAttributeToFilter('order_id', $order->getId());
                
                if(count($shipmentCollection)>0){
                                    
//                    $shipment = Mage::getResourceModel('sales/order_shipment_collection')
//                            ->addAttributeToFilter('order_id', $order->getId())
//                            ->getFirstItem();
                    $shipment = $shipmentCollection->getFirstItem();

                    echo 'Shipment was load:'.$shipment->getIncrementId().' For Order:'.$order->getIncrementId().'<br>';
                    Mage::log('Shipment was load:'.$shipment->getIncrementId().' For Order:'.$order->getIncrementId(),null,'orderstatus.log');
                } else {
                    echo 'BARADA<br>';
                }

            } else {
                $savedQtys = array();
                foreach($order->getAllItems() as $_item){
                    $savedQtys[$_item->getId()] = $_item->getQtyOrdered();
                }

                $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
                $shipment->register();
                $shipment->getOrder()->setIsInProcess(true);
                $shipment->save();
                Mage::log("Create Shipment: {$shipment->getIncrementId()} for Order: {$order->getIncrementId()} ",null,'orderstatus.log');
                echo "Create Shipment: {$shipment->getIncrementId()} for Order: {$order->getIncrementId()} <br>";
            }
        } catch (Exception $e) {
            Mage::log("Get ship model error: {$e->getMessage()}",null,'shipment.log');
        }
//        $order = $this->_getOrderModel();
//        $order->setStatus('complete');
//        $order->save();
        $this->_shipment = $shipment;
        return $shipment;
        
    }

    public function addTrack($trackingNumber, $carrierCod = false) {
    try {
            $shipment = $this->_getShipmentModel();
            if (!$shipment){
                return;
            }
            if(trim($trackingNumber)=='') { 
                $trackingNumber = 'No Tracking Available';
            }
            $track = Mage::getModel('sales/order_shipment_track')
                ->setTrackNumber($trackingNumber) // be careful with this line, bro!
->setNumber($trackingNumber)
//                    ->setCarrierCode(strtolower($importData['ship_via']))
//                    ->setCarrierCode($trackingNumber)
//                    ->setTitle($importData['ship_via'])
//                    ->setTitle('via_track')
//                    ->save()
                    ;
            if ($carrierCod && $carrierCod!=''){
                
            } else {
                $carrierCod = 'custom';
            }
            $track->setCarrierCode($carrierCod);
            $track->setTitle(strtoupper($carrierCod));
            
            $shipment->addTrack($track);
            $shipment->setEmailSent($this->_emailSent);
            
            $shipment->save();
            if ($this->_emailSent){
                $shipment->sendEmail();
            }
            $order = Mage::getSingleton('cpcore/sales_order')->load($this->getOrder()->getId());
            $filename = Mage::registry('cp_response_filename');
            $filename = 'inbound/'.substr($filename,strrpos($filename,'/')+1);
            $order->setData('cp_wms_file',str_replace($filename,'',$order->getData('cp_wms_file')).' '.$filename);
            $order->save();
            
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($shipment)
                ->addObject($shipment->getOrder())
                ->save();
            echo 'Add Track:'.$trackingNumber.' For shipment:'.$shipment->getIncrementId().'<br>';
            Mage::log('Add Track:'.$trackingNumber.' For shipment:'.$shipment->getIncrementId(),null,'orderstatus.log');
        } catch (Exception $e) {
            Mage::log("Tracking number add error: {$e->getMessage()}",null,'shipment.log');
        }
        return $this;
	}
    
//    private function _createShipment($shipment, $itemsQty)
//    {
//        $itemsQtyArr = array();
//        foreach ($itemsQty as $item)
//        {
//            $itemsQtyArr[$item->iExternalOrderId] = $item->dQtyShipped;
//            Mage::helper('firephp')->debug('$item->dQtyShipped');
//            Mage::helper('firephp')->debug($item->dQtyShipped);
//        }
//        try
//        {
//            $shipmentIncrementId = Mage::getModel('sales/order_shipment_api')->create($shipment->sOrderNumber, $itemsQtyArr, $shipment->sShipmentComment, true, true);
//
//            if ($shipmentIncrementId)
//            {
//                Mage::getModel('sales/order_shipment_api')->addTrack($shipmentIncrementId, $shipment->sCarrierCode, $shipment->sTrackingTitle, $shipment->sTrackingNumber);
//            }
//        }
//        catch(Exception $e)
//        {
//            Mage::log('Exception: ' . $e->getMessage());
//        }
//        
//
//        return $shipmentIncrementId ? true : false;
//    } 
    
    public function getShip()
    {
        return $this->_getShipmentModel();
    }

    public function saveShip()
    {
        try{
            $this->_getShipmentModel()->save();
        } catch (Exception $e) {
            Mage::log("Save shipment error: {$e->getMessage()}",null,'shipment.log');
        }
        
        return $this;
    }
    
    public function _getVarModel()
    {
        return Mage::getSingleton('cpcore/xmlformat_format_entity_variable');
    }
    
    
    
    public function setOrderByIncId($orderIncrementId)
    {
        $this->order = Mage::getModel('sales/order')->loadByIncrementId((int)$orderIncrementId);
        return $this;
    }
    
    public function setOrderByInvoice()
    {
        if (!$this->getInvoice()){
            $this->order = false;
            return false;
        }
        $this->order = $this->getInvoice()->getOrder();
        return $this;
    }
    
    public function setInvoiceByIncId($icrementId)
    {
        $this->_invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId((int)$icrementId);
        return $this;
    }
    
    public function getInvoice()
    {
        return $this->_invoice;
    }

    public function getOrder()
    {
        return $this->order;
    }


    public function updateDate($date){
        try {
            $shipment = $this->_getShipmentModel();
            if (!$shipment){
                return;
            }
            
//            $shipment->save();
            $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($shipment->getOrder())
                    ->save();
            
            $date = Mage::helper('cpcore/date')->localizeDate($date);
            
            $shipment->setCreatedAt($date)->save();
            echo 'UpdateDate:'.$date.' For shipment:'.$shipment->getIncrementId().'<br>';
            Mage::log('UpdateDate:'.$date.' For shipment:'.$shipment->getIncrementId(),null,'orderstatus.log');

        } catch (Exception $e) {
            Mage::log("Save shipment error: {$e->getMessage()}",null,'shipment.log');
        }
        
        return $this;
    }
    
//    /**
//     * Save shipment
//     * We can save only new shipment. Existing shipments are not editable
//     *
//     * @return null
//     */
//    public function createShipment()
//    {
//
//        try {
//            $shipment = $this->_initShipment();
//            if (!$shipment) {
//                $this->_forward('noRoute');
//                return;
//            }
//
//            $shipment->register();
//            $this->_saveShipment($shipment);
//            
//        } catch (Mage_Core_Exception $e) {
////            if ($isNeedCreateLabel) {
////                $responseAjax->setError(true);
////                $responseAjax->setMessage($e->getMessage());
////            } else {
////                $this->_getSession()->addError($e->getMessage());
////                $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
////            }
//        } catch (Exception $e) {
////            Mage::logException($e);
////            if ($isNeedCreateLabel) {
////                $responseAjax->setError(true);
////                $responseAjax->setMessage(Mage::helper('sales')->__('An error occurred while creating shipping label.'));
////            } else {
////                $this->_getSession()->addError($this->__('Cannot save shipment.'));
////                $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
////            }
//
//        }
//    }
    
//    /**
//     * Initialize shipment items QTY
//     */
//    protected function _getItemQtys()
//    {
//        $qtys = array();
//        foreach($this->getOrder()->getAllItems() as $item){
//            $qtys = $item->getId();
//        }
//    }

//    /**
//     * Initialize shipment model instance
//     *
//     * @return Mage_Sales_Model_Order_Shipment|bool
//     */
//    protected function _initShipment()
//    {
//        $shipment = false;
//        $order = $this->getOrder();
//
//        /**
//         * Check order existing
//         */
//        if (!$order->getId()) {
//            $this->_getSession()->addError($this->__('The order no longer exists.'));
//            return false;
//        }
//        /**
//         * Check shipment is available to create separate from invoice
//         */
//        if ($order->getForcedDoShipmentWithInvoice()) {
//            $this->_getSession()->addError($this->__('Cannot do shipment for the order separately from invoice.'));
//            return false;
//        }
//        /**
//         * Check shipment create availability
//         */
//        if (!$order->canShip()) {
//            $this->_getSession()->addError($this->__('Cannot do shipment for the order.'));
//            return false;
//        }
//        $savedQtys = $this->_getItemQtys();
//        $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
//
////            $tracks = $this->getRequest()->getPost('tracking');
//            
////            if ($tracks) {
////                foreach ($tracks as $data) {
////                    if (empty($data['number'])) {
////                        Mage::throwException($this->__('Tracking number cannot be empty.'));
////                    }
////                    $track = Mage::getModel('sales/order_shipment_track')
////                        ->addData($data);
////                    $shipment->addTrack($track);
////                }
////            }
////        }
//        Mage::register('current_shipment', $shipment);
//        return $shipment;
//    }
//
//    /**
//     * Save shipment and order in one transaction
//     *
//     * @param Mage_Sales_Model_Order_Shipment $shipment
//     * @return Mage_Adminhtml_Sales_Order_ShipmentController
//     */
//    protected function _saveShipment($shipment)
//    {
//        $shipment->getOrder()->setIsInProcess(true);
//        $transactionSave = Mage::getModel('core/resource_transaction')
//            ->addObject($shipment)
//            ->addObject($shipment->getOrder())
//            ->save();
//
//        return $this;
//    }

    public function test($val = 'TEST'){
        Mage::log('++TEST++',null,'debug.log');
        Mage::log('**'.$val.'**',null,'debug.log');
//        Zend_Debug::dump($val);
    }
    
    public function testTest(){
        Mage::log('++TEST1++',null,'debug.log');
       echo '++TEST1++';
        
    }
    
    /**
     * Alia for Reset
     */
    public function resetShipment(){
        return $this->reset();
    }

    /**
     *
     * @param type $number 
     */
    public function addTrackingNumber($number)
    {
        $this->_importData['tracking_number'][] = $number;
    }
    
    public function addSku($sku)
    {
        $this->_importData['sku'] = $sku;
    }

    public function setQty($qty)
    {
        $this->_importData['qty'] = $qty;
    }

    public function addShipMethod($value)
    {
        $this->_importData['ship_via'][] = $value;
    }
   
    public function setShipDate($date)
    {
        $this->_dateShipment = $date;
    }
    
    public function saveShipment()
    {
        $this->shipOrder($this->order,$this->_importData);
    }
    
    
    public function shipOrder($order,$importData) {
        try {
            if ($order->getIncrementId()){
                Mage::log("Shipment creation error: No Order",null,'shipment.log');
                return false;
            }
            if($order->hasShipments()) // Load existing shipment
            {
                $shipment = $order->getShipmentsCollection()->getFirstItem();
                $newShipment = false;
                echo 'Shipment was load:'.$shipment->getIncrementId().' For Order:'.$order->getIncrementId().'<br>';
                Mage::log('Shipment was load:'.$shipment->getIncrementId().' For Order:'.$order->getIncrementId(),null,'orderstatus.log');
            }
            else // Create new shipment
            {
                // All qtys are set to maximum initially.
                // Will be updated later for each order item
                $savedQtys = array();
                foreach($order->getAllItems() as $_item)
                        $savedQtys[$_item->getId()] = $_item->getQtyOrdered();
                $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
                $shipment->register();
                $shipment->getOrder()->setIsInProcess(true);

                // Assign tracking number
                if($importData['tracking number'])
                    $this->addTracks($shipment, $importData);
                $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($shipment)
                        ->addObject($shipment->getOrder())
                        ->save();

                // Capture the invoice
                try
                {
                    $invoice = $shipment->getOrder()->getInvoiceCollection()->getFirstItem();
                    if($invoice->getId() && $invoice->canCapture())
                    $invoice->capture();
                }
                catch(Exception $e)
                {
                    Mage::log("Error during invoice capture: {$e->getMessage()}",null,'shipment.log');
                }
                $newShipment = true;
                echo 'Shipment was created:'.$shipment->getIncrementId().' For Order:'.$order->getIncrementId().'<br>';
                Mage::log('Shipment was created:'.$shipment->getIncrementId().' For Order:'.$order->getIncrementId(),null,'orderstatus.log');
            }
            // Update item qty
            foreach($shipment->getAllItems() as $_item)
            {
                if($_item->getSku() == $importData['sku'])
                {
                    $_item->setData('qty', $importData['qty_shipped'])->save();
                    // Update parent item
                    if($_item->getOrderItem()->getParentItemId())
                            foreach($shipment->getAllItems() as $_parent)
                                    if($_parent->getOrderItem()->getItemId() == $_item->getOrderItem()->getParentItemId())
                                            $_parent->setData('qty', $importData['qty_shipped'])->save();
                }
            }
            // Assign tracking number
            if($importData['tracking_number'])
            {
                $this->addTracks($shipment, $importData);
                $shipment->setCreatedAt(Mage::helper('cpcore')->formatDateShip($this->_dateShipment));
                $shipment->setEmailSent($this->_emailSent);
                $shipment->save();
                if ($this->_emailSent){
                    $shipment->sendEmail();
                }
            }
//            $state = Mage_Sales_Model_Order::STATE_COMPLETE; 
//            $order->setState($state);
//            $order->setStatus('complete');
//            $order->setStatus('complete');
            $filename = Mage::registry('cp_response_filename');
            $order->setData('cp_wms_file',str_replace($filename,'',$order->getData('cp_wms_file')).' '.$filename);
            $order->save();

        } catch (Exception $e) {
                Mage::log("Shipment creation error: {$e->getMessage()}",null,'shipment.log');
        }
    }

    public function addTracks($shipment,$importData) {
        $counter = 0;
        foreach($importData['tracking_number'] as $number) {
            if (count($importData['tracking_number']) != count($importData['ship_via'])){
                $shipVia = $importData['ship_via'][0];
            } else {
                $shipVia = $importData['ship_via'][$counter];
                if (!$shipVia){
                    $shipVia = 'custom';
                }
            }
            $counter++;
            try {
                $track = Mage::getModel('sales/order_shipment_track')
                    ->setNumber($number)
                    ->setCarrierCode(strtolower($shipVia))
                    ->setTitle($shipVia);
                $shipment->addTrack($track);
                echo 'Tracking number add:'.$number.' For shipment:'.$shipment->getIncrementId().'<br>';
                Mage::log('Tracking number add:'.$number.' For shipment:'.$shipment->getIncrementId(),null,'orderstatus.log');
            } catch (Exception $e) {
                Mage::log("Tracking number add error: {$e->getMessage()}",null,'shipment.log');
            }
        }
    }
        
    public function setEmailSent($val){
        $this->_emailSent = $val;
        return true;
    }

}
