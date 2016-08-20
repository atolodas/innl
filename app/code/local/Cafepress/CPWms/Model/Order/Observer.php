<?php

class Cafepress_CPWms_Model_Order_Observer extends Mage_Sales_Model_Observer {

    protected $sequence = array(
        'header',
        'main_part',
        'addresses',
        'product',
        'footer'
    );

    public function generateXml($observer) {
        try {
            $order = $observer->getEvent()->getOrder();
            if ($order->getWmsFileStatus() == '') {
                $storeId = $order->getStoreId(); //Mage::getModel('cpwms/xmlformat')->getStoreId(); //@todo: storeId ?
                if (Mage::getStoreConfig('common/format/use_release', $storeId) == Mage::getModel('cpwms/system_config_source_release')->getRelease('RELEASE_2')) {
                    $xml = $this->getOrderXmlByFormat($order, 0);
                } else {
                    $orderXml = $this->getOrderXml($order);
                    $xml = $this->getXmlHeader() . $orderXml . $this->getXmlFooter();
                }

                $file = $this->saveXml($xml, $order);
                if ($file) {
                    $filename = basename($file);
                    $order->setData('wms_file', $order->getData('wms_file') . ' ' . $filename);
                    $order->setWmsFileStatus(Mage::helper('cpwms')->getStatusId('Created'));
                    $order->save();
//                $order->setWmsFileStatus(Mage::helper('cpwms')->getStatusId('Sent'));
//                $order->save();
                    $this->sendXmlOverPost($xml, $storeId);
                    $order->setWmsFileStatus(Mage::helper('cpwms')->getStatusId('Sent'));
                    $order->save();
                    Mage::log('Order #' . $order->getIncrementId() . ' sent. Response:'/* . $response*/, null, 'orders.log');
                    //$this->sendXmlFile($filename,$order);
                }
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'orders.log');
        }
    }

    public function generateCreditMemoXml($observer, $formatId) {
        try {
            
            $creditmemo = $observer->getEvent()->getCreditmemo();
            $order = $creditmemo->getOrder();
//            Mage::log('Order #' . $order->getIncrementId() . ' created. Credit Memo XML generating started', null, 'orders.log');

            $storeId = Mage::app()->getStore()->getId();
            if (Mage::getStoreConfig('common/format/use_release', $storeId) == Mage::getModel('cpwms/system_config_source_release')->getRelease('RELEASE_2')) {
                $xml = $this->getCreditMemoXmlByFormat($order, $creditmemo, $formatId);
            } else {
                $orderXml = $this->getOrderCreditMemoXml($order, $creditmemo);
                $xml = $this->getCreditmemoXmlHeader() . $orderXml . $this->getCreditmemoXmlFooter();
            }
            $file = $this->saveCreditMemoXml($xml, $order, $creditmemo);
            if ($file) {
                $filename = basename($file);
                $order->setData('wms_file', $order->getData('wms_file') . ' ' . $filename);
                $creditmemo->setData('wms_file', str_replace($filename, '', $order->getData('wms_file')) . ' ' . $filename);
                $creditmemo->setWmsFileStatus(Mage::helper('cpwms')->getStatusId('Created'));
                $order->save();

                $this->sendXmlOverPost($xml, $order->getStoreId());
                $creditmemo->setWmsFileStatus(Mage::helper('cpwms')->getStatusId('Sent'));
                Mage::log('CreditMemo for Order#' . $order->getIncrementId() . ' sent. Response:'/* . $response*/, null, 'orders.log');
//                  $this->sendXmlFile($filename,$order);
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'orders.log');
        }
    }

    public function generateXmlByCron($order, $formatId) {
        try {
            if (1 || $order->getWmsFileStatus() == Mage::helper('cpwms')->getStatusId('Fail Sending')) {
                $storeId = $order->getStoreId(); //Mage::getModel('cpwms/xmlformat')->getStoreId(); //@todo: storeId ?
                if (Mage::getStoreConfig('common/format/use_release', $storeId) == Mage::getModel('cpwms/system_config_source_release')->getRelease('RELEASE_2')) {
					
					$orderModel = Mage::getModel('cpwms/xmlformat_format_order')
                            ->setStoreId($order->getStoreId())
                            ->setOrderById($order->getId())
                            ->setFormat($formatId);

					if($orderModel->checkCondition() != true)
					{
						echo "Condition is not performed. <br/>";
						return;
					}
					echo "Condition is performed. <br/>";

//                    $xmlFormat = Mage::getModel('cpwms/xmlformat')->load($formatId);
//                    $xmlFormat->setLastSent(strtotime("now"));
//                    $xmlFormat->save();
if($order != false)
		{
			$_SESSION['number'] = $order->getIncrementId();
			if(!Mage::registry('number')) {
				Mage::register('number',$order->getIncrementId());
			}
		}
                    $xml = $this->getOrderXmlByFormat($order, $formatId);

//                    Zend_Debug::dump($xml);

					$xmlformat = Mage::getModel('cpwms/xmlformat')
						->setStoreId($storeId)
                        ->loadByAttributes(array(
                            'type' =>      $type = Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_type')->getType('order'),
                            'status' => '1',//1-enabled, 2- disabled
                            'entity_id' => $formatId
                            ));
					$orderFormat = Mage::getModel('cpwms/xmlformat_format_order')
                        ->setStoreId($storeId)
                        ->load($xmlformat->getId());
                    $customUrl = false;
                    if($xmlformat->getCustomUrl()) {
                       $customUrl = array('url'=>$xmlformat->getCustomUrl());
                    }
                    /*echo*/ $response = Mage::getModel('cpwms/xmlformat_outbound')->outboundFile($xml, $order, $formatId, false,$customUrl);
				
					if($response){
                        $orderFormat->processResponse($response);
                    }
                    
                    Mage::dispatchEvent('order_format_preperformed', array('order' => $order, 'format' => $xmlformat));
                } else {
                    $orderXml = $this->getOrderXml($order);
                    $xml = $this->getXmlHeader() . $orderXml . $this->getXmlFooter();
                    $file = $this->saveXml($xml, $order, $formatId);
                    if ($file) {
                        $filename = basename($file);
                        $order->setData('wms_file', str_replace($filename, '', $order->getData('wms_file')) . ' ' . $filename);
                        $order->setWmsFileStatus(Mage::helper('cpwms')->getStatusId('Created'));
                        $order->save();
                        $response = $this->sendXmlOverPost($xml, $order->getStoreId());
                        $order->setWmsFileStatus(Mage::helper('cpwms')->getStatusId('Sent'));
                        $order->save();
                        Mage::log('Order #' . $order->getIncrementId() . ' sent. Response:' . $response, null, 'orders.log');
                    }
                }
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'orders.log');
        }
    }

    public function generateCreditMemoXmlByCron($order, $creditmemo, $formatId) {
        if (!$creditmemo->getWmsFile()) {
        try {
    //        Mage::log('Order #' . $order->getIncrementId() . ' created. Credit Memo XML generating started', null, 'orders.log');
            $storeId = $order->getStoreId(); //Mage::getModel('cpwms/xmlformat')->getStoreId(); //@todo: storeId ?
            if (Mage::getStoreConfig('common/format/use_release', $storeId) == Mage::getModel('cpwms/system_config_source_release')->getRelease('RELEASE_2')) {
                $xml = $this->getCreditMemoXmlByFormat($order, $creditmemo, $formatId);
			//	Zend_Debug::dump($creditmemo);
                Mage::getModel('cpwms/xmlformat_outbound')->outboundFile($xml, $order, $formatId, $creditmemo);
//                $xmlFormat = Mage::getModel('cpwms/xmlformat')->load($formatId);
//                $xmlFormat->setLastSent(strtotime("now"));
//                $xmlFormat->save();
            } else {
                $orderXml = $this->getOrderCreditMemoXml($order, $creditmemo);
                $xml = $this->getCreditmemoXmlHeader() . $orderXml . $this->getCreditmemoXmlFooter();

                $file = $this->saveCreditMemoXml($xml, $order, $creditmemo);
                if ($file) {
                    $filename = basename($file);
                    $data = $creditmemo->getData();
                    $creditmemo = Mage::getModel('sales/service_order', $order)
                            ->prepareCreditmemo()
                            ->addData($data);
                    $creditmemo->setData('wms_file', $creditmemo->getData('wms_file') . ' ' . $filename);
                    $creditmemo->setWmsFileStatus(Mage::helper('cpwms')->getStatusId('Created'));
                    $creditmemo->save();
                    $this->sendXmlOverPost($xml, $order->getStoreId());
                    $creditmemo->setWmsFileStatus(Mage::helper('cpwms')->getStatusId('Sent'));
                    $order->save();
                    Mage::log('CreditMemo for Order#' . $order->getIncrementId() . ' sent. Response:'/* . $response*/, null, 'orders.log');
//                    $this->sendCreditmemoXmlFile($filename,$order,$creditmemo);
                }
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'orders.log');
        }
        }
    }

