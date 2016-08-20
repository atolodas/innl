<?php
require_once realpath(dirname(__FILE__).'/../../../app/Mage.php');

if (!Mage::isInstalled()) {
	echo "Application is not installed yet, please complete install wizard first.";
	exit;
}

$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', @$_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', @$_SERVER['SCRIPT_FILENAME']);

Mage::app('admin')->setUseSessionInUrl(false);

try {
	$xmlFormats = Mage::getModel('cpcore/xmlformat')
		->getCollection()
		->addAttributeToSelect('*')
		->addAttributeToFilter('type', Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getIdTypeByName('creditmemo'))
		->addAttributeToFilter('status','1');

	foreach ($xmlFormats as $xmlFormat){
        $orders = Mage::getSingleton('cpcore/xmlformat_format_order')->getOrderCollection($xmlFormat->getData('entity_id'));
        if(Mage::helper('cpcore')->checkFormatDate($xmlFormat->getLastSent(), $xmlFormat->getSchedulepro())){
            Mage::app()->getRequest()->setParam('store',0);
            $xmlFormat->setLastSent(now());
            $xmlFormat->save();
            foreach($orders as $order) {
                if($order->hasCreditmemos()) {
                    foreach($order->getCreditmemosCollection() as $creditmemo) {
                        echo "Order id ".$order->getIncrementId().". Credit Memo Id {$creditmemo->getIncrementId()} ";
                            if(substr_count($order->getWmsFile(), 'CM-'.date('m-d-Y').'-'.$creditmemo->getIncrementId().'.xml')==0) {
                                echo Mage::getStoreConfig('common/partner/delay')." minute(s) left. <br/>";
                                Mage::getModel('cpwms/order_observer')->generateCreditMemoXmlByCron($order,$creditmemo,$xmlFormat->getData('entity_id'));
                            } else {
                                echo 'Creditmemo xml already exist <br/>';
                            }
                    }
                } else {
                    echo "Order {$order->getIncrementId()} don't have creditmemos <br/>";
                }
            }
        } else{
            echo $xmlFormat->getSchedulepro()." not left. Will try later.<br/>";
        }
	}
} catch (Exception $e) {
    Mage::log($e->getMessage(),null,'exception.log');
}
