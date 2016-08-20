<?php
class Shaurmalab_Constructor_AdminController extends Mage_Core_Controller_Front_Action {

	public function init() {

		if (!Mage::helper('constructor')->isAdmin()) {
			$this->_redirectUrl(MAge::getBaseUrl().'/customer/account/login');
			return;
		}
	}

	private function _getSession() {
		return Mage::getSingleton('customer/session');
	}

	public function indexAction() {
		$this->init();

		$this->loadLayout();
		$this->_initLayoutMessages('score/session');
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}

	public function pagesAction() {
		$this->indexAction();
	}

	public function blocksAction() {
		$this->indexAction();
	}

	public function usersAction() {
		$this->indexAction();
	}


	public function objectsAction() {
		$this->indexAction();
	}

	public function formsAction() {
		$this->indexAction();
	}

	public function templatesAction() {
		$this->indexAction();
	}

	public function generalAction() {
		$this->indexAction();
	}

	public function webAction() {
		$this->indexAction();
	}

	public function designAction() {
		$this->indexAction();
	}

	public function productsAction() {
		$this->indexAction();
	}

	public function categoriesAction() {
		$this->indexAction();
	}

	public function analyticsAction() {
		$this->indexAction();
	}

	public function editBlockAction() {
		$this->indexAction();
	}

	public function editTemplateAction() {
		$this->indexAction();
	}

	public function editPageAction() {
		$this->indexAction();
	}

	public function editObjectAction() {
		$this->indexAction();
	}

	public function saveConfigAction() {
		$session = $this->_getSession();
		try {
			if (Mage::helper('score')->checkAntiForgeryToken()) {
				$post = $this->getRequest()->getPost();
				$config = new Mage_Core_Model_Config();

				$websiteId = Mage::app()->getStore()->getId();

				if (isset($post['locale'])) {
					$config->saveConfig('general/locale/code', $post['locale'], 'stores', $websiteId);
				}

				if (isset($post['title'])) {
					$config->saveConfig('design/head/default_title', $post['title'], 'stores', $websiteId);
				}

				if (isset($post['seo_description'])) {
					$config->saveConfig('design/head/default_description', $post['seo_description'], 'stores', $websiteId);
				}

				if (isset($post['seo_keywords'])) {
					$config->saveConfig('design/head/default_keywords', $post['seo_keywords'], 'stores', $websiteId);
				}

				if (isset($post['timezone'])) {
					$config->saveConfig('general/locale/timezone', $post['timezone'], 'stores', $websiteId);
				}

				if (isset($post['name'])) {
					$config->saveConfig('general/store_information/name', $post['name'], 'stores', $websiteId);
				}

				if (isset($post['phone'])) {
					$config->saveConfig('general/store_information/phone', $post['phone'], 'stores', $websiteId);
				}

				if (isset($post['cms_home_page'])) {
					$config->saveConfig('web/default/cms_home_page', $post['cms_home_page'], 'stores', $websiteId);
				}

				if (isset($post['cms_no_route'])) {
					$config->saveConfig('web/default/cms_no_route', $post['cms_no_route'], 'stores', $websiteId);
				}

				if (isset($post['cms_no_cookies'])) {
					$config->saveConfig('web/default/cms_no_cookies', $post['cms_no_cookies'], 'stores', $websiteId);
				}

				if (isset($post['logo_alt'])) {
					$config->saveConfig('design/header/logo_alt', $post['logo_alt'], 'stores', $websiteId);
				}

				if (isset($post['design_head_includes'])) {
					$config->saveConfig('design/head/includes', $post['design_head_includes'], 'stores', $websiteId);
				}

				if (isset($post['design_footer_includes'])) {
					$config->saveConfig('design/footer/absolute_footer', $post['design_footer_includes'], 'stores', $websiteId);
				}

				if (isset($post['copyright'])) {
					$config->saveConfig('design/footer/copyright', $post['copyright'], 'stores', $websiteId);
				}

				if (isset($post['analytics'])) {
					$config->saveConfig('google/analytics/account', $post['analytics'], 'stores', $websiteId);
				}

				if (isset($post['header_text_color'])) {
					$config->saveConfig('constructor/frontend/header_text_color', $post['header_text_color'], 'stores', $websiteId);
				}

				if (isset($post['header_bg_color'])) {
					$config->saveConfig('constructor/frontend/header_bg_color', $post['header_bg_color'], 'stores', $websiteId);
				}


				$websiteId = Mage::app()->getWebsite()->getId();
				// upload logo
				if (isset($_FILES['logo']['name']) && $_FILES['logo']['name'] != '') {
					try {
						$uploader = new Varien_File_Uploader('logo');

						$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
						$uploader->setAllowRenameFiles(false);

						$uploader->setFilesDispersion(false);

						$path = Mage::getBaseDir('media') . DS . 'logos';
						if (!is_dir($path)) {mkdir($path);}
						$path .= DS . 'websites';
						if (!is_dir($path)) {mkdir($path);}
						$path .= DS . $store->getId();
						if (!is_dir($path)) {mkdir($path);}

						$filename = str_replace(' ', '', $_FILES['logo']['name']);
						$uploader->save($path, $filename);
						$config->saveConfig('design/header/logo_src', "media/logos/websites/" . $store->getId() . '/' . $filename, 'websites', $websiteId);

					} catch (Exception $e) {

					}
				}
				if (isset($_FILES['favicon']['name']) && $_FILES['favicon']['name'] != '') {
					try {
						$uploader = new Varien_File_Uploader('favicon');

						$uploader->setAllowedExtensions(array('ico', 'png', 'gif', 'jpg', 'jpeg', 'apng', 'svg'));
						$uploader->setAllowRenameFiles(false);

						$uploader->setFilesDispersion(false);

						$path = Mage::getBaseDir('media') . DS . 'favicon';
						if (!is_dir($path)) {mkdir($path);}
						$path .= DS . 'websites';
						if (!is_dir($path)) {mkdir($path);}
						$path .= DS . $store->getId();
						if (!is_dir($path)) {mkdir($path);}

						$filename = str_replace(' ', '', $_FILES['favicon']['name']);
						$uploader->save($path, $filename);
						$config->saveConfig('design/head/shortcut_icon', "media/favicon/websites/" . $store->getId() . '/' . $filename, 'websites', $websiteId);

					} catch (Exception $e) {

					}
				}

				Mage::helper('constructor')->cleanCache();
				$session->addSuccess($this->__('Configuration saved'));
			} else {
				$session->addError($this->__('Something went wrong'));
			}
		} catch (Exception $e) {
			$session->addError($this->__($e->getMessage()));
		}

	}

