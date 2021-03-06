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
 * API2 for oggetto categories
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Api2_Oggetto_Category_Rest_Admin_V1 extends Shaurmalab_Score_Model_Api2_Oggetto_Category_Rest
{
    /**
     * Oggetto category assign
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        /* @var $validator Mage_Api2_Model_Resource_Validator_Fields */
        $validator = Mage::getResourceModel('api2/validator_fields', array('resource' => $this));
        if (!$validator->isValidData($data)) {
            foreach ($validator->getErrors() as $error) {
                $this->_error($error, Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }

        $oggetto = $this->_getOggetto();
        $category = $this->_getCategoryById($data['category_id']);

        $categoryIds = $oggetto->getCategoryIds();
        if (!is_array($categoryIds)) {
            $categoryIds = array();
        }
        if (in_array($category->getId(), $categoryIds)) {
            $this->_critical(sprintf('Oggetto #%d is already assigned to category #%d',
                $oggetto->getId(), $category->getId()), Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        if ($category->getId() == Shaurmalab_Score_Model_Category::TREE_ROOT_ID) {
            $this->_critical('Cannot assign oggetto to tree root category.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        $categoryIds[] = $category->getId();
        $oggetto->setCategoryIds(implode(',', $categoryIds));

        try{
            $oggetto->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return $this->_getLocation($category);
    }

    /**
     * Oggetto category unassign
     *
     * @return bool
     */
    protected function _delete()
    {
        $oggetto = $this->_getOggetto();
        $category = $this->_getCategoryById($this->getRequest()->getParam('category_id'));

        $categoryIds = $oggetto->getCategoryIds();
        $categoryToBeDeletedId = array_search($category->getId(), $categoryIds);
        if (false === $categoryToBeDeletedId) {
            $this->_critical(sprintf('Oggetto #%d isn\'t assigned to category #%d',
                $oggetto->getId(), $category->getId()), Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        // delete category
        unset($categoryIds[$categoryToBeDeletedId]);
        $oggetto->setCategoryIds(implode(',', $categoryIds));

        try{
            $oggetto->save();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return true;
    }

    /**
     * Return all assigned categories
     *
     * @return array
     */
    protected function _getCategoryIds()
    {
        return $this->_getOggetto()->getCategoryIds();
    }

    /**
     * Get resource location
     *
     * @param Mage_Core_Model_Abstract $resource
     * @return string URL
     */
    protected function _getLocation($resource)
    {
        /** @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
        $apiTypeRoute = Mage::getModel('api2/route_apiType');

        $chain = $apiTypeRoute->chain(new Zend_Controller_Router_Route(
            $this->getConfig()->getRouteWithEntityTypeAction($this->getResourceType())
        ));
        $params = array(
            'api_type' => $this->getRequest()->getApiType(),
            'id' => $this->getRequest()->getParam('id'),
            'category_id' => $resource->getId()
        );
        $uri = $chain->assemble($params);

        return '/' . $uri;
    }
}
