<?php
class Oggetto_Cacherecords_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function resaveBrandsForOrdersAction()
    {
        try {
            $attribute = 'brand';
            $obj = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute);
            $orders = Mage::getModel('sales/order')->getCollection();
            foreach ($orders as $order) {
                $html = '';
                $order = Mage::getModel('sales/order')->load($order->getId());
                $allready = array();
                foreach ($order->getAllItems() as $item) {
                    $model = Mage::getModel('catalog/product');
                    $id = $model->getIdBySku($item->getSku());
                    $product = $model->load($id);

                    if ($obj->usesSource()) {
                        $value = $product->getAttributeText($attribute);
                    } else {
                        $value = $product->getData($attribute);
                    }
                    if (!in_array($value, $allready)) {
                        if (is_array($value)) {
                            foreach ($value as $val) {
                                $html .= $val . ', ';
                            }
                        } else {
                            $html .= $value . ', ';
                        }
                        $allready[] = $value;
                    }
                }
                $html = substr($html, 0, strlen($html) - 2);
                echo "UPDATE sales_flat_order SET brands='{$html}' WHERE entity_id = {$order->getId()}; <br/>";
                echo "UPDATE sales_flat_order_grid SET brands='{$html}' WHERE entity_id = {$order->getId()}; <br/>";

            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function rebuildSizesCacheAction()
    {
        Mage::app()->getCache()->save(serialize(array()), 'processed');
        $this->loadLayout();
        $this->renderLayout();
    }


    public function rebuildSizesCachePartAction()
    {
        $initCollection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', array(2, 3, 4))

            ->addAttributeToSelect('small_image')
            ->addAttributeToSelect('special_price')
            ->addAttributeToSelect('brand');

        $initCollection->getSelect()->order('entity_id desc');

        $page = $this->getRequest()->getParam('counter');
        $count = $initCollection->getSize();
        $initCollection->setPageSize(10)
            ->setCurPage($page);

        foreach ($initCollection as $prod) {
            $_product = Mage::getModel('catalog/product')->load($prod->getId());
            echo $_product->getSku() . " processed";
            echo" <br/>";
            if ($_product->isGrouped()) {
                $prices = array();
                $associated = $_product->getTypeInstance(true)->getAssociatedProductCollection($_product)->addAttributeToSelect('special_price');
                foreach ($associated as $assoc) {
                    $prices[] = $assoc->getSpecialPrice();
                }
                $min_price = min($prices);
                $max_price = max($prices);
                $min_price = Mage::helper('brands')->applyBrandPricing($_product->getBrand(), $min_price, $_product, 'min');
                $max_price = Mage::helper('brands')->applyBrandPricing($_product->getBrand(), $max_price, $_product, 'max');

            } else {
                $_price = Mage::helper('brands')->applyBrandPricing($_product->getBrand(), $_product->getSpecialPrice(), $_product, 'min');
            }
            Mage::helper('imagecache')->getProductSmallImage(array($_product));
        }
        echo '<br/>' . ($count - $page * 10) . ' rugs left to build new caches ';
    }
}