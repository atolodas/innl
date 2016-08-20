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
 * Score category helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Helper_Oggetto_View extends Mage_Core_Helper_Abstract
{
    // List of exceptions throwable during prepareAndRender() method
    public $ERR_NO_OGGETTO_LOADED = 1;
    public $ERR_BAD_CONTROLLER_INTERFACE = 2;

     /**
     * Inits layout for viewing oggetto page
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param Mage_Core_Controller_Front_Action $controller
     *
     * @return Shaurmalab_Score_Helper_Oggetto_View
     */
    public function initOggettoLayout($oggetto, $controller)
    {
        $design = Mage::getSingleton('score/design');
        $settings = $design->getDesignSettings($oggetto);

        if ($settings->getCustomDesign()) {
            $design->applyCustomDesign($settings->getCustomDesign());
        }

        $update = $controller->getLayout()->getUpdate();
        $update->addHandle('default');
        $controller->addActionLayoutHandles();

        $update->addHandle('OGGETTO_TYPE_' . $oggetto->getTypeId());
        $update->addHandle('OGGETTO_' . $oggetto->getId());
        $controller->loadLayoutUpdates();

        // Apply custom layout update once layout is loaded
        $layoutUpdates = $settings->getLayoutUpdates();
        if ($layoutUpdates) {
            if (is_array($layoutUpdates)) {
                foreach($layoutUpdates as $layoutUpdate) {
                    $update->addUpdate($layoutUpdate);
                }
            }
        }

        $controller->generateLayoutXml()->generateLayoutBlocks();

        // Apply custom layout (page) template once the blocks are generated
        if ($settings->getPageLayout()) {
            $controller->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
        }

    //    $currentCategory = Mage::registry('current_category');
        $root = $controller->getLayout()->getBlock('root');
        if ($root) {
            $controllerClass = $controller->getFullActionName();
            if ($controllerClass != 'catalog-oggetto-view') {
                $root->addBodyClass('catalog-oggetto-view');
            }
           // $root->addBodyClass('oggetto-' . $oggetto->getUrlKey());
          /*  if ($currentCategory instanceof Shaurmalab_Score_Model_Category) {
                $root->addBodyClass('categorypath-' . $currentCategory->getUrlPath())
                    ->addBodyClass('category-' . $currentCategory->getUrlKey());
            } */
        }

        return $this;
    }

    /**
     * Prepares oggetto view page - inits layout and all needed stuff
     *
     * $params can have all values as $params in Shaurmalab_Score_Helper_Oggetto - initOggetto().
     * Plus following keys:
     *   - 'buy_request' - Varien_Object holding buyRequest to configure oggetto
     *   - 'specify_options' - boolean, whether to show 'Specify options' message
     *   - 'configure_mode' - boolean, whether we're in Configure-mode to edit oggetto configuration
     *
     * @param int $oggettoId
     * @param Mage_Core_Controller_Front_Action $controller
     * @param null|Varien_Object $params
     *
     * @return Shaurmalab_Score_Helper_Oggetto_View
     */
    public function prepareAndRender($oggettoId, $controller, $params = null)
    {
        // Prepare data
        $oggettoHelper = Mage::helper('score/oggetto');
        if (!$params) {
            $params = new Varien_Object();
        }

        // Standard algorithm to prepare and rendern oggetto view page
        $oggetto = $oggettoHelper->initOggetto($oggettoId, $controller, $params);
        if (!$oggetto) {
            throw new Mage_Core_Exception($this->__('Oggetto is not loaded'), $this->ERR_NO_OGGETTO_LOADED);
        }

        if(!(Mage::getSingleton('customer/session')->getCustomer()->getId() == $oggetto->getOwner() || $oggetto->getIsPublic()))  {
            // TODO: add Error message because of redirect
           throw new Mage_Core_Exception($this->__('Oggetto is not loaded'), $this->ERR_NO_OGGETTO_LOADED); //TODO: create a separate function to define if oggetto is visible.
        }

        $buyRequest = $params->getBuyRequest();
        if ($buyRequest) {
            $oggettoHelper->prepareOggettoOptions($oggetto, $buyRequest);
        }

        if ($params->hasConfigureMode()) {
            $oggetto->setConfigureMode($params->getConfigureMode());
        }

        Mage::dispatchEvent('score_controller_oggetto_view', array('oggetto' => $oggetto));

        if ($params->getSpecifyOptions()) {
            $notice = $oggetto->getTypeInstance(true)->getSpecifyOptionMessage();
            Mage::getSingleton('score/session')->addNotice($notice);
        }

        Mage::getSingleton('score/session')->setLastViewedOggettoId($oggetto->getId());

        $this->initOggettoLayout($oggetto, $controller);
        if($oggetto->getSetName()=='School') Mage::app()->getLayout()->getBlock('head')->setTitle($oggetto->getSetName().' profile');

        $controller->initLayoutMessages(array('score/session', 'tag/session', 'checkout/session', 'customer/session'))
            ->renderLayout();

        return $this;
    }
}
