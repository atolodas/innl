<?php

class Cafepress_CPCore_Model_Cafepress_Order extends Mage_Core_Model_Abstract
{
    public function cancel($storeId, $orderNo, $comment = ''){
//        Mage::log(array($orderNo, Mage::getStoreConfig('common/partner/id', $storeId)), null, 'lomantik.log');
        $xml = '<CancelCPSalesOrderByPartner xmlns="http://Cafepress.com/">
                    <PartnerID>'.Mage::getStoreConfig('common/partner/id', $storeId).'</PartnerID>
                    <SalesOrderNo>'.$orderNo.'</SalesOrderNo>
                    <CancelComment>'.$comment.'</CancelComment>
                </CancelCPSalesOrderByPartner>';
        $result = Mage::getModel('cpcore/xmlformat_outbound')->sendXmlOverSoap($xml, 0, false, 'CancelCPSalesOrderByPartner');

        return $result;
    }
}