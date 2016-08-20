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
 * Oggetto Compare List Model
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Compare_List extends Varien_Object
{
    /**
     * Add oggetto to Compare List
     *
     * @param int|Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Oggetto_Compare_List
     */
    public function addOggetto($oggetto)
    {
        /* @var $item Shaurmalab_Score_Model_Oggetto_Compare_Item */
        $item = Mage::getModel('score/oggetto_compare_item');
        $this->_addVisitorToItem($item);
        $item->loadByOggetto($oggetto);

        if (!$item->getId()) {
            $item->addOggettoData($oggetto);
            $item->save();
        }

        return $this;
    }

    /**
     * Add oggettos to compare list
     *
     * @param array $oggettoIds
     * @return Shaurmalab_Score_Model_Oggetto_Compare_List
     */
    public function addOggettos($oggettoIds)
    {
        if (is_array($oggettoIds)) {
            foreach ($oggettoIds as $oggettoId) {
                $this->addOggetto($oggettoId);
            }
        }
        return $this;
    }

    /**
     * Retrieve Compare Items Collection
     *
     * @return oggetto_compare_item_collection
     */
    public function getItemCollection()
    {
        return Mage::getResourceModel('score/oggetto_compare_item_collection');
    }

    /**
     * Remove oggetto from compare list
     *
     * @param int|Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Oggetto_Compare_List
     */
    public function removeOggetto($oggetto)
    {
        /* @var $item Shaurmalab_Score_Model_Oggetto_Compare_Item */
        $item = Mage::getModel('score/oggetto_compare_item');
        $this->_addVisitorToItem($item);
        $item->loadByOggetto($oggetto);

        if ($item->getId()) {
            $item->delete();
        }

        return $this;
    }

    /**
     * Add visitor and customer data to compare item
     *
     * @param Shaurmalab_Score_Model_Oggetto_Compare_Item $item
     * @return Shaurmalab_Score_Model_Oggetto_Compare_List
     */
    protected function _addVisitorToItem($item)
    {
        $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
        }

        return $this;
    }

    /**
     * Check has compare items by visitor/customer
     *
     * @param int $customerId
     * @param int $visitorId
     * @return bool
     */
    public function hasItems($customerId, $visitorId)
    {
        return Mage::getResourceSingleton('score/oggetto_compare_item')
            ->getCount($customerId, $visitorId);
    }
}
