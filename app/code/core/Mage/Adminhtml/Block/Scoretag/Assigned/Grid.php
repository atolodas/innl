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
 * Adminhtml assigned oggettos grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Scoretag_Assigned_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_currentScoretagModel;

    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_currentScoretagModel = Mage::registry('current_scoretag');
        $this->setId('scoretag_assigned_oggetto_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        if ($this->_getScoretagId()) {
            $this->setDefaultFilter(array('in_oggettos'=>1));
        }
    }

    /**
     * Scoretag ID getter
     *
     * @return int
     */
    protected function _getScoretagId()
    {
        return $this->_currentScoretagModel->getId();
    }

    /**
     * Store getter
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     * Add filter to grid columns
     *
     * @param mixed $column
     * @return Mage_Adminhtml_Block_Scoretag_Assigned_Grid
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in oggetto flag
        if ($column->getId() == 'in_oggettos') {
            $oggettoIds = $this->_getSelectedOggettos();
            if (empty($oggettoIds)) {
                $oggettoIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$oggettoIds));
            } else {
                if($oggettoIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$oggettoIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Retrieve Oggettos Collection
     *
     * @return Mage_Adminhtml_Block_Scoretag_Assigned_Grid
     */
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('score/oggetto')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            //->addAttributeToFilter('status', array(''))
            ->joinField('qty',
                'scoreinventory/stock_item',
                'qty',
                'oggetto_id=entity_id',
                '{{table}}.stock_id=1',
                'left');

        if ($store->getId()) {
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'score_oggetto/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'score_oggetto/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'score_oggetto/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'score_oggetto/price', 'entity_id', null, 'left', $store->getId());
        } else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }

        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    /**
     * Prepeare columns for grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_oggettos', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'field_name'        => 'in_oggettos',
            'values'            => $this->_getSelectedOggettos(),
            'align'             => 'center',
            'index'             => 'entity_id'
        ));

        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('score')->__('ID'),
                'width' => 50,
                'sortable'  => true,
                'type'  => 'number',
                'index' => 'entity_id',
        ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('score')->__('Name'),
                'index' => 'name',
        ));

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('score')->__('Name in %s', $store->getName()),
                    'index' => 'custom_name',
            ));
        }

        $this->addColumn('type',
            array(
                'header'    => Mage::helper('score')->__('Type'),
                'width'     => 100,
                'index'     => 'type_id',
                'type'      => 'options',
                'options'   => Mage::getSingleton('score/oggetto_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('score/oggetto')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'    => Mage::helper('score')->__('Attrib. Set Name'),
                'width'     => 100,
                'index'     => 'attribute_set_id',
                'type'      => 'options',
                'options'   => $sets,
        ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('score')->__('SKU'),
                'width' => 80,
                'index' => 'sku',
        ));

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header'        => Mage::helper('score')->__('Price'),
                'type'          => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index'         => 'price',
        ));

        $this->addColumn('visibility',
            array(
                'header'    => Mage::helper('score')->__('Visibility'),
                'width'     => 100,
                'index'     => 'visibility',
                'type'      => 'options',
                'options'   => Mage::getModel('score/oggetto_visibility')->getOptionArray(),
        ));

        $this->addColumn('status',
            array(
                'header'    => Mage::helper('score')->__('Status'),
                'width'     => 70,
                'index'     => 'status',
                'type'      => 'options',
                'options'   => Mage::getSingleton('score/oggetto_status')->getOptionArray(),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve related oggettos
     *
     * @return array
     */
    protected function _getSelectedOggettos()
    {
        $oggettos = $this->getRequest()->getPost('assigned_oggettos', null);
        if (!is_array($oggettos)) {
            $oggettos = $this->getRelatedOggettos();
        }
        return $oggettos;
    }

    /**
     * Retrieve Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/assignedGridOnly', array('_current' => true));
    }

    /**
     * Retrieve related oggettos
     *
     * @return array
     */
    public function getRelatedOggettos()
    {
        return $this->_currentScoretagModel
            ->setStatusFilter(Mage_Scoretag_Model_Scoretag::STATUS_APPROVED)
            ->getRelatedOggettoIds();
    }
}
