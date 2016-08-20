<?php
/**
 * IDEALIAGroup srl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.idealiagroup.com/magento-ext-license.html
 *
 * @category   IG
 * @package    IG_404ToSearch
 * @copyright  Copyright (c) 2012 IDEALIAGroup srl (http://www.idealiagroup.com)
 * @license    http://www.idealiagroup.com/magento-ext-license.html
 */

require_once "Mage/Cms/controllers/IndexController.php";
class IG_404ToSearch_IndexController extends Mage_Cms_IndexController
{
	/**
     * Render CMS 404 Not found page
     *
     * @param string $coreRoute
     */
    public function noRouteAction($coreRoute = null)
    {
    	$helper = Mage::helper('ig_404tosearch');

$this->getResponse()->setHeader('HTTP/1.1','200 OK');
$this->getResponse()->setHeader('Status','200 OK');
    	if (!$helper->getIsEnabled())
			return parent::noRouteAction($coreRoute);

		$urlInfo = parse_url($_SERVER['REQUEST_URI']);


    $path = urldecode($urlInfo['path']);

    $qs = $path; //$helper->getQueryString($path, $_REQUEST);

    if(substr_count($qs,'product')) {
      $module = 'catalog';
      $entity = 'product';
    } else {
      $module = 'score';
      $entity = 'oggetto';
    }
    $qs = str_replace(array('/product/','/oggetto/','.html'),'',$qs);
    $canonical_path = $qs;
    $qs = str_replace(array('/','+','_','-','"',"'"),' ',$qs);


    $queryHelper = Mage::helper($module.'search');


    $this->getRequest()->setParam($queryHelper->getQueryParamName(), $qs);

      $query = $queryHelper->getQuery();
      $query->setStoreId(Mage::app()->getStore()->getId());

        if ($query->getQueryText()) {
            if (Mage::helper($module.'search')->isMinQueryLength()) {
                $query->setId(0)
                    ->setIsActive(1)
                    ->setIsProcessed(1);
            } else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity()+1);
                } else {
                    $query->setPopularity(1);
                }
                if ($query->getRedirect()) {

                    $query->save();
                    $this->getResponse()->setRedirect($query->getRedirect());
                    return;
                } else {
                    $query->prepare();
                }
            }

            Mage::helper($module.'search')->checkNotes();

			$this->getResponse()->setHttpResponseCode(404);

        //$this->loadLayout();
        $this->loadLayout(
                    array(
                        'default',
                        $module.'search_result_index'
                    )
                );

            $this->getLayout()->getBlock('head')->setTitle($helper->getTitle());
            $this->getLayout()->getBlock('head')->setKeywords($entity.'s,'.implode(',',explode(' ',$qs)));
            $this->getLayout()->getBlock('head')->setDescription($helper->getTitle().' '.$entity.'s, '.implode(',',explode(' ',$qs)));
            $includes =  $this->getLayout()->getBlock('head')->getIncludes();

            $includes.='<link rel="canonical" href="'.(Mage::getBaseUrl()).$entity.'/'.$canonical_path.'.html" />';
            $this->getLayout()->getBlock('head')->setIncludes($includes);

            $this->_initLayoutMessages($module.'/session');
            $this->_initLayoutMessages($module.'/session');

            $this->_initLayoutMessages('customer/session');
            $this->renderLayout();

            if (!Mage::helper($module.'search')->isMinQueryLength())
			      {
                $query->save();
            }
        } else {
            $this->_redirectReferer();
        }
    }
}
