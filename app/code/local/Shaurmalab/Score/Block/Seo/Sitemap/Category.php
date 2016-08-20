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
 * SEO Categories Sitemap block
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Seo_Sitemap_Category extends Shaurmalab_Score_Block_Seo_Sitemap_Abstract
{

    /**
     * Initialize categories collection
     *
     * @return Shaurmalab_Score_Block_Seo_Sitemap_Category
     */
    protected function _prepareLayout()
    {
        $helper = Mage::helper('score/category');
        /* @var $helper Shaurmalab_Score_Helper_Category */
        $collection = $helper->getStoreCategories('name', true, false);
        $this->setCollection($collection);
        return $this;
    }

    /**
     * Get item URL
     *
     * @param Shaurmalab_Score_Model_Category $category
     * @return string
     */
    public function getItemUrl($category)
    {
        $helper = Mage::helper('score/category');
        /* @var $helper Shaurmalab_Score_Helper_Category */
        return $helper->getCategoryUrl($category);
    }

}
