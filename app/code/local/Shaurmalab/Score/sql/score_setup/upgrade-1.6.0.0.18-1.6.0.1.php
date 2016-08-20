<?php
$installer = $this;

$installer->startSetup();

$oggettoCategoryEntityTypeId = $installer->getEntityTypeId('score_category');
$oggettoEntityTypeId = $installer->getEntityTypeId('score_oggetto');



$installer->run("
ALTER TABLE `score_eav_attribute` ADD COLUMN owner VARCHAR(50) NOT NULL default '';
ALTER TABLE `score_eav_attribute` ADD COLUMN is_public INT(1) NOT NULL default 0;
ALTER TABLE `score_eav_attribute` ADD COLUMN share_to TEXT NOT NULL default '';
");



//MAKE IT WORK:
$installer->addAttribute('score_oggetto', 'owner', array(
    'type'              => 'varchar',
    'label'             => 'Owner',
    'visible'           => true,
    'required'          => true,
    'searchable'        => false,
    'is_configurable'   => false,
    'is_user_defined'	=> true
));

$installer->addAttribute('score_oggetto', 'is_public', array(
    'type'              => 'int',
    'label'             => 'Is Public',
    'visible'           => true,
    'required'          => true,
    'searchable'        => false,
    'is_configurable'   => false,
    'is_user_defined'	=> true
));

$installer->addAttribute('score_oggetto', 'share_to', array(
    'type'              => 'text',
    'label'             => 'Share To',
    'visible'           => true,
    'required'          => false,
    'searchable'        => false,
    'is_configurable'   => false,
    'is_user_defined'	=> true
));

$installer->run("
update `{$installer->getTable('eav/attribute')}` set `is_required`=0, `is_unique`=0,`is_user_defined`=1 where `entity_type_id` IN ({$oggettoCategoryEntityTypeId},{$oggettoEntityTypeId})
");


$installer->endSetup();