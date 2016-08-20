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
 * Oggetto attributes grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Attribute_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    /**
     * Prepare entity attributes grid collection object
     *
     * @return Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Attribute_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('score/oggetto_attribute_collection')
            ->addVisibleFilter()
        ->addFieldToSelect('*');
		//	echo $collection->getSelect();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare entity attributes grid columns
     *
     * @return Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Attribute_Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter('is_visible', array(
            'header'=>Mage::helper('score')->__('Visible'),
            'sortable'=>true,
            'index'=>'is_visible_on_front',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('score')->__('Yes'),
                '0' => Mage::helper('score')->__('No'),
            ),
            'align' => 'center',
        ), 'frontend_label');

        $this->addColumnAfter('is_global', array(
            'header'=>Mage::helper('score')->__('Scope'),
            'sortable'=>true,
            'index'=>'is_global',
            'type' => 'options',
            'options' => array(
                Shaurmalab_Score_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('score')->__('Store View'),
                Shaurmalab_Score_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('score')->__('Website'),
                Shaurmalab_Score_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('score')->__('Global'),
            ),
            'align' => 'center',
        ), 'is_visible');

        $this->addColumn('is_searchable', array(
            'header'=>Mage::helper('score')->__('Searchable'),
            'sortable'=>true,
            'index'=>'is_searchable',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('score')->__('Yes'),
                '0' => Mage::helper('score')->__('No'),
            ),
            'align' => 'center',
        ), 'is_user_defined');

        $this->addColumnAfter('is_public', array(
            'header'=>Mage::helper('score')->__('Is public ?'),
            'sortable'=>true,
            'index'=>'is_public',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('score')->__('Yes'),
                '0' => Mage::helper('score')->__('No'),
            ),
            'align' => 'center',
        ), 'is_public');

        $this->addColumnAfter('is_for_logged_in', array(
            'header'=>Mage::helper('score')->__('is_for_logged_in ONLY?'),
            'sortable'=>true,
            'index'=>'is_for_logged_in',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('score')->__('Yes'),
                '0' => Mage::helper('score')->__('No'),
            ),
            'align' => 'center',
        ), 'is_for_logged_in');

        $this->addColumnAfter('is_for_edit', array(
            'header'=>Mage::helper('score')->__('Available for edit?'),
            'sortable'=>true,
            'index'=>'is_for_edit',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('score')->__('Yes'),
                '0' => Mage::helper('score')->__('Hidden'),
                '2' => Mage::helper('score')->__('Label'),
                '3' => Mage::helper('score')->__('Under button'),
            ),
            'align' => 'center',
        ), 'is_for_edit');

        return $this;
    }
}
