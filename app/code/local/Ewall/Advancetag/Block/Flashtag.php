<?php
class Ewall_Advancetag_Block_Flashtag extends Mage_Tag_Block_Popular
{
	private $_tagsAll;//stores all tags
	protected $_minPopularity;
    protected $_maxPopularity;

	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

    protected function _loadTagsByNum($num)
    {
        if (empty($this->_tagsAll)) {
            $this->_tagsAll = array();

            $tags = Mage::getModel('tag/tag')->getPopularCollection()
                ->joinFields(Mage::app()->getStore()->getId())
                ->limit($num)
                ->load()
                ->getItems();
            if( count($tags) == 0 ) {
                return $this;
            }


            $this->_maxPopularity = reset($tags)->getPopularity();
            $this->_minPopularity = end($tags)->getPopularity();
            $range = $this->_maxPopularity - $this->_minPopularity;
            $range = ( $range == 0 ) ? 1 : $range;
            foreach ($tags as $tag) {
                if( !$tag->getPopularity() ) {
                    continue;
                }
                $tag->setRatio(($tag->getPopularity()-$this->_minPopularity)/$range);
                $this->_tagsAll[$tag->getName()] = $tag;
            }
            
            $customtags = Mage::getModel('customtags/customtags')->getCollection();
            foreach($customtags as $ctag) {
                $ctag->setRatio($ctag->getContent()/100);
                $ctag->setTaggedProductsUrl($ctag->getFilename());
                $ctag->setName($ctag->getTitle());
                $this->_tagsAll[$ctag->getTitle()] = $ctag;
			}
            ksort($this->_tagsAll);
        }
        return $this;
    }

    public function _getTags($n)
    {
        $this->_loadTagsByNum($n);
        return $this->_tagsAll;
    }    
    
    public function getMaxPopularity()
    {
        return $this->_maxPopularity;
    }

    public function getMinPopularity()
    {
        return $this->_minPopularity;
    }
	/*
     * 
     * $_type:1 is for all tags list(big blcok);2 is for small block
	 */
    public function getFlashTag($_type){
		$type='';
		if($_type==1){
			$type='';
		}else{
			$type="2";
		}
		//retrieve values of options
		$options=array();
		$options['width']=Mage::getStoreConfig("admin/ewall_advancetag{$type}/width");
		$options['height']=Mage::getStoreConfig("admin/ewall_advancetag{$type}/height");
		$options['tcolor']=Mage::getStoreConfig("admin/ewall_advancetag{$type}/color1");
		$options['tcolor2']=Mage::getStoreConfig("admin/ewall_advancetag{$type}/color2");
		$options['hicolor']=Mage::getStoreConfig("admin/ewall_advancetag{$type}/color3");
		$options['bgcolor']=Mage::getStoreConfig("admin/ewall_advancetag{$type}/color4");
		$options['speed']=Mage::getStoreConfig("admin/ewall_advancetag{$type}/speed");
		$options['type']=Mage::getStoreConfig("admin/ewall_advancetag{$type}/type");
		$options['trans']=Mage::getStoreConfig("admin/ewall_advancetag{$type}/transparent");
        $n=0;
		if($options['type']=='popular'){
            $n=20;
		}elseif($options['type']=='all'){
            $n=100;
		}
		if(Mage::getStoreConfig("admin/ewall_advancetag{$type}/distribute")){
		   $options['distr']="true";
		}else{
           $options['distr']="";
		}

        $js_path=Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS)."advancetag/";
		$divname="wpcumuluscontent";
		$soname="so";
        $tags=$this->_getTags($n);
		$tagsout='';
		$i=1;
        foreach($tags as $t){
          if($i==1){
	         $tagsout.="<a href='".$t->getTaggedProductsUrl()."' class='tag-link-".$i."' title='1 topic'  style='font-size:".($t->getRatio()*10+10)."pt;'>".$t->getName()."</a>";
		  }else{
             $tagsout.=$i."<a href='".$t->getTaggedProductsUrl()."' class='tag-link-".$i."' title='1 topic' style='font-size:".($t->getRatio()*10+10)."pt;'>".$t->getName()."</a>";
          } 
		  $i++;
		}
			$tagcloud = urldecode( str_replace( "&nbsp;", " ", $tagsout ) );	

		$movie = $js_path.'tagcloud.swf';

		$flashtag = '<!-- SWFObject embed by Geoff Stearns geoff@deconcept.com http://blog.deconcept.com/swfobject/ -->';	
		$flashtag .= '<script type="text/javascript" src="'.$js_path.'swfobject.js"></script>';
		$flashtag .= '<div id="'.$divname.'"><p style="display:none;">';
		// alternate content
		$flashtag .= urldecode($tagcloud); 
		$flashtag .= '</p><p>Flash tag cloud requires Flash Player 9 or better.</p></div>';
		$flashtag .= '<script type="text/javascript">';
		$flashtag .= 'var rnumber = Math.floor(Math.random()*9999999);'; // force loading of movie to fix IE weirdness
		$flashtag .= 'var '.$soname.' = new SWFObject("'.$movie.'?r="+rnumber, "tagcloudflash", "'.$options['width'].'", "'.$options['height'].'", "9", "#'.$options['bgcolor'].'");';
		if( $options['trans'] == 1 ){
	    	$flashtag .= $soname.'.addParam("wmode", "transparent");';
		}else{
            $flashtag .= $soname.'.addParam("wmode", "Opaque");';
		}
		$flashtag .= $soname.'.addParam("allowScriptAccess", "always");';
		$flashtag .= $soname.'.addVariable("tcolor", "0x'.$options['tcolor'].'");';
		$flashtag .= $soname.'.addVariable("tcolor2", "0x' . ($options['tcolor2'] == "" ? $options['tcolor'] : $options['tcolor2']) . '");';
		$flashtag .= $soname.'.addVariable("hicolor", "0x' . ($options['hicolor'] == "" ? $options['tcolor'] : $options['hicolor']) .	'");';
		$flashtag .= $soname.'.addVariable("tspeed", "'.$options['speed'].'");';
		$flashtag .= $soname.'.addVariable("distr", "'.$options['distr'].'");';
		$flashtag .= $soname.'.addVariable("mode", "tags");';
		// put tags in flashvar
		$flashtag .= $soname.'.addVariable("tagcloud", "'.urlencode('<tags>') . $tagcloud . urlencode('</tags>').'");';
		$flashtag .= $soname.'.write("'.$divname.'");';
		$flashtag .= '</script>';
		return $flashtag;
		}
}
