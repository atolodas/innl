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
try {
    $installer->run("
        ALTER TABLE `{$installer->getTable('score_oggetto_website')}` ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

        delete from `{$installer->getTable('score_oggetto_website')}` where oggetto_id not in (select entity_id from score_oggetto_entity);
        delete from `{$installer->getTable('score_oggetto_website')}` where website_id not in (select website_id from core_website);

        ALTER TABLE `{$installer->getTable('score_oggetto_website')}` DROP INDEX `FK_SCORE_OGGETTO_WEBSITE_WEBSITE`,
            ADD CONSTRAINT `FK_SCORE_OGGETTO_WEBSITE_OGGETTO` FOREIGN KEY `FK_SCORE_OGGETTO_WEBSITE_OGGETTO` (`oggetto_id`)
             REFERENCES `{$installer->getTable('score_oggetto_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ADD CONSTRAINT `FK_CATAOLOG_OGGETTO_WEBSITE_WEBSITE` FOREIGN KEY `FK_CATAOLOG_OGGETTO_WEBSITE_WEBSITE` (`website_id`)
             REFERENCES `{$installer->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
            ROW_FORMAT = FIXED;
    ");
} catch (Exception $e) {
}
$installer->endSetup();
