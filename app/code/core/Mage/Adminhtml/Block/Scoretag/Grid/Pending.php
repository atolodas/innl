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
 * Adminhtml pending scoretags grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Scoretag_Grid_Pending extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('pending_grid')
             ->setDefaultSort('name')
             ->setDefaultDir('ASC')
             ->setUseAjax(true)
             ->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('scoretag/scoretag_collection')
            ->addSummary(0)
            ->addStoresVisibility()
            ->addStatusFilter(Mage_Scoretag_Model_Scoretag::STATUS_PENDING);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('name', array(
            'header'        => Mage::helper('scoretag')->__('Scoretag'),
            'index'         => 'name'
        ));

        $this->addColumn('oggettos', array(
            'header'        => Mage::helper('scoretag')->__('Oggettos'),
            'width'         => '140px',
            'align'         => 'right',
            'index'         => 'oggettos',
            'type'          => 'number'
        ));

        $this->addColumn('customers', array(
            'header'        => Mage::helper('scoretag')->__('Customers'),
            'width'         => '140px',
            'align'         => 'right',
            'index'         => 'customers',
            'type'          => 'number'
        ));

        // Collection for stores filters
        if (!$collection = Mage::registry('stores_select_collection')) {
            $collection =  Mage::app()->getStore()->getResourceCollection()
                ->load();
            Mage::register('stores_select_collection', $collection);
        }

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible_in', array(
                'header'    => Mage::helper('scoretag')->__('Store View'),
                'type'      => 'store',
                'index'     => 'stores',
                'sortable'  => false,
                'store_view'=> true
            ));
        }

        return parent::_prepareColumns();
    }

    /**
     * Retrives row click URL
     *
     * @param  mixed $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('scoretag_id' => $row->getId(), 'ret' => 'pending'));
    }

    protected function _addColumnFilterToCollection($column)
    {
        if($column->getIndex() == 'stores') {
            $this->getCollection()->addStoreFilter($column->getFilter()->getCondition(), false);
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('scoretag_id');
        $this->getMassactionBlock()->setFormFieldName('scoretag');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('scoretag')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete', array('ret' => 'pending')),
             'confirm' => Mage::helper('scoretag')->__('Are you sure?')
        ));

        $statuses = $this->helper('scoretag/data')->getStatusesOptionsArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));

        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('scoretag')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true, 'ret' => 'pending')),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('scoretag')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));

        return $this;
    }

    /*
     * Retrieves Grid Url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/scoretag/ajaxPendingGrid', array('_current' => true));
    }
}