	public function savePageAction() {
		try {
			$this->init();
			$session = $this->_getSession();
			if (Mage::helper('score')->checkAntiForgeryToken()) {
				$id = $this->getRequest()->getParam('id');
				$page = Mage::getModel('cms/page')->load($id);
				$pageData = $page->getData();
				$data = $this->getRequest()->getPost();

				if (!isset($pageData['store_id']) || !$pageData['store_id']) {
					$data['store_id'] = Mage::app()->getStore()->getId();
				} else {
					if ($pageData['store_id'][0] != Mage::app()->getStore()->getId()) {
						$session->addError($this->__('You can not edit this page, sorry'));
						return;
					}
				}

				$page->addData($data);

				if ($page->save()) {
					$session->addSuccess($this->__('Page has been saved'));
				} else {
					$session->addError($this->__('Page is not saved'));
				}
			} else {
				$session->addError($this->__('Something went wrong'));
			}
		} catch (Exception $e) {
			$session->addError($this->__($e->getMessage()));
		}

	}

	public function saveBlockAction() {
		try {
			$this->init();
			$session = $this->_getSession();
			if (Mage::helper('score')->checkAntiForgeryToken()) {
				$id = $this->getRequest()->getParam('id');
				$block = Mage::getModel('cms/block')->load($id);
				$blockData = $block->getData();
				$data = $this->getRequest()->getPost();

				if (!isset($blockData['stores']) || !$blockData['stores']) {
					$data['stores'] = array(Mage::app()->getStore()->getId());
				} else {
					if ($blockData['stores'] != array(Mage::app()->getStore()->getId())) {
						$session->addError($this->__('You can not edit this block, sorry'));
						rerurn;
					}
				}

				$block->addData($data);
				if ($block->save()) {
					$session->addSuccess($this->__('Block has been saved'));
				} else {
					$session->addError($this->__('Block is not saved'));
				}
			} else {
				$session->addError($this->__('Something went wrong'));
			}
		} catch (Exception $e) {
			$session->addError($this->__($e->getMessage()));
		}

	}

