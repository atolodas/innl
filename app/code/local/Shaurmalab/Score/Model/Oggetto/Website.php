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
 * Score Oggetto Website Model
 *
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Website _getResource()
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Website getResource()
 * @method int getWebsiteId()
 * @method Shaurmalab_Score_Model_Oggetto_Website setWebsiteId(int $value)
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Website extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_website');
    }

    /**
     * Retrieve Resource instance wrapper
     *
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Website
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Removes oggettos from websites
     *
     * @param array $websiteIds
     * @param array $oggettoIds
     * @return Shaurmalab_Score_Model_Oggetto_Website
     */
    public function removeOggettos($websiteIds, $oggettoIds)
    {
        try {
            $this->_getResource()->removeOggettos($websiteIds, $oggettoIds);
        }
        catch (Exception $e) {
            Mage::throwException(
                Mage::helper('score')->__('An error occurred while removing oggettos from websites.')
            );
        }
        return $this;
    }

    /**
     * Add oggettos to websites
     *
     * @param array $websiteIds
     * @param array $oggettoIds
     * @return Shaurmalab_Score_Model_Oggetto_Website
     */
    public function addOggettos($websiteIds, $oggettoIds)
    {
        try {
            $this->_getResource()->addOggettos($websiteIds, $oggettoIds);
        }
        catch (Exception $e) {
            Mage::throwException(
                Mage::helper('score')->__('An error occurred while adding oggettos to websites.')
            );
        }
        return $this;
    }

    /**
     * Retrieve oggetto websites
     * Return array with key as oggetto ID and value array of websites
     *
     * @param int|array $oggettoIds
     * @return array
     */
    public function getWebsites($oggettoIds)
    {
        return $this->_getResource()->getWebsites($oggettoIds);
    }
}
