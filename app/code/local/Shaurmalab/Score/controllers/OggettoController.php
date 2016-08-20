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
 * Oggetto controller
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 */
class Shaurmalab_Score_OggettoController extends Mage_Core_Controller_Front_Action {
	/**
	 * Current applied design settings
	 *
	 * @deprecated after 1.4.2.0-beta1
	 * @var array
	 */
	protected $_designOggettoSettingsApplied = array();

	/**
	 * Initialize requested oggetto object
	 *
	 * @return Shaurmalab_Score_Model_Oggetto
	 */
	protected function _initOggetto() {
		//  $categoryId = (int) $this->getRequest()->getParam('category', false);
		$oggettoId = (int) $this->getRequest()->getParam('id');

		$params = new Varien_Object();
		//   $params->setCategoryId($categoryId);

		return Mage::helper('score/oggetto')->initOggetto($oggettoId, $this, $params);
	}

	/**
	 * Initialize oggetto view layout
	 *
	 * @param   Shaurmalab_Score_Model_Oggetto $oggetto
	 * @return  Shaurmalab_Score_OggettoController
	 */
	protected function _initOggettoLayout($oggetto) {
		Mage::helper('score/oggetto_view')->initOggettoLayout($oggetto, $this);
		return $this;
	}

	/**
	 * Recursively apply custom design settings to oggetto if it's container
	 * category custom_use_for_oggettos option is setted to 1.
	 * If not or oggetto shows not in category - applyes oggetto's internal settings
	 *
	 * @deprecated after 1.4.2.0-beta1, functionality moved to Shaurmalab_Score_Model_Design
	 * @param Shaurmalab_Score_Model_Category|Shaurmalab_Score_Model_Oggetto $object
	 * @param Mage_Core_Model_Layout_Update $update
	 */
	protected function _applyCustomDesignSettings($object, $update) {
		/* if ($object instanceof Shaurmalab_Score_Model_Category) {
		// lookup the proper category recursively
		if ($object->getCustomUseParentSettings()) {
		$parentCategory = $object->getParentCategory();
		if ($parentCategory && $parentCategory->getId() && $parentCategory->getLevel() > 1) {
		$this->_applyCustomDesignSettings($parentCategory, $update);
		}
		return;
		}

		// don't apply to the oggetto
		if (!$object->getCustomApplyToOggettos()) {
		return;
		}

		} */

		if ($this->_designOggettoSettingsApplied) {
			return;
		}

		$date = $object->getCustomDesignDate();
		if (array_key_exists('from', $date) && array_key_exists('to', $date)
			&& Mage::app()->getLocale()->isStoreDateInInterval(null, $date['from'], $date['to'])
		) {
			if ($object->getPageLayout()) {
				$this->_designOggettoSettingsApplied['layout'] = $object->getPageLayout();
			}
			$this->_designOggettoSettingsApplied['update'] = $object->getCustomLayoutUpdate();
		}
	}

