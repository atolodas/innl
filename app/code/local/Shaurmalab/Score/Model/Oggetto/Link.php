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
 * Score oggetto link model
 *
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Link _getResource()
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Link getResource()
 * @method int getOggettoId()
 * @method Shaurmalab_Score_Model_Oggetto_Link setOggettoId(int $value)
 * @method int getLinkedOggettoId()
 * @method Shaurmalab_Score_Model_Oggetto_Link setLinkedOggettoId(int $value)
 * @method int getLinkTypeId()
 * @method Shaurmalab_Score_Model_Oggetto_Link setLinkTypeId(int $value)
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Link extends Mage_Core_Model_Abstract
{
    const LINK_TYPE_RELATED     = 1;
    const LINK_TYPE_GROUPED     = 3;
    const LINK_TYPE_UPSELL      = 4;
    const LINK_TYPE_CROSSSELL   = 5;

    protected $_attributeCollection = null;

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_link');
    }

    public function useRelatedLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_RELATED);
        return $this;
    }

    public function useGroupedLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_GROUPED);
        return $this;
    }

    public function useUpSellLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_UPSELL);
        return $this;
    }

    /**
     * @return Shaurmalab_Score_Model_Oggetto_Link
     */
    public function useCrossSellLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_CROSSSELL);
        return $this;
    }

    /**
     * Retrieve table name for attribute type
     *
     * @param   string $type
     * @return  string
     */
    public function getAttributeTypeTable($type)
    {
        return $this->_getResource()->getAttributeTypeTable($type);
    }

    /**
     * Retrieve linked oggetto collection
     */
    public function getOggettoCollection()
    {
        $collection = Mage::getResourceModel('score/oggetto_link_oggetto_collection')
            ->setLinkModel($this);
        return $collection;
    }

    /**
     * Retrieve link collection
     */
    public function getLinkCollection()
    {
        $collection = Mage::getResourceModel('score/oggetto_link_collection')
            ->setLinkModel($this);
        return $collection;
    }

    public function getAttributes($type=null)
    {
        if (is_null($type)) {
            $type = $this->getLinkTypeId();
        }
        return $this->_getResource()->getAttributesByType($type);
    }

    /**
     * Save data for oggetto relations
     *
     * @param   Shaurmalab_Score_Model_Oggetto $oggetto
     * @return  Shaurmalab_Score_Model_Oggetto_Link
     */
    public function saveOggettoRelations($oggetto)
    {
        $data = $oggetto->getRelatedLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveOggettoLinks($oggetto, $data, self::LINK_TYPE_RELATED);
        }
        $data = $oggetto->getUpSellLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveOggettoLinks($oggetto, $data, self::LINK_TYPE_UPSELL);
        }
        $data = $oggetto->getCrossSellLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveOggettoLinks($oggetto, $data, self::LINK_TYPE_CROSSSELL);
        }
        return $this;
    }

    /**
     * Save grouped oggetto relation links
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Oggetto_Link
     */
    public function saveGroupedLinks($oggetto)
    {
        $data = $oggetto->getGroupedLinkData();
        if (!is_null($data)) {
            $this->_getResource()->saveGroupedLinks($oggetto, $data, self::LINK_TYPE_GROUPED);
        }
        return $this;
    }
}
