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
class Shaurmalab_Seller_Block_Products extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');

    }

    protected function _prepareLayout()
    {
        $this->setChild('add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('core')->__('Add'),
                'onclick'   => "document.location.href = '".$this->getUrl('*/*/edit', array('_current'=>true))."'",
                'class'   => 'btn btn-notice mright5'
                ))
            );
   
        $this->setChild('reset_filter_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('core')->__('Reset Filter'),
                    'onclick'   => $this->getJsObjectName().'.resetFilter()',
                    'class'   => 'btn btn-notice mright5'
                ))
        );
        $this->setChild('search_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('core')->__('Search'),
                    'onclick'   => $this->getJsObjectName().'.doFilter()',
                    'class'   => 'btn btn-success'
                ))
        );

    return $this;
    }

  public function getMainButtonsHtml()
    {
        $html = '';
        $html.= $this->getChildHtml('add_button');
        if($this->getFilterVisibility()){
            $html.= $this->getResetFilterButtonHtml();
            $html.= $this->getSearchButtonHtml();
        }
        
        return $html;
    }


    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection =  new TBT_Enhancedgrid_Model_Resource_Eav_Mysql4_Product_Collection();
        $collection->addAttributeToFilter('owner', Mage::getSingleton('customer/session')->getCustomer()->getId())
        ->addAttributeToSelect('sku')
        ->addAttributeToSelect('name')
        ->addAttributeToSelect('thumbnail')
        ->addAttributeToSelect('attribute_set_id')
        ->addAttributeToSelect('type_id');

        

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }
        $collection->addAttributeToSelect('price');
        $collection->addAttributeToSelect('image');
        $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        
        $this->setCollection($collection);
     
        parent::_prepareCollection();
        //$this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
                'width' => '200px',
                ));

    $this->addColumn('sku',
        array(
        'header'=> Mage::helper('catalog')->__('SKU'),
        'width' => '80px',
        'index' => 'sku',
        ));

     $this->addColumn( 'thumbnail', 
                array(
                    'header' => Mage::helper( 'catalog' )->__( 'Image' ), 
                    'type' => 'image', 
                    'index' => 'thumbnail'
                ) );

        $this->addColumn( 'categories', 
                array(
                    'header' => Mage::helper( 'catalog' )->__( 'Categories' ), 
                    'width' => '100px', 
                    'sortable' => true, 
                    'index' => 'categories', 
                    'sort_index' => 'category', 
                    'filter_index' => 'category'
                ) );

    $store = $this->_getStore();
    $this->addColumn('price',
        array(
        'header'=> Mage::helper('catalog')->__('Price'),
        'type'  => 'price',
        'currency_code' => $store->getBaseCurrency()->getCode(),
        'index' => 'price',
        'filter'    => false,
        ));

       
        $this->addColumn('visibility',
            array(
        'header'=> Mage::helper('catalog')->__('Visibility'),
        'width' => '70px',
        'index' => 'visibility',
        'type'  => 'options',
        'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('status',
            array(
            'header'=> Mage::helper('catalog')->__('Status'),
            'width' => '70px',
            'index' => 'status',
            'type'  => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));


        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'actionfront',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => "<i class='fa fa-eye f15 inline m5 mleft10 pull-left'></i>",
                        'url'     => array(
                            'base'=>'catalog/product/view/',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id',
                        'target'    => '_blank'
                    ),
                    array(
                        'caption' => "<i class='fa fa-edit f15 inline m5 mleft10 pull-left'></i>",
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    ),
                    array(
                        'caption' => "<i class='fa fa-remove f15 inline m5 mleft10 pull-left'></i>",
                        'confirm'   => Mage::helper('poll')->__('Are you sure you want to delete it?'),
                        'onclick'   => 'simpleAjaxNoAction(url); jQuery(this).parent().parent().hide()',
                        'url'     => array(
                            'base'=>'*/*/deleteProduct',
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

    protected function _preparePage() {
        
        $this->getCollection()
            ->getSelect()
            ->reset( Zend_Db_Select::GROUP );
        
        parent::_preparePage();
        
        $category_decorator = Mage::getModel( 'enhancedgrid/product_collection_category_decorator' );
        $category_decorator->setCollection( $this->getCollection() )
            ->addCategories();
        
        return $this;
    
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row = '')
    {
        return '';
    }
}