	/**
	 * Oggetto view action
	 */
	public function viewAction() {
		// Get initial data from request
		//  $categoryId = (int) $this->getRequest()->getParam('category', false);
		$oggettoId = (int) $this->getRequest()->getParam('id');
		$specifyOptions = $this->getRequest()->getParam('options');

		// Prepare helper and params
		$viewHelper = Mage::helper('score/oggetto_view');

		$params = new Varien_Object();
		//  $params->setCategoryId($categoryId);
		$params->setSpecifyOptions($specifyOptions);

		// Render page
		try {
			$viewHelper->prepareAndRender($oggettoId, $this, $params);
		} catch (Exception $e) {
			//  echo $e->getMessage().' '.$this->getResponse()->getBody();
			if ($e->getCode() == $viewHelper->ERR_NO_OGGETTO_LOADED) {
				if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
					$this->_redirect('');
				} elseif (!$this->getResponse()->isRedirect()) {
					$this->_forward('noRoute');
				}
			} else {
				$this->_forward('noRoute');
			}
		}
	}

	/**
	 * View oggetto gallery action
	 */
	public function galleryAction() {
		if (!$this->_initOggetto()) {
			if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
				$this->_redirect('');
			} elseif (!$this->getResponse()->isRedirect()) {
				$this->_forward('noRoute');
			}
			return;
		}
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * View oggetto gallery action
	 */
	public function editAction() {
		if ($ogg = $this->_initOggetto()) {
			$setname = Mage::helper('score/oggetto')->getSetName($ogg);
			if ($ogg->availableForSave()) {

				$this->loadLayout();
				$this->_initLayoutMessages('score/session');
				$this->_initLayoutMessages('customer/session');
				$this->renderLayout();
			} else {
				$this->_getSession()->addError($this->__($setname . ' can not be edited by you'));
				$this->_redirectReferer();
				return;

			}
		} else {
			$this->_getSession()->addError($this->__('Something went wrong'));
			$this->_redirectReferer();
			return;

		}

	}

	/**
	 * Display oggetto image action
	 *
	 * @deprecated
	 */
	public function imageAction() {
		/*
		 * All logic has been cut to avoid possible malicious usage of the method
		 */
		$this->_forward('noRoute');
	}

	/**
	 * Initialize oggetto from request parameters
	 *
	 * @return Shaurmalab_Score_Model_oggetto
	 */
	protected function _initoggettoobj() {
		$oggettoId = (int) $this->getRequest()->getParam('id');
		if (!$oggettoId) {
			$oggettoData = $this->getRequest()->getPost();
			$oggettoId = @$oggettoData['id'];
		}

		$oggetto = Mage::helper('score/oggetto')->resetOggetto($oggettoId);

		if ($oggettoId) {
			try {
				$oggetto->load($oggettoId);
			} catch (Exception $e) {
				$oggetto->setTypeId(Shaurmalab_Score_Model_Oggetto_Type::DEFAULT_TYPE);
			}
		}

		$attributes = $this->getRequest()->getParam('attributes');
	
		Mage::dispatchEvent(
			'score_oggetto_prepare_save',
			array('oggetto' => $oggetto, 'request' => $this->getRequest())
		);
		return $oggetto;
	}

	/**
	 * Initialize oggetto before saving
	 */
	protected function _initoggettosave($data) {
		$oggetto = $this->_initoggettoobj();

		$oggettoData = $data; 
		if (isset($oggettoData['id']) && $oggettoData['id'] != 0) {
			$oggettoData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($oggettoData, $oggetto->getId());
		} else {
			$oggettoData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($oggettoData);

		}
		
		
		$wasLockedMedia = false;
		if ($oggetto->isLockedAttribute('media')) {
			$oggetto->unlockAttribute('media');
			$wasLockedMedia = true;
		}

		unset($oggettoData['id']);
		$oggetto->addData($oggettoData);

		if ($wasLockedMedia) {
			$oggetto->lockAttribute('media');
		}

		if (Mage::app()->isSingleStoreMode()) {
			$oggetto->setWebsiteIds(array(Mage::app()->getStore()->getWebsiteId()));
		}

		/**
		 * Create Permanent Redirect for old URL key
		 */
		if ($oggetto->getId() && isset($oggettoData['url_key_create_redirect'])) // && $oggetto->getOrigData('url_key') != $oggetto->getData('url_key')
		{
			$oggetto->setData('save_rewrites_history', (bool) $oggettoData['url_key_create_redirect']);
		}

		/**
		 * Initialize oggetto options
		 */
		if (isset($oggettoData['options']) && !$oggetto->getOptionsReadonly()) {
			$oggetto->setoggettoOptions($oggettoData['options']);
		}

		$oggetto->setCanSaveCustomOptions(
			(bool) $this->getRequest()->getPost('affect_oggetto_custom_options')
			&& !$oggetto->getOptionsReadonly()
		);

		Mage::dispatchEvent(
			'score_oggetto_prepare_save',
			array('oggetto' => $oggetto, 'request' => $this->getRequest())
		);
		return $oggetto;
	}

	/**
	 * Save oggetto action
	 */
	public function saveAction() {
		if (!$validate = Mage::helper('score')->checkAntiForgeryToken()) {
			$this->_getSession()->addError($this->__('Something went wrong'));
			$this->_redirectReferer();
			return;
		}

		$storeId = $this->getRequest()->getParam('store');
		$oggettoId = $this->getRequest()->getParam('id');

		$data = $this->getRequest()->getPost();

		if(!Mage::getModel('neklo_abtesting/visitor')->validateVisitor()) {
			$this->_getSession()->addError('Please, don\'t spam us');
			Mage::log('Spam message: '.implode('-', $data), null, 'spam.log');
			$this->_redirectReferer();
			return;
		}

		$visitorId = Mage::getModel('neklo_abtesting/observer')->getVisitorIdCookie();
		$data['visitor_info'] = "Visitor Id: " . $visitorId . " ||| "; 
		$visitor = Mage::getModel('neklo_abtesting/visitor')->loadByVisitorId($visitorId);
		if(is_object($visitor)) { 
			$data['visitor_info'] .= $visitor->getData('visitor_info');
		}
		
		$files = $_FILES;
		try {

			foreach ($files as $key => $file) {
				if ($file['tmp_name']) {
					$uploader = new Mage_Core_Model_File_Uploader($key);
					$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
					$uploader->addValidateCallback('catalog_product_image',
						Mage::helper('catalog/image'), 'validateUploadFile');
					$uploader->setAllowRenameFiles(true);
					$uploader->setFilesDispersion(true);
					$result = $uploader->save(
						'media/score/oggetto/'
					);

					Mage::dispatchEvent('catalog_product_gallery_upload_image_after', array(
						'result' => $result,
						'action' => $this,
					));

					$result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
					$result['path'] = str_replace(DS, "/", $result['path']);

					$data[$key] = $result['file'];
				}
			}
		} catch (Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		}

		if ($data) {
			$preinit = array();
			foreach ($data as $attr => $val) {
				$isRelated = Mage::helper('score/oggetto')->isRelatedAttribute($attr);
				$isChain = Mage::helper('score/oggetto')->isChainAttribute($attr);
				if ($isRelated && is_numeric($val) && $val != '') {
					$preinit[$attr] = $val;
				}
			}
			foreach ($data as $attr => $val) {
				//print_r($data);
				$isRelated = Mage::helper('score/oggetto')->isRelatedAttribute($attr);  // _ends with _id
				$isChain = Mage::helper('score/oggetto')->isChainAttribute($attr);   // 
			    $isDict = Mage::helper('score/oggetto')->isDictionaryAttribute($attr); // ends with _dict
            	if ($isRelated && !is_numeric($val) && $val != '') {

							// @TODO: DO NOT CREATE NEW OBJECT IF EXISTS
					$init = array_merge(array(
						'attribute_set_id' => $isRelated,
						'is_public' => '1',
						'visibility' => '4',
						'status' => '1',
						'name' => $val,
					), $preinit);
					$init = Mage::helper('score/oggetto')->modifyParamsAddDefaults($init);
					$object = new Shaurmalab_Score_Model_Oggetto();
					$object->setStoreId(Mage::app()->getStore()->getId())->setId(0)->setTypeId('simple')->addData($init)->save();
					$data[$attr] = $object->getId();
					Mage::dispatchEvent(
						'score_oggetto_create',
						array('oggetto' => $object, 'request' => $this->getRequest())
					);
				} elseif($isDict && !is_numeric($val) && $val != '') { 

							// @TODO: DO NOT CREATE NEW RECORD IN VOCABLUARY
					$resource = Mage::getSingleton('core/resource');
			        $readConnection = $resource->getConnection('core_write');
			        $binds = array(
						'value' => $val,
						'store_id'	=> Mage::app()->getStore()->getId()
					);
			        $readConnection->query("insert into {$isDict} (id,title,store_id) values (id, :value, :store_id)",$binds);
			        $elements = $readConnection->query("select id from {$isDict}  where title = :value and store_id = :store_id LIMIT 1",$binds)->fetchAll();
			        $data[$attr] = $elements[0]['id'];
				}



			}
			$oggetto = $this->_initoggettosave($data);
			$initData = $oggetto->getOrigData();
			
			try {
				$redirectBack = false;

				try {
					if ($oggetto->save()) {
						Mage::getModel('score/oggetto_status')->updateOggettoStatus($oggetto->getId(), 0, 1);
						Mage::getModel('score/oggetto_status')->updateOggettoStatus($oggetto->getId(), Mage::app()->getStore()->getId(), 1);
						Mage::getSingleton('score/oggetto_action')
							->updateAttributes(array($oggetto->getId()), array('visibility' => 4), 0);
						Mage::getSingleton('score/oggetto_action')
							->updateAttributes(array($oggetto->getId()), array('visibility' => 4), Mage::app()->getStore()->getId());

						Mage::getModel('scoresearch/fulltext')->rebuildIndex(Mage::app()->getStore()->getId(), array($oggetto->getId()));
					
						$urlModel = Mage::getSingleton('score/url');
				        $urlModel->refreshOggettoRewrite($oggetto->getId(), Mage::app()->getStore()->getId());
         

						$setname = Mage::helper('score/oggetto')->getSetName($oggetto);
						if (!$oggettoId) {
							Mage::dispatchEvent(
								'score_oggetto_create',
								array('oggetto' => $oggetto, 'request' => $this->getRequest())
							);
						} else {
							Mage::dispatchEvent(
								'score_oggetto_update',
								array('oggetto' => $oggetto, 'request' => $this->getRequest())
							);
						}
						$addMessage = '';

						$oggettoId = $oggetto->getId();

						foreach ($data as $attr => $val) {
							$isRelated = Mage::helper('score/oggetto')->isRelatedAttribute($attr);  // _ends with _id
							if($isRelated && is_numeric($val)) { 
								$newParentId = $val;
								$oldParentId = (isset($initData[$attr])?$initData[$attr]:0);
							
								if($oldParentId) { 
									if($newParentId != $oldParentId) { 
										$oldParent = Mage::getModel('score/oggetto')->load($oldParentId);
										$related = $oldParent->getRelatedOggettoIds();
										unset($related[array_search($oggettoId, $related)]);
										$related = implode('=&', $related) . '=';
										$oldParent->setRelatedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($related))->save();
										$this->_getSession()->addSuccess($oggetto->getName().' removed from '.$oldParent->getName());
									}
								}

								$newParent = Mage::getModel('score/oggetto')->load($newParentId);
								$related = $newParent->getRelatedOggettoIds();
								$related[] = $oggettoId;
								$related = array_unique($related);
								$related = implode('=&', $related) . '=';
								$newParent->setRelatedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($related))->save();
							//	$this->_getSession()->addSuccess($this->__($oggetto->getName().' added to '.$newParent->getName()));
							}
						}

						if ($parentId = $this->getRequest()->getParam('parent_id')) {
							$parent = Mage::getModel('score/oggetto')->load($parentId);
							$related = $parent->getRelatedOggettoIds();
							$related[] = $oggetto->getId();
							$related = implode('=&', $related) . '=';
							$parent->setRelatedLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($related))->save();

							$oggetto = $parent;

							if ($oggetto->getUrlPath()) {
								$this->_getSession()->addSuccess($this->__($setname . ' has been saved and available at <a href="' . Mage::getBaseUrl() . $oggetto->getUrlPath() . '">' . Mage::getBaseUrl() . $oggetto->getUrlPath() . '</a>'));
							} else {
								$this->_getSession()->addSuccess($this->__($setname . ' has been saved. '));
							}

						} else {
							$oggetto = Mage::getModel('score/oggetto')->getCollection()
							->addAttributeToFilter('entity_id', $oggettoId)
							->addAttributeToSelect('url_key')
							->addAttributeToSelect('title')->getFirstItem();
							if ($oggetto->getUrlKey()) {
								$this->_getSession()->addSuccess($this->__($setname . ' has been saved and available at <a href="' . Mage::getBaseUrl() . $oggetto->getUrlKey() . '.html">' . Mage::getBaseUrl() . $oggetto->getUrlKey() . '.html</a>'));
							} else {
								if ($oggetto->getTitle()) {
									$this->_getSession()->addSuccess($this->__(strtolower($setname) . ' has been saved. '));
								} else {
									Mage::getSingleton('core/session')->addSuccess($this->__($setname . ' has been saved.'));
								}
							}
							if ($addMessage) {
								$this->_getSession()->addSuccess($addMessage);
							}
						}

					} else {
						$this->_getSession()->addError('Can not save results');
					}
				} catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			} catch (Mage_Core_Exception $e) {
				$this->_getSession()->addError($e->getMessage())
				     ->setoggettoData($data);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}

		$this->_redirectReferer();
		return;
	}

	public function deleteAction() {
		$oggettoId = $this->getRequest()->getParam('id');

		$ogg = Mage::getModel('score/oggetto')->load($oggettoId);
		$setname = Mage::helper('score/oggetto')->getSetName($ogg);
		$title = $ogg->getTitle();
		try {
			if ($ogg->availableForSave()) {
				$ogg->delete();
				$this->_getSession()->addSuccess($this->__($setname . ' has been deleted.'));
			} else {
				$this->_getSession()->addError($this->__($setname . ' can not be deleted by you'));

			}
		} catch (Exception $e) {
			$this->_getSession()->addError($this->__('Object ' . Mage::getBaseUrl() . $ogg->getUrlPath() . ' NOT deleted. ' . $e->getMessage()));
		}
		$this->_redirectReferer();
	}

	public function repinAction() {
		$oggettoId = $this->getRequest()->getParam('id');

		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$ogg = Mage::getModel('score/oggetto')->load($oggettoId);
			$setname = Mage::helper('score/oggetto')->getSetName($ogg);
			try {
				$newOwner = Mage::getSingleton('customer/session')->getCustomer()->getId();
				$storeId = $ogg->getStoreId();
				$newOgg = Mage::helper('score/oggetto')->resetOggetto();
				$oggettoData = Mage::helper('score/oggetto')->modifyParamsAddDefaults($ogg->getData(), $newOgg->getId());
				unset($oggettoData['entity_id']);
				$newOgg->setData($oggettoData);

				$newOgg->setStoreId(0);
				$newOgg->setSku($ogg->getSku() . '-' . $newOwner);
				$newOgg->setOwner($newOwner);
				$newOgg->setStatus(1);
				$newOgg->setIsPublic(0); // repin is always private to not have duplicates
				$newOgg->setVisibility(4);
				$newOgg->setName($ogg->getName() . ' (copy)');
				if ($ogg->getUrlKey()) {
					$newOgg->setUrlKey('copy-' . $ogg->getUrlKey());
					$newOgg->setUrlPath('copy-' . $ogg->getUrlPath());
				}
				Mage::dispatchEvent(
					'score_oggetto_prepare_save',
					array('oggetto' => $newOgg, 'request' => $this->getRequest())
				);
				$newOgg->save();
				Mage::dispatchEvent(
					'score_oggetto_save',
					array('oggetto' => $newOgg, 'request' => $this->getRequest())
				);

				$this->_getSession()->addSuccess($this->__($setname . ' was copied to your list'));

			} catch (Exception $e) {
				$this->_getSession()->addError($this->__($setname . ' was not copied. ' . $e->getMessage()));
			}
			$this->_redirectReferer();
		} else {
			Mage::getSingleton('customer/session')->setBeforeAuthUrl('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			$url = Mage::helper('adminhtml')->getUrl('customer/account/login', array(''));
			Mage::app()->getFrontController()->getResponse()
			           ->setRedirect($url)
			           ->sendResponse();

		}
	}

	public function freemeAction() {
		$oggettoId = $this->getRequest()->getParam('id');

		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$ogg = Mage::getModel('score/oggetto')->load($oggettoId);
			$setname = Mage::helper('score/oggetto')->getSetName($ogg);
			try {

				$ogg->setOwner(0);
				$ogg->setIsPublic(1);
				$ogg->save();

				$this->_getSession()->addSuccess($this->__($setname . ' is free now'));

			} catch (Exception $e) {
				$this->_getSession()->addError($this->__($setname . ' was not freed. ' . $e->getMessage()));
			}
			$this->_redirectReferer();
		} else {
			Mage::getSingleton('customer/session')->setBeforeAuthUrl('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			$url = Mage::helper('adminhtml')->getUrl('customer/account/login', array(''));
			Mage::app()->getFrontController()->getResponse()
			           ->setRedirect($url)
			           ->sendResponse();

		}
	}

	public function saveAttributeAction() {
		$oggettoId = $this->getRequest()->getParam('id');
		$attribute_code = $this->getRequest()->getParam('attribute_code');
		$value = $this->getRequest()->getParam('value');
		try {

			$ogg = Mage::getModel('score/oggetto')->load($oggettoId);
			if ($value == 'true') {
				$value = 1;
			}

			if ($value == 'false') {
				$value = 0;
			}

			if ($ogg->availableForSave()) {

				if ($attribute_code == 'activity_status' && $value == 1) {
					$ogg->setExpDate(date('Y-m-d 00:00:00'));
				} elseif ($attribute_code == 'activity_status' && $value == 0) {
					$ogg->setExpDate('');
				}
				$ogg->setData($attribute_code, $value)->save();
				echo 'Success';
			} else {
				echo 'Fail';
			}
		} catch (Exception $e) {
			echo 'Fail';
		}
	}

	public function applyCustomerAction() {
		$oggettoId = $this->getRequest()->getParam('id');
		$isAjax = $this->getRequest()->getParam('isAjax');
		$attribute_code = $this->getRequest()->getParam('attribute_code');

		if (!(Mage::helper( 'customer' )->isLoggedIn())) {
			$this->_getSession()->addError('Only registered users can apply here.');
		} else {

			$value =  Mage::getSingleton('customer/session')->getCustomerId();
			try {

				$ogg = Mage::getModel('score/oggetto')->load($oggettoId);
				$data = explode('|', $ogg->getData($attribute_code));

				if (in_array($value, $data)) {
					unset($data[array_search($value, $data)]);
					$_counter = $ogg->getData($attribute_code.'_counter');
					$_counter--;
					$data = str_replace('||', '|',  implode('|', $data));
					$ogg->setData($attribute_code, $data)
						->setData($attribute_code.'_counter', $_counter)
					->save();
					if(!$isAjax) $this->_getSession()->addSuccess('Vote removed');
					else echo "Vote removed";
				} else {
					$data[] = $value;
					$_counter = $ogg->getData($attribute_code.'_counter');
					$_counter++;
					$data = str_replace('||', '|',  implode('|', $data));
					$ogg->setData($attribute_code, $data)
					->setData($attribute_code.'_counter', $_counter)
					->save();
					if(!$isAjax) $this->_getSession()->addSuccess('Vote added');
					else echo "Vote added";
				}
			} catch (Exception $e) {
				echo 'Fail';
			}
		}
	}

	private function _getSession() {
		return Mage::getSingleton('core/session');
	}

	public function gridAction() {
		//$this->loadLayout();

		$this->getResponse()->setBody(Mage::app()->getLayout()->createBlock('score/oggetto_allgrid')->setData(Mage::app()->getRequest()->getParams())->setTemplate('score/oggetto/grid.phtml')->toHtml());
	}

	public function onegridAction() {
		//$this->loadLayout();

		$this->getResponse()->setBody(Mage::app()->getLayout()->createBlock('score/oggetto_onegrid')->setData(Mage::app()->getRequest()->getParams())->setTemplate('score/oggetto/onegrid.phtml')->toHtml());
	}

	public function selectgridAction() {
		//$this->loadLayout();
		$this->getResponse()->setBody(Mage::app()->getLayout()->createBlock('score/oggetto_selectgrid')->setData(Mage::app()->getRequest()->getParams())->setTemplate('score/oggetto/grid.phtml')->toHtml());
	}

	public function getCollectionAction() {
		$params = $this->getRequest()->getParams();
		$collection = Mage::getModel('score/oggetto')->getCollection()->addAttributeToFilter('attribute_set_id', $params['attribute_set_id'])->addAttributeToSelect(array('title', 'region_id', 'location_id'), 'left');

		foreach ($params as $k => $p) {
			if (!in_array($k, array('___store', 'easy_ajax', 'attribute_set_id', 'popup'))) {
				$collection->addAttributeToFilter($k, $p);
			}
		}
		$data = array();
		foreach ($collection as $object) {
			$data[] = array('id' => $object->getId(), 'title' => $object->getTitle(), 'region' => $object->getRegion() . ' / ' . $object->getLocation());
		}
		$this->getResponse()->setBody(json_encode(array('collection' => $data)));
	}

	public function assignCustomerAction() {
		$oggettoId = $this->getRequest()->getParam('object');
		$attribute_code = 'assigned_uid';

		$value = $this->getRequest()->getParam('customer');
		try {

			$ogg = Mage::getModel('score/oggetto')->load($oggettoId);
			$setname = Mage::helper('score/oggetto')->getSetName($ogg);

			$data = explode(',', $ogg->getData($attribute_code));

			if (in_array($value, $data)) {
				$this->_getSession()->addSuccess('User was added to ' . $setname);
			} else {
				$data[] = $value;
				$data = implode(',', $data);
				$ogg->setData($attribute_code, $data)->save();
				$this->_getSession()->addSuccess('User was added to ' . $setname);
			}
		} catch (Exception $e) {
			echo 'Fail';
			
		}

	}

	public function unassignCustomerAction() {
		$oggettoId = $this->getRequest()->getParam('object');
		$attribute_code = 'assigned_uid';

		$value = $this->getRequest()->getParam('customer');
		try {

			$ogg = Mage::getModel('score/oggetto')->load($oggettoId);
			$setname = Mage::helper('score/oggetto')->getSetName($ogg);

			$data = explode(',', $ogg->getData($attribute_code));

			if (in_array($value, $data)) {
				unset($data[array_search($value, $data)]);
				$data = implode(',', $data);
				$ogg->setData($attribute_code, $data)->save();
				$this->_getSession()->addSuccess('User was removed from ' . $setname);
			} else {

				$this->_getSession()->addSuccess('User was removed from ' . $setname);
			}
		} catch (Exception $e) {
			echo 'Fail';
			
		}

	}

	public function saveTranslationAction() {
		$connection = Mage::getModel('catalog/product')->getCollection()->getConnection();
		$new = $this->getRequest()->getParam('new');
		$old = $this->getRequest()->getParam('old');
		$store = Mage::app()->getStore()->getId();
		$select = $connection->query('select * from core_translate where string = "Mage_Core::' . $old . '" and store_id = ' . $store)->fetchAll();
		if (count($select)) {
			$connection->query('update core_translate set translate= "' . $new . '" where string = "Mage_Core::' . $old . '" and store_id = ' . $store);
		} else {
			$connection->query('insert into core_translate values (key_id, "Mage_Core::' . $old . '", ' . $store . ', "' . $new . '", "en_GB", "' . crc32("Mage_Core::'.$new.'") . '" )');
		}
	}

	public function newCommentMailAction() { 
		if(isset($_POST) && !empty($_POST)) { 
			
			$disqusApiSecret = Mage::getStoreConfig('score/comments/disqus_key'); 

			// The new Disqus comment ID, which we'll look up to send with the notification
			$commentId = $_POST['comment'];

			// Use the posts/details endpoint to get comment content: http://disqus.com/api/docs/posts/details/

			$session = curl_init('http://disqus.com/api/3.0/posts/details.json?api_secret=' . $disqusApiSecret .'&post=' . $commentId . '&related=thread');

			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

			$result = curl_exec($session);

			curl_close($session);
			// decode the json data to make it easier to parse the php
			$results = json_decode($result);

			// Handle errors
			if ($results === NULL) die('Error');

			
			$kind = $_POST['kind'];
			$entity = '';
			switch ($kind) {
				case 'product':
					$entity = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('entity_id',$_POST['id'])->addAttributeToSelect('owner')->getFirstItem();
					break;
				case 'oggetto':
					$entity = Mage::getModel('score/oggetto')->getCollection()->addAttributeToFilter('entity_id',$_POST['id'])->addAttributeToSelect('owner')->getFirstItem();
					break;
				default:
					break;
			}
			if(!is_object($entity) || !$entity->getOwner()) die('Nobody to notify');

			$owner = Mage::getModel('customer/customer')->load($entity->getOwner());
			
			if($owner->getEmail() == Mage::getSingleton('customer/session')->getCustomer()->getEmail()) die('No need to send notification about your own comment');
			/**********************
			// Get the data we need
			**********************/

			// Author and thread objects
			$data = array(
				'name' => $results->response->author->name,
				'avatar' => $results->response->author->avatar->small->permalink,
				'link' => $results->response->thread->link,
				'message' => $results->response->raw_message
			);

			$post = new Varien_Object();
			$post->setData($data);
			if (Mage::helper('score')->sendMailByCode('New comment', $entity, $owner,$post)) {
				echo "Email sent";
			}

			$admin = Mage::getModel('customer/customer')->load(10);
			if($admin->getEmail() != $owner->getEmail()) { 
				if (Mage::helper('score')->sendMailByCode('New comment', $entity, $admin,$post)) {
					echo "Email sent";
				}
			}
		}
	}
}
