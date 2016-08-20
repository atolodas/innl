<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Shaurmalab_Score_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/category_oggetto'),
    'CATALOG_CATEGORY_OGGETTO_CATEGORY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/category_oggetto'),
    'CATALOG_CATEGORY_OGGETTO_OGGETTO'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/category_oggetto_index'),
    'FK_SCORE_CATEGORY_PROD_IDX_CATEGORY_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/category_oggetto_index'),
    'FK_SCORE_CATEGORY_PROD_IDX_PROD_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/category_oggetto_index'),
    'FK_CATEGORY_OGGETTO_INDEX_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/compare_item'),
    'FK_SCORE_COMPARE_ITEM_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/compare_item'),
    'FK_SCORE_COMPARE_ITEM_OGGETTO'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/compare_item'),
    'FK_SCORE_COMPARE_ITEM_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/eav_attribute'),
    'FK_SCORE_EAV_ATTRIBUTE_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_enabled_index'),
    'FK_SCORE_OGGETTO_ENABLED_INDEX_OGGETTO_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_enabled_index'),
    'FK_SCORE_OGGETTO_ENABLED_INDEX_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto'),
    'FK_SCORE_OGGETTO_ENTITY_ATTRIBUTE_SET_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto'),
    'FK_SCORE_OGGETTO_ENTITY_ENTITY_TYPE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_attribute_media_gallery'),
    'FK_SCORE_OGGETTO_MEDIA_GALLERY_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_attribute_media_gallery'),
    'FK_SCORE_OGGETTO_MEDIA_GALLERY_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_attribute_media_gallery_value'),
    'FK_SCORE_OGGETTO_MEDIA_GALLERY_VALUE_GALLERY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_attribute_media_gallery_value'),
    'FK_SCORE_OGGETTO_MEDIA_GALLERY_VALUE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_attribute_tier_price'),
    'FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_GROUP'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_attribute_tier_price'),
    'FK_SCORE_OGGETTO_TIER_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_attribute_tier_price'),
    'FK_SCORE_PROD_ENTITY_TIER_PRICE_PROD_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_index_eav'),
    'FK_SCORE_OGGETTO_INDEX_EAV_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_index_eav'),
    'FK_SCORE_OGGETTO_INDEX_EAV_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_index_eav'),
    'FK_SCORE_OGGETTO_INDEX_EAV_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_index_eav_decimal'),
    'FK_SCORE_OGGETTO_INDEX_EAV_DECIMAL_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_index_eav_decimal'),
    'FK_SCORE_OGGETTO_INDEX_EAV_DECIMAL_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_index_eav_decimal'),
    'FK_SCORE_OGGETTO_INDEX_EAV_DECIMAL_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_index_price'),
    'FK_SCORE_OGGETTO_INDEX_PRICE_CUSTOMER_GROUP'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_index_price'),
    'FK_SCORE_OGGETTO_INDEX_PRICE_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_index_price'),
    'FK_SCORE_OGGETTO_INDEX_PRICE_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_index_tier_price'),
    'FK_SCORE_OGGETTO_INDEX_TIER_PRICE_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_index_tier_price'),
    'FK_SCORE_OGGETTO_INDEX_TIER_PRICE_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_index_tier_price'),
    'FK_SCORE_OGGETTO_INDEX_TIER_PRICE_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_index_website'),
    'FK_SCORE_OGGETTO_INDEX_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_link'),
    'FK_OGGETTO_LINK_LINKED_OGGETTO'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_link'),
    'FK_OGGETTO_LINK_OGGETTO'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_link'),
    'FK_OGGETTO_LINK_TYPE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_link_attribute'),
    'FK_ATTRIBUTE_OGGETTO_LINK_TYPE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_link_attribute_decimal'),
    'FK_DECIMAL_LINK'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_link_attribute_decimal'),
    'FK_DECIMAL_OGGETTO_LINK_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_link_attribute_int'),
    'FK_INT_OGGETTO_LINK'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_link_attribute_int'),
    'FK_INT_OGGETTO_LINK_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_link_attribute_varchar'),
    'FK_VARCHAR_LINK'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_link_attribute_varchar'),
    'FK_VARCHAR_OGGETTO_LINK_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_option'),
    'FK_SCORE_OGGETTO_OPTION_OGGETTO'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_option_price'),
    'FK_SCORE_OGGETTO_OPTION_PRICE_OPTION'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_option_price'),
    'FK_SCORE_OGGETTO_OPTION_PRICE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_option_title'),
    'FK_SCORE_OGGETTO_OPTION_TITLE_OPTION'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_option_title'),
    'FK_SCORE_OGGETTO_OPTION_TITLE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_option_type_price'),
    'FK_SCORE_OGGETTO_OPTION_TYPE_PRICE_OPTION'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_option_type_price'),
    'FK_SCORE_OGGETTO_OPTION_TYPE_PRICE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_option_type_title'),
    'FK_SCORE_OGGETTO_OPTION_TYPE_TITLE_OPTION'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_option_type_title'),
    'FK_SCORE_OGGETTO_OPTION_TYPE_TITLE_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_option_type_value'),
    'FK_SCORE_OGGETTO_OPTION_TYPE_VALUE_OPTION'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_relation'),
    'FK_SCORE_OGGETTO_RELATION_CHILD'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_relation'),
    'FK_SCORE_OGGETTO_RELATION_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_super_attribute'),
    'FK_SUPER_OGGETTO_ATTRIBUTE_OGGETTO'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_super_attribute_label'),
    'FK_SCORE_PROD_SUPER_ATTR_LABEL_ATTR'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_super_attribute_label'),
    'FK_SCORE_PROD_SUPER_ATTR_LABEL_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_super_attribute_pricing'),
    'CATALOG_OGGETTO_SUPER_ATTRIBUTE_PRICING_IBFK_1'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_super_attribute_pricing'),
    'FK_SCORE_OGGETTO_SUPER_PRICE_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_super_attribute_pricing'),
    'FK_SUPER_OGGETTO_ATTRIBUTE_PRICING'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_super_link'),
    'CATALOG_OGGETTO_SUPER_LINK_IBFK_1'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_super_link'),
    'CATALOG_OGGETTO_SUPER_LINK_IBFK_2'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_super_link'),
    'FK_SUPER_OGGETTO_LINK_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_super_link'),
    'FK_SUPER_OGGETTO_LINK_PARENT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_website'),
    'FK_SCORE_OGGETTO_WEBSITE_WEBSITE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_website'),
    'FK_SCORE_WEBSITE_OGGETTO_OGGETTO'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'datetime')),
    'FK_SCORE_CATEGORY_ENTITY_DATETIME_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'datetime')),
    'FK_SCORE_CATEGORY_ENTITY_DATETIME_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'datetime')),
    'FK_SCORE_CATEGORY_ENTITY_DATETIME_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'decimal')),
    'FK_SCORE_CATEGORY_ENTITY_DECIMAL_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'decimal')),
    'FK_SCORE_CATEGORY_ENTITY_DECIMAL_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'decimal')),
    'FK_SCORE_CATEGORY_ENTITY_DECIMAL_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'int')),
    'FK_SCORE_CATEGORY_EMTITY_INT_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'int')),
    'FK_SCORE_CATEGORY_EMTITY_INT_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'int')),
    'FK_SCORE_CATEGORY_EMTITY_INT_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'text')),
    'FK_SCORE_CATEGORY_ENTITY_TEXT_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'text')),
    'FK_SCORE_CATEGORY_ENTITY_TEXT_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'text')),
    'FK_SCORE_CATEGORY_ENTITY_TEXT_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'varchar')),
    'FK_SCORE_CATEGORY_ENTITY_VARCHAR_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'varchar')),
    'FK_SCORE_CATEGORY_ENTITY_VARCHAR_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/category', 'varchar')),
    'FK_SCORE_CATEGORY_ENTITY_VARCHAR_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'datetime')),
    'FK_SCORE_OGGETTO_ENTITY_DATETIME_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'datetime')),
    'FK_SCORE_OGGETTO_ENTITY_DATETIME_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'datetime')),
    'FK_SCORE_PROD_ENTITY_DATETIME_PROD_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'decimal')),
    'FK_SCORE_OGGETTO_ENTITY_DECIMAL_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'decimal')),
    'FK_SCORE_OGGETTO_ENTITY_DECIMAL_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'decimal')),
    'FK_SCORE_PROD_ENTITY_DECIMAL_PROD_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'gallery')),
    'FK_SCORE_OGGETTO_ENTITY_GALLERY_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'gallery')),
    'FK_SCORE_OGGETTO_ENTITY_GALLERY_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'gallery')),
    'FK_SCORE_OGGETTO_ENTITY_GALLERY_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'int')),
    'FK_SCORE_OGGETTO_ENTITY_INT_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'int')),
    'FK_SCORE_OGGETTO_ENTITY_INT_OGGETTO_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'int')),
    'FK_SCORE_OGGETTO_ENTITY_INT_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'text')),
    'FK_SCORE_OGGETTO_ENTITY_TEXT_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'text')),
    'FK_SCORE_OGGETTO_ENTITY_TEXT_OGGETTO_ENTITY'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'text')),
    'FK_SCORE_OGGETTO_ENTITY_TEXT_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'varchar')),
    'FK_SCORE_OGGETTO_ENTITY_VARCHAR_ATTRIBUTE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'varchar')),
    'FK_SCORE_OGGETTO_ENTITY_VARCHAR_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable(array('score/oggetto', 'varchar')),
    'FK_SCORE_PROD_ENTITY_VARCHAR_PROD_ENTITY'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute'),
    'IDX_USED_FOR_SORT_BY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('eav/attribute'),
    'IDX_USED_IN_OGGETTO_LISTING'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/category_anchor_indexer_idx'),
    'IDX_CATEGORY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/category_anchor_indexer_tmp'),
    'IDX_CATEGORY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/category'),
    'IDX_LEVEL'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/category_oggetto'),
    'UNQ_CATEGORY_OGGETTO'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/category_oggetto'),
    'CATALOG_CATEGORY_OGGETTO_CATEGORY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/category_oggetto'),
    'CATALOG_CATEGORY_OGGETTO_OGGETTO'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/category_oggetto_index'),
    'UNQ_CATEGORY_OGGETTO'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/category_oggetto_index'),
    'FK_SCORE_CATEGORY_OGGETTO_INDEX_OGGETTO_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/category_oggetto_index'),
    'FK_SCORE_CATEGORY_OGGETTO_INDEX_CATEGORY_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/category_oggetto_index'),
    'IDX_JOIN'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/category_oggetto_index'),
    'IDX_BASE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/category_oggetto_enabled_indexer_idx'),
    'IDX_OGGETTO'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/category_oggetto_enabled_indexer_tmp'),
    'IDX_OGGETTO'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/category_oggetto_indexer_idx'),
    'IDX_OGGETTO_CATEGORY_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/compare_item'),
    'FK_SCORE_COMPARE_ITEM_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/compare_item'),
    'FK_SCORE_COMPARE_ITEM_OGGETTO'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/compare_item'),
    'IDX_VISITOR_OGGETTOS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/compare_item'),
    'IDX_CUSTOMER_OGGETTOS'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/compare_item'),
    'FK_SCORE_COMPARE_ITEM_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/eav_attribute'),
    'IDX_USED_FOR_SORT_BY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/eav_attribute'),
    'IDX_USED_IN_OGGETTO_LISTING'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_enabled_index'),
    'UNQ_OGGETTO_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_enabled_index'),
    'IDX_OGGETTO_VISIBILITY_IN_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_enabled_index'),
    'FK_SCORE_OGGETTO_ENABLED_INDEX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto'),
    'FK_SCORE_OGGETTO_ENTITY_ENTITY_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto'),
    'FK_SCORE_OGGETTO_ENTITY_ATTRIBUTE_SET_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto'),
    'SKU'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_attribute_media_gallery'),
    'FK_SCORE_OGGETTO_MEDIA_GALLERY_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_attribute_media_gallery'),
    'FK_SCORE_OGGETTO_MEDIA_GALLERY_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_attribute_media_gallery_value'),
    'FK_SCORE_OGGETTO_MEDIA_GALLERY_VALUE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_attribute_tier_price'),
    'UNQ_CATALOG_OGGETTO_TIER_PRICE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_attribute_tier_price'),
    'FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_OGGETTO_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_attribute_tier_price'),
    'FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_GROUP'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_attribute_tier_price'),
    'FK_SCORE_OGGETTO_TIER_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_index_eav'),
    'IDX_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_index_eav'),
    'IDX_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_index_eav'),
    'IDX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_index_eav'),
    'IDX_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_index_eav_decimal'),
    'IDX_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_index_eav_decimal'),
    'IDX_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_index_eav_decimal'),
    'IDX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_index_eav_decimal'),
    'IDX_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_idx'),
    'IDX_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_idx'),
    'IDX_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_idx'),
    'IDX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_idx'),
    'IDX_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_tmp'),
    'IDX_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_tmp'),
    'IDX_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_tmp'),
    'IDX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_tmp'),
    'IDX_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_indexer_idx'),
    'IDX_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_indexer_idx'),
    'IDX_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_indexer_idx'),
    'IDX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_indexer_idx'),
    'IDX_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_indexer_tmp'),
    'IDX_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_indexer_tmp'),
    'IDX_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_indexer_tmp'),
    'IDX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_eav_indexer_tmp'),
    'IDX_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_index_price'),
    'IDX_CUSTOMER_GROUP'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_index_price'),
    'IDX_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_index_price'),
    'IDX_MIN_PRICE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_price_indexer_idx'),
    'IDX_CUSTOMER_GROUP'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_price_indexer_idx'),
    'IDX_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_price_indexer_idx'),
    'IDX_MIN_PRICE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_price_indexer_tmp'),
    'IDX_CUSTOMER_GROUP'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_price_indexer_tmp'),
    'IDX_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_price_indexer_tmp'),
    'IDX_MIN_PRICE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_index_tier_price'),
    'FK_SCORE_OGGETTO_INDEX_TIER_PRICE_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_index_tier_price'),
    'FK_SCORE_OGGETTO_INDEX_TIER_PRICE_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_index_website'),
    'IDX_DATE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_link'),
    'IDX_UNIQUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_link'),
    'FK_LINK_OGGETTO'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_link'),
    'FK_LINKED_OGGETTO'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_link'),
    'FK_OGGETTO_LINK_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_link_attribute'),
    'FK_ATTRIBUTE_OGGETTO_LINK_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_link_attribute_decimal'),
    'FK_DECIMAL_OGGETTO_LINK_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_link_attribute_decimal'),
    'FK_DECIMAL_LINK'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_link_attribute_int'),
    'UNQ_OGGETTO_LINK_ATTRIBUTE_ID_LINK_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_link_attribute_int'),
    'FK_INT_OGGETTO_LINK_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_link_attribute_int'),
    'FK_INT_OGGETTO_LINK'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_link_attribute_varchar'),
    'FK_VARCHAR_OGGETTO_LINK_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_link_attribute_varchar'),
    'FK_VARCHAR_LINK'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_option'),
    'CATALOG_OGGETTO_OPTION_OGGETTO'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_option_price'),
    'UNQ_OPTION_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_option_price'),
    'CATALOG_OGGETTO_OPTION_PRICE_OPTION'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_option_price'),
    'CATALOG_OGGETTO_OPTION_TITLE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_option_title'),
    'UNQ_OPTION_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_option_title'),
    'CATALOG_OGGETTO_OPTION_TITLE_OPTION'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_option_title'),
    'CATALOG_OGGETTO_OPTION_TITLE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_option_type_price'),
    'UNQ_OPTION_TYPE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_option_type_price'),
    'CATALOG_OGGETTO_OPTION_TYPE_PRICE_OPTION_TYPE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_option_type_price'),
    'CATALOG_OGGETTO_OPTION_TYPE_PRICE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_option_type_title'),
    'UNQ_OPTION_TYPE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_option_type_title'),
    'CATALOG_OGGETTO_OPTION_TYPE_TITLE_OPTION'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_option_type_title'),
    'CATALOG_OGGETTO_OPTION_TYPE_TITLE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_option_type_value'),
    'CATALOG_OGGETTO_OPTION_TYPE_VALUE_OPTION'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_relation'),
    'IDX_CHILD'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_super_attribute'),
    'UNQ_OGGETTO_ID_ATTRIBUTE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_super_attribute'),
    'FK_SUPER_OGGETTO_ATTRIBUTE_OGGETTO'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_super_attribute_label'),
    'FK_SCORE_OGGETTO_SUPER_ATTRIBUTE_LABEL_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_super_attribute_label'),
    'UNQ_ATTRIBUTE_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_super_attribute_label'),
    'FK_SUPER_OGGETTO_ATTRIBUTE_LABEL'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_super_attribute_label'),
    'FK_SCORE_PROD_SUPER_ATTR_LABEL_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_super_attribute_pricing'),
    'UNQ_OGGETTO_SUPER_ATTRIBUTE_ID_VALUE_INDEX_WEBSITE_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_super_attribute_pricing'),
    'FK_SUPER_OGGETTO_ATTRIBUTE_PRICING'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_super_attribute_pricing'),
    'FK_SCORE_OGGETTO_SUPER_PRICE_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_super_link'),
    'UNQ_OGGETTO_ID_PARENT_ID'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_super_link'),
    'FK_SUPER_OGGETTO_LINK_PARENT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_super_link'),
    'FK_SCORE_OGGETTO_SUPER_LINK'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('score/oggetto_website'),
    'FK_SCORE_OGGETTO_WEBSITE_WEBSITE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'datetime')),
    'IDX_BASE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'datetime')),
    'FK_ATTRIBUTE_DATETIME_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'datetime')),
    'FK_SCORE_CATEGORY_ENTITY_DATETIME_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'datetime')),
    'FK_SCORE_CATEGORY_ENTITY_DATETIME_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'decimal')),
    'IDX_BASE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'decimal')),
    'FK_ATTRIBUTE_DECIMAL_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'decimal')),
    'FK_SCORE_CATEGORY_ENTITY_DECIMAL_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'decimal')),
    'FK_SCORE_CATEGORY_ENTITY_DECIMAL_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'int')),
    'IDX_BASE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'int')),
    'FK_ATTRIBUTE_INT_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'int')),
    'FK_SCORE_CATEGORY_EMTITY_INT_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'int')),
    'FK_SCORE_CATEGORY_EMTITY_INT_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'text')),
    'IDX_BASE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'text')),
    'FK_ATTRIBUTE_TEXT_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'text')),
    'FK_SCORE_CATEGORY_ENTITY_TEXT_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'text')),
    'FK_SCORE_CATEGORY_ENTITY_TEXT_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'varchar')),
    'IDX_BASE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'varchar')),
    'FK_ATTRIBUTE_VARCHAR_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'varchar')),
    'FK_SCORE_CATEGORY_ENTITY_VARCHAR_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/category', 'varchar')),
    'FK_SCORE_CATEGORY_ENTITY_VARCHAR_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'datetime')),
    'IDX_ATTRIBUTE_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'datetime')),
    'FK_SCORE_OGGETTO_ENTITY_DATETIME_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'datetime')),
    'FK_SCORE_OGGETTO_ENTITY_DATETIME_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'datetime')),
    'FK_SCORE_OGGETTO_ENTITY_DATETIME_OGGETTO_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'decimal')),
    'IDX_ATTRIBUTE_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'decimal')),
    'FK_SCORE_OGGETTO_ENTITY_DECIMAL_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'decimal')),
    'FK_SCORE_OGGETTO_ENTITY_DECIMAL_OGGETTO_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'decimal')),
    'FK_SCORE_OGGETTO_ENTITY_DECIMAL_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'gallery')),
    'IDX_BASE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'gallery')),
    'FK_ATTRIBUTE_GALLERY_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'gallery')),
    'FK_SCORE_CATEGORY_ENTITY_GALLERY_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'gallery')),
    'FK_SCORE_CATEGORY_ENTITY_GALLERY_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'int')),
    'IDX_ATTRIBUTE_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'int')),
    'FK_SCORE_OGGETTO_ENTITY_INT_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'int')),
    'FK_SCORE_OGGETTO_ENTITY_INT_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'int')),
    'FK_SCORE_OGGETTO_ENTITY_INT_OGGETTO_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'text')),
    'IDX_ATTRIBUTE_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'text')),
    'FK_SCORE_OGGETTO_ENTITY_TEXT_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'text')),
    'FK_SCORE_OGGETTO_ENTITY_TEXT_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'text')),
    'FK_SCORE_OGGETTO_ENTITY_TEXT_OGGETTO_ENTITY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'varchar')),
    'IDX_ATTRIBUTE_VALUE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'varchar')),
    'FK_SCORE_OGGETTO_ENTITY_VARCHAR_ATTRIBUTE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'varchar')),
    'FK_SCORE_OGGETTO_ENTITY_VARCHAR_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable(array('score/oggetto', 'varchar')),
    'FK_SCORE_OGGETTO_ENTITY_VARCHAR_OGGETTO_ENTITY'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('score/oggetto') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity ID'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Type ID'
            ),
            'attribute_set_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute Set ID'
            ),
            'type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'default'   => 'simple',
                'comment'   => 'Type ID'
            ),
            'sku' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'SKU'
            ),
            'has_options' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Has Options'
            ),
            'required_options' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Required Options'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Creation Time'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Update Time'
            )
        ),
        'comment' => 'Catalog Oggetto Table'
    ),
    $installer->getTable('score/category') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Entity ID'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity Type ID'
            ),
            'attribute_set_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attriute Set ID'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Parent Category ID'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Creation Time'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Update Time'
            ),
            'path' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Tree Path'
            ),
            'position' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Position'
            ),
            'level' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Tree Level'
            ),
            'children_count' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Child Count'
            )
        ),
        'comment' => 'Catalog Category Table'
    ),
    $installer->getTable('score/category_oggetto') => array(
        'columns' => array(
            'category_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Category ID'
            ),
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            ),
            'position' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Position'
            )
        ),
        'comment' => 'Catalog Oggetto To Category Linkage Table'
    ),
    $installer->getTable('score/category_oggetto_index') => array(
        'columns' => array(
            'category_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Category ID'
            ),
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            ),
            'position' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Position'
            ),
            'is_parent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Parent'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'visibility' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visibility'
            )
        ),
        'comment' => 'Catalog Category Oggetto Index'
    ),
    $installer->getTable('score/compare_item') => array(
        'columns' => array(
            'score_compare_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Compare Item ID'
            ),
            'visitor_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visitor ID'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer ID'
            ),
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Store ID'
            )
        ),
        'comment' => 'Catalog Compare Table'
    ),
    $installer->getTable('score/oggetto_website') => array(
        'columns' => array(
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            )
        ),
        'comment' => 'Catalog Oggetto To Website Linkage Table'
    ),
    $installer->getTable('score/oggetto_enabled_index') => array(
        'columns' => array(
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'visibility' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visibility'
            )
        ),
        'comment' => 'Catalog Oggetto Visibility Index Table'
    ),
    $installer->getTable('score/oggetto_link_type') => array(
        'columns' => array(
            'link_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Link Type ID'
            ),
            'code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Code'
            )
        ),
        'comment' => 'Catalog Oggetto Link Type Table'
    ),
    $installer->getTable('score/oggetto_link') => array(
        'columns' => array(
            'link_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Link ID'
            ),
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            ),
            'linked_oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Linked Oggetto ID'
            ),
            'link_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Link Type ID'
            )
        ),
        'comment' => 'Catalog Oggetto To Oggetto Linkage Table'
    ),
    $installer->getTable('score/oggetto_link_attribute') => array(
        'columns' => array(
            'oggetto_link_attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Oggetto Link Attribute ID'
            ),
            'link_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Link Type ID'
            ),
            'oggetto_link_attribute_code' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Oggetto Link Attribute Code'
            ),
            'data_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 32,
                'nullable'  => false,
                'comment'   => 'Data Type'
            )
        ),
        'comment' => 'Catalog Oggetto Link Attribute Table'
    ),
    $installer->getTable('score/oggetto_link_attribute_decimal') => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'oggetto_link_attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Oggetto Link Attribute ID'
            ),
            'link_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Link ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto Link Decimal Attribute Table'
    ),
    $installer->getTable('score/oggetto_link_attribute_int') => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'oggetto_link_attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Oggetto Link Attribute ID'
            ),
            'link_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Link ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto Link Integer Attribute Table'
    ),
    $installer->getTable('score/oggetto_link_attribute_varchar') => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'oggetto_link_attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto Link Attribute ID'
            ),
            'link_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Link ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto Link Varchar Attribute Table'
    ),
    $installer->getTable('score/oggetto_super_attribute') => array(
        'columns' => array(
            'oggetto_super_attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Oggetto Super Attribute ID'
            ),
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute ID'
            ),
            'position' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Position'
            )
        ),
        'comment' => 'Catalog Oggetto Super Attribute Table'
    ),
    $installer->getTable('score/oggetto_super_attribute_label') => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'oggetto_super_attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto Super Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'use_default' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Use Default Value'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto Super Attribute Label Table'
    ),
    $installer->getTable('score/oggetto_super_attribute_pricing') => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'oggetto_super_attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto Super Attribute ID'
            ),
            'value_index' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Value Index'
            ),
            'is_percent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Is Percent'
            ),
            'pricing_value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Pricing Value'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website ID'
            )
        ),
        'comment' => 'Catalog Oggetto Super Attribute Pricing Table'
    ),
    $installer->getTable('score/oggetto_super_link') => array(
        'columns' => array(
            'link_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Link ID'
            ),
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            ),
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Parent ID'
            )
        ),
        'comment' => 'Catalog Oggetto Super Link Table'
    ),
    $installer->getTable('score/oggetto_attribute_tier_price') => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'all_groups' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Applicable To All Customer Groups'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'qty' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '1.0000',
                'comment'   => 'QTY'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Value'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Website ID'
            )
        ),
        'comment' => 'Catalog Oggetto Tier Price Attribute Backend Table'
    ),
    $installer->getTable('score/oggetto_attribute_media_gallery') => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Attribute ID'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto Media Gallery Attribute Backend Table'
    ),
    $installer->getTable('score/oggetto_attribute_media_gallery_value') => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Value ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'label' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Label'
            ),
            'position' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Position'
            ),
            'disabled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Disabled'
            )
        ),
        'comment' => 'Catalog Oggetto Media Gallery Attribute Value Table'
    ),
    $installer->getTable('score/oggetto_option') => array(
        'columns' => array(
            'option_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option ID'
            ),
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            ),
            'type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'nullable'  => false,
                'comment'   => 'Type'
            ),
            'is_require' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Required'
            ),
            'sku' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'SKU'
            ),
            'max_characters' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Max Characters'
            ),
            'file_extension' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 50,
                'comment'   => 'File Extension'
            ),
            'image_size_x' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Image Size X'
            ),
            'image_size_y' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'Image Size Y'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order'
            )
        ),
        'comment' => 'Catalog Oggetto Option Table'
    ),
    $installer->getTable('score/oggetto_option_price') => array(
        'columns' => array(
            'option_price_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Price ID'
            ),
            'option_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Option ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Price'
            ),
            'price_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 7,
                'nullable'  => false,
                'default'   => 'fixed',
                'comment'   => 'Price Type'
            )
        ),
        'comment' => 'Catalog Oggetto Option Price Table'
    ),
    $installer->getTable('score/oggetto_option_title') => array(
        'columns' => array(
            'option_title_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Title ID'
            ),
            'option_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Option ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Title'
            )
        ),
        'comment' => 'Catalog Oggetto Option Title Table'
    ),
    $installer->getTable('score/oggetto_option_type_value') => array(
        'columns' => array(
            'option_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Type ID'
            ),
            'option_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Option ID'
            ),
            'sku' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 64,
                'comment'   => 'SKU'
            ),
            'sort_order' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Sort Order'
            )
        ),
        'comment' => 'Catalog Oggetto Option Type Value Table'
    ),
    $installer->getTable('score/oggetto_option_type_price') => array(
        'columns' => array(
            'option_type_price_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Type Price ID'
            ),
            'option_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Option Type ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Price'
            ),
            'price_type' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 7,
                'nullable'  => false,
                'default'   => 'fixed',
                'comment'   => 'Price Type'
            )
        ),
        'comment' => 'Catalog Oggetto Option Type Price Table'
    ),
    $installer->getTable('score/oggetto_option_type_title') => array(
        'columns' => array(
            'option_type_title_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Option Type Title ID'
            ),
            'option_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Option Type ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'title' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Title'
            )
        ),
        'comment' => 'Catalog Oggetto Option Type Title Table'
    ),
    $installer->getTable('score/eav_attribute') => array(
        'columns' => array(
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Attribute ID'
            ),
            'frontend_input_renderer' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Frontend Input Renderer'
            ),
            'is_global' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Global'
            ),
            'is_visible' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Visible'
            ),
            'is_searchable' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Searchable'
            ),
            'is_filterable' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Filterable'
            ),
            'is_comparable' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Comparable'
            ),
            'is_visible_on_front' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Visible On Front'
            ),
            'is_html_allowed_on_front' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is HTML Allowed On Front'
            ),
            'is_used_for_price_rules' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Used For Price Rules'
            ),
            'is_filterable_in_search' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Filterable In Search'
            ),
            'used_in_oggetto_listing' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Used In Oggetto Listing'
            ),
            'used_for_sort_by' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Used For Sorting'
            ),
            'is_configurable' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Is Configurable'
            ),
            'apply_to' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Apply To'
            ),
            'is_visible_in_advanced_search' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Visible In Advanced Search'
            ),
            'position' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Position'
            ),
            'is_wysiwyg_enabled' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is WYSIWYG Enabled'
            ),
            'is_used_for_promo_rules' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Used For Promo Rules'
            )
        ),
        'comment' => 'Catalog EAV Attribute Table'
    ),
    $installer->getTable('score/oggetto_relation') => array(
        'columns' => array(
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Parent ID'
            ),
            'child_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Child ID'
            )
        ),
        'comment' => 'Catalog Oggetto Relation Table'
    ),
    $installer->getTable('score/oggetto_index_eav') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto EAV Index Table'
    ),
    $installer->getTable('score/oggetto_index_eav_decimal') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0.0000',
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto EAV Decimal Index Table'
    ),
    $installer->getTable('score/oggetto_index_price') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'tax_class_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Tax Class ID'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ),
            'final_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Final Price'
            ),
            'min_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ),
            'max_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ),
            'tier_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            )
        ),
        'comment' => 'Catalog Oggetto Price Index Table'
    ),
    $installer->getTable('score/oggetto_index_tier_price') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'min_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            )
        ),
        'comment' => 'Catalog Oggetto Tier Price Index Table'
    ),
    $installer->getTable('score/oggetto_index_website') => array(
        'columns' => array(
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'rate' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_FLOAT,
                'default'   => '1',
                'comment'   => 'Rate'
            )
        ),
        'comment' => 'Catalog Oggetto Website Index Table'
    ),
    $installer->getTable('score/oggetto_price_indexer_cfg_option_aggregate_idx') => array(
        'columns' => array(
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Parent ID'
            ),
            'child_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Child ID'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ),
            'tier_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            )
        ),
        'comment' => 'Catalog Oggetto Price Indexer Config Option Aggregate Index '
    ),
    $installer->getTable('score/oggetto_price_indexer_cfg_option_aggregate_tmp') => array(
        'columns' => array(
            'parent_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Parent ID'
            ),
            'child_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Child ID'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ),
            'tier_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            )
        ),
        'comment' => 'Catalog Oggetto Price Indexer Config Option Aggregate Temp Table',
        'engine'  => 'InnoDB'
    ),
    $installer->getTable('score/oggetto_price_indexer_cfg_option_idx') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'min_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ),
            'max_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ),
            'tier_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            )
        ),
        'comment' => 'Catalog Oggetto Price Indexer Config Option Index Table'
    ),
    $installer->getTable('score/oggetto_price_indexer_cfg_option_tmp') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'min_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ),
            'max_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ),
            'tier_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            )
        ),
        'comment' => 'Catalog Oggetto Price Indexer Config Option Temp Table',
        'engine'  => 'InnoDB'
    ),
    $installer->getTable('score/oggetto_price_indexer_final_idx') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'tax_class_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Tax Class ID'
            ),
            'orig_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Original Price'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ),
            'min_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ),
            'max_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ),
            'tier_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            ),
            'base_tier' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tier'
            )
        ),
        'comment' => 'Catalog Oggetto Price Indexer Final Index Table'
    ),
    $installer->getTable('score/oggetto_price_indexer_final_tmp') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'tax_class_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Tax Class ID'
            ),
            'orig_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Original Price'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ),
            'min_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ),
            'max_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ),
            'tier_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            ),
            'base_tier' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Base Tier'
            )
        ),
        'comment' => 'Catalog Oggetto Price Indexer Final Temp Table',
        'engine'  => 'InnoDB'
    ),
    $installer->getTable('score/oggetto_price_indexer_option_idx') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'min_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ),
            'max_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ),
            'tier_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            )
        ),
        'comment' => 'Catalog Oggetto Price Indexer Option Index Table'
    ),
    $installer->getTable('score/oggetto_price_indexer_option_tmp') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'min_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ),
            'max_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ),
            'tier_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            )
        ),
        'comment' => 'Catalog Oggetto Price Indexer Option Temp Table',
        'engine'  => 'InnoDB'
    ),
    $installer->getTable('score/oggetto_price_indexer_option_aggregate_idx') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'option_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Option ID'
            ),
            'min_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ),
            'max_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ),
            'tier_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            )
        ),
        'comment' => 'Catalog Oggetto Price Indexer Option Aggregate Index Table'
    ),
    $installer->getTable('score/oggetto_price_indexer_option_aggregate_tmp') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'option_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Option ID'
            ),
            'min_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ),
            'max_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ),
            'tier_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            )
        ),
        'comment' => 'Catalog Oggetto Price Indexer Option Aggregate Temp Table',
        'engine'  => 'InnoDB'
    ),
    $installer->getTable('score/oggetto_eav_indexer_idx') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto EAV Indexer Index Table'
    ),
    $installer->getTable('score/oggetto_eav_indexer_tmp') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto EAV Indexer Temp Table',
        'engine'  => 'InnoDB'
    ),
    $installer->getTable('score/oggetto_eav_decimal_indexer_idx') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0.0000',
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto EAV Decimal Indexer Index Table'
    ),
    $installer->getTable('score/oggetto_eav_decimal_indexer_tmp') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0.0000',
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto EAV Decimal Indexer Temp Table',
        'engine'  => 'InnoDB'
    ),
    $installer->getTable('score/oggetto_price_indexer_idx') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'tax_class_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Tax Class ID'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ),
            'final_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Final Price'
            ),
            'min_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ),
            'max_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ),
            'tier_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            )
        ),
        'comment' => 'Catalog Oggetto Price Indexer Index Table'
    ),
    $installer->getTable('score/oggetto_price_indexer_tmp') => array(
        'columns' => array(
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Entity ID'
            ),
            'customer_group_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Customer Group ID'
            ),
            'website_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Website ID'
            ),
            'tax_class_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'default'   => '0',
                'comment'   => 'Tax Class ID'
            ),
            'price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Price'
            ),
            'final_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Final Price'
            ),
            'min_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Min Price'
            ),
            'max_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Max Price'
            ),
            'tier_price' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Tier Price'
            )
        ),
        'comment' => 'Catalog Oggetto Price Indexer Temp Table',
        'engine'  => 'InnoDB'
    ),
    $installer->getTable('score/category_oggetto_indexer_idx') => array(
        'columns' => array(
            'category_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Category ID'
            ),
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            ),
            'position' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Position'
            ),
            'is_parent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Parent'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'visibility' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visibility'
            )
        ),
        'comment' => 'Catalog Category Oggetto Indexer Index Table'
    ),
    $installer->getTable('score/category_oggetto_indexer_tmp') => array(
        'columns' => array(
            'category_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Category ID'
            ),
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            ),
            'position' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Position'
            ),
            'is_parent' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Is Parent'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'visibility' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visibility'
            )
        ),
        'comment' => 'Catalog Category Oggetto Indexer Temp Table',
        'engine'  => 'InnoDB'
    ),
    $installer->getTable('score/category_oggetto_enabled_indexer_idx') => array(
        'columns' => array(
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            ),
            'visibility' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visibility'
            )
        ),
        'comment' => 'Catalog Category Oggetto Enabled Indexer Index Table'
    ),
    $installer->getTable('score/category_oggetto_enabled_indexer_tmp') => array(
        'columns' => array(
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            ),
            'visibility' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Visibility'
            )
        ),
        'comment' => 'Catalog Category Oggetto Enabled Indexer Temp Table',
        'engine'  => 'InnoDB'
    ),
    $installer->getTable('score/category_anchor_indexer_idx') => array(
        'columns' => array(
            'category_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Category ID'
            ),
            'path' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Path'
            )
        ),
        'comment' => 'Catalog Category Anchor Indexer Index Table'
    ),
    $installer->getTable('score/category_anchor_indexer_tmp') => array(
        'columns' => array(
            'category_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Category ID'
            ),
            'path' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Path'
            )
        ),
        'comment' => 'Catalog Category Anchor Indexer Temp Table',
        'engine'  => 'InnoDB'
    ),
    $installer->getTable('score/category_anchor_oggettos_indexer_idx') => array(
        'columns' => array(
            'category_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Category ID'
            ),
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            ),
            'position' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Position'
            )
        ),
        'comment' => 'Catalog Category Anchor Oggetto Indexer Index Table'
    ),
    $installer->getTable('score/category_anchor_oggettos_indexer_tmp') => array(
        'columns' => array(
            'category_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Category ID'
            ),
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto ID'
            )
        ),
        'comment' => 'Catalog Category Anchor Oggetto Indexer Temp Table',
        'engine'  => 'InnoDB'
    ),
    $installer->getTable(array('score/oggetto','datetime')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto Datetime Attribute Backend Table'
    ),
    $installer->getTable(array('score/oggetto','decimal')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto Decimal Attribute Backend Table'
    ),
    $installer->getTable(array('score/oggetto','int')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto Integer Attribute Backend Table'
    ),
    $installer->getTable(array('score/oggetto','text')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto Text Attribute Backend Table'
    ),
    $installer->getTable(array('score/oggetto','varchar')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto Varchar Attribute Backend Table'
    ),
    $installer->getTable(array('score/oggetto','gallery')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID'
            ),
            'position' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'comment'   => 'Position'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Oggetto Gallery Attribute Backend Table'
    ),
    $installer->getTable(array('score/category','datetime')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Category Datetime Attribute Backend Table'
    ),
    $installer->getTable(array('score/category','decimal')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 12,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Category Decimal Attribute Backend Table'
    ),
    $installer->getTable(array('score/category','int')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Category Integer Attribute Backend Table'
    ),
    $installer->getTable(array('score/category','text')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '64K',
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Category Text Attribute Backend Table'
    ),
    $installer->getTable(array('score/category','varchar')) => array(
        'columns' => array(
            'value_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Value ID'
            ),
            'entity_type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity Type ID'
            ),
            'attribute_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ),
            'entity_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Entity ID'
            ),
            'value' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Value'
            )
        ),
        'comment' => 'Catalog Category Varchar Attribute Backend Table'
    ),
    $installer->getTable('core/url_rewrite') => array(
        'columns' => array(
            'category_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => true,
                'comment'   => 'Category Id'
            ),
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => true,
                'comment'   => 'Oggetto Id'
            )
        )
    )
);

