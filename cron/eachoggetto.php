<?php
define('CRON_ROOT', '/var/www');
require_once CRON_ROOT . '/app/Mage.php';
define('EOL', "\n");
ini_set('memory_limit', '2048M');
ini_set('display_errors', 1);

error_reporting(E_ALL | E_STRICT);

function printInfo($string) {
	print $string . ' ||| memory: ' . (memory_get_usage(true) >> 20) . 'Mb' . EOL;
	print "================";
}

$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', @$_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', @$_SERVER['SCRIPT_FILENAME']);

Mage::app('admin')->setUseSessionInUrl(false);

try {

	$formatId = 0;
	if (!empty($argv) && isset($argv[1])) {$formatId = $argv[1];}

	if (!$formatId) {
		$xmlFormats = Mage::getModel('cpcore/xmlformat')
			->getCollection()
			->addAttributeToSelect('*')
			->addAttributeToFilter('type', Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getIdTypeByName('transformer'))
			->addAttributeToFilter('status', '1')
			->addAttributeToFilter('schedule', '0');
	} else {
		$xmlFormats = array(Mage::getModel('cpcore/xmlformat')->load($formatId));
	}

	$storeId = 0;

	foreach ($xmlFormats as $xmlFormat) {
		printInfo("Start format: {$xmlFormat->getId()}");

		if (!isset($_GET['id'])) {
			$oggettos = Mage::getModel('score/oggetto')->getCollection();
		} else {
			$oggettos = Mage::getModel('score/oggetto')->getCollection()->addAttributeToFilter('entity_id', $_GET['id']);
		}
		$entityTypeId = Mage::getSingleton('eav/config')->getEntityType('score_oggetto')->getId();
		$oggettos->addAttributeToSelect('*');
		$oggettos = Mage::helper('cpcore')->applyPrecondition($xmlFormat, $oggettos, $entityTypeId);

		printInfo($oggettos->getSelect() . " ||| {$oggettos->getSize()}  oggettos found.");

		if (Mage::helper('cpcore')->checkFormatDate($xmlFormat->getLastSent(), $xmlFormat->getSchedulepro())) {
			Mage::app()->getRequest()->setParam('store', 0);
			foreach ($oggettos as $oggetto) {
				$xmlformatModel = Mage::getModel('cpcore/xmlformat_format_transformer')->getModelformatByName($xmlFormat->getData('name'), $storeId);
				$xmlformatModel->addVar('oggetto_id', $oggetto->getId());
				$xmlformatModel->addVariable('oggetto', $oggetto);
				if (!$xmlformatModel->checkCondition()) {
					printInfo("oggetto {$oggetto->getId()} {$oggetto->getName()} NOT passed condition {$xmlFormat->getCondition()}");
					continue;
				} else {
					printInfo("oggetto {$oggetto->getId()} {$oggetto->getName()}  passed condition {$xmlFormat->getCondition()}");
				}
				printInfo("Oggetto " . $oggetto->getSku() . ' ' . $oggetto->getName() . '|' . $oggetto->getTitle());
				printInfo(Mage::getStoreConfig('common/partner/delay') . " minute(s) left.");
				$xmlformatModel->processRequest();
				$xmlResult = $xmlformatModel->getServerResponse();
				printInfo('Ressponse:');
				printInfo($xmlResult);
				$result = $xmlformatModel->processResponse();
			}
		} else {
			printInfo($xmlFormat->getSchedulepro() . " not left. Will try later.");
		}
	}
} catch (Exception $e) {
	printInfo($e->getMessage());
	Mage::log($e->getMessage(), null, 'exception.log');
}
