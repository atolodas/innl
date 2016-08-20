<?php

class Cafepress_CPWms_Model_Dump extends Mage_Core_Model_Abstract
{
    public function sendViaMail($observer){
        $status = Mage::getStoreConfig('dumpmail/main/status');
        if($status == '1'){
            $installer = new Mage_Eav_Model_Entity_Setup('core_setup');
            $installer->startSetup();

            $installer->run("DROP TABLE IF EXISTS wms_xmlformat_int_backup;");
            $installer->run("DROP TABLE IF EXISTS wms_xmlformat_datetime_backup;");
            $installer->run("DROP TABLE IF EXISTS wms_xmlformat_decimal_backup;");
            $installer->run("DROP TABLE IF EXISTS wms_xmlformat_varchar_backup;");
            $installer->run("DROP TABLE IF EXISTS wms_xmlformat_text_backup;");

            try {
                $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpwms/xmlformat', 'int_backup')))
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

                $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpwms/xmlformat', 'decimal_backup')))
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

                $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpwms/xmlformat', 'datetime_backup')))
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

                $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpwms/xmlformat', 'varchar_backup')))
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

                $table = $installer->getConnection()
                    ->newTable($installer->getTable(array('cpwms/xmlformat', 'text_backup')))
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

            $installer->run("INSERT INTO wms_xmlformat_int_backup SELECT * FROM wms_xmlformat_int;");
            $installer->run("INSERT INTO wms_xmlformat_datetime_backup SELECT * FROM wms_xmlformat_datetime;");
            $installer->run("INSERT INTO wms_xmlformat_decimal_backup SELECT * FROM wms_xmlformat_decimal;");
            $installer->run("INSERT INTO wms_xmlformat_varchar_backup SELECT * FROM wms_xmlformat_varchar;");
            $installer->run("INSERT INTO wms_xmlformat_text_backup SELECT * FROM wms_xmlformat_text;");

            $wms_tables = array(
                'wms_xmlformat',
                'wms_xmlformat_datetime',
                'wms_xmlformat_datetime_backup',
                'wms_xmlformat_decimal',
                'wms_xmlformat_decimal_backup',
                'wms_xmlformat_int',
                'wms_xmlformat_int_backup',
                'wms_xmlformat_text',
                'wms_xmlformat_text_backup',
                'wms_xmlformat_varchar',
                'wms_xmlformat_varchar_backup'
            );
            if(!file_exists(Mage::getBaseDir('var').'/')){
                mkdir(Mage::getBaseDir('var').'/');
            }
            if(!file_exists(Mage::getBaseDir('var').'/backups/')){
                mkdir(Mage::getBaseDir('var').'/backups/');
            }
            $content = Mage::helper('cpwms')->dumpDb($wms_tables, Mage::getBaseDir('var').'/backups/wms_wmlformat_backups.sql');

            Mage::helper('cpwms')->sendMail(
                'dump@gmail.com',
                'ivanovp.daemon@gmail.com',
                'XML Format Dump',
                '',
                Mage::getBaseDir('var').'/backups/wms_wmlformat_backups.sql',
                $content
            );

            $installer->endSetup();
        }
    }
}
