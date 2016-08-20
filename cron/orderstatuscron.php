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
    $orderStatuses = Mage::getModel('cpcore/xmlformat')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('type', Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getIdTypeByName('orderstatus'))
                ->addAttributeToFilter('status','1');
    foreach ($orderStatuses as $orderStatus){
        if(Mage::helper('cpcore')->checkFormatDate($orderStatus->getLastSent(), $orderStatus->getSchedulepro())){
            Mage::app()->getRequest()->setParam('store',0); 
            $orderStatus->setLastSent(now())->save();
            $orderStatus->save();
            Mage::getModel('cpcore/order_observer')->generateOrderStatusXmlByCron($orderStatus);
            break;
        } else{
            echo $orderStatus->getSchedulepro()." not left. Will try later.<br/>";
        }
    }

   
} catch (Exception $e) {
    Mage::log($e->getMessage(),null,'exception.log');
}
