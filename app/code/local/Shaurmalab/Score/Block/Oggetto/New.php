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
 * New oggettos block
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Oggetto_New extends Shaurmalab_Score_Block_Oggetto_Abstract
{
    /**
     * Default value for oggettos count that will be shown
     */
    const DEFAULT_OGGETTOS_COUNT = 10;

    /**
     * Oggettos count
     *
     * @var null
     */
    protected $_oggettosCount;

    /**
     * Initialize block's cache
     */
    protected function _construct()
    {
        parent::_construct();

        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('one_column', 5)
            ->addColumnCountLayoutDepend('two_columns_left', 4)
            ->addColumnCountLayoutDepend('two_columns_right', 4)
            ->addColumnCountLayoutDepend('three_columns', 3);

        $this->addData(array('cache_lifetime' => 86400));
        $this->addCacheTag(Shaurmalab_Score_Model_Oggetto::CACHE_TAG);
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array(
           'SCORE_OGGETTO_NEW',
           Mage::app()->getStore()->getId(),
           Mage::getDesign()->getPackageName(),
           Mage::getDesign()->getTheme('template'),
           Mage::getSingleton('customer/session')->getCustomerGroupId(),
           'template' => $this->getTemplate(),
           $this->getOggettosCount()
        );
    }

    /**
     * Prepare and return oggetto collection
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection|Object|Varien_Data_Collection
     */
    protected function _getOggettoCollection()
    {
        $todayStartOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('00:00:00')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        $todayEndOfDayDate  = Mage::app()->getLocale()->date()
            ->setTime('23:59:59')
            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        /** @var $collection Shaurmalab_Score_Model_Resource_Oggetto_Collection */
        $collection = Mage::getResourceModel('score/oggetto_collection');
        $collection->setVisibility(Mage::getSingleton('score/oggetto_visibility')->getVisibleInScoreIds());


        $collection = $this->_addOggettoAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToFilter('news_from_date', array('or'=> array(
                0 => array('date' => true, 'to' => $todayEndOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter('news_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayStartOfDayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToFilter(
                array(
                    array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
                    array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
                    )
              )
            ->addAttributeToSort('news_from_date', 'desc')
            ->setPageSize($this->getOggettosCount())
            ->setCurPage(1)
        ;

        return $collection;
    }

    /**
     * Prepare collection with new oggettos
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $this->setOggettoCollection($this->_getOggettoCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Set how much oggetto should be displayed at once.
     *
     * @param $count
     * @return Shaurmalab_Score_Block_Oggetto_New
     */
    public function setOggettosCount($count)
    {
        $this->_oggettosCount = $count;
        return $this;
    }

    /**
     * Get how much oggettos should be displayed at once.
     *
     * @return int
     */
    public function getOggettosCount()
    {
        if (null === $this->_oggettosCount) {
            $this->_oggettosCount = self::DEFAULT_OGGETTOS_COUNT;
        }
        return $this->_oggettosCount;
    }
}
