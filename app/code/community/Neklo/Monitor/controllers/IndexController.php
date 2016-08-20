<?php

class Neklo_Monitor_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        try {

            /* @var $minfo Neklo_Monitor_Model_Minfo */
            $minfo  = Mage::getModel('neklo_monitor/minfo');

            var_dump($minfo->scan()->getInfo());

//            Neklo_Monitor_Autoload::register();
//            $linfo = Mage::getModel('neklo_monitor/linfo');
//            $linfo->scan();
//            $this->getResponse()->setHeader('Content-type', 'text/json');
//            echo Mage::helper('core')->jsonEncode($linfo->getInfo());

//            echo Mage::helper('neklo_monitor/config')->getGatewaySid();


            //Mage::getModel('neklo_monitor/cron')->run();

        } catch (Exception $e) {
            echo $e->getMessage();
            echo '<br>';
            echo $e->getTraceAsString();
        }
    }

    public function testAction()
    {
        /* @var $logInfo Neklo_Monitor_Model_Log */
        $logInfo = Mage::getModel('neklo_monitor/log');
        $logInfo->getLogInfo();
        //phpinfo();
    }
}
