<?php
class Shaurmalab_Constructor_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
		$this->loadLayout();
		$this->_initLayoutMessages('score/session');
		$this->_initLayoutMessages('core/session');
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}

	public function createAppAction() {
		$post = $this->getRequest()->getPost();

		try {
			Mage::registry('isSecureArea');

			if ($message = Mage::helper('constructor')->validateData($post)) {
				throw new Exception($message);
			}

			$name = $post['name'];
			$url = $post['base_url'];
			$locale = explode('_', $post['locale']);
			$code = strtolower(str_replace(' ', '', $url)) . '_' . $locale[0];

			$owner = Mage::getSingleton('customer/session')->getCustomer();
			//#addWebsite
			/** @var $website Mage_Core_Model_Website */
			$website = Mage::getModel('core/website');
			$website->setCode($code)
			        ->setName($name)
			        ->save();

			//#addStoreGroup
			/** @var $storeGroup Mage_Core_Model_Store_Group */
			$storeGroup = Mage::getModel('core/store_group');
			$storeGroup->setWebsiteId($website->getId())
			           ->setName($name)
			           ->setRootCategoryId(0)
			           ->save();

			//#addStore
			/** @var $store Mage_Core_Model_Store */
			$store = Mage::getModel('core/store');
			$store->setCode($code)
			      ->setWebsiteId($storeGroup->getWebsiteId())
			      ->setGroupId($storeGroup->getId())
			      ->setName($name)
			      ->setIsActive(1)
			      ->setOwner($owner->getId()) // assign owner
			      ->save();

			$websiteId = $website->getId();
			$config = new Mage_Core_Model_Config();

			$baseUrl = 'http://www.innl.co/';
			$url = str_replace('www.', '', $baseUrl);
			$url = str_replace('http://', 'http://' . trim($post['base_url']) . '.', $url);
			$skinUrl = $baseUrl . 'skin/';
			$jsUrl = $baseUrl . 'js/';
			$mediaUrl = $baseUrl . 'media/';

			$config->saveConfig('general/locale/code', $post['locale'], 'websites', $websiteId);
			$config->saveConfig('design/head/default_title', $post['title'], 'websites', $websiteId);
			$config->saveConfig('design/head/default_description', $post['seo_description'], 'websites', $websiteId);
			$config->saveConfig('design/head/default_keywords', $post['seo_keywords'], 'websites', $websiteId);
			$config->saveConfig('general/locale/timezone', $post['timezone'], 'websites', $websiteId);

			$config->saveConfig('cms/wysiwyg/enabled', "1", 'websites', $websiteId); //  "enabled"
			$config->saveConfig('web/unsecure/base_url', $url, 'websites', $websiteId);
			$config->saveConfig('web/secure/base_url', $url, 'websites', $websiteId);
			$config->saveConfig('web/secure/base_js_url', $jsUrl, 'websites', $websiteId);
			$config->saveConfig('web/secure/base_skin_url', $skinUrl, 'websites', $websiteId);
			$config->saveConfig('web/secure/base_media_url', $mediaUrl, 'websites', $websiteId);
			$config->saveConfig('web/unsecure/base_js_url', $jsUrl, 'websites', $websiteId);
			$config->saveConfig('web/unsecure/base_skin_url', $skinUrl, 'websites', $websiteId);
			$config->saveConfig('web/unsecure/base_media_url', $mediaUrl, 'websites', $websiteId);
			$config->saveConfig('general/store_information/name', $name, 'websites', $websiteId);

			$config->saveConfig('contacts/email/recipient_email', $owner->getEmail(), 'websites', $websiteId);
			$config->saveConfig('trans_email/ident_general/name', $name, 'websites', $websiteId);
			$config->saveConfig('trans_email/ident_general/email', $owner->getEmail(), 'websites', $websiteId);

			// Create CMS pages "home" , "no-route"

			$homepageData = array(
				'title' => $post['title'],
				'root_template' => 'one_column',
				'meta_keywords' => @$post['seo_keywords'],
				'meta_description' => @$post['seo_desctiption'],
				'identifier' => 'home',
				'stores' => array($store->getId()),
			);
			$norouteData = array(
				'title' => '404',
				'root_template' => 'one_column',
				'meta_keywords' => '',
				'meta_description' => '',
				'identifier' => 'no-route',
				'stores' => array($store->getId()),
			);
			if ($locale == 'en') {
				$homepageData['content'] = '<h2 class="centered"> Welcome to my Startup! </h2> ';
				$norouteData['content'] = '<h2 class="centered"> Whoops! page not found, sorry </h2>';
			} elseif ($locale == 'ru') {
				$homepageData['content'] = '<h2 class="centered"> Добро пожаловать в мой Стартап! </h2>';
				$norouteData['content'] = '<h2 class="centered"> Страница не найдена </h2>';
			}

			$homepage = Mage::getModel('cms/page')->setData($homepageData)->save();
			$noroute = Mage::getModel('cms/page')->setData($norouteData)->save();

			$config->saveConfig('web/default/cms_home_page', 'home|' . $homepage->getId(), 'websites', $websiteId);
			$config->saveConfig('web/default/cms_no_route', 'no-route|' . $noroute->getId(), 'websites', $websiteId);

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
					$config->saveConfig('design/header/logo_src', "websites/" . $store->getId() . '/' . $filename, 'websites', $websiteId);

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
					$config->saveConfig('design/head/shortcut_icon', "websites/" . $store->getId() . '/' . $filename, 'websites', $websiteId);

				} catch (Exception $e) {

				}
			}

			$this->_getSession()->addSuccess(
				$this->__("Congrats! You have made a first step to Your Startup!  <br/> Domain %s will be reserved for you and we will notify you about it's Startups Constructor availability as soon as possible.", $url)
			);
			Mage::getSingleton('core/session')->setFormData(array());

			$this->_redirectReferer();
		} catch (Exception $e) {
			Mage::getSingleton('core/session')->setFormData($post);

			$this->_getSession()->addError($e->getMessage());
			$this->_redirectReferer();
		}

	}

	public function welcomeAction() { 
		$this->loadLayout();
		$this->_initLayoutMessages('core/session');
		$this->_initLayoutMessages('score/session');
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}

	private function _getSession() {
		return Mage::getSingleton('core/session');
	}
}