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

/* @var $installer Shaurmalab_Score_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

// Create Root Score Node
Mage::getModel('score/category')
    ->load(1)
    ->setId(1)
    ->setStoreId(0)
    ->setPath(1)
    ->setLevel(0)
    ->setPosition(0)
    ->setChildrenCount(0)
    ->setName('Root Catalog')
    ->setInitialSetupFlag(true)
    ->save();

/* @var $category Shaurmalab_Score_Model_Category */
$category = Mage::getModel('score/category');

$category->setStoreId(0)
    ->setName('Default Category')
    ->setDisplayMode('OGGETTOS')
    ->setAttributeSetId($category->getDefaultAttributeSetId())
    ->setIsActive(1)
    ->setPath('1')
    ->setInitialSetupFlag(true)
    ->save();

$installer->setConfigData(Shaurmalab_Score_Helper_Category::XML_PATH_CATEGORY_ROOT_ID, $category->getId());

$installer->addAttributeGroup(Shaurmalab_Score_Model_Oggetto::ENTITY, 'Default', 'Design', 6);

$entityTypeId     = $installer->getEntityTypeId(Shaurmalab_Score_Model_Category::ENTITY);
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

// update General Group
//$installer->updateAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'attribute_group_name', 'General Information');
$installer->updateAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'sort_order', '10');

$groups = array(
    'display'   => array(
        'name'  => 'Display Settings',
        'sort'  => 20,
        'id'    => null
    ),
    'design'    => array(
        'name'  => 'Custom Design',
        'sort'  => 30,
        'id'    => null
    )
);

foreach ($groups as $k => $groupProp) {
    $installer->addAttributeGroup($entityTypeId, $attributeSetId, $groupProp['name'], $groupProp['sort']);
    $groups[$k]['id'] = $installer->getAttributeGroupId($entityTypeId, $attributeSetId, $groupProp['name']);
}

// update attributes group and sort
$attributes = array(
    'custom_design'         => array(
        'group' => 'design',
        'sort'  => 10
    ),
//    'custom_design_apply'   => array(
//        'group' => 'design',
//        'sort'  => 20
//    ),
    'custom_design_from'    => array(
        'group' => 'design',
        'sort'  => 30
    ),
    'custom_design_to'      => array(
        'group' => 'design',
        'sort'  => 40
    ),
    'page_layout'           => array(
        'group' => 'design',
        'sort'  => 50
    ),
    'custom_layout_update'  => array(
        'group' => 'design',
        'sort'  => 60
    ),
    'display_mode'          => array(
        'group' => 'display',
        'sort'  => 10
    ),
    'landing_page'          => array(
        'group' => 'display',
        'sort'  => 20
    ),
    'is_anchor'             => array(
        'group' => 'display',
        'sort'  => 30
    ),
    'available_sort_by'     => array(
        'group' => 'display',
        'sort'  => 40
    ),
    'default_sort_by'       => array(
        'group' => 'display',
        'sort'  => 50
    ),
);

foreach ($attributes as $attributeCode => $attributeProp) {
    $installer->addAttributeToGroup(
        $entityTypeId,
        $attributeSetId,
        $groups[$attributeProp['group']]['id'],
        $attributeCode,
        $attributeProp['sort']
    );
}

/**
 * Install oggetto link types
 */
$data = array(
    array(
        'link_type_id'  => Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_RELATED,
        'code'          => 'relation'
    ),
    array(
        'link_type_id'  => Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_GROUPED,
        'code'  => 'super'
    ),
    array(
        'link_type_id'  => Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_UPSELL,
        'code'  => 'up_sell'
    ),
    array(
        'link_type_id'  => Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_CROSSSELL,
        'code'  => 'cross_sell'
    ),
);

foreach ($data as $bind) {
    $installer->getConnection()->insertForce($installer->getTable('score/oggetto_link_type'), $bind);
}

/**
 * install oggetto link attributes
 */
$data = array(
    array(
        'link_type_id'                  => Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_RELATED,
        'oggetto_link_attribute_code'   => 'position',
        'data_type'                     => 'int'
    ),
    array(
        'link_type_id'                  => Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_GROUPED,
        'oggetto_link_attribute_code'   => 'position',
        'data_type'                     => 'int'
    ),
    array(
        'link_type_id'                  => Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_GROUPED,
        'oggetto_link_attribute_code'   => 'qty',
        'data_type'                     => 'decimal'
    ),
    array(
        'link_type_id'                  => Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_UPSELL,
        'oggetto_link_attribute_code'   => 'position',
        'data_type'                     => 'int'
    ),
    array(
        'link_type_id'                  => Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_CROSSSELL,
        'oggetto_link_attribute_code'   => 'position',
        'data_type'                     => 'int'
    ),
);

$installer->getConnection()->insertMultiple($installer->getTable('score/oggetto_link_attribute'), $data);

/**
 * Remove Score specified attribute options (columns) from eav/attribute table
 *
 */
$describe = $installer->getConnection()->describeTable($installer->getTable('score/eav_attribute'));
foreach ($describe as $columnData) {
    if ($columnData['COLUMN_NAME'] == 'attribute_id') {
        continue;
    }
    $installer->getConnection()->dropColumn($installer->getTable('eav/attribute'), $columnData['COLUMN_NAME']);
}

