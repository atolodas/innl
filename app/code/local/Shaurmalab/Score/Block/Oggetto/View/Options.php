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
 * @package     Shaurmalab_Score
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Oggetto options block
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Oggetto_View_Options extends Mage_Core_Block_Template
{
    protected $_oggetto;

    protected $_optionRenders = array();

    public function __construct()
    {
        parent::__construct();
        $this->addOptionRenderer(
            'default',
            'score/oggetto_view_options_type_default',
            'score/oggetto/view/options/type/default.phtml'
        );
    }

    /**
     * Retrieve oggetto object
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggetto()
    {
        if (!$this->_oggetto) {
            if (Mage::registry('current_oggetto')) {
                $this->_oggetto = Mage::registry('current_oggetto');
            } else {
                $this->_oggetto = Mage::getSingleton('score/oggetto');
            }
        }
        return $this->_oggetto;
    }

    /**
     * Set oggetto object
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Block_Oggetto_View_Options
     */
    public function setOggetto(Shaurmalab_Score_Model_Oggetto $oggetto = null)
    {
        $this->_oggetto = $oggetto;
        return $this;
    }

    /**
     * Add option renderer to renderers array
     *
     * @param string $type
     * @param string $block
     * @param string $template
     * @return Shaurmalab_Score_Block_Oggetto_View_Options
     */
    public function addOptionRenderer($type, $block, $template)
    {
        $this->_optionRenders[$type] = array(
            'block' => $block,
            'template' => $template,
            'renderer' => null
        );
        return $this;
    }

    /**
     * Get option render by given type
     *
     * @param string $type
     * @return array
     */
    public function getOptionRender($type)
    {
        if (isset($this->_optionRenders[$type])) {
            return $this->_optionRenders[$type];
        }

        return $this->_optionRenders['default'];
    }

    public function getGroupOfOption($type)
    {
        $group = Mage::getSingleton('score/oggetto_option')->getGroupByType($type);

        return $group == '' ? 'default' : $group;
    }

    /**
     * Get oggetto options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->getOggetto()->getOptions();
    }

    public function hasOptions()
    {
        if ($this->getOptions()) {
            return true;
        }
        return false;
    }

    /**
     * Get price configuration
     *
     * @param Shaurmalab_Score_Model_Oggetto_Option_Value|Shaurmalab_Score_Model_Oggetto_Option $option
     * @return array
     */
    protected function _getPriceConfiguration($option)
    {
        $data = array();
        $data['price']      = Mage::helper('core')->currency($option->getPrice(true), false, false);
        $data['oldPrice']   = Mage::helper('core')->currency($option->getPrice(false), false, false);
        $data['priceValue'] = $option->getPrice(false);
        $data['type']       = $option->getPriceType();
        $data['excludeTax'] = $price = Mage::helper('tax')->getPrice($option->getOggetto(), $data['price'], false);
        $data['includeTax'] = $price = Mage::helper('tax')->getPrice($option->getOggetto(), $data['price'], true);
        return $data;
    }

    /**
     * Get json representation of
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array();

        foreach ($this->getOptions() as $option) {
            /* @var $option Shaurmalab_Score_Model_Oggetto_Option */
            $priceValue = 0;
            if ($option->getGroupByType() == Shaurmalab_Score_Model_Oggetto_Option::OPTION_GROUP_SELECT) {
                $_tmpPriceValues = array();
                foreach ($option->getValues() as $value) {
                    /* @var $value Shaurmalab_Score_Model_Oggetto_Option_Value */
                    $id = $value->getId();
                    $_tmpPriceValues[$id] = $this->_getPriceConfiguration($value);
                }
                $priceValue = $_tmpPriceValues;
            } else {
                $priceValue = $this->_getPriceConfiguration($option);
            }
            $config[$option->getId()] = $priceValue;
        }

        return Mage::helper('core')->jsonEncode($config);
    }

    /**
     * Get option html block
     *
     * @param Shaurmalab_Score_Model_Oggetto_Option $option
     */
    public function getOptionHtml(Shaurmalab_Score_Model_Oggetto_Option $option)
    {
        $renderer = $this->getOptionRender(
            $this->getGroupOfOption($option->getType())
        );
        if (is_null($renderer['renderer'])) {
            $renderer['renderer'] = $this->getLayout()->createBlock($renderer['block'])
                ->setTemplate($renderer['template']);
        }
        return $renderer['renderer']
            ->setOggetto($this->getOggetto())
            ->setOption($option)
            ->toHtml();
    }
}
