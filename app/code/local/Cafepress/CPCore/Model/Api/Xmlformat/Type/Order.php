<?php
/**
 * Cafepress extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Cafepress CPCore module to newer versions in the future.
 * If you wish to customize the Cafepress CPCore module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Cafepress
 * @package    Cafepress_CPCore
 * @copyright  Copyright (C) 2012 Cafepress
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order xml format type
 *
 * @category   Cafepress
 * @package    Cafepress_CPCore
 * @subpackage Model
 * @author     Vladimir Fishchenko <vladimir.fishchenko@gmail.com>
 */
class Cafepress_CPCore_Model_Api_Xmlformat_Type_Order extends Cafepress_CPCore_Model_Api_Xmlformat_Type_Abstract
{
    /**
     * process request
     *
     * @param Cafepress_CPCore_Model_Api_Xmlformat $object
     * @return string|bool
     */
    protected function _processRequest(Cafepress_CPCore_Model_Api_Xmlformat $object)
    {
        $orderId = $object->getAdditional('order_id');
        if (!$orderId) {
            return 'please specify \'order_id\' in additional data';
        }

        $order = Mage::getModel('sales/order')->load($orderId);
        if (!$order->getId()) {
            return 'can\'t found order with id=\'' . $orderId . '\'';
        }

        $format = $object->getFormat();
        $storeId = $order->getStoreId();
        /** @var $template Cafepress_CPCore_Model_Template */
        $template = Mage::getModel('cpcore/template')->setStoreId($storeId);

        $template->setXmlformat(new Varien_Object(array('out_xml' => $format->request_products_part)));
        $products = $this->_processProducts($template, $order);

        $template->setXmlformat(new Varien_Object(array('out_xml' => $format->request_body)));
        $data = $this->_processOrder($template, $order, $products);

        $this->_processedData['request_body'] = $data;

        return true;
    }

    private function _processProducts(
        Mage_Core_Model_Template $template, Mage_Sales_Model_Order $order
    ) {
        $processedTemplate = '';
        $counter = 1;

        $params = $this->_prepareOrderValues($order);

        foreach ($order->getAllItems() as $item) {
            //only simple products
            if ($item->getChildrenItems()) {
                continue;
            }
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if ($product->getTypeId()=='simple'){
                $product->setIsSimple(true);
            } else {
                $product->setIsSimple(false);
            }
            $parent_price = '';

            $parent_product = null;
            if ($item->getParentItem()) {
                $parent_price = $item->getParentItem()->getPrice();
                $parent_product = Mage::getModel('catalog/product')->load($item->getParentItem()->getProductId());
            }

            if ($parent_price != '') {
                $item->setPrice($parent_price);
                $item->setRowTotal($parent_price * $item->getQtyOrdered());
            }

            $item->setCounter($counter);
            $item = $this->_setGiftMessageItem($item);


            if ($product->getContinuityIs() || (!$product->getContinuityIs() && $item->getIsContinuity())) {
                $item->setHasContinuity(true);

                $item->setHasNoContinuity(false);
                $order->getPayment()->setPaymentTwo($item->getPrice());
            } else {
                $item->setHasContinuity(false);

                $item->setHasNoContinuity(true);
            }

            if ($product->getContinuityIs() || (!$product->getContinuityIs() && $item->getIsContinuity())){
                $continuity_price = $product->getContinuityPrice();
                $productDiscount = $product->getContinuityDiscount();
                if($productDiscount) {
                    $discountType = strtolower($product->getAttributeText('continuity_discount_type'));
                    if ($discountType=='fix'){
                        $continuity_price = $continuity_price - $productDiscount;
                    } else {
                        $continuity_price = $continuity_price - ($continuity_price/100)*$productDiscount;
                    }
                }
                $product->setContinuityPrice($continuity_price);

            }

            $vars = array_merge($params, array(
                'parent'    => $parent_product,
                'counter'   => $counter++,
                'item'      => $item,
                'product'   => $product,
            ));

            $processedTemplate .= $template->getProcessedTemplate($vars, true);
        }
        return $processedTemplate;
    }

    private function _processOrder(
        Mage_Core_Model_Template $template, Mage_Sales_Model_Order $order, $products
    ) {
        $params = $this->_prepareOrderValues($order);
        $params['products'] = $products;
        return $template->getProcessedTemplate($params, true);
    }

