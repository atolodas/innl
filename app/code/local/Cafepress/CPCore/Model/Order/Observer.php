<?php

class Cafepress_CPCore_Model_Order_Observer extends Mage_Sales_Model_Observer {

    protected $sequence = array(
        'header',
        'main_part',
        'addresses',
        'product',
        'footer'
    );

    public function generateXmlByCron($order, $formatId) {
        try {
            $storeId = $order->getStoreId(); //Mage::getModel('cpcore/xmlformat')->getStoreId(); //@todo: storeId ?
            $orderModel = Mage::getModel('cpcore/xmlformat_format_order')
                    ->setStoreId($order->getStoreId())
                    ->setOrderById($order->getId())
                    ->setFormat($formatId);

            if ($orderModel->checkCondition() != true) {
                echo "Condition is not performed. <br/>";
                return;
            }
            echo "Condition is performed. <br/>";
            Mage::log('*START********************************************', null, 'debug_orderformat.log');

            Mage::log('*ORDER ID:'.$order->getId(), null, 'debug_orderformat.log');
            Mage::log('*FROMAT ID:'.$formatId, null, 'debug_orderformat.log');
            Mage::log('*ORDER STATUS:'.$order->getStatus(), null, 'debug_orderformat.log');
            
            if ($order != false) {
                $_SESSION['number'] = $order->getIncrementId();
                if (Mage::registry('number')) {
                    Mage::unregister('number');
                }
                Mage::register('number', $order->getIncrementId());
            }
            $xml = $this->getOrderXmlByFormat($order, $formatId);

            Mage::log('*REQUEST ORDER XML:', null, 'debug_orderformat.log');
            Mage::log($xml, null, 'debug_orderformat.log');

            $xmlformat = Mage::getModel('cpcore/xmlformat')
                    ->setStoreId($storeId)
                    ->loadByAttributes(array(
//                'type' => $type = Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getType('order'),
                'status' => '1', //1-enabled, 2- disabled
                'entity_id' => $formatId
                    ));
            $orderFormat = Mage::getModel('cpcore/xmlformat_format_order')
                    ->setStoreId($storeId)
                    ->load($xmlformat->getId());
            $customUrl = false;
            if ($xmlformat->getCustomUrl()) {
                $customUrl = array('url' => $xmlformat->getCustomUrl());
            }
            
            Mage::log('*RESPONSE START:', null, 'debug_orderformat.log');
            Mage::log('*ORDER STATUS:'.$order->getStatus(), null, 'debug_orderformat.log');
            Mage::log('*ORDER:'.$order->getId(), null, 'debug_orderformat.log');
            Mage::log('*FROMAT:'.$formatId, null, 'debug_orderformat.log');
            Mage::log('*CUSTOM URL:', null, 'debug_orderformat.log');
            Mage::log($customUrl, null, 'debug_orderformat.log');
            
            $response = Mage::getModel('cpcore/xmlformat_outbound')->outboundFile($xml, $order, $formatId, false, $customUrl);
            
            Mage::log('*RESPONSE RESULT:', null, 'debug_orderformat.log');
            Mage::log($response, null, 'debug_orderformat.log');
            
            if ($response) {
                Mage::log('*PROCESS RESONSE START', null, 'debug_orderformat.log');
                Mage::log('*ORDER STATUS:'.$order->getStatus(), null, 'debug_orderformat.log');
                $tmp = $orderFormat->processResponse($response);
                Mage::log('*PROCESS RESONSE RESULT:', null, 'debug_orderformat.log');
                Mage::log($tmp, null, 'debug_orderformat.log');
            }
            Mage::dispatchEvent('order_format_preperformed', array('order' => $order, 'format' => $xmlformat));
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'orders.log');
            Mage::log('*ERROR:', null, 'debug_orderformat.log');
            Mage::log($e->getMessage(), null, 'debug_orderformat.log');
        }
        $order = Mage::getModel('sales/order')->load($order->getId());
        Mage::log('*ORDER STATUS:'.$order->getStatus(), null, 'debug_orderformat.log');
        
