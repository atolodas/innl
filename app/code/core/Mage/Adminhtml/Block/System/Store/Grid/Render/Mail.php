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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Store render store
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_System_Store_Grid_Render_Mail
 extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		if (!$row->getData($this->getColumn()->getIndex())) {
			return null;
		}
		$customerId = $row->getData($this->getColumn()->getIndex());
		$customer = Mage::getModel('customer/customer')->load($customerId);
		
		$localeCode = Mage::getStoreConfig('general/locale/code',$row->getStoreId());
		list($locale,$lang) = explode('_', $localeCode);
		$store = Mage::getModel('core/store')->load($row->getStoreId());

		if(!$store->getIsPublic()) { 
		$html = "To: ".$customer->getEmail().' <br/><br/> ';
		$html .= "Theme: "."Сайт ".Mage::getStoreConfig('web/secure/base_url', $row->getStoreId()).$locale. "/ доступен <br/><br/>";
		$html .= "
					Добрый день!<br/><br/>
					
					Спасибо, что зарегистрировались на нашем проекте.<br/><br/>

					Ваш сайт уже доступен по адресу ".Mage::getStoreConfig('web/secure/base_url', $row->getStoreId()).$locale. "/ <br/>
					Редактирование сайта возможно после авторизации ".Mage::getStoreConfig('web/secure/base_url', $row->getStoreId()).$locale."/customer/account/login/ и доступно по адресу ".Mage::getStoreConfig('web/secure/base_url', $row->getStoreId()).$locale."/manage/ <br/>
					<br/>
					На данный момент Вы можете добавлять неограниченное число страниц и блоков, а так же редактировать основные настройки.<br/>
					Со временем функции сайта будут расширяться.<br/>
					Буду рад ответить на любые вопросы тут или в чате - https://www.hipchat.com/gEvIe0ifV <br/>
					";
		} else { 
			$html = 'Email sent';
		}

		return $html;
	}
}
