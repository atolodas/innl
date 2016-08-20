<?php

/**
 * Product attributes tab
 *
 * @category   BusinessKing
 * @package    BusinessKing_ProductFieldsPermission
 */
class BusinessKing_ProductFieldsPermission_Block_Adminhtml_Catalog_Product_Edit_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Attributes
{
    protected function _prepareForm()
    {
        if ($group = $this->getGroup()) { 
            $form = new Varien_Data_Form();
            /**
             * Initialize product object as form property
             * for using it in elements generation
             */
            $form->setDataObject(Mage::registry('product'));

            $fieldset = $form->addFieldset('group_fields'.$group->getId(),
                array('legend'=>Mage::helper('catalog')->__($group->getAttributeGroupName()))
            );

            $attributes = $this->getGroupAttributes();
            
            $this->_setProductFieldset($attributes, $fieldset, array('gallery'));

            if ($tierPrice = $form->getElement('tier_price')) {
                $tierPrice->setRenderer(
                    $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_price_tier')
                );
            }

            /**
             * Add new attribute button if not image tab
             */
            if (!$form->getElement('media_gallery')
                 && Mage::getSingleton('admin/session')->isAllowed('catalog/attributes/attributes')) {
                $headerBar = $this->getLayout()->createBlock(
                    'adminhtml/catalog_product_edit_tab_attributes_create'
                );

                $headerBar->getConfig()
                    ->setTabId('group_' . $group->getId())
                    ->setGroupId($group->getId())
                    ->setStoreId($form->getDataObject()->getStoreId())
                    ->setAttributeSetId($form->getDataObject()->getAttributeSetId())
                    ->setTypeId($form->getDataObject()->getTypeId())
                    ->setProductId($form->getDataObject()->getId());

                $fieldset->setHeaderBar(
                    $headerBar->toHtml()
                );
            }

            if ($form->getElement('meta_description')) {
                $form->getElement('meta_description')->setOnkeyup('checkMaxLength(this, 255);');
            }

            $values = Mage::registry('product')->getData();
            /**
             * Set attribute default values for new product
             */
            if (!Mage::registry('product')->getId()) {
                foreach ($attributes as $attribute) {
                    if (!isset($values[$attribute->getAttributeCode()])) {
                        $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                    }
                }
            }

            Mage::dispatchEvent('adminhtml_catalog_product_edit_prepare_form', array('form'=>$form));

            $form->addValues($values);
            $form->setFieldNameSuffix('product');
            $this->setForm($form);
        }
    }
    
	/**
     * Set Product Fieldset to Form
     *
     * @param array $attributes attributes that are to be added
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $exclude attributes that should be skipped
     */
    protected function _setProductFieldset($attributes, $fieldset, $exclude=array())
    {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if (!$attribute || !$attribute->getIsVisible()) {
                continue;
            }
            if ( ($inputType = $attribute->getFrontend()->getInputType())
                 && !in_array($attribute->getAttributeCode(), $exclude)
                 && ('media_image' != $inputType)
               ) 
            {

                $fieldType      = $inputType;
                $rendererClass  = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType  = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                $currentUser = Mage::getSingleton('admin/session')->getUser();
                $currentRole = $currentUser->getRole();
                $roleId = $currentRole->getId();
                $attributeId = $attribute->getAttributeId();
                $isReadOnly = Mage::getModel('productfieldspermission/product_fields')->checkReadOnlyField($roleId, $attributeId);
                            
    			if ($isReadOnly) {
    				$configuration = array(
                        'name'      => $attribute->getAttributeCode(),
                        'label'     => __($attribute->getFrontend()->getLabel()),
                        'class'     => $attribute->getFrontend()->getClass(),
                        'required'  => $attribute->getIsRequired(),
                        'note'      => $attribute->getNote(),
    					'disabled'  => 'disabled',
                    );
    			}
    			else {
    				$configuration = array(
                        'name'      => $attribute->getAttributeCode(),
                        'label'     => __($attribute->getFrontend()->getLabel()),
                        'class'     => $attribute->getFrontend()->getClass(),
                        'required'  => $attribute->getIsRequired(),
                        'note'      => $attribute->getNote(),    					
                    );
    			}
                $element = $fieldset->addField($attribute->getAttributeCode(), $fieldType,
                    $configuration
                )
                ->setEntityAttribute($attribute);

                $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));

                if ($inputType == 'select' || $inputType == 'multiselect') {
                    $element->setValues($attribute->getSource()->getAllOptions(true, true));
                } elseif ($inputType == 'date') {
                    $element->setImage($this->getSkinUrl('images/grid-cal.gif'));
                    $element->setFormat(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
                }
            }
        }
    }
}
