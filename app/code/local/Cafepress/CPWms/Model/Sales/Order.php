<?php

class Cafepress_CPWms_Model_Sales_Order extends Mage_Sales_Model_Order
{
	public function statusIs($argument)
    {
		$statuses = explode(':', $argument);
		return in_array($this->getStatus(), $statuses);
    }
    
    public function isWmsRequestStatus($format, $value){
                $statuses = explode(':', $value);
		return in_array($this->getWmsRequestStatus($format), $statuses);
	}

    public function hasWmsFile(){
		return (bool)$this->getWmsFile();
	}

    public function absentWmsFile(){
		return !(bool)$this->getWmsFile();
	}

    public function getTest()
    {
        $xml = "'";
        return $xml;
    }

    public function getCreationDate(){
        $result = explode(' ',$this->getCreatedAt());
        $result = str_replace('-','',$result[0]);
        return $result;
    }
 public function addWmsFile($filename)
    {
        $this->setData('wms_file',str_replace($filename,'',$this->getData('wms_file')).' '.$filename);
           
        return true;
    }


    public function getOfferDate(){
        return date("Ymd");
    }

    public function getGiftMessage(){
        return Mage::helper("giftmessage/message")->getGiftMessage($this->getGiftMessageId())->getMessage();
    }

    public function getEncriptedCreditNumber() {
        return Mage::helper('core')->decrypt($this->getPayment()->getCcNumberEnc());
    }

    public function saveAdditionalData($observer) {
	$order = $observer->getEvent()->getOrder();
        $params = Mage::app()->getRequest()->getParams();
        if(isset($params['payment']['cc_cid'])) {
            $order->setData('cc_cid',$params['payment']['cc_cid']);
        }
        foreach ($order->getAllItems() as $item) { 
            $percent = $item->getTaxPercent();
            break;
        }
        $order->setTaxPercent($percent);
        
        /*Discount Percent*/
        $coupon = Mage::getModel('salesrule/coupon');
        $discountRule = Mage::getModel('salesrule/rule')->load($coupon->load($order->getCouponCode(), 'code')->getRuleId());
        if ($discountRule->getSimpleAction() == 'by_percent'){
            $discountPercent = $discountRule->getDiscountAmount() / 100;
            $order->setDiscountPercent($discountPercent);
        } 
    }

     public function printError($responseXML) {
          Mage::log(
                    sprintf("XML Error: %s at line %d. XML: %s",xml_error_string(xml_get_error_code($this->parser)), xml_get_current_line_number($this->parser),$responseXML),
                    null,
                    'authorization.log'
                );
         }

    public function customAuthorize($order) {
         $storeId = $order->getStoreId();
         $orderStatus = Mage::getModel('cpwms/xmlformat_format_orderstatus')
                    ->setStoreId($storeId)
                    ->load(5); // TODO make it non-hardcoded. Example of request for this format present at the end of this method.
            $request = $orderStatus
                    ->addVariables( array(
                        'date'  => Mage::getModel('cpwms/xmlformat_variable_date'),
                        'order' => $order,
                        'payment' => $order->getPayment(),
                        'credit' => Mage::helper('core')->decrypt($order->getPayment()->getCcNumberEnc())
                    ))
                    ->generateRequest();
            
            $response = Cafepress_CPWms_Model_Order_Observer::sendXmlOverPost($request,$storeId,array('url' => $orderStatus->getCustomUrl()));
            $this->parser= xml_parser_create();
            xml_parser_set_option($this->parser,XML_OPTION_SKIP_WHITE,1);
            xml_parser_set_option($this->parser,XML_OPTION_CASE_FOLDING,0);
            xml_parse_into_struct($this->parser,$response,$parsed,$i_ar) or $this->printError($response);
           if($parsed[4]['value']=='Approved' 
                    && $parsed[3]['value']=='900') {
                $order->setAuthorized(1);
                $state = Mage_Sales_Model_Order::STATE_PROCESSING;
                $order->setAuthorizationCode($parsed[6]['value']);
                $order->setTransactionId($parsed[5]['value']);
                Mage::log('Card for order '.$order->getIncrementId().' is authorized. Authorization message: '.$parsed[4]['value'], null,'authorization.log');
             //   echo 'Card for order '.$order->getIncrementId().' is authorized. Authorization message: '.$parsed[4]['value'].'<br/>';
            } else {
                 Mage::log('Cart for order '.$order->getIncrementId().' is NOT authorized. Authorization message: '.$parsed[4]['value'], null,'authorization.log');
            }
            $order->setAuthorizationResult($parsed[4]['value']);
            $order->save();
     }
     
