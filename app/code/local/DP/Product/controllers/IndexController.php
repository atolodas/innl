<?php
require_once 'app/code/core/Mage/Catalog/controllers/ProductController.php';
class DP_Product_IndexController extends Mage_Catalog_ProductController
{
    public function indexAction()
    {
        $r = $this->getRequest()->getRequestString();
        if (preg_match_all('/^\/st\/(.*)/i', $r, $matches)){
            $product_url_path = $matches[1][0];
            $url_rewrite = Mage::getModel('core/url_rewrite');
            $url_rewrite->loadByRequestPath($product_url_path);
            if(!$url_rewrite->getProductId()) { $this->_forward('noroute'); }
           // Mage::app()->getRequest()->setRouteName('catalog');
           // Mage::app()->getRequest()->setControllerName('product');
           // Mage::app()->getRequest()->setActionName('view');
            Mage::app()->getRequest()->setParam('id', $url_rewrite->getProductId());

            Mage::app()->getRequest()->setParam('category', 10);
           // Mage::register('current_product',Mage::getModel('catalog/product')->load($url_rewrite->getProductId()));
         //   Mage::app()->getRequest()->
          //      setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $url_rewrite->getRequestPath());

            if ($product = $this->_initProduct()) {
                Mage::dispatchEvent('catalog_controller_product_view', array('product'=>$product));

                if ($this->getRequest()->getParam('options')) {
                    $notice = $product->getTypeInstance(true)->getSpecifyOptionMessage();
                    Mage::getSingleton('catalog/session')->addNotice($notice);
                }

                Mage::getSingleton('catalog/session')->setLastViewedProductId($product->getId());
                Mage::getModel('catalog/design')->applyDesign($product, Mage_Catalog_Model_Design::APPLY_FOR_PRODUCT);

                $this->loadLayout(
                    array(
                        'default',
                        'catalog_product_view'
                    )
                );

                $update = $this->getLayout()->getUpdate();
                $this->addActionLayoutHandles();
                $update->addHandle('custom_product_layout');
                $this->loadLayoutUpdates();
                $this->generateLayoutXml()->generateLayoutBlocks();


                $this->_initLayoutMessages('catalog/session');
                $this->_initLayoutMessages('tag/session');
                $this->_initLayoutMessages('checkout/session');
                $this->renderLayout();

            }
            else {
                if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            }

        } else {
            $this->_forward('noroute');
        }
    }


}