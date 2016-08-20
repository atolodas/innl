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
 * @package     Shaurmalab_ScoreSearch
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Advanced search result
 *
 * @category   Mage
 * @package    Shaurmalab_ScoreSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_ScoreSearch_Block_Advanced_Result extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                'label'=>Mage::helper('scoresearch')->__('Home'),
                'title'=>Mage::helper('scoresearch')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ))->addCrumb('search', array(
                'label'=>Mage::helper('scoresearch')->__('Catalog Advanced Search'),
                'link'=>$this->getUrl('*/*/')
            ))->addCrumb('search_result', array(
                'label'=>Mage::helper('scoresearch')->__('Results')
            ));
        }
        return parent::_prepareLayout();
    }

    public function setListOrders() {
        $category = Mage::getSingleton('score/layer')
            ->getCurrentCategory();
        /* @var $category Mage_Catalog_Model_Category */

        $availableOrders = $category->getAvailableSortByOptions();
        unset($availableOrders['position']);

        $this->getChild('search_result_list')
            ->setAvailableOrders($availableOrders);
    }

    public function setListModes() {
        $this->getChild('search_result_list')
            ->setModes(array(
                'grid' => Mage::helper('scoresearch')->__('Grid'),
                'list' => Mage::helper('scoresearch')->__('List'))
            );
    }


     public function setListCollection() {
        $this->getChild('search_result_list')->setCollection($this->_getOggettoCollection());
    }

    protected function _getOggettoCollection() {

        $collection = $this->getChild('search_result_list')->getLoadedOggettoCollection(); //Mage::helper('scoresearch')->getEngine()->getResultCollection(); 
        $params = Mage::app()->getRequest()->getParams();
        $conditions = $this->getSearchModel()->getConditions($params);
        foreach ($conditions as $key => $value) {
            $collection->addAttributeToFilter($key, $value); 
        }
        if(isset($params['keywords'])) { 
            $textAttributesToFilter = array();

            foreach($params['keywords'] as $key => $value) { 
                $values = explode(',', $value);
                $keys = explode('+', $key);
                foreach ($keys as $attribute) {
                    foreach ($values as $val) {
                        $val = str_replace(' ','%',trim($val));

                        $textAttributesToFilter[] = array('attribute' => $attribute, 'like' => '%'.$val.'%');
                    }
                }
            }
            if(!empty($textAttributesToFilter)) $collection->addAttributeToFilter($textAttributesToFilter);

        }
        if(isset($params['set'])) { 
                $setId = Mage::helper('score/oggetto')->getSetIdByCode($params['set']);
                $collection->addAttributeToFilter('attribute_set_id', $setId);
        }
       
        return $collection;
    }

    public function getSearchModel()
    {
        return Mage::getSingleton('scoresearch/advanced');
    }

    public function getResultCount()
    {
        if (!$this->getData('result_count')) {
            $collection = $this->_getOggettoCollection();
            $size = $collection->getSize();
            $this->setResultCount($size);
        }
        return $this->getData('result_count');
    }

    public function getOggettoListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    public function getFormUrl()
    {
        return Mage::getModel('core/url')
            ->setQueryParams($this->getRequest()->getQuery())
            ->getUrl('*/*/', array('_escape' => true));
    }

    public function getSearchCriterias()
    {
        $searchCriterias = $this->getSearchModel()->getSearchCriterias();
        $middle = ceil(count($searchCriterias) / 2);
        $left = array_slice($searchCriterias, 0, $middle);
        $right = array_slice($searchCriterias, $middle);

        return array('left'=>$left, 'right'=>$right);
    }
}
