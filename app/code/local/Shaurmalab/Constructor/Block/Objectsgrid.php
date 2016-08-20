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
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Constructor_Block_Objectsgrid extends Mage_Adminhtml_Block_Widget_Grid
{


    public function __construct()
    {
        parent::__construct();
        $this->setId('objectsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');

    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getMainButtonsHtml()
    {
        $html = '';
        if($this->getFilterVisibility()){
            $html.= $this->getResetFilterButtonHtml();
            $html.= $this->getSearchButtonHtml();
        }
        return $html;
    }


    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', Mage::app()->getStore()->getId());
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('score/oggetto')->getCollection()
        ->addAttributeToFilter('attribute_set_id',$this->getObjectId())
        ->addAttributeToSelect('*')
        ;
        
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {
     
        $setId = $this->getObjectId();
        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load();
        $attributes = array();


        /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
        foreach ($groups as $node) {
                $nodeChildren = Mage::getResourceModel('score/oggetto_attribute_collection')
                ->addStoreLabel(Mage::app()->getStore()->getId())
                ->setAttributeGroupFilter($node->getId())
                ->addVisibleFilter()

                               // ->checkConfigurableOggettos()
                ->load();

            if ($nodeChildren->getSize() > 0) {
                foreach ($nodeChildren->getItems() as $child) {
                    /* @var $child Mage_Eav_Model_Entity_Attribute */

                    if($child->getIsVisibleOnFront() || $child->getIsPublic()) {
                      $attributes[] = $child;
                    }
                }
            }
        }

        foreach ($attributes as $attribute) {
            $isRelated = Mage::helper('score/oggetto')->isRelatedAttribute($attribute->getAttributeCode());
            $isChain = Mage::helper('score/oggetto')->isChainAttribute($attribute->getAttributeCode());
            $isUser = Mage::helper('score/oggetto')->isUserAttribute($attribute->getAttributeCode());
            $isDict = Mage::helper('score/oggetto')->isDictionaryAttribute($attribute->getAttributeCode());
               
            $label =  $attribute->getStoreLabel()?$attribute->getStoreLabel():$attribute->getFrontendLabel();
            $this->addColumn($attribute->getAttributeCode(),
            array(
                'header'=> $label,
                'index' => $attribute->getAttributeCode(),
            ));
        }


        $objects = Mage::getModel('score/oggetto')->getCollection()
        ->addAttributeToFilter('attribute_set_id',$this->getObjectId())
        ->addAttributeToSelect('owner')
        ;
        
        $customersCollection = Mage::getModel('customer/customer')->getCollection()
        ->addAttributeToFilter('entity_id',array('in'=>$objects->getColumnValues('owner')))
        ->addNameToSelect();
        $customers = array();
        $customers[0] = Mage::helper('core')->__('Guest');
        foreach ($customersCollection as $customer) {
             $customers[$customer->getId()] = $customer->getName();
         } 
        $this->addColumn('owner',
            array(
                'header'=> Mage::helper('score')->__('Owner'),
                'index' => 'owner',
                'width'     => '200px',
                'type'      => 'options',
                'options'   => $customers,

        ));
        $sourceYesNo = Mage::getSingleton('adminhtml/system_config_source_yesno');

        $this->addColumn('is_public',
            array(
                'header'=> Mage::helper('score')->__('Is Public'),
                'index' => 'is_public',
                'width'     => '100px',
                'type'      => 'options',
                'options'   => $sourceYesNo->toArray(),
        ));


        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'actionfront',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => "<i class='fa fa-edit f15 inline m5 pull-left'></i>",
                        'url'     => array(
                            'base'=>'score/oggetto/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))                        
                        ),
                        'class' => 'edit-btn',
                        'field'   => 'id'
                    ),
                    array(
                        'caption' => "<i class='fa fa-remove f15 inline m5 mleft10 pull-left'></i>",
                        'confirm'   => Mage::helper('poll')->__('Are you sure you want to delete it?'),
                        'onclick'   => 'simpleAjaxNoAction(url); jQuery(this).parent().parent().hide()',
                        'url'     => array(
                            'base'=>'*/*/deleteOggetto',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id',
                        'index'     => 'entity_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/objectsgrid', array('_current'=>true));
    }

    public function getRowUrl($row = '')
    {
        return '';

        if($row && is_object($row)) { 
            return $this->getUrl('*/*/editOggetto', array(
                'store'=>$this->getRequest()->getParam('store'),
                'id'=>$row->getId())
            );
        } else { 
            return $this->getUrl('*/*/editOggetto', array(
                'store'=>$this->getRequest()->getParam('store'),
                'id'=>0)
            );
        
        }
    }
}
