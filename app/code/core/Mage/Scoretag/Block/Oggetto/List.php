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
 * @package     Mage_Scoretag
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Scoretag_Block_Oggetto_List extends Mage_Core_Block_Template
{
    protected $_collection;
    protected $oggettoId;
     protected $currentOggettoId;
    /**
     * Unique Html Id
     *
     * @var string
     */
    protected $_uniqueHtmlId = null;

    public function getCount()
    {
        return count($this->getScoretags());
    }

    public function getScoretags()
    {
        return $this->_getCollection()->getItems();
    }

    public function getCurrentOggettoId()
    {
        return $this->currentOggettoId;
    }

    public function setCurrentOggettoId($id)
    {
        $this->currentOggettoId = $id;
        return $this;
    }

    public function setOggettoId($oggettoId)
    {
        $this->oggettoId = $oggettoId;
        return $this;
    }


    public function getOggettoId()
    {
        if($this->oggettoId) {
          return $this->oggettoId;
        } elseif ($oggetto = Mage::registry('current_oggetto')) {
            return $oggetto->getId();
        }
        return false;
    }

    protected function _getCollection()
    {

        $this->_collection = new Varien_Data_Collection();
        if( $this->getOggettoId() ) {

            $model = Mage::getModel('scoretag/scoretag');
            $this->_collection = $model->getResourceCollection()
                ->addPopularity()
                ->addStatusFilter($model->getApprovedStatus())
                ->addOggettoFilter($this->getOggettoId())
                ->setFlag('relation', true)
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->setActiveFilter()
                ->load();

        }
        return $this->_collection;
    }

    protected function _beforeToHtml()
    {
        if (!$this->getOggettoId()) {
            return false;
        }

        return parent::_beforeToHtml();
    }

    public function getFormAction()
    {
        return Mage::getUrl('scoretag/index/save', array(
            'oggetto' => $this->getOggettoId(),
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => Mage::helper('core/url')->getEncodedUrl()
        ));
    }

    /**
     * Render scoretags by specified pattern and implode them by specified 'glue' string
     *
     * @param string $pattern
     * @param string $glue
     * @return string
     */
    public function renderScoretags($pattern, $glue = ' ')
    {
        $out = array();
        $tags = $this->getScoretags();
        if(is_array($tags) && !empty($tags)) { 
            foreach ($this->getScoretags() as $scoretag) {
                $out[] = sprintf($pattern,
                    $scoretag->getScoretaggedOggettosUrl(), $this->escapeHtml($scoretag->getName()), $scoretag->getOggettos()
                );
            }
        }
        return implode($out, $glue);
    }

    /**
     * Render scoretags by specified pattern and implode them by specified 'glue' string
     *
     * @param string $pattern
     * @param string $glue
     * @return string
     */
    public function renderScoretagsShort($pattern, $glue = ' ')
    {
        $out = array();
        if(is_array($this->getScoretags())) {
            foreach ($this->getScoretags() as $scoretag) {
                $out[] = sprintf($pattern,
                    $scoretag->getScoretaggedOggettosUrl(), $this->escapeHtml($scoretag->getName()), $scoretag->getOggettos()
                );
                if(count($out)==5) break;
            }
        }
        return implode($out, $glue);
    }

    /**
     * Generate unique html id
     *
     * @param string $prefix
     * @return string
     */
    public function getUniqueHtmlId($prefix = '')
    {
        if (is_null($this->_uniqueHtmlId)) {
            $this->_uniqueHtmlId = Mage::helper('core/data')->uniqHash($prefix);
        }
        return $this->_uniqueHtmlId;
    }
}
