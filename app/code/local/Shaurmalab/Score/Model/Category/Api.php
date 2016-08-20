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
 * Score category api
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Category_Api extends Shaurmalab_Score_Model_Api_Resource
{
    public function __construct()
    {
        $this->_storeIdSessionField = 'category_store_id';
    }

    /**
     * Retrieve level of categories for category/store view/website
     *
     * @param string|int|null $website
     * @param string|int|null $store
     * @param int|null $categoryId
     * @return array
     */
    public function level($website = null, $store = null, $categoryId = null)
    {
        $ids = array();
        $storeId = Shaurmalab_Score_Model_Category::DEFAULT_STORE_ID;

        // load root categories of website
        if (null !== $website) {
            try {
                $website = Mage::app()->getWebsite($website);
                if (null === $store) {
                    if (null === $categoryId) {
                        foreach ($website->getStores() as $store) {
                            /* @var $store Mage_Core_Model_Store */
                            $ids[] = $store->getRootCategoryId();
                        }
                    } else {
                        $ids = $categoryId;
                    }
                } elseif (in_array($store, $website->getStoreIds())) {
                    $storeId = Mage::app()->getStore($store)->getId();
                    $ids = (null === $categoryId)? $store->getRootCategoryId() : $categoryId;
                } else {
                    $this->_fault('store_not_exists');
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('website_not_exists', $e->getMessage());
            }
        }
        elseif (null !== $store) {
            // load children of root category of store
            if (null === $categoryId) {
                try {
                    $store = Mage::app()->getStore($store);
                    $storeId = $store->getId();
                    $ids = $store->getRootCategoryId();
                } catch (Mage_Core_Model_Store_Exception $e) {
                    $this->_fault('store_not_exists');
                }
            }
            // load children of specified category id
            else {
                $storeId = $this->_getStoreId($store);
                $ids = (int)$categoryId;
            }
        }
        // load all root categories
        else {
            $ids = (null === $categoryId)? Shaurmalab_Score_Model_Category::TREE_ROOT_ID : $categoryId;
        }

        $collection = Mage::getModel('score/category')->getCollection()
            ->setStoreId($storeId)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');

        if (is_array($ids)) {
            $collection->addFieldToFilter('entity_id', array('in' => $ids));
        } else {
            $collection->addFieldToFilter('parent_id', $ids);
        }

        // Only basic category data
        $result = array();
        foreach ($collection as $category) {
            /* @var $category Shaurmalab_Score_Model_Category */
            $result[] = array(
                'category_id' => $category->getId(),
                'parent_id'   => $category->getParentId(),
                'name'        => $category->getName(),
                'is_active'   => $category->getIsActive(),
                'position'    => $category->getPosition(),
                'level'       => $category->getLevel()
            );
        }

        return $result;
    }

    /**
     * Retrieve category tree
     *
     * @param int $parent
     * @param string|int $store
     * @return array
     */
    public function tree($parentId = null, $store = null)
    {
        if (is_null($parentId) && !is_null($store)) {
            $parentId = Mage::app()->getStore($this->_getStoreId($store))->getRootCategoryId();
        } elseif (is_null($parentId)) {
            $parentId = 1;
        }

        /* @var $tree Shaurmalab_Score_Model_Resource_Eav_Mysql4_Category_Tree */
        $tree = Mage::getResourceSingleton('score/category_tree')
            ->load();

        $root = $tree->getNodeById($parentId);

        if($root && $root->getId() == 1) {
            $root->setName(Mage::helper('score')->__('Root'));
        }

        $collection = Mage::getModel('score/category')->getCollection()
            ->setStoreId($this->_getStoreId($store))
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');

        $tree->addCollectionData($collection, true);

        return $this->_nodeToArray($root);
    }

    /**
     * Convert node to array
     *
     * @param Varien_Data_Tree_Node $node
     * @return array
     */
    protected function _nodeToArray(Varien_Data_Tree_Node $node)
    {
        // Only basic category data
        $result = array();
        $result['category_id'] = $node->getId();
        $result['parent_id']   = $node->getParentId();
        $result['name']        = $node->getName();
        $result['is_active']   = $node->getIsActive();
        $result['position']    = $node->getPosition();
        $result['level']       = $node->getLevel();
        $result['children']    = array();

        foreach ($node->getChildren() as $child) {
            $result['children'][] = $this->_nodeToArray($child);
        }

        return $result;
    }

    /**
     * Initilize and return category model
     *
     * @param int $categoryId
     * @param string|int $store
     * @return Shaurmalab_Score_Model_Category
     */
    protected function _initCategory($categoryId, $store = null)
    {
        $category = Mage::getModel('score/category')
            ->setStoreId($this->_getStoreId($store))
            ->load($categoryId);

        if (!$category->getId()) {
            $this->_fault('not_exists');
        }

        return $category;
    }

    /**
     * Retrieve category data
     *
     * @param int $categoryId
     * @param string|int $store
     * @param array $attributes
     * @return array
     */
    public function info($categoryId, $store = null, $attributes = null)
    {
        $category = $this->_initCategory($categoryId, $store);

        // Basic category data
        $result = array();
        $result['category_id'] = $category->getId();

        $result['is_active']   = $category->getIsActive();
        $result['position']    = $category->getPosition();
        $result['level']       = $category->getLevel();

        foreach ($category->getAttributes() as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $attributes)) {
                $result[$attribute->getAttributeCode()] = $category->getData($attribute->getAttributeCode());
            }
        }
        $result['parent_id']   = $category->getParentId();
        $result['children']           = $category->getChildren();
        $result['all_children']       = $category->getAllChildren();

        return $result;
    }

    /**
     * Create new category
     *
     * @param int $parentId
     * @param array $categoryData
     * @param int|null|string $store
     * @return int
     */
    public function create($parentId, $categoryData, $store = null)
    {
        $parent_category = $this->_initCategory($parentId, $store);

        /** @var $category Shaurmalab_Score_Model_Category */
        $category = Mage::getModel('score/category')
            ->setStoreId($this->_getStoreId($store));

        $category->addData(array('path'=>implode('/', $parent_category->getPathIds())));
        $category->setAttributeSetId($category->getDefaultAttributeSetId());

        $useConfig = array();
        foreach ($category->getAttributes() as $attribute) {
            if ($this->_isAllowedAttribute($attribute)
                && isset($categoryData[$attribute->getAttributeCode()])
            ) {
                // check whether value is 'use_config'
                $attrCode = $attribute->getAttributeCode();
                $categoryDataValue = $categoryData[$attrCode];
                if ('use_config' === $categoryDataValue ||
                    (is_array($categoryDataValue) &&
                    count($categoryDataValue) == 1 &&
                    'use_config' === $categoryDataValue[0])
                ) {
                    $useConfig[] = $attrCode;
                    $category->setData($attrCode, null);
                } else {
                    $category->setData($attrCode, $categoryDataValue);
                }
            }
        }

        $category->setParentId($parent_category->getId());

        /**
         * Proceed with $useConfig set into category model for processing through validation
         */
        if (count($useConfig) > 0) {
            $category->setData("use_post_data_config", $useConfig);
        }

        try {
            $validate = $category->validate();
            if ($validate !== true) {
                foreach ($validate as $code => $error) {
                    if ($error === true) {
                        Mage::throwException(Mage::helper('score')->__('Attribute "%s" is required.', $code));
                    } else {
                        Mage::throwException($error);
                    }
                }
            }

            $category->save();
        }
        catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $category->getId();
    }

    /**
     * Update category data
     *
     * @param int $categoryId
     * @param array $categoryData
     * @param string|int $store
     * @return boolean
     */
    public function update($categoryId, $categoryData, $store = null)
    {
        $category = $this->_initCategory($categoryId, $store);

        foreach ($category->getAttributes() as $attribute) {
            if ($this->_isAllowedAttribute($attribute)
                && isset($categoryData[$attribute->getAttributeCode()])) {
                $category->setData(
                    $attribute->getAttributeCode(),
                    $categoryData[$attribute->getAttributeCode()]
                );
            }
        }

        try {
            $validate = $category->validate();
            if ($validate !== true) {
                foreach ($validate as $code => $error) {
                    if ($error === true) {
                        Mage::throwException(Mage::helper('score')->__('Attribute "%s" is required.', $code));
                    }
                    else {
                        Mage::throwException($error);
                    }
                }
            }

            $category->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Move category in tree
     *
     * @param int $categoryId
     * @param int $parentId
     * @param int $afterId
     * @return boolean
     */
    public function move($categoryId, $parentId, $afterId = null)
    {
        $category = $this->_initCategory($categoryId);
        $parent_category = $this->_initCategory($parentId);

        // if $afterId is null - move category to the down
        if ($afterId === null && $parent_category->hasChildren()) {
            $parentChildren = $parent_category->getChildren();
            $afterId = array_pop(explode(',', $parentChildren));
        }

        if( strpos($parent_category->getPath(), $category->getPath()) === 0) {
            $this->_fault('not_moved', "Operation do not allow to move a parent category to any of children category");
        }

        try {
            $category->move($parentId, $afterId);
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_moved', $e->getMessage());
        }

        return true;
    }

    /**
     * Delete category
     *
     * @param int $categoryId
     * @return boolean
     */
    public function delete($categoryId)
    {
        if (Shaurmalab_Score_Model_Category::TREE_ROOT_ID == $categoryId) {
            $this->_fault('not_deleted', 'Cannot remove the system category.');
        }

        $category = $this->_initCategory($categoryId);

        try {
            $category->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_deleted', $e->getMessage());
        }

        return true;
    }

    /**
     * Get prduct Id from sku or from oggetto id
     *
     * @param int|string $oggettoId
     * @param  string $identifierType
     * @return int
     */
    protected function _getOggettoId($oggettoId, $identifierType = null)
    {
        $oggetto = Mage::helper('score/oggetto')->getOggetto($oggettoId, null, $identifierType);
        if (!$oggetto->getId()) {
            $this->_fault('not_exists','Oggetto not exists.');
        }
        return $oggetto->getId();
    }


    /**
     * Retrieve list of assigned oggettos to category
     *
     * @param int $categoryId
     * @param string|int $store
     * @return array
     */
    public function assignedOggettos($categoryId, $store = null)
    {
        $category = $this->_initCategory($categoryId);

        $storeId = $this->_getStoreId($store);
        $collection = $category->setStoreId($storeId)->getOggettoCollection();
        ($storeId == 0)? $collection->addOrder('position', 'asc') : $collection->setOrder('position', 'asc');;

        $result = array();

        foreach ($collection as $oggetto) {
            $result[] = array(
                'oggetto_id' => $oggetto->getId(),
                'type'       => $oggetto->getTypeId(),
                'set'        => $oggetto->getAttributeSetId(),
                'sku'        => $oggetto->getSku(),
                'position'   => $oggetto->getCatIndexPosition()
            );
        }

        return $result;
    }

    /**
     * Assign oggetto to category
     *
     * @param int $categoryId
     * @param int $oggettoId
     * @param int $position
     * @return boolean
     */
    public function assignOggetto($categoryId, $oggettoId, $position = null, $identifierType = null)
    {
        $category = $this->_initCategory($categoryId);
        $positions = $category->getOggettosPosition();
        $oggettoId = $this->_getOggettoId($oggettoId);
        $positions[$oggettoId] = $position;
        $category->setPostedOggettos($positions);

        try {
            $category->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }


    /**
     * Update oggetto assignment
     *
     * @param int $categoryId
     * @param int $oggettoId
     * @param int $position
     * @return boolean
     */
    public function updateOggetto($categoryId, $oggettoId, $position = null, $identifierType = null)
    {
        $category = $this->_initCategory($categoryId);
        $positions = $category->getOggettosPosition();
        $oggettoId = $this->_getOggettoId($oggettoId, $identifierType);
        if (!isset($positions[$oggettoId])) {
            $this->_fault('oggetto_not_assigned');
        }
        $positions[$oggettoId] = $position;
        $category->setPostedOggettos($positions);

        try {
            $category->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    /**
     * Remove oggetto assignment from category
     *
     * @param int $categoryId
     * @param int $oggettoId
     * @return boolean
     */
    public function removeOggetto($categoryId, $oggettoId, $identifierType = null)
    {
        $category = $this->_initCategory($categoryId);
        $positions = $category->getOggettosPosition();
        $oggettoId = $this->_getOggettoId($oggettoId, $identifierType);
        if (!isset($positions[$oggettoId])) {
            $this->_fault('oggetto_not_assigned');
        }

        unset($positions[$oggettoId]);
        $category->setPostedOggettos($positions);

        try {
            $category->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

} // Class Shaurmalab_Score_Model_Category_Api End
