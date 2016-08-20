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
class Shaurmalab_Seller_Block_Products_Edit extends Mage_Adminhtml_Block_Widget_Form
{
	public function getAttributes() { 
		$attributes = array();
		$product = $this->getProduct();
		/* @var $product Mage_Catalog_Model_Product */
		foreach($product->getAttributes() as $attribute) {
			$attributes[] = $attribute;
		}
		return $attributes;
	}

	public function getProduct() { 
		if(Mage::registry('product')) return Mage::registry('product');
		$id = Mage::app()->getRequest()->getParam('id');
		$product = Mage::getModel('catalog/product')->load($id);
		Mage::register('product',$product);
		return $product;
	}

	

	public function getForm() { 
		$attributes = $this->getAttributes();
		$product = $this->getProduct();
		$available = Mage::helper('seller')->getAvailableAttributes();
		$form = new Varien_Data_Form();
		$form->setDataObject(Mage::registry('product'));

		foreach ($attributes as $attribute) { 
			if(in_array($attribute->getAttributeCode(),$available)) { 
				$attrs[] = $attribute;
				$codes[] = $attribute->getAttributeCode();
				if(!$product->getId() && $attribute->getAttributeCode()=='visibility') { 
				$attrs[] = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'price');
				$attrs[] = Mage::getModel('catalog/resource_eav_attribute')->loadByCode('catalog_product', 'special_price');
				}
			}
		}

		
		$fieldset = $form->addFieldset('group_fields', array(
			'legend' =>  (Mage::registry('product')->getId()?$this->__('Edit product').' "'.(Mage::registry('product')->getName()).'"':$this->__('Create product')),
			'class' => 'fieldset-wide'
		));

		$this->_setProductFieldset($attrs, $fieldset);

		$urlKey = $form->getElement('url_key');
		if ($urlKey) {
			$urlKey->setRenderer(
				$this->getLayout()->createBlock('adminhtml/catalog_form_renderer_attribute_urlkey')
				);
		}


	if ($form->getElement('meta_description')) {
		$form->getElement('meta_description')->setOnkeyup('checkMaxLength(this, 255);');
	}

	$values = Mage::registry('product')->getData();

	// Set default attribute values for new product
	if (!Mage::registry('product')->getId()) {
		foreach ($attributes as $attribute) {
			if (!isset($values[$attribute->getAttributeCode()])) {
				$values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
			}
		}
	}
	$form->addValues($values);
	$form->setFieldNameSuffix('product');
	$form->setParent($this);
	return $form; 
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
            	//if($inputType == 'media_image') $inputType = 'file';
                $fieldType      = $inputType;
                $rendererClass  = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType  = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                // $currentUser = Mage::getSingleton('admin/session')->getUser();
                // $currentRole = $currentUser->getRole();
                // $roleId = $currentRole->getId();
                // $attributeId = $attribute->getAttributeId();
                $isReadOnly = false; // Mage::getModel('productfieldspermission/product_fields')->checkReadOnlyField($roleId, $attributeId);
                            
    			if ($isReadOnly) {
    				$configuration = array(
                        'name'      => $attribute->getAttributeCode(),
                        'label'     => $this->__($attribute->getFrontend()->getLabel()),
                        'class'     => $attribute->getFrontend()->getClass(),
                        'required'  => $attribute->getIsRequired(),
                        'note'      => $attribute->getNote(),
    					'disabled'  => 'disabled',
                    );
    			}
    			else {
    				$configuration = array(
                        'name'      => $attribute->getAttributeCode(),
                        'label'     => $this->__($attribute->getFrontend()->getLabel()),
                        'class'     => $attribute->getFrontend()->getClass(),
                        'required'  => $attribute->getIsRequired(),
                        'note'      => $attribute->getNote(),    					
                    );
    			}
    			//if($fieldType == 'price') $fieldType = 'text';
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

    /**
     * Retrieve additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $result = array(
            'price'    => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_price'),
            'weight'   => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_weight'),
            'gallery'  => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_gallery'),
            'image'    => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_image'),
            'boolean'  => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_boolean'),
            'textarea' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_helper_form_wysiwyg')
        );

        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_catalog_product_edit_element_types', array('response' => $response));

        foreach ($response->getTypes() as $typeName => $typeClass) {
            $result[$typeName] = $typeClass;
        }

        return $result;
    }



}