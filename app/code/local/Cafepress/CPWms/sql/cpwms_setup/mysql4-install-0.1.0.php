<?php

$installer = $this;

$installer->startSetup();

$wms_entity = 'wms_xmlformat';

/***********************************TABLES CREATING****************************************/

$eav_installer = new Mage_Eav_Model_Entity_Setup('core_setup');

$eavConfig = Mage::getSingleton('eav/config');
$store   = Mage::app()->getStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('cpwms/xmlformat')};
    DROP TABLE IF EXISTS {$this->getTable('cpwms/xmlformat')}_int;
    DROP TABLE IF EXISTS {$this->getTable('cpwms/xmlformat')}_datetime;
    DROP TABLE IF EXISTS {$this->getTable('cpwms/xmlformat')}_decimal;
    DROP TABLE IF EXISTS {$this->getTable('cpwms/xmlformat')}_varchar;
    DROP TABLE IF EXISTS {$this->getTable('cpwms/xmlformat')}_text;
");

$installer->addEntityType($wms_entity, Array(
    'entity_model'          => 'cpwms/xmlformat',
    'attribute_model'       => 'cpwms/attribute',
    'table'			        => $this->getTable('cpwms/xmlformat'),
    'increment_model'       => '',
    'increment_per_store'   => '0'
));

