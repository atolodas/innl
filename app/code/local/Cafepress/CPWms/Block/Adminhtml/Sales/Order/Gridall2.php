<?php

class Cafepress_CPWms_Block_Adminhtml_Sales_Order_Gridall2 extends Mage_Adminhtml_Block_Sales_Order_Grid
{

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_collection';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type'      => 'datetime',
            'align'     => 'right',
            'format'    => 'M/d/yy h:m',
            'width'     => '90px',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));
        
         $this->addColumn('cost', array(
            'header' => Mage::helper('sales')->__('cost'),
            'index' => 'subtotal',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
              'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_Cost()
        ));

        $this->addColumn('subtotal', array(
            'header' => Mage::helper('sales')->__('subtotal'),
            'index' => 'subtotal',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('discount_code', array(
            'header' => Mage::helper('sales')->__('Discount Code'),
            'index' => 'discount_code',
            'type'  => 'currency',
            'currency' => 'coupon_code',
        ));

        $this->addColumn('discount_amount', array(
            'header' => Mage::helper('sales')->__('Discount Amount'),
            'index' => 'discount_amount',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_DiscountAmount()
        ));

        $this->addColumn('tax_amount', array(
            'header' => Mage::helper('sales')->__('tax amount'),
            'index' => 'tax_amount',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('shipping_amount', array(
            'header' => Mage::helper('sales')->__('shipping amount'),
            'index' => 'shipping_amount',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('total_refunded', array(
            'header' => Mage::helper('sales')->__('Total Refunded'),
            'index' => 'total_refunded',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_TotalRefunded()
        ));

        $this->addColumn('customer_name', array(
            'header' => Mage::helper('sales')->__('Customer Name'),
            'index' => 'customer_name',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_CustomerName()
        ));

        $this->addColumn('customer_group', array(
            'header' => Mage::helper('sales')->__('Customer Group'),
            'index' => 'customer_group',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_CustomerGroup()
        ));

        $this->addColumn('payment_method', array(
            'header' => Mage::helper('sales')->__('Payment Method'),
            'index' => 'payment_method',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_PaymentMethod()
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Shipping Name'),
            'index' => 'shipping_name',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_ShippingName()
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Billing Name'),
            'index' => 'billing_name',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_BillingName()
        ));

        $this->addColumn('shipping_1', array(
            'header' => Mage::helper('sales')->__('Shipping Address'),
            'index' => 'shipping_address',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_Shipping()
        ));

        $this->addColumn('company', array(
            'header' => Mage::helper('sales')->__('company'),
            'index' => 'company',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_Shippingdata()
        ));


        $this->addColumn('street', array(
            'header' => Mage::helper('sales')->__('street'),
            'index' => 'street',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_Shippingdata()
        ));

        $this->addColumn('city', array(
            'header' => Mage::helper('sales')->__('city'),
            'index' => 'city',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_Shippingdata()
        ));

        $this->addColumn('region', array(
            'header' => Mage::helper('sales')->__('region'),
            'index' => 'region',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_Shippingdata()
        ));


        $this->addColumn('postcode', array(
            'header' => Mage::helper('sales')->__('postcode'),
            'index' => 'postcode',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_Shippingdata()
        ));

        $this->addColumn('country', array(
            'header' => Mage::helper('sales')->__('country'),
            'index' => 'country_id',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_Shippingdata()
        ));


        $this->addColumn('telephone', array(
            'header' => Mage::helper('sales')->__('telephone'),
            'index' => 'telephone',
            'renderer' => new Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_Shippingdata()
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        $this->addExportType('*/*/exportOrdersPrototypeReport', Mage::helper('sales')->__('Orders Prototype Report'));

        return $this;
    }
}