     public function canRushCharge() { 
         if($this->getShippingMethod()=='tablerate_bestway') { 
            return true;
        } 
        return false;
     }
     
     #TODO INL - Check the enterprise project that have no function getCustomer but try to access it from Format
    public function getCustomerObj() { 
if($this->getCustomerId()) {
         return Mage::getModel('customer/customer')->load($this->getCustomerId());
    } else { 
        return Mage::getModel('customer/customer');
    }
 }
     
     public function getCustomerAttribute($attr) { 
         $options = Mage::getResourceSingleton('customer/customer')->getAttribute($attr)->getSource()->getAllOptions();
        $value = $this->getCustomerObj()->getData($attr);
       foreach ($options as $option):
            if ($option['value'] == $value) return $option['label'];
       endforeach;
     }
     
	public function loadBySavedIncrementId($incrementId=null) {
		
		if(Mage::registry('number') && ($incrementId == null || $incrementId == 'null')) {
			echo $incrementId = Mage::registry('number');
		}
		return $this->loadByAttribute('increment_id', $incrementId);
	}

	public function getWmsRequestStatus($format){
		$value = unserialize($this->getData('wms_file_status'));
		if(!$value)
		{
			if($format != 'last') return false;
			$statuses = Mage::helper('cpwms')->getStatuses();
			return $statuses[$this->getData('wms_file_status')];
		}
		return $value[$format];
	}

	
        
        public function getShippingCodeByDescription() { 
            $descr = $this->getShippingDescription();
            $countryData = Mage::getModel('directory/country')->loadByCode($this->getShippingAddress()->getCountryId());
            $country = $countryData->getIso3Code();
            if(substr_count($descr,'Economy') && $country =='USA') { 
                return 0;
            } elseif(substr_count($descr,'Standard') && $country =='USA') { 
                return 4;
            } elseif(substr_count($descr,'Premium') && $country =='USA') { 
                return 2;
            } elseif(substr_count($descr,'Express') && $country =='USA') { 
              return 1;  
            } elseif(substr_count($descr,'Standard') && $country =='AUS') { 
                return 21;
            } elseif(substr_count($descr,'Express') && $country =='AUS') { 
                return 22;
            } elseif(substr_count($descr,'Standard') && $country =='CAN') { 
                return 15;
            } elseif(substr_count($descr,'Express') && $country =='CAN') { 
                return 16;
            } elseif(substr_count($descr,'Standard')  && $country =='DEU') { 
                return 27;
            } elseif(substr_count($descr,'Express') && $country =='DEU') { 
                return 29;
            } elseif(substr_count($descr,'Standard') && $country =='GBR') { 
                return 18;
            } elseif(substr_count($descr,'Express')  && $country =='GBR') { 
                return 19;
            } elseif(substr_count($descr,'Economy')) { 
                return 27;
            } elseif(substr_count($descr,'Express')) { 
                return 23;
            } 
            return $descr;
        }
        
         public function getDatapakShippingCodeByDescription() { 
            $descr = $this->getShippingDescription();
            $countryData = Mage::getModel('directory/country')->loadByCode($this->getShippingAddress()->getCountryId());
            $country = $countryData->getIso3Code();
            if(substr_count($descr,'UPS Basic')) { 
                return "01";
            } elseif(substr_count($descr,'UPS 3-day')) { 
                return "04";
            } elseif(substr_count($descr,'USPS Standart')) { 
                return "06";
            } elseif(substr_count($descr,'USPS First Class')) { 
              return "07";  
            } 
            return $descr;
        }

