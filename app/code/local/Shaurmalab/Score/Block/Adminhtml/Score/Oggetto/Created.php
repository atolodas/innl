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
 * Oggetto after creation popup window
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Created extends Mage_Adminhtml_Block_Widget
{
    protected $_configurableOggetto;
    protected $_entity;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('score/oggetto/created.phtml');
    }


    protected function _prepareLayout()
    {
        $this->setChild(
            'close_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('score')->__('Close Window'),
                    'onclick' => 'addOggetto(true)'
                ))
        );
    }


    public function getCloseButtonHtml()
    {
        return $this->getChildHtml('close_button');
    }

    public function getOggettoId()
    {
        return (int) $this->getRequest()->getParam('id');
    }

    /**
     * Indentifies edit mode of popup
     *
     * @return boolean
     */
    public function isEdit()
    {
        return (bool) $this->getRequest()->getParam('edit');
    }

    /**
     * Retrive serialized json with configurable attributes values of simple
     *
     * @return string
     */
    public function getAttributesJson()
    {
        $result = array();
        foreach ($this->getAttributes() as $attribute) {
            $value = $this->getOggetto()->getAttributeText($attribute->getAttributeCode());

            $result[] = array(
                'label'         => $value,
                'value_index'   => $this->getOggetto()->getData($attribute->getAttributeCode()),
                'attribute_id'  => $attribute->getId()
            );
        }

        return Mage::helper('core')->jsonEncode($result);
    }

    public function getAttributes()
    {
        if ($this->getConfigurableOggetto()->getId()) {
            return $this->getConfigurableOggetto()->getTypeInstance(true)->getUsedOggettoAttributes($this->getConfigurableOggetto());
        }

        $attributes = array();

        $attributesIds = $this->getRequest()->getParam('required');
        if ($attributesIds) {
            $attributesIds = explode(',', $attributesIds);
            foreach ($attributesIds as $attributeId) {
                $attribute = $this->getOggetto()->getTypeInstance(true)->getAttributeById($attributeId, $this->getOggetto());
                if (!$attribute) {
                    continue;
                }
                $attributes[] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * Retrive configurable entity for created/edited simple
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getConfigurableOggetto()
    {
        if (is_null($this->_configurableOggetto)) {
            $this->_configurableOggetto = Mage::getModel('score/oggetto')
                ->setStore(0)
                ->load($this->getRequest()->getParam('entity'));
        }
        return $this->_configurableOggetto;
    }

    /**
     * Retrive entity
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggetto()
    {
        if (is_null($this->_entity)) {
            $this->_entity = Mage::getModel('score/oggetto')
                ->setStore(0)
                ->load($this->getRequest()->getParam('id'));
        }
        return $this->_entity;
    }
} // Class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Created End