	public function saveTemplateAction() { 
			try {
			$this->init();
			$session = $this->_getSession();
			if (Mage::helper('score')->checkAntiForgeryToken()) {
				$id = $this->getRequest()->getParam('id');
				$template = Mage::getModel('dcontent/templates')->load($id);
				$templateData = $template->getData();
				$data = $this->getRequest()->getPost();
				
				if (!isset($templateData['store_id'])) {
					$data['store_id'] = Mage::app()->getStore()->getId();
				} 

				$template->addData($data);
				if ($template->save()) {
					$session->addSuccess($this->__('Template has been saved'));
				} else {
					$session->addError($this->__('Template is not saved'));
				}
			} else {
				$session->addError($this->__('Something went wrong'));
			}
		} catch (Exception $e) {
			$session->addError($this->__($e->getMessage()));
		}
	}

	public function deletePageAction() {
		$this->init();
		$session = $this->_getSession();
		$id = $this->getRequest()->getParam('id');
		$page = Mage::getModel('cms/page')->load($id);
		if ($page->getStoreId() != Mage::app()->getStore()->getId()) {
			$session->addError($this->__('You can not delete this page, sorry'));
		}
		if ($page->delete()) {
			$session->addSuccess($this->__('Page has been deleted'));
		} else {
			$session->addError($this->__('Page is not deleted'));
		}

	}

	public function deleteBlockAction() {
		$this->init();
		$session = $this->_getSession();
		$id = $this->getRequest()->getParam('id');
		$block = Mage::getModel('cms/block')->load($id);
		if ($block->getStores() != array(Mage::app()->getStore()->getId())) {
			$session->addError($this->__('You can not delete this block, sorry'));
		}
		if ($block->delete()) {
			$session->addSuccess($this->__('Block has been deleted'));
		} else {
			$session->addError($this->__('Block is not deleted'));
		}

	}

	public function previewAction($output = '') {
		$data = $this->getRequest()->getParams();

		$page = Mage::getSingleton('cms/page');
		$data['title'] = " (" . Mage::helper('constructor')->__('Preview') . ")";
		$data['content'] = '';
		$data['root_template'] = '1column';
		$data['status'] = 1;
		$page->setData($data);

		$this->getLayout()->getUpdate()
		     ->addHandle('default')
		     ->addHandle('cms_page');

		if ($page->getRootTemplate()) {
			$handle = $page->getRootTemplate();
			$this->getLayout()->helper('page/layout')->applyHandle($handle);
		}
		$this->addActionLayoutHandles();

		//Mage::dispatchEvent('cms_page_render', array('page' => $page, 'controller_action' => $action));

		$this->loadLayoutUpdates();
		$layoutUpdate = $page->getLayoutUpdateXml();
		$this->getLayout()->getUpdate()->addUpdate($layoutUpdate);
		$this->generateLayoutXml()->generateLayoutBlocks();

		if ($data['root_template']) {
			$this->getLayout()->helper('page/layout')
			     ->applyTemplate($data['root_template']);
		}

		if($output) { 
			$this->getLayout()->getBlock('content')->append($output); 
		} 
		$html = $this->renderLayout();
		
		return $html;
	}

	public function previewTemplateAction() { 
		$object = new Varien_Object();
		$object->addData($this->getRequest()->getParams());
		
		$key = $object->getType() . '.' . $object->getKind();
		if(Mage::registry($key)) Mage::unregister($key);
		Mage::register($key, serialize($object));
		
		$html =  Mage::helper('constructor')->getTemplatePreview($object);
			
		$code =  $html;
		echo Mage::helper('core')->jsonEncode(array('content'=>$code));
	}

	public function previewPageAction() {
		$data = $this->getRequest()->getParams();

		$page = Mage::getSingleton('cms/page');
		$data['title'] .= " (" . Mage::helper('constructor')->__('Preview') . ")";
		$page->setData($data);

		$this->getLayout()->getUpdate()
		     ->addHandle('default')
		     ->addHandle('cms_page');

		if ($page->getRootTemplate()) {
			$handle = $page->getRootTemplate();
			$this->getLayout()->helper('page/layout')->applyHandle($handle);
		}
		$this->addActionLayoutHandles();

		//Mage::dispatchEvent('cms_page_render', array('page' => $page, 'controller_action' => $action));

		$this->loadLayoutUpdates();
		$layoutUpdate = $page->getLayoutUpdateXml();
		$this->getLayout()->getUpdate()->addUpdate($layoutUpdate);
		$this->generateLayoutXml()->generateLayoutBlocks();

		if ($data['root_template']) {
			$this->getLayout()->helper('page/layout')
			     ->applyTemplate($data['root_template']);
		}

		$html = $this->renderLayout();

		return $html;
	}

