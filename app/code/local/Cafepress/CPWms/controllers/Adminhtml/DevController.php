<?php


class Cafepress_CPWms_Adminhtml_DevController extends Mage_Adminhtml_Controller_Action
{

	private $_safePurgeDirs = array('log', 'report');

	public function purgeAction()
	{
		$dir = $this->getRequest()->getParam('dir');
		if($dir){
			if(in_array($dir, $this->_safePurgeDirs)){
				foreach (new DirectoryIterator(Mage::getBaseDir('var').DS.$dir.DS) as $fileInfo) {
				    if($fileInfo->isDot()){
				    	continue;
				    }

					try{
						unlink($fileInfo->getPathname());
						$this->_getSession()->addSuccess('Deleted: ' . $fileInfo->getFilename());
					}catch(Exception $e){
						$this->_getSession()->addError($e->getMessage());
					}

				}
			}
		}
	}

	public function tailFileAction()
	{
		$f = $this->getRequest()->getParam('file');

		$contents = '';
		if($f){

			$tail = Mage::getModel('cpwms/tail', $f);
			if ($this->getRequest()->getParam('grep')) {
				$tail->setGrep($this->getRequest()->getParam('grep'));
			}

			if ($this->getRequest()->getParam('cpwms_show')){
				$tail->setNumberOfLines($this->getRequest()->getParam('cpwms_show'));
			}

			$contents = $tail->output(216);
		}

		$this->getResponse()->setBody($contents);
	}

    public function xmlTailFileAction()
    {
        $f = $this->getRequest()->getParam('file');

        $contents = '';
        if($f){

            $tail = Mage::getModel('cpwms/xmltail', $f);
            if ($this->getRequest()->getParam('grep')) {
                $tail->setGrep($this->getRequest()->getParam('grep'));
            }

            if ($this->getRequest()->getParam('cpwms_show')){
                $tail->setNumberOfLines($this->getRequest()->getParam('cpwms_show'));
            }

            $contents = $tail->output(216);
        }

        $this->getResponse()->setBody($contents);
    }

	public function logAction()
	{

		if(!Mage::getStoreConfig('dev/log/active')){
			$this->_getSession()->addError($this->__('Developer logs are not enabled, <a href="%s">click here</a> to enable.', Mage::getUrl('adminhtml/system_config/edit/section/dev/')));
		}

		Mage::register('found_logs', Mage::getModel('devtools/log')->fetchFiles());

        $this->loadLayout(array(
                'default',
                'system_devtools_log'
            ));
		$this->_setActiveMenu('system');

		$this->renderLayout();
	}

	public function reportAction()
	{
		Mage::register('found_reports', Mage::getModel('devtools/report')->fetchFiles());

        $this->loadLayout(array(
                'default',
                'system_devtools_report'
            ));
		$this->_setActiveMenu('system');

		$this->renderLayout();
	}

	public function systemAction()
	{
        $this->loadLayout(array(
                'default',
                'system_devtools_system'
            ));
		$this->_setActiveMenu('system');

		$this->_extension_check(array(
			'curl',
			'dom',
			'gd',
			'hash',
			'iconv',
			'mcrypt',
			'pcre',
			'pdo',
			'pdo_mysql',
			'simplexml'
		));


		$this->renderLayout();
	}

	protected function _extension_check($extensions)
	{
		$fail = '';
		$pass = '';

		if(version_compare(phpversion(), '5.2.0', '<')) {
			$fail .= '<li>You need<strong> PHP 5.2.0</strong> (or greater)</li>';
		}
		else {
			$pass .='<li>You have<strong> PHP 5.2.0</strong> (or greater)</li>';
		}

		if(!ini_get('safe_mode')) {
			$pass .='<li>Safe Mode is <strong>off</strong></li>';
			preg_match('/[0-9]\.[0-9]+\.[0-9]+/', shell_exec('mysql -V'), $version);

			if(version_compare($version[0], '4.1.20', '<')) {
				$fail .= '<li>You need<strong> MySQL 4.1.20</strong> (or greater)</li>';
			}
			else {
				$pass .='<li>You have<strong> MySQL 4.1.20</strong> (or greater)</li>';
			}
		}
		else { $fail .= '<li>Safe Mode is <strong>on</strong></li>';  }

		foreach($extensions as $extension) {
			if(!extension_loaded($extension)) {
				$fail .= '<li> You are missing the <strong>'.$extension.'</strong> extension</li>';
			}
			else{	$pass .= '<li>You have the <strong>'.$extension.'</strong> extension</li>';
			}
		}

		if($fail) {

			$rs = '<p><strong>Your server does not meet the following requirements in order to install Magento.</strong>'.
			'<br>The following requirements failed, please contact your hosting provider in order to receive assistance with meeting the system requirements for Magento:'.
			'<ul>'.$fail.'</ul></p>'.
			'The following requirements were successfully met:'.
			'<ul>'.$pass.'</ul>';

			Mage::register('sys_check', $rs);

		} else {
			$rs = '<p><strong>Congratulations!</strong> Your server meets the requirements for Magento.</p>'.
			 '<ul>'.$pass.'</ul>';
			Mage::register('sys_check', $rs);
		}
	}

}