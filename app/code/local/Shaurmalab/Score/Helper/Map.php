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
 * Score (site)map helper
 */
class Shaurmalab_Score_Helper_Map extends Mage_Core_Helper_Abstract
{
    CONST XML_PATH_USE_TREE_MODE = 'score/sitemap/tree_mode';

    public function getCategoryUrl()
    {
        return $this->_getUrl('score/seo_sitemap/category');
    }

    public function getOggettoUrl()
    {
        return $this->_getUrl('score/seo_sitemap/oggetto');
    }

    /**
     * Return true if category tree mode enabled
     *
     * @return boolean
     */
    public function getIsUseCategoryTreeMode()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_USE_TREE_MODE);
    }

}
