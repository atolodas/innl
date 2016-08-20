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

$installer->startSetup();
// fix for sample data 1.2.0
$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_website'),
    'FK_SCORE_OGGETTO_WEBSITE_OGGETTO'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_website'),
    'FK_CATAOLOG_OGGETTO_WEBSITE_WEBSITE'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('score/oggetto_website'),
    'FK_SCORE_OGGETTO_WEBSITE_WEBSITE'
);
$installer->getConnection()->dropKey(
    $installer->getTable('score/oggetto_website'),
    'FK_CATAOLOG_OGGETTO_WEBSITE_WEBSITE'
);
$installer->getConnection()->dropKey(
    $installer->getTable('score/oggetto_website'),
    'FK_SCORE_OGGETTO_WEBSITE_WEBSITE'
);
$installer->getConnection()->addConstraint('FK_SUPER_OGGETTO_ATTRIBUTE_LABEL',
    $installer->getTable('score/oggetto_super_attribute_label'), 'oggetto_super_attribute_id',
    $installer->getTable('score/oggetto_super_attribute'), 'oggetto_super_attribute_id',
    'CASCADE', 'CASCADE', true);
$installer->getConnection()->addConstraint('FK_SUPER_OGGETTO_ATTRIBUTE_PRICING',
    $installer->getTable('score/oggetto_super_attribute_pricing'), 'oggetto_super_attribute_id',
    $installer->getTable('score/oggetto_super_attribute'), 'oggetto_super_attribute_id',
    'CASCADE', 'CASCADE', true);
$installer->getConnection()->addConstraint('FK_SUPER_OGGETTO_LINK_ENTITY',
    $installer->getTable('score/oggetto_super_link'), 'oggetto_id',
    $installer->getTable('score/oggetto'), 'entity_id',
    'CASCADE', 'CASCADE', true);
$installer->getConnection()->addConstraint('FK_SUPER_OGGETTO_LINK_PARENT',
    $installer->getTable('score/oggetto_super_link'), 'parent_id',
    $installer->getTable('score/oggetto'), 'entity_id',
    'CASCADE', 'CASCADE', true);
$installer->getConnection()->addConstraint('FK_SCORE_OGGETTO_WEBSITE_WEBSITE',
    $installer->getTable('score/oggetto_website'), 'website_id',
    $installer->getTable('core/website'), 'website_id',
    'CASCADE', 'CASCADE', true);
$installer->getConnection()->addConstraint('FK_SCORE_WEBSITE_OGGETTO_OGGETTO',
    $installer->getTable('score/oggetto_website'), 'oggetto_id',
    $installer->getTable('score/oggetto'), 'entity_id',
    'CASCADE', 'CASCADE', true);
$installer->endSetup();
