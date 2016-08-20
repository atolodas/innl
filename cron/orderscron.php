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
		->addAttributeToFilter('type', Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getIdTypeByName('order'))
		->addAttributeToFilter('status','1');

	foreach ($xmlFormats as $xmlFormat){
        echo "Start format:{$xmlFormat->getId()} <br/>";
        $orders = Mage::getSingleton('cpcore/xmlformat_format_order')->getOrderCollection($xmlFormat->getData('entity_id'));
        if(Mage::helper('cpcore')->checkFormatDate($xmlFormat->getLastSent(), $xmlFormat->getSchedulepro())){
            Mage::app()->getRequest()->setParam('store',0);
            $xmlFormat->setLastSent(now());
            $xmlFormat->save();
            foreach($orders as $order) {
                echo "<p>";
                echo "Order id ".$order->getIncrementId()."<br/>";
                    echo Mage::getStoreConfig('common/partner/delay')." minute(s) left. ";
                    $order = Mage::getModel('sales/order')->load($order->getId());
                    if(is_object($order->getPayment())) {
                        echo "Payment exists. Trying to generate xml. <br/>";
                        Mage::getModel('cpcore/order_observer')->generateXmlByCron($order, $xmlFormat->getData('entity_id'));
                    } else {
                        echo "Payment not exists. Will try later  <br/>";
                    }
                echo "</p>";
            }
        } else{
            echo $xmlFormat->getSchedulepro()." not left. Will try later.<br/>";
        }
	}
} catch (Exception $e) {
    Mage::log($e->getMessage(),null,'exception.log');
}