$installer->getConnection()->modifyTables($tables);

$installer->getConnection()->changeColumn(
    $installer->getTable('score/oggetto_index_website'),
    'date',
    'website_date',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
        'comment'   => 'Website Date'
    )
);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('score/category_anchor_indexer_idx'),
    $installer->getIdxName('score/category_anchor_indexer_idx', array('category_id')),
    array('category_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/category_anchor_indexer_tmp'),
    $installer->getIdxName('score/category_anchor_indexer_tmp', array('category_id')),
    array('category_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/category'),
    $installer->getIdxName('score/category', array('level')),
    array('level')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/category_oggetto'),
    'PRIMARY',
    array('category_id', 'oggetto_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/category_oggetto'),
    $installer->getIdxName('score/category_oggetto', array('oggetto_id')),
    array('oggetto_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/category_oggetto_index'),
    'PRIMARY',
    array('category_id', 'oggetto_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/category_oggetto_index'),
    $installer->getIdxName(
        'score/category_oggetto_index',
        array('oggetto_id', 'store_id', 'category_id', 'visibility')
    ),
    array('oggetto_id', 'store_id', 'category_id', 'visibility')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/category_oggetto_index'),
    $installer->getIdxName(
        'score/category_oggetto_index',
        array('store_id', 'category_id', 'visibility', 'is_parent', 'position')
    ),
    array('store_id', 'category_id', 'visibility', 'is_parent', 'position')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/category_oggetto_index'),
    $installer->getIdxName(
        'score/category_oggetto_index',
        array('oggetto_id', 'store_id', 'category_id', 'visibility')
    ),
    array('oggetto_id', 'store_id', 'category_id', 'visibility')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/category_oggetto_enabled_indexer_idx'),
    $installer->getIdxName('score/category_oggetto_enabled_indexer_idx', array('oggetto_id')),
    array('oggetto_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/category_oggetto_enabled_indexer_tmp'),
    $installer->getIdxName('score/category_oggetto_enabled_indexer_tmp', array('oggetto_id')),
    array('oggetto_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/category_oggetto_indexer_idx'),
    $installer->getIdxName('score/category_oggetto_indexer_idx', array('oggetto_id', 'category_id', 'store_id')),
    array('oggetto_id', 'category_id', 'store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/compare_item'),
    $installer->getIdxName('score/compare_item', array('customer_id')),
    array('customer_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/compare_item'),
    $installer->getIdxName('score/compare_item', array('oggetto_id')),
    array('oggetto_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/compare_item'),
    $installer->getIdxName('score/compare_item', array('visitor_id', 'oggetto_id')),
    array('visitor_id', 'oggetto_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/compare_item'),
    $installer->getIdxName('score/compare_item', array('customer_id', 'oggetto_id')),
    array('customer_id', 'oggetto_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/compare_item'),
    $installer->getIdxName('score/compare_item', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/eav_attribute'),
    $installer->getIdxName('score/eav_attribute', array('used_for_sort_by')),
    array('used_for_sort_by')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/eav_attribute'),
    $installer->getIdxName('score/eav_attribute', array('used_in_oggetto_listing')),
    array('used_in_oggetto_listing')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_enabled_index'),
    $installer->getIdxName('score/oggetto_enabled_index', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_enabled_index'),
    'PRIMARY',
    array('oggetto_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_PRIMARY
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto'),
    $installer->getIdxName('score/oggetto', array('entity_type_id')),
    array('entity_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto'),
    $installer->getIdxName('score/oggetto', array('attribute_set_id')),
    array('attribute_set_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto'),
    $installer->getIdxName('score/oggetto', array('sku')),
    array('sku')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_attribute_media_gallery'),
    $installer->getIdxName('score/oggetto_attribute_media_gallery', array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_attribute_media_gallery'),
    $installer->getIdxName('score/oggetto_attribute_media_gallery', array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_attribute_media_gallery_value'),
    $installer->getIdxName('score/oggetto_attribute_media_gallery_value', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_attribute_tier_price'),
    $installer->getIdxName(
        'score/oggetto_attribute_tier_price',
        array('entity_id', 'all_groups', 'customer_group_id', 'qty', 'website_id')
    ),
    array('entity_id', 'all_groups', 'customer_group_id', 'qty', 'website_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_attribute_tier_price'),
    $installer->getIdxName('score/oggetto_attribute_tier_price', array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_attribute_tier_price'),
    $installer->getIdxName('score/oggetto_attribute_tier_price', array('customer_group_id')),
    array('customer_group_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_attribute_tier_price'),
    $installer->getIdxName('score/oggetto_attribute_tier_price', array('website_id')),
    array('website_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_index_eav'),
    $installer->getIdxName('score/oggetto_index_eav', array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_index_eav'),
    $installer->getIdxName('score/oggetto_index_eav', array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_index_eav'),
    $installer->getIdxName('score/oggetto_index_eav', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_index_eav'),
    $installer->getIdxName('score/oggetto_index_eav', array('value')),
    array('value')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_index_eav_decimal'),
    $installer->getIdxName('score/oggetto_index_eav_decimal', array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_index_eav_decimal'),
    $installer->getIdxName('score/oggetto_index_eav_decimal', array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_index_eav_decimal'),
    $installer->getIdxName('score/oggetto_index_eav_decimal', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_index_eav_decimal'),
    $installer->getIdxName('score/oggetto_index_eav_decimal', array('value')),
    array('value')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_idx'),
    $installer->getIdxName('score/oggetto_eav_decimal_indexer_idx', array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_idx'),
    $installer->getIdxName('score/oggetto_eav_decimal_indexer_idx', array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_idx'),
    $installer->getIdxName('score/oggetto_eav_decimal_indexer_idx', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_idx'),
    $installer->getIdxName('score/oggetto_eav_decimal_indexer_idx', array('value')),
    array('value')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_tmp'),
    $installer->getIdxName('score/oggetto_eav_decimal_indexer_tmp', array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_tmp'),
    $installer->getIdxName('score/oggetto_eav_decimal_indexer_tmp', array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_tmp'),
    $installer->getIdxName('score/oggetto_eav_decimal_indexer_tmp', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_decimal_indexer_tmp'),
    $installer->getIdxName('score/oggetto_eav_decimal_indexer_tmp', array('value')),
    array('value')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_indexer_idx'),
    $installer->getIdxName('score/oggetto_eav_indexer_idx', array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_indexer_idx'),
    $installer->getIdxName('score/oggetto_eav_indexer_idx', array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_indexer_idx'),
    $installer->getIdxName('score/oggetto_eav_indexer_idx', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_indexer_idx'),
    $installer->getIdxName('score/oggetto_eav_indexer_idx', array('value')),
    array('value')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_indexer_tmp'),
    $installer->getIdxName('score/oggetto_eav_indexer_tmp', array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_indexer_tmp'),
    $installer->getIdxName('score/oggetto_eav_indexer_tmp', array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_indexer_tmp'),
    $installer->getIdxName('score/oggetto_eav_indexer_tmp', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_eav_indexer_tmp'),
    $installer->getIdxName('score/oggetto_eav_indexer_tmp', array('value')),
    array('value')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_index_price'),
    $installer->getIdxName('score/oggetto_index_price', array('customer_group_id')),
    array('customer_group_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_index_price'),
    $installer->getIdxName('score/oggetto_index_price', array('website_id')),
    array('website_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_index_price'),
    $installer->getIdxName('score/oggetto_index_price', array('min_price')),
    array('min_price')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_price_indexer_idx'),
    $installer->getIdxName('score/oggetto_price_indexer_idx', array('customer_group_id')),
    array('customer_group_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_price_indexer_idx'),
    $installer->getIdxName('score/oggetto_price_indexer_idx', array('website_id')),
    array('website_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_price_indexer_idx'),
    $installer->getIdxName('score/oggetto_price_indexer_idx', array('min_price')),
    array('min_price')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_price_indexer_tmp'),
    $installer->getIdxName('score/oggetto_price_indexer_tmp', array('customer_group_id')),
    array('customer_group_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_price_indexer_tmp'),
    $installer->getIdxName('score/oggetto_price_indexer_tmp', array('website_id')),
    array('website_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_price_indexer_tmp'),
    $installer->getIdxName('score/oggetto_price_indexer_tmp', array('min_price')),
    array('min_price')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_index_tier_price'),
    $installer->getIdxName('score/oggetto_index_tier_price', array('customer_group_id')),
    array('customer_group_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_index_tier_price'),
    $installer->getIdxName('score/oggetto_index_tier_price', array('website_id')),
    array('website_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_index_website'),
    $installer->getIdxName('score/oggetto_index_website', array('website_date')),
    array('website_date')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_link'),
    $installer->getIdxName(
        'score/oggetto_link',
        array('link_type_id', 'oggetto_id', 'linked_oggetto_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('link_type_id', 'oggetto_id', 'linked_oggetto_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_link'),
    $installer->getIdxName('score/oggetto_link', array('oggetto_id')),
    array('oggetto_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_link'),
    $installer->getIdxName('score/oggetto_link', array('linked_oggetto_id')),
    array('linked_oggetto_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_link'),
    $installer->getIdxName('score/oggetto_link', array('link_type_id')),
    array('link_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_link_attribute'),
    $installer->getIdxName('score/oggetto_link_attribute', array('link_type_id')),
    array('link_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_link_attribute_decimal'),
    $installer->getIdxName(
        'score/oggetto_link_attribute_decimal',
        array('oggetto_link_attribute_id', 'link_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('oggetto_link_attribute_id', 'link_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_link_attribute_decimal'),
    $installer->getIdxName('score/oggetto_link_attribute_decimal', array('oggetto_link_attribute_id')),
    array('oggetto_link_attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_link_attribute_decimal'),
    $installer->getIdxName('score/oggetto_link_attribute_decimal', array('link_id')),
    array('link_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_link_attribute_int'),
    $installer->getIdxName(
        'score/oggetto_link_attribute_int',
        array('oggetto_link_attribute_id', 'link_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('oggetto_link_attribute_id', 'link_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_link_attribute_int'),
    $installer->getIdxName('score/oggetto_link_attribute_int', array('oggetto_link_attribute_id')),
    array('oggetto_link_attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_link_attribute_int'),
    $installer->getIdxName('score/oggetto_link_attribute_int', array('link_id')),
    array('link_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_link_attribute_varchar'),
    $installer->getIdxName(
        'score/oggetto_link_attribute_varchar',
        array('oggetto_link_attribute_id', 'link_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('oggetto_link_attribute_id', 'link_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_link_attribute_varchar'),
    $installer->getIdxName('score/oggetto_link_attribute_varchar', array('oggetto_link_attribute_id')),
    array('oggetto_link_attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_link_attribute_varchar'),
    $installer->getIdxName('score/oggetto_link_attribute_varchar', array('link_id')),
    array('link_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_option'),
    $installer->getIdxName('score/oggetto_option', array('oggetto_id')),
    array('oggetto_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_option_price'),
    $installer->getIdxName(
        'score/oggetto_option_price',
        array('option_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('option_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_option_price'),
    $installer->getIdxName('score/oggetto_option_price', array('option_id')),
    array('option_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_option_price'),
    $installer->getIdxName('score/oggetto_option_price', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_option_title'),
    $installer->getIdxName(
        'score/oggetto_option_title',
        array('option_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('option_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_option_title'),
    $installer->getIdxName('score/oggetto_option_title', array('option_id')),
    array('option_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_option_title'),
    $installer->getIdxName('score/oggetto_option_title', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_option_type_price'),
    $installer->getIdxName(
        'score/oggetto_option_type_price',
        array('option_type_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('option_type_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_option_type_price'),
    $installer->getIdxName('score/oggetto_option_type_price', array('option_type_id')),
    array('option_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_option_type_price'),
    $installer->getIdxName('score/oggetto_option_type_price', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_option_type_title'),
    $installer->getIdxName(
        'score/oggetto_option_type_title',
        array('option_type_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('option_type_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_option_type_title'),
    $installer->getIdxName('score/oggetto_option_type_title', array('option_type_id')),
    array('option_type_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_option_type_title'),
    $installer->getIdxName('score/oggetto_option_type_title', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_option_type_value'),
    $installer->getIdxName('score/oggetto_option_type_value', array('option_id')),
    array('option_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_relation'),
    $installer->getIdxName('score/oggetto_relation', array('child_id')),
    array('child_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_super_attribute'),
    $installer->getIdxName(
        'score/oggetto_super_attribute',
        array('oggetto_id', 'attribute_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('oggetto_id', 'attribute_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_super_attribute'),
    $installer->getIdxName('score/oggetto_super_attribute', array('oggetto_id')),
    array('oggetto_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_super_attribute_label'),
    $installer->getIdxName(
        'score/oggetto_super_attribute_label',
        array('oggetto_super_attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('oggetto_super_attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_super_attribute_label'),
    $installer->getIdxName('score/oggetto_super_attribute_label', array('oggetto_super_attribute_id')),
    array('oggetto_super_attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_super_attribute_label'),
    $installer->getIdxName('score/oggetto_super_attribute_label', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_super_attribute_pricing'),
    $installer->getIdxName(
        'score/oggetto_super_attribute_pricing',
        array('oggetto_super_attribute_id', 'value_index', 'website_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('oggetto_super_attribute_id', 'value_index', 'website_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_super_attribute_pricing'),
    $installer->getIdxName('score/oggetto_super_attribute_pricing', array('oggetto_super_attribute_id')),
    array('oggetto_super_attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_super_attribute_pricing'),
    $installer->getIdxName('score/oggetto_super_attribute_pricing', array('website_id')),
    array('website_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_super_link'),
    $installer->getIdxName(
        'score/oggetto_super_link',
        array('oggetto_id', 'parent_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('oggetto_id', 'parent_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_super_link'),
    $installer->getIdxName('score/oggetto_super_link', array('parent_id')),
    array('parent_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_super_link'),
    $installer->getIdxName('score/oggetto_super_link', array('oggetto_id')),
    array('oggetto_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('score/oggetto_website'),
    $installer->getIdxName('score/oggetto_website', array('website_id')),
    array('website_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'datetime')),
    $installer->getIdxName(
        array('score/category', 'datetime'),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'datetime')),
    $installer->getIdxName(array('score/category', 'datetime'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'datetime')),
    $installer->getIdxName(array('score/category', 'datetime'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'datetime')),
    $installer->getIdxName(array('score/category', 'datetime'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'decimal')),
    $installer->getIdxName(
        array('score/category', 'decimal'),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'decimal')),
    $installer->getIdxName(array('score/category', 'decimal'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'decimal')),
    $installer->getIdxName(array('score/category', 'decimal'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'decimal')),
    $installer->getIdxName(array('score/category', 'decimal'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'int')),
    $installer->getIdxName(
        array('score/category', 'int'),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'int')),
    $installer->getIdxName(array('score/category', 'int'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'int')),
    $installer->getIdxName(array('score/category', 'int'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'int')),
    $installer->getIdxName(array('score/category', 'int'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'text')),
    $installer->getIdxName(
        array('score/category', 'text'),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'text')),
    $installer->getIdxName(array('score/category', 'text'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'text')),
    $installer->getIdxName(array('score/category', 'text'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'text')),
    $installer->getIdxName(array('score/category', 'text'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'varchar')),
    $installer->getIdxName(
        array('score/category', 'varchar'),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'varchar')),
    $installer->getIdxName(array('score/category', 'varchar'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'varchar')),
    $installer->getIdxName(array('score/category', 'varchar'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/category', 'varchar')),
    $installer->getIdxName(array('score/category', 'varchar'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'datetime')),
    $installer->getIdxName(
        array('score/oggetto', 'datetime'),
        array('entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'datetime')),
    $installer->getIdxName(array('score/oggetto', 'datetime'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'datetime')),
    $installer->getIdxName(array('score/oggetto', 'datetime'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'datetime')),
    $installer->getIdxName(array('score/oggetto', 'datetime'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'decimal')),
    $installer->getIdxName(
        array('score/oggetto', 'decimal'),
        array('entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'decimal')),
    $installer->getIdxName(array('score/oggetto', 'decimal'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'decimal')),
    $installer->getIdxName(array('score/oggetto', 'decimal'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'decimal')),
    $installer->getIdxName(array('score/oggetto', 'decimal'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'gallery')),
    $installer->getIdxName(
        array('score/oggetto', 'gallery'),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'gallery')),
    $installer->getIdxName(array('score/oggetto', 'gallery'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'gallery')),
    $installer->getIdxName(array('score/oggetto', 'gallery'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'gallery')),
    $installer->getIdxName(array('score/oggetto', 'gallery'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'int')),
    $installer->getIdxName(
        array('score/oggetto', 'int'),
        array('entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'int')),
    $installer->getIdxName(array('score/oggetto', 'int'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'int')),
    $installer->getIdxName(array('score/oggetto', 'int'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'int')),
    $installer->getIdxName(array('score/oggetto', 'int'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'text')),
    $installer->getIdxName(
        array('score/oggetto', 'text'),
        array('entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'text')),
    $installer->getIdxName(array('score/oggetto', 'text'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'text')),
    $installer->getIdxName(array('score/oggetto', 'text'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'text')),
    $installer->getIdxName(array('score/oggetto', 'text'), array('entity_id')),
    array('entity_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'varchar')),
    $installer->getIdxName(
        array('score/oggetto', 'varchar'),
        array('entity_id', 'attribute_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('entity_id', 'attribute_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'varchar')),
    $installer->getIdxName(array('score/oggetto', 'varchar'), array('attribute_id')),
    array('attribute_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'varchar')),
    $installer->getIdxName(array('score/oggetto', 'varchar'), array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable(array('score/oggetto', 'varchar')),
    $installer->getIdxName(array('score/oggetto', 'varchar'), array('entity_id')),
    array('entity_id')
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/category_oggetto', 'category_id', 'score/category', 'entity_id'),
    $installer->getTable('score/category_oggetto'),
    'category_id',
    $installer->getTable('score/category'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/category_oggetto', 'oggetto_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/category_oggetto'),
    'oggetto_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/category_oggetto_index', 'category_id', 'score/category', 'entity_id'),
    $installer->getTable('score/category_oggetto_index'),
    'category_id',
    $installer->getTable('score/category'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/category_oggetto_index', 'oggetto_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/category_oggetto_index'),
    'oggetto_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/category_oggetto_index', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('score/category_oggetto_index'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/compare_item', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('score/compare_item'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/compare_item', 'oggetto_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/compare_item'),
    'oggetto_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/compare_item', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('score/compare_item'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/eav_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('score/eav_attribute'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_enabled_index', 'oggetto_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_enabled_index'),
    'oggetto_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_enabled_index', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('score/oggetto_enabled_index'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto', 'attribute_set_id', 'eav_attribute_set', 'attribute_set_id'),
    $installer->getTable('score/oggetto'),
    'attribute_set_id',
    $installer->getTable('eav_attribute_set'),
    'attribute_set_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto', 'entity_type_id', 'eav_entity_type', 'entity_type_id'),
    $installer->getTable('score/oggetto'),
    'entity_type_id',
    $installer->getTable('eav_entity_type'),
    'entity_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_attribute_media_gallery', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('score/oggetto_attribute_media_gallery'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_attribute_media_gallery', 'entity_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_attribute_media_gallery'),
    'entity_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'score/oggetto_attribute_media_gallery_value',
        'value_id',
        'score/oggetto_attribute_media_gallery',
        'value_id'
    ),
    $installer->getTable('score/oggetto_attribute_media_gallery_value'),
    'value_id',
    $installer->getTable('score/oggetto_attribute_media_gallery'),
    'value_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_attribute_media_gallery_value', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('score/oggetto_attribute_media_gallery_value'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'score/oggetto_attribute_tier_price',
        'customer_group_id',
        'customer/customer_group',
        'customer_group_id'
    ),
    $installer->getTable('score/oggetto_attribute_tier_price'),
    'customer_group_id',
    $installer->getTable('customer/customer_group'),
    'customer_group_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_attribute_tier_price', 'entity_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_attribute_tier_price'),
    'entity_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_attribute_tier_price', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('score/oggetto_attribute_tier_price'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_index_eav', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('score/oggetto_index_eav'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_index_eav', 'entity_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_index_eav'),
    'entity_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_index_eav', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('score/oggetto_index_eav'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_index_eav_decimal', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('score/oggetto_index_eav_decimal'),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_index_eav_decimal', 'entity_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_index_eav_decimal'),
    'entity_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_index_eav_decimal', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('score/oggetto_index_eav_decimal'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'score/oggetto_index_price',
        'customer_group_id',
        'customer/customer_group',
        'customer_group_id'
    ),
    $installer->getTable('score/oggetto_index_price'),
    'customer_group_id',
    $installer->getTable('customer/customer_group'),
    'customer_group_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_index_price', 'entity_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_index_price'),
    'entity_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_index_price', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('score/oggetto_index_price'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'score/oggetto_index_tier_price',
        'customer_group_id',
        'customer/customer_group',
        'customer_group_id'
    ),
    $installer->getTable('score/oggetto_index_tier_price'),
    'customer_group_id',
    $installer->getTable('customer/customer_group'),
    'customer_group_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_index_tier_price', 'entity_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_index_tier_price'),
    'entity_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_index_tier_price', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('score/oggetto_index_tier_price'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_index_website', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('score/oggetto_index_website'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_link', 'linked_oggetto_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_link'),
    'linked_oggetto_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_link', 'oggetto_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_link'),
    'oggetto_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_link', 'link_type_id', 'score/oggetto_link_type', 'link_type_id'),
    $installer->getTable('score/oggetto_link'),
    'link_type_id',
    $installer->getTable('score/oggetto_link_type'),
    'link_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'score/oggetto_link_attribute',
        'link_type_id',
        'score/oggetto_link_type',
        'link_type_id'
    ),
    $installer->getTable('score/oggetto_link_attribute'),
    'link_type_id',
    $installer->getTable('score/oggetto_link_type'),
    'link_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_link_attribute_decimal', 'link_id', 'score/oggetto_link', 'link_id'),
    $installer->getTable('score/oggetto_link_attribute_decimal'),
    'link_id',
    $installer->getTable('score/oggetto_link'),
    'link_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'score/oggetto_link_attribute_decimal',
        'oggetto_link_attribute_id',
        'score/oggetto_link_attribute',
        'oggetto_link_attribute_id'
    ),
    $installer->getTable('score/oggetto_link_attribute_decimal'),
    'oggetto_link_attribute_id',
    $installer->getTable('score/oggetto_link_attribute'),
    'oggetto_link_attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'score/oggetto_link_attribute_int',
        'oggetto_link_attribute_id',
        'score/oggetto_link_attribute',
        'oggetto_link_attribute_id'
    ),
    $installer->getTable('score/oggetto_link_attribute_int'),
    'oggetto_link_attribute_id',
    $installer->getTable('score/oggetto_link_attribute'),
    'oggetto_link_attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_link_attribute_int', 'link_id', 'score/oggetto_link', 'link_id'),
    $installer->getTable('score/oggetto_link_attribute_int'),
    'link_id',
    $installer->getTable('score/oggetto_link'),
    'link_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_link_attribute_varchar', 'link_id', 'score/oggetto_link', 'link_id'),
    $installer->getTable('score/oggetto_link_attribute_varchar'),
    'link_id',
    $installer->getTable('score/oggetto_link'),
    'link_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'score/oggetto_link_attribute_varchar',
        'oggetto_link_attribute_id',
        'score/oggetto_link_attribute',
        'oggetto_link_attribute_id'
    ),
    $installer->getTable('score/oggetto_link_attribute_varchar'),
    'oggetto_link_attribute_id',
    $installer->getTable('score/oggetto_link_attribute'),
    'oggetto_link_attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_option', 'oggetto_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_option'),
    'oggetto_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_option_price', 'option_id', 'score/oggetto_option', 'option_id'),
    $installer->getTable('score/oggetto_option_price'),
    'option_id',
    $installer->getTable('score/oggetto_option'),
    'option_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_option_price', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('score/oggetto_option_price'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_option_title', 'option_id', 'score/oggetto_option', 'option_id'),
    $installer->getTable('score/oggetto_option_title'),
    'option_id',
    $installer->getTable('score/oggetto_option'),
    'option_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_option_title', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('score/oggetto_option_title'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'score/oggetto_option_type_price',
        'option_type_id',
        'score/oggetto_option_type_value',
        'option_type_id'
    ),
    $installer->getTable('score/oggetto_option_type_price'),
    'option_type_id',
    $installer->getTable('score/oggetto_option_type_value'),
    'option_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_option_type_price', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('score/oggetto_option_type_price'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'score/oggetto_option_type_title',
        'option_type_id',
        'score/oggetto_option_type_value',
        'option_type_id'
    ),
    $installer->getTable('score/oggetto_option_type_title'),
    'option_type_id',
    $installer->getTable('score/oggetto_option_type_value'),
    'option_type_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_option_type_title', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('score/oggetto_option_type_title'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_option_type_value', 'option_id', 'score/oggetto_option', 'option_id'),
    $installer->getTable('score/oggetto_option_type_value'),
    'option_id',
    $installer->getTable('score/oggetto_option'),
    'option_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_relation', 'child_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_relation'),
    'child_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_relation', 'parent_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_relation'),
    'parent_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_super_attribute', 'oggetto_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_super_attribute'),
    'oggetto_id',
    $installer->getTable('score/oggetto'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'score/oggetto_super_attribute_label',
        'oggetto_super_attribute_id',
        'score/oggetto_super_attribute',
        'oggetto_super_attribute_id'
    ),
    $installer->getTable('score/oggetto_super_attribute_label'),
    'oggetto_super_attribute_id',
    $installer->getTable('score/oggetto_super_attribute'),
    'oggetto_super_attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_super_attribute_label', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('score/oggetto_super_attribute_label'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_super_attribute_pricing', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('score/oggetto_super_attribute_pricing'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'score/oggetto_super_attribute_pricing',
        'oggetto_super_attribute_id',
        'score/oggetto_super_attribute',
        'oggetto_super_attribute_id'
    ),
    $installer->getTable('score/oggetto_super_attribute_pricing'),
    'oggetto_super_attribute_id',
    $installer->getTable('score/oggetto_super_attribute'),
    'oggetto_super_attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_super_link', 'parent_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_super_link'),
    'parent_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_super_link', 'oggetto_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_super_link'),
    'oggetto_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_website', 'website_id', 'core/website', 'website_id'),
    $installer->getTable('score/oggetto_website'),
    'website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('score/oggetto_website', 'oggetto_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('score/oggetto_website'),
    'oggetto_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'datetime'), 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(array('score/category', 'datetime')),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'datetime'), 'entity_id', 'score/category', 'entity_id'),
    $installer->getTable(array('score/category', 'datetime')),
    'entity_id',
    $installer->getTable('score/category'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'datetime'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('score/category', 'datetime')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'decimal'), 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(array('score/category', 'decimal')),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'decimal'), 'entity_id', 'score/category', 'entity_id'),
    $installer->getTable(array('score/category', 'decimal')),
    'entity_id',
    $installer->getTable('score/category'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'decimal'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('score/category', 'decimal')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'int'), 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(array('score/category', 'int')),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'int'), 'entity_id', 'score/category', 'entity_id'),
    $installer->getTable(array('score/category', 'int')),
    'entity_id',
    $installer->getTable('score/category'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'int'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('score/category', 'int')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'text'), 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(array('score/category', 'text')),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'text'), 'entity_id', 'score/category', 'entity_id'),
    $installer->getTable(array('score/category', 'text')),
    'entity_id',
    $installer->getTable('score/category'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'text'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('score/category', 'text')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'varchar'), 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(array('score/category', 'varchar')),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'varchar'), 'entity_id', 'score/category', 'entity_id'),
    $installer->getTable(array('score/category', 'varchar')),
    'entity_id',
    $installer->getTable('score/category'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/category', 'varchar'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('score/category', 'varchar')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'datetime'), 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(array('score/oggetto', 'datetime')),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'datetime'), 'entity_id', 'score/oggetto', 'entity_id'),
    $installer->getTable(array('score/oggetto', 'datetime')),
    'entity_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'datetime'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('score/oggetto', 'datetime')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'decimal'), 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(array('score/oggetto', 'decimal')),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'decimal'), 'entity_id', 'score/oggetto', 'entity_id'),
    $installer->getTable(array('score/oggetto', 'decimal')),
    'entity_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'decimal'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('score/oggetto', 'decimal')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'gallery'), 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(array('score/oggetto', 'gallery')),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'gallery'), 'entity_id', 'score/oggetto', 'entity_id'),
    $installer->getTable(array('score/oggetto', 'gallery')),
    'entity_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'gallery'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('score/oggetto', 'gallery')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'int'), 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(array('score/oggetto', 'int')),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'int'), 'entity_id', 'score/oggetto', 'entity_id'),
    $installer->getTable(array('score/oggetto', 'int')),
    'entity_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'int'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('score/oggetto', 'int')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'text'), 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(array('score/oggetto', 'text')),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'text'), 'entity_id', 'score/oggetto', 'entity_id'),
    $installer->getTable(array('score/oggetto', 'text')),
    'entity_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'text'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('score/oggetto', 'text')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'varchar'), 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable(array('score/oggetto', 'varchar')),
    'attribute_id',
    $installer->getTable('eav/attribute'),
    'attribute_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'varchar'), 'entity_id', 'score/oggetto', 'entity_id'),
    $installer->getTable(array('score/oggetto', 'varchar')),
    'entity_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName(array('score/oggetto', 'varchar'), 'store_id', 'core/store', 'store_id'),
    $installer->getTable(array('score/oggetto', 'varchar')),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->endSetup();
$installer->getConnection()->closeConnection();
