<?php

class Cafepress_CPCore_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction(){
//        $partnerId = 'F@nC@m02816';
//        $orderNo = '447495914';
//        $comment = 'test';
//        $result = Mage::getModel('cpcore/cafepress_order')->cancel($partnerId, $orderNo, $comment);
//        Zend_Debug::dump($result);
    }

//    public function index2Action(){
//        $xml = '<CancelCPSalesOrderByPartner xmlns="http://Cafepress.com/">
//                    <PartnerID>F@nC@m02816</PartnerID>
//                    <SalesOrderNo>447495914</SalesOrderNo>
//                    <CancelComment>test</CancelComment>
//                </CancelCPSalesOrderByPartner>';
//        $result = Mage::getModel('cpcore/xmlformat_outbound')->sendXmlOverSoap($xml, 0, false, 'CancelCPSalesOrderByPartner');
//        Zend_Debug::dump($result);
//    }
    
    public function resendAction(){
    }

    public function generateXmlForOrderAction() {
        $id = $this->getRequest()->getParam('id');
        $order = Mage::getModel('sales/order')->load($id);
        Mage::getModel('cpcore/order_observer')->generateXmlForOrder($order);
    }

    public function getAknowledgmentAction() {
        $files = array();
        Mage::getModel('cpcore/order_observer')->getFiles($files, 'Orderck', 'acknowledgment');
        Mage::getModel('cpcore/order_observer')->parseAndDeleteAknowledgment($files);
    }

    public function getShippingAction() {
        $files = array();
        Mage::getModel('cpcore/order_observer')->getFiles($files, 'SN', 'shipment');
        Mage::getModel('cpcore/order_observer')->parseAndDeleteShipping($files);
    }

    public function updateStatusAction() {
        $status = $this->getRequest()->getParam('status');
        $order = $this->getRequest()->getParam('order');
        Mage::getModel('sales/order')->load($order)->setCpWmsFileStatus($status)->save();
    }

    public function getInventoryAdviceAction() {
        Mage::getModel('cpcore/catalog_product_import')->getInventoryAdvice();
    }

    public function getInventoryTestAction() {
        Mage::getModel('cpcore/catalog_product_import')->updateInventory();
    }

    public function importAttributeOptionsAction() {
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $this->getRequest()->getParam('attribute'));
        $opts = $attribute->getFrontend()->getSelectOptions();
        $options = array();
        foreach ($opts as $option) {
            if (Mage::helper('core/string')->strlen($option['value'])) {
                $options[$option['value']] = $option['label'];
            }
        }

        $import = $this->getRequest()->getParam('options');
        $values = explode($this->getRequest()->getParam('separator0'), $import);
        foreach ($values as $val) {
            list($value, $name) = explode($this->getRequest()->getParam('separator'), $val);
            if (!in_array($name, $options) && !in_array($value, array_keys($options))) {
                $o = Mage::getModel('eav/entity_attribute_option')->setAttributeId($attribute->getId())->setStoreId(0)->save();
                $optionId = $o->getId();
                $option = array();
                $option['attribute_id'] = $attribute->getId();
                $option['value'][$o->getId()][0] = $value;
                $option['value'][$o->getId()][1] = $name;
                $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
                $opt = $setup->addAttributeOption($option);

                $options[$value] = $value;
            }
        }
    }

    public function fixDbForeignKeyAction() {
        $installer = new Mage_Eav_Model_Entity_Setup('core_setup');
        $installer->startSetup();

        /**
         * Create New Tables cpcore_xmlformat_*
         */
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_int;");
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_datetime;");
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_decimal;");
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_varchar;");
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_text;");

        try {
            /**
             * Create table array('cpcore/xmlformat', 'int')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'int')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                            ), 'Value')
                    ->addIndex(
                            $installer->getIdxName(
                                    array('cpcore/xmlformat', 'int'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
                            ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'int'), array('entity_id')), array('entity_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'int'), array('attribute_id')), array('attribute_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'int'), array('store_id')), array('store_id'))
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'int'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'int'), 'entity_id', 'cpcore/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpcore/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'int'), 'store_id', 'core/store', 'store_id'),
                            //        'FK_cpcore_xmlformat_int_storeid',
                            'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->setComment('wms xmlformat Int Attribute Backend Table');
            $installer->getConnection()->createTable($table);


            /**
             * Create table array('cpcore/xmlformat', 'decimal')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'decimal')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
                            ), 'Value')
                    ->addIndex(
                            $installer->getIdxName(
                                    array('cpcore/xmlformat', 'decimal'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
                            ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'decimal'), array('entity_id')), array('entity_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'decimal'), array('attribute_id')), array('attribute_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'decimal'), array('store_id')), array('store_id'))
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'decimal'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'decimal'), 'entity_id', 'cpcore/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpcore/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'decimal'), 'store_id', 'core/store', 'store_id'),
                            //        'FK_cpcore_xmlformat_decimal_storeid',
                            'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->setComment('wms xmlformat Decimal Attribute Backend Table');
            $installer->getConnection()->createTable($table);

            /**
             * Create table array('cpcore/xmlformat', 'datetime')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'datetime')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
                            ), 'Value')
                    ->addIndex(
                            $installer->getIdxName(
                                    array('cpcore/xmlformat', 'datetime'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
                            ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'datetime'), array('entity_id')), array('entity_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'datetime'), array('attribute_id')), array('attribute_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'datetime'), array('store_id')), array('store_id'))
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'datetime'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'datetime'), 'entity_id', 'cpcore/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpcore/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'datetime'), 'store_id', 'core/store', 'store_id'),
                            //        'FK_cpcore_xmlformat_datetime_storeid',
                            'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->setComment('wms xmlformat Datetime Attribute Backend Table');
            $installer->getConnection()->createTable($table);

            /**
             * Create table array('cpcore/xmlformat', 'varchar')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'varchar')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                            ), 'Value')
                    ->addIndex(
                            $installer->getIdxName(
                                    array('cpcore/xmlformat', 'varchar'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
                            ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'varchar'), array('entity_id')), array('entity_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'varchar'), array('attribute_id')), array('attribute_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'varchar'), array('store_id')), array('store_id'))
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'varchar'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'varchar'), 'entity_id', 'cpcore/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpcore/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'varchar'), 'store_id', 'core/store', 'store_id'),
                            //        'FK_cpcore_xmlformat_varchar_storeid',
                            'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->setComment('wms xmlformat Varchar Attribute Backend Table');
            $installer->getConnection()->createTable($table);

            /**
             * Create table array('cpcore/xmlformat', 'text')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'text')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
                            ), 'Value')
                    ->addIndex(
                            $installer->getIdxName(
                                    array('cpcore/xmlformat', 'varchar'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
                            ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'text'), array('entity_id')), array('entity_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'text'), array('attribute_id')), array('attribute_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'text'), array('store_id')), array('store_id'))
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'text'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'text'), 'entity_id', 'cpcore/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpcore/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'text'), 'store_id', 'core/store', 'store_id'),
                            //        'FK_cpcore_xmlformat_text_storeid',
                            'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->setComment('wms xmlformat Text Attribute Backend Table');
            $installer->getConnection()->createTable($table);
        } catch (Exception $e) {
            echo $e;
        }

        $installer->run("INSERT INTO `cpcore_xmlformat_int` SELECT * FROM `cpcore_xmlformat_int_backup`;");
        $installer->run("INSERT INTO `cpcore_xmlformat_datetime` SELECT * FROM `cpcore_xmlformat_datetime_backup`;");
        $installer->run("INSERT INTO `cpcore_xmlformat_decimal` SELECT * FROM `cpcore_xmlformat_decimal_backup`;");
        $installer->run("INSERT INTO `cpcore_xmlformat_varchar` SELECT * FROM `cpcore_xmlformat_varchar_backup`;");
        $installer->run("INSERT INTO `cpcore_xmlformat_text` SELECT * FROM `cpcore_xmlformat_text_backup`;");

        $installer->endSetup();
    }

    public function getOrdersIdAction(){
        $result = array('error'=>true);
        if ($this->getRequest()->isPost()) {
            $precondition = $this->getRequest()->getPost('precondition');
            $formatId = (int)$this->getRequest()->getParam('id');
            if ($formatId){
                $orders = Mage::getSingleton('cpcore/xmlformat_format_order')->getOrderCollection($formatId,$precondition);

                $html = '<div id="orders_grid_content">Order Quantity: ';
                $html .= count($orders);
                $html .= '<select multiple="multiple" size="10">';
                foreach ($orders as $order) {
                    $html .= '<option>'.$order->getId().'</option>';
                }
                $html .= '</select></div>';

                $result['update_orders_grid_section_html'] = $html;
                $result['error'] = false;
            }
        }
        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode($result)
        );
        return;
    }

    public function redumpDbAction() {
        $installer = new Mage_Eav_Model_Entity_Setup('core_setup');
        $installer->startSetup();

        /**
         * Rebuild cpcore_xmlformat_* tables
         */
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_int_backup;");
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_datetime_backup;");
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_decimal_backup;");
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_varchar_backup;");
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_text_backup;");

        try {
            /**
             * Create Backup table array('cpcore/xmlformat', 'int')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'int_backup')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                            ), 'Value')
                    ->setComment('Backup wms xmlformat Int Attribute Backend Table');
            $installer->getConnection()->createTable($table);


            /**
             * Create Backup table array('cpcore/xmlformat', 'decimal')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'decimal_backup')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
                            ), 'Value')
                    ->setComment('Backup wms xmlformat Decimal Attribute Backend Table');
            $installer->getConnection()->createTable($table);

            /**
             * Create Backup table array('cpcore/xmlformat', 'datetime')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'datetime_backup')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
                            ), 'Value')
                    ->setComment('Backup wms xmlformat Datetime Attribute Backend Table');
            $installer->getConnection()->createTable($table);
            /**
             * Create Backup table array('cpcore/xmlformat', 'varchar')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'varchar_backup')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                            ), 'Value')
                    ->setComment('Backup wms xmlformat Varchar Attribute Backend Table');
            $installer->getConnection()->createTable($table);
            /**
             * Create Backup table array('cpcore/xmlformat', 'text')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'text_backup')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
                            ), 'Value')
                    ->setComment('Backup wms xmlformat Text Attribute Backend Table');
            $installer->getConnection()->createTable($table);
        } catch (Exception $e) {
            echo $e;
        }

        $installer->run("INSERT INTO cpcore_xmlformat_int_backup SELECT * FROM cpcore_xmlformat_int;");
        $installer->run("INSERT INTO cpcore_xmlformat_datetime_backup SELECT * FROM cpcore_xmlformat_datetime;");
        $installer->run("INSERT INTO cpcore_xmlformat_decimal_backup SELECT * FROM cpcore_xmlformat_decimal;");
        $installer->run("INSERT INTO cpcore_xmlformat_varchar_backup SELECT * FROM cpcore_xmlformat_varchar;");
        $installer->run("INSERT INTO cpcore_xmlformat_text_backup SELECT * FROM cpcore_xmlformat_text;");

        /**
         * Create New Tables cpcore_xmlformat_*
         */
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_int;");
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_datetime;");
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_decimal;");
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_varchar;");
        $installer->run("DROP TABLE IF EXISTS cpcore_xmlformat_text;");

        try {
            /**
             * Create table array('cpcore/xmlformat', 'int')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'int')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                            ), 'Value')
                    ->addIndex(
                            $installer->getIdxName(
                                    array('cpcore/xmlformat', 'int'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
                            ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'int'), array('entity_id')), array('entity_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'int'), array('attribute_id')), array('attribute_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'int'), array('store_id')), array('store_id'))
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'int'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'int'), 'entity_id', 'cpcore/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpcore/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'int'), 'store_id', 'core/store', 'store_id'),
                            //        'FK_cpcore_xmlformat_int_storeid',
                            'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->setComment('wms xmlformat Int Attribute Backend Table');
            $installer->getConnection()->createTable($table);


            /**
             * Create table array('cpcore/xmlformat', 'decimal')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'decimal')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
                            ), 'Value')
                    ->addIndex(
                            $installer->getIdxName(
                                    array('cpcore/xmlformat', 'decimal'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
                            ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'decimal'), array('entity_id')), array('entity_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'decimal'), array('attribute_id')), array('attribute_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'decimal'), array('store_id')), array('store_id'))
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'decimal'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'decimal'), 'entity_id', 'cpcore/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpcore/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'decimal'), 'store_id', 'core/store', 'store_id'),
                            //        'FK_cpcore_xmlformat_decimal_storeid',
                            'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->setComment('wms xmlformat Decimal Attribute Backend Table');
            $installer->getConnection()->createTable($table);

            /**
             * Create table array('cpcore/xmlformat', 'datetime')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'datetime')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
                            ), 'Value')
                    ->addIndex(
                            $installer->getIdxName(
                                    array('cpcore/xmlformat', 'datetime'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
                            ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'datetime'), array('entity_id')), array('entity_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'datetime'), array('attribute_id')), array('attribute_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'datetime'), array('store_id')), array('store_id'))
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'datetime'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'datetime'), 'entity_id', 'cpcore/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpcore/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'datetime'), 'store_id', 'core/store', 'store_id'),
                            //        'FK_cpcore_xmlformat_datetime_storeid',
                            'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->setComment('wms xmlformat Datetime Attribute Backend Table');
            $installer->getConnection()->createTable($table);

            /**
             * Create table array('cpcore/xmlformat', 'varchar')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'varchar')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                            ), 'Value')
                    ->addIndex(
                            $installer->getIdxName(
                                    array('cpcore/xmlformat', 'varchar'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
                            ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'varchar'), array('entity_id')), array('entity_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'varchar'), array('attribute_id')), array('attribute_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'varchar'), array('store_id')), array('store_id'))
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'varchar'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'varchar'), 'entity_id', 'cpcore/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpcore/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'varchar'), 'store_id', 'core/store', 'store_id'),
                            //        'FK_cpcore_xmlformat_varchar_storeid',
                            'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->setComment('wms xmlformat Varchar Attribute Backend Table');
            $installer->getConnection()->createTable($table);

            /**
             * Create table array('cpcore/xmlformat', 'text')
             */
            $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpcore/xmlformat', 'text')))
                    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                            ), 'Value ID')
                    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity Type ID')
                    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Attribute ID')
                    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Store ID')
                    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                        'unsigned' => true,
                        'nullable' => false,
                        'default' => '0',
                            ), 'Entity ID')
                    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
                            ), 'Value')
                    ->addIndex(
                            $installer->getIdxName(
                                    array('cpcore/xmlformat', 'varchar'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
                            ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'text'), array('entity_id')), array('entity_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'text'), array('attribute_id')), array('attribute_id'))
                    ->addIndex($installer->getIdxName(array('cpcore/xmlformat', 'text'), array('store_id')), array('store_id'))
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'text'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'text'), 'entity_id', 'cpcore/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpcore/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->addForeignKey(
                            $installer->getFkName(array('cpcore/xmlformat', 'text'), 'store_id', 'core/store', 'store_id'),
                            //        'FK_cpcore_xmlformat_text_storeid',
                            'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                    ->setComment('wms xmlformat Text Attribute Backend Table');
            $installer->getConnection()->createTable($table);
        } catch (Exception $e) {
            echo $e;
        }

        $installer->run("INSERT INTO `cpcore_xmlformat_int` SELECT * FROM `cpcore_xmlformat_int_backup`;");
        $installer->run("INSERT INTO `cpcore_xmlformat_datetime` SELECT * FROM `cpcore_xmlformat_datetime_backup`;");
        $installer->run("INSERT INTO `cpcore_xmlformat_decimal` SELECT * FROM `cpcore_xmlformat_decimal_backup`;");
        $installer->run("INSERT INTO `cpcore_xmlformat_varchar` SELECT * FROM `cpcore_xmlformat_varchar_backup`;");
        $installer->run("INSERT INTO `cpcore_xmlformat_text` SELECT * FROM `cpcore_xmlformat_text_backup`;");

        $installer->endSetup();
    }

}
