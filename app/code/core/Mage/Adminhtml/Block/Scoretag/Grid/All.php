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
 * Adminhtml all scoretags grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Scoretag_Grid_All extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('scoretagsGrid');
        $this->setDefaultSort('scoretag_id', 'desc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('scoretag/scoretag_collection')
//            ->addStoreFilter(Mage::app()->getStore()->getId())
               ->addStoresVisibility()
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => Mage::helper('scoretag')->__('Scoretag'),
            'index'     => 'name',
        ));
        $this->addColumn('total_used', array(
            'header'    => Mage::helper('scoretag')->__('# of Uses'),
            'width'     => '140px',
            'align'     => 'center',
            'index'     => 'total_used',
            'type'      => 'number',
        ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('scoretag')->__('Status'),
            'width'     => '90px',
            'index'     => 'status',
            'type'      => 'options',
            'options'    => array(
                Mage_Scoretag_Model_Scoretag::STATUS_DISABLED => Mage::helper('scoretag')->__('Disabled'),
                Mage_Scoretag_Model_Scoretag::STATUS_PENDING  => Mage::helper('scoretag')->__('Pending'),
                Mage_Scoretag_Model_Scoretag::STATUS_APPROVED => Mage::helper('scoretag')->__('Approved'),
            ),
        ));



        $this->setColumnFilter('id')
            ->setColumnFilter('name')
            ->setColumnFilter('total_used')
        ;

        return parent::_prepareColumns();
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection() && $column->getFilter()->getValue()) {
            if($column->getIndex()=='stores') {
                $this->getCollection()->addAttributeToFilter( $column->getIndex(), $column->getFilter()->getCondition());
            } else {
                $this->getCollection()->addStoreFilter($column->getFilter()->getCondition());
            }
        }
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/oggettos', array('scoretag_id' => $row->getId()));
    }

}