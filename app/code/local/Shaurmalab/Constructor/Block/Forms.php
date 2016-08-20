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
class Shaurmalab_Constructor_Block_Forms extends Mage_Adminhtml_Block_Widget_Grid
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
        // $this->setChild('add_button',
        //     $this->getLayout()->createBlock('adminhtml/widget_button')
        //     ->setData(array(
        //         'label'     => Mage::helper('core')->__('Add'),
        //         'href'   => $this->getRowUrl(),
        //         'class'   => 'btn btn-notice mright5 edit-btn'
        //         ))
        //     );
   
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
           $collection = Mage::getModel('formbuilder/forms')->getResourceCollection()
           ->addFieldToFilter('stores', Mage::app()->getStore()->getId());
     

          // foreach ($collection as &$view) {
          //     if ( $view->getStores() == array(Mage::app()->getStore()->getId()) || $view->getStores() == Mage::app()->getStore()->getId() ) {
          //       unset($view);
          //     }
          // }   
          $this->setCollection($collection);
          return parent::_prepareCollection();
    }

    protected function _addColumnFilterToCollection($column)
    {
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns()
    {
      $this->addColumn('title', array(
          'header'    => Mage::helper('formbuilder')->__('Title'),
          'align'     =>'left',
          'width'     => '250px',
          'index'     => 'title',
      ));

      $this->addColumn('no_of_fields', array(
          'header'    => Mage::helper('formbuilder')->__('Fields'),          
          'align'     =>'left',
          'width'     => '50px',
          'index'     => 'no_of_fields',
          'renderer'  => 'formbuilder/adminhtml_formbuilder_renderer_numberoffields'
      ));
  
      $this->addColumn('created_time', array(
          'header'    => Mage::helper('formbuilder')->__('Created Time'),
          'align'     => 'left',
          'width'     => '150px',
          'index'     => 'created_time',
      ));

      $this->addColumn('update_time', array(
          'header'    => Mage::helper('formbuilder')->__('Updated Time'),
          'align'     => 'left',
          'width'     => '150px',
          'index'     => 'update_time',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('formbuilder')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));     

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customer')->__('Action'),
                'width'     => '100',
                'type'      => 'actionfront',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => "<i class='fa fa-edit f15 inline m5 pull-left'></i>",
                        'url'     => array(
                            'base'=>'constructor/admin/editForm',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))                        
                        ),
                        'class' => 'simpleAjaxInner',
                        'field'   => 'id'
                    ),
                    array(
                        'caption' => "<i class='fa fa-remove f15 inline m5 mleft10 pull-left'></i>",
                        'confirm'   => Mage::helper('poll')->__('Are you sure you want to delete it?'),
                        'onclick'   => 'simpleAjaxNoAction(url); jQuery(this).parent().parent().hide()',
                        'url'     => array(
                            'base'=>'constructor/admin/deleteForm',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id',
                        'index'     => 'entity_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('constructor/admin/formsGrid', array('_current'=>true));
    }

    public function getRowUrl($row = '')
    {
        return '';
    }
}
