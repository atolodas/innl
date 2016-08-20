<?php

class Cafepress_CPCore_Block_Catalog_Product_Edit_Tab_Common extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('cpcore/adminhtml_fieldset_element')
        );
    }

	    /**
     * Set Fieldset to Form
     *
     * @param array $attributes attributes that are to be added
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $exclude attributes that should be skipped
     */
	protected function _setFieldset($attributes, $fieldset/*, $exclude=array()*/, $additionalData=array())
	{
		$this->_addElementTypes($fieldset);
		$already_displayed=array();

		foreach($attributes as $attribute)
		{
			if(!$attribute) continue;
		//	echo $attribute->getAttributeCode();
		//	echo '<br />';
			if(in_array($attribute->getAttributeCode(), $already_displayed)) continue;

			if(!in_array($attribute->getAttributeCode(), array_keys($additionalData))) continue;
			$already_displayed[] = $attribute->getAttributeCode();

		//	echo $attribute->getAttributeCode();
		//	echo '<br />';

			if($attribute->getFrontendInput()) $fieldType      = $attribute->getFrontendInput();
			else $fieldType = 'select';

			$rendererClass  = $attribute->getFrontend()->getInputRendererClass();
			if(!empty($rendererClass))
			{
				$fieldType  = $inputType . '_' . $attribute->getAttributeCode();
				$fieldset->addType($fieldType, $rendererClass);
			}

			$element = $fieldset->addField($attribute->getAttributeCode(), $fieldType,
				array(
					'name'      => "xmlformat[{$attribute->getAttributeCode()}]",
					'label'     => $attribute->getFrontend()->getLabel(),
					'class'     => $attribute->getFrontend()->getClass(),
					'required'  => $attribute->getIsRequired(),
					'note'      => $attribute->getNote(),
					'style'		=> @$additionalData[$attribute->getAttributeCode()]['style'],
				)
				)
				->setEntityAttribute($attribute);

			$element->setAfterElementHtml($this->_getAdditionalElementHtml($element));

			if($fieldType == 'select')
			{
				$element->setValues(@$additionalData[$attribute->getAttributeCode()]['source']);
			} else if($fieldType == 'multiselect')
			{
                if($attribute){
                    $element->setValues($attribute->getSource()->getAllOptions(false, true));
                }
		/*	} else if($inputType == 'date')
			{
				$element->setImage($this->getSkinUrl('images/grid-cal.gif'));
				$element->setFormat(
				Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
				);*/
			} else if($fieldType == 'multiline')
			{
				$element->setLineCount($attribute->getMultilineCount());
			}
		}
	}
}
