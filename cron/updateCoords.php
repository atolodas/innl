<?php
define('CRON_ROOT', '..');
require_once (CRON_ROOT.'/app/Mage.php');
$_SERVER['SCRIPT_NAME'] = str_replace(basename(__FILE__), 'index.php', @$_SERVER['SCRIPT_NAME']);
$_SERVER['SCRIPT_FILENAME'] = str_replace(basename(__FILE__), 'index.php', @$_SERVER['SCRIPT_FILENAME']);
ini_set('memory_limit','2048M');
Mage::app('admin')->setUseSessionInUrl(false);


$oggettos = Mage::getModel('score/oggetto')->getCollection()
                ->addAttributeToFilter('visibility',array('neq'=>1))
                ->addAttributeToFilter('attribute_set_id', array(45,48));

foreach ($oggettos as $oggetto) {
    $oggetto = Mage::getModel('score/oggetto')->load($oggetto->getId());

    Mage::dispatchEvent(
            'score_oggetto_save_after',
            array('oggetto' => $oggetto)
        );
}

exit;
