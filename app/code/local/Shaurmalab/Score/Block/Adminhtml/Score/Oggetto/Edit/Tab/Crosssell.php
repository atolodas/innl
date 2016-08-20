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
 * Crossell entitys admin grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Crosssell extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('cross_sell_entity_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->_getOggetto()->getId()) {
            $this->setDefaultFilter(array('in_entitys'=>1));
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

    /**
     * Retirve currently edited entity model
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    protected function _getOggetto()
    {
        return Mage::registry('current_entity');
    }

    /**
     * Add filter
     *
     * @param object $column
     * @return Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Crosssell
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in entity flag
        if ($column->getId() == 'in_entitys') {
            $entityIds = $this->_getSelectedOggettos();
            if (empty($entityIds)) {
                $entityIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$entityIds));
            } else {
                if($entityIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$entityIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Shaurmalab_Score_Model_Resource_Oggetto_Link_Oggetto_Collection */
        $collection = Mage::getModel('score/oggetto_link')->useCrossSellLinks()
            ->getOggettoCollection()
            ->setOggetto($this->_getOggetto())
            ->addAttributeToSelect('*');

        if ($this->isReadonly()) {
            $entityIds = $this->_getSelectedOggettos();
            if (empty($entityIds)) {
                $entityIds = array(0);
            }
            $collection->addFieldToFilter('entity_id', array('in' => $entityIds));
        }


        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_getOggetto()->getCrosssellReadonly();
    }

    /**
     * Add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        if (!$this->isReadonly()) {
            $this->addColumn('in_entitys', array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_entitys',
                'values'            => $this->_getSelectedOggettos(),
                'align'             => 'center',
                'index'             => 'entity_id'
            ));
        }

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('score')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'entity_id'
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('score')->__('Name'),
            'index'     => 'name'
        ));

        $this->addColumn('type', array(
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

        $this->addColumn('set_name', array(
            'header'    => Mage::helper('score')->__('Attrib. Set Name'),
            'width'     => 130,
            'index'     => 'attribute_set_id',
            'type'      => 'options',
            'options'   => $sets,
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('score')->__('Status'),
            'width'     => 90,
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('score/oggetto_status')->getOptionArray(),
        ));

        $this->addColumn('visibility', array(
            'header'    => Mage::helper('score')->__('Visibility'),
            'width'     => 90,
            'index'     => 'visibility',
            'type'      => 'options',
            'options'   => Mage::getSingleton('score/oggetto_visibility')->getOptionArray(),
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('score')->__('SKU'),
            'width'     => 80,
            'index'     => 'sku'
        ));

        $this->addColumn('price', array(
            'header'        => Mage::helper('score')->__('Price'),
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'         => 'price'
        ));


        $this->addColumn('position', array(
            'header'            => Mage::helper('score')->__('Position'),
            'name'              => 'position',
            'width'             => 60,
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
            'editable'          => !$this->isReadonly(),
            'edit_only'         => !$this->_getOggetto()->getId()
        ));

        return parent::_prepareColumns();
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/crosssellGrid', array('_current'=>true));
    }

    /**
     * Retrieve selected crosssell entitys
     *
     * @return array
     */
    protected function _getSelectedOggettos()
    {
        $entitys = $this->getOggettosCrossSell();
        if (!is_array($entitys)) {
            $entitys = array_keys($this->getSelectedCrossSellOggettos());
        }
        return $entitys;
    }

    /**
     * Retrieve crosssell entitys
     *
     * @return array
     */
    public function getSelectedCrossSellOggettos()
    {
        $entitys = array();
        foreach (Mage::registry('current_entity')->getCrossSellOggettos() as $entity) {
            $entitys[$entity->getId()] = array('position' => $entity->getPosition());
        }
        return $entitys;
    }
}
