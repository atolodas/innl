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
 * Oggetto in category grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Super_Group extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('super_entity_grid');
        $this->setDefaultSort('entity_id');
        $this->setSkipGenerateContent(true);
        $this->setUseAjax(true);
        if ($this->_getOggetto()->getId()) {
            $this->setDefaultFilter(array('in_entitys'=>1));
        }
    }

    public function getTabUrl()
    {
        return $this->getUrl('*/*/superGroup', array('_current'=>true));
    }

    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Retrieve currently edited entity model
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    protected function _getOggetto()
    {
        return Mage::registry('current_entity');
    }

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
            }
            else {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$entityIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Prepare collection
     *
     * @return Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Super_Group
     */
    protected function _prepareCollection()
    {
        $allowOggettoTypes = array();
        $allowOggettoTypeNodes = Mage::getConfig()
            ->getNode('global/score/oggetto/type/grouped/allow_entity_types')->children();
        foreach ($allowOggettoTypeNodes as $type) {
            $allowOggettoTypes[] = $type->getName();
        }

        $collection = Mage::getModel('score/oggetto_link')->useGroupedLinks()
            ->getOggettoCollection()
            ->setOggetto($this->_getOggetto())
            ->addAttributeToSelect('*')
            ->addFilterByRequiredOptions()
            ->addAttributeToFilter('type_id', $allowOggettoTypes);

        if ($this->getIsReadonly() === true) {
            $collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedOggettos()));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_entitys', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_entitys',
            'values'    => $this->_getSelectedOggettos(),
            'align'     => 'center',
            'index'     => 'entity_id'
        ));

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('score')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('score')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('score')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku'
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('score')->__('Price'),
            'type'      => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));

        $this->addColumn('qty', array(
            'header'    => Mage::helper('score')->__('Default Qty'),
            'name'      => 'qty',
            'type'      => 'number',
            'validate_class' => 'validate-number',
            'index'     => 'qty',
            'width'     => '1',
            'editable'  => true
        ));

        $this->addColumn('position', array(
            'header'    => Mage::helper('score')->__('Position'),
            'name'      => 'position',
            'type'      => 'number',
            'validate_class' => 'validate-number',
            'index'     => 'position',
            'width'     => '1',
            'editable'  => true,
            'edit_only' => !$this->_getOggetto()->getId()
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->_getData('grid_url')
            ? $this->_getData('grid_url') : $this->getUrl('*/*/superGroupGridOnly', array('_current'=>true));
    }

    /**
     * Retrieve selected grouped entitys
     *
     * @return array
     */
    protected function _getSelectedOggettos()
    {
        $entitys = $this->getOggettosGrouped();
        if (!is_array($entitys)) {
            $entitys = array_keys($this->getSelectedGroupedOggettos());
        }
        return $entitys;
    }

    /**
     * Retrieve grouped entitys
     *
     * @return array
     */
    public function getSelectedGroupedOggettos()
    {
        $associatedOggettos = Mage::registry('current_entity')->getTypeInstance(true)
            ->getAssociatedOggettos(Mage::registry('current_entity'));
        $entitys = array();
        foreach ($associatedOggettos as $entity) {
            $entitys[$entity->getId()] = array(
                'qty'       => $entity->getQty(),
                'position'  => $entity->getPosition()
            );
        }
        return $entitys;
    }

    public function getTabLabel()
    {
        return Mage::helper('score')->__('Associated Oggettos');
    }
    public function getTabTitle()
    {
        return Mage::helper('score')->__('Associated Oggettos');
    }
    public function canShowTab()
    {
        return true;
    }
    public function isHidden()
    {
        return false;
    }
}