        Mage::log('*END********************************************', null, 'debug_orderformat.log');
    }

    public function generateCreditMemoXmlByCron($order, $creditmemo, $formatId) {
        if (!$creditmemo->getCpWmsFile()) {
            try {
                $storeId = $order->getStoreId(); //Mage::getModel('cpcore/xmlformat')->getStoreId(); //@todo: storeId ?
                $xml = $this->getCreditMemoXmlByFormat($order, $creditmemo, $formatId);
                Mage::getModel('cpcore/xmlformat_outbound')->outboundFile($xml, $order, $formatId, $creditmemo);
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'orders.log');
            }
        }
    }


    /**
     *
     * @param type $order
     * @param type $typeOfFormat : {ORDER, CREDITMEMO}
     * @return type 
     */
    public function getXmlByFormat($order, $formatType = 'order', $creditMemo = false, $formatId = 0) {
        $order = Mage::getModel('cpcore/sales_order')->load($order->getId());
        Mage::register('number', $order->getIncrementId(), true);
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
        $type = Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getType($formatType);
        $store = Mage::getModel('cpcore/xmlformat')->setStoreId($storeId)->getStore();
        $xmlformat = Mage::getModel('cpcore/xmlformat')
                ->setStoreId($storeId)
                ->loadByAttributes(array(
            'type' => $type,
//            'status' => '1', //1-enabled, 2- disabled
            'entity_id' => $formatId
                ));
        if (!$xmlformat || !$xmlformat->getId()){
            return false;
        }

        $xml = '';
        $dataFormat = $xmlformat->getData();
        $template = Mage::getModel('cpcore/template')->setStoreId($storeId);

//        $coupon = Mage::getModel('salesrule/coupon');
//        /** @var Mage_SalesRule_Model_Coupon */
//        $discountRule = Mage::getModel('salesrule/rule')->load($coupon->load($order->getCouponCode(), 'code')->getRuleId());
////        $discountPercent = $discountRule->getDiscountAmount() / 100;
//        $discountAmount = $discountRule->getDiscountAmount();
        $discountPercent = 0;
        if ($order->getDiscountPercent()) {
            $discountPercent = $order->getDiscountPercent();
        }

        $itemsPrice = 0;
        $continyityPrice = 0;
        $itemsPriceDiscount = 0;
        $itemsPriceDiscountAlter = 0;
        foreach ($order->getAllItems() as $item) {
            $product = Mage::getModel('cpcore/catalog_product')->load($item->getProductId());
            $price = $item->getPrice();
            if ($product->getContinuityIs()) { // || (!$product->getContinuityIs() && $item->getIsContinuity())
                $continyityPrice += $product->getContinuityPayment2() * $item->getQtyOrdered();
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
            $itemPrice = $item->getPrice() * $item->getQtyOrdered();
            if ($order->getDiscountPercent()) {
                $itemPriceDiscount = $price * $item->getQtyOrdered() * (1 - $order->getDiscountPercent());
            } else {
                $itemPriceDiscount = $price * $item->getQtyOrdered() - $item->getDiscountAmount();
            }
            $itemsPrice += $itemPrice;
            $itemPriceDiscount = round($itemPriceDiscount * 1.00, 2);
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
        $hardVar['payment2'] = $continyityPrice;
        $hardVar['items_price_discount_tax'] = $itemsPriceDiscount * $order->getTaxPercent() / 100;
        if ($order->getDiscountPercent()) {
            $hardVar['continyity_discount'] = $continyityPrice * (1 - $order->getDiscountPercent());
        } elseif ($continyityPrice != 0) {
            $hardVar['continyity_discount'] = $continyityPrice - $item->getDiscountAmount();
        } else {
            $hardVar['continyity_discount'] = 0;
        }

        $hardVar['continyity_discount_tax'] = $hardVar['continyity_discount'] * $order->getTaxPercent() / 100;
        $message = Mage::helper("giftmessage/message")->getGiftMessage($order->getGiftMessageId());

        foreach ($hardVar as $key => $val) {
            $hardVar[$key] = round($val * 1.00, 2);
        }
        $hardVar['items_price_discount'] = $itemsPriceDiscount;

        if (!isset($add['cc_type'])) {
            $add['cc_type'] = '';
        }
        if (!isset($add['last_trans_id'])) {
            $add['last_trans_id'] = '';
        }
        if (!isset($add['cc_last4'])) {
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
                        if ($item->getChildrenItems()) continue;
//                        $product = Mage::getModel('catalog/product')->load($item->getProductId());
                        $product = Mage::getModel('cpcore/catalog_product')->load($item->getProductId());
                        if ($product->getTypeId() == 'simple') {
                            $product->setIsSimple(true);
                        } else {
                            $product->setIsSimple(false);
                        }
                        $parent_price = '';

                        $parent_product = null;
                        if ($item->getParentItem()) {
                            $parent_price = $item->getParentItem()->getPrice();
                            $parent_product = Mage::getModel('cpcore/catalog_product')->load($item->getParentItem()->getProductId());
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

                        if ($product->getContinuityIs() || (!$product->getContinuityIs() && $item->getIsContinuity())) {
                            $continuity_price = $product->getContinuityPrice();
                            $productDiscount = $product->getContinuityDiscount();
                            if ($productDiscount) {
                                $discountType = strtolower($product->getAttributeText('continuity_discount_type'));
                                if ($discountType == 'fix') {
                                    $continuity_price = $continuity_price - $productDiscount;
                                } else {
                                    $continuity_price = $continuity_price - ($continuity_price / 100) * $productDiscount;
                                }
                            }
                            $product->setContinuityPrice($continuity_price);
                        }
//                        /**
//                         * Get Cafepress Product Id
//                         * START
//                         */
//                        $keyRegistry = 'cpext_product_get_custom_option_unrecursia';
//                        Mage::register($keyRegistry, true);
//                        
//                        if ($parent_product){
//                            $idProductForCpCustomOption = $parent_product->getId();
////                            $parentItem = $order->getItemById($item->getParentItem()->getId());
//                            $options = $item->getParentItem()->getProductOptions();
////                            $parentData = $parentItem->getData();
////                            $options = unserialize($parentData['product_options']);
//                        } else {
//                            $idProductForCpCustomOption = $item->getProductId();
//                            $options = $item->getProductOptions();
//                        }
//                        
//                        $optionId = Mage::helper('cpext/option')->getCustogemOptionIdByTitle($idProductForCpCustomOption, 'Cafepress Product ID');
//                        if (isset($options['options'])) {
//                            foreach($options['options'] as $optionn){
//                                if ($optionn['option_id'] == $optionId){
//                                    $cpProductId = $optionn['value'];
//                                }
//                            }
//                        }
//                        $item->setCpProductId($cpProductId);
//                        /**
//                         * Get Cafepress Product Id
//                         * END
//                         */
                        
                        $xml .= $this->substitutionVarsToXml(
                                '', $template, $vars = array(
                            'order' => $order,
                            'store' => $store,
                            'product' => $product,
                            'parent' => $parent_product,
                            'item' => $item,
                            'counter' => $counter,
                            'partnerId' => $partnerId,
                            'ps' => $ps,
                            'sp' => $sp,
                            'retailerTerms' => $retailer_terms,
                            'payment' => $payment,
                            'payment2' => ($hardVar['items_price_discount'] + $order->getShippingAmount() + $hardVar['items_price_discount_tax'] - $order->getGrandTotal()),
                            'discount_percent' => $discountPercent,
                            'add' => $add,
                            'add_cc_type' => $add['cc_type'],
                            'add_last_trans_id' => $add['last_trans_id'],
                            'add_cc_last4' => $add['cc_last4'],
                            'message' => $message,
                            'items_price_discount' => $hardVar['items_price_discount'],
                            'items_price_discount_tax' => $hardVar['items_price_discount_tax'],
                            'continyity_discount' => $hardVar['continyity_discount'],
                            'continyity_discount_tax' => $hardVar['continyity_discount_tax'],
                                ));

                        $counter++;
                    }
                } else {
                    foreach ($creditMemo->getAllItems() as $item) {
                        $unit_cost = null; //??

                        if ($item->getOrderItem()->getChildrenItems())
                            continue;
                        $product = Mage::getModel('cpcore/catalog_product')->load($item->getProductId());
                        if ($product->getTypeId() == 'simple') {
                            $product->setIsSimple(true);
                        } else {
                            $product->setIsSimple(false);
                        }
                        $parent_price = '';
                        $parent_product = null;
                        if ($item->getOrderItem()->getParentItem()) {
                            $parent_price = $item->getOrderItem()->getParentItem()->getPrice();
                            $parent_product = Mage::getModel('cpcore/catalog_product')->load($item->getOrderItem()->getParentItem()->getProductId());
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


                        $xml .= $this->substitutionVarsToXml(
                                '', $template, $vars = array(
                            'order' => $order,
                            'store' => $store,
                            'product' => $product,
                            'parent' => $parent_product,
                            'item' => $item,
                            'counter' => $counter,
                            'creditMemo' => $creditMemo,
                            'partnerId' => $partnerId,
                            'ps' => $ps,
                            'sp' => $sp,
                            'retailerTerms' => $retailer_terms,
                            'retailer_terms' => $retailer_terms,
                            'payment' => $payment,
                            'add' => $add,
                            'discountPercent' => $discountPercent,
                            'discount_percent' => $discountPercent,
                            'items_price_discount' => (float)$hardVar['items_price_discount'],
                            'items_price_discount_tax' => $hardVar['items_price_discount_tax'],
                            'continyity_discount' => $hardVar['continyity_discount'],
                            'continyity_discount_tax' => $hardVar['continyity_discount_tax'],
                                ));

                        $counter++;
                    }
                }
                continue;
            }
            $xml .= $this->substitutionVarsToXml(
                    '', $template, $vars = array(
                'order' => $order,
                'creditMemo' => $creditMemo,
                'store' => $store,
                'partnerId' => $partnerId,
                'ps' => $ps,
                'sp' => $sp,
                'retailerTerms' => $retailer_terms,
                'retailer_terms' => $retailer_terms,
                'payment' => $payment,
                'add' => $add,
                'add_cc_type' => $add['cc_type'],
                'add_last_trans_id' => $add['last_trans_id'],
                'add_cc_last4' => $add['cc_last4'],
                'message' => $message,
                'payment2' => $hardVar['payment2'],
                'total' => $order->getGrandTotal() + $hardVar['payment2'],
                'discountPercent' => $discountPercent,
                'discount_percent' => $discountPercent,
                'items_price_discount' => $hardVar['items_price_discount'],
                'items_price_discount_tax' => $hardVar['items_price_discount_tax'],
                'continyity_discount' => $hardVar['continyity_discount'],
                'continyity_discount_tax' => $hardVar['continyity_discount_tax'],
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
//
//        try {
//            Mage::log('Order XML #' . $order->getId() . ' generating started', null, 'orders.log');
//            $storeId = $order->getStoreId();
//            $orderModel = Mage::getModel('cpcore/xmlformat_format_order')
//                    ->setStoreId($storeId)
//                    ->load($order->getId())
//                    ->setOrderById($order->getId());
//            $request = $orderModel
//                    ->addVariables(array(
//                        'date' => Mage::getModel('cpcore/xmlformat_variable_date'),
//                    ))
//                    ->generateRequest();
//            Mage::log('OrderStatus #' . $orderStatus->getId() . ' has been processed. Response:' . $response, null, 'orders.log');
//        } catch (Exception $e) {
//            Mage::log($e->getMessage(), null, 'orders.log');
//        }
//        return $this->getXmlByFormat($order, 'order', false, $formatId);
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
            $orderStatus = Mage::getModel('cpcore/xmlformat_format_orderstatus')
                    ->setStoreId($storeId)
                    ->load($orderStatus->getId());
            $request = $orderStatus
                    ->addVariables(array(
                        'date' => Mage::getModel('cpcore/xmlformat_variable_date'),
                    ))
                    ->generateRequest();
            $server = array();
            if ($orderStatus->getCustomUrl()) {
                $server['url'] = $orderStatus->getCustomUrl();
            }
            $response = Mage::getModel('cpcore/xmlformat_outbound')->sendXmlOverPost($request, $storeId, $server);

            if ($response) {
                $result = $orderStatus->processResponse($response);
            } else {
                $result = false;
            }

            Mage::dispatchEvent('orderstatus_format_preperformed_action', array(
                'format' => $orderStatus,
                'request' => $request,
                'response' => $response,
                'result' => $result
            ));
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'wms.log');
            return false;
        }
    }

    public function generateDownfileparsrespByCron($dfpr) {
        try {
            $storeId = $dfpr->getStoreId();
            $dfprModel = Mage::getModel('cpcore/xmlformat_format_downfileparsresp')
                    ->setStoreId($storeId)
                    ->load($dfpr->getId());
            $dfprModel->downloadRequestFiles();
            $dfprModel->processResponse();
            $dfprModel->deleteFileAfterSuccess();

            Mage::dispatchEvent('downfileparsresp_format_performed', array(
                'format' => $dfprModel,
            ));
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'wms.log');
        }
    }

}
