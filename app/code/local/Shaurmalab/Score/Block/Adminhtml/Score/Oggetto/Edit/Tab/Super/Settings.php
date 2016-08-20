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
 * Create Configuranle procuct Settings Tab Block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Super_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare block children and data
     *
     */
    protected function _prepareLayout()
    {
        $onclick = "setSuperSettings('".$this->getContinueUrl()."','attribute-checkbox', 'attributes')";
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('score')->__('Continue'),
                    'onclick'   => $onclick,
                    'class'     => 'save'
                ))
        );

        $backButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label'     => Mage::helper('score')->__('Back'),
                'onclick'   => "setLocation('".$this->getBackUrl()."')",
                'class'     => 'back'
            ));

        $this->setChild('back_button', $backButton);
        parent::_prepareLayout();
    }

    /**
     * Retrieve currently edited entity object
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    protected function _getOggetto()
    {
        return Mage::registry('current_entity');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Super_Settings
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array(
            'legend'=>Mage::helper('score')->__('Select Configurable Attributes ')
        ));

        $entity    = $this->_getOggetto();
        $attributes = $entity->getTypeInstance(true)
            ->getSetAttributes($entity);

        $fieldset->addField('req_text', 'note', array(
            'text' => '<ul class="messages"><li class="notice-msg"><ul><li>'
                    .  $this->__('Only attributes with scope "Global", input type "Dropdown" and Use To Create Configurable Oggetto "Yes" are available.')
                    . '</li></ul></li></ul>'
        ));

        $hasAttributes = false;

        foreach ($attributes as $attribute) {
            if ($entity->getTypeInstance(true)->canUseAttribute($attribute, $entity)) {
                $hasAttributes = true;
                $fieldset->addField('attribute_'.$attribute->getAttributeId(), 'checkbox', array(
                    'label' => $attribute->getFrontend()->getLabel(),
                    'title' => $attribute->getFrontend()->getLabel(),
                    'name'  => 'attribute',
                    'class' => 'attribute-checkbox',
                    'value' => $attribute->getAttributeId()
                ));
            }
        }

        if ($hasAttributes) {
            $fieldset->addField('attributes', 'hidden', array(
                        'name'  => 'attribute_validate',
                        'value' => '',
                        'class' => 'validate-super-entity-attributes'
                    ));

            $fieldset->addField('continue_button', 'note', array(
                'text' => $this->getChildHtml('continue_button'),
            ));
        }
        else {
            $fieldset->addField('note_text', 'note', array(
                'text' => $this->__('This attribute set does not have attributes which we can use for configurable entity')
            ));
            $fieldset->addField('back_button', 'note', array(
                'text' => $this->getChildHtml('back_button'),
            ));
        }


        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve Continue URL
     *
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->getUrl('*/*/new', array(
            '_current'   => true,
            'attributes' => '{{attributes}}'
        ));
    }

    /**
     * Retrieve Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/new', array('set'=>null, 'type'=>null));
    }
}
