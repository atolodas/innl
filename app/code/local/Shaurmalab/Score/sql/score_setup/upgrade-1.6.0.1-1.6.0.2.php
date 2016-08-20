<?php
$installer = $this;

$installer->startSetup();

$oggettoCategoryEntityTypeId = $installer->getEntityTypeId('score_category');
$oggettoEntityTypeId = $installer->getEntityTypeId('score_oggetto');

$installer->run("

INSERT INTO `score_category_entity` (`entity_id`, `entity_type_id`, `attribute_set_id`, `parent_id`, `created_at`, `updated_at`, `path`, `position`, `level`, `children_count`) VALUES
(1, 11, 0, 0, '2013-10-02 01:02:03', '2013-10-02 01:02:03', '1', 0, 0, 1),
(2, 11, 13, 1, '2013-10-02 01:02:04', '2013-10-02 01:02:04', '1/2', 1, 1, 0);

INSERT INTO `score_category_entity_int` (`value_id`, `entity_type_id`, `attribute_id`, `store_id`, `entity_id`, `value`) VALUES
(1, 11, 220, 0, 1, 1),
(2, 11, 220, 1, 1, 1),
(3, 11, 195, 0, 2, 1),
(4, 11, 220, 0, 2, 1),
(5, 11, 195, 1, 2, 1),
(6, 11, 220, 1, 2, 1);

INSERT INTO `score_category_entity_varchar` (`value_id`, `entity_type_id`, `attribute_id`, `store_id`, `entity_id`, `value`) VALUES
(1, 11, 194, 0, 1, 'Root Catalog'),
(2, 11, 194, 1, 1, 'Root Catalog'),
(3, 11, 196, 1, 1, 'root-catalog'),
(4, 11, 194, 0, 2, 'Default Category'),
(5, 11, 194, 1, 2, 'Default Category'),
(6, 11, 202, 1, 2, 'PRODUCTS'),
(7, 11, 196, 1, 2, 'default-category');
");


$installer->endSetup();