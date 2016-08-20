<?php
/*
/**
* Phxsolution Formbuilder
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@magentocommerce.com so you can be sent a copy immediately.
*
* Original code copyright (c) 2008 Irubin Consulting Inc. DBA Varien
*
* @category   adminhtml block
* @package    Phxsolution_Formbuilder
* @author     Murad Ali
* @contact    contact@phxsolution.com
* @site       www.phxsolution.com
* @copyright  Copyright (c) 2014 Phxsolution Formbuilder
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
?>
<?php
class Shaurmalab_Constructor_Block_Formrecords extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function _construct()
    {
        parent::_construct();
        $this->setId('recordsGrid');
        $this->setDefaultSort('records_index');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection()
    {
        $currentFormId = $this->getRequest()->getParam('id');
        if(!$currentFormId) $currentFormId = $this->getFormId();
        $recordsModel = Mage::helper('formbuilder')->getRecordsModel();
        $collection = $recordsModel->getRecordsCollection($currentFormId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $currentFormId = $this->getRequest()->getParam('id');
        $fieldsModel = Mage::helper('formbuilder')->getFieldsModel();
        $prepareFieldTitles = array();
        $prepareFieldTitles = $fieldsModel->prepareFieldTitles($currentFormId);
        
        if(count($prepareFieldTitles))
        {
           
            $i=1;
            foreach ($prepareFieldTitles as $fieldId => $fieldTitle)
            {                
                $this->addColumn($fieldId, array(
                    'header' => Mage::helper('formbuilder')->__($fieldTitle),
                    'align' => 'left',
                    'name'  =>  $i++,
                    'index' => $fieldId,
                    'renderer'  => 'formbuilder/adminhtml_formbuilder_renderer_recordvalue'
                ));
            }
        }

        
        $this->addExportType('*/*/exportCsv', Mage::helper('formbuilder')->__('CSV'));
        return parent::_prepareColumns();
    }
    //this method is reuired if you want ajax grid
    public function getGridUrl()
    {
        return $this->getUrl('constructon/admin/recordsgrid', array('_current' => true));
    }
    public function canShowTab()
    {
        return true;
    }
    public function isHidden()
    {
        return false;
    }
    public function getTabLabel()
    {
        return $this->__('Fields List');
    }
    public function getTabTitle()
    {
        return $this->__('Fields List');
    }
}