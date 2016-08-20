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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Oggetto inventory data
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Inventory extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('score/oggetto/tab/inventory.phtml');
    }

    public function getBackordersOption()
    {
        if (Mage::helper('score')->isModuleEnabled('Mage_CatalogInventory')) {
            return Mage::getSingleton('cataloginventory/source_backorders')->toOptionArray();
        }

        return array();
    }

    /**
     * Retrieve stock option array
     *
     * @return array
     */
    public function getStockOption()
    {
        if (Mage::helper('score')->isModuleEnabled('Mage_CatalogInventory')) {
            return Mage::getSingleton('cataloginventory/source_stock')->toOptionArray();
        }

        return array();
    }

    /**
     * Return current entity instance
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggetto()
    {
        return Mage::registry('entity');
    }

    /**
     * Retrieve Score Inventory  Stock Item Model
     *
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    public function getStockItem()
    {
        return $this->getOggetto()->getStockItem();
    }

    public function isConfigurable()
    {
        return $this->getOggetto()->isConfigurable();
    }

    public function getFieldValue($field)
    {
        if ($this->getStockItem()) {
            return $this->getStockItem()->getDataUsingMethod($field);
        }

        return Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . $field);
    }

    public function getConfigFieldValue($field)
    {
        if ($this->getStockItem()) {
            if ($this->getStockItem()->getData('use_config_' . $field) == 0) {
                return $this->getStockItem()->getData($field);
            }
        }

        return Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . $field);
    }

    public function getDefaultConfigValue($field)
    {
        return Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . $field);
    }

    /**
     * Is readonly stock
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getOggetto()->getInventoryReadonly();
    }

    public function isNew()
    {
        if ($this->getOggetto()->getId()) {
            return false;
        }
        return true;
    }

    public function getFieldSuffix()
    {
        return 'entity';
    }

    /**
     * Check Whether entity type can have fractional quantity or not
     *
     * @return bool
     */
    public function canUseQtyDecimals()
    {
        return $this->getOggetto()->getTypeInstance()->canUseQtyDecimals();
    }

    /**
     * Check if entity type is virtual
     *
     * @return boolean
     */
    public function isVirtual()
    {
        return $this->getOggetto()->getTypeInstance()->isVirtual();
    }
}
