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
 * Score Observer
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Observer
{
    /**
     * Process score ata related with store data changes
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_Score_Model_Observer
     */
    public function storeEdit(Varien_Event_Observer $observer)
    {
        /** @var $store Mage_Core_Model_Store */
        $store = $observer->getEvent()->getStore();
        if ($store->dataHasChangedFor('group_id')) {
            Mage::app()->reinitStores();
            /** @var $categoryFlatHelper Shaurmalab_Score_Helper_Category_Flat */
            $categoryFlatHelper = Mage::helper('score/category_flat');
            if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
                Mage::getResourceModel('score/category_flat')->synchronize(null, array($store->getId()));
            }
            Mage::getResourceSingleton('score/oggetto')->refreshEnabledIndex($store);
        }
        return $this;
    }

    /**
     * Process score data related with new store
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_Score_Model_Observer
     */
    public function storeAdd(Varien_Event_Observer $observer)
    {
        /* @var $store Mage_Core_Model_Store */
        $store = $observer->getEvent()->getStore();
        Mage::app()->reinitStores();
        Mage::getConfig()->reinit();
        /** @var $categoryFlatHelper Shaurmalab_Score_Helper_Category_Flat */
        $categoryFlatHelper = Mage::helper('score/category_flat');
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            Mage::getResourceModel('score/category_flat')->synchronize(null, array($store->getId()));
        }
        Mage::getResourceModel('score/oggetto')->refreshEnabledIndex($store);
        return $this;
    }

    /**
     * Process score data related with store group root category
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_Score_Model_Observer
     */
    public function storeGroupSave(Varien_Event_Observer $observer)
    {
        /* @var $group Mage_Core_Model_Store_Group */
        $group = $observer->getEvent()->getGroup();
        if ($group->dataHasChangedFor('root_category_id') || $group->dataHasChangedFor('website_id')) {
            Mage::app()->reinitStores();
            foreach ($group->getStores() as $store) {
                /** @var $categoryFlatHelper Shaurmalab_Score_Helper_Category_Flat */
                $categoryFlatHelper = Mage::helper('score/category_flat');
                if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
                    Mage::getResourceModel('score/category_flat')->synchronize(null, array($store->getId()));
                }
            }
        }
        return $this;
    }

    /**
     * Process delete of store
     *
     * @param Varien_Event_Observer $observer
     * @return Shaurmalab_Score_Model_Observer
     */
    public function storeDelete(Varien_Event_Observer $observer)
    {
        /** @var $categoryFlatHelper Shaurmalab_Score_Helper_Category_Flat */
        $categoryFlatHelper = Mage::helper('score/category_flat');
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            $store = $observer->getEvent()->getStore();
            Mage::getResourceModel('score/category_flat')->deleteStores($store->getId());
        }
        return $this;
    }

    /**
     * Process score data after category move
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_Score_Model_Observer
     */
    public function categoryMove(Varien_Event_Observer $observer)
    {
        $categoryId = $observer->getEvent()->getCategoryId();
        $prevParentId = $observer->getEvent()->getPrevParentId();
        $parentId = $observer->getEvent()->getParentId();
        /** @var $categoryFlatHelper Shaurmalab_Score_Helper_Category_Flat */
        $categoryFlatHelper = Mage::helper('score/category_flat');
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            Mage::getResourceModel('score/category_flat')->move($categoryId, $prevParentId, $parentId);
        }
        return $this;
    }

    /**
     * Process score data after oggettos import
     *
     * @param   Varien_Event_Observer $observer
     * @return  Shaurmalab_Score_Model_Observer
     */
    public function scoreOggettoImportAfter(Varien_Event_Observer $observer)
    {
        Mage::getModel('score/url')->refreshRewrites();
        Mage::getResourceSingleton('score/category')->refreshOggettoIndex();
        return $this;
    }

    /**
     * Score Oggetto Compare Items Clean
     *
     * @param Varien_Event_Observer $observer
     * @return Shaurmalab_Score_Model_Observer
     */
    public function scoreOggettoCompareClean(Varien_Event_Observer $observer)
    {
        Mage::getModel('score/oggetto_compare_item')->clean();
        return $this;
    }

    /**
     * After save event of category
     *
     * @param Varien_Event_Observer $observer
     * @return Shaurmalab_Score_Model_Observer
     */
    public function categorySaveAfter(Varien_Event_Observer $observer)
    {
        /** @var $categoryFlatHelper Shaurmalab_Score_Helper_Category_Flat */
        $categoryFlatHelper = Mage::helper('score/category_flat');
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            $category = $observer->getEvent()->getCategory();
            Mage::getResourceModel('score/category_flat')->synchronize($category);
        }
        return $this;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param Varien_Event_Observer $observer
     * @return Shaurmalab_Score_Model_Observer
     */
    public function scoreCheckIsUsingStaticUrlsAllowed(Varien_Event_Observer $observer)
    {
        $storeId = $observer->getEvent()->getData('store_id');
        $result  = $observer->getEvent()->getData('result');
        $result->isAllowed = Mage::helper('score')->setStoreId($storeId)->isUsingStaticUrlsAllowed();
    }

    /**
     * Cron job method for oggetto prices to reindex
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function reindexOggettoPrices(Mage_Cron_Model_Schedule $schedule)
    {
        $indexProcess = Mage::getSingleton('index/indexer')->getProcessByCode('score_oggetto_price');
        if ($indexProcess) {
            $indexProcess->reindexAll();
        }
    }

    /**
     * Adds score categories to top menu
     *
     * @param Varien_Event_Observer $observer
     */
    public function addScoreToTopmenuItems(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        $block->addCacheTag(Shaurmalab_Score_Model_Category::CACHE_TAG);
        $this->_addCategoriesToMenu(
            Mage::helper('score/category')->getStoreCategories(), $observer->getMenu(), $block, true
        );
    }

    /**
     * Recursively adds categories to top menu
     *
     * @param Varien_Data_Tree_Node_Collection|array $categories
     * @param Varien_Data_Tree_Node $parentCategoryNode
     * @param Mage_Page_Block_Html_Topmenu $menuBlock
     * @param bool $addTags
     */
    protected function _addCategoriesToMenu($categories, $parentCategoryNode, $menuBlock, $addTags = false)
    {
        $categoryModel = Mage::getModel('score/category');
        foreach ($categories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }

            $nodeId = 'category-node-' . $category->getId();

            $categoryModel->setId($category->getId());
            if ($addTags) {
                $menuBlock->addModelTags($categoryModel);
            }

            $tree = $parentCategoryNode->getTree();
            $categoryData = array(
                'name' => $category->getName(),
                'id' => $nodeId,
                'url' => Mage::helper('score/category')->getCategoryUrl($category),
                'is_active' => $this->_isActiveMenuCategory($category)
            );
            $categoryNode = new Varien_Data_Tree_Node($categoryData, 'id', $tree, $parentCategoryNode);
            $parentCategoryNode->addChild($categoryNode);

            $flatHelper = Mage::helper('score/category_flat');
            if ($flatHelper->isEnabled() && $flatHelper->isBuilt(true)) {
                $subcategories = (array)$category->getChildrenNodes();
            } else {
                $subcategories = $category->getChildren();
            }

            $this->_addCategoriesToMenu($subcategories, $categoryNode, $menuBlock, $addTags);
        }
    }

    /**
     * Checks whether category belongs to active category's path
     *
     * @param Varien_Data_Tree_Node $category
     * @return bool
     */
    protected function _isActiveMenuCategory($category)
    {
        $scoreLayer = Mage::getSingleton('score/layer');
        if (!$scoreLayer) {
            return false;
        }

        $currentCategory = $scoreLayer->getCurrentCategory();
        if (!$currentCategory) {
            return false;
        }

        $categoryPathIds = explode(',', $currentCategory->getPathInStore());
        return in_array($category->getId(), $categoryPathIds);
    }

    /**
     * Checks whether attribute_code by current module is reserved
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Core_Exception
     */
    public function checkReservedAttributeCodes(Varien_Event_Observer $observer)
    {
        /** @var $attribute Shaurmalab_Score_Model_Oggetto_Attribute */
        $attribute = $observer->getEvent()->getAttribute();
        if (!is_object($attribute)) {
            return;
        }
        /** @var $oggetto Shaurmalab_Score_Model_Oggetto */
        $oggetto = Mage::getModel('score/oggetto');
        if ($oggetto->isReservedAttribute($attribute)) {
           // throw new Mage_Core_Exception(
           //     Mage::helper('score')->__('The attribute code \'%s\' is reserved by system. Please try another attribute code', $attribute->getAttributeCode())
           // );
        }
    }

    public function syncApis(Varien_Event_Observer $observer) {
        // TODO: run only 1 time formats?
     	$oggetto  = $observer->getEvent()->getData('oggetto');
        $url = Mage::getBaseUrl().'cron/eachoggetto.php';
        $params = array('id' => $oggetto->getId());
        $url .= '?' . http_build_query($params);
        Mage::log('request to '.$url,null,'curl.log');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        $error_no = curl_errno($ch);
        curl_close($ch);
        return true;

}

    public function addAttributesFromTemplates($observer) {
    // Commented part of function is very slow ((
        $event = $observer->getEvent();
        $collection = $event->getCollection();
//        $collection->addAttributeToSelect('*');
       /* $dataTypes = $collection->getColumnValues('attribute_set_id');
       // print_r(array_unique($dataTypes)); die;

        $templates = Mage::getModel('dcontent/templates') -> getCollection()
        ->addFieldToFilter('type', array('in'=>array_unique($dataTypes)))
        ->addFieldToFilter('kind', array('in'=>array('list','grid','mylist','mygrid')));
        if($templates) {
          $attributes = array();
          foreach($templates as $template) {
            while (preg_match_all('/^(.*)\[\[([a-zA-Z0-9\.\(\)\ \-\_]*)\]\](.*)/i', $template->getProduct(), $matches)) {
                  foreach ($matches[2] as $attribute_text) {
                    if (preg_match_all('/^(.*)\.format\((.*)\)/i', $attribute_text, $format)) {
                      $attributes[] = trim($format[1][0]);
                    } else {
                      $attributes[] = $attribute_text;
                    }
                  }
            }
          }
        $collection->addAttributeToSelect($attributes);
      }*/
      return $collection;
    }
}