	public function imageUploadAction() {
		$dir = Mage::getBaseDir('media') . DS . 'wysiwyg' . DS . Mage::registry('scode') . DS;
		if (!is_dir($dir)) {
			mkdir($dir);
			chmod($dir, 0777);
		}

		$_FILES['file']['type'] = strtolower($_FILES['file']['type']);

		if ($_FILES['file']['type'] == 'image/png'
			|| $_FILES['file']['type'] == 'image/jpg'
			|| $_FILES['file']['type'] == 'image/gif'
			|| $_FILES['file']['type'] == 'image/jpeg'
			|| $_FILES['file']['type'] == 'image/pjpeg') {
			// setting file's mysterious name
			$filename = md5(date('YmdHis')) . '.jpg';
			$file = $dir . $filename;

			// copying
			copy($_FILES['file']['tmp_name'], $file);

			// displaying file
			$array = array(
				'filelink' => Mage::getBaseUrl('media') . DS . 'wysiwyg' . DS . Mage::registry('scode') . DS . $filename,
			);

			echo stripslashes(json_encode($array));

		}

	}

	public function sendEmailAction() { 
		$params = $this->getRequest()->getParams();
		$storeId = $params['id'];

		$store = Mage::getModel('core/store')->load($storeId);
		if(!$store->getIsPublic()) { 
		
			$localeCode = Mage::getStoreConfig('general/locale/code',$storeId);
			list($locale,$lang) = explode('_', $localeCode);
			$baseUrl = Mage::getStoreConfig('web/secure/base_url',$storeId);
			
			$owner = Mage::getModel('customer/customer')->load($store->getOwner());
		
			$email = $owner->getEmail();
			$name = $owner->getName();
			$mailTemplate = Mage::getModel('core/email_template');
			$sender = array('email'=>'innativelife@gmail.com','name'=> 'Ай-на-нэ / innl.co');
			$vars = array(
					'store' => $store,
					'owner' => $owner,
					'base_url' => $baseUrl,
					'locale' => $locale
				);
			$templateId = 'constructor_welcome_email';
			$mailSubject = 'Сайт '.$baseUrl.' доступен';
			try { 
				if($mailTemplate->setTemplateSubject($mailSubject)->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId)) { 
					$store->setIsPublic(1)->save();
				}
			} catch (Exception $e) { print_r($e->getMessage()); }
			echo 'Email sent';
		} else { 
			echo 'Email sent before';
		} 
	}

	public function objectsgridAction() { 
		$objectId = 0;
		if($this->getRequest()->getParam('id')) $objectId = $this->getRequest()->getParam('id');
		echo Mage::getBlockSingleton('constructor/objectsgrid')->setObjectId($objectId)->toHtml();
	}

	public function usersGridAction() { 
		echo Mage::getBlockSingleton('constructor/users')->toHtml();
	}

	public function deleteOggettoAction() { 
		$this->init();
		$oggettoId = $this->getRequest()->getParam('id');

		if($oggettoId) { 
			$ogg = Mage::getModel('score/oggetto')->load($oggettoId);
			$setname = Mage::helper('score/oggetto')->getSetName($ogg);
			$title = $ogg->getTitle();
			try {
				if($ogg->delete()) {
					$this->_getSession()->addSuccess($this->__($setname . ' has been deleted.'));
				} else {
					$this->_getSession()->addError($this->__('Can not be delete %s',$setname));

				}
			} catch (Exception $e) {
				$this->_getSession()->addError($this->__('Object ' . Mage::getBaseUrl() . $ogg->getUrlPath() . ' NOT deleted. ' . $e->getMessage()));
				Mage::log($e->getMessage(), null, 'system.log');
			}
			return;
		}
	}

	public function editFormAction() { 
		$this->loadLayout();
		$this->renderLayout();
	}

	public function formsGridAction() { 
		echo Mage::getBlockSingleton('constructor/forms')->toHtml();
	}

	public function recordsgridAction() { 
		echo Mage::getBlockSingleton('constructor/formrecords')->toHtml();
	}

}
