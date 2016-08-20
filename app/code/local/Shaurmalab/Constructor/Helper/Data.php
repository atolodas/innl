<?php
class Shaurmalab_Constructor_Helper_Data extends Mage_Core_Helper_Abstract {

	public function validateData($data) {
		if (strlen($data['base_url']) < 4) {
			return $this->__('Subdomain of your startup name should be 4 characters or longer');
		}
	}

	public function isAdmin() {
		if (Mage::app()->getStore()->getOwner() && Mage::getSingleton('customer/session')->getCustomerId() === Mage::app()->getStore()->getOwner()) {
			return true;
		}

		if (Mage::registry('scode') == 'demo') {
			return true;
		}

		if (Mage::registry('scode') == 'wiki' && substr_count(Mage::getSingleton('customer/session')->getCustomer()->getEmail(),'neklo.com')) {
			return true;
		}

		if (Mage::getSingleton('customer/session')->getCustomerId() == 10) {
			return true;
		}

		return false;
	}


	public function isSuperAdmin() {
		if (Mage::app()->getStore()->getOwner() && Mage::getSingleton('customer/session')->getCustomerId() === Mage::app()->getStore()->getOwner()) {
			return true;
		}

		if (Mage::getSingleton('customer/session')->getCustomerId() == 10) {
			return true;
		}

		return false;
	}
	/**
	 * Currenty selected store ID if applicable
	 *
	 * @var int
	 */
	protected $_storeId = null;

	/**
	 * Set a specified store ID value
	 *
	 * @param int $store
	 * @return Mage_Catalog_Helper_Data
	 */
	public function setStoreId($store) {
		$this->_storeId = $store;
		return $this;
	}

	public function cleanCache() {
		$this->recurseRmdir(Mage::getBaseDir() . DS . 'var' . DS . 'cache' . DS . Mage::registry('scode') . '_' . Mage::registry('slang'));
	}

	public function recurseRmdir($dir) {
		$files = array_diff(scandir($dir), array('.', '..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->recurseRmdir("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}

	public function getTemplatePreview($template) {
		$_oggettos= Mage::getModel('score/oggetto')->getCollection()->addAttributeToFilter('attribute_set_id',$template->getType());
        $_oggettos->getSelect()->order(new Zend_Db_Expr('RAND()'));
           $_oggettos->setPageSize(3)->setCurPage(1);
           $_oggettos->addAttributeToSelect('*');

        if($template->getKind() == 'grid' || $template->getKind() == 'mygrid') {
            $html = Mage::getBlockSingleton('score/oggetto_all')->setCollection($_oggettos)->setDefaultMode('grid')->setTemplate('score/oggetto/list.phtml')->toHtml();
        } elseif($template->getKind() == 'list' || $template->getKind() == 'mylist') {
            $html = Mage::getBlockSingleton('score/oggetto_all')->setCollection($_oggettos)->setDefaultMode('list')->setTemplate('score/oggetto/list.phtml')->toHtml();
        } elseif($template->getKind() == 'main') {
             $oggetto = Mage::getModel('score/oggetto')->load($_oggettos->getFirstItem()->getId());
             Mage::register('oggetto',$oggetto);
             $html = Mage::getBlockSingleton('dcontent/oggettos')->previewTemplate($oggetto,$template);
        } elseif($template->getKind() == 'customer_list') {
        	$html = Mage::getBlockSingleton('magemlm/customer_search')->toHtml();
        } elseif($template->getKind() == 'customer_main') {
        	$html = Mage::getBlockSingleton('magemlm/customer_meet')->toHtml();
        } elseif($template->getKind() == 'child') {
        	$html = Mage::getBlockSingleton("score/oggetto_list_related")->setCollection($_oggettos)->setTemplate("score/oggetto/list/child.phtml")->toHtml();
        } elseif($template->getKind() == 'parrent') {
        	$html = Mage::getBlockSingleton("score/oggetto_list_related")->setCollection($_oggettos)->setTemplate("score/oggetto/list/parent.phtml")->toHtml();
        }
        $search = array ("'<script[^>]*?>.*?</script>'si");
        $replace = array ("");
        //$html = preg_replace($search, $replace, $html);
        $html = implode('',explode('\n',implode('',explode(PHP_EOL,$html))));
        return $html;
	}


}
