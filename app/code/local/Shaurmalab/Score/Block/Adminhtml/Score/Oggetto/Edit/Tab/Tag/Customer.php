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
 * List of customers tagged a entity
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Tag_Customer extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tag_customers_grid');
        $this->setDefaultSort('firstname');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        if (Mage::helper('score')->isModuleEnabled('Mage_Tag')) {
            $collection = Mage::getModel('tag/tag')
                ->getCustomerCollection()
                ->addOggettoFilter($this->getOggettoId())
                ->addGroupByTag()
                ->addDescOrder();

            $this->setCollection($collection);
        }
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        return parent::_afterLoadCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('firstname', array(
            'header'    => Mage::helper('score')->__('First Name'),
            'index'     => 'firstname',
        ));

        $this->addColumn('lastname', array(
            'header'        => Mage::helper('score')->__('Last Name'),
            'index'         => 'lastname',
        ));

        $this->addColumn('email', array(
            'header'        => Mage::helper('score')->__('Email'),
            'index'         => 'email',
        ));

        $this->addColumn('name', array(
            'header'        => Mage::helper('score')->__('Tag Name'),
            'index'         => 'name',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit', array('id' => $row->getCustomerId()));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/score_oggetto/tagCustomerGrid', array(
            '_current' => true,
            'id'       => $this->getOggettoId(),
            'entity_id' => $this->getOggettoId(),
        ));
    }
}
