<?php
/**
 * Shaurmalabnto
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
 * Do not edit or add to this file if you wish to upgrade Shaurmalabnto to newer
 * versions in the future. If you wish to customize Shaurmalabnto for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Shaurmalab
 * @package     Shaurmalab_Score
 * @copyright   Copyright (c) 2013 Shaurmalabnto Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Entity controller
 *
 * @category   Shaurmalab
 * @package    Shaurmalab_Score
 */
class Shaurmalab_Score_OggettoController extends Mage_Core_Controller_Front_Action
{
    /**
     * Current applied design settings
     *
     * @deprecated after 1.4.2.0-beta1
     * @var array
     */
    protected $_designEntitySettingsApplied = array();

    /**
     * Initialize requested entity object
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    protected function _initEntity()
    {
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $entityId  = (int) $this->getRequest()->getParam('id');

        $params = new Varien_Object();
        $params->setCategoryId($categoryId);

        return Mage::helper('score/oggetto')->initEntity($entityId, $this, $params);
    }

    /**
     * Initialize entity view layout
     *
     * @param   Shaurmalab_Score_Model_Oggetto $entity
     * @return  Shaurmalab_Score_OggettoController
     */
    protected function _initEntityLayout($entity)
    {
        Mage::helper('score/oggetto_view')->initEntityLayout($entity, $this);
        return $this;
    }

    /**
     * Recursively apply custom design settings to entity if it's container
     * category custom_use_for_entitys option is setted to 1.
     * If not or entity shows not in category - applyes entity's internal settings
     *
     * @deprecated after 1.4.2.0-beta1, functionality moved to Shaurmalab_Score_Model_Design
     * @param Shaurmalab_Score_Model_Category|Shaurmalab_Score_Model_Oggetto $object
     * @param Mage_Core_Model_Layout_Update $update
     */
    protected function _applyCustomDesignSettings($object, $update)
    {
        if ($object instanceof Shaurmalab_Score_Model_Category) {
            // lookup the proper category recursively
            if ($object->getCustomUseParentSettings()) {
                $parentCategory = $object->getParentCategory();
                if ($parentCategory && $parentCategory->getId() && $parentCategory->getLevel() > 1) {
                    $this->_applyCustomDesignSettings($parentCategory, $update);
                }
                return;
            }

            // don't apply to the entity
            if (!$object->getCustomApplyToEntitys()) {
                return;
            }
        }

        if ($this->_designEntitySettingsApplied) {
            return;
        }

        $date = $object->getCustomDesignDate();
        if (array_key_exists('from', $date) && array_key_exists('to', $date)
            && Mage::app()->getLocale()->isStoreDateInInterval(null, $date['from'], $date['to'])
        ) {
            if ($object->getPageLayout()) {
                $this->_designEntitySettingsApplied['layout'] = $object->getPageLayout();
            }
            $this->_designEntitySettingsApplied['update'] = $object->getCustomLayoutUpdate();
        }
    }

    /**
     * Entity view action
     */
    public function viewAction()
    {
        // Get initial data from request
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $entityId  = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');

        // Prepare helper and params
        $viewHelper = Mage::helper('score/oggetto_view');

        $params = new Varien_Object();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        // Render page
        try {
            $viewHelper->prepareAndRender($entityId, $this, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_OGGETTO_LOADED) {
                if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }
    }

    /**
     * View entity gallery action
     */
    public function galleryAction()
    {
        if (!$this->_initEntity()) {
            if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
                $this->_redirect('');
            } elseif (!$this->getResponse()->isRedirect()) {
                $this->_forward('noRoute');
            }
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Display entity image action
     *
     * @deprecated
     */
    public function imageAction()
    {
        /*
         * All logic has been cut to avoid possible malicious usage of the method
         */
        $this->_forward('noRoute');
    }
}