    public function generateXmlForOrder($order) {
        try {
            //if(!$order->getWmsFile()) { 
      //      Mage::log('Order #' . $order->getIncrementId() . ' created. XML generating started', null, 'orders.log');
            $orderXml = $this->getOrderXml($order);
            $xml = $this->getXmlHeader() . $orderXml . $this->getXmlFooter();
            $file = $this->saveXml($xml, $order);
            if ($file) {
                $filename = basename($file);
                $order->setData('wms_file', $filename);
                $order->setData('wms_file_status', (Mage::helper('cpwms')->getStatusId('Created')));
                $order->save();

                $this->sendXmlOverPost($xml, $order->getStoreId());
                $order->setData('wms_file_status', (Mage::helper('cpwms')->getStatusId('Sent')));
                Mage::log('Order#' . $order->getIncrementId() . ' sent. Response:'/* . $response*/, null, 'orders.log');
//				   	$this->sendXmlFile($filename,$order);
            }
            echo 'XML generated <br>';
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'orders.log');
        }
    		
	}
	
    public function sendXmlFile($file, $order) {
        try {
            $server = trim(Mage::getStoreConfig('ftp/orders/address'));
            $login = trim(Mage::getStoreConfig('ftp/orders/login'));
            $password = trim(Mage::getStoreConfig('ftp/orders/password'));
            $folder = trim(Mage::getStoreConfig('ftp/orders/inbound'));
            $ch = curl_init();
            $filename = $file;
            //$filename = 'dontProcessItPlease.xml';
            $localfile = Mage::getBaseDir('media') . '/xmls/outbound/' . $file;
            $fp = fopen($localfile, 'r');
            curl_setopt($ch, CURLOPT_URL, "ftp://$login:$password@$server/$folder/" . $filename);
            curl_setopt($ch, CURLOPT_UPLOAD, 1);
            //              curl_setopt($ch, CURLOPT_FTPPORT, 21);
            curl_setopt($ch, CURLOPT_INFILE, $fp);
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localfile));
            curl_exec($ch);
            $error_no = curl_errno($ch);
            curl_close($ch);
            if ($error_no == 0) {
                $error = 'File uploaded succesfully.';
                $order->setData('wms_file_status', (Mage::helper('cpwms')->getStatusId('Sent')));
                $order->save();
            } else {
                $error = 'File upload error. ' . "ftp://$login:$password@$server/$folder/";
                $order->setData('wms_file_status', (Mage::helper('cpwms')->getStatusId('Fail Sending')));
                $order->save();
            }
            Mage::log($error . ' ' . $error_no . ' ' . $filename, null, 'orders.log');
        } catch (Exception $e) {
            Mage::log('Order XML not uploaded. ' . $e->getMessage(), null, 'orders.log');
        }
    }

    public function sendCreditmemoXmlFile($file, $order, $creditmemo) {

		try {
			$server = trim(Mage::getStoreConfig('ftp/orders/address'));
	   		$login = trim(Mage::getStoreConfig('ftp/orders/login'));
	   		$password = trim(Mage::getStoreConfig('ftp/orders/password'));
	   		$folder = trim(Mage::getStoreConfig('ftp/orders/inbound'));
			$ch = curl_init();
			$filename = $file;
			//$filename = 'dontProcessItPlease.xml';
		 	$localfile = Mage::getBaseDir('media').'/xmls/outbound/'.$file;
		 	$fp = fopen($localfile, 'r');
		 	curl_setopt($ch, CURLOPT_URL, "ftp://$login:$password@$server/$folder/".$filename);
		 	curl_setopt($ch, CURLOPT_UPLOAD, 1);
          //              curl_setopt($ch, CURLOPT_FTPPORT, 21);
		 	curl_setopt($ch, CURLOPT_INFILE, $fp);
		 	curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localfile));
		 	curl_exec($ch);
		 	$error_no = curl_errno($ch);
		 	curl_close($ch);
//		        if ($error_no==0) {
            $error = 'Creditmemo file uploaded succesfully.' . $file;
//		        	$order->setData('wms_file_status',(Mage::helper('cpwms')->getStatusId('Sent')));
//				   	$order->save();
//		        } else {
            $error = 'File upload error. ' . "ftp://$login:$password@$server/$folder/";
