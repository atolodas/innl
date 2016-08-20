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
 * Grouped oggetto type implementation
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Type_Grouped extends Shaurmalab_Score_Model_Oggetto_Type_Abstract
{
    const TYPE_CODE = 'grouped';

    /**
     * Cache key for Associated Oggettos
     *
     * @var string
     */
    protected $_keyAssociatedOggettos   = '_cache_instance_associated_oggettos';

    /**
     * Cache key for Associated Oggetto Ids
     *
     * @var string
     */
    protected $_keyAssociatedOggettoIds = '_cache_instance_associated_oggetto_ids';

    /**
     * Cache key for Status Filters
     *
     * @var string
     */
    protected $_keyStatusFilters        = '_cache_instance_status_filters';

    /**
     * Oggetto is composite properties
     *
     * @var bool
     */
    protected $_isComposite             = true;

    /**
     * Oggetto is configurable
     *
     * @var bool
     */
    protected $_canConfigure            = true;

    /**
     * Return relation info about used oggettos
     *
     * @return Varien_Object Object with information data
     */
    public function getRelationInfo()
    {
        $info = new Varien_Object();
        $info->setTable('score/oggetto_link')
            ->setParentFieldName('oggetto_id')
            ->setChildFieldName('linked_oggetto_id')
            ->setWhere('link_type_id=' . Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_GROUPED);
        return $info;
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param int $parentId
     * @param bool $required
     * @return array
     */
    public function getChildrenIds($parentId, $required = true)
    {
        return Mage::getResourceSingleton('score/oggetto_link')
            ->getChildrenIds($parentId,
                Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_GROUPED);
    }

    /**
     * Retrieve parent ids array by requered child
     *
     * @param int|array $childId
     * @return array
     */
    public function getParentIdsByChild($childId)
    {
        return Mage::getResourceSingleton('score/oggetto_link')
            ->getParentIdsByChild($childId,
                Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_GROUPED);
    }

    /**
     * Retrieve array of associated oggettos
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getAssociatedOggettos($oggetto = null)
    {
        if (!$this->getOggetto($oggetto)->hasData($this->_keyAssociatedOggettos)) {
            $associatedOggettos = array();

            if (!Mage::app()->getStore()->isAdmin()) {
                $this->setSaleableStatus($oggetto);
            }

            $collection = $this->getAssociatedOggettoCollection($oggetto)
                ->addAttributeToSelect('*')
                ->addFilterByRequiredOptions()
                ->setPositionOrder()
                ->addStoreFilter($this->getStoreFilter($oggetto))
                ->addAttributeToFilter('status', array('in' => $this->getStatusFilters($oggetto)));

            foreach ($collection as $item) {
                $associatedOggettos[] = $item;
            }

            $this->getOggetto($oggetto)->setData($this->_keyAssociatedOggettos, $associatedOggettos);
        }
        return $this->getOggetto($oggetto)->getData($this->_keyAssociatedOggettos);
    }

    /**
     * Add status filter to collection
     *
     * @param  int $status
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Oggetto_Type_Grouped
     */
    public function addStatusFilter($status, $oggetto = null)
    {
        $statusFilters = $this->getOggetto($oggetto)->getData($this->_keyStatusFilters);
        if (!is_array($statusFilters)) {
            $statusFilters = array();
        }

        $statusFilters[] = $status;
        $this->getOggetto($oggetto)->setData($this->_keyStatusFilters, $statusFilters);

        return $this;
    }

    /**
     * Set only saleable filter
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Oggetto_Type_Grouped
     */
    public function setSaleableStatus($oggetto = null)
    {
        $this->getOggetto($oggetto)->setData($this->_keyStatusFilters,
            Mage::getSingleton('score/oggetto_status')->getSaleableStatusIds());
        return $this;
    }

    /**
     * Return all assigned status filters
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getStatusFilters($oggetto = null)
    {
        if (!$this->getOggetto($oggetto)->hasData($this->_keyStatusFilters)) {
            return array(
                Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED,
                Shaurmalab_Score_Model_Oggetto_Status::STATUS_DISABLED
            );
        }
        return $this->getOggetto($oggetto)->getData($this->_keyStatusFilters);
    }

    /**
     * Retrieve related oggettos identifiers
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getAssociatedOggettoIds($oggetto = null)
    {
        if (!$this->getOggetto($oggetto)->hasData($this->_keyAssociatedOggettoIds)) {
            $associatedOggettoIds = array();
            foreach ($this->getAssociatedOggettos($oggetto) as $item) {
                $associatedOggettoIds[] = $item->getId();
            }
            $this->getOggetto($oggetto)->setData($this->_keyAssociatedOggettoIds, $associatedOggettoIds);
        }
        return $this->getOggetto($oggetto)->getData($this->_keyAssociatedOggettoIds);
    }

    /**
     * Retrieve collection of associated oggettos
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Link_Oggetto_Collection
     */
    public function getAssociatedOggettoCollection($oggetto = null)
    {
        $collection = $this->getOggetto($oggetto)->getLinkInstance()->useGroupedLinks()
            ->getOggettoCollection()
            ->setFlag('require_stock_items', true)
            ->setFlag('oggetto_children', true)
            ->setIsStrongMode();
        $collection->setOggetto($this->getOggetto($oggetto));
        return $collection;
    }

    /**
     * Check is oggetto available for sale
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return bool
     */
    public function isSalable($oggetto = null)
    {
        $salable = parent::isSalable($oggetto);
        if (!is_null($salable)) {
            return $salable;
        }

        $salable = false;
        foreach ($this->getAssociatedOggettos($oggetto) as $associatedOggetto) {
            $salable = $salable || $associatedOggetto->isSalable();
        }
        return $salable;
    }

    /**
     * Save type related data
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Oggetto_Type_Grouped
     */
    public function save($oggetto = null)
    {
        parent::save($oggetto);
        $this->getOggetto($oggetto)->getLinkInstance()->saveGroupedLinks($this->getOggetto($oggetto));
        return $this;
    }

    /**
     * Prepare oggetto and its configuration to be added to some oggettos list.
     * Perform standard preparation process and add logic specific to Grouped oggetto type.
     *
     * @param Varien_Object $buyRequest
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareOggetto(Varien_Object $buyRequest, $oggetto, $processMode)
    {
        $oggetto = $this->getOggetto($oggetto);
        $oggettosInfo = $buyRequest->getSuperGroup();
        $isStrictProcessMode = $this->_isStrictProcessMode($processMode);

        if (!$isStrictProcessMode || (!empty($oggettosInfo) && is_array($oggettosInfo))) {
            $oggettos = array();
            $associatedOggettosInfo = array();
            $associatedOggettos = $this->getAssociatedOggettos($oggetto);
            if ($associatedOggettos || !$isStrictProcessMode) {
                foreach ($associatedOggettos as $subOggetto) {
                    $subOggettoId = $subOggetto->getId();
                    if(isset($oggettosInfo[$subOggettoId])) {
                        $qty = $oggettosInfo[$subOggettoId];
                        if (!empty($qty) && is_numeric($qty)) {

                            $_result = $subOggetto->getTypeInstance(true)
                                ->_prepareOggetto($buyRequest, $subOggetto, $processMode);
                            if (is_string($_result) && !is_array($_result)) {
                                return $_result;
                            }

                            if (!isset($_result[0])) {
                                return Mage::helper('checkout')->__('Cannot process the item.');
                            }

                            if ($isStrictProcessMode) {
                                $_result[0]->setCartQty($qty);
                                $_result[0]->addCustomOption('oggetto_type', self::TYPE_CODE, $oggetto);
                                $_result[0]->addCustomOption('info_buyRequest',
                                    serialize(array(
                                        'super_oggetto_config' => array(
                                            'oggetto_type'  => self::TYPE_CODE,
                                            'oggetto_id'    => $oggetto->getId()
                                        )
                                    ))
                                );
                                $oggettos[] = $_result[0];
                            } else {
                                $associatedOggettosInfo[] = array($subOggettoId => $qty);
                                $oggetto->addCustomOption('associated_oggetto_' . $subOggettoId, $qty);
                            }
                        }
                    }
                }
            }

            if (!$isStrictProcessMode || count($associatedOggettosInfo)) {
                $oggetto->addCustomOption('oggetto_type', self::TYPE_CODE, $oggetto);
                $oggetto->addCustomOption('info_buyRequest', serialize($buyRequest->getData()));

                $oggettos[] = $oggetto;
            }

            if (count($oggettos)) {
                return $oggettos;
            }
        }

        return Mage::helper('score')->__('Please specify the quantity of oggetto(s).');
    }

    /**
     * Retrieve oggettos divided into groups required to purchase
     * At least one oggetto in each group has to be purchased
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getOggettosToPurchaseByReqGroups($oggetto = null)
    {
        $oggetto = $this->getOggetto($oggetto);
        return array($this->getAssociatedOggettos($oggetto));
    }

    /**
     * Prepare selected qty for grouped oggetto's options
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @param  Varien_Object $buyRequest
     * @return array
     */
    public function processBuyRequest($oggetto, $buyRequest)
    {
        $superGroup = $buyRequest->getSuperGroup();
        $superGroup = (is_array($superGroup)) ? array_filter($superGroup, 'intval') : array();

        $options = array('super_group' => $superGroup);

        return $options;
    }
}
