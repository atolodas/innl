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
 * Score Entity widgets controller for CMS WYSIWYG
 *
 * @category   Mage
 * @package    Score_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Adminhtml_Score_Oggetto_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $massAction = $this->getRequest()->getParam('use_massaction', false);
        $entityTypeId = $this->getRequest()->getParam('entity_type_id', null);

        $entitysGrid = $this->getLayout()->createBlock('score/adminhtml_score_oggetto_widget_chooser', '', array(
            'id'                => $uniqId,
            'use_massaction' => $massAction,
            'entity_type_id' => $entityTypeId,
            'category_id'       => $this->getRequest()->getParam('category_id')
        ));

        $html = $entitysGrid->toHtml();

        if (!$this->getRequest()->getParam('entitys_grid')) {
            $categoriesTree = $this->getLayout()->createBlock('score/adminhtml_score_category_widget_chooser', '', array(
                'id'                  => $uniqId.'Tree',
                'node_click_listener' => $entitysGrid->getCategoryClickListenerJs(),
                'with_empty_node'     => true
            ));

            $html = $this->getLayout()->createBlock('score/adminhtml_score_oggetto_widget_chooser_container')
                ->setTreeHtml($categoriesTree->toHtml())
                ->setGridHtml($html)
                ->toHtml();
        }

        $this->getResponse()->setBody($html);
    }
}
