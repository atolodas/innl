<?php
class Snowcommerce_Seo_Block_Seo extends Mage_Core_Block_Template
{

    private $_permutations = array();

    protected function _getCurrentPageUrl()
    {
        return $_SERVER['REQUEST_URI'];
    }

	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

     public function getSeo()
     {
        if (!$this->hasData('seo')) {
            $this->setData('seo', Mage::registry('seo'));
        }
        return $this->getData('seo');

    }

protected function _construct()
    {
      $this->addData(array(
      'cache_lifetime' => 0,
      'cache_tags'     => array(rand(1,123123123)),
      'cache_key'      => rand(1,123123123),
      ));
    }

	public function getSearchedUrls() {
    	$pageURL = 'http://';  $pageURL .= $_SERVER["SERVER_NAME"] . $this->_getCurrentPageUrl();

	    $variants = array();
    	if(!is_array($pageURL)) {
    		$pageURL = array($pageURL);
    	}
    	foreach($pageURL as $url) {
    		$v = $this->buildVariants($url);
    		$variants = array_merge($variants,$v);
    	}

		$_SESSION['content_urls'] = serialize($variants);
            return $variants;
	}

    public function getPermutations($items, $perms = array( )) {
        if(count($p)>4) return;
        if (empty($items)) {
            $p = array_filter($perms,'strlen');
            if(!in_array(implode('&',$p),$this->_permutations)):
                $this->_permutations[] = implode('&',$p);
            endif;
        } else {
            for ($i = count($items) - 1; $i >= 0; --$i) {
                $newitems = $items;
                $newperms = $perms;
                list($foo) = array_splice($newitems, $i, 1);
                array_unshift($newperms, $foo);
                $this->getPermutations($newitems, $newperms);
            }
        }
    }

	protected function buildVariants($pageURL) {
        $removeParams = explode(',',Mage::getStoreConfig('seo/seo/remove_params',Mage::app()->getStore()->getId()));

        $params = parse_url($pageURL);
        $query = @$params['query'];
        if(!empty($_GET)) {
            foreach($_GET as $key=>$val) {
                if(in_array($key,$removeParams)) {
                    $query = str_replace($key.'='.$val,'',$query);
                    $query = str_replace('&&','&',$query);
                } else {
                    if(!is_array($val) && !is_array($key)) $query = str_replace($key.'='.$val,$key.'=%',$query);
                }
            }
        }
        //$this->getPermutations(explode('&',$query));

        $pageURL = explode('?',$pageURL);
        $pageURL = $pageURL[0];

		if(in_array(substr($pageURL,strlen($pageURL)-1,1),array('&','?','/'))) {
            $pageURL = substr($pageURL,0,strlen($pageURL)-1);
        }
        $url = str_replace(Mage::getBaseUrl(),'',$pageURL);
		// foreach($this->_permutations as $mutation) {
             $variants[] = '%'.$url.'%'.$pageURL[1].'%';
  //       }
       // $variants[] = '%'.$url.'*';

        return $variants;
	}


	public function getContentForCurrentPage()
    {
        if (!Mage::registry('_content_for_page')) {

            $urls = $this->getSearchedUrls();
            /* add url by first segment */
            $arr = explode('/',$this->_getCurrentPageUrl());

            $filterUrl = array();
            foreach($urls as $url) {
                $filterUrl[] = array('like'=>$url);
            }

            $filterCategory = array();
            if($category = Mage::registry('current_category')) {
            $filterCategory[] = array('like'=>$category->getId().",%");
                $filterCategory[] = array('like'=>"%,".$category->getId());
                $filterCategory[] = array('like'=>"%,".$category->getId().",%");
                $filterCategory[] = array('eq'=>$category->getId());
            }

            $productFilter = array();
            if($product = Mage::registry('current_product')) {
                $productFilter = 'product';
            }

			      $oggettoFilter = array();
            if($oggetto = Mage::registry('current_oggetto')) {
                $oggettoFilter = 'oggetto';
				        $set = $oggetto->getAttributeSetId();
            }



            $content = Mage::getModel('seo/seo')->getResourceCollection()
            ->addFieldToFilter('status',1)
            ->setOrder('priority','asc');

            if($productFilter) {
                $content->addFieldToFilter(array('type','url'),array($productFilter,$filterUrl));
            } elseif($oggettoFilter) {
                $content->addFieldToFilter('type',$oggettoFilter);
                $content->addFieldToFilter(array('oggetto_type','url'),array($set,$filterUrl));
            } elseif($filterCategory) {
                $content->addFieldToFilter(array('category','url'),array($filterCategory,$filterUrl));

            } else {
                $content->addFieldToFilter('url',$filterUrl);
            }




            if (!Mage::app()->isSingleStoreMode()) {
                $store = Mage::app()->getStore();
                $content->addFieldToFilter(
                    'store_id',
                    array(
                        array('like'=>$store->getId().',%'),
                        array('like'=>'%,'.$store->getId()),
                        array('like'=>'%,'.$store->getId().',%')
                    )
                );
            }
			//echo $content->getSelect(); die;
            $content->getSelect()->limit(1);

			if(count($content)==0) {
				if(!in_array($this->_getCurrentPageUrl(),array('','/'))) {
					/* Autosave url for seo  TODO: apply later when SEO will be improved. This way it's saving SEO data for same pages many times */
/*					$autosave = Mage::getModel('seo/seo')->load(0)
					->setUrl($this->_getCurrentPageUrl())
					->setStoreId(Mage::app()->getStore()->getId().',')
					->setPriority(9)
					->setStatus(1)
					->setCreatedTime(date('Y-m-d h:i:s'))
					->setUpdatedTime(date('Y-m-d h:i:s'))
					->setMetaTitle(Mage::app()->getLayout()->getBlock('head')->getTitle())
					->setMetaKeyword(Mage::app()->getLayout()->getBlock('head')->getKeywords())
					->setMetaDescription(Mage::app()->getLayout()->getBlock('head')->getDescription())
          ;
          if($productFilter) $autosave->setType('product');
          elseif($oggettoFilter) $autosave->setType('oggetto');
          $autosave->save();
  */        //*/
				}
			}
            Mage::register('_content_for_page', $content->getFirstItem());
        }
        return Mage::registry('_content_for_page');
	}

	public function getContent($type)
	{
        $r = Mage::app()->getRequest()->getRequestString();
		$c = $this->getContentForCurrentPage();
		if(count($c)>0) {
			  return Mage::helper('seo')->replacePatterns($c->getData($type));
		}
		else {
			return '';
		}
	}

	public function getSeoTag() {
		$seotag =  Mage::getBlockSingleton('seo/seo')->getContent('seo_tag');
        return $seotag;
	}

	public function isContentPresent() {

		$content = $this->getContentForCurrentPage();
		if(count($content)>0) {
			return true;
		}
		else {
			return false;
		}
	}


}




