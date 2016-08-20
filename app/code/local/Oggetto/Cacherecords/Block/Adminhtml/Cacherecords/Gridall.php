<?php

class Oggetto_Cacherecords_Block_Adminhtml_Cacherecords_Gridall extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('cacherecordsGrid');
      $this->setDefaultSort('cacherecords_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('cacherecords/cacherecords')->getCollection();
      $this->setCollection($collection);
      
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {

      $this->addColumn('cacherecords_id', array(
          'header'    => Mage::helper('cacherecords')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'cacherecords_id',
      ));

      $this->addColumn('url', array(
          'header'    => Mage::helper('cacherecords')->__('Url'),
          'align'     =>'left',
          'index'     => 'url',
          'type'	=> 'action',
          'renderer' => new Oggetto_Cacherecords_Block_Adminhtml_Renderer_Longurl(),
          'filter_condition_callback' => array($this, '_rugFilter')
      ));

      $this->addColumn('tags', array(
          'header'    => Mage::helper('cacherecords')->__('Tags'),
          'align'     =>'left',
          'index'     => 'mkeys',

      ));

    $this->addColumn('file_exist', array(
          'header'    => Mage::helper('cacherecords')->__('Cache exists?'),
          'align'     =>'left',
          'index'     => 'file_exist',
      	  'type'	=> 'text',
      //   'renderer' => new Oggetto_Cacherecords_Block_Adminhtml_Renderer_File(),
      ));

//      $this->addColumn('mkeys', array(
//          'header'    => Mage::helper('cacherecords')->__('Cache keys'),
//          'align'     =>'left',
//          'index'     => 'mkeys',
//      ));
     $this->addColumn('title', array(
      'header'    => Mage::helper('cacherecords')->__('Title'),
      'align'     =>'left',
      'index'     => 'title',
      //   'renderer'  => new Oggetto_Cacherecords_Block_Adminhtml_Renderer_Title()
      ));

     $this->addColumn('created_time', array(
          'header'    => Mage::helper('cacherecords')->__('Created Time'),
          'align'     =>'left',
          'index'     => 'created_time',
      	  'type'	=> 'datetime',
          'sortable'  => true,
     //     'renderer' => new Oggetto_Cacherecords_Block_Adminhtml_Renderer_Time(),
        ));
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('cacherecords_id');
        $this->getMassactionBlock()->setFormFieldName('cacherecords');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('cacherecords')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('cacherecords')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('cacherecords/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
//        $this->getMassactionBlock()->addItem('status', array(
//             'label'=> Mage::helper('cacherecords')->__('Change status'),
//             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
//             'additional' => array(
//                    'visibility' => array(
//                         'name' => 'status',
//                         'type' => 'select',
//                         'class' => 'required-entry',
//                         'label' => Mage::helper('cacherecords')->__('Status'),
//                         'values' => $statuses
//                     )
//             )
//        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      //return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

    protected function _rugFilter($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }


        $contain = $value[0];
        $notcontain = $value[1];
        $endsWith = $value[2];

        $queries = array();
        if($contain && $contain!='skip') {
            $contain = explode(',', $contain);
            $searchArray1 = array();
            foreach ($contain as $k => $val) {
                $queries[] = "url like '%$val%'";
            }
            $this->getCollection()->getSelect()->where(
                implode(" OR ",$queries)
            );
        }

        $queries = array();
        if($notcontain && $notcontain!='skip') {
            $notcontain = explode(',', $notcontain);
            $searchArray2 = array();
            foreach ($notcontain as $val) {
                $queries[] = "url not like '%$val%'";
            }
            $this->getCollection()->getSelect()->where(
                implode(" AND ",$queries)
            );
        }

        $queries = array();
        if($endsWith && $endsWith!='skip') {
            $endsWith = explode(',', $endsWith);
            $searchArray3 = array();
            foreach ($endsWith as $val) {
                $queries[] = "url like '%$val'";
            }
            $this->getCollection()->getSelect()->where(
                implode(" OR ",$queries)
            );
        }

        return $this;
    }

}
