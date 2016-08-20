<?php

class Ewall_Customtags_Block_Adminhtml_Customtags_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('customtagsGrid');
      $this->setDefaultSort('customtags_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('customtags/customtags')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('web_id', array(
          'header'    => Mage::helper('customtags')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'web_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('customtags')->__('Tag'),
          'align'     =>'left',
          'index'     => 'title',
      ));
      
      $this->addColumn('filename', array(
          'header'    => Mage::helper('customtags')->__('URL'),
          'align'     =>'left',
          'index'     => 'filename',
      ));
      
      $this->addColumn('content', array(
          'header'    => Mage::helper('customtags')->__('Popularity'),
          'align'     =>'left',
          'index'     => 'content',
      ));

      $this->addColumn('status', array(
          'header'    => Mage::helper('customtags')->__('Status'),
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
                'header'    =>  Mage::helper('customtags')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('customtags')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('web_id');
        $this->getMassactionBlock()->setFormFieldName('customtags');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('customtags')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('customtags')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('customtags/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('customtags')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('customtags')->__('Status'),
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
