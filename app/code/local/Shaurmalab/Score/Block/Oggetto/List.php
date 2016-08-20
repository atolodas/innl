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
 * Oggetto list
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Oggetto_List extends Shaurmalab_Score_Block_Oggetto_Abstract
{
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'score/oggetto_list_toolbar';

    /**
     * Oggetto Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_oggettoCollection;

    public function getSetId()
    {
        $set = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('score/oggetto')->getResource()->getTypeId())
            ->addFieldToFilter('attribute_set_name',$this->getSet())
            ->getFirstItem(); // TODO: add filter by owner when needed

        return $set->getId();
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getOggettoCollection()
    {

            $layer = $this->getLayer();

            $origCategory = null;

            $p = 1;
            if($this->getNewlimit()) {
                $limit = $this->getNewlimit();
                $p = $this->getNewpage();
                if(isset($_GET['p'])) $p = $_GET['p'];
                Mage::app()->getRequest()->setParam('limit',$limit);
                if($p) {
                    Mage::app()->getRequest()->setParam('p',$p);
                }
            }



            if(is_null($this->_oggettoCollection)) {
                $oggettos =  $layer->getOggettoCollection();
            } else {
                $oggettos = $this->_oggettoCollection;
            }

            $oggettos->addAttributeToSort('created_at', 'desc');
            $filters = $this->getPrefilter();
            $filters = explode(',',$filters);

            foreach($filters as $filter) {
                if($filter) {
                    list($code,$value) = explode('=',$filter);
                    if($code == 'owner' && $value == 'customer_id' && is_object(Mage::getSingleton('customer/session'))) {
                        $value = Mage::getSingleton('customer/session')->getCustomerId();
                    }
                    $oggettos->addAttributeToFilter($code,array('like'=>$value));

                }
            }
            $oggettos->addAttributeToSelect('*');

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

        return $oggettos;
    }

    /**
     * Get score layer model
     *
     * @return Shaurmalab_Score_Model_Layer
     */
    public function getLayer()
    {
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('score/layer');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedOggettoCollection()
    {
        return $this->_getOggettoCollection();
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        if($this->getDefaultMode()) {
            return $this->getDefaultMode();
        }
        return $this->getChild('toolbar')->getCurrentMode();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getOggettoCollection();

        // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        Mage::dispatchEvent('score_block_oggetto_list_collection', array(
            'collection' => $this->_getOggettoCollection()
        ));

        $this->_getOggettoCollection()->load();

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Shaurmalab_Score_Block_Oggetto_List_Toolbar
     */
    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function setCollection($collection)
    {
        $this->_oggettoCollection = $collection;
        return $this;
    }

    public function addAttribute($code)
    {
        $this->_getOggettoCollection()->addAttributeToSelect($code);
        return $this;
    }

    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');
    }

    /**
     * Retrieve Score Config object
     *
     * @return Shaurmalab_Score_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('score/config');
    }

    /**
     * Prepare Sort By fields from Category Data
     *
     * @param Shaurmalab_Score_Model_Category $category
     * @return Shaurmalab_Score_Block_Oggetto_List
     */
    public function prepareSortableFieldsByCategory($category) {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            if ($categorySortBy = $category->getDefaultSortBy()) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }
                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve block cache tags based on oggetto collection
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(
            parent::getCacheTags(),
            $this->getItemsTags($this->_getOggettoCollection())
        );
    }
}
