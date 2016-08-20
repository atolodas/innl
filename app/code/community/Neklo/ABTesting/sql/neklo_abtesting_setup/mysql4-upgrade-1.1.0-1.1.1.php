<?php
/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$this->run("INSERT INTO `". $this->getTable('neklo_abtesting/abevent') ."` VALUES 
    (2, 'Custom', 'custom'),
    (3, 'Customer Login', 'customer_login'),
    (4, 'Product View', 'catalog_controller_product_view'),
    (5, 'Product Added to cart', 'checkout_cart_product_add_after'),
    (6, 'Order placed', 'checkout_type_onepage_save_order_after')
");


$this->endSetup();