    public function getRelastinShippingCodeByDescription() {
        $descr = $this->getShippingDescription();

            if(substr_count($descr,'UPS Basic')) {
            return "01";
            } elseif(substr_count($descr,'UPS 3-day')) {
                return "04";
            } elseif(substr_count($descr,'UPS Standard')) {
                return "07";
            } elseif(substr_count($descr,'USPS First Class')) {
                return "06";
            }
    }

    public function getElaydaShippingCodeByDescription() {
        $descr = $this->getShippingDescription();

if(substr_count($descr,'UPS Basic')) {
    return "01";
} elseif(substr_count($descr,'UPS 3-day')) {
    return "04";
} elseif(substr_count($descr,'USPS Parcel Post')) {
    return "07";
}
    }

    public function getProductsString($fields_string) {
        $result = '';
        if(!$fields_string){
            return $result;
        }
        $items = $this->getAllItems();
        $fields_array = explode(':', $fields_string);
        if(count($fields_array) % 2 != 0){
            return $result;
        }
        $fields = array();
        $i = 0;
        while($i < count($fields_array)){
            $fields[$fields_array[$i]] = $fields_array[$i+1];
            $i += 2;
        }
        $i = 1;
        foreach($items as $item){
            $product = Mage::getModel('cpwms/catalog_product')->load($item->getProductId());
            foreach($fields as $key => $value){
                $in_value = $product->getData($value);
                if(!$in_value){
                    $in_value = $item->getData($value);
                }
                if(is_numeric($in_value)){
                    $in_value = round($in_value, 2);
                }
                $result .= '<'.$key.$i.'>'.$in_value.'</'.$key.$i.'>';
            }
            $i++;
        }
        return $result;
    }

    public function createInvoiceForOrder($orderIncrementId) {
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        try {
            if (!$order->canInvoice()) {
                Mage::log("Cannot create an invoice for order:" . $order->getIncrementId(), null, 'invoice.log');
                unset ($order);
                return;
            }
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

            if (!$invoice->getTotalQty()) {
                Mage::log("Cannot create an invoice without products. for order:" . $order->getIncrementId(), null, 'invoice.log');
                unset ($order);
                return;
            }
            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
            $invoice->register();
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
            $transactionSave->save();
        } catch (Mage_Core_Exception $e) {
            Mage::log("Error:" . $e->getMessage(), null, 'invoice.log');
        }
        unset ($order);
    }

    public function setWmsFileStatus($status, $format = false){
        if(!$status){
            return $this;
        }
        $value = array();
        if($this->getData('wms_file_status') && $this->getData('wms_file_status') != ''){
            @$value = unserialize($this->getData('wms_file_status'));
        }
        $statuses = Mage::helper('cpwms')->getStatuses();
        if(!$value)
        {
            if(is_numeric($status)){
                $value['last'] = $statuses[$status];
                $_SESSION['wms_log_status'] = $statuses[$status];
            } else{
                $value['last'] = $statuses[$this->getData('wms_file_status')];
                $_SESSION['wms_log_status'] = $statuses[$this->getData('wms_file_status')];
                $value[$format] = $status;
            }
        }
        else
        {
            if(!$format){
                if(is_numeric($status)){
                    $value['last'] = $statuses[$status];
                    $_SESSION['wms_log_status'] = $statuses[$status];
                } else{
                    $value['last'] = $status;
                    $_SESSION['wms_log_status'] = $status;
                }
            }
            else
            {
                $value[$format] = $status;
                $_SESSION['wms_log_status'] = $status;
            }
        }
        $this->setData('wms_file_status', serialize($value));
        return $this;
    }

    public function setWmsRequestStatus($status, $format = false){
        $this->setWmsFileStatus($status, $format);

        if($filename = basename(Mage::registry('wms_response_filename'))) {
            $this->setData('wms_file',str_replace($filename,'',$this->getData('wms_file')).' '.$filename);
            $this->save();
        }
        return $this;
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

        @unlink($file);
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
        @unlink($file);
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
                    @unlink($local_file);
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
}
