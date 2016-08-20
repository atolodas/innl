<?php
define('CRON_ROOT', getcwd());
require_once (CRON_ROOT.'/../app/Mage.php');

if (!Mage::isInstalled()) {
    echo "Application is not installed yet, please cmplete install wizard first.";
    exit;
}

$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', @$_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', @$_SERVER['SCRIPT_FILENAME']);

Mage::app('admin')->setUseSessionInUrl(false);
try {
   $downfileparsresps = Mage::getModel('cpcore/xmlformat')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('type', Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getIdTypeByName('downfileparsresp'))
                ->addAttributeToFilter('status','1');
    foreach ($downfileparsresps as $downfileparsresp){
        if(Mage::helper('cpcore')->checkFormatDate($downfileparsresp->getLastSent(), $downfileparsresp->getSchedulepro())){
            Mage::app()->getRequest()->setParam('store',0);
            $downfileparsresp->setLastSent(now());
            $downfileparsresp->save();
            echo "Downfileparsresp id ".$downfileparsresp->getId()."<br/>";
            echo "Creation date: ".$downfileparsresp->getCreatedAt()."<br/>";
            echo "Now: ".date('Y-m-d H:i:s')."<br/>";
                echo Mage::getStoreConfig('common/partner/delay')." minute(s) left. \n";
                Mage::getModel('cpcore/order_observer')->generateDownfileparsrespByCron($downfileparsresp);
        } else{
            echo $downfileparsresp->getSchedulepro()." not left. Will try later.<br/>";
        }
    }

    if(!file_exists(Mage::getBaseDir('media').'/')){
        return;
    }
    if(!file_exists(Mage::getBaseDir('media').'/xmls/')){
        return;
    }
    if(!file_exists(Mage::getBaseDir('media').'/xmls/inbound/')){
        return;
    }
    $inbound_dir = opendir(Mage::getBaseDir('media').'/xmls/inbound/');
    $inbound_files = array();
    while(($file = readdir($inbound_dir)) !== false){
        $filepath = Mage::getBaseDir('media').'/xmls/inbound/'.$file;
        if(filetype($filepath) == 'file'){
            $info = pathinfo($filepath);
            if($info['extension'] == 'xml'){
                $inbound_files[] = $file;
            }
        }
    }

    $order_files = array();
    $orders = Mage::getModel('cpcore/sales_order')->getCollection();
    foreach($orders as $order){
        $wms_file_field = $order->getWmsFile();
        $wms_file_field_array = explode(' ', $wms_file_field);
        foreach($wms_file_field_array as $wms_file){
            if($wms_file != ""){
                $wms_file_array = explode('/', $wms_file);
                if($wms_file_array[0] == 'inbound'){
                    $order_files[] = $wms_file_array[1];
                }
            }
        }
    }

    echo 'Files to delete from the server:<br/>';
    $first = true;
    $result = array();
    foreach($inbound_files as $inbound_file){
        if(!in_array($inbound_file, $order_files)){
            $result[] = $inbound_file;
            if($first){
                $first = false;
            } else{
                echo ', ';
            }
            echo $inbound_file;
        }
    }
    echo ';<br/>';

    Mage::getModel('cpcore/xmlformat_outbound')->deleteFilesFromFtp($result);
    echo 'Files deleted.<br/>';

   
} catch (Exception $e) {
    echo $e->getMessage();
    Mage::log($e->getMessage(),null,'exception.log');
}
