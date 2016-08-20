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
 * Score oggetto links collection
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Oggetto_Link_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Oggetto object
     *
     * @var Shaurmalab_Score_Model_Oggetto
     */
    protected $_oggetto;

    /**
     * Oggetto Link model class
     *
     * @var Shaurmalab_Score_Model_Oggetto_Link
     */
    protected $_linkModel;

    /**
     * Oggetto Link Type identifier
     *
     * @var Shaurmalab_Score_Model_Oggetto_Type
     */
    protected $_linkTypeId;

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_link');
    }

    /**
     * Declare link model and initialize type attributes join
     *
     * @param Shaurmalab_Score_Model_Oggetto_Link $linkModel
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Collection
     */
    public function setLinkModel(Shaurmalab_Score_Model_Oggetto_Link $linkModel)
    {
        $this->_linkModel = $linkModel;
        if ($linkModel->hasLinkTypeId()) {
            $this->_linkTypeId = $linkModel->getLinkTypeId();
        }
        return $this;
    }

    /**
     * Retrieve collection link model
     *
     * @return Shaurmalab_Score_Model_Oggetto_Link
     */
    public function getLinkModel()
    {
        return $this->_linkModel;
    }

    /**
     * Initialize collection parent oggetto and add limitation join
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Collection
     */
    public function setOggetto(Shaurmalab_Score_Model_Oggetto $oggetto)
    {
        $this->_oggetto = $oggetto;
        return $this;
    }

    /**
     * Retrieve collection base oggetto object
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggetto()
    {
        return $this->_oggetto;
    }

    /**
     * Add link's type to filter
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Collection
     */
    public function addLinkTypeIdFilter()
    {
        if ($this->_linkTypeId) {
            $this->addFieldToFilter('link_type_id', array('eq' => $this->_linkTypeId));
        }
        return $this;
    }

    /**
     * Add oggetto to filter
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Collection
     */
    public function addOggettoIdFilter()
    {
        if ($this->getOggetto() && $this->getOggetto()->getId()) {
            $this->addFieldToFilter('oggetto_id',  array('eq' => $this->getOggetto()->getId()));
        }
        return $this;
    }

 	/**
     * Add child to filter
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Collection
     */
    public function addChildIdFilter()
    {
       if ($this->getOggetto() && $this->getOggetto()->getId()) {
            $this->addFieldToFilter('linked_oggetto_id',  array('eq' => (int)$this->getOggetto()->getId()));
       }
        return $this;
    }

    /**
     * Join attributes
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Link_Collection
     */
    public function joinAttributes()
    {
        if (!$this->getLinkModel()) {
            return $this;
        }
        $attributes = $this->getLinkModel()->getAttributes();
        $adapter = $this->getConnection();
        foreach ($attributes as $attribute) {
            $table = $this->getLinkModel()->getAttributeTypeTable($attribute['type']);
            $alias = sprintf('link_attribute_%s_%s', $attribute['code'], $attribute['type']);

            $aliasInCondition = $adapter->quoteColumnAs($alias, null);
            $this->getSelect()->joinLeft(
                array($alias => $table),
                $aliasInCondition . '.link_id = main_table.link_id AND '
                    . $aliasInCondition . '.oggetto_link_attribute_id = ' . (int) $attribute['id'],
                array($attribute['code'] => 'value')
            );
        }

        return $this;
    }
}