    private function _prepareOrderValues(Mage_Sales_Model_Order $order)
    {
        $payment = $order->getPayment();
        $store = Mage::app()->getStore($order->getStoreId());
        $add = $payment->getAdditionalInformation();
        if (is_array($add) && isset($add['authorize_cards']) && is_array($add['authorize_cards'])) {
            $add = array_pop(@$add['authorize_cards']);
        }
        if (substr_count($order->getStore()->getName(), 'POS') > 0) {
            $partnerId = 'C00064';
        } else {
            $partnerId = Mage::getStoreConfig('common/partner/id');
        }
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $ps = '';
        if (is_object($customer->getResource()->getAttribute('packing_slip'))) {
            $ps = $customer->getResource()
                ->getAttribute('packing_slip')->getSource()
                ->getOptionText($customer->getData('packing_slip')); //getAttributeText('packing_slip');
        }
        $sp = '';
        if (is_object($customer->getResource()->getAttribute('sales_person'))) {
            $sp = $customer->getResource()
                ->getAttribute('sales_person')->getSource()
                ->getOptionText($customer->getData('sales_person')); //getAttributeText('packing_slip');
        }
        $retailer_terms = '';
        if (is_object($customer->getResource()->getAttribute('retailer_terms'))) {
            $retailer_terms = $customer->getResource()
                ->getAttribute('retailer_terms')->getSource()
                ->getOptionText($customer->getData('retailer_terms')); //getAttributeText('packing_slip');
        }

        $discountPercent = 0;
        if ($order->getDiscountPercent()){
            $discountPercent = $order->getDiscountPercent();
        }

        $itemsPrice = 0;
        $continyityPrice = 0;
        $itemsPriceDiscount = 0;
        foreach ($order->getAllItems() as $item) {
            $product = Mage::getModel('cpcore/catalog_product')->load($item->getProductId());
            $price = $item->getPrice();
            if ($product->getContinuityIs()) { // || (!$product->getContinuityIs() && $item->getIsContinuity())
                $continyityPrice += $product->getContinuityPayment2()*$item->getQtyOrdered();
                $price = $product->getContinuityPrice();
            }
            $itemPrice = $item->getPrice()*$item->getQtyOrdered();
            if ($order->getDiscountPercent()){
                $itemPriceDiscount = $price*$item->getQtyOrdered()*(1-$order->getDiscountPercent());
            } else {
                $itemPriceDiscount = $price*$item->getQtyOrdered()-$item->getDiscountAmount();
            }
            $itemsPrice += $itemPrice;
            $itemPriceDiscount = round($itemPriceDiscount*1.00,2);
            $itemsPriceDiscount += $itemPriceDiscount;
        }
        $hardVar['payment2']                    = $continyityPrice;
        $hardVar['items_price_discount_tax']    = $itemsPriceDiscount*$order->getTaxPercent()/100;
        if ($order->getDiscountPercent()){
            $hardVar['continyity_discount']     = $continyityPrice*(1-$order->getDiscountPercent());
        } elseif ($continyityPrice!=0) {
            $hardVar['continyity_discount']     = $continyityPrice-$item->getDiscountAmount();
        }
        else {
            $hardVar['continyity_discount']     = 0;
        }

        $hardVar['continyity_discount_tax']     = $hardVar['continyity_discount']*$order->getTaxPercent()/100;
        $message = Mage::helper("giftmessage/message")->getGiftMessage($order->getGiftMessageId());

        foreach ($hardVar as $key=>$val){
            $hardVar[$key] = round($val*1.00,2);
        }
        $hardVar['items_price_discount'] = $itemsPriceDiscount;

        if (!isset($add['cc_type'])){
            $add['cc_type'] = '';
        }
        if (!isset($add['last_trans_id'])){
            $add['last_trans_id'] = '';
        }
        if (!isset($add['cc_last4'])){
            $add['cc_last4'] = '';
        }

        return array(
            'order'                     => $order,
            'store'                     => $store,
            'partnerId'                 => $partnerId,
            'ps'                        => $ps,
            'sp'                        => $sp,
            'retailerTerms'             => $retailer_terms,
            'retailer_terms'            => $retailer_terms,
            'payment'                   => $payment,
            'payment2'                  => ($hardVar['items_price_discount'] + $order->getShippingAmount() + $hardVar['items_price_discount_tax'] - $order->getGrandTotal()),
            'discount_percent'          => $discountPercent,
            'discountPercent'           => $discountPercent,
            'add'                       => $add,
            'add_cc_type'               => $add['cc_type'],
            'add_last_trans_id'         => $add['last_trans_id'],
            'add_cc_last4'              => $add['cc_last4'],
            'message'                   => $message,
            'items_price_discount'      => $hardVar['items_price_discount'],
            'items_price_discount_tax'  => $hardVar['items_price_discount_tax'],
            'continyity_discount'       => $hardVar['continyity_discount'],
            'continyity_discount_tax'   => $hardVar['continyity_discount_tax'],
            'total'                     => $order->getGrandTotal() + $hardVar['payment2'],
        );
    }

    /**
     *
     * @param Mage_Sales_Model_Order_Item $item
     * @return Mage_Sales_Model_Order_Item
     */
    private function _setGiftMessageItem(Mage_Sales_Model_Order_Item $item) {
        if ($item->getGiftMessageId()) {
            $giftMessage = Mage::helper('giftmessage/message')->getGiftMessage($item->getGiftMessageId());
            $item->setGiftMessage($giftMessage->getMessage());
            $item->setGiftMessageFrom($giftMessage->getSender());
        }
        return $item;
    }
}
