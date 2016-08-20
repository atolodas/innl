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
 * Adminhtml super entity links grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Super_Config_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Config attribute codes
     *
     * @var null|array
     */
    protected $_configAttributeCodes = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setId('super_entity_links');

        if ($this->_getOggetto()->getId()) {
            $this->setDefaultFilter(array('in_entitys' => 1));
        }
    }

    /**
     * Retrieve currently edited entity object
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

            $createdOggettos = $this->_getCreatedOggettos();

            $existsOggettos = $entityIds; // Only for "Yes" Filter we will add created entitys

            if(count($createdOggettos)>0) {
                if(!is_array($existsOggettos)) {
                    $existsOggettos = $createdOggettos;
                } else {
                    $existsOggettos = array_merge($createdOggettos);
                }
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$existsOggettos));
            }
            else {
                if($entityIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$entityIds));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _getCreatedOggettos()
    {
        $entitys = $this->getRequest()->getPost('new_entitys', null);
        if (!is_array($entitys)) {
            $entitys = array();
        }

        return $entitys;
    }

    /**
     * Prepare collection
     *
     * @return Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Super_Config_Grid
     */
    protected function _prepareCollection()
    {
        $allowOggettoTypes = array();
        foreach (Mage::helper('score/oggetto_configuration')->getConfigurableAllowedTypes() as $type) {
            $allowOggettoTypes[] = $type->getName();
        }

        $entity = $this->_getOggetto();
        $collection = $entity->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('price')
            ->addFieldToFilter('attribute_set_id',$entity->getAttributeSetId())
            ->addFieldToFilter('type_id', $allowOggettoTypes)
            ->addFilterByRequiredOptions()
            ->joinAttribute('name', 'score_oggetto/name', 'entity_id', null, 'inner');

//        if (Mage::helper('score')->isModuleEnabled('Mage_CatalogInventory')) {
//            Mage::getModel('cataloginventory/stock_item')->addCatalogInventoryToOggettoCollection($collection);
//        }

        foreach ($entity->getTypeInstance(true)->getUsedOggettoAttributes($entity) as $attribute) {
            $collection->addAttributeToSelect($attribute->getAttributeCode());
            $collection->addAttributeToFilter($attribute->getAttributeCode(), array('notnull'=>1));
        }

        $this->setCollection($collection);

        if ($this->isReadonly()) {
            $collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedOggettos()));
        }

        parent::_prepareCollection();
        return $this;
    }

    protected function _getSelectedOggettos()
    {
        $entitys = $this->getRequest()->getPost('entitys', null);
        if (!is_array($entitys)) {
            $entitys = $this->_getOggetto()->getTypeInstance(true)->getUsedOggettoIds($this->_getOggetto());
        }
        return $entitys;
    }

    /**
     * Check block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        if ($this->hasData('is_readonly')) {
            return $this->getData('is_readonly');
        }
        return $this->_getOggetto()->getCompositeReadonly();
    }

    protected function _prepareColumns()
    {
        $entity = $this->_getOggetto();
        $attributes = $entity->getTypeInstance(true)->getConfigurableAttributes($entity);

        if (!$this->isReadonly()) {
            $this->addColumn('in_entitys', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_entitys',
                'values'    => $this->_getSelectedOggettos(),
                'align'     => 'center',
                'index'     => 'entity_id',
                'renderer'  => 'score/adminhtml_score_oggetto_edit_tab_super_config_grid_renderer_checkbox',
                'attributes' => $attributes
            ));
        }

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


        $sets = Mage::getModel('eav/entity_attribute_set')->getCollection()
            ->setEntityTypeFilter($this->_getOggetto()->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('score')->__('Attrib. Set Name'),
                'width' => '130px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
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

        $this->addColumn('is_saleable', array(
            'header'    => Mage::helper('score')->__('Inventory'),
            'renderer'  => 'score/adminhtml_score_oggetto_edit_tab_super_config_grid_renderer_inventory',
            'filter'    => 'score/adminhtml_score_oggetto_edit_tab_super_config_grid_filter_inventory',
            'index'     => 'is_saleable'
        ));

        foreach ($attributes as $attribute) {
            $entityAttribute = $attribute->getOggettoAttribute();
            $entityAttribute->getSource();
            $this->addColumn($entityAttribute->getAttributeCode(), array(
                'header'    => $entityAttribute->getFrontend()->getLabel(),
                'index'     => $entityAttribute->getAttributeCode(),
                'type'      => $entityAttribute->getSourceModel() ? 'options' : 'number',
                'options'   => $entityAttribute->getSourceModel() ? $this->getOptions($attribute) : ''
            ));
        }

         $this->addColumn('action',
            array(
                'header'    => Mage::helper('score')->__('Action'),
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('score')->__('Edit'),
                        'url'     => $this->getEditParamsForAssociated(),
                        'field'   => 'id',
                        'onclick'  => 'superOggetto.createPopup(this.href);return false;'
                    )
                ),
                'filter'    => false,
                'sortable'  => false
        ));

        return parent::_prepareColumns();
    }

    public function getEditParamsForAssociated()
    {
        return array(
            'base'      =>  '*/*/edit',
            'params'    =>  array(
                'required' => $this->_getRequiredAttributesIds(),
                'popup'    => 1,
                'entity'  => $this->_getOggetto()->getId()
            )
        );
    }

    /**
     * Retrieve Required attributes Ids (comma separated)
     *
     * @return string
     */
    protected function _getRequiredAttributesIds()
    {
        $attributesIds = array();
        foreach (
            $this->_getOggetto()
                ->getTypeInstance(true)
                ->getConfigurableAttributes($this->_getOggetto()) as $attribute
        ) {
            $attributesIds[] = $attribute->getOggettoAttribute()->getId();
        }

        return implode(',', $attributesIds);
    }

    public function getOptions($attribute) {
        $result = array();
        foreach ($attribute->getOggettoAttribute()->getSource()->getAllOptions() as $option) {
            if($option['value']!='') {
                $result[$option['value']] = $option['label'];
            }
        }

        return $result;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/superConfig', array('_current'=>true));
    }

    /**
     * Retrieving configurable attributes
     *
     * @return array
     */
    protected function _getConfigAttributeCodes()
    {
        if (is_null($this->_configAttributeCodes)) {
            $entity = $this->_getOggetto();
            $attributes = $entity->getTypeInstance(true)->getConfigurableAttributes($entity);
            $attributeCodes = array();
            foreach ($attributes as $attribute) {
                $entityAttribute = $attribute->getOggettoAttribute();
                $attributeCodes[] = $entityAttribute->getAttributeCode();
            }
            $this->_configAttributeCodes = $attributeCodes;
        }
        return $this->_configAttributeCodes;
    }

    /**
     * Retrieve item row configurable attribute data
     *
     * @param Varien_Object $item
     * @return array
     */
    protected function _retrieveRowData(Varien_Object $item)
    {
        $attributeValues = array();
        foreach ($this->_getConfigAttributeCodes() as $attributeCode) {
            $data = $item->getData($attributeCode);
            if ($data) {
                $attributeValues[$attributeCode] = $data;
            }
        }
        return $attributeValues;
    }

    /**
     * Checking the data contains the same value of data after collection
     *
     * @return Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Super_Config_Grid
     */
    protected function _afterLoadCollection()
    {
        parent::_afterLoadCollection();

        $attributeCodes = $this->_getConfigAttributeCodes();
        if (!$attributeCodes) {
            return $this;
        }

        $disableMultiSelect = false;
        $ids = array();
        foreach ($this->_collection as $item) {
            $ids[] = $item->getId();
            $needleAttributeValues = $this->_retrieveRowData($item);
            foreach($this->_collection as $item2) {
                // Skip the data if already checked
                if (in_array($item2->getId(), $ids)) {
                   continue;
                }
                $attributeValues = $this->_retrieveRowData($item2);
                $disableMultiSelect = ($needleAttributeValues == $attributeValues);
                if ($disableMultiSelect) {
                   break;
                }
            }
            if ($disableMultiSelect) {
                break;
            }
        }

        // Disable multiselect column
        if ($disableMultiSelect) {
            $selectAll = $this->getColumn('in_entitys');
            if ($selectAll) {
                $selectAll->setDisabled(true);
            }
        }

        return $this;
    }
}
