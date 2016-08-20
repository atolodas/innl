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
 * New oggettos widget
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Oggetto_Widget_New extends Shaurmalab_Score_Block_Oggetto_New
    implements Mage_Widget_Block_Interface
{
    /**
     * Display oggettos type
     */
    const DISPLAY_TYPE_ALL_OGGETTOS         = 'all_oggettos';
    const DISPLAY_TYPE_NEW_OGGETTOS         = 'new_oggettos';

    /**
     * Default value whether show pager or not
     */
    const DEFAULT_SHOW_PAGER                = false;

    /**
     * Default value for oggettos per page
     */
    const DEFAULT_OGGETTOS_PER_PAGE         = 5;

    /**
     * Name of request parameter for page number value
     */
    const PAGE_VAR_NAME                     = 'np';

    /**
     * Instance of pager block
     *
     * @var Shaurmalab_Score_Block_Oggetto_Widget_Html_Pager
     */
    protected $_pager;

    /**
     * Initialize block's cache and template settings
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addPriceBlockType('bundle', 'bundle/score_oggetto_price', 'bundle/score/oggetto/price.phtml');
    }

    /**
     * Oggetto collection initialize process
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection|Object|Varien_Data_Collection
     */
    protected function _getOggettoCollection()
    {
        switch ($this->getDisplayType()) {
            case self::DISPLAY_TYPE_NEW_OGGETTOS:
                $collection = parent::_getOggettoCollection();
                break;
            default:
                $collection = $this->_getRecentlyAddedOggettosCollection();
                break;
        }
        return $collection;
    }

    /**
     * Prepare collection for recent oggetto list
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Collection|Object|Varien_Data_Collection
     */
    protected function _getRecentlyAddedOggettosCollection()
    {
        /** @var $collection Shaurmalab_Score_Model_Resource_Oggetto_Collection */
        $collection = Mage::getResourceModel('score/oggetto_collection');
        $collection->setVisibility(Mage::getSingleton('score/oggetto_visibility')->getVisibleInScoreIds());

        $collection = $this->_addOggettoAttributesAndPrices($collection)
            ->addStoreFilter()
            ->addAttributeToSort('created_at', 'desc')
            ->setPageSize($this->getOggettosCount())
            ->setCurPage(1)
        ;
        return $collection;
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return array_merge(parent::getCacheKeyInfo(), array(
            $this->getDisplayType(),
            $this->getOggettosPerPage(),
            intval($this->getRequest()->getParam(self::PAGE_VAR_NAME))
        ));
    }

    /**
     * Retrieve display type for oggettos
     *
     * @return string
     */
    public function getDisplayType()
    {
        if (!$this->hasData('display_type')) {
            $this->setData('display_type', self::DISPLAY_TYPE_ALL_OGGETTOS);
        }
        return $this->getData('display_type');
    }

    /**
     * Retrieve how much oggettos should be displayed
     *
     * @return int
     */
    public function getOggettosCount()
    {
        if (!$this->hasData('oggettos_count')) {
            return parent::getOggettosCount();
        }
        return $this->getData('oggettos_count');
    }

    /**
     * Retrieve how much oggettos should be displayed
     *
     * @return int
     */
    public function getOggettosPerPage()
    {
        if (!$this->hasData('oggettos_per_page')) {
            $this->setData('oggettos_per_page', self::DEFAULT_OGGETTOS_PER_PAGE);
        }
        return $this->getData('oggettos_per_page');
    }

    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function showPager()
    {
        if (!$this->hasData('show_pager')) {
            $this->setData('show_pager', self::DEFAULT_SHOW_PAGER);
        }
        return (bool)$this->getData('show_pager');
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        if ($this->showPager()) {
            if (!$this->_pager) {
                $this->_pager = $this->getLayout()
                    ->createBlock('score/oggetto_widget_html_pager', 'widget.new.oggetto.list.pager');

                $this->_pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName(self::PAGE_VAR_NAME)
                    ->setLimit($this->getOggettosPerPage())
                    ->setTotalLimit($this->getOggettosCount())
                    ->setCollection($this->getOggettoCollection());
            }
            if ($this->_pager instanceof Mage_Core_Block_Abstract) {
                return $this->_pager->toHtml();
            }
        }
        return '';
    }
}
