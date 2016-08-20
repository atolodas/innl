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
 * Adminhtml scoretagged oggettos grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Scoretag_Grid_Oggettos extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('scoretag/oggetto_collection')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
        ;
        if ($scoretagId = $this->getRequest()->getParam('scoretag_id')) {
            $collection->addScoretagFilter($scoretagId);
        }
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $collection->addCustomerFilter($customerId);
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('oggetto_id', array(
            'header'    => Mage::helper('scoretag')->__('ID'),
            'align'     => 'center',
            'width'     => '60px',
            'sortable'  => false,
            'index'     => 'oggetto_id'
        ));
        $this->addColumn('sku', array(
            'header'    => Mage::helper('scoretag')->__('SKU'),
            'align'     => 'center',
            'index'     => 'sku'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('scoretag')->__('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('scoretags', array(
            'header'    => Mage::helper('scoretag')->__('Scoretags'),
            'index'     => 'scoretags',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'adminhtml/scoretag_grid_column_renderer_scoretags'
        ));
        $this->addColumn('action', array(
            'header'    => Mage::helper('scoretag')->__('Action'),
            'align'     => 'center',
            'width'     => '120px',
            'format'    => '<a href="'.$this->getUrl('*/*/customers/oggetto_id/$oggetto_id').'">'.Mage::helper('scoretag')->__('View Customers').'</a>',
            'filter'    => false,
            'sortable'  => false,
            'is_system' => true
        ));

        return parent::_prepareColumns();
    }

}

