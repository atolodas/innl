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


/**
 * Score Comapare Oggettos Sidebar Block
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Oggetto_Compare_Sidebar extends Shaurmalab_Score_Block_Oggetto_Compare_Abstract
{
    /**
     * Compare Oggettos Collection
     *
     * @var null|Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Compare_Item_Collection
     */
    protected $_itemsCollection = null;

    /**
     * Initialize block
     *
     */
    protected function _construct()
    {
        $this->setId('compare');
    }

    /**
     * Retrieve Compare Oggettos Collection
     *
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Compare_Item_Collection
     */
    public function getItems()
    {
        if ($this->_itemsCollection) {
            return $this->_itemsCollection;
        }
        return $this->_getHelper()->getItemCollection();
    }

    /**
     * Set Compare Oggettos Collection
     *
     * @param Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Compare_Item_Collection $collection
     * @return Shaurmalab_Score_Block_Oggetto_Compare_Sidebar
     */
    public function setItems($collection)
    {
        $this->_itemsCollection = $collection;
        return $this;
    }

    /**
     * Retrieve compare oggetto helper
     *
     * @return Shaurmalab_Score_Helper_Oggetto_Compare
     */
    public function getCompareOggettoHelper()
    {
        return $this->_getHelper();
    }

    /**
     * Retrieve Clean Compared Items URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        return $this->_getHelper()->getClearListUrl();
    }

    /**
     * Retrieve Full Compare page URL
     *
     * @return string
     */
    public function getCompareUrl()
    {
        return $this->_getHelper()->getListUrl();
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     */
    public function getCacheTags()
    {
        $compareItem = Mage::getModel('score/oggetto_compare_item');
        foreach ($this->getItems() as $oggetto) {
            $this->addModelTags($oggetto);
            $this->addModelTags(
                $compareItem->setId($oggetto->getScoreCompareItemId())
            );
        }
        return parent::getCacheTags();
    }
}
