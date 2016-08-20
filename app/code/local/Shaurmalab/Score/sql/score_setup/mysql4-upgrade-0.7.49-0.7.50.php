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

/* @var $this Shaurmalab_Score_Model_Resource_Eav_Mysql4_Setup */
$this->startSetup();

// get options_container attribute and update its value to 'container1' for configurable oggettos
$attribute = $this->getAttribute('score_oggetto', 'options_container');
if (!empty($attribute['attribute_id'])) {
    $this->run("
        UPDATE {$this->getTable('score_oggetto_entity_varchar')}
        SET value = 'container1'
        WHERE
            entity_id IN (SELECT entity_id FROM {$this->getTable('score_oggetto_entity')} WHERE type_id='configurable')
            AND attribute_id={$attribute['attribute_id']}
    ");
}

$this->endSetup();
