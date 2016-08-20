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
 * @package     Mage_Scoretag
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * List of scoretagged oggettos
 *
 * @category   Mage
 * @package    Mage_Scoretag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Scoretag_Block_Oggetto_Result extends Shaurmalab_Score_Block_Oggetto_Abstract
{
    protected $_oggettoCollection;


    public function getScoretag()
    {
        return Mage::registry('current_scoretag');
    }

    protected function _prepareLayout()
    {
        $title = $this->getHeaderText();
        $this->getLayout()->getBlock('head')->setTitle($title);
        $this->getLayout()->getBlock('root')->setHeaderTitle($title);
        return parent::_prepareLayout();
    }

    public function setListOrders() {
        $this->getChild('search_result_list')
            ->setAvailableOrders(array(
                'name' => Mage::helper('scoretag')->__('Name'),
                'price'=>Mage::helper('scoretag')->__('Price'))
            );
    }

    public function setListModes() {
        $this->getChild('search_result_list')
            ->setModes(array(
                'grid' => Mage::helper('scoretag')->__('Grid'),
                'list' => Mage::helper('scoretag')->__('List'))
            );
    }

    public function setListCollection() {
        $this->getChild('search_result_list')
           ->setCollection($this->_getOggettoCollection());
    }

    public function getOggettoListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    protected function _getOggettoCollection()
    {
        if(is_null($this->_oggettoCollection)) {
            $scoretagModel = Mage::getModel('scoretag/scoretag');
            $this->_oggettoCollection = $scoretagModel->getEntityCollection()
                ->addAttributeToSelect(Mage::getSingleton('score/config')->getOggettoAttributes())
                ->addAttributeToFilter('is_public', 1)
                ->addScoretagFilter((int)$this->getScoretag()->getId())
                ->addStoreFilter((int)Mage::app()->getStore()->getId())
                ->addAttributeToSelect('image')
                ->addUrlRewrite()
                ->setActiveFilter();

            Mage::getSingleton('score/oggetto_visibility')->addVisibleInSiteFilterToCollection(
                $this->_oggettoCollection
            );

        }

        return $this->_oggettoCollection;
    }

    public function getResultCount()
    {
        if (!$this->getData('result_count')) {
            $size = $this->_getOggettoCollection()->getSize();
            $this->setResultCount($size);
        }
        return $this->getData('result_count');
    }

    public function getHeaderText()
    {
        if( $this->getScoretag()->getName() ) {
            return Mage::helper('scoretag')->__("What's tagged with '%s'", $this->escapeHtml($this->getScoretag()->getName()));
        } else {
            return false;
        }
    }

    public function getLayer()
    {
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('score/layer');
    }

    public function getSubheaderText()
    {
        return false;
    }

    public function getNoResultText()
    {
        return Mage::helper('scoretag')->__('No matches found.');
    }
}
