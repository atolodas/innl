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
 * Oggetto attributes tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Attributes extends Shaurmalab_Score_Block_Adminhtml_Score_Form
{
    /**
     * Load Wysiwyg on demand and prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::helper('score')->isModuleEnabled('Mage_Cms')
            && Mage::getSingleton('cms/wysiwyg_config')->isEnabled()
        ) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    public function getFrontAttributes() {

        $setId = Mage::registry('entity')->getAttributeSetId();
        /* @var $groups Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection */
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
                ->load();

            if ($nodeChildren->getSize() > 0) {
                foreach ($nodeChildren->getItems() as $child) {
                    /* @var $child Mage_Eav_Model_Entity_Attribute */

                   // if($child->getIsVisibleOnFront() || $child->getIsPublic() || in_array($child->getAttributeCode(), array('visitor_info', 'url_key'))) {
                      $attributes[] = $child->getAttributeCode();
                   // }
                }
            }
        }
        return $attributes;


    }

    /**
     * Prepare attributes form
     *
     * @return null
     */
    protected function _prepareForm()
    {
        $group = $this->getGroup();
        if ($group) {
            $form = new Varien_Data_Form();

            // Initialize entity object as form property to use it during elements generation
            $form->setDataObject(Mage::registry('entity'));

            $fieldset = $form->addFieldset('group_fields' . $group->getId(), array(
                'legend' => Mage::helper('score')->__($group->getAttributeGroupName()),
                'class' => 'fieldset-wide'
            ));

            $attributes = $this->getGroupAttributes();

            $frontAttributes = $this->getFrontAttributes();

            $frontAttributes = array_merge($frontAttributes,array('media_gallery','gallery','meta_description','meta_keyword'));

            foreach ($attributes as $k => $attr) {
                if(!in_array($attr->getAttributeCode(), $frontAttributes)) unset($attributes[$k]);
            }


            $this->_setFieldset($attributes, $fieldset, array('gallery'));

            $urlKey = $form->getElement('url_key');
            if ($urlKey) {
                $urlKey->setRenderer(
                    $this->getLayout()->createBlock('score/adminhtml_score_form_renderer_attribute_urlkey')
                );
            }

            $tierPrice = $form->getElement('tier_price');
            if ($tierPrice) {
                $tierPrice->setRenderer(
                    $this->getLayout()->createBlock('score/adminhtml_score_oggetto_edit_tab_price_tier')
                );
            }

            $groupPrice = $form->getElement('group_price');
            if ($groupPrice) {
                $groupPrice->setRenderer(
                    $this->getLayout()->createBlock('score/adminhtml_score_oggetto_edit_tab_price_group')
                );
            }

            $recurringProfile = $form->getElement('recurring_profile');
            if ($recurringProfile) {
                $recurringProfile->setRenderer(
                    $this->getLayout()->createBlock('score/adminhtml_score_oggetto_edit_tab_price_recurring')
                );
            }

            // Add new attribute button if it is not an image tab
            if (!$form->getElement('media_gallery')
                && Mage::getSingleton('admin/session')->isAllowed('score/attributes/attributes')
            ) {
                $headerBar = $this->getLayout()->createBlock('score/adminhtml_score_oggetto_edit_tab_attributes_create');

                $headerBar->getConfig()
                    ->setTabId('group_' . $group->getId())
                    ->setGroupId($group->getId())
                    ->setStoreId($form->getDataObject()->getStoreId())
                    ->setAttributeSetId($form->getDataObject()->getAttributeSetId())
                    ->setTypeId($form->getDataObject()->getTypeId())
                    ->setOggettoId($form->getDataObject()->getId());

                $fieldset->setHeaderBar($headerBar->toHtml());
            }

            if ($form->getElement('meta_description')) {
                $form->getElement('meta_description')->setOnkeyup('checkMaxLength(this, 255);');
            }

            $values = Mage::registry('entity')->getData();

            // Set default attribute values for new entity
            if (!Mage::registry('entity')->getId()) {
                foreach ($attributes as $attribute) {
                    if (!isset($values[$attribute->getAttributeCode()])) {
                        $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                    }
                }
            }

            if (Mage::registry('entity')->hasLockedAttributes()) {
                foreach (Mage::registry('entity')->getLockedAttributes() as $attribute) {
                    $element = $form->getElement($attribute);
                    if ($element) {
                        $element->setReadonly(true, true);
                    }
                }
            }
            $form->addValues($values);
            $form->setFieldNameSuffix('entity');

            Mage::dispatchEvent('adminhtml_score_oggetto_edit_prepare_form', array('form' => $form));

            $this->setForm($form);
        }
    }

    /**
     * Retrieve additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $result = array(
            'price'    => Mage::getConfig()->getBlockClassName('score/adminhtml_score_oggetto_helper_form_price'),
            'weight'   => Mage::getConfig()->getBlockClassName('score/adminhtml_score_oggetto_helper_form_weight'),
            'gallery'  => Mage::getConfig()->getBlockClassName('score/adminhtml_score_oggetto_helper_form_gallery'),
            'image'    => Mage::getConfig()->getBlockClassName('score/adminhtml_score_oggetto_helper_form_image'),
            'boolean'  => Mage::getConfig()->getBlockClassName('score/adminhtml_score_oggetto_helper_form_boolean'),
            'textarea' => Mage::getConfig()->getBlockClassName('score/adminhtml_score_helper_form_wysiwyg')
        );

        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_score_oggetto_edit_element_types', array('response' => $response));

        foreach ($response->getTypes() as $typeName => $typeClass) {
            $result[$typeName] = $typeClass;
        }

        return $result;
    }
}
