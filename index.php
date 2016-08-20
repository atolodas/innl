<?php
/**
 * Error reporting
 */
error_reporting(E_ALL | E_STRICT);

/**
 * Compilation includes configuration file
 */
define('MAGENTO_ROOT', getcwd());

$compilerConfig = MAGENTO_ROOT . '/includes/config.php';
if (file_exists($compilerConfig)) {
	include $compilerConfig;
}

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
$maintenanceFile = 'maintenance.flag';

if (!file_exists($mageFilename)) {
	if (is_dir('downloader')) {
		header("Location: downloader");
	} else {
		echo $mageFilename . " was not found";
	}
	exit;
}

//if (file_exists($maintenanceFile)) {
//    include_once dirname(__FILE__) . '/errors/503.php';
//    exit;
//}

require_once $mageFilename;



//require_once './MongoDB.php';
//require_once './MongoInt32.php';
//require_once './MongoClient.php';
#Varien_Profiler::enable();

// TEST NOSQL
//$dbhost = 'Pauls-MacBook-Pro.local:27017';
//$dbname = 'test';
//
//// Connect to test database
//$m = new MongoClient("mongodb://$dbhost");
//echo $db = $m->setDb($dbname);
ini_set('display_errors', 1);
ini_set('memory_limit', '2048M');

require(MAGENTO_ROOT . '/disqusapi/disqusapi.php');
$disqus = new DisqusAPI(Mage::getStoreConfig('score/comments/disqus_key')); // Wrong config here?

if (preg_match('#/backend/#i', $_SERVER['REQUEST_URI'])) {
	$store = 'admin';
	if (Mage::registry('scode')) {
		Mage::unregister('scode');
	}

	Mage::register('scode', $store);

} else {
require_once MAGENTO_ROOT . '/storemapping.php';

	$_GET['___store'] = $store;
}
// if ($store != 'admin') {
// 	if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
//		Mage::setIsDeveloperMode(true);
// 	}
// }
$maintenanceFlag = Mage::getStoreConfig('general/store_information/maitenance');
if($maintenanceFlag && $store != 'admin') {
	include_once dirname(__FILE__) . '/maintenance/index-'.Mage::registry('slang').'.php';
	exit;
}

# replace the REQUEST_URI such as /en/blah.html with /blah.html
if (strpos($_SERVER['REQUEST_URI'], "/en/") !== false) {
	$lang = 'en';
} else {
	$lang = 'ru'; # which is the default language of the default store of your installation (/)
}

$_SERVER['REQUEST_URI'] = preg_replace("#^/$lang(/.*)#i", '$1', $_SERVER['REQUEST_URI']);
# we want our /lang/ urls to take priority over store querystrings
# but not if we are in the admin panel

umask(0);

$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';
Mage::run($store, $mageRunType, array('cache_dir' => 'var/cache/' . str_replace('.', '_', $_SERVER['SERVER_NAME']) ));
          // 'app_dir' => string '/var/www/magento/app' (length=36)
          // 'base_dir' => string '/var/www/magento' (length=32)
          // 'code_dir' => string '/var/www/magento/app/code' (length=41)
          // 'design_dir' => string '/var/www/magento/app/design' (length=43)
          // 'etc_dir' => string '/var/www/magento/app/etc' (length=40)
          // 'lib_dir' => string '/var/www/magento/lib' (length=36)
          // 'locale_dir' => string '/var/www/magento/app/locale' (length=43)
          // 'media_dir' => string '/var/www/magento/media' (length=38)
          // 'skin_dir' => string '/var/www/magento/skin' (length=37)
          // 'var_dir' => string '/var/www/magento/var' (length=36)
          // 'tmp_dir' => string '/var/www/magento/var/tmp' (length=40)
          // 'cache_dir' => string '/var/www/magento/var/cache' (length=42)
          // 'log_dir' => string '/var/www/magento/var/log' (length=40)
          // 'session_dir' => string '/var/www/magento/var/session' (length=44)
          // 'upload_dir' => string '/var/www/magento/media/upload' (length=45)
          // 'export_dir' => string '/var/www/magento/var/export' (length=43)
