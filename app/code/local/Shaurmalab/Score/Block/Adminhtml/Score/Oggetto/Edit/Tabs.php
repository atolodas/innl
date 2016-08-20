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
 * admin entity edit tabs
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected $_attributeTabBlock = 'score/adminhtml_score_oggetto_edit_tab_attributes';

    public function __construct()
    {
        parent::__construct();
        $this->setId('entity_info_tabs');
        $this->setDestElementId('entity_edit_form');
        $this->setTitle(Mage::helper('score')->__('Oggetto Information'));
    }

    protected function _prepareLayout()
    {
        $entity = $this->getOggetto();

        if (!($setId = $entity->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {
            $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                ->setAttributeSetFilter($setId)
                ->setSortOrder()
                ->load();

            foreach ($groupCollection as $group) {
                $attributes = $entity->getAttributes($group->getId(), true);
                // do not add groups without attributes

                foreach ($attributes as $key => $attribute) {
                    if( !$attribute->getIsVisible() ) {
                    //    unset($attributes[$key]);
                    }
                }

                if (count($attributes)==0) {
                    continue;
                }

                $this->addTab('group_'.$group->getId(), array(
                    'label'     => Mage::helper('score')->__($group->getAttributeGroupName()),
                    'content'   => $this->_translateHtml($this->getLayout()->createBlock($this->getAttributeTabBlock(),
                        'adminhtml.score.oggetto.edit.tab.attributes')->setGroup($group)
                            ->setGroupAttributes($attributes)
                            ->toHtml()),
                ));
            }

 /*           if (Mage::helper('core')->isModuleEnabled('Mage_CatalogInventory')) {
                $this->addTab('inventory', array(
                    'label'     => Mage::helper('score')->__('Inventory'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('score/adminhtml_score_oggetto_edit_tab_inventory')->toHtml()),
                ));
            }
*/
            /**
             * Don't display website tab for single mode
             */
            if (!Mage::app()->isSingleStoreMode()) {
                $this->addTab('websites', array(
                    'label'     => Mage::helper('score')->__('Websites'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('score/adminhtml_score_oggetto_edit_tab_websites')->toHtml()),
                ));
            }

           // $this->addTab('categories', array(
           //      'label'     => Mage::helper('score')->__('Categories'),
           //      'url'       => $this->getUrl('*/*/categories', array('_current' => true)),
           //     'class'     => 'ajax',
           //  ));

          $this->addTab('related', array(
                'label'     => Mage::helper('score')->__('Child Oggettos'),
                'url'       => $this->getUrl('*/*/related', array('_current' => true)),
                'class'     => 'ajax',
            ));

//            $this->addTab('upsell', array(
//                'label'     => Mage::helper('score')->__('Up-sells'),
//                'url'       => $this->getUrl('*/*/upsell', array('_current' => true)),
//                'class'     => 'ajax',
//            ));

//            $this->addTab('crosssell', array(
//                'label'     => Mage::helper('score')->__('Cross-sells'),
//                'url'       => $this->getUrl('*/*/crosssell', array('_current' => true)),
//                'class'     => 'ajax',
//            ));

            $storeId = 0;
            if ($this->getRequest()->getParam('store')) {
                $storeId = Mage::app()->getStore($this->getRequest()->getParam('store'))->getId();
            }

            $alertPriceAllow = Mage::getStoreConfig('score/oggettoalert/allow_price');
            $alertStockAllow = Mage::getStoreConfig('score/oggettoalert/allow_stock');

            if (($alertPriceAllow || $alertStockAllow) && !$entity->isGrouped()) {
                $this->addTab('entityalert', array(
                    'label'     => Mage::helper('score')->__('Oggetto Alerts'),
                    'content'   => $this->_translateHtml($this->getLayout()
                        ->createBlock('score/adminhtml_score_oggetto_edit_tab_alerts', 'admin.alerts.entitys')->toHtml())
                ));
            }

            if( $this->getRequest()->getParam('id', false) ) {
                // if (Mage::helper('score')->isModuleEnabled('Mage_Review')) {
                //     if (Mage::getSingleton('admin/session')->isAllowed('admin/score/reviews_ratings')){
                //         $this->addTab('reviews', array(
                //             'label' => Mage::helper('score')->__('Oggetto Reviews'),
                //             'url'   => $this->getUrl('*/*/reviews', array('_current' => true)),
                //             'class' => 'ajax',
                //         ));
                //     }
                // }
                // if (Mage::helper('score')->isModuleEnabled('Mage_Tag')) {
                //     if (Mage::getSingleton('admin/session')->isAllowed('admin/score/tag')){
                //         $this->addTab('tags', array(
                //          'label'     => Mage::helper('score')->__('Oggetto Tags'),
                //          'url'   => $this->getUrl('*/*/tagGrid', array('_current' => true)),
                //          'class' => 'ajax',
                //         ));

                //         $this->addTab('customers_tags', array(
                //             'label'     => Mage::helper('score')->__('Customers Tagged Oggetto'),
                //             'url'   => $this->getUrl('*/*/tagCustomerGrid', array('_current' => true)),
                //             'class' => 'ajax',
                //         ));
                //     }
                // }

            }

            /**
             * Do not change this tab id
             * @see Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tabs_Configurable
             * @see Mage_Bundle_Block_Adminhtml_Catalog_Oggetto_Edit_Tabs
             */
//            if (!$entity->isGrouped()) {
//                $this->addTab('customer_options', array(
//                    'label' => Mage::helper('score')->__('Custom Options'),
//                    'url'   => $this->getUrl('*/*/options', array('_current' => true)),
//                    'class' => 'ajax',
//                ));
//            }

        }
        else {
            $this->addTab('set', array(
                'label'     => Mage::helper('score')->__('Settings'),
                'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('score/adminhtml_score_oggetto_edit_tab_settings')->toHtml()),
                'active'    => true
            ));
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrive entity object from object if not from registry
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggetto()
    {
        if (!($this->getData('entity') instanceof Shaurmalab_Score_Model_Oggetto)) {
            $this->setData('entity', Mage::registry('entity'));
        }
        return $this->getData('entity');
    }

    /**
     * Getting attribute block name for tabs
     *
     * @return string
     */
    public function getAttributeTabBlock()
    {
        if (is_null(Mage::helper('score/adminhtml_score')->getAttributeTabBlock())) {
            return $this->_attributeTabBlock;
        }
        return Mage::helper('score/adminhtml_score')->getAttributeTabBlock();
    }

    public function setAttributeTabBlock($attributeTabBlock)
    {
        $this->_attributeTabBlock = $attributeTabBlock;
        return $this;
    }

    /**
     * Translate html content
     *
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}
