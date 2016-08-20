<?php

class Snowcommerce_Seo_Block_Adminhtml_Seo_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('seoGrid');
      $this->setDefaultSort('seo_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
  	 $store = $this->_getStore();
  	 
      $collection = Mage::getModel('seo/seo')->getCollection();
      
       if ($store->getId()) {
       	 $collection = Mage::getModel('seo/seo')->getResourceCollection()->addFieldToFilter('store_id',array(array('like'=>$store->getId().',%'),array('like'=>'%,'.$store->getId()),array('like'=>'%,'.$store->getId().',%')));
       	 
       }
       
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }
  
 protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

  protected function _prepareColumns()
  {
      $this->addColumn('seo_id', array(
          'header'    => Mage::helper('seo')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'type'    => 'number',
          'index'     => 'seo_id',
      ));

      $this->addColumn('url', array(
          'header'    => Mage::helper('seo')->__('Url'),
          'align'     =>'left',
          'index'     => 'url',
      	  'renderer'  => 'Snowcommerce_Seo_Block_Adminhtml_Renderer_Url',
      ));



      $this->addColumn('meta_title', array(
          'header'    => Mage::helper('seo')->__('Seo Title'),
          'align'     =>'left',
          'index'     => 'meta_title',
      ));

      $this->addColumn('meta_description', array(
          'header'    => Mage::helper('seo')->__('Seo Description'),
          'align'     =>'left',
          'index'     => 'meta_description',
      ));
	  
	   $this->addColumn('meta_keyword', array(
          'header'    => Mage::helper('seo')->__('Seo Keywords'),
          'align'     =>'left',
          'index'     => 'meta_keyword',
      ));

	   $this->addColumn('store_id', array(
          'header'    => Mage::helper('seo')->__('Stores'),
          'align'     =>'left',
          'index'     => 'store_id',
        
      ));




      /*
      $this->addColumn('content', array(
            'header'    => Mage::helper('seo')->__('Item Content'),
            'width'     => '150px',
            'index'     => 'content',
      ));
      */

      $this->addColumn('status', array(
          'header'    => Mage::helper('seo')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));

	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('seo')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('seo')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('seo')->__('CSV'));
		//$this->addExportType('*/*/exportXml', Mage::helper('seo')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('seo_id');
        $this->getMassactionBlock()->setFormFieldName('seo');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('seo')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('seo')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('seo/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('seo')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('seo')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}