<?php
/**
 * Product blocks grid
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Adminhtml_Dcontent_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  /**
   * Init grid
   *
   */
  public function __construct()
  {
      parent::__construct();
      $this->setId('dcontentGrid');
      $this->setDefaultSort('dcontent_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  /**
   * Prepare grid collection
   *
   * @return this
   */
  protected function _prepareCollection()
  {
      $collection = Mage::getModel('dcontent/dcontent')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }
  
 /**
   * Prepare grid columns
   *
   * @return this
   */
  protected function _prepareColumns()
  {
      $this->addColumn('dcontent_id', array(
          'header'    => Mage::helper('dcontent')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'dcontent_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('dcontent')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));

      $this->addColumn('products', array(
          'header'    => Mage::helper('dcontent')->__('Products'),
          'align'     =>'left',
      	  'type'  => 'action',
          'index'     => 'products',
       	  'filterable' => false,
      	  'renderer'	=> new SLab_Dcontent_Block_Adminhtml_Renderer_Products(),
      ));
      
      $this->addColumn('status', array(
          'header'    => Mage::helper('dcontent')->__('Status'),
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
                'header'    =>  Mage::helper('dcontent')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('dcontent')->__('Edit'),
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

    /**
     * Prepare grid massaction
     *
     * @return this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('dcontent_id');
        $this->getMassactionBlock()->setFormFieldName('dcontent');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('dcontent')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('dcontent')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('dcontent/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('dcontent')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('dcontent')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  /**
   * Generate row url
   *
   * @return string
   */
  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}