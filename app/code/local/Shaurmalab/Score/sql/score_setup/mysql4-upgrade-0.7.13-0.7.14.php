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

$installer = $this;
/* @var $installer Shaurmalab_Score_Model_Resource_Eav_Mysql4_Setup */

$mediaAttributeId = (int) $installer->getAttributeId('score_oggetto', 'media_gallery');

$imagesAttributesIds = implode(",", array(
    (int) $installer->getAttributeId('score_oggetto', 'small_image'),
    (int) $installer->getAttributeId('score_oggetto', 'image'),
    (int) $installer->getAttributeId('score_oggetto', 'thumbnail')
));


$installer->startSetup();
$installer->run("
INSERT INTO `{$installer->getTable('score_oggetto_entity_media_gallery')}` (attribute_id, entity_id, value)
    SELECT $mediaAttributeId as attribute_id, entity_id, `value`
        FROM `{$installer->getTable('score_oggetto_entity_gallery')}`
        GROUP BY `value`;

INSERT INTO `{$installer->getTable('score_oggetto_entity_media_gallery')}` (attribute_id, entity_id, value)
    SELECT $mediaAttributeId as attribute_id, entity_id, `value`
        FROM `{$installer->getTable('score_oggetto_entity_varchar')}`
        WHERE attribute_id IN($imagesAttributesIds) AND store_id = 0
        GROUP BY `value`;
");

$installer->endSetup();
