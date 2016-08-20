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
 * Score oggetto related items block
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Oggetto_List_Related extends Shaurmalab_Score_Block_Oggetto_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    protected $_itemCollection;

	protected $_parentCollection; 
	
    protected function _prepareData()
    {
        $oggetto = Mage::registry('current_oggetto');
        if(!is_object($oggetto)) $oggetto = Mage::registry('oggetto');
        
        /* @var $oggetto Shaurmalab_Score_Model_Oggetto */

        if(!$this->_itemCollection) { 
        $this->_itemCollection = $oggetto->getRelatedOggettoCollection()
            ->addAttributeToSelect('*')
            //->setPositionOrder()
            // TODO: return sorting after Child oggetto positions fix. How to fix Parent positions ? 

            ->addStoreFilter()
        ;

        if($this->getSet()) { 
                $this->_itemCollection->addAttributeToFilter('attribute_set_id',Mage::helper('score/oggetto')->getSetIdByCode($this->getSet()));
        }
		
        if (Mage::helper('score')->isModuleEnabled('Mage_Checkout')) {
           // Mage::getResourceSingleton('checkout/cart')->addExcludeOggettoFilter($this->_itemCollection,
           //     Mage::getSingleton('checkout/session')->getQuoteId()
           // );
            $this->_addOggettoAttributesAndPrices($this->_itemCollection);
        }

        // TODO: return visibility later ? 
        // Mage::getSingleton('score/oggetto_visibility')->addVisibleInScoreFilterToCollection($this->_itemCollection);

        $this->_itemCollection->load();
		}

        if(!$this->_parentCollection) { 
		 $this->_parentCollection = $oggetto->getParentOggettoCollection()
            ->addAttributeToSelect('*')
           // ->setPositionOrder()
            ->addStoreFilter()
        ;

        if($this->getSet()) { 
                $this->_parentCollection->addAttributeToFilter('attribute_set_id',Mage::helper('score/oggetto')->getSetIdByCode($this->getSet()));
        }

        if (Mage::helper('score')->isModuleEnabled('Mage_Checkout')) {
           // Mage::getResourceSingleton('checkout/cart')->addExcludeOggettoFilter($this->_itemCollection,
           //     Mage::getSingleton('checkout/session')->getQuoteId()
           // );
            $this->_addOggettoAttributesAndPrices($this->_parentCollection);
        }

        // TODO: return visibility later ? 
        // Mage::getSingleton('score/oggetto_visibility')->addVisibleInScoreFilterToCollection($this->_itemCollection);

        $this->_parentCollection->load();
        }


        foreach ($this->_itemCollection as $oggetto) {
            $oggetto->setDoNotUseCategoryId(true);
        }
		foreach ($this->_parentCollection as $oggetto) {
            $oggetto->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    public function setCollection($collection) { 
         $this->_itemCollection = $collection;
         $this->_parentCollection = $collection; 
         return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    public function getItems()
    {
        return $this->_itemCollection;
    }
	
	public function getParentItems()
    {
        return $this->_parentCollection;
    }

    /**
     * Get tags array for saving cache
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(parent::getCacheTags(), $this->getItemsTags($this->getItems()));
    }
}
