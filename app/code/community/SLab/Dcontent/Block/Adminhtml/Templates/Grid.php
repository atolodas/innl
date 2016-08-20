<?php
/**
 * Templates  grid
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Adminhtml_Templates_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  /**
   * Init grid
   *
   */
  public function __construct()
  {
      parent::__construct();
      $this->setId('templatesGrid');
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
      $collection = Mage::getModel('dcontent/templates')->getCollection();
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

      $this->addColumn('header', array(
          'header'    => Mage::helper('dcontent')->__('Header'),
          'align'     =>'left',
          'index'     => 'header',
      ));
      
       $this->addColumn('product', array(
          'header'    => Mage::helper('dcontent')->__('Product'),
          'align'     =>'left',
          'index'     => 'product',
      ));
      
       $this->addColumn('separator', array(
          'header'    => Mage::helper('dcontent')->__('Separator'),
          'align'     =>'left',
          'index'     => 'separator',
      ));

       $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('dcontent')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('dcontent')->__('for Product'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ),
                     array(
                        'caption'   => Mage::helper('dcontent')->__('for Oggetto'),
                        'url'       => array('base'=> '*/*/editOggettos'),
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