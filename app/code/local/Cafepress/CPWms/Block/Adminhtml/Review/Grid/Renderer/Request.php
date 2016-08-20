<?php

class Cafepress_CPWms_Block_Adminhtml_Review_Grid_Renderer_Request extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $orderIncrementId = $this->getRequest()->getParam('order');
//        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $itemStoreId = $row->getData('store_id');
        if ($itemStoreId){
            $storeId = $itemStoreId;
        } else {
            $storeId = (int)$this->getRequest()->getParam('store', 0);
        }

    	$formatId = $row->getData($this->getColumn()->getIndex());
        $format = Mage::getModel('cpwms/xmlformat')->load($formatId);
        $formatType = Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_type')->getNameTypeById($format->getType());
        $request = 'Error! Method: '.__METHOD__; #TODO INL: Exseption!
        try{
            switch ($formatType){
                case 'order':
                    if($orderIncrementId){ 
                        $order = Mage::getModel('cpwms/sales_order')->loadByIncrementId($orderIncrementId);
                        $request = Mage::getModel('cpwms/order_observer')->getOrderXmlByFormat($order, $formatId);
                    } else{
                        $request = $format->getHeader().$format->getMainPart().$format->getAddresses().$format->getProduct().$format->getFooter();
                    }
                    break;
                case 'creditmemo':
                    $request = $format->getHeader().$format->getMainPart().$format->getAddresses().$format->getProduct().$format->getFooter();
                    if($orderIncrementId){ 
                        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
                        if(isset($order) && $order->hasCreditmemos()){
                            $creditMemo = $order->getCreditmemosCollection()->getFirstItem();
                            $request = Mage::getModel('cpwms/order_observer')->getXmlByFormat($order, $formatType, $creditMemo, $formatId);
                        } 
                    }
                    break;
                case 'orderstatus':
                    if ($orderIncrementId){
                        $orderStatus = Mage::getModel('cpwms/xmlformat_format_orderstatus')
                            ->setStoreId($storeId)
                            ->load($formatId);

                        $request = $orderStatus
                            ->addVariables(array(
                                'date' => Mage::getModel('cpwms/xmlformat_variable_date'),
                            ))
                            ->generateRequest();
                    } else {
                        $request = $format->getRequest();
                    }
                    break;
                case 'downfileparsresp':
                    $request = $format->getRequest();
                    break;
                case 'transformer':
                    $request = $format->getRequest();
                    break;
            }
        
        } catch (Exception $e){
            return 'error! method:'.__METHOD__;
        }
        
//        try {
//            
//            $sXml = new SimpleXMLElement($request);
//            $dom = dom_import_simplexml($sXml)->ownerDocument;
//            $dom->formatOutput = true;
//            $output = $dom->saveXML();
////            $output = '<pre>'.htmlspecialchars($dom->saveXML()).'</pre>';
//            $request = $output;
//            
//        } catch (Exception $e){
//            echo $e->getMessage();
////            $request = 'asd';
//        }

        return '<div class="wms-grid-request"><pre>'.Mage::helper('cpwms')->formatXml($request, '<br/>').'</pre></div>';
//        return '<div class="wms-grid-request"><pre>'.htmlspecialchars($request).'</pre></div>';
    }
}

?>