$installer->createEntityTables($wms_entity,array('no-default-types'=>true, 'types'=>array('int'=>array(Varien_Db_Ddl_Table::TYPE_INTEGER,null),'varchar'=>array(Varien_Db_Ddl_Table::TYPE_VARCHAR,255),'decimal'=>array(Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4'),'datetime'=>array('datetime', null))));

$installer->run("
    DROP TABLE IF EXISTS {$installer->getTable('cpwms/attribute')};
    CREATE TABLE `{$installer->getTable('cpwms/attribute')}` (
        `attribute_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
        `is_global` tinyint(1) unsigned NOT NULL DEFAULT '0',
        `is_visible` tinyint(1) unsigned NOT NULL DEFAULT '1',
        `position` int(11) NOT NULL,
        PRIMARY KEY (`attribute_id`),
        CONSTRAINT `FK_WMS_EAV_ATTRIBUTE_ID` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->installEntities();
$table = $this->getTable('cpwms/xmlformat');
$installer->getConnection()->dropColumn($table, 'parent_id');
$installer->getConnection()->dropColumn($table, 'store_id');
$installer->getConnection()->dropColumn($table, 'is_active');

$fields = array('attribute_id');
$stmt = $installer->getConnection()->select()
    ->from($installer->getTable('eav/attribute'), $fields)
    ->Where('entity_type_id = ?', $installer->getEntityTypeId($wms_entity));
$result = $installer->getConnection()->fetchAll($stmt);
$table = $installer->getTable('cpwms/attribute');
foreach ($result as $data) {
    $installer->getConnection()->insert($table, $data);
}

$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('cpwms/xmlformat')}_int;
    DROP TABLE IF EXISTS {$this->getTable('cpwms/xmlformat')}_datetime;
    DROP TABLE IF EXISTS {$this->getTable('cpwms/xmlformat')}_decimal;
    DROP TABLE IF EXISTS {$this->getTable('cpwms/xmlformat')}_varchar;
    DROP TABLE IF EXISTS {$this->getTable('cpwms/xmlformat')}_text;
");

try {
    $table = $installer->getConnection()
        ->newTable($installer->getTable(array('cpwms/xmlformat', 'int')))
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
            array('cpwms/xmlformat', 'int'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'int'), array('entity_id')), array('entity_id'))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'int'), array('attribute_id')), array('attribute_id'))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'int'), array('store_id')), array('store_id'))
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'int'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'int'), 'entity_id', 'cpwms/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpwms/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'int'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('wms xmlformat Int Attribute Backend Table');
    $installer->getConnection()->createTable($table);

    $table = $installer->getConnection()
        ->newTable($installer->getTable(array('cpwms/xmlformat', 'decimal')))
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
            array('cpwms/xmlformat', 'decimal'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'decimal'), array('entity_id')), array('entity_id'))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'decimal'), array('attribute_id')), array('attribute_id'))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'decimal'), array('store_id')), array('store_id'))
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'decimal'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'decimal'), 'entity_id', 'cpwms/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpwms/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'decimal'), 'store_id', 'core/store', 'store_id'),
        //        'FK_wms_xmlformat_decimal_storeid',
        'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('wms xmlformat Decimal Attribute Backend Table');
    $installer->getConnection()->createTable($table);

    $table = $installer->getConnection()
        ->newTable($installer->getTable(array('cpwms/xmlformat', 'datetime')))
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
            array('cpwms/xmlformat', 'datetime'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'datetime'), array('entity_id')), array('entity_id'))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'datetime'), array('attribute_id')), array('attribute_id'))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'datetime'), array('store_id')), array('store_id'))
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'datetime'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'datetime'), 'entity_id', 'cpwms/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpwms/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'datetime'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('wms xmlformat Datetime Attribute Backend Table');
    $installer->getConnection()->createTable($table);

    $table = $installer->getConnection()
        ->newTable($installer->getTable(array('cpwms/xmlformat', 'varchar')))
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
            array('cpwms/xmlformat', 'varchar'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'varchar'), array('entity_id')), array('entity_id'))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'varchar'), array('attribute_id')), array('attribute_id'))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'varchar'), array('store_id')), array('store_id'))
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'varchar'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'varchar'), 'entity_id', 'cpwms/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpwms/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'varchar'), 'store_id', 'core/store', 'store_id'),
        //        'FK_wms_xmlformat_varchar_storeid',
        'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('wms xmlformat Varchar Attribute Backend Table');
    $installer->getConnection()->createTable($table);

    $table = $installer->getConnection()
        ->newTable($installer->getTable(array('cpwms/xmlformat', 'text')))
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
            array('cpwms/xmlformat', 'varchar'), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ), array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'text'), array('entity_id')), array('entity_id'))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'text'), array('attribute_id')), array('attribute_id'))
        ->addIndex($installer->getIdxName(array('cpwms/xmlformat', 'text'), array('store_id')), array('store_id'))
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'text'), 'attribute_id', 'eav/attribute', 'attribute_id'), 'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'text'), 'entity_id', 'cpwms/xmlformat', 'entity_id'), 'entity_id', $installer->getTable('cpwms/xmlformat'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addForeignKey(
        $installer->getFkName(array('cpwms/xmlformat', 'text'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->setComment('wms xmlformat Text Attribute Backend Table');
    $installer->getConnection()->createTable($table);
} catch (Exception $e) {
    echo $e;
}

$installer->run("
    DROP TABLE IF EXISTS {$installer->getTable('wmslog/log')};
    CREATE TABLE wms_execution_log (
        `id` int(11) NOT NULL auto_increment,
        `format_id` int(11),
        `execution_date` datetime,
        `function` text,
        `request` text,
        `response` text,
        `response_format` text,
        `status` text,
        `link_to_file` text,
        `order_id` int(11),
        `url_of_request` text,
        `wms_files` text,
        `wms_statuses` text,
        `parent_id` int(11),
        PRIMARY KEY (id)
    ) ENGINE=innoDB DEFAULT CHARSET=utf8;
");

$installer->run("
    DROP TABLE IF EXISTS {$installer->getTable('wmsreplacer/replacer')};
    CREATE TABLE {$installer->getTable('wmsreplacer/replacer')} (
        `id` int(11) NOT NULL auto_increment,
        `pattern` text,
        `helper` text,
        `conditions` text,
        PRIMARY KEY (id)
    ) ENGINE=innoDB DEFAULT CHARSET=utf8;
");

$installer->run("
    DROP TABLE IF EXISTS {$installer->getTable('wmsreplacer/replacer_line')};
    CREATE TABLE {$installer->getTable('wmsreplacer/replacer_line')} (
        `id` int(11) NOT NULL auto_increment,
        `replacer_id` int(11),
        `line_id` int(11),
        `default_value` varchar (255),
        `type` int(3),
        PRIMARY KEY (id)
    ) ENGINE=innoDB DEFAULT CHARSET=utf8;
");

$installer->run("
    DROP TABLE IF EXISTS {$installer->getTable('wmsreplacer/replacer_sub')};
    CREATE TABLE {$installer->getTable('wmsreplacer/replacer_sub')} (
        `id` int(11) NOT NULL auto_increment,
        `replacer_id` int(11),
        `line_id` int(11),
        `store_id` int(11),
        `value` varchar (255),
        PRIMARY KEY (id)
    ) ENGINE=innoDB DEFAULT CHARSET=utf8;
");

$installer->run("DROP TABLE IF EXISTS {$installer->getTable('merchandise/merchandise')};
    CREATE TABLE {$installer->getTable('merchandise/merchandise')} (
        `id` int(11) NOT NULL auto_increment,
        `type_id` int(11),
        `name` text,
        `category_id` int(11),
        `category_caption` text,
        `content` text,
        `image_url` text,
        PRIMARY KEY  (`id`)
    ) ENGINE=innoDB DEFAULT CHARSET=utf8;
");

/************************************ATTRIBUTES ADDING**************************************/

$entityTypeId = $installer->getEntityTypeId('wms_xmlformat');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$newFields = array(
    'last_sent' => array(
        'type'      => 'datetime',
        'label'     => 'The last time the administration',
        'backend'   => 'eav/entity_attribute_backend_datetime',
        'input'     => 'date',
    ),
    'schedule'       => array(
        'type'      => 'int',
        'label'     => 'Schedule',
        'input'     => 'select'
    ),
    'request'       => array(
        'type'      => 'text',
        'label'     => 'Request',
        'input'     => 'textarea'
    ),
    'response'       => array(
        'type'      => 'text',
        'label'     => 'Response',
        'input'     => 'textarea'
    ),
    'filename_out'  => array(
        'type'      => 'text',
        'label'     => 'Filename Out',
        'input'     => 'text'
    ),
    'custom_url' => array(
        'type'      => 'text',
        'label'     => 'Custom URL',
        'input'     => 'text',
    ),
    'condition' => array(
        'type'      => 'text',
        'label'     => 'Order Condition',
        'input'     => 'text',
    ),
    'url_request' => array(
        'type'      => 'text',
        'label'     => 'Request URL',
        'input'     => 'text',
    ),
    'pattern_request' => array(
        'type'      => 'text',
        'label'     => 'Request Pattern File',
        'input'     => 'text',
    ),
    'pattern_response' => array(
        'type'      => 'text',
        'label'     => 'Response Pattern File',
        'input'     => 'text',
    ),
    'request_method' => array(
        'type'      => 'int',
        'label'     => 'Method Request',
        'input'     => 'select',
    ),
    'response_method' => array(
        'type'      => 'int',
        'label'     => 'Method Response',
        'input'     => 'select',
    ),
    'schedulepro'       => array(
        'type'      => 'text',
        'label'     => 'Schedule',
        'input'     => 'text',
        'default'   => '*Month *Day *Hour *Minute',
    ),
    'precondition'       => array(
        'type'      => 'text',
        'label'     => 'Precondition',
        'input'     => 'text',
        'global'    => 2,
    )
);

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
foreach($newFields as $attributeName => $attributeDefs) {
    $setup->addAttribute($wms_entity, $attributeName, array(
        'group'             => $attributeDefs['group']?$attributeDefs['group']:'',
        'type'              => $attributeDefs['type'],
        'label'             => $attributeDefs['label'],
        'input'             => $attributeDefs['input'],
        'backend'           => $attributeDefs['backend']?$attributeDefs['backend']:'',
        'frontend'          => $attributeDefs['frontend']?$attributeDefs['frontend']:'',
        'class'             => $attributeDefs['class']?$attributeDefs['class']:'',
        'source'            => $attributeDefs['source']?$attributeDefs['source']:'',
        'global'            => $attributeDefs['global']?$attributeDefs['global']:0,
        'visible'           => $attributeDefs['visible']?$attributeDefs['visible']:true,
        'required'          => $attributeDefs['required']?$attributeDefs['required']:false,
        'user_defined'      => $attributeDefs['user_defined']?$attributeDefs['user_defined']:true,
        'default'           => $attributeDefs['default']?$attributeDefs['default']:'',
        'searchable'        => $attributeDefs['searchable']?$attributeDefs['searchable']:false,
        'filterable'        => $attributeDefs['filterable']?$attributeDefs['filterable']:false,
        'comparable'        => $attributeDefs['comparable']?$attributeDefs['comparable']:false,
        'visible_on_front'  => $attributeDefs['visible_on_front']?$attributeDefs['visible_on_front']:false,
        'unique'            => $attributeDefs['unique']?$attributeDefs['unique']:false,
        'option'            => $attributeDefs['option']?$attributeDefs['option']:'',
        'note'           => $attributeDefs['note']?$attributeDefs['note']:''
    ));
}

$installer->run("
    DELETE FROM eav_entity_attribute WHERE attribute_id=(SELECT attribute_id FROM eav_attribute WHERE attribute_code='condition' AND entity_type_id='{$entityTypeId}');
    DELETE FROM eav_entity_attribute WHERE attribute_id=(SELECT attribute_id FROM eav_attribute WHERE attribute_code='url_request' AND entity_type_id='{$entityTypeId}');
    DELETE FROM eav_entity_attribute WHERE attribute_id=(SELECT attribute_id FROM eav_attribute WHERE attribute_code='pattern_request' AND entity_type_id='{$entityTypeId}');
    DELETE FROM eav_entity_attribute WHERE attribute_id=(SELECT attribute_id FROM eav_attribute WHERE attribute_code='pattern_response' AND entity_type_id='{$entityTypeId}');
    DELETE FROM eav_entity_attribute WHERE attribute_id=(SELECT attribute_id FROM eav_attribute WHERE attribute_code='request_method' AND entity_type_id='{$entityTypeId}');
    DELETE FROM eav_entity_attribute WHERE attribute_id=(SELECT attribute_id FROM eav_attribute WHERE attribute_code='response_method' AND entity_type_id='{$entityTypeId}');
    DELETE FROM eav_entity_attribute WHERE attribute_id=(SELECT attribute_id FROM eav_attribute WHERE attribute_code='schedulepro' AND entity_type_id='{$entityTypeId}');
    DELETE FROM eav_entity_attribute WHERE attribute_id=(SELECT attribute_id FROM eav_attribute WHERE attribute_code='precondition' AND entity_type_id='{$entityTypeId}');
");
$installer->run("
    INSERT INTO eav_entity_attribute (entity_type_id, attribute_set_id, attribute_group_id, attribute_id, sort_order) VALUES ({$entityTypeId}, {$attributeSetId}, {$attributeGroupId}, (SELECT attribute_id FROM eav_attribute WHERE attribute_code='condition' AND entity_type_id='{$entityTypeId}'), 3);
    INSERT INTO eav_entity_attribute (entity_type_id, attribute_set_id, attribute_group_id, attribute_id, sort_order) VALUES ({$entityTypeId}, {$attributeSetId}, {$attributeGroupId}, (SELECT attribute_id FROM eav_attribute WHERE attribute_code='url_request' AND entity_type_id='{$entityTypeId}'), 5);
    INSERT INTO eav_entity_attribute (entity_type_id, attribute_set_id, attribute_group_id, attribute_id, sort_order) VALUES ({$entityTypeId}, {$attributeSetId}, {$attributeGroupId}, (SELECT attribute_id FROM eav_attribute WHERE attribute_code='pattern_request' AND entity_type_id='{$entityTypeId}'), 12);
    INSERT INTO eav_entity_attribute (entity_type_id, attribute_set_id, attribute_group_id, attribute_id, sort_order) VALUES ({$entityTypeId}, {$attributeSetId}, {$attributeGroupId}, (SELECT attribute_id FROM eav_attribute WHERE attribute_code='pattern_response' AND entity_type_id='{$entityTypeId}'), 13);
    INSERT INTO eav_entity_attribute (entity_type_id, attribute_set_id, attribute_group_id, attribute_id, sort_order) VALUES ({$entityTypeId}, {$attributeSetId}, {$attributeGroupId}, (SELECT attribute_id FROM eav_attribute WHERE attribute_code='request_method' AND entity_type_id='{$entityTypeId}'), 2);
    INSERT INTO eav_entity_attribute (entity_type_id, attribute_set_id, attribute_group_id, attribute_id, sort_order) VALUES ({$entityTypeId}, {$attributeSetId}, {$attributeGroupId}, (SELECT attribute_id FROM eav_attribute WHERE attribute_code='response_method' AND entity_type_id='{$entityTypeId}'), 2);
    INSERT INTO eav_entity_attribute (entity_type_id, attribute_set_id, attribute_group_id, attribute_id, sort_order) VALUES ({$entityTypeId}, {$attributeSetId}, {$attributeGroupId}, (SELECT attribute_id FROM eav_attribute WHERE attribute_code='schedulepro' AND entity_type_id='{$entityTypeId}'), 13);
    INSERT INTO eav_entity_attribute (entity_type_id, attribute_set_id, attribute_group_id, attribute_id, sort_order) VALUES ({$entityTypeId}, {$attributeSetId}, {$attributeGroupId}, (SELECT attribute_id FROM eav_attribute WHERE attribute_code='precondition' AND entity_type_id='{$entityTypeId}'), 4);
");

$newFields = array(
//    'continuity_is' => array(
//        'type'      => 'int',
//        'label'     => 'Has Continuity',
//        'backend'   => 'int',
//        'input'     => 'select',
//        'source'    => 'eav/entity_attribute_source_boolean',
//        'default'   => 0
//    ),
//    'continuity_sku' => array(
//        'type'      => 'varchar',
//        'label'     => 'Continuity SKU',
//        'backend'   => 'static',
//        'input'     => 'text',
//    ),
//    'continuity_price' => array(
//        'type'      => 'varchar',
//        'label'     => 'Continuity Price',
//        'backend'   => 'decimal',
//        'input'     => 'price',
//    ),
//    'continuity_payment2' => array(
//        'type'      => 'varchar',
//        'label'     => 'Payment 2',
//        'backend'   => 'decimal',
//        'input'     => 'price',
//    ),
//    'continuity_frequency' => array(
//        'type'      => 'int',
//        'label'     => 'Frequency',
//        'backend'   => 'static',
//        'input'     => 'text',
//        'default'   => '60',
//    ),
//    'continuity_shipping_charge' => array(
//        'type'      => 'varchar',
//        'label'     => 'Shipping charge',
//        'backend'   => 'decimal',
//        'input'     => 'price',
//        'default'   => '7.95',
//        'note'   => 'Shipping charge for next shipment'
//    ),
//    'continuity_number_payments' => array(
//        'type'      => 'int',
//        'label'     => 'Number of payments',
//        'backend'   => 'static',
//        'input'     => 'text',
//        'default'   => '2',
//    ),
    'cafepress_image' => array(
        'type'      => 'varchar',
        'label'     => 'Image for Cafe Press',
        'input'     => 'media_image',
        'group'     => 'Images',
        'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'frontend'  => 'catalog/product_attribute_frontend_image'
    ),
    'design_id' => array(
        'type'      => 'varchar',
        'label'     => 'CP Design Id',
        'input'     => 'text',
        'group'     => 'Cafe Press',
        'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
    ),
    'image_location' => array(
        'type'      => 'int',
        'label'     => 'CP Image Location',
        'input'     => 'select',
        'source'    => 'eav/entity_attribute_source_table',
        'option'   => array('value' => array(
            'f_c' => array('FrontCenter'),
            'f_p' => array('FrontPocket'),
            'b_c' => array('BackCenter'),
            'b_s' => array('BackShoulder'))),
        'group'     => 'Cafe Press',
        'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
    ),
    'media_height' => array(
        'type'      => 'varchar',
        'label'     => 'CP Media Height',
        'input'     => 'text',
        'group'     => 'Cafe Press',
        'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
    ),
    'cp_sellprice' => array(
        'type'      => 'decimal',
        'label'     => 'CP Sell Price',
        'input'     => 'price',
        'backend'   => 'catalog/product_attribute_backend_price',
        'group'     => 'Cafe Press',
        'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
    ),
    'cp_save_product_id' => array(
        'type'      => 'varchar',
        'label'     => 'CP Product Id',
        'input'     => 'text',
        'group'     => 'Cafe Press',
        'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
    ),
    'cp_ptn' => array(
        'type'      => 'varchar',
        'label'     => 'CP PTN',
        'input'     => 'text',
        'group'     => 'Cafe Press',
        'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
    ),
    'cp_user_token' => array(
        'type'      => 'varchar',
        'label'     => 'CP User Token',
        'input'     => 'text',
        'group'     => 'Cafe Press',
        'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
    ),
    'cp_merchandise_content' => array(
        'type'      => 'text',
        'label'     => 'CP Merchandise Content',
        'input'     => 'textarea',
        'group'     => 'Cafe Press',
        'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
    ),
    'cp_create_product_xml' => array(
        'type'      => 'text',
        'label'     => 'CP Create Product XML',
        'input'     => 'textarea',
        'group'     => 'Cafe Press',
        'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
    ),
    'cp_save_product_xml' => array(
        'type'      => 'text',
        'label'     => 'CP Save Product Result',
        'input'     => 'textarea',
        'group'     => 'Cafe Press',
        'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
    ),
    'color' => array(
        'type'      => 'int',
        'label'     => 'Color',
        'input'     => 'select',
        'group'     => 'Cafe Press',
        'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//        'source'    => 'eav/entity_attribute_source_table',
        'is_configurable'   => true
    ),
    'size' => array(
        'type'      => 'int',
        'label'     => 'Size',
        'input'     => 'select',
        'group'     => 'Cafe Press',
        'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
//        'source'    => 'eav/entity_attribute_source_table',
        'is_configurable'   => true
    )
);

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
foreach($newFields as $attributeName => $attributeDefs) {
    $setup->addAttribute('catalog_product', $attributeName, array(
        'group'             => $attributeDefs['group']?$attributeDefs['group']:'',
        'type'              => $attributeDefs['type'],
        'label'             => $attributeDefs['label'],
        'input'             => $attributeDefs['input'],
        'backend'           => $attributeDefs['backend']?$attributeDefs['backend']:'',
        'frontend'          => $attributeDefs['frontend']?$attributeDefs['frontend']:'',
        'class'             => $attributeDefs['class']?$attributeDefs['class']:'',
        'source'            => $attributeDefs['source']?$attributeDefs['source']:'',
        'global'            => $attributeDefs['global']?$attributeDefs['global']:0,
        'visible'           => $attributeDefs['visible']?$attributeDefs['visible']:true,
        'required'          => $attributeDefs['required']?$attributeDefs['required']:false,
        'user_defined'      => $attributeDefs['user_defined']?$attributeDefs['user_defined']:true,
        'default'           => $attributeDefs['default']?$attributeDefs['default']:'',
        'searchable'        => $attributeDefs['searchable']?$attributeDefs['searchable']:false,
        'filterable'        => $attributeDefs['filterable']?$attributeDefs['filterable']:false,
        'comparable'        => $attributeDefs['comparable']?$attributeDefs['comparable']:false,
        'visible_on_front'  => $attributeDefs['visible_on_front']?$attributeDefs['visible_on_front']:false,
        'unique'            => $attributeDefs['unique']?$attributeDefs['unique']:false,
        'option'            => $attributeDefs['option']?$attributeDefs['option']:'',
        'note'              => $attributeDefs['note']?$attributeDefs['note']:'',
        'is_configurable'   => $attributeDefs['is_configurable']?$attributeDefs['is_configurable']:false
    ));
}

/*************************************COLUMNS ADDING****************************************/

$conn = $installer->getConnection();
if ($conn->isTableExists('enterprise_sales_order_grid_archive')) {
    $result = $conn->fetchOne("SHOW TABLES WHERE Tables_in_clear='enterprise_sales_order_grid_archive'");
    if($result){
        $installer->run("
            ALTER TABLE enterprise_sales_order_grid_archive DROP COLUMN wms_file;
            ALTER TABLE enterprise_sales_order_grid_archive DROP COLUMN wms_file_status;
        ");
        $installer->run("
            ALTER TABLE enterprise_sales_order_grid_archive ADD wms_file text COMMENT 'Wms File';
            ALTER TABLE enterprise_sales_order_grid_archive ADD wms_file_status text COMMENT 'Wms File Status';
        ");
    }
}

$needed_columns = array('wms_file', 'wms_file_status');
$conn = $installer->getConnection();
$columns = $conn->fetchAll("SHOW COLUMNS FROM sales_flat_order_grid");
foreach($columns as $column)
{
    $column_name = $column['Field'];
    if(in_array($column_name, $needed_columns)){
        $installer->run("ALTER TABLE sales_flat_order_grid DROP COLUMN {$column_name};");
    }
}

$needed_columns = array('wms_file', 'wms_file_status');
$conn = $installer->getConnection();
$columns = $conn->fetchAll("SHOW COLUMNS FROM sales_flat_order");
foreach($columns as $column)
{
    $column_name = $column['Field'];
    if(in_array($column_name, $needed_columns)){
        $installer->run("ALTER TABLE sales_flat_order DROP COLUMN {$column_name};");
    }
}

$installer->run("
    ALTER TABLE sales_flat_order_grid ADD wms_file text COMMENT 'Wms File';
    ALTER TABLE sales_flat_order_grid ADD wms_file_status text COMMENT 'Wms File Status';
    ALTER TABLE sales_flat_order ADD wms_file text COMMENT 'Wms File';
    ALTER TABLE sales_flat_order ADD wms_file_status text COMMENT 'Wms File Status';
");

$needed_columns = array('cc_cid', 'authorized', 'authorization_result', 'transaction_id', 'authorization_code', 'tax_percent', 'discount_percent', 'custom_number');
$conn = $installer->getConnection();
$columns = $conn->fetchAll("SHOW COLUMNS FROM sales_flat_order");
foreach($columns as $column)
{
    $column_name = $column['Field'];
    if(in_array($column_name, $needed_columns)){
        $installer->run("ALTER TABLE sales_flat_order DROP COLUMN {$column_name};");
    }
}

$installer->run("
    ALTER TABLE sales_flat_order ADD cc_cid text;
    ALTER TABLE sales_flat_order ADD authorized tinyint(1) unsigned NOT NULL DEFAULT 0;
    ALTER TABLE sales_flat_order ADD authorization_result text;
    ALTER TABLE sales_flat_order ADD transaction_id text;
    ALTER TABLE sales_flat_order ADD authorization_code text;
    ALTER TABLE sales_flat_order ADD tax_percent decimal(12,4);
    ALTER TABLE sales_flat_order ADD discount_percent decimal(12,4);
    ALTER TABLE sales_flat_order ADD custom_number text;
");

$attribute_codes = array(
    '1' => 'name',
    '2' => 'filename_out',
    '4' => 'status',
    '5' => 'custom_url',
    '6' => 'header',
    '7' => 'main_part',
    '8' => 'addresses',
    '9' => 'product',
    '10' => 'footer',
    '11' => 'request',
    '12' => 'response',
    '13' => 'schedule',
    '14' => 'last_sent'
);
$conn = $installer->getConnection();
$attribute_ids = array();
foreach($attribute_codes as $position => $attribute_code){
    $result = $conn->fetchOne("SELECT attribute_id FROM eav_attribute WHERE attribute_code='{$attribute_code}' AND entity_type_id='{$entityTypeId}'");
    if($result){
        $attribute_ids[$position] = $result;
    }
}

foreach($attribute_ids as $position => $attribute_id){
    $installer->run("
        DELETE FROM eav_entity_attribute WHERE attribute_id={$attribute_id};
        INSERT INTO eav_entity_attribute (entity_type_id, attribute_set_id, attribute_group_id, attribute_id, sort_order) VALUES ({$entityTypeId}, {$attributeSetId}, {$attributeGroupId}, {$attribute_id}, {$position});
    ");
}

$conn = $installer->getConnection();
if($conn->fetchOne("SHOW COLUMNS FROM eav_attribute_option WHERE Field='custom_id'")){
    $installer->run("ALTER TABLE eav_attribute_option DROP COLUMN custom_id");
}
$installer->run("ALTER TABLE eav_attribute_option ADD custom_id text");

/***********************************DIRECTORIES CREATING*********************************/

$path = Mage::getBaseDir('media').DS.'xmls';
if(!file_exists($path)){
    mkdir($path);
}
$path = Mage::getBaseDir('media').DS.'xmls'.DS.'inbound';
if(!file_exists($path)){
    mkdir($path);
}
$path = Mage::getBaseDir('media').DS.'xmls'.DS.'outbound';
if(!file_exists($path)){
    mkdir($path);
}
$path = Mage::getBaseDir('media').DS.'cafepress';
if(!file_exists($path)){
    mkdir($path, 0777);
}
$path = Mage::getBaseDir('media').DS.'cafepress'.DS.'images';
if(!file_exists($path)){
    mkdir($path, 0777);
}
$path = Mage::getBaseDir('media').DS.'cafepress'.DS.'prints';
if(!file_exists($path)){
    mkdir($path, 0777);
}

$installer->endSetup();
