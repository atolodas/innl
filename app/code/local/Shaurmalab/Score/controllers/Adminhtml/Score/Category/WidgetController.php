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
 * @package     Score_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Score category widgets controller for CMS WYSIWYG
 *
 * @category   Mage
 * @package    Score_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Adminhtml_Score_Category_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $this->getResponse()->setBody(
            $this->_getCategoryTreeBlock()->toHtml()
        );
    }

    /**
     * Categories tree node (Ajax version)
     */
    public function categoriesJsonAction()
    {
        if ($categoryId = (int) $this->getRequest()->getPost('id')) {

            $category = Mage::getModel('score/category')->load($categoryId);
            if ($category->getId()) {
                Mage::register('category', $category);
                Mage::register('current_category', $category);
            }
            $this->getResponse()->setBody(
                $this->_getCategoryTreeBlock()->getTreeJson($category)
            );
        }
    }

    protected function _getCategoryTreeBlock()
    {
        return $this->getLayout()->createBlock('score/adminhtml_score_category_widget_chooser', '', array(
            'id' => $this->getRequest()->getParam('uniq_id'),
            'use_massaction' => $this->getRequest()->getParam('use_massaction', false)
        ));
    }
}
