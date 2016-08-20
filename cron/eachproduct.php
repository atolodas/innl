<?php
define('CRON_ROOT', '/var/www');
require_once (CRON_ROOT.'/app/Mage.php');
if (!Mage::isInstalled()) {
	echo "Application is not installed yet, please complete install wizard first.";
	exit;
}

$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', @$_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', @$_SERVER['SCRIPT_FILENAME']);
ini_set('memory_limit','2048M');
Mage::app('admin')->setUseSessionInUrl(false);

try {
$formatId = 0;
        if (!empty($argv) && isset($argv[1])) {$formatId = $argv[1];}
else { $formatId = $_GET['f']; } 
        if (!$formatId) {	
$xmlFormats = Mage::getModel('cpcore/xmlformat')
		->getCollection()
		->addAttributeToSelect('*')
		->addAttributeToFilter('type', Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getIdTypeByName('transformer'))
		->addAttributeToFilter('status','1');
    } else {
      $xmlFormats = array( Mage::getModel('cpcore/xmlformat')->load($formatId));
    }
    $storeId = 0;
	foreach ($xmlFormats as $xmlFormat){
        echo "Start format:{$xmlFormat->getId()} <br/>";

      $entityTypeId =  Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId();
      if(!isset($_GET['id'])) {
        $products = Mage::getModel('catalog/product')->getCollection();
      } else {
        $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('entity_id',$_GET['id']);
      }
      $products = Mage::helper('cpcore')->applyPrecondition($xmlFormat,$products,$entityTypeId);
      echo $products->getSelect(); echo "||| {$products->getSize()}  product found. <br/><br/>";
      $products->addAttributeToSelect('*');
      if(Mage::helper('cpcore')->checkFormatDate($xmlFormat->getLastSent(), $xmlFormat->getSchedulepro())){
            Mage::app()->getRequest()->setParam('store',0);
            foreach($products as $product) {
               $xmlformatModel = Mage::getModel('cpcore/xmlformat_format_transformer')->getModelformatByName($xmlFormat->getData('name'),$storeId);

            //    $product = Mage::getModel('catalog/product')->load($product->getId());
                $xmlformatModel->addVar('product_id', $product->getId());
                    $xmlformatModel->addVariable('product', $product);

            if(!$xmlformatModel->checkCondition()) {
                  echo "product {$product->getId()} {$product->getName()} NOT passed condition {$xmlFormat->getCondition()} <br/>";
                  continue;
                } else {
                  echo "product {$product->getId()} {$product->getName()}  passed condition {$xmlFormat->getCondition()} <br/>";
                }
                echo "<p>";
                echo "product ".$product->getSku()."<br/>";
                    echo Mage::getStoreConfig('common/partner/delay')." minute(s) left. ";
                    $xmlformatModel->processRequest();
                    $xmlResult = $xmlformatModel->getServerResponse();
                    echo 'Ressponse:';
                    Zend_Debug::dump($xmlResult);
                    $result = $xmlformatModel->processResponse();
                    echo "</p>";
            }
        } else{
            echo $xmlFormat->getSchedulepro()." not left. Will try later.<br/>";
        }
	}
} catch (Exception $e) {
echo $e->getMessage();
    Mage::log($e->getMessage(),null,'exception.log');
}
