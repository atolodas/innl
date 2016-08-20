<?php
$store = '';
if (strpos($_SERVER['REQUEST_URI'], "/backend/") !== false && strpos($_SERVER['REQUEST_URI'], "/formbuilder/admin") !== false) {
	$store = 'admin';
}
$allStores = Mage::app()->getStores();
foreach ($allStores as $_eachStoreId => $val) {
        $storeCodes[Mage::app()->getStore($_eachStoreId)->getId()] = Mage::app()->getStore($_eachStoreId)->getCode();
}

if(!$store) {

$standardStore = false;
$segments = explode('/', $_SERVER['REQUEST_URI']);

foreach ($storeCodes as $id => $s) {
	if ($id == 4) {
		continue;
	}
//echo  $_SERVER['HTTP_HOST'] .' '.Mage::getStoreConfig('web/unsecure/base_url', $id);
	if (substr_count(Mage::getStoreConfig('web/unsecure/base_url', $id), 'http://'.$_SERVER['HTTP_HOST']) ||
		substr_count(Mage::getStoreConfig('web/secure/base_url', $id), 'https://'.$_SERVER['HTTP_HOST'] )) {
		list($store, $slang) = explode('_', $s);

		$standardStore = true;

	}
}
}

if( $_SERVER['HTTP_HOST'] == 'innl.co' ||  $_SERVER['HTTP_HOST'] == 'www.innl.co') $store = 'base';

if (strpos($_SERVER['REQUEST_URI'], "/ru/") !== false) {
	$lang = 'ru';
} else {
	$lang = 'en'; # which is the default language of the default store of your installation (/)
}

$storeCode = $store;
$store .= '_' . $lang;
if (!in_array($store, $storeCodes)) {
	$oldLang = $lang;
	if ($lang == 'en'): $lang = 'ru';
	else:$lang = 'en';
	endif;

	$store = str_replace('_' . $oldLang, '_' . $lang, $store);
}

if (!in_array($store, $storeCodes)) {
	$store = $storeCode;
}
if (!Mage::registry('scode')) {
	Mage::register('scode', str_replace('_' . $lang, '', $store));
}

if (!Mage::registry('slang')) {
	Mage::register('slang', $lang);
}
