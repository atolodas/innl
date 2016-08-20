<?php

/**
 * Branko Ajzele, http://activecodeline.com
 *
 * NOTICE OF LICENSE
 *
 * @category    ActiveCodeline
 * @package     ActiveCodeline_Widget
 * @copyright   Copyright (c) 2009 Branko Ajzele
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class EM_NewsFeedWidget_Block_List extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface {

    protected function _construct() {
        $cache_time = Mage::getStoreConfig('newsfeed_section/config_group/cache');
        if (empty($cache_time)) {
            $cache_time = 30;
        }
        $expired = 60 * $cache_time;
        //======== set data magento cache ===============
        $this->addData(array(
            'cache_lifetime' => $expired,
            'cache_tags' => array(Mage_Catalog_Model_Product::CACHE_TAG),
            'cache_key' => base64_encode($this->_data['feed_link'])
        ));
        //======== end =============
    }

    protected function _toHtml() {
        $template = null;
        $template = $this->getTemplate();
        if (isset($template)) {
            $this->setTemplate($template);
        } else {
            $this->setTemplate('em_newsfeedwidget/list.phtml');
        }
        return parent::_toHtml();
    }

    public function getFeedAndSave() {
        $feed_link = $this->getFeedLink();
        if (isset($feed_link)) {
            $feed_link = sha1($feed_link);
        }
        if (isset($feed_link)) {
            $cache_time = Mage::getStoreConfig('newsfeed_section/config_group/cache');
            if (empty($cache_time)) {
                $cache_time = 30;
            }
            $expired = time();

            $feed = Mage::getModel('newsfeedwidget/newsfeedwidget');
            $feed->setLink($feed_link);
            $feed->setExpired(date('o-m-d H:i:s', $expired));
            $feed->save();
            $feed_id = $feed->getId();

            $lstItem = Zend_Feed_Reader::import($this->getFeedLink());
            //======================= rss ========================
            if ($lstItem->count() > 0) {
                foreach ($lstItem as $item) {
                    $row = Mage::getModel('newsfeedwidget/item');
                    $row->setFeedId($feed_id);
                    $row->setLink($item->getLink());
                    $row->setAuthor($item->getAuthor(0));
                    $row->setTitle($item->getTitle());
                    $row->setDescription(trim(strip_tags($item->getContent())));
                    $row->setDate(date('o-m-d H:i:s', $item->getDateCreated()->get()));

                    $img = $item->getElement()->getElementsByTagName('img')->item(0);
                    if (isset($img)) {
                        $row->setImage($img->getAttribute('src'));
                    } else {
                        $result = array();
                        preg_match('/<img[^>]+>/i', $item->getDescription(), $result);
                        if (count($result) > 0) {
                            $doc = new DOMDocument();
                            $doc->loadHTML('<body>' . $result[0] . '</body>');
                            $image = $doc->getElementsByTagName('img')->item(0);
                            $row->setImage(isset($image) ? strip_tags($image->getAttribute('src')) : '');
                        }
                    }

                    $row->save();
                }
            }
            //==================== end rss ======================
            return $feed_id;
        }
    }

    public function getFeed() {
        $feed_link = $this->getFeedLink();
        if (isset($feed_link)) {
            $feed_link = sha1($feed_link);
        }
        $count = $this->getCount();
        if (empty($count)) {
            $count = 5;
        }
        $items = null;
        if (isset($feed_link)) {
            $collection = Mage::getModel('newsfeedwidget/newsfeedwidget')->getCollection();
            $collection->addFieldToFilter('link', $feed_link);

            $feeds = $collection->load();
            //==== if don't have in database, get it ==========
            if ($feeds->count() < 1) {
                $this->getFeedAndSave();
                $collection = Mage::getModel('newsfeedwidget/newsfeedwidget')->getCollection();
                $collection->addFieldToFilter('link', $feed_link);
                $collection->setPageSize($count);
                $feeds = $collection->load();
            }
            //============== end ====================
            foreach ($feeds as $feed) {
                $col = Mage::getModel('newsfeedwidget/item')->getCollection();
                $col->addFieldToFilter('feed_id', $feed->getId());
                $col->setPageSize($count);
                $items = $col->load();
            }

            //======= check cache and update ========
            $cache_time = Mage::getStoreConfig('newsfeed_section/config_group/cache');
            if (empty($cache_time)) {
                $cache_time = 30;
            }


            foreach ($feeds as $item) {
                $expired = strtotime($item->getExpired()) + $cache_time * 60;
                $expired = date('o-m-d H:i:s', $expired);
                if ($expired < date('o-m-d H:i:s')) {
                    $item->delete();
                    $this->getFeedAndSave();
                }
            }
            //======== end check cache ===============
        }
        $desc_length = $this->getDescLength();
        if (empty($desc_length)) {
            $desc_length = 200;
        }

        $str = $this->getImageSize();
        $arr = null;
        $arr = split("x", $str);
        $img = null;
        foreach ($items as $item) {
            $date = strftime($this->getDateFormat(), strtotime($item->getDate()));
            $item->setDate($date);
            $item->setDescription(mb_substr($item->getDescription(), 0, $desc_length, 'UTF-8') . "..");

            //======== set image tag ===========
            if ($item->getImage()) {
                if (count($arr) != 2) {
                    $arr = split(";", $str);
                    if (count($arr) != 2) {
                        $arr = null;
                    }
                }
                if ($arr == null) {
                    $img = '<img src="' . $item->getImage() . '" alt="' . $item->getTitle() . '" />';
                } else {
                    $img = '<img src="' . $item->getImage() . '" alt="' . $item->getTitle() . '" width="' . $arr[0] . '" height="' . $arr[1] . '" />';
                }
                $item->setImage($img);
            }
        }
        return $items;
    }

    public function getImage() {
        $str = $this->getImageSize();
        $arr = null;
        $arr = split("x", $str);
        if (count($arr) != 2) {
            $arr = split(";", $str);
            if (count($arr) != 2) {
                $arr = null;
            }
        }
        if ($arr == null) {
            return null;
        } else {
            return $arr[$index];
        }
    }

}
