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
 * Adminhtml score super entity configurable tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Super_Config extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Initialize block
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setOggettoId($this->getRequest()->getParam('id'));
        $this->setTemplate('score/oggetto/edit/super/config.phtml');
        $this->setId('config_super_entity');
        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }

    /**
     * Retrieve Tab class (for loading)
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Check block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return (bool) $this->_getOggetto()->getCompositeReadonly();
    }

    /**
     * Check whether attributes of configurable entitys can be editable
     *
     * @return boolean
     */
    public function isAttributesConfigurationReadonly()
    {
        return (bool) $this->_getOggetto()->getAttributesConfigurationReadonly();
    }

    /**
     * Check whether prices of configurable entitys can be editable
     *
     * @return boolean
     */
    public function isAttributesPricesReadonly()
    {
        return $this->_getOggetto()->getAttributesConfigurationReadonly() ||
            (Mage::helper('score')->isPriceGlobal() && $this->isReadonly());
    }

    /**
     * Prepare Layout data
     *
     * @return Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Super_Config
     */
    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()->createBlock('score/adminhtml_score_oggetto_edit_tab_super_config_grid',
                'admin.entity.edit.tab.super.config.grid')
        );

        $this->setChild('create_empty',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('score')->__('Create Empty'),
                    'class' => 'add',
                    'onclick' => 'superOggetto.createEmptyOggetto()'
                ))
        );

        if ($this->_getOggetto()->getId()) {
            $this->setChild('simple',
                $this->getLayout()->createBlock('score/adminhtml_score_oggetto_edit_tab_super_config_simple',
                    'score.oggetto.edit.tab.super.config.simple')
            );

            $this->setChild('create_from_configurable',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label' => Mage::helper('score')->__('Copy From Configurable'),
                        'class' => 'add',
                        'onclick' => 'superOggetto.createNewOggetto()'
                    ))
            );
        }

        return parent::_prepareLayout();
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
     * Retrieve attributes data in JSON format
     *
     * @return string
     */
    public function getAttributesJson()
    {
        $attributes = $this->_getOggetto()->getTypeInstance(true)
            ->getConfigurableAttributesAsArray($this->_getOggetto());
        if(!$attributes) {
            return '[]';
        } else {
            // Hide price if needed
            foreach ($attributes as &$attribute) {
                if (isset($attribute['values']) && is_array($attribute['values'])) {
                    foreach ($attribute['values'] as &$attributeValue) {
                        if (!$this->getCanReadPrice()) {
                            $attributeValue['pricing_value'] = '';
                            $attributeValue['is_percent'] = 0;
                        }
                        $attributeValue['can_edit_price'] = $this->getCanEditPrice();
                        $attributeValue['can_read_price'] = $this->getCanReadPrice();
                    }
                }
            }
        }
        return Mage::helper('core')->jsonEncode($attributes);
    }

    /**
     * Retrieve Links in JSON format
     *
     * @return string
     */
    public function getLinksJson()
    {
        $entitys = $this->_getOggetto()->getTypeInstance(true)
            ->getUsedOggettos(null, $this->_getOggetto());
        if(!$entitys) {
            return '{}';
        }
        $data = array();
        foreach ($entitys as $entity) {
            $data[$entity->getId()] = $this->getConfigurableSettings($entity);
        }
        return Mage::helper('core')->jsonEncode($data);
    }

    /**
     * Retrieve configurable settings
     *
     * @param Shaurmalab_Score_Model_Oggetto $entity
     * @return array
     */
    public function getConfigurableSettings($entity) {
        $data = array();
        $attributes = $this->_getOggetto()->getTypeInstance(true)
            ->getUsedOggettoAttributes($this->_getOggetto());
        foreach ($attributes as $attribute) {
            $data[] = array(
                'attribute_id' => $attribute->getId(),
                'label'        => $entity->getAttributeText($attribute->getAttributeCode()),
                'value_index'  => $entity->getData($attribute->getAttributeCode())
            );
        }

        return $data;
    }

    /**
     * Retrieve Grid child HTML
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * Retrieve Grid JavaScript object name
     *
     * @return string
     */
    public function getGridJsObject()
    {
        return $this->getChild('grid')->getJsObjectName();
    }

    /**
     * Retrieve Create New Empty Oggetto URL
     *
     * @return string
     */
    public function getNewEmptyOggettoUrl()
    {
        return $this->getUrl(
            '*/*/new',
            array(
                'set'      => $this->_getOggetto()->getAttributeSetId(),
                'type'     => Shaurmalab_Score_Model_Oggetto_Type::TYPE_SIMPLE,
                'required' => $this->_getRequiredAttributesIds(),
                'popup'    => 1
            )
        );
    }

    /**
     * Retrieve Create New Oggetto URL
     *
     * @return string
     */
    public function getNewOggettoUrl()
    {
        return $this->getUrl(
            '*/*/new',
            array(
                'set'      => $this->_getOggetto()->getAttributeSetId(),
                'type'     => Shaurmalab_Score_Model_Oggetto_Type::TYPE_SIMPLE,
                'required' => $this->_getRequiredAttributesIds(),
                'popup'    => 1,
                'entity'  => $this->_getOggetto()->getId()
            )
        );
    }

    /**
     * Retrieve Quick create entity URL
     *
     * @return string
     */
    public function getQuickCreationUrl()
    {
        return $this->getUrl(
            '*/*/quickCreate',
            array(
                'entity'  => $this->_getOggetto()->getId()
            )
        );
    }

    /**
     * Retrieve Required attributes Ids (comma separated)
     *
     * @return string
     */
    protected function _getRequiredAttributesIds()
    {
        $attributesIds = array();
        $configurableAttributes = $this->_getOggetto()
            ->getTypeInstance(true)->getConfigurableAttributes($this->_getOggetto());
        foreach ($configurableAttributes as $attribute) {
            $attributesIds[] = $attribute->getOggettoAttribute()->getId();
        }

        return implode(',', $attributesIds);
    }

    /**
     * Escape JavaScript string
     *
     * @param string $string
     * @return string
     */
    public function escapeJs($string)
    {
        return addcslashes($string, "'\r\n\\");
    }

    /**
     * Retrieve Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('score')->__('Associated Oggettos');
    }

    /**
     * Retrieve Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('score')->__('Associated Oggettos');
    }

    /**
     * Can show tab flag
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check is a hidden tab
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Show "Use default price" checkbox
     *
     * @return bool
     */
    public function getShowUseDefaultPrice()
    {
        return !Mage::helper('score')->isPriceGlobal()
            && $this->_getOggetto()->getStoreId();
    }
}
