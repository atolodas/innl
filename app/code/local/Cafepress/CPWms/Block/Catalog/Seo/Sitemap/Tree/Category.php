<?php

class Cafepress_CPWms_Block_Catalog_Seo_Sitemap_Tree_Category extends Mage_Catalog_Block_Seo_Sitemap_Tree_Category
{
        /**
     * Prepare array of categories separated into pages
     *
     * @return Mage_Catalog_Block_Seo_Sitemap_Tree_Category
     */
    public function prepareCategoriesToPages()
    {
        $linesPerPage = Mage::getStoreConfig(self::XML_PATH_LINES_PER_PAGE);
        $tmpCollection = Mage::getModel('catalog/category')->getCollection()
            ->addIsActiveFilter()
            ->addPathsFilter($this->_storeRootCategoryPath . '/')
            ->addLevelFilter($this->_storeRootCategoryLevel + 1)
            ->addOrderField('path');
        $count = 0;
        $page = 1;
        $categories = array();
        foreach ($tmpCollection as $item) {
            $children = $item->getChildrenCount()+1;
            $this->_total += $children;
            if (($children+$count) >= $linesPerPage) {
             if($this->_total) { 
                $categories[$page][$item->getId()] = array(
                    'path' => $item->getPath(),
                    'children_count' => $this->_total
                );
    //            $page++;
                $count = 0;
                continue;
             }
            }
            if($this->_total) { 
             $categories[$page][$item->getId()] = array(
                 'path' => $item->getPath(),
                 'children_count' => $this->_total
             );
            }
            if($this->_total) { 
             $count += $children;
            }
        }
        $this->_categoriesToPages = $categories;
        return $this;
    }
}
