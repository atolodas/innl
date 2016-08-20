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
 * Oggetto Chooser for "Oggetto Link" Cms Widget Plugin
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Widget_Chooser extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_selectedOggettos = array();

    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
     */
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setDefaultSort('name');
        $this->setUseAjax(true);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('*/score_oggetto_widget/chooser', array(
            'uniq_id' => $uniqId,
            'use_massaction' => false,
        ));

        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()) {
            $value = explode('/', $element->getValue());
            $entityId = false;
            if (isset($value[0]) && isset($value[1]) && $value[0] == 'entity') {
                $entityId = $value[1];
            }
            $categoryId = isset($value[2]) ? $value[2] : false;
            $label = '';
            if ($categoryId) {
                $label = Mage::getResourceSingleton('score/category')
                    ->getAttributeRawValue($categoryId, 'name', Mage::app()->getStore()) . '/';
            }
            if ($entityId) {
                $label .= Mage::getResourceSingleton('score/oggetto')
                    ->getAttributeRawValue($entityId, 'name', Mage::app()->getStore());
            }
            $chooser->setLabel($label);
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        if ($this->getUseMassaction()) {
            return "function (grid, element) {
                $(grid.containerId).fire('entity:changed', {element: element});
            }";
        }
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        if (!$this->getUseMassaction()) {
            $chooserJsObject = $this->getId();
            return '
                function (grid, event) {
                    var trElement = Event.findElement(event, "tr");
                    var entityId = trElement.down("td").innerHTML;
                    var entityName = trElement.down("td").next().next().innerHTML;
                    var optionLabel = entityName;
                    var optionValue = "entity/" + entityId.replace(/^\s+|\s+$/g,"");
                    if (grid.categoryId) {
                        optionValue += "/" + grid.categoryId;
                    }
                    if (grid.categoryName) {
                        optionLabel = grid.categoryName + " / " + optionLabel;
                    }
                    '.$chooserJsObject.'.setElementValue(optionValue);
                    '.$chooserJsObject.'.setElementLabel(optionLabel);
                    '.$chooserJsObject.'.close();
                }
            ';
        }
    }

    /**
     * Category Tree node onClick listener js function
     *
     * @return string
     */
    public function getCategoryClickListenerJs()
    {
        $js = '
            function (node, e) {
                {jsObject}.addVarToUrl("category_id", node.attributes.id);
                {jsObject}.reload({jsObject}.url);
                {jsObject}.categoryId = node.attributes.id != "none" ? node.attributes.id : false;
                {jsObject}.categoryName = node.attributes.id != "none" ? node.text : false;
            }
        ';
        $js = str_replace('{jsObject}', $this->getJsObjectName(), $js);
        return $js;
    }

    /**
     * Filter checked/unchecked rows in grid
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Widget_Chooser
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_entitys') {
            $selected = $this->getSelectedOggettos();
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$selected));
            } else {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$selected));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare entitys collection, defined collection filters (category, entity type)
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Collection */
        $collection = Mage::getResourceModel('score/oggetto_collection')
            ->setStoreId(0)
            ->addAttributeToSelect('name');

        if ($categoryId = $this->getCategoryId()) {
            $category = Mage::getModel('score/category')->load($categoryId);
            if ($category->getId()) {
                // $collection->addCategoryFilter($category);
                $entityIds = $category->getOggettosPosition();
                $entityIds = array_keys($entityIds);
                if (empty($entityIds)) {
                    $entityIds = 0;
                }
                $collection->addFieldToFilter('entity_id', array('in' => $entityIds));
            }
        }

        if ($entityTypeId = $this->getOggettoTypeId()) {
            $collection->addAttributeToFilter('type_id', $entityTypeId);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for entitys grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        if ($this->getUseMassaction()) {
            $this->addColumn('in_entitys', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_entitys',
                'inline_css' => 'checkbox entities',
                'field_name' => 'in_entitys',
                'values'    => $this->getSelectedOggettos(),
                'align'     => 'center',
                'index'     => 'entity_id',
                'use_index' => true,
            ));
        }

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('score')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('chooser_sku', array(
            'header'    => Mage::helper('score')->__('SKU'),
            'name'      => 'chooser_sku',
            'width'     => '80px',
            'index'     => 'sku'
        ));
        $this->addColumn('chooser_name', array(
            'header'    => Mage::helper('score')->__('Oggetto Name'),
            'name'      => 'chooser_name',
            'index'     => 'name'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Adds additional parameter to URL for loading only entitys grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/score_oggetto_widget/chooser', array(
            'entitys_grid' => true,
            '_current' => true,
            'uniq_id' => $this->getId(),
            'use_massaction' => $this->getUseMassaction(),
            'entity_type_id' => $this->getOggettoTypeId()
        ));
    }

    /**
     * Setter
     *
     * @param array $selectedOggettos
     * @return Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Widget_Chooser
     */
    public function setSelectedOggettos($selectedOggettos)
    {
        $this->_selectedOggettos = $selectedOggettos;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedOggettos()
    {
        if ($selectedOggettos = $this->getRequest()->getParam('selected_entitys', null)) {
            $this->setSelectedOggettos($selectedOggettos);
        }
        return $this->_selectedOggettos;
    }
}