//		        	$order->setData('wms_file_status',(Mage::helper('cpwms')->getStatusId('Fail Sending')));
//				   	$order->save();
//		        }
            Mage::log($error . ' ' . $error_no . ' ' . $filename, null, 'orders.log');
        } catch (Exception $e) {
            Mage::log('Creditmemo XML not uploaded. ' . $e->getMessage(), null, 'orders.log');
        }
    }

    public function getXmlHeader() {
        return '<?xml version="1.0" encoding="UTF-8" ?>
						<PurchaseOrders messagebatch-id="D25jBWBiwrd9ds">
  							';
    }

    public function getXmlFooter() {
        return '	  </PurchaseOrderMessage>
                    <messageCount>1</messageCount>
					</PurchaseOrders>';
    }

    public function getCreditmemoXmlHeader() {
        return '<?xml version="1.0" encoding="UTF-8" ?>
                <CreditMemo messagebatch-id="D25jBWBiwrd9ds">';
    }

    public function getCreditmemoXmlFooter() {
        return '</CreditMemoMessage>
                    <messageCount>1</messageCount>
                    </CreditMemo>';
    }

    public function getOrderXml($order) {
        $order = Mage::getModel('cpwms/sales_order')->load($order->getId());
        $payment = $order->getPayment();
        $add = $payment->getAdditionalInformation();
        $add = array_pop(@$add['authorize_cards']);

        $xml = '';
        if (substr_count($order->getStore()->getName(), 'POS') > 0) {
            $partnerId = 'C00064';
        } else {
            $partnerId = Mage::getStoreConfig('common/partner/id');
        }
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $createdDate = explode(' ', $order->getCreatedAt());
        $createdDate = str_replace('-', '', $createdDate[0]);
        $ps = '';
        if (is_object($customer->getResource()->getAttribute('packing_slip'))) {
            $ps = $customer->getResource()
                    ->getAttribute('packing_slip')->getSource()
                    ->getOptionText($customer->getData('packing_slip')); //getAttributeText('packing_slip');
        }

        $sp = '';
        if (is_object($customer->getResource()->getAttribute('sales_person'))) {
            $sp = $customer->getResource()
                    ->getAttribute('sales_person')->getSource()
                    ->getOptionText($customer->getData('sales_person')); //getAttributeText('packing_slip');
        } else {
            $sp = '';
        }

        $retailer_terms = '';
        if (is_object($customer->getResource()->getAttribute('retailer_terms'))) {
            $retailer_terms = $customer->getResource()
                    ->getAttribute('retailer_terms')->getSource()
                    ->getOptionText($customer->getData('retailer_terms')); //getAttributeText('packing_slip');
        } else {
            $retailer_terms = '';
        }

        $message = Mage::helper("giftmessage/message")->getGiftMessage($order->getGiftMessageId());
        $xml.='<PartnerID>' . Mage::helper('cpwms')->encodeXml($partnerId) . '</PartnerID>
               <WebsiteID>' . Mage::helper('cpwms')->encodeXml(Mage::getStoreConfig('common/partner/site_id', $order->getStoreId())) . '</WebsiteID>
        <PurchaseOrderMessage message-id="XpI4hffEwHM6EM" message-purpose="issue" purchase-offer-number="' . $order->getIncrementId() . '">
                <participatingParty participationCode="To:" role="vendor"></participatingParty>
                <offerDate>' . date("Ymd") . '</offerDate>
                <shipMethod code="' . Mage::helper('cpwms')->encodeXml($order->getShippingMethod()) . '" />
                <salesOrderNumber>' . $order->getIncrementId() . '</salesOrderNumber>
                <salesOrderDate>' . $createdDate . '</salesOrderDate>
                <salesDivision>' . Mage::helper('cpwms')->encodeXml($ps) . '</salesDivision>
                <customerAttribute1 name="sales_person">' . Mage::helper('cpwms')->encodeXml($sp) . '</customerAttribute1>
                <customerAttribute2 name="retailer_terms">' . Mage::helper('cpwms')->encodeXml($retailer_terms) . '</customerAttribute2>
                <customerNo>' . $order->getCustomerId() . '</customerNo>
                <SubTotal>' . $order->getSubtotal() . '</SubTotal>
                <ShippingTotal>' . $order->getShippingAmount() . '</ShippingTotal>
                <TaxTotal>' . $order->getTaxAmount() . '</TaxTotal>
                <OrderDiscount>' . $order->getDiscountAmount() . '</OrderDiscount>
                <GrandTotal>' . $order->getGrandTotal() . '</GrandTotal>
                <DiscountCode>' . Mage::helper('cpwms')->encodeXml($order->getCouponCode()) . '</DiscountCode>
                <GiftCertificate>' . $order->getGiftcertAmount() . '</GiftCertificate>
                <GiftCertificateAmount>' . $order->getGiftcertAmount() . '</GiftCertificateAmount>
                <PaymentMethodAmount>' . $order->getGrandTotal() . '</PaymentMethodAmount>
                <PaymentMethod>' . Mage::helper('cpwms')->encodeXml($payment->getMethodInstance()->getTitle()) . '</PaymentMethod>
                <CCType>' . ($payment->getCcType() ? $payment->getCcType() : $add['cc_type']) . '</CCType>
                <TransNumber>' . ($payment->getLastTransId() ? $payment->getLastTransId() : $add['last_trans_id']) . '</TransNumber>
                <CCLast4>' . ($payment->getCcLast4() ? $payment->getCcLast4() : $add['cc_last4']) . '</CCLast4>
                <PurchaseOrderNumber>' . $payment->getPoNumber() . '</PurchaseOrderNumber>
                <SpecialInstructions>' . Mage::helper('cpwms')->encodeXml($order->getSpecialInstructions()) . '</SpecialInstructions>
                <giftFrom>' . Mage::helper('cpwms')->encodeXml($message->getSender()) . '</giftFrom>
                <giftTo>' . Mage::helper('cpwms')->encodeXml($message->getRecipient()) . '</giftTo>
                <giftMessage>' . Mage::helper('cpwms')->encodeXml($message->getMessage()) . '</giftMessage>
                <shippingInstructions>' . Mage::helper('cpwms')->encodeXml($order->getSpecialInstructions()) . '</shippingInstructions>';

        $counter = 1;
        //Mage::getStoreConfig('common/partner/salesDivision')  - previously used for salesDivision
        foreach ($order->getAllItems() as $item) {
            $unit_cost = null;
            if ($item->getChildrenItems())
                continue;
            if ($item->getParentItem()) {
                $unit_price = $item->getParentItem()->getPrice();
            }
            $xml.= $this->getItemXml($item, $counter, $unit_price);
            $counter++;
        }
        $xml.=$this->getOrderAddressesXml($order);
        return $xml;
    }

    public function getOrderCreditMemoXml($order, $creditmemo) {
        $order = Mage::getModel('sales/order')->load($order->getId());
        $payment = $order->getPayment();
        $add = $payment->getAdditionalInformation();
        $add = array_pop(@$add['authorize_cards']);
        if (substr_count($order->getStore()->getName(), 'POS') > 0) {
            $partnerId = 'C00064';
        } else {
            $partnerId = Mage::getStoreConfig('common/partner/id');
        }
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $createdDate = explode(' ', $order->getCreatedAt());
        $createdDate = str_replace('-', '', $createdDate[0]);
        $ps = '';
        if (is_object($customer->getResource()->getAttribute('packing_slip'))) {
            $ps = $customer->getResource()
                    ->getAttribute('packing_slip')->getSource()
                    ->getOptionText($customer->getData('packing_slip')); //getAttributeText('packing_slip');
        }

        $sp = '';
        if (is_object($customer->getResource()->getAttribute('sales_person'))) {
            $sp = $customer->getResource()
                    ->getAttribute('sales_person')->getSource()
                    ->getOptionText($customer->getData('sales_person')); //getAttributeText('packing_slip');
        } else {
            $sp = 'test';
        }
        $retailer_terms = '';
        if (is_object($customer->getResource()->getAttribute('retailer_terms'))) {
            $retailer_terms = $customer->getResource()
                    ->getAttribute('retailer_terms')->getSource()
                    ->getOptionText($customer->getData('retailer_terms')); //getAttributeText('packing_slip');
        } else {
            $retailer_terms = '';
        }
        $message = Mage::helper("giftmessage/message")->getGiftMessage($order->getGiftMessageId());
        //$payment = $order->getPayment();
        $xml = '';
        $xml.='
                <PartnerID>' . Mage::helper('cpwms')->encodeXml($partnerId) . '</PartnerID>
                <WebsiteID>' . Mage::helper('cpwms')->encodeXml(Mage::getStoreConfig('common/partner/site_id', $order->getStoreId())) . '</WebsiteID>
                 <CreditMemoMessage message-id="XpI4hffEwHM6EM" message-purpose="issue" purchase-offer-number="">
                <participatingParty participationCode="To:" role="vendor"></participatingParty>
                <creditMemoNumber>' . $creditmemo->getIncrementId() . '</creditMemoNumber>
                <creditMemoDate>' . date("Ymd") . '</creditMemoDate>
                <shipMethod code="' . Mage::helper('cpwms')->encodeXml($order->getShippingMethod()) . '" />
                <salesOrderNumber>' . $order->getIncrementId() . '</salesOrderNumber>
                <salesOrderDate>' . $createdDate . '</salesOrderDate>
                <salesDivision>' . Mage::helper('cpwms')->encodeXml($ps) . '</salesDivision>
                <customerAttribute1 name="sales_person">' . Mage::helper('cpwms')->encodeXml($sp) . '</customerAttribute1>
                <customerAttribute2 name="retailer_terms">' . Mage::helper('cpwms')->encodeXml($retailer_terms) . '</customerAttribute2>
                <customerNo>' . $order->getCustomerId() . '</customerNo>
                <CreditMemoSubTotal>' . $creditmemo->getSubtotal() . '</CreditMemoSubTotal>
                <CreditMemoShippingTotal>' . $creditmemo->getShippingAmount() . '</CreditMemoShippingTotal>
                <CreditMemoTaxTotal>' . $creditmemo->getTaxAmount() . '</CreditMemoTaxTotal>
                <CreditMemoDiscount>' . $creditmemo->getDiscountAmount() . '</CreditMemoDiscount>
                <CreditMemoAdjustmentRefund>' . $creditmemo->getAdjustmentPositive() . '</CreditMemoAdjustmentRefund>
                <CreditMemoAdjustmentFee>' . $creditmemo->getAdjustmentNegative() . '</CreditMemoAdjustmentFee>
                <CreditMemoGrandTotal>' . $creditmemo->getGrandTotal() . '</CreditMemoGrandTotal>
                <DiscountCode>' . Mage::helper('cpwms')->encodeXml($order->getCouponCode()) . '</DiscountCode>
                <GiftCertificate>' . $order->getGiftcertAmount() . '</GiftCertificate>
                <GiftCertificateAmount>' . $order->getGiftcertAmount() . '</GiftCertificateAmount>
                <PaymentMethodAmount>' . $order->getGrandTotal() . '</PaymentMethodAmount>
                <PaymentMethod>' . Mage::helper('cpwms')->encodeXml($payment->getMethodInstance()->getTitle()) . '</PaymentMethod>
                <CCType>' . ($payment->getCcType() ? $payment->getCcType() : $add['cc_type']) . '</CCType>
                <TransNumber>' . ($payment->getLastTransId() ? $payment->getLastTransId() : $add['last_trans_id']) . '</TransNumber>
                <CCLast4>' . ($payment->getCcLast4() ? $payment->getCcLast4() : $add['cc_last4']) . '</CCLast4>
                <PurchaseOrderNumber>' . $payment->getPoNumber() . '</PurchaseOrderNumber>
                <SpecialInstructions>' . Mage::helper('cpwms')->encodeXml($order->getSpecialInstructions()) . '</SpecialInstructions>
                <giftFrom>' . Mage::helper('cpwms')->encodeXml($message->getSender()) . '</giftFrom>
                <giftTo>' . Mage::helper('cpwms')->encodeXml($message->getRecipient()) . '</giftTo>
                <giftMessage>' . Mage::helper('cpwms')->encodeXml($message->getMessage()) . '</giftMessage>
                <shippingInstructions>' . Mage::helper('cpwms')->encodeXml($order->getSpecialInstructions()) . '</shippingInstructions>';
        $counter = 1;
        foreach ($creditmemo->getAllItems() as $item) {
            $unit_cost = null;
            if ($item->getOrderItem()->getChildrenItems())
                continue;
            if ($item->getOrderItem()->getParentItem()) {
                $unit_price = $item->getOrderItem()->getParentItem()->getPrice();
            }
            $xml.= $this->getCreditmemoItemXml($item, $counter, $unit_price);
            $counter++;
        }
        $xml.=$this->getOrderAddressesXml($order);
        return $xml;
    }

    public function getCreditmemoItemXml($item, $i, $parent_price) {
        $product = Mage::getModel('cpwms/catalog_product')->load($item->getProductId());
        $rowCost = $product->getCost();
        $rowPrice = $item->getPrice();

        $rowTotal = $item->getRowTotal();
        if ($parent_price != '') {
            $rowPrice = $parent_price;
            $rowTotal = $rowPrice * $item->getQty();
        }

        $rowCost2 = $rowCost;
        $rowPrice2 = $rowPrice;
        $rowTotal2 = $rowTotal;

        $rowCost = "";
        $rowPrice = "";
        $rowTotal = "";
        return '
                <lineitemRefundedDefinition credit-memo-line-number="' . $i . '">
			<qtyRefunded>' . round($item->getQty()) . '</qtyRefunded>
			<buyerProductDescription>' . Mage::helper('cpwms')->encodeXml($product->getName()) . '</buyerProductDescription>
                      <buyerSKU>' . Mage::helper('cpwms')->encodeXml($product->getSku()) . '</buyerSKU>
                      <vendorSKU>' . Mage::helper('cpwms')->encodeXml($product->getVendorSku()) . '</vendorSKU>
                      <GS1>' . Mage::helper('cpwms')->encodeXml($product->getUpc()) . '</GS1>
                      <unitCost>' . $rowCost2 . '</unitCost>
                      <UnitPrice>' . $rowPrice2 . '</UnitPrice>
                      <ExtendedPrice>' . $rowTotal2 . '</ExtendedPrice>
                      <unitProcessingCost></unitProcessingCost>
                      <giftWrap>0</giftWrap>
		</lineitemRefundedDefinition>';
    }

    public function getItemXml($item, $i, $parent_price) {
        $product = Mage::getModel('cpwms/catalog_product')->load($item->getProductId());
        // This is where we remove the configurable items
        //if ($product->get
//		if ($product->getTypeID() == 'configurable')
//		{
//			$rowCost = $product->getCost();
//			$rowPrice = $item->getPrice();
//			$rowTotal = $item->getRowTotal();
//			return '';
//		}
//		else
//		{
//
//			if ($rowCost != "")
//			{
        $rowCost = $product->getCost();
        $rowPrice = $item->getPrice();

        $rowTotal = $item->getRowTotal();
        if ($parent_price != '') {
            $rowPrice = $parent_price;
            $rowTotal = $rowPrice * $item->getQtyOrdered();
        }
//			}

        $rowCost2 = $rowCost;
        $rowPrice2 = $rowPrice;
        $rowTotal2 = $rowTotal;

        $rowCost = "";
        $rowPrice = "";
        $rowTotal = "";

        return '
        <lineitemDefinition purchase-offer-line-number="' . $i . '">
                  <qtyOrdered>' . round($item->getQtyOrdered()) . '</qtyOrdered>
                  <buyerProductDescription>' . Mage::helper('cpwms')->encodeXml($product->getName()) . '</buyerProductDescription>
                  <buyerSKU>' . Mage::helper('cpwms')->encodeXml($product->getSku()) . '</buyerSKU>
                  <vendorSKU>' . Mage::helper('cpwms')->encodeXml($product->getVendorSku()) . '</vendorSKU>
                  <GS1>' . Mage::helper('cpwms')->encodeXml($product->getUpc()) . '</GS1>
                  <unitCost>' . $rowCost2 . '</unitCost>
                  <UnitPrice>' . $rowPrice2 . '</UnitPrice>
                  <ExtendedPrice>' . $rowTotal2 . '</ExtendedPrice>
                  <unitProcessingCost></unitProcessingCost>
                  <giftWrap>0</giftWrap>
        </lineitemDefinition>';
//    }
    }

    public function getOrderaddressesXml($order) {
        $sa = $order->getShippingAddress();
        $ba = $order->getBillingAddress();

        $shipping_region = $sa->getRegion();
        if ($sa->getRegionId()) {
            $shipping_region = $sa->getRegionCode();
        }
        $billing_region = $sa->getRegion();
        if ($ba->getRegionId()) {
            $billing_region = $ba->getRegionCode();
        }

        return '<personPlaceInfo personPlaceID="SHIPTO_' . $sa->getId() . '">
						  <company>' . Mage::helper('cpwms')->encodeXml($sa->getCompany()) . '</company>
						  <firstName>' . Mage::helper('cpwms')->encodeXml($sa->getFirstname()) . '</firstName>
						  <lastName>' . Mage::helper('cpwms')->encodeXml($sa->getLastname()) . '</lastName>
						  <address1>' . Mage::helper('cpwms')->encodeXml($sa->getStreet(1)) . '</address1>
						  <address2>' . Mage::helper('cpwms')->encodeXml($sa->getStreet(2)) . '</address2>
						  <city>' . Mage::helper('cpwms')->encodeXml($sa->getCity()) . '</city>
						  <state>' . Mage::helper('cpwms')->encodeXml($shipping_region) . '</state>
						  <postalCode>' . Mage::helper('cpwms')->encodeXml($sa->getPostcode()) . '</postalCode>
						  <country>' . Mage::helper('cpwms')->encodeXml($sa->getCountry()) . '</country>
						  <dayPhone>' . Mage::helper('cpwms')->encodeXml($sa->getTelephone()) . '</dayPhone>
						 </personPlaceInfo>
						 <personPlaceInfo personPlaceID="BILLTO_' . $ba->getId() . '">
						  <company></company>
						  <firstName>' . Mage::helper('cpwms')->encodeXml($ba->getFirstname()) . '</firstName>
						  <lastName>' . Mage::helper('cpwms')->encodeXml($ba->getLastname()) . '</lastName>
						  <address1>' . Mage::helper('cpwms')->encodeXml($ba->getStreet(1)) . '</address1>
						  <address2>' . Mage::helper('cpwms')->encodeXml($ba->getStreet(2)) . '</address2>
						  <city>' . Mage::helper('cpwms')->encodeXml($ba->getCity()) . '</city>
						  <state>' . Mage::helper('cpwms')->encodeXml($billing_region) . '</state>
						  <postalCode>' . Mage::helper('cpwms')->encodeXml($ba->getPostcode()) . '</postalCode>
						  <country>' . Mage::helper('cpwms')->encodeXml($ba->getCountry()) . '</country>
						  <dayPhone>' . Mage::helper('cpwms')->encodeXml($ba->getTelephone()) . '</dayPhone>
						</personPlaceInfo>
                                                 <fulfillerLocationInfo locationID="RETURNTO_0">
                                                  <company></company>
                                                  <name1></name1>
                                                  <address1></address1>
                                                  <address2></address2>
                                                  <city></city>
                                                  <state></state>
                                                  <country></country>
                                                  <postalCode></postalCode>
                                                  <dayPhone></dayPhone>
                                                </fulfillerLocationInfo>
                                              ';
    }

    public function saveXml($xml, $order, $formatId) {
        $type = Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_type')->getType('order');
        $orderModel = Mage::getModel('cpwms/xmlformat_format_order')
                ->setStoreId($order->getStoreId())
                ->setOrderById($order->getId())
                ->setFormat($formatId);
        $saveFilename = $orderModel->getSaveFilename();

        $this->checkFolders();
        if ($saveFilename != ''){
            $file = Mage::getBaseDir().'/media/xmls/outbound/'.$saveFilename;
        } else {
            $file = Mage::getBaseDir().'/media/xmls/outbound/PO-'.date('m-d-Y-').$order->getIncrementId().'.xml';
        }

        unlink($file);
        $resource = fopen($file, "a+");
        if (fwrite($resource, $xml) === FALSE) {
            Mage::log('Can\'t save file ' . $file . ' for order ' . $order->getIncrementId(), null, 'orders.log');
            return false;
        } else {
            Mage::log('File ' . $file . ' for order ' . $order->getIncrementId() . ' saved', null, 'orders.log');
            fclose($resource);
            return $file;
        }
    }

    public function saveCreditMemoXml($xml, $order, $creditmemo) {
        $this->checkFolders();
        $file = Mage::getBaseDir().'/media/xmls/outbound/CM-'.date('m-d-Y-').$creditmemo->getIncrementId().'.xml';
        unlink($file);
        $resource = fopen($file, "a+");
        if (fwrite($resource, $xml) === FALSE) {
            Mage::log('Can\'t save file ' . $file . ' for order ' . $order->getIncrementId(), null, 'orders.log');
            return false;
        } else {
            Mage::log('File ' . $file . ' for order ' . $order->getIncrementId() . ' saved', null, 'orders.log');
            fclose($resource);
            return $file;
        }
    }

	public function getFiles(&$files,$id,$type) {
   		$dateObj = Mage::app()->getLocale()->date();
		$hours = $dateObj->get('HH')*1;
		if((trim(Mage::getStoreConfig($type.'/options/check'))==1 && $hours==trim(Mage::getStoreConfig($type.'/options/hour')) )
		|| trim(Mage::getStoreConfig($type.'/options/check'))==0
		|| Mage::app()->getRequest()->getParam('force')==1) {
		try {
   			$server = trim(Mage::getStoreConfig('ftp/orders/address'));
	   		$login = trim(Mage::getStoreConfig('ftp/orders/login'));
	   		$password = trim(Mage::getStoreConfig('ftp/orders/password'));
	   		$folder = trim(Mage::getStoreConfig('ftp/orders/outbound'));
	 		$c = curl_init();
	 		curl_setopt($c, CURLOPT_URL, "ftp://$server/$folder/");
	 		curl_setopt($c, CURLOPT_USERPWD, "$login:$password");
	 		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			curl_error($c);
  			$return = curl_exec($c);
	 		foreach(explode(PHP_EOL,$return) as $line) {
  				foreach(explode(' ',$line) as $item) {
  					if(substr_count($item,'.xml') && substr_count($item,$id)) {
  						$items[] = $item;
  					}
  				}
  			}
  			curl_close ($c);

                if (empty($items)) {
                    Mage::log('No ' . $type . ' files to import', null, $type . '.log');
                    echo 'No files ' . $type . ' to import';
                    return false;
                }

  			$local_dir = Mage::getBaseDir('media').'/xmls/inbound/';
  			$local_files = scandir($local_dir);

                $download = array_diff($local_files, $items);

                foreach ($items as $item) {
                    $local_file = $local_dir . $item;
                    $c = curl_init("ftp://$login:$password@$server/$folder/$item");
                    $fh = fopen($local_file, 'w') or die('Can\'t open file');
                    curl_setopt($c, CURLOPT_FILE, $fh);
                    curl_exec($c);
                    curl_close($c);
                    fclose($fh);

                    $files[] = $local_file;
                }
                return $files;
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, $type . '.log');
                echo $e->getMessage();
            }
        } else {
            echo 'File will not be imported now. Check the settings';
        }
    }

    public function parseAndDeleteAknowledgment($files) {
        try {
            $server = trim(Mage::getStoreConfig('ftp/orders/address'));
            $login = trim(Mage::getStoreConfig('ftp/orders/login'));
            $password = trim(Mage::getStoreConfig('ftp/orders/password'));
            $folder = trim(Mage::getStoreConfig('ftp/orders/outbound'));

            foreach ($files as $local_file) {
                $item = basename($local_file);
                if ($this->parseAknowledgment($local_file)) {
                    $file = "$folder/$item";
                    // set up basic connection
                    $conn_id = ftp_connect($server);

                    // login with username and password
                    $login_result = ftp_login($conn_id, $login, $password);

                    // try to delete $file
                    if (ftp_delete($conn_id, $file)) {
                        echo "$file deleted successfully \n";
                        Mage::log("$file deleted successfully", null, 'acknowledgment.log');
                    } else {
                        echo "could not delete $file\n";
                        Mage::log("Could not delete $file", null, 'acknowledgment.log');
                    }

                    // close the connection
                    ftp_close($conn_id);
                } else {
                    echo "Can't parse Aknoledgment file " . $local_file;
                    Mage::log("Can't parse Aknoledgment file " . $local_file, null, 'acknowledgment.log');
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function parseAndDeleteShipping($files) {
        try {
            $server = trim(Mage::getStoreConfig('ftp/orders/address'));
            $login = trim(Mage::getStoreConfig('ftp/orders/login'));
            $password = trim(Mage::getStoreConfig('ftp/orders/password'));
            $folder = trim(Mage::getStoreConfig('ftp/orders/outbound'));

            foreach ($files as $local_file) {
                $item = basename($local_file);
                if ($this->parseShipping($local_file)) {
                    $file = "$folder/$item";
                    // set up basic connection
                    $conn_id = ftp_connect($server);
                    // login with username and password
                    $login_result = ftp_login($conn_id, $login, $password);

                    // try to delete $file
                    if (ftp_delete($conn_id, $file)) {
                        echo "$file deleted successfully \n";
                        Mage::log("$file deleted successfully", null, 'shipment.log');
                    } else {
                        echo "Could not delete $file\n";
                        Mage::log("could not delete $file", null, 'shipment.log');
                    }

                    // close the connection
                    ftp_close($conn_id);
                } else {
                    echo "Can't parse Shipment file " . $local_file;
                    Mage::log("Can't parse Shipment file " . $local_file, null, 'shipment.log');
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function parseAknowledgment($file) {
        try {
            $parsed = Mage::getModel('cpwms/catalog_product_import')->parseXml($file);
            $d_ar = $parsed[0];
            $i_ar = $parsed[1];

            foreach ($d_ar as $element) {
                $value = @$element['value'];
                switch ($element['tag']) {
                    case 'trxID':
                        $orderNumber = $value;
                        if ($orderNumber) {
                            $filename = basename($file);
                            $order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);
                            if ($order->getId()) {
                                if (substr_count($order->getData('wms_file'), $filename) == 0) {
                                    $order->setData('wms_file', $order->getData('wms_file') . ' ' . $filename);
                                }
                                $order->setData('wms_file_status', (Mage::helper('cpwms')->getStatusId('Received')));
                                $order->save();
                            } else {
                                Mage::log("Order #" . $orderNumber . " don't exists", null, 'acknowledgment.log');
                            }
                        } else {
                            Mage::log("Can't find order number in Aknoledgment file " . $file, null, 'acknowledgment.log');
                        }
                        break;
                    default:
                        break;
                }
            }

            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function parseShipping($file) {
        try {
            $parsed = Mage::getModel('cpwms/catalog_product_import')->parseXml($file);

            $d_ar = $parsed[0];
            $i_ar = $parsed[1];
            for ($i = 0; $i < count($i_ar['FulfillmentConfirmationMessage']); $i++) {
                if ($d_ar[$i_ar['FulfillmentConfirmationMessage'][$i]]['type'] == 'open') {
                    $importData['tracking_number'] = array();
                    //now for all content within single <Product> element
                    //extract needed information
                    for ($j = $i_ar['FulfillmentConfirmationMessage'][$i]; $j < $i_ar['FulfillmentConfirmationMessage'][$i + 1]; $j++) {
                        $value = $d_ar[$j]['value'];
                        switch ($d_ar[$j]['tag']) {
                            case 'salesOrderNumber':
                                $orderNumber = $value;
                                break;
                            case 'trackingNumber':
                                $importData['tracking_number'][] = $value;
                                break;
                            case 'buyerSKU':
                                $importData['sku'] = $value;
                                break;
                            case 'quantity':
                                $importData['qty_shipped'] = $value;
                                break;
                            case 'shipMethod':
                                $importData['ship_via'] = $value; //$d_ar[$j]['attributes']['code'];
                                break;
                            default:
                                break;
                        }
                    }

                    if ($orderNumber) {
                        $filename = basename($file);
                        $order = Mage::getModel('sales/order')->loadByIncrementId($orderNumber);
                        if ($order->getId()) {
                            if (substr_count($order->getData('wms_file'), $filename) == 0) {
                                $order->setData('wms_file', $order->getData('wms_file') . ' ' . $filename);
                            }
                            $order->setData('wms_file_status', (Mage::helper('cpwms')->getStatusId('Returned')));
                            $this->shipOrder($order, $importData);
                            $order->setStatus('complete');
                            $order->save();
                        } else {
                            Mage::log("Order #" . $orderNumber . " don't exists", null, 'shipment.log');
                        }
                    } else {
                        Mage::log("Can't find order number in Shipment file " . $file, null, 'shipment.log');
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function shipOrder($order, $importData) {
        try {
            if ($order->hasShipments()) { // Load existing shipment
                $shipment = $order->getShipmentsCollection()->getFirstItem();
                $newShipment = false;
            } else { // Create new shipment
                // All qtys are set to maximum initially.
                // Will be updated later for each order item
                $savedQtys = array();
                foreach ($order->getAllItems() as $_item)
                    $savedQtys[$_item->getId()] = $_item->getQtyOrdered();
                $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
                $shipment->register();
                $shipment->getOrder()->setIsInProcess(true);

                // Assign tracking number
                if ($importData['tracking number'])
                    $this->addTrack($shipment, $importData);
                $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($shipment)
                        ->addObject($shipment->getOrder())
                        ->save();

                // Capture the invoice
                try {
                    $invoice = $shipment->getOrder()->getInvoiceCollection()->getFirstItem();
                    if ($invoice->getId() && $invoice->canCapture())
                        $invoice->capture();
                } catch (Exception $e) {
                    Mage::log("Error during invoice capture: {$e->getMessage()}", null, 'shipment.log');
                }
                $newShipment = true;
            }
            // Update item qty
            foreach ($shipment->getAllItems() as $_item) {
                if ($_item->getSku() == $importData['sku']) {
                    $_item->setData('qty', $importData['qty_shipped'])->save();
                    // Update parent item
                    if ($_item->getOrderItem()->getParentItemId())
                        foreach ($shipment->getAllItems() as $_parent)
                            if ($_parent->getOrderItem()->getItemId() == $_item->getOrderItem()->getParentItemId())
                                $_parent->setData('qty', $importData['qty_shipped'])->save();
                }
            }
            // Assign tracking number
            if ($importData['tracking_number']) {
                $this->addTrack($shipment, $importData);
                $shipment->save();
            }
            
            
            $shipment->setEmailSent(true);
            $shipment->sendEmail(true, '');
        } catch (Exception $e) {
            Mage::log("Shipment creation error: {$e->getMessage()}", null, 'shipment.log');
        }
    }

    public function addTrack($shipment, $importData) {
        foreach ($importData['tracking_number'] as $number) {
            try {
                $track = Mage::getModel('sales/order_shipment_track')
                        ->setNumber($number)
                        ->setCarrierCode(strtolower($importData['ship_via']))
                        ->setTitle($importData['ship_via']);
                $shipment->addTrack($track);
            } catch (Exception $e) {
                Mage::log("Tracking number add error: {$e->getMessage()}", null, 'shipment.log');
            }
        }
    }

    public function checkFolders() {
        if (!is_dir(Mage::getBaseDir() . '/media/xmls/')) {
            mkdir(Mage::getBaseDir() . '/media/xmls/');
            chmod(Mage::getBaseDir() . '/media/xmls/', 0777);
        }
        if(!is_dir(Mage::getBaseDir().'/media/xmls/outbound/')) {
            mkdir(Mage::getBaseDir().'/media/xmls/outbound/');
            chmod(Mage::getBaseDir().'/media/xmls/outbound/',0777);
        }
    }

    /**
     *
     * @param type $order
     * @param type $typeOfFormat : {ORDER, CREDITMEMO}
     * @return type 
     */
    public function getXmlByFormat($order, $formatType = 'order', $creditMemo = false, $formatId = 0) {
        $order = Mage::getModel('cpwms/sales_order')->load($order->getId());
        Mage::register('number',$order->getIncrementId(),true);
        $payment = $order->getPayment();
        $add = $payment->getAdditionalInformation();
        if (is_array($add) && isset($add['authorize_cards']) && is_array($add['authorize_cards'])) {
            $add = array_pop(@$add['authorize_cards']);
        }
        if (substr_count($order->getStore()->getName(), 'POS') > 0) {
            $partnerId = 'C00064';
        } else {
            $partnerId = Mage::getStoreConfig('common/partner/id');
        }
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $createdDate = explode(' ', $order->getCreatedAt());
        $createdDate = str_replace('-', '', $createdDate[0]);
        $ps = '';
        if (is_object($customer->getResource()->getAttribute('packing_slip'))) {
            $ps = $customer->getResource()
                    ->getAttribute('packing_slip')->getSource()
                    ->getOptionText($customer->getData('packing_slip')); //getAttributeText('packing_slip');
        }
        $sp = '';
        if (is_object($customer->getResource()->getAttribute('sales_person'))) {
            $sp = $customer->getResource()
                    ->getAttribute('sales_person')->getSource()
                    ->getOptionText($customer->getData('sales_person')); //getAttributeText('packing_slip');
        } else {
            $sp = '';
        }
        $retailer_terms = '';
        if (is_object($customer->getResource()->getAttribute('retailer_terms'))) {
            $retailer_terms = $customer->getResource()
                    ->getAttribute('retailer_terms')->getSource()
                    ->getOptionText($customer->getData('retailer_terms')); //getAttributeText('packing_slip');
        } else {
            $retailer_terms = '';
        }
        $message = Mage::helper("giftmessage/message")->getGiftMessage($order->getGiftMessageId());
        if ($payment->getLastTransId()) {
            $payment->setTransactionId($payment->getLastTransId());
        } else {
            if (is_array($add) && isset($add['last_trans_id'])) {
                $payment->setTransactionId($payment->getLastTransId($add['last_trans_id']));
            }
        }
        if ('checkmo' == $payment->getMethod()) {
            $payment->setIsCheck('true');
        }
//        $payment->setTitleMethod($payment->getMethodInstance()->getTitle());

        $storeId = $order->getStoreId(); //@todo: storeId ?
        //$statusesEnabled = Mage::getModel('catalog/product_status')->getVisibleStatusIds();
        $type = Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_type')->getType($formatType);
        $store = Mage::getModel('cpwms/xmlformat')->setStoreId($storeId)->getStore();
        $xmlformat = Mage::getModel('cpwms/xmlformat')
                ->setStoreId($storeId)
                ->loadByAttributes(array(
            'type' => $type,
            'status' => '1',//1-enabled, 2- disabled
			'entity_id' => $formatId
                ));
        
        $xml = '';
        $dataFormat = $xmlformat->getData();
        $template = Mage::getModel('cpwms/template')->setStoreId($storeId);

//        $coupon = Mage::getModel('salesrule/coupon');
//        /** @var Mage_SalesRule_Model_Coupon */
//        $discountRule = Mage::getModel('salesrule/rule')->load($coupon->load($order->getCouponCode(), 'code')->getRuleId());
////        $discountPercent = $discountRule->getDiscountAmount() / 100;
//        $discountAmount = $discountRule->getDiscountAmount();
        $discountPercent = 0;
        if ($order->getDiscountPercent()){
            $discountPercent = $order->getDiscountPercent();
        }
        
        $itemsPrice = 0;
        $continyityPrice = 0;
        $itemsPriceDiscount = 0;
        $itemsPriceDiscountAlter = 0;
        foreach ($order->getAllItems() as $item) {
            $product = Mage::getModel('cpwms/catalog_product')->load($item->getProductId());
            $price = $item->getPrice();
            if ($product->getContinuityIs()) { // || (!$product->getContinuityIs() && $item->getIsContinuity())
                $continyityPrice += $product->getContinuityPayment2()*$item->getQtyOrdered();
                $price = $product->getContinuityPrice();
//                $productDiscount = $product->getContinuityDiscount();
//                            if($productDiscount) { 
//                                $discountType = strtolower($product->getAttributeText('continuity_discount_type'));
//                                if ($discountType=='fix'){
//                                    $continuity_price = $continuity_price - $productDiscount;
//                                } else {
//                                    $continuity_price = $continuity_price - ($continuity_price/100)*$productDiscount;
//                                }
//                            }
            }
            $itemPrice = $item->getPrice()*$item->getQtyOrdered();
            if ($order->getDiscountPercent()){
                $itemPriceDiscount = $price*$item->getQtyOrdered()*(1-$order->getDiscountPercent());
            } else {
                $itemPriceDiscount = $price*$item->getQtyOrdered()-$item->getDiscountAmount();
            }
            $itemsPrice += $itemPrice;
            $itemPriceDiscount = round($itemPriceDiscount*1.00,2);
            $itemsPriceDiscount += $itemPriceDiscount;

            #TODO INL: add 0.01$ in order
//            if ($item->getIsContinuity()){
//                if ($order->getDiscountPercent()){
//                    $itemsPriceDiscountAlter += round($product->getContinuityPrice()*(1-$discountPercent)*1.00,2);
//                } else {
//                    $itemsPriceDiscountAlter += round(($product->getContinuityPrice() - $item->getDiscountAmount())*1.00,2);
//                }
//                
//            } else {
//                if ($order->getDiscountPercent()){
//                    $itemsPriceDiscountAlter += round($item->getPrice()*(1-$discountPercent)*1.00,2);
//                } else {
//                    $itemsPriceDiscountAlter += round(($item->getPrice()->getContinuityPrice() - $item->getDiscountAmount())*1.00,2);
//                }
//            }
        }
        $hardVar['payment2']                    = $continyityPrice;
        $hardVar['items_price_discount_tax']    = $itemsPriceDiscount*$order->getTaxPercent()/100;
        if ($order->getDiscountPercent()){
            $hardVar['continyity_discount']     = $continyityPrice*(1-$order->getDiscountPercent());
        } elseif ($continyityPrice!=0) {
            $hardVar['continyity_discount']     = $continyityPrice-$item->getDiscountAmount();
        } 
        else {
            $hardVar['continyity_discount']     = 0;
        }
        
        $hardVar['continyity_discount_tax']     = $hardVar['continyity_discount']*$order->getTaxPercent()/100;
        $message = Mage::helper("giftmessage/message")->getGiftMessage($order->getGiftMessageId());
        
        foreach ($hardVar as $key=>$val){
            $hardVar[$key] = round($val*1.00,2);
        }
        $hardVar['items_price_discount'] = $itemsPriceDiscount;
        
        if (!isset($add['cc_type'])){
            $add['cc_type'] = '';
        }
        if (!isset($add['last_trans_id'])){
            $add['last_trans_id'] = '';
        }
        if (!isset($add['cc_last4'])){
            $add['cc_last4'] = '';
        }
        foreach ($this->sequence as $key) {
//            echo $key.' '.$dataFormat[$key];
            
            $xmlformat->setOutXml($dataFormat[$key]);
            $template->setXmlformat($xmlformat);
            
            if ($key == 'product') {
                $counter = 1;
                if (!$creditMemo) {
                    foreach ($order->getAllItems() as $item) {
                        $unit_cost = null; // ??
                        if ($item->getChildrenItems())
                            continue;
//                        $product = Mage::getModel('catalog/product')->load($item->getProductId());
                        $product = Mage::getModel('cpwms/catalog_product')->load($item->getProductId());
                        if ($product->getTypeId()=='simple'){
                            $product->setIsSimple(true);
                        } else {
                            $product->setIsSimple(false);
                        }
                        $parent_price = '';
                        
                        $parent_product = null;
                        if ($item->getParentItem()) {
                            $parent_price = $item->getParentItem()->getPrice();
                            $parent_product = Mage::getModel('cpwms/catalog_product')->load($item->getParentItem()->getProductId());
                        }

                        if ($parent_price != '') {
                            $item->setPrice($parent_price);
                            $item->setRowTotal($parent_price * $item->getQtyOrdered());
                        }

                        $item->setCounter($counter);
                        $item = $this->setGiftMessageItem($item);

                        
                        if ($product->getContinuityIs() || (!$product->getContinuityIs() && $item->getIsContinuity())) {
                                $item->setHasContinuity(true);
                            
                            $item->setHasNoContinuity(false);
                            $payment->setPaymentTwo($item->getPrice());
                        } else {
                            $item->setHasContinuity(false);
                            
                            $item->setHasNoContinuity(true);
                        }
                        
                        if ($product->getContinuityIs() || (!$product->getContinuityIs() && $item->getIsContinuity())){
                            $continuity_price = $product->getContinuityPrice();
                            $productDiscount = $product->getContinuityDiscount();
                            if($productDiscount) { 
                                $discountType = strtolower($product->getAttributeText('continuity_discount_type'));
                                if ($discountType=='fix'){
                                    $continuity_price = $continuity_price - $productDiscount;
                                } else {
                                    $continuity_price = $continuity_price - ($continuity_price/100)*$productDiscount;
                                }
                            }
                               $product->setContinuityPrice($continuity_price);
                            
                        }

                        $xml .= $this->substitutionVarsToXml(
                                '', $template, $vars = array(
                            'order'                     => $order,
                            'store'                     => $store,
                            'product'                   => $product,
                            'parent' => $parent_product,
                            'item'                      => $item,
                            'counter'                   => $counter,
                            'partnerId'                 => $partnerId,
                            'ps'                        => $ps,
                            'sp'                        => $sp,
                            'retailerTerms'             => $retailer_terms,
                            'payment'                   => $payment,
                            'payment2' => ($hardVar['items_price_discount'] + $order->getShippingAmount() + $hardVar['items_price_discount_tax'] - $order->getGrandTotal()),
                            'discount_percent'          => $discountPercent,
                            'add'                       => $add,
                            'add_cc_type'               => $add['cc_type'],
                            'add_last_trans_id'         => $add['last_trans_id'],
                            'add_cc_last4'              => $add['cc_last4'],
                            'message'                   => $message,
                            'items_price_discount'      => $hardVar['items_price_discount'],
                            'items_price_discount_tax'  => $hardVar['items_price_discount_tax'],
                            'continyity_discount'       => $hardVar['continyity_discount'],
                            'continyity_discount_tax'   => $hardVar['continyity_discount_tax'],
                                ));
                        
                        $counter++;
                    }
                } else {
                    foreach ($creditMemo->getAllItems() as $item) {
                        $unit_cost = null; //??
                        
                          if ($item->getOrderItem()->getChildrenItems())
                            continue;
                        $product = Mage::getModel('cpwms/catalog_product')->load($item->getProductId());
                        if ($product->getTypeId()=='simple'){
                            $product->setIsSimple(true);
                        } else {
                            $product->setIsSimple(false);
                        }
                        $parent_price = '';
                        $parent_product = null;
                        if ($item->getOrderItem()->getParentItem()) {
                            $parent_price = $item->getOrderItem()->getParentItem()->getPrice();
                            $parent_product = Mage::getModel('cpwms/catalog_product')->load($item->getOrderItem()->getParentItem()->getProductId());
                   
                        }

                        if ($parent_price != '') {
                            $item->setPrice($parent_price);
                            $item->setRowTotal($parent_price * $item->getQtyOrdered());
                        }

                        $item->setCounter($counter);
                        $item = $this->setGiftMessageItem($item);

                        if ($product->getContinuityIs() || (!$product->getContinuityIs() && $item->getIsContinuity())){
                            $item->setHasContinuity(true);
                            
                            $item->setHasNoContinuity(false);
                            $payment->setPaymentTwo($item->getPrice());
                        } else {
                            $item->setHasContinuity(false);
                            
                            $item->setHasNoContinuity(true);
                        }


                        $xml .= $this->substitutionVarsToXml(
                                '', $template, $vars = array(
                                'order'                     => $order,
                                'store'                     => $store,
                                'product'                   => $product,
                                'parent'                    => $parent_product,
                                'item'                      => $item,
                                'counter'                   => $counter,
                                'creditMemo'                => $creditMemo,
                                'partnerId'                 => $partnerId,
                                'ps'                        => $ps,
                                'sp'                        => $sp,
                                'retailerTerms'             => $retailer_terms,
                                'retailer_terms'            => $retailer_terms,
                                'payment'                   => $payment,
                                'add'                       => $add,
                                'discountPercent'           => $discountPercent,
                                'discount_percent'          => $discountPercent,
                                'items_price_discount'      => $hardVar['items_price_discount'],
                                'items_price_discount_tax'  => $hardVar['items_price_discount_tax'],
                                'continyity_discount'       => $hardVar['continyity_discount'],
                                'continyity_discount_tax'   => $hardVar['continyity_discount_tax'],
                                ));

                        $counter++;
                    }
                }
                continue;
            }
            $xml .= $this->substitutionVarsToXml(
                    '', $template, $vars = array(
                        'order'                     => $order,
                        'creditMemo'                => $creditMemo,
                        'store'                     => $store,
                        'partnerId'                 => $partnerId,
                        'ps'                        => $ps,
                        'sp'                        => $sp,
                        'retailerTerms'             => $retailer_terms,
                        'retailer_terms'            => $retailer_terms,
                        'payment'                   => $payment,
                        'add'                       => $add,
                        'add_cc_type'               => $add['cc_type'],
                        'add_last_trans_id'         => $add['last_trans_id'],
                        'add_cc_last4'              => $add['cc_last4'],
                        'message'                   => $message,
                        'payment2'                  => $hardVar['payment2'],
                        'total'                     => $order->getGrandTotal() + $hardVar['payment2'],
                        'discountPercent'           => $discountPercent,
                        'discount_percent'          => $discountPercent,
                        'items_price_discount'      => $hardVar['items_price_discount'],
                        'items_price_discount_tax'  => $hardVar['items_price_discount_tax'],
                        'continyity_discount'       => $hardVar['continyity_discount'],
                        'continyity_discount_tax'   => $hardVar['continyity_discount_tax'],
                        
                    ));
        }
        return $xml;
    }

    private function substitutionVarsToXml($xml, $template, $vars = array()) {
        Varien_Profiler::start("xmlformat_template_proccessing");
        $templateProcessed = $template->getProcessedTemplate($vars, true);
        Varien_Profiler::stop("xmlformat_template_proccessing");
        $xml .= $templateProcessed;
        return $xml;
    }

    /**
     *
     * @param type $order
     * @return type 
     */
    public function getOrderXmlByFormat($order, $formatId) {
        return $this->getXmlByFormat($order, 'order', false, $formatId);

        try {
            Mage::log('Order XML #' . $order->getId() . ' generating started', null, 'orders.log');
            $storeId = $order->getStoreId();
            $orderModel = Mage::getModel('cpwms/xmlformat_format_order')
                    ->setStoreId($storeId)
                    ->load($order->getId())
                    ->setOrderById($order->getId());
            $request = $orderModel
                    ->addVariables(array(
                        'date' => Mage::getModel('cpwms/xmlformat_variable_date'),
                    ))
                    ->generateRequest();
//            $response = $this->sendXmlOverPost($request,$storeId);
//            Mage::log('OrderStatus #'.$orderStatus->getId().' sent. Response:'.$response,null,'orders.log');
//            Zend_Debug::dump($response);
//            $result = $orderStatus->processResponse($response);
            Mage::log('OrderStatus #' . $orderStatus->getId() . ' has been processed. Response:' . $response, null, 'orders.log');
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'orders.log');
        }
        Mage::log($request, null, 'debuger.log');

        return $this->getXmlByFormat($order, 'order', false, $formatId);
    }

    /**
     *
     * @param type $order
     * @param type $creditmMemo
     * @return type 
     */
    private function getCreditMemoXmlByFormat($order, $creditMemo, $formatId) {
        return $this->getXmlByFormat($order, 'creditmemo', $creditMemo, $formatId);
    }

    /**
     *
     * @param type $item
     * @return type 
     */
    private function setGiftMessageItem($item) {
        if ($item->getGiftMessageId()) {
            $giftMessage = Mage::helper('giftmessage/message')->getGiftMessage($item->getGiftMessageId());
            $item->setGiftMessage($giftMessage->getMessage());
            $item->setGiftMessageFrom($giftMessage->getSender());
        }
        return $item;
    }

    public function generateOrderStatusXmlByCron($orderStatus) {
        try {
            $storeId = $orderStatus->getStoreId();
            $orderStatus = Mage::getModel('cpwms/xmlformat_format_orderstatus')
                    ->setStoreId($storeId)
                    ->load($orderStatus->getId());
//            $content =  file_get_contents('/home/sergoslav/temp/m_ea2a79ae.jpg');
            $request = $orderStatus
                    ->addVariables(array(
                        'date' => Mage::getModel('cpwms/xmlformat_variable_date'),
//                        'file_content' => urlencode($content),
                    ))
                    ->generateRequest();
//            $response = Mage::getModel('cpwms/xmlformat_outbound')->outboundFile($request, $orderStatus, $formatId, false, $orderFormat->getCustomUrl());
        $server = array();
            if($orderStatus->getCustomUrl()) { 
              $server['url'] =  $orderStatus->getCustomUrl();
          }
            $response = Mage::getModel('cpwms/xmlformat_outbound')->sendXmlOverPost($request, $storeId,$server);

            if($response){
                $result = $orderStatus->processResponse($response);
            } else{
                $result = false;
            }

//            $xmlFormat = Mage::getModel('cpwms/xmlformat')->load($orderStatus->getData('entity_id'));
//            $xmlFormat->setLastSent(strtotime("now"));
//            $xmlFormat->save();
            
            Mage::dispatchEvent('orderstatus_format_preperformed_action',
                    array(
                        'format' => $orderStatus,
                        'request'=>$request,
                        'response'=>$response,
                        'result'=>$result
                        ));
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'wms.log');
            return false;
        }
    }

    /**
     *
     * @param type $xml
     * @param type $server : $server = array('url'=>$url, 'username'=>$username, 'password'=>$password)
     * @return type 
     */
    public function sendXmlOverPost($xml, $storeId = 0, $server = false) {
        $_SESSION['wms_log_request'] = $xml;
        if(0 && $server['function']) {
                $result = Mage::getModel('cpwms/xmlformat_outbound')->sendXmlOverSoap($xml, $storeId, $server);
                $_SESSION['wms_log_response'] = $result;
                return $result;
        } else {
            $result = Mage::getModel('cpwms/xmlformat_outbound')->sendXmlOverPost($xml, $storeId, $server);
            $_SESSION['wms_log_response'] = $result;
            return $result;
        }
    }

    public function generateDownfileparsrespByCron($dfpr) {
        try {
            $storeId = $dfpr->getStoreId();
            $dfprModel = Mage::getModel('cpwms/xmlformat_format_downfileparsresp')
                    ->setStoreId($storeId)
                    ->load($dfpr->getId());
            $dfprModel->downloadRequestFiles();
            $dfprModel->processResponse();
            $dfprModel->deleteFileAfterSuccess();

//            $xmlFormat = Mage::getModel('cpwms/xmlformat')->load($dfpr->getData('entity_id'));
//            $xmlFormat->setLastSent(strtotime("now"));
//            $xmlFormat->save();
            
            Mage::dispatchEvent('downfileparsresp_format_performed', 
                    array(
                        'format' => $dfprModel,
                        ));
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'wms.log');
        }
    }
}
