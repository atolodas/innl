<?php

class Shaurmalab_Score_Block_Adminhtml_Score_Dictionary_Edit extends Mage_Core_Block_Template
{
    /**
     * Set template
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('score/dictionary.phtml');
    }

    public function getDictionary($tableName = '') { 
    	
    	if(!$tableName) $tableName = Mage::app()->getRequest()->getParam('id');

    	$data =  Mage::getSingleton('core/resource')->getConnection('core_read')->query("SELECT * FROM ".$tableName)->fetchAll();

    	return $data;
    }
}