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
 * Recurring profile attribute edit renderer
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Price_Recurring
    extends Shaurmalab_Score_Block_Adminhtml_Score_Form_Renderer_Fieldset_Element
{
    /**
     * Element output getter
     *
     * @return string
     */
    public function getElementHtml()
    {
        $result = new StdClass;
        $result->output = '';
        Mage::dispatchEvent('score_oggetto_edit_form_render_recurring', array(
            'result' => $result,
            'entity_element' => $this->_element,
            'entity'   => Mage::registry('current_entity'),
        ));
        return $result->output;
    }
}
