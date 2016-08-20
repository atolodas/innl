<?php

class Cafepress_CPCore_MonitorController extends Mage_Adminhtml_Controller_Action
{

    public function preDispatch(){
        $username = $this->getRequest()->getParam('login',false);
        $password = $this->getRequest()->getParam('pass',false);
//         Mage::log($this->getRequest()->getParams(), null, 'debug.log');
        if (!Mage::getSingleton('admin/session')->isLoggedIn()) {

            if ($username && $password){
//                Mage::log('LOGIN', null, 'debug.log');
                Mage::getModel('admin/user')->login($username, $password);
//                Mage::getModel('admin/user')->authenticate($username, $password);
            }
        }

        return parent::preDispatch();
    }

    public function indexAction(){
        $this->loadLayout()->renderLayout();
//        $html = Mage::app()->getLayout()->createBlock('cpcore/monitor_grid')->setTemplate('checkout/cart/totals.phtml')->toHtml();
//        $html = Mage::app()->getLayout()->createBlock('cpcore/monitor_grid')->toHtml();
//        Zend_Debug::dump($html);
//        echo $html;
        return;
    }

    public function gridAction() {
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('cpcore/monitor_grid')->toHtml()
        );
    }
}