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
 * Adminhtml tag edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Shaurmalab_Score_Block_Adminhtml_Score_Search_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Init Form properties
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('score_search_form');
        $this->setTitle(Mage::helper('score')->__('Search Information'));
    }

    /**
     * Prepare form fields
     *
     * @return Shaurmalab_Score_Block_Adminhtml_Score_Search_Edit_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_score_search');
        /* @var $model Shaurmalab_ScoreSearch_Model_Query */

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('score')->__('General Information')));

        $yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('score')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('score')->__('Yes')
            ));

        if ($model->getId()) {
            $fieldset->addField('query_id', 'hidden', array(
                'name'      => 'query_id',
            ));
        }

        $fieldset->addField('query_text', 'text', array(
            'name'      => 'query_text',
            'label'     => Mage::helper('score')->__('Search Query'),
            'title'     => Mage::helper('score')->__('Search Query'),
            'required'  => true,
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => Mage::helper('score')->__('Store'),
                'title'     => Mage::helper('score')->__('Store'),
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(true, false),
                'required'  => true,
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id'
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        if ($model->getId()) {
            $fieldset->addField('num_results', 'text', array(
                'name'     => 'num_results',
                'label'    => Mage::helper('score')->__('Number of results'),
                'title'    => Mage::helper('score')->__('Number of results (For the last time placed)'),
                'note'     => Mage::helper('score')->__('For the last time placed.'),
                'required' => true,
            ));

            $fieldset->addField('popularity', 'text', array(
                'name'     => 'popularity',
                'label'    => Mage::helper('score')->__('Number of Uses'),
                'title'    => Mage::helper('score')->__('Number of Uses'),
                'required' => true,
            ));
        }

        $fieldset->addField('synonym_for', 'text', array(
            'name'  => 'synonym_for',
            'label' => Mage::helper('score')->__('Synonym For'),
            'title' => Mage::helper('score')->__('Synonym For'),
            'note'  => Mage::helper('score')->__('Will make search for the query above return results for this search.'),
        ));

        $fieldset->addField('redirect', 'text', array(
            'name'  => 'redirect',
            'label' => Mage::helper('score')->__('Redirect URL'),
            'title' => Mage::helper('score')->__('Redirect URL'),
            'class' => 'validate-url',
            'note'  => Mage::helper('score')->__('ex. http://domain.com'),
        ));

        $fieldset->addField('display_in_terms', 'select', array(
            'name'   => 'display_in_terms',
            'label'  => Mage::helper('score')->__('Display in Suggested Terms'),
            'title'  => Mage::helper('score')->__('Display in Suggested Terms'),
            'values' => $yesno,
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
