<?php
class Snowcommerce_Seo_Model_Observer extends Mage_Core_Model_Abstract
{
    public function seoChanges($observer) {
        /** @var $_block Mage_Core_Block_Abstract */
        /*Get block instance*/
        $block = $observer->getBlock();
        /*get Block type*/
        $type = $block->getType();
        /*Check block type*/
        if ($type == 'page/html_head') {
            $block->setTitle(Mage::getBlockSingleton('seo/page_html_head')->getTitle());
            $block->setKeywords(Mage::getBlockSingleton('seo/page_html_head')->getKeywords());
            $block->setDescription(Mage::getBlockSingleton('seo/page_html_head')->getDescription());
            $block->setRobots(Mage::getBlockSingleton('seo/page_html_head')->getRobots());
            if($canonical = Mage::getBlockSingleton('seo/page_html_head')->getCanonical()) {
                $block->addLinkRel('canonical', $canonical);
            }

            $additionalHeadBlock = Mage::getBlockSingleton('seo/seo')->setTemplate('seo/head.phtml');
            $block->setChild('add_head',$additionalHeadBlock);
        }
    }
}